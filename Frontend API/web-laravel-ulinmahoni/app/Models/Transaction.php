<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 't_transactions';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'order_id',
        'user_id',
        'user_name',
        'user_phone_number',
        'property_id',
        'property_name',
        'transaction_date',
        'check_in',
        'check_out',
        'room_id',
        'room_name',
        'user_email',
        'booking_type',
        'booking_days',
        'daily_price',
        'booking_months',
        'booking_years',      /* Multi-Tier Pricing: number of years for annual bookings */
        'monthly_price',
        'annual_price',       /* Multi-Tier Pricing: per-year rate for annual bookings */
        'room_price',
        'attachment',
        'admin_fees',
        'service_fees',
        'deposit_fee',
        'parking_fee',
        'parking_type',
        'parking_duration',
        'grandtotal_price',
        'voucher_id',
        'voucher_code',
        'discount_amount',
        'subtotal_before_discount',
        'property_type',
        'transaction_type',
        'transaction_code',
        'transaction_status',
        'status',
        'paid_at',
        'expired_at',
        'is_renewal',
        'renewal_status',
        'price_breakdown',    /* Multi-Tier Pricing: JSON array of per-date prices */
    ];

    protected $hidden = [
        // 'attachment',
        'created_at',
        'updated_at'
    ];

    protected $dates = [
        'transaction_date',
        'check_in',
        'check_out',
        'paid_at',
        'expired_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'grandtotal_price' => 'decimal:2',
        'daily_price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',    /* Multi-Tier Pricing */
        'price_breakdown' => 'array',     /* Multi-Tier Pricing: cast JSON to array */
        'room_price' => 'decimal:2',
        'admin_fees' => 'decimal:2',
        'service_fees' => 'decimal:2',
        'deposit_fee' => 'decimal:2',
        'parking_fee' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal_before_discount' => 'decimal:2',
        'booking_days' => 'integer'
    ];

    public function getStatusColorAttribute()
    {
        return match($this->transaction_status) {
            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
            'confirmed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
            'completed' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800'],
        };
    }

    public function getFormattedGrandtotalPriceAttribute()
    {
        return 'Rp ' . number_format($this->grandtotal_price, 0, ',', '.');
    }

    public function getFormattedDailyPriceAttribute()
    {
        return 'Rp ' . number_format($this->daily_price, 0, ',', '.');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'order_id', 'order_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_id', 'idrec');
    }

    public function voucherUsage()
    {
        return $this->hasOne(VoucherUsage::class, 'transaction_id', 'idrec');
    }

    // Use transaction_code as the route key
    public function getRouteKeyName()
    {
        return 'transaction_code';
    }

    public function getFormattedDiscountAmountAttribute()
    {
        return 'Rp ' . number_format($this->discount_amount, 0, ',', '.');
    }

    public function getCheckInAt()
    {
        $booking = $this->booking;

        if ($booking && $booking->check_in_at !== null) {
            return $booking->check_in_at;
        }

        return null;
    }

    public function chatConversations()
    {
        return $this->hasMany(ChatConversation::class, 'order_id', 'order_id');
    }
}
