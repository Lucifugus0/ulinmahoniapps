<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class InvoiceNumberService
{
    /**
     * The cutoff date from which the new annual reset system applies.
     * Transactions on or after this date use the new per-year sequence.
     * Transactions before this date retain their legacy sequential numbering.
     */
    const CUTOFF_DATE = '2026-03-01';

    protected static array $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Generate invoice number based on paid_at date.
     * Format: 0001/{property_initial}/KGA-INV/III/2026
     *
     * @param Transaction $transaction
     * @param int|null $sequenceNumber Optional override for sequence number
     * @return string
     */
    public static function generate(Transaction $transaction, ?int $sequenceNumber = null): string
    {
        $paidAt = $transaction->paid_at ? Carbon::parse($transaction->paid_at) : now();
        $year = $paidAt->format('Y');
        $month = (int) $paidAt->format('n');
        $romanMonth = self::$romanMonths[$month];

        // Get sequence number if not provided
        if ($sequenceNumber === null) {
            $sequenceNumber = self::getSequenceNumber($transaction, $year);
        }

        // Get property initial from transaction's property relationship (uppercase)
        $propertyInitial = 'PK1'; // Default fallback
        if ($transaction->property && $transaction->property->initial) {
            $propertyInitial = strtoupper($transaction->property->initial);
        }

        // Format: 0001/{property_initial}/KGA-INV/III/2026
        $formattedSequence = str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);

        return "{$formattedSequence}/{$propertyInitial}/KGA-INV/{$romanMonth}/{$year}";
    }

    /**
     * Determine the sequence start boundary for a given year.
     *
     * - Transactions BEFORE the cutoff date use legacy counting (all of that year).
     * - For year 2026 (new system): sequence starts from CUTOFF_DATE (March 1, 2026).
     * - For 2027 and beyond: sequence starts from January 1 of that year.
     *
     * @param string $year
     * @param bool $isLegacy Whether this is a legacy (pre-cutoff) transaction
     * @return string|null Start datetime string, or null for legacy (count all year)
     */
    protected static function getSequenceStart(string $year, bool $isLegacy): ?string
    {
        if ($isLegacy) {
            return null; // Legacy: count all transactions in that year before cutoff
        }

        if ($year === '2026') {
            return self::CUTOFF_DATE . ' 00:00:00';
        }

        return $year . '-01-01 00:00:00';
    }

    /**
     * Get sequence number for a single transaction.
     *
     * @param Transaction $transaction
     * @param string $year
     * @return int
     */
    protected static function getSequenceNumber(Transaction $transaction, string $year): int
    {
        $cutoff = Carbon::parse(self::CUTOFF_DATE);
        $paidAt = Carbon::parse($transaction->paid_at);
        $isLegacy = $paidAt->lt($cutoff);

        $query = Transaction::where('transaction_status', 'paid')
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', $year);

        if ($isLegacy) {
            // Legacy: only count transactions before the cutoff in that year
            $query->where('paid_at', '<', self::CUTOFF_DATE . ' 00:00:00');
        } else {
            // New system: count from the sequence start for that year
            $sequenceStart = self::getSequenceStart($year, false);
            $query->where('paid_at', '>=', $sequenceStart);
        }

        $count = $query->where(function ($q) use ($transaction) {
            $q->where('paid_at', '<', $transaction->paid_at)
                ->orWhere(function ($q2) use ($transaction) {
                    $q2->where('paid_at', '=', $transaction->paid_at)
                        ->where('idrec', '<=', $transaction->idrec);
                });
        })->count();

        return $count > 0 ? $count : 1;
    }

    /**
     * Generate invoice numbers for a collection of transactions.
     * Uses true global rank so the number is stable regardless of filters or per_page.
     *
     * Sequencing rules:
     * - Before 2026-03-01 (legacy): sequences are counted per-year, excluding new-system transactions.
     * - From 2026-03-01 (year 2026): sequence starts at 001, counting from March 1, 2026.
     * - From 2027 onwards: sequence starts at 001 each January 1.
     *
     * @param \Illuminate\Support\Collection $transactions
     * @return array Map of transaction idrec => invoice number
     */
    public static function generateBatch($transactions): array
    {
        $invoiceNumbers = [];
        $cutoff = Carbon::parse(self::CUTOFF_DATE);

        // Split into legacy (before cutoff) and new (from cutoff onwards)
        $legacy = $transactions->filter(
            fn($t) => $t->paid_at && Carbon::parse($t->paid_at)->lt($cutoff)
        );
        $newTxns = $transactions->filter(
            fn($t) => $t->paid_at && Carbon::parse($t->paid_at)->gte($cutoff)
        );

        // --- Handle legacy transactions ---
        $legacyByYear = $legacy->groupBy(
            fn($t) => Carbon::parse($t->paid_at)->format('Y')
        );

        foreach ($legacyByYear as $year => $yearTxns) {
            // Count only legacy (pre-cutoff) paid transactions in this year
            $orderedIdrecs = Transaction::where('transaction_status', 'paid')
                ->whereNotNull('paid_at')
                ->whereYear('paid_at', $year)
                ->where('paid_at', '<', self::CUTOFF_DATE . ' 00:00:00')
                ->orderBy('paid_at', 'asc')
                ->orderBy('idrec', 'asc')
                ->pluck('idrec')
                ->toArray();

            $rankMap = array_flip($orderedIdrecs);

            foreach ($yearTxns as $transaction) {
                $rank = isset($rankMap[$transaction->idrec])
                    ? $rankMap[$transaction->idrec] + 1
                    : 1;
                $invoiceNumbers[$transaction->idrec] = self::generate($transaction, $rank);
            }
        }

        // --- Handle new-system transactions ---
        $newByYear = $newTxns->groupBy(
            fn($t) => Carbon::parse($t->paid_at)->format('Y')
        );

        foreach ($newByYear as $year => $yearTxns) {
            $sequenceStart = self::getSequenceStart($year, false);

            $orderedIdrecs = Transaction::where('transaction_status', 'paid')
                ->whereNotNull('paid_at')
                ->whereYear('paid_at', $year)
                ->where('paid_at', '>=', $sequenceStart)
                ->orderBy('paid_at', 'asc')
                ->orderBy('idrec', 'asc')
                ->pluck('idrec')
                ->toArray();

            $rankMap = array_flip($orderedIdrecs);

            foreach ($yearTxns as $transaction) {
                $rank = isset($rankMap[$transaction->idrec])
                    ? $rankMap[$transaction->idrec] + 1
                    : 1;
                $invoiceNumbers[$transaction->idrec] = self::generate($transaction, $rank);
            }
        }

        return $invoiceNumbers;
    }

}
