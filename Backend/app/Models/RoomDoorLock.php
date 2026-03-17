<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomDoorLock extends Model
{
    protected $table = 'm_rooms_door_lock';
    protected $primaryKey = 'idrec';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'room_idrec',
        'lock_id',
        'lock_alias',
        'lock_mac',
        'model_num',
        'firmware_revision',
        'battery_level',
        'has_gateway',
        'lock_sound',
        'privacy_lock',
        'is_frozen',
        'passage_mode',
        'last_sync_at',
        'passcode',
        'passcode_name',
        'passcode_start',
        'passcode_end',
    ];

    protected $casts = [
        'has_gateway' => 'boolean',
        'battery_level' => 'integer',
        'lock_sound' => 'integer',
        'privacy_lock' => 'integer',
        'is_frozen' => 'integer',
        'passage_mode' => 'integer',
        'last_sync_at' => 'integer',
        'passcode_start' => 'integer',
        'passcode_end' => 'integer',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_idrec', 'idrec');
    }
}
