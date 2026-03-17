<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    protected $table = 't_chat_conversations';

    protected $fillable = [
        'order_id',
        'property_id',
        'title',
        'status',
        'last_message_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Relationships
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'order_id', 'order_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 't_chat_participants', 'conversation_id', 'user_id')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeWithUnreadCount($query, $userId)
    {
        return $query->withCount(['messages as unread_count' => function ($q) use ($userId) {
            $q->whereDoesntHave('readBy', function ($subQ) use ($userId) {
                $subQ->where('user_id', $userId);
            });
        }]);
    }

    // Methods
    public function addParticipant($userId, $role = 'customer')
    {
        return ChatParticipant::firstOrCreate([
            'conversation_id' => $this->id,
            'user_id' => $userId,
        ], [
            'role' => $role,
            'created_by' => auth()->id() ?? 'system',
            'updated_by' => auth()->id() ?? 'system',
        ]);
    }

    public function getUnreadCountForUser($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if (!$participant) {
            return 0;
        }

        $query = $this->messages()->where('sender_id', '!=', $userId);

        // If participant has last_read_at, only count messages after that time
        // If null, count all messages from other senders
        if ($participant->last_read_at) {
            $query->where('created_at', '>', $participant->last_read_at);
        }

        return $query->count();
    }

    public function markAsReadByUser($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();
        if ($participant) {
            $participant->update([
                'last_read_at' => now(),
                'updated_by' => $userId,
            ]);
        }
    }
}
