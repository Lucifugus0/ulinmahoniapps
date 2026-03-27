<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fix collation mismatch between t_deposit_fee_transaction and t_transactions.
 * The deposit table uses utf8mb4_general_ci while transactions uses utf8mb4_unicode_ci,
 * causing "Illegal mix of collations" error on JOIN via order_id.
 * This migration converts the deposit table to utf8mb4_unicode_ci to match.
 */
return new class extends Migration
{
    public function up(): void
    {
        /* Convert the table's default collation */
        DB::statement('ALTER TABLE `t_deposit_fee_transaction` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        /* Also fix t_deposit_fee_transaction_images if it exists */
        if (DB::getSchemaBuilder()->hasTable('t_deposit_fee_transaction_images')) {
            DB::statement('ALTER TABLE `t_deposit_fee_transaction_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `t_deposit_fee_transaction` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');

        if (DB::getSchemaBuilder()->hasTable('t_deposit_fee_transaction_images')) {
            DB::statement('ALTER TABLE `t_deposit_fee_transaction_images` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci');
        }
    }
};
