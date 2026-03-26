<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket broadcasts table.
 * Stores one-way announcements sent by Front Desk or HQ
 * to targeted audiences of users.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_broadcasts', function (Blueprint $table) {
            $table->id();
            /** Unique broadcast reference number (e.g., KOS1-BCT-0001 or HQ-BCT-0001) */
            $table->string('broadcast_number', 20)->unique();
            /** Who sent: front_desk (property) or hq */
            $table->enum('sender_type', ['front_desk', 'hq']);
            /** Property ID — NULL for HQ broadcasts */
            $table->unsignedInteger('property_id')->nullable();
            /** Admin user who created and sent the broadcast */
            $table->unsignedBigInteger('sender_id');
            /** Broadcast title */
            $table->string('title', 255);
            /** Broadcast message body */
            $table->text('message_text');
            /** Target audience filter */
            $table->enum('audience', ['all_users', 'active_bookings', 'active_and_future_bookings']);
            /** Number of users who received this broadcast */
            $table->unsignedInteger('recipient_count')->default(0);
            /** When the broadcast was sent */
            $table->timestamp('sent_at');
            $table->string('created_by', 50)->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamps();

            $table->index('property_id', 'idx_broadcasts_property');
            $table->index('sender_type', 'idx_broadcasts_sender');

            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_broadcasts');
    }
};
