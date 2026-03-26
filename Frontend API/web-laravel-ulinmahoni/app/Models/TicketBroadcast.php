<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketBroadcast model — one-way announcement from Front Desk or HQ.
 * Broadcasts are read-only threads that appear in the user's ticket list.
 * Sent to targeted audiences via FCM push notifications.
 */
class TicketBroadcast extends Model
{
    protected $table = 't_ticket_broadcasts';

    protected $fillable = [
        'broadcast_number', 'sender_type', 'property_id', 'sender_id',
        'title', 'message_text', 'audience', 'recipient_count',
        'sent_at', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /** The admin user who sent this broadcast */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** The property this broadcast is from (NULL for HQ) */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    /** Read receipts for this broadcast */
    public function reads()
    {
        return $this->hasMany(TicketBroadcastRead::class, 'broadcast_id');
    }

    /** Scope: filter by sender type */
    public function scopeBySenderType($query, string $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    /** Scope: filter by property */
    public function scopeForProperty($query, int $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    /** Check if a specific user has read this broadcast */
    public function isReadByUser(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->whereNotNull('read_at')->exists();
    }

    /** Mark broadcast as read by a user */
    public function markAsReadByUser(int $userId): void
    {
        TicketBroadcastRead::updateOrCreate(
            ['broadcast_id' => $this->id, 'user_id' => $userId],
            ['read_at' => now()]
        );
    }
}
