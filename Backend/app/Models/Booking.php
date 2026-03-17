<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\Transaction;
use App\Models\Room;

class Booking extends Model
{
    use HasFactory;

    protected $table = 't_booking';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'order_id',
        'room_id',
        'user_name',
        'user_email',
        'user_phone_number',
        'property_id',
        'check_in_at',
        'doc_type',
        'doc_path',
        'check_out_at',
        'created_by',
        'updated_by',
        'status',
        'previous_booking_id',
        'reason',
        'description',
        'room_changed_at',
        'room_changed_by',
        'is_printed',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'room_changed_at' => 'datetime',
        'status' => 'integer',
    ];

    protected $dates = [
        'check_in_at',
        'check_out_at',
        'room_changed_at',
        'created_at',
        'updated_at',
    ];

    // ==================== RELATIONSHIPS ====================

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'idrec');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'order_id', 'order_id');
    }

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Transaction::class,
            'order_id',      // Foreign key di Transaction
            'id',            // Foreign key di User
            'order_id',      // Local key di Booking
            'user_id'        // Local key di Transaction
        );
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    public function refund()
    {
        return $this->hasOne(Refund::class, 'id_booking', 'order_id');
    }

    public function itemConditions()
    {
        return $this->hasMany(RoomItemCondition::class, 'order_id', 'order_id');
    }

    /**
     * Get the previous booking in the room change chain.
     * This is the booking that was replaced when moving to current room.
     */
    public function previousBooking()
    {
        return $this->belongsTo(Booking::class, 'previous_booking_id', 'idrec');
    }

    /**
     * Get the next booking(s) that replaced this booking.
     * Usually only one, but could be multiple in edge cases.
     */
    public function nextBookings()
    {
        return $this->hasMany(Booking::class, 'previous_booking_id', 'idrec');
    }

    /**
     * Get the user who processed the room change.
     */
    public function roomChangedByUser()
    {
        return $this->belongsTo(User::class, 'room_changed_by', 'id');
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the booking status based on transaction status.
     * This is a computed status, not from database column.
     */
    public function getStatusAttribute()
    {
        // Jika tidak ada transaksi terkait
        if (!$this->transaction) {
            return 'Unknown';
        }

        // Pastikan order_id match
        if ($this->order_id !== $this->transaction->order_id) {
            return 'Order ID Mismatch';
        }

        switch ($this->transaction->transaction_status) {
            case 'pending':
                return 'Waiting For Payment';
            case 'waiting':
                return 'Waiting For Confirmation';
            case 'paid':
                if (is_null($this->check_in_at) && is_null($this->check_out_at)) {
                    return 'Waiting For Check-In';
                } elseif (!is_null($this->check_in_at) && is_null($this->check_out_at)) {
                    return 'Checked-In';
                } elseif (!is_null($this->check_in_at) && !is_null($this->check_out_at)) {
                    return 'Checked-Out';
                }
                break;
            case 'canceled':
            case 'cancelled':
                return 'Canceled';
            case 'expired':
                return 'Expired';
            case 'failed':
                return 'Payment Failed';
        }

        return 'Unknown';
    }

    /**
     * Get the number of times this booking has been transferred.
     * Counts all transfer records (previous_booking_id not null) for the same order_id.
     */
    public function getTransferCountAttribute()
    {
        return static::where('order_id', $this->order_id)
            ->whereNotNull('previous_booking_id')
            ->count();
    }

    /**
     * Check if this booking has been transferred (has room change history).
     */
    public function getHasBeenTransferredAttribute()
    {
        return $this->previous_booking_id !== null;
    }

    /**
     * Check if this booking can be transferred.
     * Must be active and not checked out.
     */
    public function getCanBeTransferredAttribute()
    {
        return $this->getRawOriginal('status') == 1 && is_null($this->check_out_at);
    }

    /**
     * Get the full chain of room changes for this order.
     * Returns array from original booking to current.
     */
    public function getRoomChangeChainAttribute()
    {
        $chain = collect();

        // First, find the original booking (one without previous_booking_id)
        $originalBooking = $this->getOriginalBooking();

        if ($originalBooking) {
            $chain->push($originalBooking);

            // Then traverse forward through nextBookings
            $current = $originalBooking;
            while ($current->nextBookings->isNotEmpty()) {
                $next = $current->nextBookings->first();
                $chain->push($next);
                $current = $next;
            }
        }

        return $chain;
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get the original booking in the chain (first booking without previous_booking_id).
     */
    public function getOriginalBooking()
    {
        $booking = $this;

        while ($booking->previous_booking_id) {
            $prev = $booking->previousBooking;
            if (!$prev) break;
            $booking = $prev;
        }

        return $booking;
    }

    /**
     * Get the current active booking for this order_id.
     */
    public static function getActiveBookingByOrderId($orderId)
    {
        return static::where('order_id', $orderId)
            ->where('status', 1)
            ->first();
    }

    /**
     * Get all bookings in the room change chain for an order.
     */
    public static function getBookingChainByOrderId($orderId)
    {
        return static::where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Scope to get only active bookings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope to get only inactive (transferred) bookings.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 0);
    }

    /**
     * Scope to get bookings that can be transferred.
     */
    public function scopeTransferable($query)
    {
        return $query->where('status', 1)
            ->whereNull('check_out_at');
    }

    // ==================== INVOICE DATA ====================

    public function getInvoiceData()
    {
        return [
            'transaction_code' => $this->transaction->transaction_code ?? 'N/A',
            'transaction_date' => $this->transaction->transaction_date ?? now(),
            'user_name' => $this->transaction->user_name ?? $this->user_name ?? 'N/A',
            'user_phone' => $this->transaction->user_phone_number ?? $this->user_phone_number ?? 'N/A',
            'user_email' => $this->transaction->user_email ?? $this->user_email ?? 'N/A',
            'property_type' => $this->transaction->property_type ?? $this->property->type ?? 'N/A',
            'check_in' => $this->transaction->check_in ?? $this->check_in_at,
            'check_out' => $this->transaction->check_out ?? $this->check_out_at,
            'room_name' => $this->transaction->room_name ?? $this->room->name ?? 'N/A',
            'booking_days' => $this->transaction->booking_days ?? 0,
            'booking_months' => $this->transaction->booking_months ?? 0,
            'booking_type' => $this->transaction->booking_type ?? 'daily',
            'room_price' => $this->transaction->room_price ?? 0,
            'admin_fees' => $this->transaction->admin_fees ?? 0,
            'grandtotal' => $this->transaction->grandtotal_price ?? 0,
            'status' => $this->transaction->transaction_status ?? 'pending'
        ];
    }
}
