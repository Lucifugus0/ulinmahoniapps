<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Daily expiry sweep — flips status=1 → 0 on every non-trashed t_parking row
 * whose end_rent is in the past. The "Show Expired" toggle on the Parking
 * Management page surfaces these rows; default view hides them.
 *
 * Scheduled daily at 00:00 (see Kernel.php). Also runnable manually.
 *
 *   php artisan parking:deactivate-expired
 *   php artisan parking:deactivate-expired --dry-run
 */
class DeactivateExpiredParking extends Command
{
    protected $signature = 'parking:deactivate-expired
                            {--dry-run : Print which rows would be deactivated without writing}';

    protected $description = 'Set t_parking.status = 0 for rows whose end_rent has passed';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        // Sweep targets: any status=1 (and not soft-deleted) row that is either
        //   (a) past its end_rent, OR
        //   (b) has NULL end_rent — these are stale legacy / orphaned rows that
        //       slipped past validation; with current code paths every new row
        //       gets a period, so a NULL means "broken, treat as inactive".
        $base = DB::table('t_parking')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->where(function ($q) {
                $q->whereNull('end_rent')
                  ->orWhere(function ($q2) {
                      $q2->whereNotNull('end_rent')
                         ->whereDate('end_rent', '<', DB::raw('CURDATE()'));
                  });
            });

        $count = (clone $base)->count();

        if ($count === 0) {
            $this->info('No expired or orphaned active parking rows found. Nothing to do.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $sample = (clone $base)
                ->select('idrec', 'property_id', 'parking_type', 'vehicle_plate', 'order_id', 'end_rent')
                ->orderByRaw('end_rent IS NULL DESC')  // surface NULL-end rows first
                ->orderBy('end_rent')
                ->limit(20)
                ->get();
            $this->warn("DRY RUN — would deactivate {$count} row(s). Showing up to 20:");
            foreach ($sample as $row) {
                $endLabel = $row->end_rent ?: '(no end_rent — orphaned)';
                $this->line("  #{$row->idrec}  {$row->parking_type}  {$row->vehicle_plate}  {$row->order_id}  ended {$endLabel}");
            }
            return self::SUCCESS;
        }

        $updated = $base->update([
            'status' => 0,
            'updated_at' => now(),
        ]);

        $this->info("Deactivated {$updated} expired or orphaned parking row(s).");
        return self::SUCCESS;
    }
}
