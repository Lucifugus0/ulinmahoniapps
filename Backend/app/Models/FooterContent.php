<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FooterContent model — stores key-value pairs for footer section content (e.g. copyright text, description).
 * Table: m_footer_content
 */
class FooterContent extends Model
{
    protected $table = 'm_footer_content';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'key',
        'value',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /** Scope: only active records (status = 1) */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /** Relationship: admin who created this record */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this record */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
