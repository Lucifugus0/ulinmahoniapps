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
        Schema::table('t_payment', function (Blueprint $table) {
            // Change verified_by from integer to string
            $table->string('verified_by', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_payment', function (Blueprint $table) {
            // Revert verified_by back to integer
            $table->integer('verified_by')->nullable()->change();
        });
    }
};
