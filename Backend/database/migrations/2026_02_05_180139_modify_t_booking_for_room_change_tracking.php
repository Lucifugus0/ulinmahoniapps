<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration modifies t_booking table for improved room change tracking:
     * - Removes 'status' column (was: 1=active, 2=transferred, 3=old)
     * - Adds 'is_active' column (1=active booking, 0=inactive/old)
     * - Adds 'previous_booking_id' for chain tracking
     * - Adds 'room_changed_at' and 'room_changed_by' for audit
     */
    public function up(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            // Add new columns first
            $table->tinyInteger('is_active')->default(1)->after('status');
            $table->unsignedBigInteger('previous_booking_id')->nullable()->after('is_active');
            $table->dateTime('room_changed_at')->nullable()->after('description');
            $table->integer('room_changed_by')->nullable()->after('room_changed_at');

            // Add foreign key for previous_booking_id (self-referencing)
            $table->foreign('previous_booking_id')
                  ->references('idrec')
                  ->on('t_booking')
                  ->onDelete('set null');
        });

        // Migrate existing data: convert status to is_active
        // status 1 (active) or 2 (transferred but still active) → is_active = 1
        // status 3 (old/cancelled) → is_active = 0
        DB::statement("UPDATE t_booking SET is_active = CASE WHEN status IN (1, 2) THEN 1 ELSE 0 END");

        // Try to establish previous_booking_id links for existing transferred bookings
        // Find bookings with status 3 (old) and link them to status 2 (new) bookings with same order_id
        $oldBookings = DB::table('t_booking')
            ->where('status', 3)
            ->whereNotNull('reason')
            ->get();

        foreach ($oldBookings as $oldBooking) {
            // Find the corresponding new booking (status 2) with same order_id
            $newBooking = DB::table('t_booking')
                ->where('order_id', $oldBooking->order_id)
                ->where('status', 2)
                ->where('idrec', '!=', $oldBooking->idrec)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($newBooking) {
                // Set previous_booking_id on the new booking pointing to old booking
                DB::table('t_booking')
                    ->where('idrec', $newBooking->idrec)
                    ->update([
                        'previous_booking_id' => $oldBooking->idrec,
                        'room_changed_at' => $newBooking->updated_at,
                    ]);
            }
        }

        // Now drop the status column
        Schema::table('t_booking', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            // Add back status column
            $table->tinyInteger('status')->nullable()->after('updated_by');
        });

        // Restore status from is_active
        // is_active = 1 → status = 1 (we can't distinguish 1 vs 2 anymore)
        // is_active = 0 → status = 3
        DB::statement("UPDATE t_booking SET status = CASE WHEN is_active = 1 THEN 1 ELSE 3 END");

        // For bookings that have previous_booking_id, set status to 2 (transferred)
        DB::statement("UPDATE t_booking SET status = 2 WHERE previous_booking_id IS NOT NULL AND is_active = 1");

        Schema::table('t_booking', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['previous_booking_id']);

            // Drop new columns
            $table->dropColumn([
                'is_active',
                'previous_booking_id',
                'room_changed_at',
                'room_changed_by'
            ]);
        });
    }
};
