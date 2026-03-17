<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoBanner extends Model
{
    protected $table = 'm_promo_banners';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'title',
        'image_id',
        'descriptions',
        'promo_code',
        'how_to_claim',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status'       => 'integer',
        'how_to_claim' => 'array',
    ];

    public function images()
    {
        return $this->hasMany(PromoBannerImage::class, 'promo_banner_id', 'idrec')->orderBy('sort_order');
    }

    public function primaryImage()
    {
        return $this->belongsTo(PromoBannerImage::class, 'image_id', 'idrec');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    public function getStatusLabelAttribute()
    {
        return $this->status == 1 ? 'Active' : 'Inactive';
    }
}
