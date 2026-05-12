<?php

namespace App\Console\Commands;

use App\Models\Parking;
use App\Models\ParkingFeeTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Backfills `start_rent` + `end_rent` on t_parking and t_parking_fee_transaction.
 *
 * Pass 1 — t_parking_fee_transaction:
 *   start_rent = DATE(transaction_date)
 *   end_rent   = MIN(start_rent + parking_duration months, linked_booking.check_out)
 *
 * Pass 2 — t_parking:
 *   If a paid txn exists for same property + plate -> copy its start/end.
 *   Else (Frontend booking-bundled or management_only with no txn) ->
 *     start_rent = booking.check_in
 *     end_rent   = MIN(check_in + parking_duration months, booking.check_out)
 *   If parking_duration is NULL on the t_parking row AND no txn -> leave NULL (malformed).
 *
 * Idempotent: only fills rows where start_rent IS NULL.
 *
 * Usage:
 *   php artisan parking:backfill-rent-periods
 *   php artisan parking:backfill-rent-periods --dry-run
 */
class BackfillParkingRentPeriods extends Command
{
    protected $signature = 'parking:backfill-rent-periods
                            {--dry-run : Print what would be written without persisting}';

    protected $description = 'Compute and persist start_rent + end_rent on existing t_parking and t_parking_fee_transaction rows';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $this->info($dryRun ? 'DRY RUN — no writes' : 'WRITING to DB');

        DB::beginTransaction();
        try {
            $txnUpdates = $this->backfillTransactions($dryRun);
            $parkingUpdates = $this->backfillParkings($dryRun);

            if ($dryRun) {
                DB::rollBack();
                $this->info("[dry-run] would update {$txnUpdates} txn rows + {$parkingUpdates} parking rows");
            } else {
                DB::commit();
                $this->info("Updated {$txnUpdates} txn rows + {$parkingUpdates} parking rows");
            }
            return 0;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Backfill failed: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Pass 1 — populate start_rent / end_rent on every txn that's missing them.
     * Loaded in chunks to avoid OOM on large tables.
     */
    private function backfillTransactions(bool $dryRun): int
    {
        $count = 0;

        ParkingFeeTransaction::whereNull('start_rent')
            ->whereNotNull('parking_duration')
            ->whereNotNull('transaction_date')
            ->orderBy('idrec')
            ->chunkById(200, function ($rows) use ($dryRun, &$count) {
                foreach ($rows as $txn) {
                    $start = Carbon::parse($txn->transaction_date)->startOfDay();
                    $end = $start->copy()->addMonths((int) $txn->parking_duration);

                    // Cap end at booking check_out when we can find the parent booking
                    $booking = Transaction::where('order_id', $txn->order_id)->first();
                    if ($booking && $booking->check_out) {
                        $checkOut = Carbon::parse($booking->check_out)->startOfDay();
                        if ($end->gt($checkOut)) {
                            $end = $checkOut;
                        }
                    }

                    if (!$dryRun) {
                        $txn->update([
                            'start_rent' => $start->toDateString(),
                            'end_rent'   => $end->toDateString(),
                        ]);
                    }
                    $count++;
                }
            }, 'idrec');

        return $count;
    }

    /**
     * Pass 2 — populate t_parking from latest paid txn (preferred) or from booking dates.
     */
    private function backfillParkings(bool $dryRun): int
    {
        $count = 0;

        Parking::withTrashed()
            ->whereNull('start_rent')
            ->orderBy('idrec')
            ->chunkById(200, function ($rows) use ($dryRun, &$count) {
                foreach ($rows as $parking) {
                    [$start, $end] = $this->computeParkingPeriod($parking);

                    if (!$start || !$end) {
                        // Malformed legacy row (e.g. NULL parking_duration + no txn) — skip.
                        continue;
                    }

                    if (!$dryRun) {
                        $parking->forceFill([
                            'start_rent' => $start->toDateString(),
                            'end_rent'   => $end->toDateString(),
                        ])->save();
                    }
                    $count++;
                }
            }, 'idrec');

        return $count;
    }

    /**
     * Resolve [start, end] for a parking row using the priority:
     *   1. Latest paid t_parking_fee_transaction matching same property + plate (already backfilled in pass 1)
     *   2. Linked booking via order_id -> use check_in + parking_duration months capped at check_out
     */
    private function computeParkingPeriod(Parking $parking): array
    {
        $latestTxn = ParkingFeeTransaction::where('property_id', $parking->property_id)
            ->where('vehicle_plate', $parking->vehicle_plate)
            ->where('transaction_status', 'paid')
            ->orderByDesc('transaction_date')
            ->first();

        if ($latestTxn && $latestTxn->start_rent && $latestTxn->end_rent) {
            return [
                Carbon::parse($latestTxn->start_rent),
                Carbon::parse($latestTxn->end_rent),
            ];
        }

        // No paid txn — fall back to booking check_in / check_out
        if ($parking->order_id && $parking->parking_duration) {
            $booking = Transaction::where('order_id', $parking->order_id)->first();
            if ($booking && $booking->check_in) {
                $start = Carbon::parse($booking->check_in)->startOfDay();
                $end = $start->copy()->addMonths((int) $parking->parking_duration);
                if ($booking->check_out) {
                    $checkOut = Carbon::parse($booking->check_out)->startOfDay();
                    if ($end->gt($checkOut)) {
                        $end = $checkOut;
                    }
                }
                return [$start, $end];
            }
        }

        return [null, null];
    }
}
