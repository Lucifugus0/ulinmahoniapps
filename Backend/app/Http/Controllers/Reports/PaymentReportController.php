<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Property;
use App\Exports\PaymentReportExport;
use App\Services\InvoiceNumberService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get properties for filter based on user access
        // Super Admin and HO users can see all properties
        // Site users only see their assigned property
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

        // Set property_id based on user access
        // Super Admin and HO users can select any property
        // Site users are restricted to their assigned property
        if ($user->isSuperAdmin() || $user->isHO()) {
            $propertyId = $request->input('property_id');
        } else {
            $propertyId = $user->property_id;
        }

        return view('pages.reports.payment-report.index', compact(
            'properties',
            'startDate',
            'endDate',
            'propertyId'
        ));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();

        $query = Transaction::with([
                'payment.verifiedBy',
                'property',
                'room',
                'booking.refund',
                'user'
            ])
            ->whereHas('payment')
            ->where('transaction_status', 'paid')
            ->orderByDesc('paid_at');

        // Date range filter based on paid_at date
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;

            $query->whereBetween('paid_at', [
                $startDate . ' 00:00:00',
                $endDate . ' 23:59:59'
            ]);
        }

        // Property filter based on user access
        // Site users (user_type = 1) only see their property
        // HO users (user_type = 0) and Super Admin can filter by any property
        if ($user->isSuperAdmin() || $user->isHO()) {
            if ($request->filled('property_id')) {
                $query->where('property_id', $request->property_id);
            }
        } else {
            // Site users: automatically filter by their property
            if ($user->property_id) {
                $query->where('property_id', $user->property_id);
            }
        }

        // Search filter (order_id, transaction_code, user_name)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhere('transaction_code', 'like', "%{$search}%")
                    ->orWhere('user_name', 'like', "%{$search}%");
            });
        }

        $payments = $query->paginate($request->input('per_page', 15));

        // Generate invoice numbers for the paginated results
        $invoiceNumbers = InvoiceNumberService::generateBatch($payments->getCollection());

        // Transform data for display (28 columns)
        $data = $payments->map(function ($transaction, $index) use ($payments, $invoiceNumbers) {
            $payment = $transaction->payment;

            // Detect refund
            $isRefund = false;
            $refundInfo = null;
            if ($transaction->booking && $transaction->booking->refund) {
                $isRefund = true;
                $refundInfo = $transaction->booking->refund;
            }

            $offset = ($payments->currentPage() - 1) * $payments->perPage();

            // Get invoice number
            $invoiceNumber = $invoiceNumbers[$transaction->idrec] ?? '-';

            // Get duration
            $bookingType = $transaction->booking_type ?? 'daily';
            $duration = 0;
            if ($bookingType === 'monthly') {
                $duration = $transaction->booking_months ?? 0;
            } else {
                $duration = $transaction->booking_days ?? 0;
            }

            // Get price per unit (daily or monthly rate)
            $pricePerUnit = 0;
            if ($bookingType === 'monthly') {
                $pricePerUnit = $transaction->monthly_price ?? 0;
            } else {
                $pricePerUnit = $transaction->daily_price ?? 0;
            }

            // Calculate DPP (Dasar Pengenaan Pajak) - price without VAT 11%
            $dppKamarPerUnit = $pricePerUnit / 1.11;

            // Subtotal = Duration * DPP Kamar Per Unit
            $subtotal = $duration * $dppKamarPerUnit;

            // Discount (from voucher)
            $diskon = $transaction->discount_amount ?? 0;
            $dppDiskon = $diskon / 1.11;

            // Parking
            $parkir = $transaction->parking_fee ?? 0;
            $dppParkir = $parkir / 1.11;

            // Deposit Fee
            $depositFee = $transaction->deposit_fee ?? 0;
            $dppDepositFee = $depositFee / 1.11;

            // VATT 11% = (Subtotal - DPP Diskon + DPP Parkir + DPP Deposit Fee) * 11%
            $vatt = ($subtotal - $dppDiskon + $dppParkir + $dppDepositFee) * 0.11;

            // Room type (name from m_rooms)
            $roomType = '-';
            if ($transaction->room) {
                $roomType = $transaction->room->name ?? '-';
            }

            // Room number (no from m_rooms)
            $roomNumber = $transaction->room ? ($transaction->room->no ?? '-') : '-';

            // Get NIK from user if registered, otherwise null
            $nik = $transaction->user ? ($transaction->user->nik ?? '-') : '-';

            // Get verified_by username
            $verifiedBy = '-';
            if ($payment && $payment->verifiedBy) {
                $verifiedBy = $payment->verifiedBy->username ?? '-';
            }

            return [
                'no' => $offset + $index + 1,
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-',
                'transaction_code' => $transaction->transaction_code ?? '-',
                'property_name' => $transaction->property_name ?? '-',
                'room_type' => $roomType,
                'room_number' => $roomNumber,
                'room_name' => $roomNumber,
                'tenant_name' => $transaction->user_name ?? '-',
                'nik' => $nik,
                'mobile_number' => $transaction->user_phone_number ?? '-',
                'email' => $transaction->user_email ?? '-',
                'check_in' => $transaction->check_in ? Carbon::parse($transaction->check_in)->format('d M Y') : '-',
                'check_out' => $transaction->check_out ? Carbon::parse($transaction->check_out)->format('d M Y') : '-',
                'duration' => $duration . ' ',
                'price_per_unit' => 'Rp ' . number_format($pricePerUnit, 0, ',', '.'),
                'dpp_kamar_per_unit' => 'Rp ' . number_format(round($dppKamarPerUnit, 0), 0, ',', '.'),
                'subtotal' => 'Rp ' . number_format(round($subtotal, 0), 0, ',', '.'),
                'diskon' => 'Rp ' . number_format(round($diskon, 0), 0, ',', '.'),
                'dpp_diskon' => 'Rp ' . number_format(round($dppDiskon, 0), 0, ',', '.'),
                'parkir' => 'Rp ' . number_format(round($parkir, 0), 0, ',', '.'),
                'dpp_parkir' => 'Rp ' . number_format(round($dppParkir, 0), 0, ',', '.'),
                'vatt' => 'Rp ' . number_format(round($vatt, 0), 0, ',', '.'),
                'grand_total' => 'Rp ' . number_format($transaction->grandtotal_price ?? 0, 0, ',', '.'),
                'deposit' => 'Rp ' . number_format($transaction->deposit ?? 0, 0, ',', '.'),
                'deposit_fee' => 'Rp ' . number_format(round($depositFee, 0), 0, ',', '.'),
                'dpp_deposit_fee' => 'Rp ' . number_format(round($dppDepositFee, 0), 0, ',', '.'),
                'service_fee' => 'Rp ' . number_format($transaction->service_fees ?? 0, 0, ',', '.'),
                'payment_status' => 'Paid',
                'verified_by' => $verifiedBy,
                'verified_at' => $payment && $payment->verified_at ? Carbon::parse($payment->verified_at)->format('d M Y H:i') : '-',
                'notes' => $this->formatNotes($transaction, $isRefund, $refundInfo),
                'is_refund' => $isRefund,
                // Legacy fields for backward compatibility
                'order_id' => $transaction->order_id,
                'payment_date' => $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-',
                'room_price' => 'Rp ' . number_format($transaction->room_price ?? 0, 0, ',', '.'),
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

        // Set property_id based on user access
        // Super Admin and HO users can select any property
        // Site users are restricted to their assigned property
        $propertyId = ($user->isSuperAdmin() || $user->isHO())
            ? $request->input('property_id')
            : $user->property_id;

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'property_id' => $propertyId,
            'search' => $request->input('search'),
        ];

        $filename = 'payment-report-' . now()->format('Y-m-d-His') . '.xlsx';

        $exporter = new PaymentReportExport($filters);
        return $exporter->export($filename);
    }

    private function formatNotes($transaction, $isRefund, $refundInfo)
    {
        $notes = $transaction->payment->notes ?? '';

        if ($isRefund && $refundInfo) {
            $refundDate = Carbon::parse($refundInfo->refund_date)->format('d M Y');
            $refundText = "REFUNDED on {$refundDate}";

            if ($refundInfo->reason) {
                $refundText .= " - Reason: {$refundInfo->reason}";
            }

            if ($refundInfo->amount) {
                $refundText .= " - Amount: Rp " . number_format($refundInfo->amount, 0, ',', '.');
            }

            $notes = $notes ? $notes . " | " . $refundText : $refundText;
        }

        return $notes;
    }
}
