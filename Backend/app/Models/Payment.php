<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 't_payment';
    protected $primaryKey = 'idrec';
    public $timestamps = false; 
    
    protected $fillable = [
        'order_id',
        'property_id',
        'room_id',
        'user_id',
        'grandtotal_price',
        'verified_by',
        'verified_at',
        'notes',
        'payment_status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by', 'id');
    }
}
