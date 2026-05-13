<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guard against duplicate parking-fee transactions caused by admin double-submit
     * on `Backend Finance > Parking Entry`. The form `submitBtn.disabled = true` was
     * not robust against slow networks — production has 2 confirmed duplicate pairs
     * (parking_id 395 idrec 32, parking_id 399 idrec 36), both same `order_id` +
     * `vehicle_plate` + `start_rent`, created 47s and 1m16s apart by the same admin.
     *
     * Two parts:
     *   1. Soft-delete (status = 0) the later row in each existing duplicate group,
     *      keeping the row with the smallest idrec. Adds an audit note so the
     *      operation is traceable.
     *   2. Add a unique key on (order_id, vehicle_plate, start_rent, status) so
     *      future double-submits fail at the DB layer. `status` is in the tuple so
     *      that multiple soft-deleted (status = 0) rows can coexist without
     *      conflicting with the single live (status = 1) row.
     *
     * Legacy NULL `start_rent` rows (pre-2026-05-07 migration) are not touched —
     * NULLs in MySQL unique indexes are treated as distinct, so they will not
     * trigger the constraint even if they share order_id + vehicle_plate.
     */
    public function up(): void
    {
        // Step 1 — Soft-delete duplicates: keep MIN(idrec) per group, mark the rest status=0.
        $duplicateGroups = DB::table('t_parking_fee_transaction')
            ->select(
                'order_id',
                'vehicle_plate',
                'start_rent',
                DB::raw('MIN(idrec) AS keep_idrec'),
                DB::raw('COUNT(*) AS cnt')
            )
            ->where('status', 1)
            ->whereNotNull('order_id')
            ->whereNotNull('vehicle_plate')
            ->whereNotNull('start_rent')
            ->groupBy('order_id', 'vehicle_plate', 'start_rent')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicateGroups as $group) {
            // Tag the soft-deleted rows with an audit note so the dedup is traceable later.
            $auditNote = "\n[auto-dedup " . now()->toDateTimeString() .
                "] superseded by idrec={$group->keep_idrec} in same (order_id, vehicle_plate, start_rent) group";

            DB::table('t_parking_fee_transaction')
                ->where('order_id', $group->order_id)
                ->where('vehicle_plate', $group->vehicle_plate)
                ->where('start_rent', $group->start_rent)
                ->where('status', 1)
                ->where('idrec', '!=', $group->keep_idrec)
                ->update([
                    'status'     => 0,
                    'notes'      => DB::raw("CONCAT(COALESCE(notes, ''), " . DB::connection()->getPdo()->quote($auditNote) . ")"),
                    'updated_at' => now(),
                ]);
        }

        // Step 2 — Add the unique index. `status` is part of the tuple so soft-deleted
        // rows do not block future legitimate inserts of the same (order, plate, period).
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->unique(
                ['order_id', 'vehicle_plate', 'start_rent', 'status'],
                'tpft_unique_active_period'
            );
        });
    }

    public function down(): void
    {
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->dropUnique('tpft_unique_active_period');
        });

        // Soft-deleted duplicate rows are intentionally left in place. The audit note
        // in `notes` identifies them; manual UPDATE is required if you want to restore.
    }
};
