<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_transaction_logging', function (Blueprint $table) {
            $table->id();
            $table->string('payment_method', 50); // 'virtual_account', 'qris', 'credit_card'
            $table->string('invoice_number')->index();
            $table->string('transaction_id')->nullable()->index();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 10)->default('IDR');
            $table->string('status', 50); // 'SUCCESS', 'PENDING', 'FAILED', 'CANCELLED'
            $table->string('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('approval_code')->nullable();
            $table->string('authorize_id')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_data')->nullable();
            $table->json('payment_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('transaction_date')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['payment_method', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_transaction_logging');
    }
};
