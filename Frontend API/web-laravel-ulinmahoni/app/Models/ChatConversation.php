<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory;

    protected $table = 't_chat_conversations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'order_id',
        'property_id',
        'title',
        'status',
        'last_message_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // RELATIONSHIPS

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id', 'id')
                    ->orderBy('created_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id', 'id')
                    ->latest('created_at');
    }

    public function participants()
    {
        return $this->hasMany(ChatParticipant::class, 'conversation_id', 'id');
    }

    public function participantUsers()
    {
        return $this->belongsToMany(User::class, 't_chat_participants', 'conversation_id', 'user_id')
                    ->withPivot('role', 'joined_at', 'last_read_at')
                    ->withTimestamps();
    }

    // SCOPES

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('participants', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // HELPER METHODS

    public function isParticipant($userId)
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }

    public function getUnreadCount($userId)
    {
        $participant = $this->participants()->where('user_id', $userId)->first();

        if (!$participant || !$participant->last_read_at) {
            return $this->messages()->count();
        }

        return $this->messages()
                    ->where('created_at', '>', $participant->last_read_at)
                    ->where('sender_id', '!=', $userId)
                    ->count();
    }

    public function updateLastMessageTime()
    {
        $this->update(['last_message_at' => now()]);
    }
}
