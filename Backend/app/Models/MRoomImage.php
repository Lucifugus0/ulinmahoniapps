<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MRoomImage extends Model
{
    protected $table = 'm_room_images';
    protected $primaryKey = 'idrec';
    public $timestamps = false; // karena tidak pakai timestamps Laravel standar

    protected $fillable = [
        'room_id',
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

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }

            if (Storage::disk('public')->exists($this->image)) {

                return asset('storage/' . ltrim($this->image, '/'));
            }

            return null;
        }

        return null;
    }


    protected $appends = ['image_url'];

    

   
}
