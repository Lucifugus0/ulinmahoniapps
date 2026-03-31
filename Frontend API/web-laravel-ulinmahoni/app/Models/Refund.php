<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;

    protected $table = 't_refund';

    protected $fillable = [
        'id_booking',
        'status',
        'reason',
        'amount',
        'img',
        'image_caption',
        'image_path',
        'refund_date',
        // User-initiated refund fields
        'requested_by',
        'refund_type',
        'refund_bank_name',
        'refund_account_no',
        'refund_account_holder',
        // Refund breakdown
        'room_refund',
        'deposit_refund',
        'other_refund',
        // Admin processing
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'room_refund' => 'decimal:4',
        'deposit_refund' => 'decimal:4',
        'other_refund' => 'decimal:4',
        'amount' => 'decimal:4',
    ];

    // Linked transaction via order_id
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_booking', 'order_id');
    }

    // User who requested the refund
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
