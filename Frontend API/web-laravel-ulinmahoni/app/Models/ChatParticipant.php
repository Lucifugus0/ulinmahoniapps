<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;

    protected $table = 't_chat_participants';
    protected $primaryKey = 'id';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'joined_at',
        'last_read_at',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // RELATIONSHIPS

    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // SCOPES

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staff');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeByConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // HELPER METHODS

    public function updateLastRead()
    {
        $this->update(['last_read_at' => now()]);
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
