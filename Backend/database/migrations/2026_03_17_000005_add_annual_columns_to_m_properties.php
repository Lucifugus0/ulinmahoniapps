<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * <!-- Multi-Tier Pricing: Add annual pricing columns to m_properties -->
 * <!-- Properties aggregate min/max prices from their rooms for display purposes -->
 * <!-- These additive columns support annual pricing at the property level -->
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            /* m_properties doesn't have periode_monthly column, so add after price_discounted_monthly instead */
            $table->tinyInteger('periode_annual')->nullable()->default(0)->after('price_discounted_monthly');
            $table->decimal('price_original_annual', 18, 4)->nullable()->after('periode_annual');
            $table->decimal('price_discounted_annual', 18, 4)->nullable()->after('price_original_annual');
        });
    }

    public function down(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->dropColumn([
                'periode_annual',
                'price_original_annual',
                'price_discounted_annual',
            ]);
        });
    }
};
