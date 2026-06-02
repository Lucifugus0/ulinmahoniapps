<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DeviceToken model — reads from the shared `device_tokens` table.
 * The Frontend API writes to this table when mobile users register their FCM tokens.
 * The Backend reads these tokens to send push notifications for chat messages.
 * No migration needed — table already exists from Frontend API.
 */
class DeviceToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'device_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Relationship to the user who owns this device token */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
