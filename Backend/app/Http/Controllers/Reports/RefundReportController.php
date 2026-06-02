<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Property;
use App\Exports\RefundReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel as MaatwebsiteExcel;
use Carbon\Carbon;

/**
 * Refund Report controller — lists refunds from t_refund with optional filters
 * (date range on refund_date, status, property, search) and an Excel export.
 * Mirrors BookingReportController shape so the page/admin UX stays consistent.
 */
class RefundReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // <!-- Property list filtered by user access (super admin sees all) -->
        if ($user->isSuperAdmin()) {
            $properties = Property::where('status', 1)
                ->orderBy('name')
                ->get();
        } else {
            $properties = Property::where('status', 1)
                ->where('idrec', $user->property_id)
                ->orderBy('name')
                ->get();
        }

        $startDate = $request->input('start_date', '');
        $endDate = $request->input('end_date', '');
        $status = $request->input('status');

        $propertyId = $user->isSuperAdmin()
            ? $request->input('property_id')
            : $user->property_id;

        return view('pages.reports.refund-report.index', compact(
            'properties',
            'startDate',
            'endDate',
            'status',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        // <!-- Eager-load transaction + property + room + requestedBy + processedBy -->
        $query = Refund::with([
                'transaction.property',
                'transaction.room',
                'transaction.user',
                'requestedBy',
                'processedBy',
            ])
            // <!-- Newest refund first. `id` desc is a tie-breaker because refund_date is only
            //      second-precision — refunds processed in the same second would otherwise have
            //      an undefined row order. -->
            ->orderByDesc('refund_date')
            ->orderByDesc('id');

        // <!-- Date range on refund_date -->
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('refund_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59',
            ]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('refund_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('refund_date', '<=', $request->end_date);
        }

        // <!-- Status filter (pending, refunded, rejected) -->
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // <!-- Refund type filter (admin / user initiated) -->
        if ($request->filled('refund_type')) {
            $query->where('refund_type', $request->refund_type);
        }

        // <!-- Property filter: super admin can pick any; others scoped to own -->
        if ($user->isSuperAdmin()) {
            if ($request->filled('property_id')) {
                $propertyId = $request->property_id;
                $query->whereHas('transaction', function ($q) use ($propertyId) {
                    $q->where('property_id', $propertyId);
                });
            }
        } elseif ($user->property_id) {
            $propertyId = $user->property_id;
            $query->whereHas('transaction', function ($q) use ($propertyId) {
                $q->where('property_id', $propertyId);
            });
        }

        // <!-- Search across order id / tenant name / refund reason -->
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_booking', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('transaction', function ($q2) use ($search) {
                        $q2->where('user_name', 'like', "%{$search}%")
                            ->orWhere('order_id', 'like', "%{$search}%");
                    });
            });
        }

        $refunds = $query->paginate($request->input('per_page', 15));

        $data = $refunds->map(function ($refund) {
            $transaction = $refund->transaction;

            // <!-- Tenant name: prefer user first+last, fallback to transaction.user_name -->
            $tenantName = '-';
            if ($transaction) {
                $user = $transaction->user;
                if ($user) {
                    $full = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
                    $tenantName = $full !== '' ? $full : ($transaction->user_name ?? '-');
                } else {
                    $tenantName = $transaction->user_name ?? '-';
                }
            }

            $propertyName = $transaction && $transaction->property ? $transaction->property->name : '-';
            $address = $transaction && $transaction->property ? ($transaction->property->address ?? '-') : '-';
            $roomName = $transaction && $transaction->room ? $transaction->room->name : '-';

            $processedByName = $refund->processedBy ? ($refund->processedBy->username ?? '-') : '-';
            $requestedByName = $refund->requestedBy ? ($refund->requestedBy->username ?? '-') : ($refund->refund_type === 'user' ? 'User' : 'Admin');

            return [
                'refund_date' => $refund->refund_date ? Carbon::parse($refund->refund_date)->format('d M Y H:i') : '-',
                'order_id' => $refund->id_booking,
                'tenant_name' => $tenantName,
                'property_name' => $propertyName,
                'address' => $address,
                'room' => $roomName,
                'refund_type' => ucfirst($refund->refund_type ?? 'admin'),
                'reason' => $refund->reason ?? '-',
                'amount' => 'Rp ' . number_format((float) ($refund->amount ?? 0), 0, ',', '.'),
                'room_refund' => 'Rp ' . number_format((float) ($refund->room_refund ?? 0), 0, ',', '.'),
                'deposit_refund' => 'Rp ' . number_format((float) ($refund->deposit_refund ?? 0), 0, ',', '.'),
                'other_refund' => 'Rp ' . number_format((float) ($refund->other_refund ?? 0), 0, ',', '.'),
                'bank_name' => $refund->refund_bank_name ?? '-',
                'account_no' => $refund->refund_account_no ?? '-',
                'account_holder' => $refund->refund_account_holder ?? '-',
                'requested_by' => $requestedByName,
                'processed_by' => $processedByName,
                'processed_at' => $refund->processed_at ? Carbon::parse($refund->processed_at)->format('d M Y H:i') : '-',
                'admin_notes' => $refund->admin_notes ?? '-',
                'status' => $refund->status ?? '-',
                'raw_status' => strtolower($refund->status ?? 'pending'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $refunds->currentPage(),
                'last_page' => $refunds->lastPage(),
                'per_page' => $refunds->perPage(),
                'total' => $refunds->total(),
            ],
        ]);
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        $propertyId = $user->isSuperAdmin()
            ? $request->input('property_id')
            : $user->property_id;

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'status' => $request->input('status'),
            'refund_type' => $request->input('refund_type'),
            'property_id' => $propertyId,
            'search' => $request->input('search'),
        ];

        $filename = 'refund-report-' . now()->format('Y-m-d-His') . '.xlsx';

        return MaatwebsiteExcel::download(new RefundReportExport($filters), $filename);
    }
}
