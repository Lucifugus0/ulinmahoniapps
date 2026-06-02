<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * LegalPage model — stores legal/policy pages (e.g. Terms of Service, Privacy Policy) with slug-based routing.
 * Table: m_legal_pages
 */
class LegalPage extends Model
{
    protected $table = 'm_legal_pages';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'slug',
        'title',
        'content',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /** Relationship: admin who created this legal page */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this legal page */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
