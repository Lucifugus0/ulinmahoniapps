<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MRouter extends Model
{
    use HasFactory;

    protected $table = 'm_router'; 
    protected $primaryKey = 'idrouter'; 
    public $incrementing = false; 
    protected $keyType = 'string'; 

    protected $fillable = [
        'idrouter',
        'serialno',
        'room_id',
        'username',
        'password',
        'ip',
        'api_port',
        'web_post',
        'type',
    ];

    public function wifi()
    {
        return $this->hasMany(TWifi::class, 'idrouter', 'idrouter');
    }
}
