<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Voucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'm_vouchers';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_percentage',
        'max_discount_amount',
        'max_total_usage',
        'current_usage_count',
        'max_usage_per_user',
        'valid_from',
        'valid_to',
        'min_transaction_amount',
        'scope_type',
        'scope_ids',
        'property_id',
        'status',
        'how_to_claim',
        'created_by',
        'updated_by'
    ];

    protected $hidden = [
        'deleted_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'discount_percentage' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
        'max_total_usage' => 'integer',
        'current_usage_count' => 'integer',
        'max_usage_per_user' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'scope_ids' => 'array',
        'how_to_claim' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'valid_from',
        'valid_to',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // Relationships
    public function usages()
    {
        return $this->hasMany(VoucherUsage::class, 'voucher_id', 'idrec');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'm_voucher_properties', 'voucher_id', 'property_id', 'idrec', 'idrec');
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'm_voucher_rooms', 'voucher_id', 'room_id', 'idrec', 'idrec');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('valid_from', '<=', $now)
                    ->where('valid_to', '>=', $now);
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->valid()
                    ->where(function($q) {
                        $q->where('max_total_usage', 0)
                          ->orWhereRaw('current_usage_count < max_total_usage');
                    });
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where(function ($q) use ($propertyId) {
            $q->whereNull('property_id')  // Global vouchers (NULL = all properties)
              ->orWhere('property_id', 0)  // Global vouchers (0 = all properties)
              ->orWhere('property_id', $propertyId);  // Property-specific vouchers
        });
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return Carbon::now()->isAfter($this->valid_to);
    }

    public function getIsValidDateAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->valid_from, $this->valid_to);
    }

    public function getIsUsageAvailableAttribute()
    {
        if ($this->max_total_usage == 0) {
            return true; // Unlimited
        }
        return $this->current_usage_count < $this->max_total_usage;
    }

    public function getRemainingUsageAttribute()
    {
        if ($this->max_total_usage == 0) {
            return 'Unlimited';
        }
        return max(0, $this->max_total_usage - $this->current_usage_count);
    }

    public function getFormattedDiscountAttribute()
    {
        return number_format($this->discount_percentage, 0) . '% (Max Rp ' . number_format($this->max_discount_amount, 0, ',', '.') . ')';
    }

    public function getFormattedMinTransactionAttribute()
    {
        return 'Rp ' . number_format($this->min_transaction_amount, 0, ',', '.');
    }
}
