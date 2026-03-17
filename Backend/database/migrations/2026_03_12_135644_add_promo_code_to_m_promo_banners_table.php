<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->string('promo_code', 50)->nullable()->after('descriptions');
        });
    }

    public function down(): void
    {
        Schema::table('m_promo_banners', function (Blueprint $table) {
            $table->dropColumn('promo_code');
        });
    }
};
