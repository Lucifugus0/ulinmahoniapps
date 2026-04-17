<?php

namespace App\Services;

use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Generates and persists invoice numbers using the format:
 *   {seq:4}/{property_initial}/{invoice_code}-INV/{roman_month}/{year}
 *
 * Rules:
 * - Only paid transactions get an invoice number.
 * - Transactions with paid_at < 2026-03-06 are skipped (no number assigned).
 * - Sequence is per (invoice_code, year) and resets each January 1.
 * - Booking (t_transactions) and parking (t_parking_fee_transaction) share
 *   the same per-invoice-code counter via m_invoice_sequences.
 * - The number is assigned once and never recomputed; refunds keep it.
 */
class InvoiceNumberService
{
    /** First date eligible for the new persisted invoice numbering. */
    const CUTOFF_DATE = '2026-03-06';

    protected static array $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Assign and persist an invoice number for a paid transaction.
     * Accepts either a booking Transaction or a ParkingFeeTransaction.
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

        // Resolve property — invoice_code is mandatory for new format
        $property = Property::find($transaction->property_id);
        if (!$property || empty($property->invoice_code)) {
            Log::warning('InvoiceNumberService: missing property or invoice_code', [
                'transaction_idrec' => $transaction->idrec ?? null,
                'property_id'       => $transaction->property_id ?? null,
            ]);
            return null;
        }

        $invoiceCode     = strtoupper($property->invoice_code);
        $propertyInitial = strtoupper($property->initial ?? '');
        $year            = (int) $paidAt->format('Y');
        $month           = (int) $paidAt->format('n');
        $romanMonth      = self::$romanMonths[$month];

        // Atomically increment per (invoice_code, year) counter and write back
        return DB::transaction(function () use ($transaction, $invoiceCode, $propertyInitial, $year, $romanMonth) {
            // Ensure the counter row exists, then lock + increment
            DB::table('m_invoice_sequences')->insertOrIgnore([
                'invoice_code' => $invoiceCode,
                'year'         => $year,
                'last_seq'     => 0,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);

            $row = DB::table('m_invoice_sequences')
                ->where('invoice_code', $invoiceCode)
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
                $propertyInitial,
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
     * (t_transactions.invoice_number vs t_parking_fee_transaction.invoice_id).
     */
    protected static function getStoredNumber($transaction): ?string
    {
        if ($transaction instanceof ParkingFeeTransaction) {
            return $transaction->invoice_id;
        }
        return $transaction->invoice_number ?? null;
    }

    /**
     * Persists the invoice number to the correct column for the given model.
     */
    protected static function storeNumber($transaction, string $invoiceNumber): void
    {
        if ($transaction instanceof ParkingFeeTransaction) {
            $transaction->invoice_id = $invoiceNumber;
            $transaction->save();
            return;
        }
        $transaction->invoice_number = $invoiceNumber;
        $transaction->save();
    }
}
