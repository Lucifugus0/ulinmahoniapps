<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * <!-- Multi-Tier Pricing: Add price_type column to m_room_prices -->
 * <!-- Tracks WHY each date has its price (weekday, weekend, holiday, high_season, low_season, manual) -->
 * <!-- Used by admin calendar UI for color coding and by the price generator to preserve manual overrides -->
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_room_prices', function (Blueprint $table) {
            $table->string('price_type', 20)->nullable()->default('weekday')->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('m_room_prices', function (Blueprint $table) {
            $table->dropColumn('price_type');
        });
    }
};
