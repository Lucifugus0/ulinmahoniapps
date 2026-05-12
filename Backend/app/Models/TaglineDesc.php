<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TaglineDesc model — stores tagline descriptions displayed randomly on home pages.
 * Table: m_tagline_desc
 */
class TaglineDesc extends Model
{
    protected $table = 'm_tagline_desc';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'description',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /** Relationship: admin who created this tagline description */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this tagline description */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
