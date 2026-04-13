<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FooterLink model — stores navigation links displayed in the website footer.
 * Table: m_footer_links
 */
class FooterLink extends Model
{
    protected $table = 'm_footer_links';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'label',
        'url',
        'link_group',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    /** Scope: only active records (status = 1), ordered by sort_order */
    public function scopeActive($query)
    {
        return $query->where('status', 1)->orderBy('sort_order');
    }

    /** Relationship: admin who created this link */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this link */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
