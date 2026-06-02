<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketBroadcastRead model — tracks which users have read each broadcast.
 */
class TicketBroadcastRead extends Model
{
    protected $table = 't_ticket_broadcast_reads';

    public $timestamps = false;

    protected $fillable = ['broadcast_id', 'user_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /** The broadcast this read record belongs to */
    public function broadcast()
    {
        return $this->belongsTo(TicketBroadcast::class, 'broadcast_id');
    }

    /** The user who read the broadcast */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
