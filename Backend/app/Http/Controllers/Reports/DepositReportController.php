<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\DepositFeeTransaction;
use App\Models\Property;
use App\Exports\DepositReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DepositReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isSuperAdmin() || $user->isHO()) {
            $properties = Property::where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            $properties = Property::where('status', 1)
                ->where('idrec', $user->property_id)
                ->orderBy('name')
                ->get();
        }

        // Get filter values (no default date - show all data)
        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');

        if ($user->isSuperAdmin() || $user->isHO()) {
            $propertyId = $request->input('property_id');
        } else {
            $propertyId = $user->property_id;
        }

        return view('pages.reports.deposit-report.index', compact(
            'properties',
            'startDate',
            'endDate',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = DepositFeeTransaction::with([
                'transaction',
                'transaction.property',
                'transaction.room',
                'verifiedBy',
            ])
            ->where('transaction_status', 'paid')
            ->orderByDesc('paid_at');

        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('paid_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Property filter via transaction relationship
        if ($user->isSuperAdmin() || $user->isHO()) {
            if ($request->filled('property_id')) {
                $propertyId = $request->property_id;
                $query->whereHas('transaction', function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                });
            }
        } else {
            if ($user->property_id) {
                $propertyId = $user->property_id;
                $query->whereHas('transaction', function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                });
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('user_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->paginate($request->input('per_page', 15));

        $data = $payments->map(function ($deposit, $index) use ($payments) {
            $offset = ($payments->currentPage() - 1) * $payments->perPage();

            $verifiedByName = '-';
            if ($deposit->verifiedBy) {
                $verifiedByName = $deposit->verifiedBy->first_name . ' ' . $deposit->verifiedBy->last_name;
            }

            $propertyName = '-';
            $roomName = '-';
            $tenantName = '-';
            $tenantPhone = '-';

            if ($deposit->transaction) {
                $propertyName = $deposit->transaction->property_name ?? '-';
                $tenantName = $deposit->transaction->user_name ?? '-';
                $tenantPhone = $deposit->transaction->user_phone_number ?? '-';
                if ($deposit->transaction->room) {
                    $roomName = $deposit->transaction->room->name;
                }
            }

            return [
                'no' => $offset + $index + 1,
                'invoice_id' => $deposit->invoice_id ?? '-',
                'order_id' => $deposit->order_id ?? '-',
                'property_name' => $propertyName,
                'room_name' => $roomName,
                'tenant_name' => $tenantName,
                'phone' => $tenantPhone,
                'fee_amount' => 'Rp ' . number_format($deposit->fee_amount ?? 0, 0, ',', '.'),
                'transaction_date' => $deposit->transaction_date ? Carbon::parse($deposit->transaction_date)->format('d M Y') : '-',
                'paid_at' => $deposit->paid_at ? Carbon::parse($deposit->paid_at)->format('d M Y H:i') : '-',
                'payment_status' => 'Paid',
                'verified_by' => $verifiedByName,
                'verified_at' => $deposit->verified_at ? Carbon::parse($deposit->verified_at)->format('d M Y H:i') : '-',
                'notes' => $deposit->notes ?? '-',
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
            ]
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        $propertyId = ($user->isSuperAdmin() || $user->isHO())
            ? $request->input('property_id')
            : $user->property_id;

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'property_id' => $propertyId,
            'search' => $request->input('search'),
        ];

        $filename = 'deposit-report-' . now()->format('Y-m-d-His') . '.xlsx';

        $exporter = new DepositReportExport($filters);
        return $exporter->export($filename);
    }
}
