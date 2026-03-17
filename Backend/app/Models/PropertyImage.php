<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class PropertyImage extends Model
{
    use HasFactory;

    protected $table = 'm_property_images';
    protected $primaryKey = 'idrec';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'property_id',
        'image',
        'thumbnail',
        'caption',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'thumbnail' => 'boolean',
    ];

    protected $appends = ['image_url', 'thumbnail_url'];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    /**
     * Get full image URL - Handle multiple scenarios
     */
    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return null;
        }

        // Cek jika sudah full URL
        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        // Cek jika path sudah ada storage/
        if (strpos($this->image, 'storage/') === 0) {
            return asset($this->image);
        }

        // Cek jika file exists di public path
        $publicPath = public_path($this->image);
        if (file_exists($publicPath)) {
            return asset($this->image);
        }

        // Coba dengan storage path
        $storagePath = 'storage/' . $this->image;
        $fullStoragePath = public_path($storagePath);
        if (file_exists($fullStoragePath)) {
            return asset($storagePath);
        }

        // Fallback ke storage URL
        return asset('storage/' . $this->image);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        if (!empty($this->thumbnail) && !empty($this->image)) {
            return $this->image_url; // Use same logic as image_url
        }
        return null;
    }

    /**
     * Check if image file actually exists
     */
    public function getImageExistsAttribute()
    {
        if (empty($this->image)) {
            return false;
        }

        $possiblePaths = [
            public_path($this->image),
            public_path('storage/' . $this->image),
            storage_path('app/public/' . $this->image)
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return true;
            }
        }

        return false;
    }
}