<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Room extends Model
{
    protected $table = 'm_rooms';
    protected $primaryKey = 'idrec';
    public $incrementing = true;
    public $timestamps = false;
    protected $keyType = 'int';

    protected $fillable = [
        'property_id',
        'property_name',
        'slug',
        'no',
        'name',
        'descriptions',
        'size',
        'bed_type',
        'capacity',
        'periode',
        'periode_daily',
        'periode_monthly',
        'type',
        'level',
        'facility',
        'price',
        'discount_percent',
        'price_original_daily',
        'price_discounted_daily',
        'price_original_monthly',
        'price_discounted_monthly',
        /* Multi-Tier Pricing: annual pricing + weekday/weekend base prices */
        'periode_annual',
        'price_original_annual',
        'price_discounted_annual',
        'price_weekday',
        'price_weekend',
        'created_by',
        'updated_by',
        'status',
        'rental_status',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'facility' => 'array',
        'periode' => 'array'
    ];
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function dailyPrices()
    {
        return $this->hasMany(RoomPrices::class, 'idrec', 'room_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function roomImages()
    {
        return $this->hasMany(MRoomImage::class, 'room_id', 'idrec');
    }

    /**
     * <!-- Multi-Tier Pricing: relationship to pricing rules (weekday, weekend, holiday, seasons) -->
     */
    public function pricingRules()
    {
        return $this->hasMany(RoomPricingRule::class, 'room_id', 'idrec');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'room_id', 'idrec');
    }

    public function thumbnail()
    {
        return $this->hasOne(MRoomImage::class, 'room_id', 'idrec')
            ->where('thumbnail', true);
    }

    public function getFacilityNamesAttribute()
    {
        if (empty($this->facility)) {
            return [];
        }

        $facilityIds = $this->facility;
        $facilities = RoomFacility::whereIn('idrec', $facilityIds)
            ->where('status', 1)
            ->pluck('facility', 'idrec')
            ->toArray();

        $facilityNames = [];
        foreach ($facilityIds as $id) {
            if (isset($facilities[$id])) {
                $facilityNames[] = $facilities[$id];
            }
        }

        return $facilityNames;
    }
}
