<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLogging extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 't_transaction_logging';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payment_method',
        'invoice_number',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'customer_id',
        'customer_name',
        'customer_email',
        'approval_code',
        'authorize_id',
        'request_headers',
        'request_body',
        'response_data',
        'payment_details',
        'notes',
        'transaction_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_data' => 'array',
        'payment_details' => 'array',
        'transaction_date' => 'datetime',
    ];

    /**
     * Scope a query to only include logs for a specific payment method.
     */
    public function scopePaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Scope a query to only include logs with a specific status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include logs for a specific invoice.
     */
    public function scopeInvoice($query, string $invoiceNumber)
    {
        return $query->where('invoice_number', $invoiceNumber);
    }
}
