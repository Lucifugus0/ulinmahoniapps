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
        'thumbnail',
        'caption',
        'sort_order'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function promoBanner()
    {
        return $this->belongsTo(PromoBanner::class, 'promo_banner_id', 'idrec');
    }
}
