<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_parking_fee_transaction_image', function (Blueprint $table) {
            $table->id('idrec');
            $table->unsignedBigInteger('parking_transaction_id');
            $table->longText('image');
            $table->string('image_type', 20)->nullable();
            $table->string('description', 255)->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('parking_transaction_id')
                ->references('idrec')
                ->on('t_parking_fee_transaction')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_parking_fee_transaction_image');
    }
};
