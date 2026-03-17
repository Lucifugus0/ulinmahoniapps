<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add checked_in_by and checked_out_by columns to track which admin
     * performed check-in and check-out actions on a booking.
     */
    public function up(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            $table->unsignedBigInteger('checked_in_by')->nullable()->after('check_in_at');
            $table->unsignedBigInteger('checked_out_by')->nullable()->after('check_out_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_booking', function (Blueprint $table) {
            $table->dropColumn(['checked_in_by', 'checked_out_by']);
        });
    }
};
