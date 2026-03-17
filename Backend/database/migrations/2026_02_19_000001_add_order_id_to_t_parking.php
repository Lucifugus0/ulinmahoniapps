<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom order_id ke t_parking untuk melacak dari booking mana
     * kendaraan ini pertama kali didaftarkan.
     */
    public function up(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->string('order_id')->nullable()->after('user_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('t_parking', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
