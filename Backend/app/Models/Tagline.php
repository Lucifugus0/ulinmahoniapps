<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tagline model — stores taglines displayed randomly on home pages.
 * Table: m_taglines
 */
class Tagline extends Model
{
    protected $table = 'm_taglines';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'tagline',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /** Relationship: admin who created this tagline */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this tagline */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
