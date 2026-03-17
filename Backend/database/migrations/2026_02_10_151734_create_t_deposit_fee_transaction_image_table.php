<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_deposit_fee_transaction_image', function (Blueprint $table) {
            $table->id('idrec');
            $table->unsignedBigInteger('deposit_transaction_id');
            $table->text('image')->nullable();
            $table->string('image_type', 10)->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('deposit_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_deposit_fee_transaction_image');
    }
};
