<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tandai record t_parking yang dibuat via Parking Management (tanpa invoice).
     * Dipakai untuk tracking apakah quota sudah dikonsumsi oleh PM atau PP.
     *
     * management_only = 1 → quota dikonsumsi oleh Parking Management (tidak ada invoice)
     * management_only = 0 → quota dikonsumsi oleh Parking Payments (ada invoice di t_parking_fee_transaction)
     */
    public function up(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->tinyInteger('management_only')->default(0)->after('status')
                ->comment('1=quota by Parking Management, 0=quota by Parking Payments');
        });
    }

    public function down(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->dropColumn('management_only');
        });
    }
};
