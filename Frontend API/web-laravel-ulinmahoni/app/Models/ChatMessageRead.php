<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessageRead extends Model
{
    use HasFactory;

    protected $table = 't_chat_message_reads';
    protected $primaryKey = 'id';

    public $timestamps = false; // Only created_at in migration

    protected $fillable = [
        'message_id',
        'user_id',
        'read_at'
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime'
    ];

    // RELATIONSHIPS

    public function message()
    {
        return $this->belongsTo(ChatMessage::class, 'message_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // SCOPES

    public function scopeByMessage($query, $messageId)
    {
        return $query->where('message_id', $messageId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
