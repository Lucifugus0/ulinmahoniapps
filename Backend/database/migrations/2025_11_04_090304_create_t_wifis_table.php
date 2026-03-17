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
        Schema::create('t_wifi', function (Blueprint $table) {
            $table->id('idrec');
            $table->string('idrouter');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->string('password', 8);
            $table->timestamps();

            $table->foreign('idrouter')->references('idrouter')->on('m_router')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_wifi');
    }
};
