<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the optional `invoice_code` column to `m_properties`.
 * Separate from `initial` — `initial` is the short property code used
 * for room numbering, while `invoice_code` is the prefix used on
 * printed invoices / transaction IDs. Nullable so existing rows and
 * the Mobile App / Frontend API's JSON payloads remain untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->string('invoice_code', 10)->nullable()->after('initial');
        });
    }

    public function down(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->dropColumn('invoice_code');
        });
    }
};
