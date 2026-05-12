<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Parking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_parking';
    protected $primaryKey = 'idrec';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'property_id',
        'parking_type',
        'vehicle_plate',
        'owner_name',
        'owner_phone',
        'user_id',
        'order_id',
        'parking_duration',
        'start_rent',
        'end_rent',
        'fee_amount',
        'notes',
        'status',
        'management_only',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'start_rent'  => 'date',
        'end_rent'    => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transactions()
    {
        return $this->hasMany(ParkingFeeTransaction::class, 'parking_id', 'idrec');
    }

    /**
     * The latest paid parking fee transaction for this parking row — used to surface
     * the active invoice number on the Parking Management page. Returns null when
     * the parking was created via the Frontend booking flow (no separate parking
     * payment row); the caller should then fall back to the room booking's invoice.
     */
    public function latestPaidTransaction()
    {
        return $this->hasOne(ParkingFeeTransaction::class, 'parking_id', 'idrec')
            ->where('transaction_status', 'paid')
            ->orderByDesc('idrec');
    }

    /**
     * Resolved invoice number for display: prefer the latest paid parking fee
     * transaction's invoice_id; fall back to the linked booking transaction's
     * invoice_number; null if neither exists. Both relationships must be eager-loaded.
     */
    public function getInvoiceDisplayAttribute(): ?string
    {
        return $this->latestPaidTransaction?->invoice_id
            ?? $this->bookingTransaction?->invoice_number
            ?? null;
    }

    /**
     * Where the displayed invoice number came from — drives the label under the
     * invoice column on the Parking Management page. Returns:
     *   'addon'   — standalone parking payment via Finance > Parking Entry (t_parking_fee_transaction)
     *   'bundled' — parking paid as part of a room booking transaction (t_transactions)
     *   null      — no paid invoice associated yet (pending / unpaid / legacy)
     */
    public function getInvoiceSourceAttribute(): ?string
    {
        if ($this->latestPaidTransaction?->invoice_id) {
            return 'addon';
        }
        if ($this->bookingTransaction?->invoice_number) {
            return 'bundled';
        }
        return null;
    }

    public function bookingTransaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    /**
     * The currently-active t_booking row for this parking's order_id.
     * Used to look up the room number on the Parking Management page —
     * a booking chain may have multiple t_booking rows (renewal clones,
     * modify-booking clones), so we filter to status=1 to pick the live one.
     */
    public function activeBooking()
    {
        return $this->hasOne(\App\Models\Booking::class, 'order_id', 'order_id')
            ->where('status', 1);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the parking fee configuration for this parking's property + type.
     */
    public function getParkingFeeAttribute()
    {
        return ParkingFee::where('property_id', $this->property_id)
            ->where('parking_type', $this->parking_type)
            ->where('status', 1)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    /**
     * "01 Mar 2026 → 01 Jun 2026" — null-safe display label for the current rental period.
     * Falls back to "—" when either date is missing (legacy / malformed rows).
     */
    public function getRentPeriodLabelAttribute(): string
    {
        if (!$this->start_rent || !$this->end_rent) {
            return '—';
        }
        return $this->start_rent->format('d M Y') . ' → ' . $this->end_rent->format('d M Y');
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('vehicle_plate', 'like', "%{$search}%")
              ->orWhere('owner_name', 'like', "%{$search}%")
              ->orWhereHas('property', function ($q2) use ($search) {
                  $q2->where('name', 'like', "%{$search}%");
              });
        });
    }
}
