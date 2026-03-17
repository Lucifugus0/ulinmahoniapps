<?php

namespace App\Http\Controllers\RoomAvailability;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Room;
use App\Models\Booking;

class RoomAvailabilityController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 8);
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query untuk room availability
        $rooms = Room::where('status', 1)->with(['property', 'thumbnail', 'bookings' => function ($query) use ($startDate, $endDate) {
            // Hanya ambil booking aktif (status=1) dengan status paid
            // status=0 berarti booking lama dari pindah kamar, tidak perlu ditampilkan
            $query->where('status', 1)
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->with(['user', 'transaction', 'payment']);

            // Filter berdasarkan tanggal menggunakan transaction dates untuk konsistensi
            // Logika overlap: booking overlap jika check_in < endDate DAN check_out > startDate
            if ($startDate && $endDate) {
                $query->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                    $q->where('check_in', '<', $endDate)
                      ->where('check_out', '>', $startDate);
                });
            }
        }]);

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $rooms->where('property_id', $user->property_id);
        }

        $rooms = $rooms->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('no', 'like', "%{$search}%")
                        ->orWhereHas('property', function ($q2) use ($search) {
                            $q2->where('property_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'available') {
                    $query->where('rental_status', 0);
                } elseif ($status === 'booked') {
                    $query->where('rental_status', 1);
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        // Jika request AJAX, return JSON
        if ($request->ajax()) {
            return response()->json([
                'html' => view('pages.room_availability.partials.roomAvailability_table', compact('rooms'))->render(),
                'pagination' => $rooms->links()->toHtml()
            ]);
        }

        return view('pages.room_availability.index', compact('rooms'));
    }

    public function getRoomBookings($roomId, Request $request)
    {
        $room = Room::with(['property'])->findOrFail($roomId);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Query untuk booking dengan status paid
        // Hanya tampilkan booking aktif (status = 1) - booking lama dari room change diabaikan
        $bookingsQuery = Booking::where('room_id', $roomId)
            ->where('status', 1)
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->whereHas('transaction', function ($q) {
                $q->where('transaction_status', 'paid');
            })
            ->with(['user', 'transaction', 'payment']);

        // Filter berdasarkan overlap tanggal
        // Logika overlap: booking overlap jika check_in < endDate DAN check_out > startDate
        if ($startDate && $endDate) {
            $bookingsQuery->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->where('check_in', '<', $endDate)
                  ->where('check_out', '>', $startDate);
            });
        }

        $bookings = $bookingsQuery->get();

        $formattedBookings = $bookings->map(function ($booking) {

            if (!$booking->transaction) {
                return null;
            }

            $checkIn = new \DateTime($booking->transaction->check_in);
            $checkOut = new \DateTime($booking->transaction->check_out);

            $durationText = '';
            if ($booking->transaction->booking_type === 'monthly') {

                $months = $booking->transaction->booking_months ?? 0;
                $durationText = $months . ($months == 1 ? ' bulan' : ' bulan');
            } elseif ($booking->transaction->booking_type === 'daily') {

                $days = $booking->transaction->booking_days ?? $checkIn->diff($checkOut)->days;
                $durationText = $days . ($days == 1 ? ' malam' : ' malam');
            } else {

                $days = $checkIn->diff($checkOut)->days;
                $durationText = $days . ($days == 1 ? ' malam' : ' malam');
            }

            $userName = 'N/A';
            if ($booking->user && $booking->user->first_name) {
                $userName = $booking->user->first_name;
            } elseif ($booking->transaction->user_name) {
                $userName = $booking->transaction->user_name;
            } elseif ($booking->user_name) {
                $userName = $booking->user_name;
            }

            $userEmail = 'N/A';
            if ($booking->user && $booking->user->email) {
                $userEmail = $booking->user->email;
            } elseif ($booking->transaction->user_email) {
                $userEmail = $booking->transaction->user_email;
            } elseif ($booking->user_email) {
                $userEmail = $booking->user_email;
            }
            return [
                'id' => $booking->idrec,
                'booking_code' => $booking->order_id,
                'user_name' => $userName,
                'user_email' => $userEmail,
                'check_in' => $booking->transaction->check_in,
                'check_out' => $booking->transaction->check_out,
                'booking_type' => $booking->transaction->booking_type ?? 'daily',
                'duration' => $durationText,
                'total_amount' => $booking->payment->grandtotal_price ?? 0,
                'payment_status' => $booking->payment->payment_status ?? 'pending',
                'status' => $booking->status,
                'status_badge' => $this->getStatusBadgeFromText($booking->status),
                'created_at' => $booking->created_at->format('d M Y H:i'),
                // is_renewal=1 berarti perpanjangan dari booking sebelumnya (user sama)
                'is_renewal' => $booking->transaction->is_renewal == 1,
                // is_room_changed=true berarti booking ini hasil pindah kamar (previous_booking_id ada & room_changed_at ada)
                'is_room_changed' => !is_null($booking->previous_booking_id) && !is_null($booking->room_changed_at),
            ];
        })->filter();

        // Hitung penyewa unik: user_id sama = perpanjangan = 1 penyewa
        $uniqueTenantCount = $bookings
            ->groupBy(function ($booking) {
                return $booking->transaction->user_id
                    ?? $booking->user_id
                    ?? $booking->order_id;
            })
            ->count();

        return response()->json([
            'success' => true,
            'room_name' => $room->name . ' - ' . ($room->property->property_name ?? ''),
            'bookings' => $formattedBookings,
            'total_bookings' => $uniqueTenantCount,
        ]);
    }

    // Badge warna berdasarkan status text (computed accessor)
    private function getStatusBadgeFromText($statusText)
    {
        $badges = [
            'Waiting For Payment' => 'bg-yellow-100 text-yellow-800',
            'Waiting For Confirmation' => 'bg-orange-100 text-orange-800',
            'Waiting For Check-In' => 'bg-blue-100 text-blue-800',
            'Checked-In' => 'bg-green-100 text-green-800',
            'Checked-Out' => 'bg-gray-100 text-gray-800',
            'Canceled' => 'bg-red-100 text-red-800',
            'Expired' => 'bg-red-100 text-red-800',
            'Payment Failed' => 'bg-red-100 text-red-800',
        ];

        return $badges[$statusText] ?? 'bg-gray-100 text-gray-800';
    }

    // Status text (deprecated - kept for backward compatibility)
    private function getStatusText($status)
    {
        $statuses = [
            0 => 'Pending',
            1 => 'Confirmed',
            2 => 'Checked In',
            3 => 'Checked Out',
            4 => 'Cancelled'
        ];

        return $statuses[$status] ?? 'Unknown';
    }

    // Badge warna (deprecated - kept for backward compatibility)
    private function getStatusBadge($status)
    {
        $badges = [
            0 => 'bg-yellow-100 text-yellow-800',
            1 => 'bg-green-100 text-green-800',
            2 => 'bg-blue-100 text-blue-800',
            3 => 'bg-gray-100 text-gray-800',
            4 => 'bg-red-100 text-red-800'
        ];

        return $badges[$status] ?? 'bg-gray-100 text-gray-800';
    }


    public function getAvailabilityData(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $search = $request->get('search');
        $status = $request->get('status', 'all');

        // Query yang sama dengan index untuk konsistensi data
        $roomsQuery = Room::where('status', 1);

        // Filter by property_id for site users
        $user = Auth::user();
        if ($user && $user->isSiteRole() && $user->property_id) {
            $roomsQuery->where('property_id', $user->property_id);
        }

        $roomsQuery = $roomsQuery->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('no', 'like', "%{$search}%")
                    ->orWhereHas('property', function ($q2) use ($search) {
                        $q2->where('property_name', 'like', "%{$search}%");
                    });
            });
        })
            ->when($status !== 'all', function ($query) use ($status) {
                if ($status === 'available') {
                    $query->where('rental_status', 0);
                } elseif ($status === 'booked') {
                    $query->where('rental_status', 1);
                }
            });

        $totalRooms = $roomsQuery->count();
        $availableRooms = $roomsQuery->clone()->where('rental_status', 0)->count();
        $bookedRooms = $roomsQuery->clone()->where('rental_status', 1)->count();

        return response()->json([
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRooms,
            'booked_rooms' => $bookedRooms
        ]);
    }

    public function updateRentalStatus(Request $request, $id)
    {
        $request->validate([
            'rental_status' => 'required|in:0,1'
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'rental_status' => $request->rental_status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status kamar berhasil diupdate',
            'rental_status' => $room->rental_status,
            'status_text' => $room->rental_status == 1 ? 'Booked' : 'Available'
        ]);
    }
}
