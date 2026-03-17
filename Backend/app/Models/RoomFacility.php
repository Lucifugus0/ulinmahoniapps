<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomFacility extends Model
{
    use HasFactory;
    protected $table = 'm_room_facility';
    protected $primaryKey = 'idrec';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'facility',
        'icon',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
