<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketRead model — tracks the last-read timestamp per user per ticket.
 * Used to calculate unread message counts for each user.
 */
class TicketRead extends Model
{
    protected $table = 't_ticket_reads';

    protected $fillable = ['ticket_id', 'user_id', 'last_read_at'];

    protected $casts = [
        'last_read_at' => 'datetime',
    ];

    /** The ticket this read record belongs to */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /** The user this read record belongs to */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
