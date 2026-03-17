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
        Schema::table('m_vouchers', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('scope_ids');
            $table->index('property_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_vouchers', function (Blueprint $table) {
            $table->dropIndex(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};
