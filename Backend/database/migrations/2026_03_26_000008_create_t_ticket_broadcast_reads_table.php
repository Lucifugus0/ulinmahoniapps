<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the broadcast reads table.
 * Tracks which users have read each broadcast announcement.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_broadcast_reads', function (Blueprint $table) {
            $table->id();
            /** FK to the broadcast */
            $table->unsignedBigInteger('broadcast_id');
            /** FK to the user who read it */
            $table->unsignedBigInteger('user_id');
            /** When the user read this broadcast */
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            /** Each user has one read record per broadcast */
            $table->unique(['broadcast_id', 'user_id']);

            $table->foreign('broadcast_id')->references('id')->on('t_ticket_broadcasts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_broadcast_reads');
    }
};
