<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_parking', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('property_id', 11);
            $table->enum('parking_type', ['car', 'motorcycle']);
            $table->string('vehicle_plate', 20);
            $table->string('owner_name', 150)->nullable();
            $table->string('owner_phone', 50)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('parking_duration')->nullable();
            $table->decimal('fee_amount', 15, 2)->nullable();
            $table->string('notes', 500)->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('property_id');
            $table->index('parking_type');
            $table->index('vehicle_plate');
            $table->unique(['property_id', 'vehicle_plate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_parking');
    }
};
