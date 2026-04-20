<?php

namespace App\Services;

use App\Models\DepositFeeTransaction;
use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Generates and persists invoice numbers using the format:
 *   Booking / Parking: {seq:4}/{property_initial}/{invoice_code}-INV/{roman_month}/{year}
 *   Deposit:           {seq:4}/DEP-{property_initial}/{invoice_code}-INV/{roman_month}/{year}
 *
 * Rules:
 * - Only paid transactions get an invoice number.
 * - Transactions with paid_at < 2026-03-01 are skipped (no number assigned).
 * - Sequence is per (counter_key, year) and resets each January 1.
 *   - Booking (t_transactions) and parking (t_parking_fee_transaction) share
 *     counter_key = invoice_code (e.g. "KGA") — same counter.
 *   - Deposit (t_deposit_fee_transaction) uses counter_key = "DEP-{invoice_code}" —
 *     independent per-property counter so deposit numbering starts from 1 regardless
 *     of booking volume.
 * - The number is assigned once and never recomputed; refunds keep it.
 */
class InvoiceNumberService
{
    /** First date eligible for the new persisted invoice numbering. */
    const CUTOFF_DATE = '2026-03-01';

    protected static array $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Assign and persist an invoice number for a paid transaction.
     * Accepts a booking Transaction, ParkingFeeTransaction, or DepositFeeTransaction.
     * Returns the assigned number, or null if the transaction is ineligible
     * (not paid, paid_at before cutoff, or property without invoice_code).
     */
    public static function assign($transaction): ?string
    {
        if (!$transaction) {
            return null;
        }

        // Already assigned — never recompute (refunds preserve the number)
        $existing = self::getStoredNumber($transaction);
        if (!empty($existing)) {
            return $existing;
        }

        // Must be paid
        if (($transaction->transaction_status ?? null) !== 'paid') {
            return null;
        }

        // Must have paid_at on/after cutoff
        if (empty($transaction->paid_at)) {
            return null;
        }
        $paidAt = Carbon::parse($transaction->paid_at);
        if ($paidAt->lt(Carbon::parse(self::CUTOFF_DATE))) {
            return null;
        }

        // Resolve property. Deposit transactions belong to a booking via order_id,
        // so fall back to the linked booking Transaction's property_id for deposits.
        $propertyId = $transaction->property_id ?? null;
        if (empty($propertyId) && $transaction instanceof DepositFeeTransaction) {
            $linkedBooking = Transaction::where('order_id', $transaction->order_id)->first();
            $propertyId = $linkedBooking?->property_id;
        }

        $property = $propertyId ? Property::find($propertyId) : null;
        if (!$property || empty($property->invoice_code)) {
            Log::warning('InvoiceNumberService: missing property or invoice_code', [
                'transaction_type'  => get_class($transaction),
                'transaction_idrec' => $transaction->idrec ?? null,
                'property_id'       => $propertyId,
            ]);
            return null;
        }

        $invoiceCode     = strtoupper($property->invoice_code);
        $propertyInitial = strtoupper($property->initial ?? '');
        $year            = (int) $paidAt->format('Y');
        $month           = (int) $paidAt->format('n');
        $romanMonth      = self::$romanMonths[$month];

        // Deposits use a distinct counter key ("DEP-{code}") and a "DEP-{initial}" segment
        // in the format so deposit invoice numbers are visually & sequentially distinct
        // from booking/parking numbers.
        $isDeposit         = $transaction instanceof DepositFeeTransaction;
        $counterKey        = $isDeposit ? ('DEP-' . $invoiceCode) : $invoiceCode;
        $formatInitialPart = $isDeposit ? ('DEP-' . $propertyInitial) : $propertyInitial;

        // Atomically increment per (counter_key, year) counter and write back
        return DB::transaction(function () use ($transaction, $counterKey, $formatInitialPart, $invoiceCode, $year, $romanMonth) {
            // Ensure the counter row exists, then lock + increment
            DB::table('m_invoice_sequences')->insertOrIgnore([
                'invoice_code' => $counterKey,
                'year'         => $year,
                'last_seq'     => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $row = DB::table('m_invoice_sequences')
                ->where('invoice_code', $counterKey)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            $next = ((int) $row->last_seq) + 1;

            DB::table('m_invoice_sequences')
                ->where('idrec', $row->idrec)
                ->update(['last_seq' => $next, 'updated_at' => now()]);

            $invoiceNumber = sprintf(
                '%s/%s/%s-INV/%s/%d',
                str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                $formatInitialPart,
                $invoiceCode,
                $romanMonth,
                $year
            );

            self::storeNumber($transaction, $invoiceNumber);

            return $invoiceNumber;
        });
    }

    /**
     * Reads the persisted invoice number from whichever column the model uses
     * (t_transactions.invoice_number vs t_parking_fee_transaction.invoice_id vs
     * t_deposit_fee_transaction.invoice_id).
     */
    protected static function getStoredNumber($transaction): ?string
    {
        if ($transaction instanceof ParkingFeeTransaction || $transaction instanceof DepositFeeTransaction) {
            return $transaction->invoice_id;
        }
        return $transaction->invoice_number ?? null;
    }

    /**
     * Persists the invoice number to the correct column for the given model.
     */
    protected static function storeNumber($transaction, string $invoiceNumber): void
    {
        if ($transaction instanceof ParkingFeeTransaction || $transaction instanceof DepositFeeTransaction) {
            $transaction->invoice_id = $invoiceNumber;
            $transaction->save();
            return;
        }
        $transaction->invoice_number = $invoiceNumber;
        $transaction->save();
    }
}
