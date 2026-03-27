<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix collation mismatch on booking-related tables.
 * t_booking used latin1_swedish_ci while t_transactions used utf8mb4_general_ci,
 * causing "Illegal mix of collations" on LEFT JOIN queries (All Bookings filter,
 * Today's Check-Out filter).
 */
return new class extends Migration
{
    public function up(): void
    {
        /* Convert t_booking from latin1_swedish_ci to utf8mb4_general_ci */
        DB::statement('ALTER TABLE `t_booking` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
    }

    public function down(): void
    {
        /* Not reverting — latin1 was incorrect */
    }
};
