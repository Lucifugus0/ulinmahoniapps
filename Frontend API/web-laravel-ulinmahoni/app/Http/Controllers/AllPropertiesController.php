<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Room;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllPropertiesController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Start with rooms query joined with properties
            $query = Room::query()
                ->join('m_properties', 'm_rooms.property_id', '=', 'm_properties.idrec')
                ->where('m_rooms.status', 1)
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

                $checkIn = Carbon::parse($request->check_in)->setTime(14, 0, 0);
                $checkOut = Carbon::parse($request->check_out)->setTime(12, 0, 0);

                // Exclude rooms that have conflicting bookings
                $query->whereNotExists(function($subQuery) use ($checkIn, $checkOut) {
                    $subQuery->select(DB::raw(1))
                        ->from('t_transactions')
                        ->whereColumn('t_transactions.room_id', 'm_rooms.idrec')
                        ->where('t_transactions.status', '1')
                        ->whereNotIn('t_transactions.transaction_status', ['cancelled', 'expired'])
                        ->where('t_transactions.check_in', '<', $checkOut)
                        ->where('t_transactions.check_out', '>', $checkIn);
                });
            }

            // Order by created_at (newest first)
            $query->orderBy('m_rooms.created_at', 'desc');

            // Get paginated results
            $rooms = $query->with('property')->paginate(12)->withQueryString();

            // Group rooms by property for better display
            $groupedRooms = $rooms->getCollection()->groupBy('property_id');

            // Transform to properties with available rooms
            $properties = $groupedRooms->map(function($roomsGroup) use ($period) {
                $property = $roomsGroup->first()->property;

                // Add available rooms to property
                $property->available_rooms = $roomsGroup->map(function($room) use ($period) {
                    // Add current price based on period
                    $room->current_price = $period === 'daily'
                        ? $room->price_original_daily
                        : $room->price_original_monthly;
                    $room->current_period = $period;
                    return $room;
                });

                // Add lowest price for display
                $property->lowest_price = $roomsGroup->min(function($room) use ($period) {
                    return $period === 'daily'
                        ? $room->price_original_daily
                        : $room->price_original_monthly;
                });

                return $this->formatProperty($property);
            })->values();

            // Create a paginator for the grouped results
            $currentPage = $rooms->currentPage();
            $perPage = $rooms->perPage();
            $total = $rooms->total();

            $propertiesPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $properties,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Pass filter values to view
            $filters = [
                'type' => $request->type ?? '',
                'period' => $period,
                'check_in' => $request->check_in ?? '',
                'check_out' => $request->check_out ?? '',
                'search' => $request->search ?? '',
                'province' => $request->province ?? '',
                'city' => $request->city ?? '',
                'price_min' => $request->price_min ?? '',
                'price_max' => $request->price_max ?? '',
            ];

            return view('pages.properties.index', [
                'properties' => $propertiesPaginator,
                'filters' => $filters
            ]);
        } catch (Exception $e) {
            \Log::error('Error in properties search: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error loading properties: ' . $e->getMessage());
        }
    }

    /**
     * Format a property model instance with thumbnail and room prices.
     *
     * @param Property $property The property model instance to format
     * @return Property The property with additional formatted data
     */
    private function formatProperty($property)
    {
        // Get the lowest room price if available
        $roomPrice = $property->rooms()
            ->whereNotNull('price_original_monthly')
            ->where('price_original_monthly', '>', 0)
            ->min('price_original_monthly');

        // Get all property images using the accessor
        $images = $property->images;

        // Use the first image from the images array as the main image, fallback to property image
        $mainImage = !empty($images) ? $images[0]['image'] : $property->image;

        // Get thumbnail - prioritize images with thumbnail field
        $thumbnail = null;
        if (!empty($images)) {
            // Find first image with thumbnail
            foreach ($images as $image) {
                if (!empty($image['thumbnail'])) {
                    $thumbnail = $image['thumbnail'];
                    break;
                }
            }
            // If no thumbnail found, use the first image
            if (!$thumbnail && !empty($images[0]['image'])) {
                $thumbnail = $images[0]['image'];
            }
        }
        // Fallback to property image if no thumbnail
        if (!$thumbnail) {
            $thumbnail = $property->image;
        }

        // Add formatted data to property
        $property->formatted_main_image = $mainImage;
        $property->formatted_thumbnail = $thumbnail;
        $property->formatted_images = $images;
        $property->formatted_room_price = $roomPrice ?? $property->price_original_monthly;

        return $property;
    }
} 