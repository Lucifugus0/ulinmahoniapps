<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        /* Use production DB on staging to read live device tokens;
           falls back to default connection on production and local */
        if (env('DB_PROD_DATABASE')) {
            $this->connection = 'production_mysql';
        }
    }

    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'device_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
