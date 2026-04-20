<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Frontend API mirror of the Backend InvoiceNumberService. Assigns a persisted
 * invoice number to booking transactions the moment they flip to `paid` via a
 * DOKU webhook (VA / QRIS / Credit Card). Only handles App\Models\Transaction
 * because DOKU callbacks never touch parking or deposit — those live solely
 * in the Backend admin app.
 *
 * Format: {seq:4}/{property_initial}/{invoice_code}-INV/{roman_month}/{year}
 *
 * Rules:
 * - Only paid transactions get a number.
 * - paid_at < 2026-03-01 → skipped (leaves column NULL, like pre-cutoff rows).
 * - Sequence per (invoice_code, year), resets each January 1. Shared counter
 *   with bookings + parking in the Backend app via m_invoice_sequences (both
 *   Laravel apps connect to the same DB).
 * - Number is assigned once and never recomputed; refunds preserve it.
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
     * Assign and persist an invoice number for a paid booking Transaction.
     * Returns the assigned number, or null if ineligible.
     */
    public static function assign(?Transaction $transaction): ?string
    {
        if (!$transaction) {
            return null;
        }

        // Idempotent: already assigned → never recompute (refunds keep it)
        if (!empty($transaction->invoice_number)) {
            return $transaction->invoice_number;
        }

        if (($transaction->transaction_status ?? null) !== 'paid') {
            return null;
        }

        if (empty($transaction->paid_at)) {
            return null;
        }
        $paidAt = Carbon::parse($transaction->paid_at);
        if ($paidAt->lt(Carbon::parse(self::CUTOFF_DATE))) {
            return null;
        }

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

        return DB::transaction(function () use ($transaction, $invoiceCode, $propertyInitial, $year, $romanMonth) {
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

            // Direct assignment + save bypasses $fillable guard (mass-assignment only).
            $transaction->invoice_number = $invoiceNumber;
            $transaction->save();

            return $invoiceNumber;
        });
    }
}
