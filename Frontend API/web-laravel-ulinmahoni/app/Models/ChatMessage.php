<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 't_chat_messages';
    protected $primaryKey = 'id';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message_text',
        'message_type',
        'is_edited',
        'edited_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $appends = ['formatted_time'];

    // RELATIONSHIPS

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'message_id', 'id');
    }

    public function reads()
    {
        return $this->hasMany(ChatMessageRead::class, 'message_id', 'id');
    }

    public function readByUsers()
    {
        return $this->belongsToMany(User::class, 't_chat_message_reads', 'message_id', 'user_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    // SCOPES

    public function scopeByConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeBySender($query, $senderId)
    {
        return $query->where('sender_id', $senderId);
    }

    public function scopeText($query)
    {
        return $query->where('message_type', 'text');
    }

    public function scopeWithAttachments($query)
    {
        return $query->whereIn('message_type', ['image', 'file']);
    }

    public function scopeUnreadByUser($query, $userId, $lastReadAt)
    {
        return $query->where('sender_id', '!=', $userId)
                    ->where('created_at', '>', $lastReadAt);
    }

    // HELPER METHODS

    public function isReadBy($userId)
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function markAsReadBy($userId)
    {
        return ChatMessageRead::firstOrCreate([
            'message_id' => $this->id,
            'user_id' => $userId
        ], [
            'read_at' => now()
        ]);
    }

    public function canBeEditedBy($userId)
    {
        // Only sender can edit
        if ($this->sender_id !== $userId) {
            return false;
        }

        // Can't edit system messages
        if ($this->message_type === 'system') {
            return false;
        }

        // Optional: Set time limit (e.g., 15 minutes)
        // $editTimeLimit = 15; // minutes
        // return $this->created_at->diffInMinutes(now()) < $editTimeLimit;

        return true;
    }

    public function getFormattedTimeAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s');
    }

    // EVENTS
    protected static function boot()
    {
        parent::boot();

        static::created(function ($message) {
            // Update conversation's last_message_at
            $message->conversation->updateLastMessageTime();
        });
    }
}
