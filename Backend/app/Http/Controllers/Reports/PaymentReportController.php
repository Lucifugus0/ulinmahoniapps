<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Property;
use App\Exports\PaymentReportExport;
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

        /* Status filter: default to all 3 post-payment-processing statuses (paid + cancelled + rejected).
           If the request specifies one explicitly, narrow to it. Anything outside the whitelist falls
           back to the full set. */
        $statusFilter = $request->input('status');
        $validStatuses = ['paid', 'cancelled', 'rejected'];
        $statusList = in_array($statusFilter, $validStatuses, true) ? [$statusFilter] : $validStatuses;

        $query = Transaction::with([
                'payment.verifiedBy',
                'property',
                'room',
                'booking.refund',
                'user'
            ])
            ->whereHas('payment')
            ->whereIn('transaction_status', $statusList)
            /* Order by the same expression we filter on — rejected rows have paid_at set by
               PaymentController::reject(), cancelled rows may have NULL paid_at but cancel_at is set,
               paid rows always have paid_at. COALESCE always resolves to a non-NULL date. */
            ->orderByRaw('COALESCE(paid_at, cancel_at, created_at) DESC');

        /* Transaction date range — uses COALESCE(paid_at, cancel_at, created_at) so it works
           uniformly for all 3 statuses. ~38% of cancelled rows have NULL paid_at; their cancel_at
           is always set, which is what the admin's mental model of "cancellation date" expects. */
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereRaw(
                'DATE(COALESCE(paid_at, cancel_at, created_at)) BETWEEN ? AND ?',
                [$request->start_date, $request->end_date]
            );
        } elseif ($request->filled('start_date')) {
            $query->whereRaw(
                'DATE(COALESCE(paid_at, cancel_at, created_at)) >= ?',
                [$request->start_date]
            );
        } elseif ($request->filled('end_date')) {
            $query->whereRaw(
                'DATE(COALESCE(paid_at, cancel_at, created_at)) <= ?',
                [$request->end_date]
            );
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

        // Transform data for display (28 columns)
        $data = $payments->map(function ($transaction, $index) use ($payments) {
            $payment = $transaction->payment;

            // Detect refund
            $isRefund = false;
            $refundInfo = null;
            if ($transaction->booking && $transaction->booking->refund) {
                $isRefund = true;
                $refundInfo = $transaction->booking->refund;
            }

            $offset = ($payments->currentPage() - 1) * $payments->perPage();

            // Read persisted invoice number — set when the transaction was paid
            $invoiceNumber = $transaction->invoice_number ?: '-';

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

            // <!-- Deposit Fee — not subject to VAT (refundable security deposit) -->
            $depositFee = $transaction->deposit_fee ?? 0;

            // <!-- VATT = (Subtotal - DPP Diskon + DPP Parkir) × 11% — deposit excluded from VAT base -->
            $vatt = ($subtotal - $dppDiskon + $dppParkir) * 0.11;

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
                /* Show booking ID (order_id) instead of internal transaction_code */
                'transaction_code' => $transaction->order_id ?? '-',
                'property_name' => $transaction->property_name ?? '-',
                'room_type' => $roomType,
                'room_number' => $roomNumber,
                'room_name' => $roomNumber,
                /* Tenant name from user.first_name + last_name (per finance team request);
                   falls back to legacy transaction.user_name when user is not linked */
                'tenant_name' => $transaction->user
                    ? trim(($transaction->user->first_name ?? '') . ' ' . ($transaction->user->last_name ?? ''))
                        ?: ($transaction->user_name ?? '-')
                    : ($transaction->user_name ?? '-'),
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
                /* Removed standalone 'deposit' field — table now shows only deposit_fee under the "Deposit" header */
                'deposit_fee' => 'Rp ' . number_format(round($depositFee, 0), 0, ',', '.'),
                'service_fee' => 'Rp ' . number_format($transaction->service_fees ?? 0, 0, ',', '.'),
                /* Payment status reflects the transaction lifecycle + (for cancelled) the refund scheme:
                     - "Paid"        → green badge
                     - "Rejected"    → red badge — admin rejected payment proof
                     - "NO REFUND"   → orange badge — cancelled with refund.amount = 0
                     - "FULL REFUND" → orange badge — cancelled with refund.amount == room+deposit+parking
                     - "REFUND"     → orange badge — cancelled with tier-based refund (any other amount > 0) */
                'payment_status' => $this->resolvePaymentStatus($transaction, $refundInfo)['label'],
                'payment_status_class' => $this->resolvePaymentStatus($transaction, $refundInfo)['class'],
                /* Show payment bank as the verifier and paid_at as verification time (per finance team request) */
                'verified_by' => $transaction->payment_bank ?? '-',
                'verified_at' => $transaction->paid_at ? Carbon::parse($transaction->paid_at)->format('d M Y H:i') : '-',
                'notes' => $this->formatNotes($transaction, $isRefund, $refundInfo),
                'is_refund' => $isRefund,
                /* Rejected flag for row tinting in the table + Excel export (parallel to is_refund). */
                'is_rejected' => strtolower($transaction->transaction_status ?? '') === 'rejected',
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
            /* Pass status through to the export so the spreadsheet matches what's on screen. */
            'status' => $request->input('status'),
        ];

        $filename = 'payment-report-' . now()->format('Y-m-d-His') . '.xlsx';

        $exporter = new PaymentReportExport($filters);
        return $exporter->export($filename);
    }

    /**
     * Resolve the payment status label + Tailwind badge class.
     *
     * Returns ['label' => ..., 'class' => ...].
     *
     * Categories:
     *   - "Paid"                     → green   — not cancelled, not rejected
     *   - "Cancelled - NO REFUND"    → orange  — cancelled, refund amount = 0
     *   - "Cancelled - FULL REFUND"  → orange  — cancelled, refund amount = room + deposit + parking
     *   - "Cancelled - REFUND"       → orange  — cancelled, tier-based refund (any other amount > 0)
     *   - "Rejected - NO REFUND"     → red     — rejected, refund amount = 0
     *   - "Rejected - FULL REFUND"   → red     — rejected, full refund processed
     *   - "Rejected - REFUND"        → red     — rejected, partial refund
     */
    private function resolvePaymentStatus($transaction, $refundInfo): array
    {
        $status = strtolower($transaction->transaction_status ?? '');

        /* Rejected and Cancelled both attach a refund-variant suffix. Rejected rows keep the red badge
           (lifecycle dominates), cancelled rows get the orange badge. Paid rows skip the variant.
           Dark-mode classes follow the same pattern used by `newreserve_table.blade.php` status pills:
           `bg-{color}-900/40 dark:text-{color}-300` — translucent dark surface + light text. */
        if ($status === 'rejected') {
            return [
                'label' => 'Rejected - ' . $this->resolveRefundVariant($transaction, $refundInfo),
                'class' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
            ];
        }

        if ($status === 'cancelled') {
            return [
                'label' => 'Cancelled - ' . $this->resolveRefundVariant($transaction, $refundInfo),
                'class' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
            ];
        }

        return [
            'label' => 'Paid',
            'class' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
        ];
    }

    /**
     * Decide which refund variant applies to a transaction based on its refund record:
     *   - "NO REFUND"   when no refund record exists or refund.amount <= 0
     *   - "FULL REFUND" when refund.amount equals room + deposit + parking (within float tolerance)
     *   - "REFUND"      otherwise (partial / tier-based refund)
     */
    private function resolveRefundVariant($transaction, $refundInfo): string
    {
        $refundAmount = (float) ($refundInfo->amount ?? 0);
        if ($refundAmount <= 0) {
            return 'NO REFUND';
        }

        $fullRefundAmount = (float) ($transaction->room_price ?? 0)
            + (float) ($transaction->deposit_fee ?? 0)
            + (float) ($transaction->parking_fee ?? 0);

        /* Allow tiny float drift (rounding) when comparing to full refund */
        if ($fullRefundAmount > 0 && abs($refundAmount - $fullRefundAmount) < 1) {
            return 'FULL REFUND';
        }

        return 'REFUND';
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
