<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * <!-- Multi-Tier Pricing: Add annual pricing + price breakdown to t_transactions -->
 * <!-- annual_price: the per-year rate used for annual bookings -->
 * <!-- booking_years: number of years booked -->
 * <!-- price_breakdown: JSON array of per-date prices [{date, price, type}] for daily bookings -->
 * <!-- price_breakdown locks in the exact per-date prices at booking time, so later rule changes don't affect it -->
 */
return new class extends Migration
{
    public function up(): void
    {
        /* Temporarily disable strict SQL mode to work around invalid default '0000-00-00 00:00:00' on updated_at */
        DB::statement("SET SESSION sql_mode = ''");
        DB::statement('ALTER TABLE t_transactions ADD COLUMN annual_price DECIMAL(18,4) NULL AFTER monthly_price');
        DB::statement('ALTER TABLE t_transactions ADD COLUMN booking_years INT NULL AFTER booking_months');
        DB::statement('ALTER TABLE t_transactions ADD COLUMN price_breakdown JSON NULL AFTER subtotal_before_discount');
        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function down(): void
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            $table->dropColumn(['annual_price', 'booking_years', 'price_breakdown']);
        });
    }
};
