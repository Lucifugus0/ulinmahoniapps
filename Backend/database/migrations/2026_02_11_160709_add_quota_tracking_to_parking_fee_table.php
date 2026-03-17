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
        Schema::table('m_parking_fee', function (Blueprint $table) {
            // Kolom untuk tracking berapa quota yang sedang digunakan
            // capacity = kwota maksimal
            // quota_used = kwota yang sedang terpakai
            // available = capacity - quota_used
            $table->integer('quota_used')->default(0)->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_parking_fee', function (Blueprint $table) {
            $table->dropColumn('quota_used');
        });
    }
};
