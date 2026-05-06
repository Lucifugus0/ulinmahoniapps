<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds a per-banner Syarat & Ketentuan (terms & conditions) field.
     * Stored as longtext (JSON-cast on the model, mirroring how_to_claim).
     * NULL means "fall back to the hardcoded i18n defaults in the modal".
     */
    public function up(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->longText('terms_conditions')->nullable()->after('how_to_claim');
        });
    }

    public function down(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->dropColumn('terms_conditions');
        });
    }
};
