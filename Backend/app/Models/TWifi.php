<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TWifi extends Model
{
    use HasFactory;

    protected $table = 't_wifi';
    protected $primaryKey = 'idrec';
    protected $fillable = [
        'idrouter',
        'first_name',
        'last_name',
        'check_out_at',
        'password',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($wifi) {
            $wifi->password = $wifi->password ?? substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        });
    }
}
