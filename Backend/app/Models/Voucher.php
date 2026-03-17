<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

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
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scope_ids' => 'array',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'discount_percentage' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'min_transaction_amount' => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function usages()
    {
        return $this->hasMany(VoucherUsage::class, 'voucher_id', 'idrec');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'voucher_id', 'idrec');
    }

    // Check if voucher is valid
    public function isValid()
    {
        $now = now();

        // Check status
        if ($this->status !== 'active') {
            return false;
        }

        // Check validity period
        if ($now->lt($this->valid_from) || $now->gt($this->valid_to)) {
            return false;
        }

        // Check usage limit
        if ($this->max_total_usage > 0 && $this->current_usage_count >= $this->max_total_usage) {
            return false;
        }

        return true;
    }

    // Check if user can use this voucher
    public function canBeUsedByUser($userId)
    {
        if (!$this->isValid()) {
            return false;
        }

        $userUsageCount = $this->usages()
            ->where('user_id', $userId)
            ->where('status', 'applied')
            ->count();

        return $userUsageCount < $this->max_usage_per_user;
    }
}
