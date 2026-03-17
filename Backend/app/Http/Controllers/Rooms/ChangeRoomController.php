<?php

namespace App\Http\Controllers\Rooms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ChangeRoomController extends Controller
{
    /**
     * Display the change room interface.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');
        $historySearch = $request->input('history_search');

        // Set property_id based on user_type
        if ($user->user_type == 1) {
            $propertyId = $user->property_id;
        } else {
            $propertyId = $request->input('property_id');
        }

        // Get active bookings that are eligible for room transfer
        // Must be: active, paid, and not checked out yet
        $bookings = Booking::with(['user', 'room', 'property', 'transaction', 'payment', 'previousBooking'])
            ->when($propertyId, function ($query, $propertyId) {
                return $query->where('property_id', $propertyId);
            })
            ->whereHas('payment', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->where('status', 1) // Only active bookings
            ->whereNull('check_out_at') // Not checked out yet
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('username', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByRaw('check_in_at IS NULL DESC, check_in_at DESC')
            ->paginate(3);

        // Check if this is an AJAX request for live search
        if ($request->ajax() || $request->get('ajax')) {
            $html = view('pages.rooms.changerooms.partials.changeRoom_table', [
                'bookings' => $bookings,
                'per_page' => request('per_page', 8),
            ])->render();

            $pagination = $bookings->withQueryString()->links()->render();

            return response()->json([
                'html' => $html,
                'pagination' => $pagination
            ]);
        }

        // Get transfer history - bookings that have been transferred (have previous_booking_id)
        // Group by order_id to show chain history
        $transferHistory = $this->getTransferHistory($propertyId, $historySearch);

        return view('pages.rooms.changerooms.index', compact(
            'bookings',
            'search',
            'transferHistory',
            'historySearch'
        ));
    }

    /**
     * Get transfer history grouped by order_id with chain visualization.
     */
    private function getTransferHistory($propertyId = null, $search = null)
    {
        // Get distinct order_ids that have room changes
        $orderIds = Booking::when($propertyId, function ($query, $propertyId) {
                return $query->where('property_id', $propertyId);
            })
            ->whereNotNull('previous_booking_id')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('order_id', 'like', '%' . $search . '%')
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('username', 'like', '%' . $search . '%');
                        });
                });
            })
            ->select('order_id')
            ->distinct()
            ->pluck('order_id');

        // Build history data with full chain for each order
        $history = collect();

        foreach ($orderIds as $orderId) {
            $bookings = Booking::with(['room', 'property', 'user', 'roomChangedByUser'])
                ->where('order_id', $orderId)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($bookings->isEmpty()) continue;

            $activeBooking = $bookings->where('status', 1)->first();
            $firstBooking = $bookings->first();

            $history->push([
                'order_id' => $orderId,
                'guest_name' => $firstBooking->user->username ?? 'N/A',
                'property' => $firstBooking->property,
                'chain' => $bookings->values(), // All bookings including original as starting point
                'active_booking' => $activeBooking,
                'transfer_count' => $bookings->count() - 1, // Exclude original booking
                'last_transfer_at' => $activeBooking?->room_changed_at ?? $activeBooking?->created_at,
            ]);
        }

        // Sort by last transfer date descending, exclude entries with no actual transfers
        return $history->filter(fn($h) => $h['transfer_count'] > 0)
            ->sortByDesc('last_transfer_at')
            ->values();
    }

    /**
     * Get available rooms for transfer based on booking dates.
     */
    public function getAvailableRooms(Request $request)
    {
        $user = Auth::user();
        $roomId = $request->input('room_id');
        $checkIn = $request->input('check_in');
        $checkOut = $request->input('check_out');
        $reason = $request->input('reason');

        // Set property_id based on user_type
        if ($user->user_type == 1) {
            $propertyId = $user->property_id;
        } else {
            $propertyId = $request->input('property_id');
        }

        if (!$checkIn || !$checkOut || !$propertyId) {
            return response()->json(['error' => 'Missing required fields.'], 400);
        }

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);

        // Get current room to filter by same room name (type)
        $currentRoomName = null;
        if ($roomId) {
            $currentRoom = Room::find($roomId);
            $currentRoomName = $currentRoom?->name;
        }

        // Get rooms that are active and available
        // status = 1 means room is active (not disabled)
        // rental_status = 0 means available, rental_status = 1 means booked (monthly/long-term)
        $rooms = Room::where('property_id', $propertyId)
            ->where('status', 1)
            ->where('rental_status', 0)
            ->when($currentRoomName, function ($query) use ($currentRoomName) {
                return $query->where('name', $currentRoomName);
            })
            ->when($roomId, function ($query) use ($roomId) {
                return $query->where('idrec', '!=', $roomId);
            })
            ->get();

        // Get order_id of active booking for current room to exclude from conflict check
        $currentOrderId = $roomId
            ? Booking::where('room_id', $roomId)->where('status', 1)->value('order_id')
            : null;

        // Filter rooms that don't have booking conflicts
        $availableRooms = $rooms->filter(function ($room) use ($checkInDate, $checkOutDate, $currentOrderId) {
            $hasConflict = $this->checkRoomConflict($room->idrec, $checkInDate, $checkOutDate, $currentOrderId);
            return !$hasConflict;
        });

        return response()->json($availableRooms->values());
    }

    /**
     * Process room transfer.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_property_id' => 'required|string|max:100',
            'order_id' => 'required|string|max:100',
            'new_room' => 'required|string|max:255',
            'check_in' => 'required|date',
            'check_out' => 'nullable|date',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fill all required fields.');
        }

        $authUser = Auth::user();

        // Validate Site user can only transfer rooms within their property
        if ($authUser->user_type == 1 && $authUser->property_id != $request->current_property_id) {
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses untuk memindahkan kamar di properti ini.');
        }

        try {
            // Get the current active booking
            $currentBooking = Booking::where('order_id', $request->order_id)
                ->where('status', 1)
                ->whereNull('check_out_at')
                ->firstOrFail();

            // Validate that new room is different from current room
            if ($currentBooking->room_id == $request->new_room) {
                return redirect()->back()
                    ->with('error', 'Kamar baru tidak boleh sama dengan kamar saat ini.');
            }

            // Get room details
            $currentRoom = Room::findOrFail($currentBooking->room_id);
            $newRoom = Room::findOrFail($request->new_room);

            // Validate new room belongs to same property
            if ($newRoom->property_id != $request->current_property_id) {
                return redirect()->back()
                    ->with('error', 'Kamar baru harus dalam properti yang sama.');
            }

            // Validate new room has the same name/type as current room
            if ($newRoom->name !== $currentRoom->name) {
                return redirect()->back()
                    ->with('error', 'Kamar baru harus bertipe sama dengan kamar saat ini (' . $currentRoom->name . ').');
            }

            // Validate new room is available
            if ($newRoom->status != 1 || $newRoom->rental_status != 0) {
                return redirect()->back()
                    ->with('error', 'Kamar yang dipilih tidak tersedia.');
            }

            // Use dates from booking record (not from request) to prevent tampering
            $checkInDate = $currentBooking->check_in_at ?? Carbon::parse($currentBooking->transaction?->check_in);
            $checkOutDate = $currentBooking->check_out_at ?? Carbon::parse($currentBooking->transaction?->check_out);

            $hasConflict = $this->checkRoomConflict($request->new_room, $checkInDate, $checkOutDate, $currentBooking->order_id);

            if ($hasConflict) {
                return redirect()->back()
                    ->with('error', 'Kamar yang dipilih sudah dipesan untuk tanggal tersebut.');
            }

            // Create new booking record for the transfer
            $newBooking = Booking::create([
                'property_id' => $request->current_property_id,
                'order_id' => $request->order_id,
                'room_id' => $request->new_room,
                'user_id' => $currentBooking->user_id ?? null,
                'user_name' => $currentBooking->user_name,
                'user_email' => $currentBooking->user_email,
                'user_phone_number' => $currentBooking->user_phone_number,
                'check_in_at' => $currentBooking->check_in_at,
                'check_out_at' => $currentBooking->check_out_at,
                'doc_type' => $currentBooking->doc_type,
                'doc_path' => $currentBooking->doc_path,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'status' => 1,
                'previous_booking_id' => $currentBooking->idrec,
                'reason' => $request->reason,
                'description' => $request->notes,
                'room_changed_at' => now(),
                'room_changed_by' => Auth::id(),
                'is_printed' => 0,
            ]);

            // Deactivate the current booking
            $currentBooking->update([
                'status' => 0,
                'updated_by' => Auth::id(),
            ]);

            // Old room: free (guest has moved out)
            $currentRoom->update(['rental_status' => 0]);

            // New room: mark as occupied
            $newRoom->update(['rental_status' => 1]);

            return redirect()->route('changerooom.index')
                ->with('success', 'Kamar berhasil dipindahkan!');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Booking tidak ditemukan atau tidak dapat dipindahkan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memindahkan kamar: ' . $e->getMessage());
        }
    }

    /**
     * Rollback to previous room.
     */
    public function rollback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Data tidak valid.');
        }

        $authUser = Auth::user();

        try {
            // Get the current active booking
            $currentBooking = Booking::with(['previousBooking', 'room', 'property'])
                ->where('idrec', $request->booking_id)
                ->where('status', 1)
                ->firstOrFail();

            // Check if there's a previous booking to rollback to
            if (!$currentBooking->previous_booking_id) {
                return redirect()->back()
                    ->with('error', 'Tidak ada kamar sebelumnya untuk di-rollback.');
            }

            $previousBooking = $currentBooking->previousBooking;

            if (!$previousBooking) {
                return redirect()->back()
                    ->with('error', 'Data booking sebelumnya tidak ditemukan.');
            }

            // Validate Site user can only rollback rooms within their property
            if ($authUser->user_type == 1 && $authUser->property_id != $currentBooking->property_id) {
                return redirect()->back()
                    ->with('error', 'Anda tidak memiliki akses untuk rollback kamar di properti ini.');
            }

            // Check if the previous room is still available
            // status = 1 means active, rental_status = 0 means available (not long-term booked)
            $previousRoom = Room::find($previousBooking->room_id);

            if (!$previousRoom || $previousRoom->status != 1 || $previousRoom->rental_status != 0) {
                return redirect()->back()
                    ->with('error', 'Kamar sebelumnya tidak lagi tersedia atau sudah terbooking.');
            }

            // Check for conflicts on the previous room
            $checkInDate = $currentBooking->check_in_at ?? Carbon::parse($currentBooking->transaction->check_in);
            $checkOutDate = $currentBooking->check_out_at ?? Carbon::parse($currentBooking->transaction->check_out);

            $hasConflict = $this->checkRoomConflict($previousRoom->idrec, $checkInDate, $checkOutDate, $currentBooking->order_id);

            if ($hasConflict) {
                return redirect()->back()
                    ->with('error', 'Kamar sebelumnya sudah dipesan untuk tanggal tersebut.');
            }

            $currentRoom = $currentBooking->room;

            // Create new booking record for the rollback
            $rollbackBooking = Booking::create([
                'property_id' => $currentBooking->property_id,
                'order_id' => $currentBooking->order_id,
                'room_id' => $previousRoom->idrec,
                'user_name' => $currentBooking->user_name,
                'user_email' => $currentBooking->user_email,
                'user_phone_number' => $currentBooking->user_phone_number,
                'check_in_at' => $currentBooking->check_in_at,
                'check_out_at' => $currentBooking->check_out_at,
                'doc_type' => $currentBooking->doc_type,
                'doc_path' => $currentBooking->doc_path,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
                'status' => 1,
                'previous_booking_id' => $currentBooking->idrec,
                'reason' => 'rollback',
                'description' => $request->notes,
                'room_changed_at' => now(),
                'room_changed_by' => Auth::id(),
                'is_printed' => 0,
            ]);

            // Deactivate current booking
            $currentBooking->update([
                'status' => 0,
                'updated_by' => Auth::id(),
            ]);

            // Current room: free (guest has moved out)
            $currentRoom->update(['rental_status' => 0]);

            // Previous room (rollback target): mark as occupied
            $previousRoom->update(['rental_status' => 1]);

            return redirect()->route('changerooom.index')
                ->with('success', 'Berhasil rollback ke kamar sebelumnya: ' . $previousRoom->name);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()
                ->with('error', 'Booking tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal rollback: ' . $e->getMessage());
        }
    }

    /**
     * Get room change chain for a specific order.
     */
    public function getChain(Request $request)
    {
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json(['error' => 'Order ID required'], 400);
        }

        $bookings = Booking::with(['room', 'roomChangedByUser'])
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($booking) {
                return [
                    'idrec' => $booking->idrec,
                    'room_id' => $booking->room_id,
                    'room_name' => $booking->room->name ?? 'N/A',
                    'room_no' => $booking->room->no ?? 'N/A',
                    'status' => $booking->status,
                    'reason' => $booking->reason,
                    'reason_label' => $this->getReasonLabel($booking->reason),
                    'description' => $booking->description,
                    'room_changed_at' => $booking->room_changed_at?->format('d M Y H:i'),
                    'room_changed_by' => $booking->roomChangedByUser->username ?? null,
                    'created_at' => $booking->created_at->format('d M Y H:i'),
                ];
            });

        return response()->json([
            'order_id' => $orderId,
            'chain' => $bookings,
            'transfer_count' => $bookings->count() - 1,
        ]);
    }

    /**
     * Check room availability for rollback.
     */
    public function checkRollbackAvailability(Request $request)
    {
        $bookingId = $request->input('booking_id');

        $booking = Booking::with('previousBooking')
            ->where('idrec', $bookingId)
            ->where('status', 1)
            ->first();

        if (!$booking || !$booking->previous_booking_id) {
            return response()->json([
                'available' => false,
                'message' => 'Tidak ada kamar sebelumnya untuk di-rollback.'
            ]);
        }

        $previousRoom = Room::find($booking->previousBooking->room_id);

        if (!$previousRoom || $previousRoom->status != 1 || $previousRoom->rental_status != 0) {
            return response()->json([
                'available' => false,
                'message' => 'Kamar sebelumnya tidak lagi tersedia atau sudah terbooking.',
                'room' => $previousRoom ? [
                    'name' => $previousRoom->name,
                    'no' => $previousRoom->no,
                ] : null
            ]);
        }

        // Check for conflicts
        $checkInDate = $booking->check_in_at ?? Carbon::parse($booking->transaction->check_in);
        $checkOutDate = $booking->check_out_at ?? Carbon::parse($booking->transaction->check_out);

        $hasConflict = $this->checkRoomConflict($previousRoom->idrec, $checkInDate, $checkOutDate, $booking->order_id);

        return response()->json([
            'available' => !$hasConflict,
            'message' => $hasConflict ? 'Kamar sebelumnya sudah dipesan untuk tanggal tersebut.' : 'Kamar tersedia untuk rollback.',
            'room' => [
                'idrec' => $previousRoom->idrec,
                'name' => $previousRoom->name,
                'no' => $previousRoom->no,
            ]
        ]);
    }

    /**
     * Check if a room has booking conflicts for given dates.
     */
    private function checkRoomConflict($roomId, Carbon $checkInDate, Carbon $checkOutDate, $excludeOrderId = null)
    {
        return Booking::where('room_id', $roomId)
            ->where('status', 1)
            ->when($excludeOrderId, function ($query, $excludeOrderId) {
                return $query->where('order_id', '!=', $excludeOrderId);
            })
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->where(function ($q1) use ($checkInDate, $checkOutDate) {
                    $q1->where('check_in_at', '>=', $checkInDate)
                       ->where('check_in_at', '<', $checkOutDate);
                })
                ->orWhere(function ($q2) use ($checkInDate, $checkOutDate) {
                    $q2->where('check_out_at', '>', $checkInDate)
                       ->where('check_out_at', '<=', $checkOutDate);
                })
                ->orWhere(function ($q3) use ($checkInDate, $checkOutDate) {
                    $q3->where('check_in_at', '<=', $checkInDate)
                       ->where('check_out_at', '>=', $checkOutDate);
                });
            })
            ->exists();
    }

    /**
     * Get human-readable reason label.
     */
    private function getReasonLabel($reason)
    {
        $labels = [
            'maintenance' => 'Maintenance/Perawatan',
            'upgrade' => 'Upgrade Kamar',
            'downgrade' => 'Downgrade Kamar',
            'guest_request' => 'Permintaan Tamu',
            'rollback' => 'Rollback',
            'other' => 'Lainnya',
        ];

        return $labels[$reason] ?? $reason ?? 'N/A';
    }
}
