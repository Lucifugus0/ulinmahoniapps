<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->string('gender', 10)->nullable()->after('tags');
        });
    }

    public function down(): void
    {
        Schema::table('m_properties', function (Blueprint $table) {
            $table->dropColumn('gender');
        });
    }
};
