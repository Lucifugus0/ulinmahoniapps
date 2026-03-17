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
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'id_booking', 'order_id');
    }

    public function getImageUrlAttribute()
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : null;
    }
}
