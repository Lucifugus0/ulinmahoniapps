<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
        'sort_order',
    ];

    protected $appends = ['image_url', 'mobile_image_url'];

    public function promoBanner()
    {
        return $this->belongsTo(PromoBanner::class, 'promo_banner_id', 'idrec');
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        // If already a full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // If starts with storage/
        if (str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        // If starts with public/
        if (str_starts_with($this->image, 'public/')) {
            return asset('storage/' . str_replace('public/', '', $this->image));
        }

        // Default: assume it's in storage
        return asset('storage/' . $this->image);
    }

    /** Accessor: full URL for mobile-optimized banner image (falls back to main image) */
    public function getMobileImageUrlAttribute()
    {
        if (empty($this->mobile_image)) {
            return $this->image_url;
        }

        if (filter_var($this->mobile_image, FILTER_VALIDATE_URL)) {
            return $this->mobile_image;
        }

        if (str_starts_with($this->mobile_image, 'storage/')) {
            return asset($this->mobile_image);
        }

        return asset('storage/' . $this->mobile_image);
    }
}
