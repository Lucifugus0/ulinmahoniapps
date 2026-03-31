<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_refund', function (Blueprint $table) {
            // User-initiated refund tracking
            $table->unsignedBigInteger('requested_by')->nullable()->after('refund_date')
                ->comment('user_id who requested the refund (NULL = admin-initiated)');
            $table->string('refund_type', 10)->default('admin')->after('requested_by')
                ->comment('admin or user');

            // Bank account details for QRIS/VA refunds
            $table->string('refund_bank_name', 100)->nullable()->after('refund_type')
                ->comment('Bank name for QRIS/VA refunds');
            $table->string('refund_account_no', 50)->nullable()->after('refund_bank_name')
                ->comment('Bank account number for QRIS/VA refunds');
            $table->string('refund_account_holder', 100)->nullable()->after('refund_account_no')
                ->comment('Bank account holder name for QRIS/VA refunds');

            // Refund amount breakdown
            $table->decimal('room_refund', 18, 4)->nullable()->after('refund_account_holder')
                ->comment('Room price refund amount');
            $table->decimal('deposit_refund', 18, 4)->nullable()->after('room_refund')
                ->comment('Deposit refund amount (always 100%)');
            $table->decimal('other_refund', 18, 4)->nullable()->after('deposit_refund')
                ->comment('Parking and other fees refund amount');

            // Admin processing fields
            $table->text('admin_notes')->nullable()->after('other_refund')
                ->comment('Admin notes when processing refund');
            $table->unsignedBigInteger('processed_by')->nullable()->after('admin_notes')
                ->comment('Admin user_id who processed the refund');
            $table->dateTime('processed_at')->nullable()->after('processed_by')
                ->comment('When the refund was processed by admin');
        });
    }

    public function down(): void
    {
        Schema::table('t_refund', function (Blueprint $table) {
            $table->dropColumn([
                'requested_by',
                'refund_type',
                'refund_bank_name',
                'refund_account_no',
                'refund_account_holder',
                'room_refund',
                'deposit_refund',
                'other_refund',
                'admin_notes',
                'processed_by',
                'processed_at',
            ]);
        });
    }
};
