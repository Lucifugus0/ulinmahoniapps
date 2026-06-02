<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FooterContact model — stores contact information items displayed in the website footer (e.g. phone, email, address).
 * Table: m_footer_contacts
 */
class FooterContact extends Model
{
    protected $table = 'm_footer_contacts';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'icon_class',
        'label',
        'value',
        'link_url',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    /** Relationship: admin who created this contact entry */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this contact entry */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
