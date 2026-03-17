<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Parking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_parking';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'property_id',
        'parking_type',
        'vehicle_plate',
        'owner_name',
        'owner_phone',
        'user_id',
        'order_id',
        'parking_duration',
        'fee_amount',
        'notes',
        'status',
        'management_only',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(ParkingFeeTransaction::class, 'parking_id', 'idrec');
    }

    public function bookingTransaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the parking fee configuration for this parking's property + type.
     */
    public function getParkingFeeAttribute()
    {
        return ParkingFee::where('property_id', $this->property_id)
            ->where('parking_type', $this->parking_type)
            ->where('status', 1)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('vehicle_plate', 'like', "%{$search}%")
              ->orWhere('owner_name', 'like', "%{$search}%")
              ->orWhereHas('property', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }
}
