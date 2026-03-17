<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roomNumber = $request->input('room_number');
        $registrationStatus = $request->input('registration_status');
        $propertyId = $request->input('property_id');
        $bookingStatus = $request->input('booking_status');
        $perPage = $request->input('per_page', 8);

        // Get properties for filter dropdown
        $properties = Property::select('idrec', 'name')->orderBy('name')->get();

        // Build the customer query by combining registered users and guest transactions
        $customers = $this->getCustomersQuery($search, $registrationStatus, $propertyId, $bookingStatus, $roomNumber)->paginate($perPage)->appends([
            'search' => $search,
            'room_number' => $roomNumber,
            'registration_status' => $registrationStatus,
            'property_id' => $propertyId,
            'booking_status' => $bookingStatus,
            'per_page' => $perPage
        ]);

        return view('pages.customers.index', [
            'customers' => $customers,
            'perPage' => $perPage,
            'properties' => $properties
        ]);
    }

    public function filter(Request $request)
    {
        $search = $request->input('search');
        $roomNumber = $request->input('room_number');
        $registrationStatus = $request->input('registration_status');
        $propertyId = $request->input('property_id');
        $bookingStatus = $request->input('booking_status');
        $perPage = $request->input('per_page', 8);

        $customers = $this->getCustomersQuery($search, $registrationStatus, $propertyId, $bookingStatus, $roomNumber)->paginate($perPage);
        $customers->setPath(route('customers.index'));
        $customers->appends($request->except('page'));

        return view('pages.customers.partials.customer_table', [
            'customers' => $customers,
            'perPage' => $perPage
        ])->render();
    }

    private function getCustomersQuery($search = null, $registrationStatus = null, $propertyId = null, $bookingStatus = null, $roomNumber = null)
    {
        // Get registered users with their booking statistics
        $registeredUsers = User::select([
            DB::raw('users.id as id'),
            DB::raw('CONVERT(users.first_name USING utf8mb4) as first_name'),
            DB::raw('CONVERT(users.last_name USING utf8mb4) as last_name'),
            DB::raw('CONVERT(users.nik USING utf8mb4) as nik'),
            DB::raw('CONVERT(users.username USING utf8mb4) as username'),
            DB::raw('CONVERT(users.email USING utf8mb4) as email'),
            DB::raw('CONVERT(users.phone_number USING utf8mb4) as phone'),
            DB::raw('CONVERT("registered" USING utf8mb4) as registration_status'),
            DB::raw('COALESCE(COUNT(DISTINCT t_transactions.order_id), 0) as total_bookings'),
            DB::raw('COALESCE(SUM(t_transactions.grandtotal_price), 0) as total_spent'),
            DB::raw('MAX(t_transactions.transaction_date) as last_booking_date'),
            DB::raw('(SELECT CONVERT(property_name USING utf8mb4) FROM t_transactions t2 WHERE t2.user_id = users.id ORDER BY t2.transaction_date DESC LIMIT 1) as last_property_name'),
            DB::raw('(SELECT CONVERT(room_name USING utf8mb4) FROM t_transactions t3 WHERE t3.user_id = users.id ORDER BY t3.transaction_date DESC LIMIT 1) as last_room_name'),
            DB::raw('(SELECT CONVERT(m_rooms.no USING utf8mb4) FROM t_transactions t4 LEFT JOIN m_rooms ON t4.room_id = m_rooms.idrec WHERE t4.user_id = users.id ORDER BY t4.transaction_date DESC LIMIT 1) as last_room_number'),
            DB::raw('(SELECT CONVERT(renewal_status USING utf8mb4) FROM t_transactions t5 WHERE t5.user_id = users.id ORDER BY t5.transaction_date DESC LIMIT 1) as renewal_status'),
            DB::raw('(SELECT is_renewal FROM t_transactions t6 WHERE t6.user_id = users.id ORDER BY t6.transaction_date DESC LIMIT 1) as is_renewal'),
            DB::raw('(SELECT CONVERT(GROUP_CONCAT(DISTINCT vehicle_plate SEPARATOR ", ") USING utf8mb4) FROM t_parking_fee_transaction pft WHERE pft.user_id = users.id AND pft.status = 1) as parking_info'),
            DB::raw("(SELECT CASE WHEN tb.status = 'cancelled' THEN 'cancelled' WHEN tb.check_in_at IS NULL THEN 'pending' WHEN tb.check_out_at IS NULL THEN 'checked-in' ELSE 'completed' END FROM t_booking tb INNER JOIN t_transactions t7 ON BINARY tb.order_id = BINARY t7.order_id WHERE t7.user_id = users.id ORDER BY t7.transaction_date DESC LIMIT 1) as current_booking_status"),
            DB::raw('(SELECT t7.check_in FROM t_transactions t7 WHERE t7.user_id = users.id ORDER BY t7.transaction_date DESC LIMIT 1) as last_check_in'),
            DB::raw('(SELECT t8.check_out FROM t_transactions t8 WHERE t8.user_id = users.id ORDER BY t8.transaction_date DESC LIMIT 1) as last_check_out')
        ])
            ->leftJoin('t_transactions', 'users.id', '=', 't_transactions.user_id')
            ->where(function ($query) {
                // Filter untuk mengecualikan admin
                $query->where('users.is_admin', '!=', 1)
                    ->orWhere('users.is_admin', 0)
                    ->orWhereNull('users.is_admin');
            })
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.nik', 'users.username', 'users.email', 'users.phone_number');

        // Get guest customers (transactions without user_id)
        $guestCustomers = Transaction::select([
            DB::raw('NULL as id'),
            DB::raw('NULL as first_name'),
            DB::raw('NULL as last_name'),
            DB::raw('NULL as nik'),
            DB::raw('CONVERT(user_name USING utf8mb4) as username'),
            DB::raw('CONVERT(user_email USING utf8mb4) as email'),
            DB::raw('CONVERT(user_phone_number USING utf8mb4) as phone'),
            DB::raw('CONVERT("guest" USING utf8mb4) as registration_status'),
            DB::raw('COUNT(DISTINCT order_id) as total_bookings'),
            DB::raw('SUM(grandtotal_price) as total_spent'),
            DB::raw('MAX(transaction_date) as last_booking_date'),
            DB::raw('(SELECT CONVERT(property_name USING utf8mb4) FROM t_transactions t2 WHERE BINARY t2.user_email = BINARY t_transactions.user_email AND t2.user_id IS NULL ORDER BY t2.transaction_date DESC LIMIT 1) as last_property_name'),
            DB::raw('(SELECT CONVERT(room_name USING utf8mb4) FROM t_transactions t3 WHERE BINARY t3.user_email = BINARY t_transactions.user_email AND t3.user_id IS NULL ORDER BY t3.transaction_date DESC LIMIT 1) as last_room_name'),
            DB::raw('(SELECT CONVERT(m_rooms.no USING utf8mb4) FROM t_transactions t4 LEFT JOIN m_rooms ON t4.room_id = m_rooms.idrec WHERE BINARY t4.user_email = BINARY t_transactions.user_email AND t4.user_id IS NULL ORDER BY t4.transaction_date DESC LIMIT 1) as last_room_number'),
            DB::raw('(SELECT CONVERT(renewal_status USING utf8mb4) FROM t_transactions t5 WHERE BINARY t5.user_email = BINARY t_transactions.user_email AND t5.user_id IS NULL ORDER BY t5.transaction_date DESC LIMIT 1) as renewal_status'),
            DB::raw('(SELECT is_renewal FROM t_transactions t6 WHERE BINARY t6.user_email = BINARY t_transactions.user_email AND t6.user_id IS NULL ORDER BY t6.transaction_date DESC LIMIT 1) as is_renewal'),
            DB::raw('(SELECT CONVERT(GROUP_CONCAT(DISTINCT vehicle_plate SEPARATOR ", ") USING utf8mb4) FROM t_parking_fee_transaction pft WHERE BINARY pft.user_phone = BINARY t_transactions.user_phone_number AND pft.status = 1) as parking_info'),
            DB::raw("(SELECT CASE WHEN tb.status = 'cancelled' THEN 'cancelled' WHEN tb.check_in_at IS NULL THEN 'pending' WHEN tb.check_out_at IS NULL THEN 'checked-in' ELSE 'completed' END FROM t_booking tb INNER JOIN t_transactions t7 ON BINARY tb.order_id = BINARY t7.order_id WHERE BINARY t7.user_email = BINARY t_transactions.user_email AND t7.user_id IS NULL ORDER BY t7.transaction_date DESC LIMIT 1) as current_booking_status"),
            DB::raw('(SELECT t7.check_in FROM t_transactions t7 WHERE BINARY t7.user_email = BINARY t_transactions.user_email AND t7.user_id IS NULL ORDER BY t7.transaction_date DESC LIMIT 1) as last_check_in'),
            DB::raw('(SELECT t8.check_out FROM t_transactions t8 WHERE BINARY t8.user_email = BINARY t_transactions.user_email AND t8.user_id IS NULL ORDER BY t8.transaction_date DESC LIMIT 1) as last_check_out')
        ])
            ->whereNull('user_id')
            ->groupBy('user_name', 'user_email', 'user_phone_number');

        // Apply property filter to registered users - only show users who have booked at this property
        if ($propertyId) {
            $registeredUsers->whereExists(function ($query) use ($propertyId) {
                $query->select(DB::raw(1))
                    ->from('t_transactions as t_prop')
                    ->whereColumn('t_prop.user_id', 'users.id')
                    ->where('t_prop.property_id', $propertyId);
            });
        }

        // Apply property filter to guest customers
        if ($propertyId) {
            $guestCustomers->where('property_id', $propertyId);
        }

        // Apply search filter to registered users (name, email, phone, order ID)
        if ($search) {
            $registeredUsers->where(function ($query) use ($search) {
                $query->where('users.username', 'like', '%' . $search . '%')
                    ->orWhere('users.email', 'like', '%' . $search . '%')
                    ->orWhere('users.phone_number', 'like', '%' . $search . '%')
                    ->orWhereExists(function ($q) use ($search) {
                        $q->select(DB::raw(1))
                            ->from('t_transactions as t_oid')
                            ->whereColumn('t_oid.user_id', 'users.id')
                            ->where('t_oid.order_id', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply room number filter to registered users
        if ($roomNumber) {
            $registeredUsers->whereExists(function ($q) use ($roomNumber) {
                $q->select(DB::raw(1))
                    ->from('t_transactions as t_rn')
                    ->leftJoin('m_rooms as rm', 't_rn.room_id', '=', 'rm.idrec')
                    ->whereColumn('t_rn.user_id', 'users.id')
                    ->where('rm.no', 'like', '%' . $roomNumber . '%');
            });
        }

        // Apply search filter to guest customers (name, email, phone, order ID)
        if ($search) {
            $guestCustomers->where(function ($query) use ($search) {
                $query->where('user_name', 'like', '%' . $search . '%')
                    ->orWhere('user_email', 'like', '%' . $search . '%')
                    ->orWhere('user_phone_number', 'like', '%' . $search . '%')
                    ->orWhereExists(function ($q) use ($search) {
                        $q->select(DB::raw(1))
                            ->from('t_transactions as t_oid')
                            ->whereRaw('BINARY t_oid.user_email = BINARY t_transactions.user_email')
                            ->whereNull('t_oid.user_id')
                            ->where('t_oid.order_id', 'like', '%' . $search . '%');
                    });
            });
        }

        // Apply room number filter to guest customers
        if ($roomNumber) {
            $guestCustomers->whereExists(function ($q) use ($roomNumber) {
                $q->select(DB::raw(1))
                    ->from('t_transactions as t_rn')
                    ->leftJoin('m_rooms as rm', 't_rn.room_id', '=', 'rm.idrec')
                    ->whereRaw('BINARY t_rn.user_email = BINARY t_transactions.user_email')
                    ->whereNull('t_rn.user_id')
                    ->where('rm.no', 'like', '%' . $roomNumber . '%');
            });
        }

        // Combine both queries using union
        $query = $registeredUsers->union($guestCustomers);

        // Apply registration status filter
        if ($registrationStatus && $registrationStatus !== 'all') {
            $query = DB::table(DB::raw("({$query->toSql()}) as customers"))
                ->mergeBindings($query->getQuery())
                ->where('registration_status', $registrationStatus);
        } else {
            $query = DB::table(DB::raw("({$query->toSql()}) as customers"))
                ->mergeBindings($query->getQuery());
        }

        // Apply booking status filter (derived from check_in_at / check_out_at)
        if ($bookingStatus) {
            $query->where('current_booking_status', $bookingStatus);
        }

        // Order by last booking date descending (newest first)
        return $query->orderBy('last_booking_date', 'desc');
    }

    public function getBookings(Request $request, $identifier)
    {
        $type = $request->input('type', 'registered');

        if ($type === 'registered') {
            // Get bookings for registered users
            // TAMBAHKAN FILTER UNTUK MEMASTIKAN BUKAN ADMIN
            $bookings = Transaction::with(['booking', 'property', 'room', 'payment'])
                ->whereHas('user', function ($query) {
                    $query->where('is_admin', '!=', 1)
                        ->orWhere('is_admin', 0)
                        ->orWhereNull('is_admin');
                })
                ->where('user_id', $identifier)
                ->orderBy('transaction_date', 'desc')
                ->get();

            // Get parking info for registered user
            $parkingInfo = DB::table('t_parking_fee_transaction')
                ->where('user_id', $identifier)
                ->where('status', 1)
                ->select('parking_type', 'vehicle_plate', 'order_id')
                ->get()
                ->groupBy('order_id');

            // TAMBAHKAN FILTER UNTUK MEMASTIKAN BUKAN ADMIN
            $customer = User::where('id', $identifier)
                ->where(function ($query) {
                    $query->where('is_admin', '!=', 1)
                        ->orWhere('is_admin', 0)
                        ->orWhereNull('is_admin');
                })
                ->first();
            $customerName = $customer ? $customer->username : 'Unknown';
        } else {
            // Get bookings for guest customers by email
            $bookings = Transaction::with(['booking', 'property', 'room', 'payment'])
                ->where('user_email', $identifier)
                ->whereNull('user_id')
                ->orderBy('transaction_date', 'desc')
                ->get();

            // Get parking info for guest by phone
            $phoneNumber = $bookings->first()->user_phone_number ?? null;
            $parkingInfo = collect([]);
            if ($phoneNumber) {
                $parkingInfo = DB::table('t_parking_fee_transaction')
                    ->where('user_phone', $phoneNumber)
                    ->where('status', 1)
                    ->select('parking_type', 'vehicle_plate', 'order_id')
                    ->get()
                    ->groupBy('order_id');
            }

            $customerName = $bookings->first()->user_name ?? 'Unknown';
        }

        return response()->json([
            'customer_name' => $customerName,
            'bookings' => $bookings->map(function ($transaction) use ($parkingInfo) {
                $parking = $parkingInfo->get($transaction->order_id);
                $parkingDetails = [];
                $seenPlates = [];
                if ($parking) {
                    foreach ($parking as $p) {
                        if (!in_array($p->vehicle_plate, $seenPlates)) {
                            $seenPlates[] = $p->vehicle_plate;
                            $parkingDetails[] = [
                                'type' => $p->parking_type,
                                'plate' => $p->vehicle_plate
                            ];
                        }
                    }
                }

                return [
                    'order_id' => $transaction->order_id,
                    'property_name' => $transaction->property_name,
                    'room_name' => $transaction->room_name,
                    'room_number' => $transaction->room ? $transaction->room->no : null,
                    'transaction_date' => $transaction->transaction_date ? $transaction->transaction_date->format('M d, Y') : '-',
                    'check_in' => $transaction->check_in ? $transaction->check_in->format('M d, Y') : '-',
                    'check_out' => $transaction->check_out ? $transaction->check_out->format('M d, Y') : '-',
                    'booking_days' => $transaction->booking_days,
                    'booking_months' => $transaction->booking_months,
                    'grandtotal_price' => number_format($transaction->grandtotal_price, 0, ',', '.'),
                    'transaction_status' => $transaction->transaction_status,
                    'booking_status' => $transaction->booking ? $transaction->booking->status : '-',
                    'payment_status' => $transaction->payment ? $transaction->payment->status : 'unpaid',
                    'is_renewal' => $transaction->is_renewal,
                    'renewal_status' => $transaction->renewal_status,
                    'parking' => $parkingDetails
                ];
            })
        ]);
    }

    public function preRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'username'     => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'nik'          => 'nullable|string|max:16',
            'password'     => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $mainAppUrl = env('MAIN_APP_URL');
            $apiUrl = rtrim($mainAppUrl, '/') . '/api/v1/register-without-verification';

            $postData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => $request->password,
            ];

            if ($request->filled('nik')) {
                $postData['nik'] = $request->nik;
            }

            $response = Http::timeout(30)->post($apiUrl, $postData);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'success') {
                // Update NIK in local users table if NIK was provided
                if ($request->filled('nik')) {
                    $localUser = User::where('email', $request->email)->first();
                    if ($localUser) {
                        $localUser->update(['nik' => $request->nik]);
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => $responseData['message'] ?? 'Registration successful.',
                    'data' => $responseData['data'] ?? null
                ], 200);
            }

            // Handle error response from external API
            Log::warning('Pre-registration API error', [
                'status' => $response->status(),
                'response' => $responseData,
            ]);
            return response()->json([
                'status' => 'error',
                'message' => $responseData['message'] ?? 'Registration failed. Please try again.',
                'errors' => $responseData['errors'] ?? null
            ], $response->status() ?: 400);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Pre-registration API connection error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Unable to connect to registration service. Please try again later.'
            ], 503);

        } catch (\Exception $e) {
            Log::error('Pre-registration error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function updateCustomer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name'   => 'nullable|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'username'     => 'nullable|string|max:255',
            'email'        => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'nik'          => 'nullable|string|max:16',
            'password'     => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user = User::findOrFail($id);

            foreach (['first_name', 'last_name', 'username', 'email', 'phone_number', 'nik'] as $field) {
                if ($request->filled($field)) {
                    $user->$field = $request->input($field);
                }
            }

            if ($request->filled('first_name') || $request->filled('last_name')) {
                $user->name = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''));
            }

            if ($request->filled('password')) {
                $user->password = \Illuminate\Support\Facades\Hash::make($request->input('password'));
            }

            $user->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Customer updated successfully.',
                'data'    => $user->only(['id', 'first_name', 'last_name', 'username', 'email', 'phone_number', 'nik'])
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Customer not found.'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Customer update error: ' . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }
}
