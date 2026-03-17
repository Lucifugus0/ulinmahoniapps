<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('t_voucher_logging', function (Blueprint $table) {
            $table->id('idrec');

            // Voucher reference
            $table->unsignedBigInteger('voucher_id');
            $table->string('voucher_code', 20);

            // User and transaction reference
            $table->unsignedBigInteger('user_id');
            $table->string('order_id', 255)->nullable()->index();
            $table->unsignedBigInteger('transaction_id')->nullable()->index();

            // Booking details
            $table->unsignedInteger('property_id')->nullable();
            $table->unsignedInteger('room_id')->nullable();

            // Financial details
            $table->decimal('original_amount', 12, 2); // Before discount
            $table->decimal('discount_amount', 12, 2); // Actual discount applied
            $table->decimal('final_amount', 12, 2); // After discount

            // Usage details
            $table->dateTime('used_at');
            $table->enum('status', ['applied', 'cancelled', 'refunded'])->default('applied');

            // Additional metadata
            $table->json('metadata')->nullable(); // Store additional context

            $table->timestamps();

            // Foreign keys
            $table->foreign('voucher_id')->references('idrec')->on('m_vouchers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Note: transaction_id, property_id, room_id foreign keys removed to avoid type mismatch
            // The relationships are maintained at application level via Eloquent

            // Indexes
            $table->index(['voucher_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index('used_at');
            $table->index('property_id');
            $table->index('room_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_voucher_logging');
    }
};
