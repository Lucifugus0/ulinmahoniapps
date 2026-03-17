<?php

namespace App\Http\Controllers\AccountSettings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use App\Models\Booking;
use App\Models\Payment;
use Carbon\Carbon;

class UserSettingsController extends Controller
{
    /**
     * Update the authenticated user's own profile data.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
        ], [
            'first_name.required'  => 'First name is required.',
            'last_name.required'   => 'Last name is required.',
            'email.required'       => 'Email is required.',
            'email.unique'         => 'This email is already registered.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'profile')
                ->withInput()
                ->with('active_tab', 'account');
        }

        $validated = $validator->validated();

        $updateData = [
            'first_name'   => $validated['first_name'],
            'last_name'    => $validated['last_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? $user->phone_number,
            'updated_by'   => $user->id,
        ];

        $username = $request->input('username');
        if (!empty($username)) {
            $updateData['username'] = $username;
        }

        $user->update($updateData);

        return redirect()->route('users.show')
            ->with('success', 'Profile updated successfully.')
            ->with('active_tab', 'account');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required'         => 'Current password is required.',
            'current_password.current_password' => 'The current password is incorrect.',
            'password.required'                 => 'New password is required.',
            'password.confirmed'                => 'Password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'security')
                ->withInput()
                ->with('active_tab', 'security');
        }

        $request->user()->update([
            'password' => Hash::make($validator->validated()['password']),
        ]);

        return redirect()->back()
            ->with('password_success', 'Password updated successfully.')
            ->with('active_tab', 'security');
    }

    /**
     * Update the user's locale preference.
     */
    public function updateLocale(Request $request)
    {
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:en,id'],
        ]);

        $request->user()->update([
            'locale' => $validated['locale'],
        ]);

        App::setLocale($validated['locale']);

        return redirect()->back()
            ->with('success', __('ui.language_updated'))
            ->with('active_tab', 'account');
    }

    /**
     * Get user activity history for all users
     * Filters activities from today up to 1 month ago
     */
    public function getUserActivity()
    {
        try {
            $activities = [];

            // Define date range: from 1 month ago to today (end of day)
            $startDate = Carbon::now()->subMonth()->startOfDay();
            $endDate = Carbon::now()->endOfDay();

            // Get all bookings within the date range
            $bookings = Booking::with(['room', 'property', 'transaction'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate])
                        ->orWhereBetween('check_in_at', [$startDate, $endDate])
                        ->orWhereBetween('check_out_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($bookings as $booking) {
                // Create separate activity entries for each status, but only if within date range

                // Reservation activity (created_at)
                if ($booking->created_at >= $startDate && $booking->created_at <= $endDate) {
                    $activities[] = [
                        'type' => 'reservation',
                        'title' => $this->getActivityTitle('reservation', $booking),
                        'description' => 'New booking created',
                        'timestamp' => $booking->created_at->toISOString(),
                        'data' => [
                            'room_name' => $booking->room->name ?? 'N/A',
                            'property_name' => $booking->property->name ?? 'N/A',
                            'guest_name' => $booking->user_name ?? 'N/A',
                            'order_id' => $booking->order_id ?? 'N/A',
                        ]
                    ];
                }

                // Check-in activity
                if ($booking->check_in_at) {
                    $checkInDate = Carbon::parse($booking->check_in_at);
                    if ($checkInDate >= $startDate && $checkInDate <= $endDate) {
                        $activities[] = [
                            'type' => 'checkin',
                            'title' => $this->getActivityTitle('checkin', $booking),
                            'description' => 'Guest checked in',
                            'timestamp' => $checkInDate->toISOString(),
                            'data' => [
                                'room_name' => $booking->room->name ?? 'N/A',
                                'property_name' => $booking->property->name ?? 'N/A',
                                'guest_name' => $booking->user_name ?? 'N/A',
                                'order_id' => $booking->order_id ?? 'N/A',
                            ]
                        ];
                    }
                }

                // Check-out activity
                if ($booking->check_out_at) {
                    $checkOutDate = Carbon::parse($booking->check_out_at);
                    if ($checkOutDate >= $startDate && $checkOutDate <= $endDate) {
                        $activities[] = [
                            'type' => 'checkout',
                            'title' => $this->getActivityTitle('checkout', $booking),
                            'description' => 'Guest checked out',
                            'timestamp' => $checkOutDate->toISOString(),
                            'data' => [
                                'room_name' => $booking->room->name ?? 'N/A',
                                'property_name' => $booking->property->name ?? 'N/A',
                                'guest_name' => $booking->user_name ?? 'N/A',
                                'order_id' => $booking->order_id ?? 'N/A',
                            ]
                        ];
                    }
                }

                // Print registration agreement activity
                if ($booking->is_printed == 1) {
                    // Use updated_at as proxy for print time if printed_at not available
                    $printDate = $booking->printed_at ? Carbon::parse($booking->printed_at) : $booking->updated_at;
                    if ($printDate >= $startDate && $printDate <= $endDate) {
                        $activities[] = [
                            'type' => 'print_registration',
                            'title' => $this->getActivityTitle('print_registration', $booking),
                            'description' => 'Registration agreement printed',
                            'timestamp' => $printDate->toISOString(),
                            'data' => [
                                'room_name' => $booking->room->name ?? 'N/A',
                                'property_name' => $booking->property->name ?? 'N/A',
                                'guest_name' => $booking->user_name ?? 'N/A',
                                'order_id' => $booking->order_id ?? 'N/A',
                                'is_printed' => $booking->is_printed,
                                'printed_at' => $printDate->toISOString(),
                                'printed_by' => $booking->printed_by ?? 'System',
                                'print_attempts' => $booking->print_attempts ?? 1,
                            ]
                        ];
                    }
                }
            }

            // Get all payment activities within the date range
            $payments = Payment::whereBetween('created_at', [$startDate, $endDate])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($payments as $payment) {
                $activities[] = [
                    'type' => 'payment',
                    'title' => 'Payment ' . ucfirst($payment->payment_status ?? 'processed'),
                    'description' => 'Payment status: ' . ($payment->payment_status ?? 'N/A'),
                    'timestamp' => $payment->created_at->toISOString(),
                    'data' => [
                        'order_id' => $payment->order_id ?? 'N/A',
                        'amount' => $payment->grandtotal_price ?? 0,
                        'status' => $payment->payment_status ?? 'N/A',
                    ]
                ];
            }

            // Sort all activities by timestamp (newest first)
            usort($activities, function ($a, $b) {
                return strtotime($b['timestamp']) - strtotime($a['timestamp']);
            });

            // All activities within the date range (1 month) will be shown

            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch activities: ' . $e->getMessage(),
                'activities' => []
            ], 500);
        }
    }

    /**
     * Get activity title based on type
     */
    private function getActivityTitle($type, $booking = null)
    {
        switch ($type) {
            case 'reservation':
                return 'New Reservation Created';
            case 'checkin':
                return 'Guest Check-In';
            case 'checkout':
                return 'Guest Check-Out';
            case 'payment':
                return 'Payment Processed';
            case 'print_registration':
                // Jika ada nama tamu, tambahkan di title
                if ($booking && $booking->user_name) {
                    return 'Registration Agreement Printed for ' . $booking->user_name;
                }
                return 'Registration Agreement Printed';
            default:
                return 'Activity';
        }
    }

}
