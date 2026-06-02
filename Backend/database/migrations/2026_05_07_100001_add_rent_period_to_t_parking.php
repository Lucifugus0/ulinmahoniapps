<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add explicit `start_rent` / `end_rent` to t_parking so the current active rental
     * period is queryable as dates instead of being computed on the fly from
     * t_parking_fee_transaction.transaction_date + parking_duration months.
     *
     * Backfill is handled separately by `php artisan parking:backfill-rent-periods`.
     */
    public function up(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->date('start_rent')->nullable()->after('parking_duration');
            $table->date('end_rent')->nullable()->after('start_rent');
            // Index end_rent so listings can quickly bucket "active" vs "expired"
            $table->index('end_rent');
        });
    }

    public function down(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->dropIndex(['end_rent']);
            $table->dropColumn(['start_rent', 'end_rent']);
        });
    }
};
