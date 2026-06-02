<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the persisted `invoice_number` column to `t_transactions`.
 * Replaces the previous on-the-fly generation in InvoiceNumberService:
 * once a transaction is marked paid (on/after 2026-03-06), the assigned
 * invoice number is written here once and never recomputed — refunds
 * preserve the value.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            $table->string('invoice_number', 100)->nullable()->unique()->after('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            $table->dropUnique(['invoice_number']);
            $table->dropColumn('invoice_number');
        });
    }
};
