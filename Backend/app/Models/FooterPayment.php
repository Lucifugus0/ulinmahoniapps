<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FooterPayment model — stores accepted payment method icons displayed in the website footer.
 * Table: m_footer_payments
 */
class FooterPayment extends Model
{
    protected $table = 'm_footer_payments';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'name',
        'icon_image',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    /** Relationship: admin who created this payment entry */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this payment entry */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
