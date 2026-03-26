<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * TicketCategory model — lookup table for ticket type + category combos.
 * Each row defines a valid combination (e.g., complaint/wifi) with routing
 * info (front_desk or hq) and whether a booking is required.
 */
class TicketCategory extends Model
{
    protected $table = 't_ticket_categories';

    protected $fillable = [
        'ticket_type', 'category', 'label_en', 'label_id', 'label_zh',
        'recipient_type', 'requires_booking', 'sort_order', 'status',
    ];

    protected $casts = [
        'requires_booking' => 'boolean',
        'status' => 'boolean',
    ];

    /** Get all active categories, ordered by sort_order */
    public function scopeActive($query)
    {
        return $query->where('status', 1)->orderBy('sort_order');
    }

    /** Filter by ticket type (booking, complaint, suggestion) */
    public function scopeByType($query, string $type)
    {
        return $query->where('ticket_type', $type);
    }

    /** Filter by recipient type (front_desk, hq) */
    public function scopeByRecipient($query, string $recipientType)
    {
        return $query->where('recipient_type', $recipientType);
    }

    /** Get all tickets in this category */
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'category_id');
    }

    /** Get the localized label based on current app locale */
    public function getLocalizedLabelAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "label_{$locale}";
        return $this->{$field} ?? $this->label_en;
    }
}
