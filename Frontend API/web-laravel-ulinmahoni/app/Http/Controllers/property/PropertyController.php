<?php

namespace App\Http\Controllers\Property;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Property;

class PropertyController extends Controller {
    public function index(Request $request){
        try {
            // Get properties from the database with active status
            $query = Property::where('status', 1);
            
            // Filter by property type (tags)
            if ($request->has('type') && !empty($request->type)) {
                $query->where('tags', $request->type);
            }
            
            // Filter by rent period (price field)
            if ($request->has('period') && !empty($request->period)) {
                $priceField = 'price_original_' . $request->period . 'ly';
                $query->where($priceField, '>', 0);
            }

            // Filter by check-in and check-out dates (if needed)
            if ($request->has('check_in') && !empty($request->check_in)) {
                // Add availability check logic here if needed
                // This would typically check against a bookings table
            }

            if ($request->has('check_out') && !empty($request->check_out)) {
                // Add availability check logic here if needed
            }

            // Add other filters if provided in the request
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            if ($request->has('province')) {
                $query->where('province', $request->province);
            }

            if ($request->has('city')) {
                $query->where('city', $request->city);
            }

            if ($request->has('price_min')) {
                $query->where('price_original_monthly', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price_original_monthly', '<=', $request->price_max);
            }

            // Order by created_at (newest first)
            $query->orderBy('created_at', 'desc');

            // Get paginated results and transform the data
            $properties = $query->paginate(12)->through(function($property) {
                return [
                    'id' => $property->idrec,
                    'slug' => $property->slug,
                    'name' => $property->name,
                    'type' => $property->tags,
                    'subLocation' => $property->subdistrict . ', ' . $property->city,
                    'distance' => $property->distance,
                    'location' => $property->location,
                    'price_original_daily' => $property->price_original_daily,
                    'price_original_monthly' => $property->price_original_monthly,
                    'price_discounted_daily' => $property->price_discounted_daily,
                    'price_discounted_monthly' => $property->price_discounted_monthly,
                    'price' => [
                        'original' => json_decode($property->price)->original ?? 0,
                        'discounted' => json_decode($property->price)->discounted ?? 0
                    ],
                    'features' => is_string($property->features) ? json_decode($property->features, true) : [],
                    'image' => $property->image
                ];
            });

            return view("pages.properties.index", compact('properties'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading properties: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Raw query to get property with its rooms
            $property = DB::select("
                SELECT 
                    p.*,
                    r.idrec as room_id,
                    r.property_id,
                    r.property_name,
                    r.slug as room_slug,
                    r.name as room_name,
                    r.descriptions as room_descriptions,
                    r.type as room_type,
                    r.level as room_level,
                    r.facility as room_facility,
                    r.attachment as room_attachment,
                    r.periode as room_periode,
                    r.created_at as room_created_at,
                    r.updated_at as room_updated_at,
                    r.created_by as room_created_by,
                    r.updated_by as room_updated_by,
                    r.status as room_status
                FROM m_properties p
                LEFT JOIN m_rooms r ON p.idrec = r.property_id
                WHERE p.idrec = ?
                ORDER BY idrec ASC
            ", [$id]);

            if (empty($property)) {
                throw new Exception('Property not found');
            }

            // Get the first row for property data (since it's repeated in the join)
            $propertyData = $property[0];

            // Format rooms data
            $formattedRooms = collect($property)->map(function($row) {
                if ($row->room_id) {
                    return [
                        'id' => $row->room_id,
                        'property_id' => $row->property_id,
                        'property_name' => $row->property_name,
                        'slug' => $row->room_slug,
                        'name' => $row->room_name,
                        'descriptions' => $row->room_descriptions,
                        'type' => $row->room_type,
                        'level' => $row->room_level,
                        'facility' => is_string($row->room_facility) ? json_decode($row->room_facility, true) : [],
                        'attachment' => is_string($row->room_attachment) ? json_decode($row->room_attachment, true) : [],
                        'periode' => is_string($row->room_periode) ? json_decode($row->room_periode, true) : [],
                        'created_at' => $row->room_created_at,
                        'updated_at' => $row->room_updated_at,
                        'created_by' => $row->room_created_by,
                        'updated_by' => $row->room_updated_by,
                        'status' => $row->room_status
                    ];
                }
                return null;
            })->filter();

            // Format the data for the view
            $formattedProperty = [
                'id' => $propertyData->idrec,
                'slug' => $propertyData->slug,
                'name' => $propertyData->name,
                'type' => $propertyData->tags,
                'location' => $propertyData->address,
                'subLocation' => $propertyData->subdistrict . ', ' . $propertyData->city,
                'distance' => $propertyData->distance ? "{$propertyData->distance} km dari {$propertyData->location}" : null,
                // 'price' => [
                //     'original' => json_decode($propertyData->price)->original ?? 0,
                //     'discounted' => json_decode($propertyData->price)->discounted ?? 0
                // ],
                'price_original_daily' => $propertyData->price_original_daily,
                'price_discounted_daily' => $propertyData->price_discounted_daily,
                'price_original_monthly' => $propertyData->price_original_monthly,
                'price_discounted_monthly' => $propertyData->price_discounted_monthly,
                'features' => is_string($propertyData->features) ? json_decode($propertyData->features, true) : [],
                // 'image' => $propertyData->image,
                // 'image_2' => $propertyData->image_2 ?? null,
                // 'image_3' => $propertyData->image_3 ?? null,
                'description' => $propertyData->description,
                'address' => [
                    'province' => $propertyData->province,
                    'city' => $propertyData->city,
                    'subdistrict' => $propertyData->subdistrict,
                    'village' => $propertyData->village,
                    'postal_code' => $propertyData->postal_code,
                    'full_address' => $propertyData->address
                ],
                'status' => $propertyData->status,
                // 'rooms' => $formattedRooms
            ];

            // Return the appropriate view based on property type
            $view = match($propertyData->tags) {
                'House' => 'pages.house.show',
                'Apartment' => 'pages.apartment.show',
                'Villa' => 'pages.villa.show',
                'Hotel' => 'pages.hotel.show',
                default => 'pages.property.show'
            };

            return view($view, [
                'property' => $formattedProperty
            ]);

        } catch (Exception $e) {
            Log::error('Property not found or error occurred: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * Search properties with filters
     */
    public function search(Request $request)
    {
        try {
            $query = Property::query();

            // Filter by type
            if ($request->has('type') && !empty($request->type)) {
                $query->where('tags', $request->type);
            }

            // Filter by rent period
            if ($request->has('rent_period') && !empty($request->rent_period)) {
                $query->whereJsonContains('price->original->' . $request->rent_period, '>', 0);
            }

            // Filter by check-in/check-out dates
            if ($request->has('check_in') && $request->has('check_out') && 
                !empty($request->check_in) && !empty($request->check_out)) {
                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);

                // Add date availability logic here if needed
                // For now, we'll just ensure the property is active
                $query->where('status', 1);
            }

            // Order by created_at (newest first)
            $query->orderBy('created_at', 'desc');

            // Get paginated results and transform the data
            $properties = $query->paginate(12)->through(function($property) {
                return [
                    'id' => $property->idrec,
                    'slug' => $property->slug,
                    'name' => $property->name,
                    'type' => $property->tags,
                    'subLocation' => $property->subdistrict . ', ' . $property->city,
                    'distance' => $property->distance ? "{$property->distance} km dari {$property->location}" : null,
                    'price' => [
                        'original' => json_decode($property->price)->original ?? 0,
                        'discounted' => json_decode($property->price)->discounted ?? 0
                    ],
                    'features' => is_string($property->features) ? json_decode($property->features, true) : [],
                    'image' => $property->image
                ];
            })->withQueryString();

            return view('pages.properties.index', compact('properties'));
        } catch (Exception $e) {
            return back()->with('error', 'Error searching properties: ' . $e->getMessage());
        }
    }
}