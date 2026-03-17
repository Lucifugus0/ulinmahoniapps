<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ParkingFee extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_parking_fee';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'property_id',
        'parking_type',
        'fee',
        'capacity',
        'quota_used',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fee' => 'decimal:4',
        'capacity' => 'integer',
        'quota_used' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function transactions()
    {
        return $this->hasMany(ParkingFeeTransaction::class, 'parking_fee_id', 'idrec');
    }

    /**
     * Get parkings registered for this fee's property + type.
     */
    public function parkings()
    {
        return Parking::where('property_id', $this->property_id)
            ->where('parking_type', $this->parking_type);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('property', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%");
        });
    }

    public function getAvailableCapacityAttribute(): int
    {
        $occupied = $this->transactions()
            ->whereIn('transaction_status', ['paid', 'waiting', 'pending'])
            ->where('status', 1)
            ->count();

        return max(0, $this->capacity - $occupied);
    }

    /**
     * Get available quota (capacity - quota_used)
     */
    public function getAvailableQuotaAttribute(): int
    {
        return max(0, $this->capacity - $this->quota_used);
    }

    /**
     * Check if quota is available
     */
    public function hasAvailableQuota(int $amount = 1): bool
    {
        return $this->available_quota >= $amount;
    }

    /**
     * Increment quota used (when parking payment is created)
     */
    public function incrementQuota(int $amount = 1): bool
    {
        if (!$this->hasAvailableQuota($amount)) {
            return false;
        }

        $this->increment('quota_used', $amount);
        return true;
    }

    /**
     * Decrement quota used (when vehicle checks out)
     */
    public function decrementQuota(int $amount = 1): bool
    {
        if ($this->quota_used < $amount) {
            return false;
        }

        $this->decrement('quota_used', $amount);
        return true;
    }

    /**
     * Get quota usage percentage
     */
    public function getQuotaUsagePercentageAttribute(): float
    {
        if ($this->capacity <= 0) {
            return 0;
        }

        return round(($this->quota_used / $this->capacity) * 100, 2);
    }
}
