<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            // Stores the day-of-month from the original check-in date.
            // Used for renewal checkout calculation to avoid losing days
            // when months have fewer days (e.g., Jan 31 → Feb 28 → Mar 31).
            // For room changes, this resets to the new check-in day.
            $table->unsignedTinyInteger('original_checkin_day')->nullable()->after('check_out')
                ->comment('Day of month from original check-in (1-31). Preserved across renewals.');
        });

        // Backfill existing data: set original_checkin_day = DAY(check_in)
        DB::statement('UPDATE t_transactions SET original_checkin_day = DAY(check_in) WHERE check_in IS NOT NULL');
    }

    public function down(): void
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            $table->dropColumn('original_checkin_day');
        });
    }
};
