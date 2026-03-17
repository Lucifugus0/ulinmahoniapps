<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherUsage extends Model
{
    use HasFactory;

    protected $table = 't_voucher_logging';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'voucher_id',
        'voucher_code',
        'user_id',
        'order_id',
        'transaction_id',
        'property_id',
        'room_id',
        'original_amount',
        'discount_amount',
        'final_amount',
        'used_at',
        'status',
        'metadata'
    ];

    protected $hidden = [
        'metadata'
    ];

    protected $casts = [
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'used_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $dates = [
        'used_at',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'idrec');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'idrec');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    // Scopes
    public function scopeApplied($query)
    {
        return $query->where('status', 'applied');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByVoucher($query, $voucherId)
    {
        return $query->where('voucher_id', $voucherId);
    }

    // Accessors
    public function getFormattedDiscountAttribute()
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    public function getFormattedOriginalAmountAttribute()
    {
        return 'Rp ' . number_format($this->original_amount, 0, ',', '.');
    }

    public function getFormattedFinalAmountAttribute()
    {
        return 'Rp ' . number_format($this->final_amount, 0, ',', '.');
    }
}
