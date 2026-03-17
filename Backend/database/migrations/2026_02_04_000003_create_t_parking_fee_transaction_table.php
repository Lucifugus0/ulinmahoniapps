<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_parking_fee_transaction', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('property_id', 11);
            $table->unsignedBigInteger('parking_fee_id');
            $table->string('invoice_id', 100)->nullable()->unique();
            $table->string('order_id', 100);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name', 150)->nullable();
            $table->string('user_phone', 50)->nullable();
            $table->enum('parking_type', ['car', 'motorcycle']);
            $table->string('vehicle_plate', 20)->nullable();
            $table->decimal('fee_amount', 18, 4)->default(0);
            $table->dateTime('transaction_date');
            $table->string('transaction_status', 50)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->longText('notes')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index('property_id');
            $table->index('order_id', 'idx_parking_order_id');
            $table->index('transaction_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_parking_fee_transaction');
    }
};
