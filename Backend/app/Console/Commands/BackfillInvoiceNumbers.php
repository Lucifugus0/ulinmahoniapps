<?php

namespace App\Console\Commands;

use App\Models\ParkingFeeTransaction;
use App\Models\Property;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfills invoice_number / invoice_id for paid transactions on/after
 * the cutoff date (2026-03-06). Walks bookings and parking transactions
 * together, ordered chronologically by paid_at, so the shared per-
 * (invoice_code, year) sequence reflects real payment order.
 *
 * Run once after deploying the migrations + service rewrite:
 *   php artisan invoices:backfill
 *   php artisan invoices:backfill --dry-run    (preview without writes)
 */
class BackfillInvoiceNumbers extends Command
{
    protected $signature = 'invoices:backfill {--dry-run : Print what would be assigned without writing}';
    protected $description = 'Assign persisted invoice numbers to paid bookings + parking transactions on/after 2026-03-06';

    const CUTOFF = '2026-03-06 00:00:00';

    protected array $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info($dryRun ? 'Backfill DRY RUN — no writes will occur.' : 'Backfilling invoice numbers...');

        // Map property_id => [initial, invoice_code]
        $properties = Property::whereNotNull('invoice_code')
            ->get(['idrec', 'initial', 'invoice_code'])
            ->keyBy('idrec');

        if ($properties->isEmpty()) {
            $this->error('No properties have invoice_code set — populate m_properties.invoice_code first.');
            return Command::FAILURE;
        }

        // Pull bookings that were ever paid (paid_at set) on/after cutoff and
        // have no invoice number. Status may now be 'paid', 'refunded', or
        // 'cancelled' — once an invoice number is issued, it must stick (rule #8).
        $bookings = Transaction::whereNull('invoice_number')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', self::CUTOFF)
            ->whereIn('property_id', $properties->keys())
            ->orderBy('paid_at')
            ->orderBy('idrec')
            ->get(['idrec', 'property_id', 'paid_at']);

        // Same rule for parking — include 'completed' (post-checkout) etc.
        $parking = ParkingFeeTransaction::whereNull('invoice_id')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', self::CUTOFF)
            ->whereIn('property_id', $properties->keys())
            ->orderBy('paid_at')
            ->orderBy('idrec')
            ->get(['idrec', 'property_id', 'paid_at']);

        $this->line("  Bookings to assign: {$bookings->count()}");
        $this->line("  Parking to assign:  {$parking->count()}");

        // Merge into one stream and sort by paid_at globally.
        // Each item carries its source so we know which table to write back to.
        $stream = collect();
        foreach ($bookings as $t) {
            $stream->push(['source' => 'booking', 'idrec' => $t->idrec, 'property_id' => $t->property_id, 'paid_at' => Carbon::parse($t->paid_at)]);
        }
        foreach ($parking as $t) {
            $stream->push(['source' => 'parking', 'idrec' => $t->idrec, 'property_id' => $t->property_id, 'paid_at' => Carbon::parse($t->paid_at)]);
        }

        $stream = $stream->sortBy([
            ['paid_at', 'asc'],
            ['source', 'asc'],
            ['idrec', 'asc'],
        ])->values();

        if ($stream->isEmpty()) {
            $this->info('Nothing to backfill.');
            return Command::SUCCESS;
        }

        // Per-(invoice_code, year) running counters seeded from existing m_invoice_sequences
        $counters = [];

        $assigned = 0;
        $skipped  = 0;

        foreach ($stream as $item) {
            $property = $properties->get($item['property_id']);
            if (!$property || empty($property->invoice_code)) {
                $skipped++;
                continue;
            }

            $invoiceCode     = strtoupper($property->invoice_code);
            $propertyInitial = strtoupper($property->initial ?? '');
            $year            = (int) $item['paid_at']->format('Y');
            $month           = (int) $item['paid_at']->format('n');
            $key             = $invoiceCode . '|' . $year;

            if (!isset($counters[$key])) {
                $row = DB::table('m_invoice_sequences')
                    ->where('invoice_code', $invoiceCode)
                    ->where('year', $year)
                    ->first();
                $counters[$key] = $row ? (int) $row->last_seq : 0;
            }

            $next = ++$counters[$key];

            $invoiceNumber = sprintf(
                '%s/%s/%s-INV/%s/%d',
                str_pad((string) $next, 4, '0', STR_PAD_LEFT),
                $propertyInitial,
                $invoiceCode,
                $this->romanMonths[$month],
                $year
            );

            $this->line(sprintf(
                '  %s #%s  paid_at=%s  -> %s',
                $item['source'],
                $item['idrec'],
                $item['paid_at']->format('Y-m-d H:i:s'),
                $invoiceNumber
            ));

            if (!$dryRun) {
                if ($item['source'] === 'booking') {
                    DB::table('t_transactions')
                        ->where('idrec', $item['idrec'])
                        ->update(['invoice_number' => $invoiceNumber, 'updated_at' => now()]);
                } else {
                    DB::table('t_parking_fee_transaction')
                        ->where('idrec', $item['idrec'])
                        ->update(['invoice_id' => $invoiceNumber, 'updated_at' => now()]);
                }
            }

            $assigned++;
        }

        // Persist the final counter values back to m_invoice_sequences
        if (!$dryRun) {
            foreach ($counters as $key => $lastSeq) {
                [$invoiceCode, $year] = explode('|', $key);
                DB::table('m_invoice_sequences')->updateOrInsert(
                    ['invoice_code' => $invoiceCode, 'year' => (int) $year],
                    ['last_seq' => $lastSeq, 'updated_at' => now(), 'created_at' => now()]
                );
            }
        }

        $this->info("Done. Assigned: {$assigned}, Skipped (no invoice_code): {$skipped}");
        if ($dryRun) {
            $this->warn('Dry run — no rows were modified.');
        }

        return Command::SUCCESS;
    }
}
