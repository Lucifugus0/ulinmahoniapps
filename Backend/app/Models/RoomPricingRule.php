<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * <!-- Multi-Tier Pricing: Model for m_room_pricing_rules table -->
 * <!-- Stores admin-configured pricing rules (weekday, weekend, holiday, high_season, low_season) per room -->
 * <!-- The RoomPriceGeneratorService resolves these rules into per-date prices in m_room_prices -->
 */
class RoomPricingRule extends Model
{
    protected $table = 'm_room_pricing_rules';
    protected $primaryKey = 'idrec';
    public $incrementing = true;

    protected $fillable = [
        'room_id',
        'rule_type',
        'price',
        'date_start',
        'date_end',
        'label',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'date_start' => 'date',
        'date_end' => 'date',
    ];

    /**
     * <!-- Relationship: each pricing rule belongs to a room -->
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    /**
     * <!-- Scope: filter by rule type (weekday, weekend, holiday, high_season, low_season) -->
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('rule_type', $type);
    }

    /**
     * <!-- Scope: filter only active rules -->
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * <!-- Scope: find date-based rules (holiday/season) that overlap with a given date -->
     */
    public function scopeContainingDate($query, string $date)
    {
        return $query->where('date_start', '<=', $date)
                     ->where('date_end', '>=', $date);
    }
}
