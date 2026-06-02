<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-invoice-code per-year sequence counter.
 * One row per (invoice_code, year). Updated atomically inside a DB
 * transaction with lockForUpdate(), so two concurrent paid transactions
 * for the same property cannot collide on the same sequence number.
 *
 * Booking transactions (t_transactions) and parking transactions
 * (t_parking_fee_transaction) for properties sharing the same
 * invoice_code all draw from this single counter — so KOST 1's #5
 * booking and KOST 2's #6 parking both increment the KGA row.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_invoice_sequences', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('invoice_code', 10);
            $table->smallInteger('year');
            $table->unsignedInteger('last_seq')->default(0);
            $table->timestamps();

            $table->unique(['invoice_code', 'year'], 'uniq_invoice_code_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_invoice_sequences');
    }
};
