<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Master room name types model — admin-manageable room type names.
 * Replaces hardcoded room type dropdown (Standar, Superior, Deluxe, Suite).
 */
class RoomNameType extends Model
{
    protected $table = 'm_room_name_types';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'name',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Scope: only active room types */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '1');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
