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
        Schema::table('m_vouchers', function (Blueprint $table) {
            // Add property_id column - nullable means voucher can be global (all properties)
            // If property_id is set, voucher is bound to that specific property
            $table->unsignedBigInteger('property_id')->nullable()->after('scope_ids');
            $table->foreign('property_id')->references('idrec')->on('m_properties')->onDelete('set null');
            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_vouchers', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropIndex(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};
