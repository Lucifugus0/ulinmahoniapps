<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket reads table.
 * Tracks the last-read timestamp per user per ticket
 * for calculating unread message counts.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_reads', function (Blueprint $table) {
            $table->id();
            /** FK to the ticket */
            $table->unsignedBigInteger('ticket_id');
            /** FK to the user */
            $table->unsignedBigInteger('user_id');
            /** Timestamp of when the user last read this ticket */
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            /** Each user has one read record per ticket */
            $table->unique(['ticket_id', 'user_id']);

            $table->foreign('ticket_id')->references('id')->on('t_tickets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_reads');
    }
};
