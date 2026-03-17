<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomItemCondition extends Model
{
    use HasFactory;

    protected $table = 't_room_item_conditions';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'order_id',
        'booking_id',
        'item_name',
        'condition',
        'custom_text',
        'notes',
        'damage_charge',
        'created_by',
    ];

    protected $casts = [
        'damage_charge' => 'decimal:2',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'order_id', 'order_id');
    }

    /**
     * Get the condition label in Indonesian
     */
    public function getConditionLabelAttribute()
    {
        return match ($this->condition) {
            'good' => 'Baik',
            'missing' => 'Hilang',
            'damaged' => 'Rusak',
            default => 'Tidak Diketahui',
        };
    }
}
