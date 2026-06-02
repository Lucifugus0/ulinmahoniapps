<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add explicit `start_rent` / `end_rent` to t_parking_fee_transaction so each
     * payment row carries its own rental period as dates. Preserves history when
     * extensions/renewals overwrite the period stored on t_parking.
     *
     * Backfill is handled separately by `php artisan parking:backfill-rent-periods`.
     */
    public function up(): void
    {
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->date('start_rent')->nullable()->after('parking_duration');
            $table->date('end_rent')->nullable()->after('start_rent');
            $table->index('end_rent');
        });
    }

    public function down(): void
    {
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->dropIndex(['end_rent']);
            $table->dropColumn(['start_rent', 'end_rent']);
        });
    }
};
