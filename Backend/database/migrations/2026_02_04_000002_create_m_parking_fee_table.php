<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_parking_fee', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('property_id', 11);
            $table->enum('parking_type', ['car', 'motorcycle']);
            $table->decimal('fee', 18, 4)->default(0);
            $table->integer('capacity')->default(0);
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'parking_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_parking_fee');
    }
};
