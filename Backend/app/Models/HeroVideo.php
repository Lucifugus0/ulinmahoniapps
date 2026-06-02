<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * HeroVideo model — stores hero background videos for home pages.
 * Table: m_hero_videos. Only one video can be active (status=1) at a time.
 * Constraints: MP4 format, 21:9 aspect ratio, max 100MB.
 */
class HeroVideo extends Model
{
    protected $table = 'm_hero_videos';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'title',
        'file_path',
        'file_size',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'file_size' => 'integer',
    ];

    /** Accessor: full URL to the video file via storage symlink */
    public function getVideoUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /** Relationship: admin who uploaded this video */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /** Relationship: admin who last updated this video */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }
}
