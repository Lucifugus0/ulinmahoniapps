<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $table = 't_chat_messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'message_text',
        'message_type',
        'is_edited',
        'edited_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    // Relationships
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments()
    {
        return $this->hasMany(ChatAttachment::class, 'message_id');
    }

    public function readBy()
    {
        return $this->belongsToMany(User::class, 't_chat_message_reads', 'message_id', 'user_id')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeWithAttachments($query)
    {
        return $query->with('attachments');
    }

    public function scopeSearchText($query, $searchTerm)
    {
        return $query->whereRaw("MATCH(message_text) AGAINST(? IN BOOLEAN MODE)", [$searchTerm]);
    }

    // Methods
    public function markAsReadBy($userId)
    {
        if (!$this->isReadBy($userId)) {
            $this->readBy()->attach($userId, [
                'read_at' => now(),
                'created_at' => now(),
            ]);

            // Update conversation's last_read_at for participant
            $participant = ChatParticipant::where('conversation_id', $this->conversation_id)
                                         ->where('user_id', $userId)
                                         ->first();
            if ($participant) {
                $participant->update([
                    'last_read_at' => now(),
                    'updated_by' => $userId,
                ]);
            }
        }
    }

    public function isReadBy($userId)
    {
        return $this->readBy()->where('user_id', $userId)->exists();
    }

    public function hasAttachments()
    {
        return $this->attachments()->exists();
    }
}
