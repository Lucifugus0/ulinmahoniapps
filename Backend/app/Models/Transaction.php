<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 't_transactions';
    protected $primaryKey = 'idrec';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'property_id',
        'room_id',
        'order_id',
        'invoice_number',
        'user_id',
        'user_name',
        'user_phone_number',
        'property_name',
        'transaction_date',
        'check_in',
        'check_out',
        'original_checkin_day',
        'room_name',
        'user_email',
        'booking_days',
        'booking_months',
        'booking_years',      /* Multi-Tier Pricing: number of years for annual bookings */
        'daily_price',
        'monthly_price',
        'annual_price',       /* Multi-Tier Pricing: per-year rate for annual bookings */
        'room_price',
        'admin_fees',
        'service_fees',
        'grandtotal_price',
        'deposit_fee',
        'parking_fee',
        'parking_duration',
        'parking_type',
        'voucher_id',
        'voucher_code',
        'discount_amount',           /* Voucher/promo discount deducted from room_price */
        'subtotal_before_discount',  /* room_price before discount was applied */
        'virtual_account_no',
        'payment_bank',
        'property_type',
        'transaction_type',
        'transaction_code',
        'transaction_status',
        'is_renewal',
        'renewal_status',
        'booking_type',
        'notes',
        'attachment',
        'status',
        'paid_at',
        'cancel_at',
        'deposit_fee',
        'parking_fee',
        'expired_at',
        'price_breakdown',    /* Multi-Tier Pricing: JSON array of per-date prices [{date, price, type}] */
    ];


    protected $casts = [
        'transaction_date' => 'datetime',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'paid_at' => 'datetime',
        'cancel_at' => 'datetime',
        'expired_at' => 'datetime',
        'price_breakdown' => 'array',  /* Multi-Tier Pricing: cast JSON to array */
    ];

    public function booking()
    {
        return $this->hasOne(Booking::class, 'order_id', 'order_id')
            ->orderByDesc('idrec');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'order_id', 'order_id');
    }

    public function getAttachmentBase64Attribute()
    {
        return $this->attachment ? base64_encode($this->attachment) : null;
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    // Chat Relationship
    public function chatConversations()
    {
        return $this->hasMany(ChatConversation::class, 'order_id', 'order_id');
    }
}
