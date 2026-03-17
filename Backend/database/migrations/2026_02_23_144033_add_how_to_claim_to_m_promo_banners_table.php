<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->json('how_to_claim')->nullable()->after('descriptions');
        });
    }

    public function down(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->dropColumn('how_to_claim');
        });
    }
};
