<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Exports\ParkingReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ParkingReportController extends Controller
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

        return view('pages.reports.parking-report.index', compact(
            'properties',
            'startDate',
            'endDate',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = ParkingFeeTransaction::with([
                'property',
                'transaction',
                'transaction.room',
                'verifiedBy',
            ])
            // Include both 'paid' (active rentals) and 'completed' (rentals whose period
            // has ended) — both represent money-was-received historical rows and belong
            // on the Parking Report. Mirrors the backfill walk in BackfillInvoiceNumbers.
            ->whereIn('transaction_status', ['paid', 'completed'])
            // Order + filter by transaction_date so the list, the date filter, and the displayed
            // Payment Date column all agree (Payment Date column sources transaction_date below).
            ->orderByDesc('transaction_date');

        // Date range filter — matches the displayed Payment Date column (transaction_date).
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('transaction_date', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Property filter
        if ($user->isSuperAdmin() || $user->isHO()) {
            if ($request->filled('property_id')) {
                $query->where('property_id', $request->property_id);
            }
        } else {
            if ($user->property_id) {
                $query->where('property_id', $user->property_id);
            }
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_id', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%")
                    ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate($request->input('per_page', 15));

        $data = $payments->map(function ($transaction, $index) use ($payments) {
            $offset = ($payments->currentPage() - 1) * $payments->perPage();

            $verifiedByName = '-';
            if ($transaction->verifiedBy) {
                $verifiedByName = $transaction->verifiedBy->first_name . ' ' . $transaction->verifiedBy->last_name;
            }

            $propertyName = $transaction->property ? $transaction->property->name : '-';
            $roomName = '-';
            if ($transaction->transaction && $transaction->transaction->room) {
                $roomName = $transaction->transaction->room->name;
            }

            return [
                'no' => $offset + $index + 1,
                'invoice_id' => $transaction->invoice_id ?? '-',
                'order_id' => $transaction->order_id ?? '-',
                'property_name' => $propertyName,
                'room_name' => $roomName,
                'tenant_name' => $transaction->user_name ?? '-',
                'phone' => $transaction->user_phone ?? '-',
                'parking_type' => ucfirst($transaction->parking_type ?? '-'),
                'vehicle_plate' => $transaction->vehicle_plate ?? '-',
                'fee_amount' => 'Rp ' . number_format($transaction->fee_amount ?? 0, 0, ',', '.'),
                // Column meanings (after 2026-05-07 swap):
                //   Transaction Date column ← paid_at (server clock when row was recorded)
                //   Payment Date column     ← transaction_date (admin-keyed actual money date)
                'transaction_date' => $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-',
                'paid_at' => $transaction->transaction_date ? Carbon::parse($transaction->transaction_date)->format('d M Y') : '-',
                'payment_status' => 'Paid',
                'verified_by' => $verifiedByName,
                'verified_at' => $transaction->verified_at ? Carbon::parse($transaction->verified_at)->format('d M Y H:i') : '-',
                'notes' => $transaction->notes ?? '-',
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

        $filename = 'parking-report-' . now()->format('Y-m-d-His') . '.xlsx';

        $exporter = new ParkingReportExport($filters);
        return $exporter->export($filename);
    }
}
