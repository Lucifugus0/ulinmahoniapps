<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * <!-- Multi-Tier Pricing: Add annual pricing + weekday/weekend columns to m_rooms -->
 * <!-- periode_annual: flag to enable annual booking type for a room -->
 * <!-- price_original_annual / price_discounted_annual: annual flat rate -->
 * <!-- price_weekday / price_weekend: base daily prices for Mon-Fri and Sat-Sun -->
 * <!-- These are additive columns — no existing columns changed or renamed -->
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_rooms', function (Blueprint $table) {
            $table->tinyInteger('periode_annual')->nullable()->default(0)->after('periode_monthly');
            $table->decimal('price_original_annual', 18, 4)->nullable()->after('price_discounted_monthly');
            $table->decimal('price_discounted_annual', 18, 4)->nullable()->after('price_original_annual');
            $table->decimal('price_weekday', 18, 4)->nullable()->after('price_discounted_annual');
            $table->decimal('price_weekend', 18, 4)->nullable()->after('price_weekday');
        });
    }

    public function down(): void
    {
        Schema::table('m_rooms', function (Blueprint $table) {
            $table->dropColumn([
                'periode_annual',
                'price_original_annual',
                'price_discounted_annual',
                'price_weekday',
                'price_weekend',
            ]);
        });
    }
};
