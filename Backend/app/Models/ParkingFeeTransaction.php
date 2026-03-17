<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingFeeTransaction extends Model
{
    use HasFactory;

    protected $table = 't_parking_fee_transaction';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'property_id',
        'parking_id',
        'invoice_id',
        'order_id',
        'user_id',
        'user_name',
        'user_phone',
        'parking_type',
        'vehicle_plate',
        'parking_duration',
        'fee_amount',
        'transaction_date',
        'transaction_status',
        'paid_at',
        'verified_by',
        'verified_at',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:4',
        'parking_duration' => 'integer',
        'transaction_date' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id', 'idrec');
    }

    /**
     * Get parking fee via parking registration's property + type.
     */
    public function getParkingFeeViaParking()
    {
        if ($this->parking) {
            return ParkingFee::where('property_id', $this->parking->property_id)
                ->where('parking_type', $this->parking->parking_type)
                ->where('status', 1)
                ->first();
        }

        return null;
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function images()
    {
        return $this->hasMany(ParkingFeeTransactionImage::class, 'parking_transaction_id', 'idrec');
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
