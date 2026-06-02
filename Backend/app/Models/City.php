<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class City extends Model
{
    use HasFactory;

    protected $table = 'm_cities';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'city_name',
        'province',
        'slug',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Auto-generate slug from city_name when creating a new city.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($city) {
            if (empty($city->slug)) {
                $city->slug = Str::slug($city->city_name);
            }
        });

        static::updating(function ($city) {
            if ($city->isDirty('city_name') && !$city->isDirty('slug')) {
                $city->slug = Str::slug($city->city_name);
            }
        });
    }

    /**
     * Scope: only active cities (status = 1).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '1');
    }

    /**
     * Scope: search by city_name or province.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('city_name', 'like', "%{$search}%")
              ->orWhere('province', 'like', "%{$search}%");
        });
    }

    /**
     * Relationship: user who created this city record.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: user who last updated this city record.
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
