<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $table = 'm_room_type';
    protected $primaryKey = 'idrec';
    public $incrementing = false;
    public $timestamps = false;

    public function property()
    {
        return $this->hasOne(Property::class, 'property_id', 'idrec');
    }
}
