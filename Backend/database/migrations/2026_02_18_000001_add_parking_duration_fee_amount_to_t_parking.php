<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            if (!Schema::hasColumn('t_parking', 'parking_duration')) {
                $table->integer('parking_duration')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('t_parking', 'fee_amount')) {
                $table->decimal('fee_amount', 15, 2)->nullable()->after('parking_duration');
            }
        });
    }

    public function down(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->dropColumn(['parking_duration', 'fee_amount']);
        });
    }
};
