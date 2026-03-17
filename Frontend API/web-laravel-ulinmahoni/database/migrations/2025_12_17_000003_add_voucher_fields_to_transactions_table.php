<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Temporarily disable strict mode to avoid timestamp issues
        DB::statement('SET SESSION sql_mode = ""');

        Schema::table('t_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('voucher_id')->nullable()->after('grandtotal_price');
            $table->string('voucher_code', 20)->nullable()->after('voucher_id');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('voucher_code');
            $table->decimal('subtotal_before_discount', 12, 2)->nullable()->after('discount_amount');

            // Foreign key
            $table->foreign('voucher_id')->references('idrec')->on('m_vouchers')->onDelete('set null');

            // Index
            $table->index('voucher_code');
        });

        // Re-enable strict mode
        DB::statement("SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    }

    public function down()
    {
        Schema::table('t_transactions', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropColumn(['voucher_id', 'voucher_code', 'discount_amount', 'subtotal_before_discount']);
        });
    }
};
