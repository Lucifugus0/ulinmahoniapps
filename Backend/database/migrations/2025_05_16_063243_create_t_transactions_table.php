<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_transactions', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('property_id', 11)->nullable();
            $table->string('room_id', 11)->nullable();
            $table->string('order_id', 100);
            $table->integer('user_id');
            $table->string('user_name', 150)->nullable();
            $table->string('user_email', 100)->nullable();
            $table->string('user_phone_number', 150)->nullable();
            $table->string('property_name', 150)->nullable();
            $table->string('property_type', 100)->nullable();
            $table->string('room_name', 100)->nullable();
            $table->string('booking_type', 100)->nullable(); 
            $table->integer('booking_days')->nullable(); 
            $table->integer('booking_months')->nullable();
            $table->decimal('daily_price', 18, 4)->nullable(); 
            $table->decimal('monthly_price', 18, 4)->nullable(); 
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->decimal('room_price', 18, 4)->nullable(); 
            $table->decimal('admin_fees', 18, 4)->nullable();
            $table->decimal('service_fees', 18, 4)->nullable();
            $table->integer('grandtotal_price')->nullable();
            $table->integer('parking_duration')->nullable();
            $table->string('parking_type', 255)->nullable();
            $table->dateTime('transaction_date');
            $table->string('transaction_type', 100)->nullable();
            $table->string('transaction_code', 100)->nullable();
            $table->string('transaction_status', 100)->nullable();
            $table->integer('is_renewal')->default(0);
            $table->integer('renewal_status')->default(0);
            $table->string('virtual_account_no', 255)->nullable();
            $table->string('payment_bank', 255)->nullable();
            $table->longText('notes')->nullable();
            $table->integer('status')->default(1);
            $table->timestamp('paid_at')->nullable();
            $table->binary('attachment')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_transactions');
    }
};
