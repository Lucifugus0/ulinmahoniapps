<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix collation mismatch across all tables.
 * Some tables used utf8mb4_general_ci while others used utf8mb4_unicode_ci,
 * causing "Illegal mix of collations" errors on JOINs (e.g. Deposit Entry page).
 * Standardize all to utf8mb4_unicode_ci (Laravel default).
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'm_deposit_fee',
            't_transactions',
            't_booking',
            'm_properties',
            'm_rooms',
            'm_customers',
            't_payment',
            'm_room_type',
            'sidebar_items',
            't_transactions_archive',
            'm_calendar_dates',
            'dashboard_widgets',
            'm_property_images',
            'permissions',
            't_parking_fee_transaction',
            'm_rooms_door_lock',
            't_parking_archive',
            't_parking_fee_transaction_image',
            'm_room_pricing_rules',
            't_payment_archive',
            'm_room_name_types',
            't_parking',
            'm_cities',
            'm_parking_fee',
            't_deposit_fee_transaction_image',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
    }

    public function down(): void
    {
        // No rollback — standardizing collation is a one-way improvement
    }
};
