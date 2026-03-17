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
        Schema::create('m_router', function (Blueprint $table) {
            $table->string('idrouter')->primary();
            $table->string('serialno')->nullable();
            $table->integer('room_id')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('ip')->nullable();
            $table->string('api_port')->nullable();
            $table->string('web_post')->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_router');
    }
};
