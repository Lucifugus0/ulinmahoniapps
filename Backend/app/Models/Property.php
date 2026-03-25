<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $table = 'm_properties';
    protected $primaryKey = 'idrec';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'slug',
        'tags',
        'gender',
        'name',
        'initial',
        'description',
        'level_count',
        'province',
        'city',
        'subdistrict',
        'village',
        'postal_code',
        'address',
        'location',
        'latitude',
        'longitude',
        'price',
        'price_original_daily',
        'price_discounted_daily',
        'price_original_monthly',
        'price_discounted_monthly',
        /* Multi-Tier Pricing: annual pricing at property level */
        'periode_annual',
        'price_original_annual',
        'price_discounted_annual',

        'general',
        'security',
        'amenities',
        'nearby_locations',

        'room_facilities',
        'rules',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'general' => 'array',
        'security' => 'array',
        'amenities' => 'array',
        'nearby_locations' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function roomTypes()
    {
        return $this->hasMany(RoomType::class, 'property_id', 'idrec');
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class, 'property_id', 'idrec');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'property_id', 'idrec');
    }

    public function thumbnail()
    {
        return $this->hasOne(PropertyImage::class, 'property_id', 'idrec')
            ->where('thumbnail', 1);
    }

    public function getGeneralFacilities()
    {
        return PropertyFacility::whereIn('idrec', $this->general ?? [])->get();
    }

    public function getSecurityFacilities()
    {
        return PropertyFacility::whereIn('idrec', $this->security ?? [])->get();
    }

    public function getAmenityFacilities()
    {
        return PropertyFacility::whereIn('idrec', $this->amenities ?? [])->get();
    }

    /* Relationship: one deposit fee per property */
    public function depositFee()
    {
        return $this->hasOne(DepositFee::class, 'property_id', 'idrec');
    }

    /* Relationship: parking fees (car + motorcycle) for this property */
    public function parkingFees()
    {
        return $this->hasMany(ParkingFee::class, 'property_id', 'idrec');
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
