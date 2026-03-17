<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'm_promo_banners';
    protected $primaryKey = 'idrec';
    
    protected $fillable = [
        'title',
        'attachment',
        'descriptions',
        'created_by',
        'updated_by',
        'status'
    ];
} 