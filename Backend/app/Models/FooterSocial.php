<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * FooterSocial model — stores social media links displayed in the website footer (e.g. Instagram, TikTok, YouTube).
 * Table: m_footer_socials
 */
class FooterSocial extends Model
{
    protected $table = 'm_footer_socials';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'name',
        'icon_class',
        'icon_image',
        'url',
        'hover_color',
        'sort_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort_order' => 'integer',
    ];

    /** Relationship: admin who created this social link */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this social link */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
