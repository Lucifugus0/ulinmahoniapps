<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingFeeTransactionImage extends Model
{
    use HasFactory;

    protected $table = 't_parking_fee_transaction_image';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'parking_transaction_id',
        'image',
        'image_type',
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function parkingTransaction()
    {
        return $this->belongsTo(ParkingFeeTransaction::class, 'parking_transaction_id', 'idrec');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
