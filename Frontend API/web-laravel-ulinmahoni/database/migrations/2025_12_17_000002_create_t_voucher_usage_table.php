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
            $table->integer('property_id')->nullable();
            $table->integer('room_id')->nullable();

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
            // Note: transaction_id foreign key removed to avoid circular dependency
            // The relationship is maintained at application level via Eloquent
            $table->foreign('property_id')->references('idrec')->on('m_properties')->onDelete('set null');
            $table->foreign('room_id')->references('idrec')->on('m_rooms')->onDelete('set null');

            // Indexes
            $table->index(['voucher_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index('used_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_voucher_logging');
    }
};
