<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('parking_id')->nullable()->after('parking_fee_id');
            $table->integer('parking_duration')->default(1)->after('vehicle_plate');
            $table->index('parking_id');
        });

        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->dropColumn('parking_fee_id');
        });
    }

    public function down(): void
    {
        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->unsignedBigInteger('parking_fee_id')->nullable()->after('property_id');
        });

        Schema::table('t_parking_fee_transaction', function (Blueprint $table) {
            $table->dropIndex(['parking_id']);
            $table->dropColumn('parking_id');
            $table->dropColumn('parking_duration');
        });
    }
};
