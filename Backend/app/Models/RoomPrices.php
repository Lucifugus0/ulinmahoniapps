<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomPrices extends Model
{
    protected $table = 'm_room_prices';
    protected $primaryKey = 'idrec';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'price',
        'price_type', /* Multi-Tier Pricing: tracks why this date has its price (weekday/weekend/holiday/high_season/low_season/manual) */
        'date',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }
}
