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
        'start_rent',
        'end_rent',
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
        'start_rent' => 'date',
        'end_rent' => 'date',
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

    /**
     * "01 Mar 2026 → 01 Jun 2026" — display label for this payment's rental period.
     * Falls back to the legacy on-the-fly computation when columns are unbackfilled.
     */
    public function getRentPeriodLabelAttribute(): string
    {
        if ($this->start_rent && $this->end_rent) {
            return $this->start_rent->format('d M Y') . ' → ' . $this->end_rent->format('d M Y');
        }

        // Backward-compat: legacy rows pre-2026-05-07 have neither column populated;
        // fall back to transaction_date + parking_duration months so the UI never breaks.
        if ($this->transaction_date && $this->parking_duration) {
            $start = $this->transaction_date->copy()->startOfDay();
            $end = $start->copy()->addMonths((int) $this->parking_duration);
            return $start->format('d M Y') . ' → ' . $end->format('d M Y');
        }

        return '—';
    }
}
