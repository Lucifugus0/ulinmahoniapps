<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
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
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'used_at' => 'datetime',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'idrec');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }
}
