<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    protected $table = 'm_promo_banners';
    protected $primaryKey = 'idrec';
    public $timestamps = false;

    protected $fillable = [
        'title',
        'image',
        'descriptions',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
        'status',
    ];

    /**
     * Get the image attribute as base64.
     */
    public function getImageAttribute($value)
    {
        if (!$value) return null;
        // If already base64, return as is
        if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $value)) {
            return $value;
        }
        // If it's a file path, encode
        if (file_exists($value)) {
            return base64_encode(file_get_contents($value));
        }
        return base64_encode($value);
    }
}