<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 't_booking';
    protected $primaryKey = 'idrec';
    // public $incrementing = true;
    // public $timestamps = true; // Uses created_at and updated_at

    protected $fillable = [
        'property_id',
        'room_id',
        'order_id',
        'user_name',
        'user_email',
        'user_phone_number',
        'check_in_at',
        'ktp_img',
        'check_out_at',
        'doc_type',
        'doc_path',
        'created_by',
        'updated_by',
        'status',
        'previous_booking_id',
        'reason',
        'description',
        'room_changed_at',
        'room_changed_by',
        'is_printed'
    ];

    protected $dates = [
        'transaction_date',
        'check_in',
        'check_out',
        'paid_at',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'grandtotal_price' => 'decimal:2',
        'daily_price' => 'decimal:2',
        'room_price' => 'decimal:2',
        'admin_fees' => 'decimal:2',
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

    public function getFormattedPriceAttribute()
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

    // Use transaction_code as the route key
    public function getRouteKeyName()
    {
        return 'transaction_code';
    }
}
