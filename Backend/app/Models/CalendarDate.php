<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Global calendar date classification model.
 * Stores individual dates with their type (holiday, high_season, low_season).
 * Applied uniformly to ALL rooms — no per-room date overrides.
 */
class CalendarDate extends Model
{
    protected $table = 'm_calendar_dates';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'date',
        'date_type',
        'label',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        // <!-- Serialize as plain Y-m-d (no UTC conversion). The default 'date' cast
        //      serializes via toJSON() in UTC; with app timezone Asia/Jakarta (UTC+7)
        //      that turns 2026-05-26 00:00 into 2026-05-25T17:00:00Z, so the calendar
        //      JS (e.date.substring(0,10)) rendered every entry one day early. The
        //      :Y-m-d format makes Laravel emit the date in its own timezone. -->
        'date' => 'date:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Scope: only active entries */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /** Scope: filter by date type */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('date_type', $type);
    }

    /** Scope: entries within a date range */
    public function scopeForDateRange(Builder $query, $from, $to): Builder
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /** Relationship: user who created this entry */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Relationship: user who last updated this entry */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
