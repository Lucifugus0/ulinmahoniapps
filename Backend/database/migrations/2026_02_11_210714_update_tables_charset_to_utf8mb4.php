<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // List of tables to update
        $tables = [
            'users',
            't_transactions',
            't_booking',
            'm_rooms',
            't_parking_fee_transaction',
            'm_properties',
            't_refund',
            't_voucher_usage',
            'm_vouchers',
        ];

        foreach ($tables as $table) {
            // Check if table exists before converting
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // List of tables to revert
        $tables = [
            'users',
            't_transactions',
            't_booking',
            'm_rooms',
            't_parking_fee_transaction',
            'm_properties',
            't_refund',
            't_voucher_usage',
            'm_vouchers',
        ];

        foreach ($tables as $table) {
            // Check if table exists before reverting
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci");
            }
        }
    }
};
