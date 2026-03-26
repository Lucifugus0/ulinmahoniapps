<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Ticket model — represents a customer service ticket.
 * A ticket is a conversation between a customer and staff/HQ CS,
 * categorized by type (booking/complaint/suggestion) with lifecycle states.
 */
class Ticket extends Model
{
    protected $table = 't_tickets';

    protected $fillable = [
        'ticket_number', 'order_id', 'property_id', 'user_id', 'category_id',
        'recipient_type', 'subject', 'ticket_status', 'priority',
        'closed_at', 'closed_by', 'reopened_at', 'reopen_deadline',
        'last_message_at', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'reopen_deadline' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    /** The customer who created this ticket */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** The ticket category (type + category combo) */
    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    /** The property this ticket is about (NULL for HQ tickets) */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    /** The booking transaction this ticket relates to (NULL for Suggestion Box) */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    /** All messages in this ticket */
    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    /** Read tracking records for this ticket */
    public function reads()
    {
        return $this->hasMany(TicketRead::class, 'ticket_id');
    }

    /** The user who closed this ticket */
    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /** Scope: filter by ticket status */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('ticket_status', $status);
    }

    /** Scope: filter open tickets (open or reopened) */
    public function scopeOpen($query)
    {
        return $query->whereIn('ticket_status', ['open', 'reopened', 'in_progress']);
    }

    /** Scope: filter by property */
    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /** Scope: filter by recipient type (front_desk or hq) */
    public function scopeForRecipient($query, string $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }

    /** Scope: filter by user */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /** Get unread message count for a specific user */
    public function getUnreadCountForUser(int $userId): int
    {
        /* Find the user's last-read timestamp for this ticket */
        $read = $this->reads()->where('user_id', $userId)->first();
        $lastReadAt = $read?->last_read_at;

        /* Count messages from other users that arrived after last read */
        $query = $this->messages()->where('sender_id', '!=', $userId);
        if ($lastReadAt) {
            $query->where('created_at', '>', $lastReadAt);
        }

        return $query->count();
    }

    /** Mark ticket as read by a user — updates or creates the read record */
    public function markAsReadByUser(int $userId): void
    {
        TicketRead::updateOrCreate(
            ['ticket_id' => $this->id, 'user_id' => $userId],
            ['last_read_at' => now()]
        );
    }

    /** Check if ticket can be reopened (within 7-day window) */
    public function canBeReopened(): bool
    {
        if ($this->ticket_status !== 'closed') {
            return false;
        }
        if (!$this->reopen_deadline) {
            return false;
        }
        return now()->lte($this->reopen_deadline);
    }

    /** Check if ticket is in an active (non-closed) state */
    public function isActive(): bool
    {
        return in_array($this->ticket_status, ['open', 'in_progress', 'reopened']);
    }
}
