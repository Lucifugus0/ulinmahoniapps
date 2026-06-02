<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketMessage model — a single message within a ticket conversation.
 * Supports text, image, and system message types.
 * System messages are auto-generated for status changes (close, reopen, etc.).
 */
class TicketMessage extends Model
{
    protected $table = 't_ticket_messages';

    protected $fillable = [
        'ticket_id', 'sender_id', 'message_text', 'message_type',
        'is_edited', 'edited_at', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    /** The ticket this message belongs to */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    /** The user who sent this message */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** Image/file attachments on this message */
    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'message_id');
    }

    /** Check if this message has any attachments */
    public function hasAttachments(): bool
    {
        return $this->attachments()->exists();
    }

    /** Auto-update the parent ticket's last_message_at when a message is created */
    protected static function booted(): void
    {
        static::created(function (TicketMessage $message) {
            /* Update the ticket's last_message_at timestamp to keep sorting accurate */
            $message->ticket()->update(['last_message_at' => $message->created_at]);
        });
    }
}
