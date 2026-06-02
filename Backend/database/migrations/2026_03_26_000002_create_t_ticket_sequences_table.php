<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates the ticket/broadcast number sequences table.
 * Each property + HQ gets a separate auto-increment counter
 * for generating ticket numbers like KOS1-TKT-0001.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ticket_sequences', function (Blueprint $table) {
            $table->id();
            /** Scope identifier: property initial (e.g., 'KOS1') or 'HQ' */
            $table->string('scope', 20);
            /** Sequence type: ticket or broadcast */
            $table->enum('sequence_type', ['ticket', 'broadcast']);
            /** Last assigned number — atomically incremented on ticket creation */
            $table->unsignedInteger('last_number')->default(0);

            $table->unique(['scope', 'sequence_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ticket_sequences');
    }
};
