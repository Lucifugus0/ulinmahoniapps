<?php

namespace App\Console\Commands;

use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfills invoice_number / invoice_id for paid bookings + parking on/after
 * the cutoff date (2026-03-01). Each transaction type walks its own counter
 * independently:
 *   - Bookings -> counter_key = invoice_code         e.g. "KGA"
 *   - Parking  -> counter_key = "PRK-{invoice_code}" e.g. "PRK-KGA"
 *     and uses the format segment "PRK-{initial}" instead of just "{initial}".
 *
 * Pre-cutoff rows (paid_at < 2026-03-01) are intentionally left NULL.
 *
 * Usage:
 *   php artisan invoices:backfill                     Fill rows that have no number yet.
 *   php artisan invoices:backfill --dry-run           Preview without writes.
 *   php artisan invoices:backfill --reset             DESTRUCTIVE: wipe post-cutoff
 *                                                     booking + parking numbers and the
 *                                                     matching m_invoice_sequences rows,
 *                                                     then re-assign cleanly. Use this
 *                                                     after the parking format change so
 *                                                     bookings get gap-free sequences and
 *                                                     parking gets the new "PRK-" format.
 *   php artisan invoices:backfill --reset --dry-run   Preview the reset without writes.
 */
class BackfillInvoiceNumbers extends Command
{
    protected $signature = 'invoices:backfill
                            {--dry-run : Print what would be assigned without writing}
                            {--reset : Wipe existing post-cutoff booking + parking numbers and counters, then re-assign}';

    protected $description = 'Assign persisted invoice numbers to paid bookings + parking transactions on/after 2026-03-01';

    const CUTOFF = '2026-03-01 00:00:00';

    protected array $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $reset  = (bool) $this->option('reset');

        if ($reset && !$dryRun) {
            $this->warn('--reset will WIPE invoice_number on t_transactions and invoice_id on');
            $this->warn('t_parking_fee_transaction for all rows with paid_at >= ' . self::CUTOFF . ',');
            $this->warn('and delete the matching counter rows in m_invoice_sequences.');
            if (!$this->confirm('Proceed with reset?', false)) {
                $this->info('Aborted.');
                return Command::SUCCESS;
            }
        }

        $this->info($dryRun ? 'Backfill DRY RUN — no writes will occur.' : 'Backfilling invoice numbers...');

        // Map property_id => [initial, invoice_code]
        $properties = Property::whereNotNull('invoice_code')
            ->get(['idrec', 'initial', 'invoice_code'])
            ->keyBy('idrec');

        if ($properties->isEmpty()) {
            $this->error('No properties have invoice_code set — populate m_properties.invoice_code first.');
            return Command::FAILURE;
        }

        if ($reset) {
            $this->resetPostCutoff($properties, $dryRun);
        }

        // In a real --reset run the wipe above has already nulled the columns,
        // so the default whereNull filter selects every post-cutoff row.
        // In --reset --dry-run we skip the wipe, so we have to bypass that
        // filter explicitly to preview what the reset+backfill would produce.
        $includeAll = $reset && $dryRun;

        // freshCounters mirrors the same reason: in --reset --dry-run the actual
        // counter delete didn't happen, so reading m_invoice_sequences would seed
        // from stale post-cutoff values and the preview would show sequences
        // continuing from where the old run left off (e.g. 0178 instead of 0001).
        $freshCounters = $reset && $dryRun;

        $bookingAssigned = $this->backfillBookings($properties, $dryRun, $includeAll, $freshCounters);
        $parkingAssigned = $this->backfillParking($properties, $dryRun, $includeAll, $freshCounters);

        $this->info("Done. Bookings assigned: {$bookingAssigned}, Parking assigned: {$parkingAssigned}");
        if ($dryRun) {
            $this->warn('Dry run — no rows were modified.');
        }

        return Command::SUCCESS;
    }

    /**
     * Wipe post-cutoff invoice numbers on t_transactions / t_parking_fee_transaction
     * and delete the matching m_invoice_sequences counter rows so the rebuild starts
     * cleanly from sequence 1 per (counter_key, year).
     *
     * Deposits are intentionally NOT touched — their counter ("DEP-{code}") is
     * independent and the deposit flow assigns numbers at creation/approval time.
     */
    protected function resetPostCutoff($properties, bool $dryRun): void
    {
        $this->line('  Resetting post-cutoff booking + parking invoice numbers...');

        $bookingCount = Transaction::whereNotNull('invoice_number')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', self::CUTOFF)
            ->count();

        // Parking wipe is intentionally broader than the booking wipe: we clear ALL parking
        // rows that currently carry an invoice_id, not just those with paid_at >= cutoff.
        // The new parking eligibility rule (transaction_date >= cutoff) may exclude rows
        // that were numbered under the OLD rule (paid_at >= cutoff but transaction_date < cutoff),
        // so the wipe has to catch them too — otherwise they'd keep stale numbers post-reset.
        $parkingCount = ParkingFeeTransaction::whereNotNull('invoice_id')->count();

        $this->line("    Booking rows to clear: {$bookingCount}");
        $this->line("    Parking rows to clear: {$parkingCount}");

        // Counter keys to drop: booking uses invoice_code; parking uses PRK-{invoice_code}.
        $bookingKeys = $properties->pluck('invoice_code')->map(fn($c) => strtoupper($c))->unique()->values()->all();
        $parkingKeys = array_map(fn($c) => 'PRK-' . $c, $bookingKeys);
        $allKeys     = array_merge($bookingKeys, $parkingKeys);

        $counterRowCount = DB::table('m_invoice_sequences')->whereIn('invoice_code', $allKeys)->count();
        $this->line("    Counter rows to delete: {$counterRowCount} (booking + parking keys)");

        if ($dryRun) {
            return;
        }

        Transaction::whereNotNull('invoice_number')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', self::CUTOFF)
            ->update(['invoice_number' => null, 'updated_at' => now()]);

        // Parking wipe: clear EVERY row with invoice_id (see scope comment above).
        ParkingFeeTransaction::whereNotNull('invoice_id')
            ->update(['invoice_id' => null, 'updated_at' => now()]);

        DB::table('m_invoice_sequences')->whereIn('invoice_code', $allKeys)->delete();

        $this->line('  Reset complete.');
    }

    /**
     * Walk paid bookings on/after cutoff in payment order, assigning sequential
     * numbers from the per-(invoice_code, year) booking counter.
     */
    protected function backfillBookings($properties, bool $dryRun, bool $includeAll = false, bool $freshCounters = false): int
    {
        $query = Transaction::whereNotNull('paid_at')
            ->where('paid_at', '>=', self::CUTOFF)
            ->whereIn('property_id', $properties->keys());
        if (!$includeAll) {
            $query->whereNull('invoice_number');
        }
        $bookings = $query->orderBy('paid_at')
            ->orderBy('idrec')
            ->get(['idrec', 'property_id', 'paid_at']);

        $this->line("  Bookings to assign: {$bookings->count()}");
        if ($bookings->isEmpty()) {
            return 0;
        }

        $counters = []; // key = "{invoice_code}|{year}" => last_seq
        $assigned = 0;

        foreach ($bookings as $t) {
            $property = $properties->get($t->property_id);
            if (!$property || empty($property->invoice_code)) {
                continue;
            }

            $invoiceCode     = strtoupper($property->invoice_code);
            $propertyInitial = strtoupper($property->initial ?? '');
            $paidAt          = Carbon::parse($t->paid_at);
            $year            = (int) $paidAt->format('Y');
            $month           = (int) $paidAt->format('n');

            $next = $this->nextSeq($counters, $invoiceCode, $year, $freshCounters);

            $invoiceNumber = sprintf(
                '%s/%s/%s-INV/%s/%d',
                str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                $propertyInitial,
                $invoiceCode,
                $this->romanMonths[$month],
                $year
            );

            $this->line(sprintf(
                '  booking #%s  paid_at=%s  -> %s',
                $t->idrec,
                $paidAt->format('Y-m-d H:i:s'),
                $invoiceNumber
            ));

            if (!$dryRun) {
                DB::table('t_transactions')
                    ->where('idrec', $t->idrec)
                    ->update(['invoice_number' => $invoiceNumber, 'updated_at' => now()]);
            }

            $assigned++;
        }

        if (!$dryRun) {
            $this->persistCounters($counters);
        }

        return $assigned;
    }

    /**
     * Walk paid parking transactions on/after cutoff in payment order, assigning
     * sequential numbers from the per-("PRK-{invoice_code}", year) counter.
     * Format segment is "PRK-{initial}" so parking is visually distinct from
     * bookings.
     */
    protected function backfillParking($properties, bool $dryRun, bool $includeAll = false, bool $freshCounters = false): int
    {
        // Filter on status = 1 so soft-deleted duplicate rows (status = 0, created by the
        // 2026-05-13 dedup migration) never get renumbered — re-numbering them would
        // re-introduce the duplicate invoice_id collisions the migration just cleaned up.
        //
        // Walk both 'paid' (active rentals) and 'completed' (rentals whose period has ended)
        // transaction_status values. Both represent money-was-received historical rows that
        // belong in the invoice sequence. The runtime path (`InvoiceNumberService::assign`)
        // still only assigns on 'paid' — assignment happens at payment time; the transition
        // to 'completed' happens later by which point the number is already set.
        //
        // Eligibility cutoff for add-on parking uses COALESCE(transaction_date, paid_at)
        // (not paid_at like bookings/deposits). Admins routinely back-date parking entries
        // weeks after the actual money date, so paid_at would qualify rows that legitimately
        // belong to a pre-cutoff period (e.g. a Jan-period parking entered in March).
        // Per the rule "add-on parking invoices start from 01 March 2026", we anchor on
        // transaction_date and only fall back to paid_at when transaction_date is null.
        $query = ParkingFeeTransaction::whereIn('transaction_status', ['paid', 'completed'])
            ->whereNotNull('paid_at')
            ->whereRaw('COALESCE(transaction_date, DATE(paid_at)) >= ?', [substr(self::CUTOFF, 0, 10)])
            ->where('status', 1)
            ->whereIn('property_id', $properties->keys());
        if (!$includeAll) {
            $query->whereNull('invoice_id');
        }
        // Order by COALESCE(transaction_date, paid_at) so the sequence aligns with the
        // actual money-changed-hands order (matches the new month/year derivation below).
        $parking = $query->orderByRaw('COALESCE(transaction_date, paid_at) ASC')
            ->orderBy('idrec')
            ->get(['idrec', 'property_id', 'paid_at', 'transaction_date']);

        $this->line("  Parking to assign:  {$parking->count()}");
        if ($parking->isEmpty()) {
            return 0;
        }

        $counters = []; // key = "PRK-{invoice_code}|{year}" => last_seq
        $assigned = 0;

        foreach ($parking as $t) {
            $property = $properties->get($t->property_id);
            if (!$property || empty($property->invoice_code)) {
                continue;
            }

            $invoiceCode     = strtoupper($property->invoice_code);
            $propertyInitial = strtoupper($property->initial ?? '');
            $counterKey      = 'PRK-' . $invoiceCode;
            $paidAt          = Carbon::parse($t->paid_at);
            // The {roman_month}/{year} segments come from transaction_date (admin-keyed actual
            // payment date). Falls back to paid_at when transaction_date is missing. Mirrors
            // the runtime path in InvoiceNumberService so both code paths produce identical numbers.
            $invoiceDate     = !empty($t->transaction_date)
                ? Carbon::parse($t->transaction_date)
                : $paidAt;
            $year            = (int) $invoiceDate->format('Y');
            $month           = (int) $invoiceDate->format('n');

            $next = $this->nextSeq($counters, $counterKey, $year, $freshCounters);

            $invoiceNumber = sprintf(
                '%s/PRK-%s/%s-INV/%s/%d',
                str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                $propertyInitial,
                $invoiceCode,
                $this->romanMonths[$month],
                $year
            );

            $this->line(sprintf(
                '  parking #%s  paid_at=%s  -> %s',
                $t->idrec,
                $paidAt->format('Y-m-d H:i:s'),
                $invoiceNumber
            ));

            if (!$dryRun) {
                DB::table('t_parking_fee_transaction')
                    ->where('idrec', $t->idrec)
                    ->update(['invoice_id' => $invoiceNumber, 'updated_at' => now()]);
            }

            $assigned++;
        }

        if (!$dryRun) {
            $this->persistCounters($counters);
        }

        return $assigned;
    }

    /**
     * Returns the next sequence number for (counter_key, year), seeding from
     * m_invoice_sequences on first access in this run.
     */
    protected function nextSeq(array &$counters, string $counterKey, int $year, bool $fresh = false): int
    {
        $key = $counterKey . '|' . $year;
        if (!isset($counters[$key])) {
            // Normally seed from m_invoice_sequences so that incremental backfill
            // continues from where live assignments left off. With $fresh=true
            // (used by --reset --dry-run) we skip the seed because the real
            // --reset run would have deleted those rows first.
            if ($fresh) {
                $counters[$key] = 0;
            } else {
                $row = DB::table('m_invoice_sequences')
                    ->where('invoice_code', $counterKey)
                    ->where('year', $year)
                    ->first();
                $counters[$key] = $row ? (int) $row->last_seq : 0;
            }
        }
        return ++$counters[$key];
    }

    /**
     * Persist the final counter values back to m_invoice_sequences so that
     * future live assignments via InvoiceNumberService continue from where
     * the backfill left off.
     */
    protected function persistCounters(array $counters): void
    {
        foreach ($counters as $key => $lastSeq) {
            [$counterKey, $year] = explode('|', $key);
            DB::table('m_invoice_sequences')->updateOrInsert(
                ['invoice_code' => $counterKey, 'year' => (int) $year],
                ['last_seq' => $lastSeq, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
