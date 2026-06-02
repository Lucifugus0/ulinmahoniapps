<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoBannerImage extends Model
{
    protected $table = 'm_promo_banner_images';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'promo_banner_id',
        'image',
        'mobile_image',
        'thumbnail',
        'caption',
        'sort_order'
    ];

    protected $appends = ['mobile_image_url'];

    /** Accessor: full URL for mobile banner image (falls back to main image) */
    public function getMobileImageUrlAttribute()
    {
        $adminUrl = rtrim(config('app.admin_url', env('ADMIN_URL', '')), '/');
        if (!empty($this->mobile_image) && $adminUrl) {
            return $adminUrl . '/storage/' . $this->mobile_image;
        }
        // Fallback to main image URL
        if (!empty($this->image) && $adminUrl) {
            return $adminUrl . '/storage/' . $this->image;
        }
        return null;
    }

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function promoBanner()
    {
        return $this->belongsTo(PromoBanner::class, 'promo_banner_id', 'idrec');
    }
}
