<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert all remaining tables to utf8mb4 to fix collation mismatch errors.
     */
    public function up(): void
    {
        $tables = [
            // Tables that may have been missed or created after the first conversion
            'm_parking_fee',
            't_parking_fee_transaction_image',
            'm_deposit_fee',
            't_deposit_fee_transaction',
            't_deposit_fee_transaction_image',
            't_parking',
            // Re-convert previously converted tables to ensure consistency
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
        $tables = [
            'm_parking_fee',
            't_parking_fee_transaction_image',
            'm_deposit_fee',
            't_deposit_fee_transaction',
            't_deposit_fee_transaction_image',
            't_parking',
        ];

        foreach ($tables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci");
            }
        }
    }
};
