<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Property;
use App\Models\Room;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends ApiController
{
    /**
     * Search for available rooms with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchRooms(Request $request)
    {
        try {
            // Start with rooms query joined with properties
            $query = Room::query()
                ->join('m_properties', 'm_rooms.property_id', '=', 'm_properties.idrec')
                ->where('m_rooms.status', 1)
                ->where(function($q) {
                    $q->where('m_rooms.rental_status', '!=', 1)
                      ->orWhereNull('m_rooms.rental_status');
                })
                ->where('m_properties.status', 1)
                ->select('m_rooms.*');

            // Apply property type filter
            if ($request->has('type') && !empty($request->type)) {
                $query->where('m_properties.tags', $request->type);
            }

            // Apply search filter (search in property name, room name, description, address)
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('m_properties.name', 'like', "%{$search}%")
                      ->orWhere('m_rooms.name', 'like', "%{$search}%")
                      ->orWhere('m_properties.description', 'like', "%{$search}%")
                      ->orWhere('m_properties.address', 'like', "%{$search}%");
                });
            }

            // Apply location filters
            if ($request->has('province') && !empty($request->province)) {
                $query->where('m_properties.province', $request->province);
            }

            if ($request->has('city') && !empty($request->city)) {
                $query->where('m_properties.city', $request->city);
            }

            // Determine the period (daily or monthly)
            $period = strtolower($request->get('period', 'monthly'));

            // Handle period filter (daily or monthly)
            if ($period === 'daily' || $period === '1_day') {
                // Filter rooms that have daily pricing
                $query->whereNotNull('m_rooms.price_original_daily')
                      ->where('m_rooms.price_original_daily', '>', 0);

                // Apply price filters for daily
                if ($request->has('price_min') && !empty($request->price_min)) {
                    $query->where('m_rooms.price_original_daily', '>=', $request->price_min);
                }
                if ($request->has('price_max') && !empty($request->price_max)) {
                    $query->where('m_rooms.price_original_daily', '<=', $request->price_max);
                }
            } else {
                // Filter rooms that have monthly pricing
                $query->whereNotNull('m_rooms.price_original_monthly')
                      ->where('m_rooms.price_original_monthly', '>', 0);

                // Apply price filters for monthly
                if ($request->has('price_min') && !empty($request->price_min)) {
                    $query->where('m_rooms.price_original_monthly', '>=', $request->price_min);
                }
                if ($request->has('price_max') && !empty($request->price_max)) {
                    $query->where('m_rooms.price_original_monthly', '<=', $request->price_max);
                }
            }

            // Handle availability checking if dates are provided
            if ($request->has('check_in') && !empty($request->check_in) &&
                $request->has('check_out') && !empty($request->check_out)) {

                // Use same time handling as BookingController's checkAvailability
                // startOfDay() for check-in (00:00:00) and endOfDay() for check-out (23:59:59)
                $checkIn = Carbon::parse($request->check_in)->startOfDay();
                $checkOut = Carbon::parse($request->check_out)->endOfDay();

                // Exclude rooms that have conflicting bookings
                // Match the exact logic from BookingController's checkAvailability function
                $query->whereNotExists(function($subQuery) use ($checkIn, $checkOut) {
                    $subQuery->select(DB::raw(1))
                        ->from('t_transactions')
                        ->whereColumn('t_transactions.property_id', 'm_rooms.property_id')  // Match property_id
                        ->whereColumn('t_transactions.room_id', 'm_rooms.idrec')           // Match room_id
                        ->where('t_transactions.status', '1')
                        ->whereNotIn('t_transactions.transaction_status', ['cancelled', 'expired'])
                        ->where('t_transactions.check_in', '<', $checkOut)
                        ->where('t_transactions.check_out', '>', $checkIn);
                });
            }

            // Order by property_id ascending
            $query->orderBy('m_rooms.property_id', 'asc');

            // Get paginated results
            $perPage = $request->get('per_page', 12);
            $rooms = $query->with('property')->paginate($perPage);

            // Group rooms by property for better display
            $groupedRooms = $rooms->getCollection()->groupBy('property_id');

            // Transform to properties with available rooms
            $properties = $groupedRooms->map(function($roomsGroup) use ($period) {
                $property = $roomsGroup->first()->property;

                // Add available rooms to property
                $availableRooms = $roomsGroup->map(function($room) use ($period) {
                    // Add current price based on period
                    $currentPrice = $period === 'daily'
                        ? $room->price_original_daily
                        : $room->price_original_monthly;

                    return [
                        'id' => $room->idrec,
                        'slug' => $room->slug,
                        'no' => $room->no,
                        'name' => $room->name,
                        'image' => $room->image,
                        'images' => $room->images,
                        'bed_count' => $room->bed_count,
                        'room_size' => $room->room_size,
                        'current_price' => $currentPrice,
                        'current_period' => $period,
                        'price_daily' => $room->price_original_daily,
                        'price_monthly' => $room->price_original_monthly,
                        'status' => $room->status,
                        'rental_status' => $room->rental_status,
                    ];
                });

                // Calculate lowest price
                $lowestPrice = $roomsGroup->min(function($room) use ($period) {
                    return $period === 'daily'
                        ? $room->price_original_daily
                        : $room->price_original_monthly;
                });

                return [
                    'id' => $property->idrec,
                    'name' => $property->name,
                    'slug' => $property->slug,
                    'tags' => $property->tags,
                    'gender' => $property->gender,
                    'address' => $property->address,
                    'city' => $property->city,
                    'province' => $property->province,
                    'image' => $property->image,
                    'images' => $property->images,
                    'features' => $property->features,
                    'available_rooms_count' => $availableRooms->count(),
                    'available_rooms' => $availableRooms,
                    'lowest_price' => $lowestPrice,
                ];
            })->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Rooms retrieved successfully',
                'data' => $properties,
                'meta' => [
                    'current_page' => $rooms->currentPage(),
                    'last_page' => $rooms->lastPage(),
                    'per_page' => $rooms->perPage(),
                    'total' => $rooms->total(),
                    'from' => $rooms->firstItem(),
                    'to' => $rooms->lastItem(),
                ],
                'filters' => [
                    'type' => $request->type ?? '',
                    'period' => $period,
                    'check_in' => $request->check_in ?? '',
                    'check_out' => $request->check_out ?? '',
                    'search' => $request->search ?? '',
                    'province' => $request->province ?? '',
                    'city' => $request->city ?? '',
                    'price_min' => $request->price_min ?? '',
                    'price_max' => $request->price_max ?? '',
                ]
            ]);

        } catch (Exception $e) {
            \Log::error('Error in room search API: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Error searching rooms',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred'
            ], 500);
        }
    }
}
