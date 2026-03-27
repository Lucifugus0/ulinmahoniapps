<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketSequence model — manages auto-increment counters for ticket/broadcast numbers.
 * Each scope (property initial or 'HQ') + type ('ticket' or 'broadcast') has its own counter.
 * Use lockForUpdate() when incrementing to prevent race conditions.
 */
class TicketSequence extends Model
{
    protected $table = 't_ticket_sequences';

    public $timestamps = false;

    protected $fillable = ['scope', 'sequence_type', 'last_number'];
}
