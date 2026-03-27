<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositFeeTransaction extends Model
{
    use HasFactory;

    protected $table = 't_deposit_fee_transaction';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'invoice_id',
        'order_id',
        'fee_amount',
        'transaction_date',
        'transaction_status',
        'paid_at',
        'verified_by',
        'verified_at',
        'notes',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:4',
        'transaction_date' => 'datetime',
        'paid_at' => 'datetime',
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    /**
     * Get the deposit fee via the transaction's property.
     * Uses hasOneThrough to join through t_transactions to m_deposit_fee.
     */
    public function depositFee()
    {
        return $this->hasOneThrough(
            DepositFee::class,
            Transaction::class,
            'order_id',    // FK on t_transactions
            'property_id', // FK on m_deposit_fee
            'order_id',    // Local key on t_deposit_fee_transaction
            'property_id'  // Local key on t_transactions
        );
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function images()
    {
        return $this->hasMany(DepositFeeTransactionImage::class, 'deposit_transaction_id', 'idrec');
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
