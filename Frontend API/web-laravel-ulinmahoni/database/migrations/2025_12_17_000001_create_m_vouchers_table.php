<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('m_vouchers', function (Blueprint $table) {
            $table->id('idrec');

            // Voucher identification
            $table->string('code', 20)->unique()->index();
            $table->string('name', 255);
            $table->text('description')->nullable();

            // Discount configuration
            $table->decimal('discount_percentage', 5, 2); // e.g., 10.00 for 10%
            $table->decimal('max_discount_amount', 12, 2); // Maximum IDR discount cap

            // Usage limits
            $table->integer('max_total_usage')->default(0); // 0 = unlimited
            $table->integer('current_usage_count')->default(0);
            $table->integer('max_usage_per_user')->default(1); // Default: 1 time per user

            // Validity period
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');

            // Minimum transaction requirement
            $table->decimal('min_transaction_amount', 12, 2)->default(0);

            // Scope configuration
            $table->enum('scope_type', ['global', 'property', 'room'])->default('global');
            // JSON array of property/room IDs if scope is not global
            $table->json('scope_ids')->nullable();

            // Status
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active')->index();

            // Audit fields
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');

            // Indexes for performance
            $table->index(['status', 'valid_from', 'valid_to']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('m_vouchers');
    }
};
