<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepositFee extends Model
{
    use HasFactory;

    protected $table = 'm_deposit_fee';

    protected $fillable = [
        // Data
        "idrec",    
        "property_id",
        "amount",
        "status",
        // Logs
        "created_by",
        "updated_by",
        "created_at",
        "updated_at",
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        "property_id" => "integer",
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }
}
