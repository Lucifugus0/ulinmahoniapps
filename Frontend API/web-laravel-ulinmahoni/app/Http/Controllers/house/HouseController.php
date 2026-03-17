<?php

namespace App\Http\Controllers\House;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Models\Room;

class HouseController extends Controller {
    public function index(Request $request) 
    {
        try {
            // Get houses from the database
            $query = Property::where('status', 1);
            
            // Add filters if provided in the request
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
                $query->where('price->original', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price->original', '<=', $request->price_max);
            }

            // Filter for houses
            $query->where('tags', 'House');

            // Order by created_at (newest first)
            $query->orderBy('created_at', 'desc');

            // Get paginated results and transform the data
            $houses = $query->paginate(12)->through(function($house) {
                return [
                    'id' => $house->idrec,
                    'slug' => $house->slug,
                    'name' => $house->name,
                    'type' => $house->tags,
                    'subLocation' => $house->subdistrict . ', ' . $house->city,
                    'distance' => $house->distance,
                    'location' => $house->location,
                    'price_original_daily' => $house->price_original_daily,
                    'price_original_monthly' => $house->price_original_monthly,
                    'price_discounted_daily' => $house->price_discounted_daily,
                    'price_discounted_monthly' => $house->price_discounted_monthly,
                    'price' => [
                        'original' => json_decode($house->price)->original ?? 0,
                        'discounted' => json_decode($house->price)->discounted ?? 0
                    ],
                    'features' => is_string($house->features) ? json_decode($house->features, true) : [],
                    'image' => $house->image
                ];
            });

            return view("pages.house.index", compact('houses'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading houses: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            // Get the property with its images using the Property model
            $property = Property::findOrFail($id);
            
            // Get all images using the Property model's accessor
            $propertyImages = $property->images;
            
            // Get the main image (first image or fallback to property image)
            $mainImage = !empty($propertyImages[0]['image']) ? $propertyImages[0]['image'] : $property->image;
            
            // Get secondary images (skip the first one if it was used as main)
            $secondaryImages = array_slice($propertyImages, !empty($propertyImages[0]['image']) ? 1 : 0);
            
            // Format the property data for the view
            $formattedHouse = [
                'id' => $property->idrec,
                'slug' => $property->slug,
                'name' => $property->name,
                'type' => $property->tags,
                'location' => $property->address,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'subLocation' => $property->subdistrict . ', ' . $property->city,
                'distance' => $property->distance ? "{$property->distance} km dari {$property->location}" : null,
                'price_original_daily' => $property->price_original_daily,
                'price_original_monthly' => $property->price_original_monthly,
                'price_discounted_daily' => $property->price_discounted_daily,
                'price_discounted_monthly' => $property->price_discounted_monthly,
                'features' => is_string($property->features) ? json_decode($property->features, true) : [],
                'facility' => $property->facility,
                'image' => $mainImage,
                'images' => $propertyImages,
                'description' => $property->description,
                'description_en' => $property->description_en,
                'address' => [
                    'province' => $property->province,
                    'city' => $property->city,
                    'subdistrict' => $property->subdistrict,
                    'village' => $property->village,
                    'postal_code' => $property->postal_code,
                    'full_address' => $property->address
                ],
                'gender' => $property->gender,
                'status' => $property->status,
                'rooms' => $this->getPropertyRooms($property->idrec),
                'nearby_locations' => is_string($property->nearby_locations) ? json_decode($property->nearby_locations, true) : ($property->nearby_locations ?? [])
            ];

            return view('pages.house.show', [
                'house' => $formattedHouse,
                'primaryImage' => $mainImage,
                'secondaryImages' => $secondaryImages,
                'totalImages' => count($propertyImages) > 0 ? count($propertyImages) : ($mainImage ? 1 : 0)
            ]);

        } catch (Exception $e) {
            $errorMessage = 'House not found or error occurred: ' . $e->getMessage();
            Log::error($errorMessage);
            
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], 404);
        }
    }
    public function showId($id)
    {
        try {
            // Get the property with its images using the Property model
            $property = Property::findOrFail($id);
            
            // Get all images using the Property model's accessor
            $propertyImages = $property->images;
            
            // Get the main image (first image or fallback to property image)
            $mainImage = !empty($propertyImages[0]['image']) ? $propertyImages[0]['image'] : $property->image;
            
            // Get secondary images (skip the first one if it was used as main)
            $secondaryImages = array_slice($propertyImages, !empty($propertyImages[0]['image']) ? 1 : 0);
            
            // Format the property data for the view
            $formattedHouse = [
                'id' => $property->idrec,
                'slug' => $property->slug,
                'name' => $property->name,
                'type' => $property->tags,
                'location' => $property->address,
                'subLocation' => $property->subdistrict . ', ' . $property->city,
                'distance' => $property->distance ? "{$property->distance} km dari {$property->location}" : null,
                'price_original_daily' => $property->price_original_daily,
                'price_original_monthly' => $property->price_original_monthly,
                'price_discounted_daily' => $property->price_discounted_daily,
                'price_discounted_monthly' => $property->price_discounted_monthly,
                'features' => is_string($property->features) ? json_decode($property->features, true) : [],
                'image' => $mainImage,
                'images' => $propertyImages,
                'description' => $property->description,
                'description_en' => $property->description_en,
                'address' => [
                    'province' => $property->province,
                    'city' => $property->city,
                    'subdistrict' => $property->subdistrict,
                    'village' => $property->village,
                    'postal_code' => $property->postal_code,
                    'full_address' => $property->address
                ],
                'status' => $property->status,
                'rooms' => $this->getPropertyRooms($property->idrec)
            ];

            return view('pages.house.id.show', [
                'house' => $formattedHouse,
                'primaryImage' => $mainImage,
                'secondaryImages' => $secondaryImages,
                'totalImages' => count($propertyImages) > 0 ? count($propertyImages) : ($mainImage ? 1 : 0)
            ]);

        } catch (Exception $e) {
            $errorMessage = 'House not found or error occurred: ' . $e->getMessage();
            Log::error($errorMessage);
            
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], 404);
        }
    }
    public function showEn($id)
    {
        try {
            // Get the property with its images using the Property model
            $property = Property::findOrFail($id);
            
            // Get all images using the Property model's accessor
            $propertyImages = $property->images;
            
            // Get the main image (first image or fallback to property image)
            $mainImage = !empty($propertyImages[0]['image']) ? $propertyImages[0]['image'] : $property->image;
            
            // Get secondary images (skip the first one if it was used as main)
            $secondaryImages = array_slice($propertyImages, !empty($propertyImages[0]['image']) ? 1 : 0);
            
            // Format the property data for the view
            $formattedHouse = [
                'id' => $property->idrec,
                'slug' => $property->slug,
                'name' => $property->name,
                'type' => $property->tags,
                'location' => $property->address,
                'latitude' => $property->latitude,
                'longitude' => $property->longitude,
                'subLocation' => $property->subdistrict . ', ' . $property->city,
                'distance' => $property->distance ? "{$property->distance} km dari {$property->location}" : null,
                'price_original_daily' => $property->price_original_daily,
                'price_original_monthly' => $property->price_original_monthly,
                'price_discounted_daily' => $property->price_discounted_daily,
                'price_discounted_monthly' => $property->price_discounted_monthly,
                'features' => is_string($property->features) ? json_decode($property->features, true) : [],
                'facility' => $property->facility,
                'image' => $mainImage,
                'images' => $propertyImages,
                'description' => $property->description,
                'description_en' => $property->description_en,
                'address' => [
                    'province' => $property->province,
                    'city' => $property->city,
                    'subdistrict' => $property->subdistrict,
                    'village' => $property->village,
                    'postal_code' => $property->postal_code,
                    'full_address' => $property->address
                ],
                'status' => $property->status,
                'rooms' => $this->getPropertyRooms($property->idrec)
            ];

            return view('pages.house.en.show', [
                'house' => $formattedHouse,
                'primaryImage' => $mainImage,
                'secondaryImages' => $secondaryImages,
                'totalImages' => count($propertyImages) > 0 ? count($propertyImages) : ($mainImage ? 1 : 0)
            ]);

        } catch (Exception $e) {
            $errorMessage = 'House not found or error occurred: ' . $e->getMessage();
            Log::error($errorMessage);
            
            return response()->json([
                'status' => 'error',
                'message' => $errorMessage
            ], 404);
        }
    }
    
    /**
     * Get rooms for a property
     */
    protected function getPropertyRooms($propertyId)
    {
        $rooms = Room::where('property_id', $propertyId)->where('status', 1)->get();
            
        return $rooms->map(function($room) {
            // Get all room images using the Room model's accessor
            $roomImages = $room->images;
            $mainImage = !empty($roomImages[0]['image']) ? $roomImages[0]['image'] : $room->image;
            
            return [
                'id' => $room->idrec,
                'property_id' => $room->property_id,
                'property_name' => $room->property_name,
                'slug' => $room->slug,
                'no' => $room->no,
                'name' => $room->name,
                'descriptions' => $room->descriptions,
                'type' => $room->type,
                'level' => $room->level,
                'facility' => is_string($room->facility) ? (json_decode($room->facility, true) ?? []) : ($room->facility ?? []),
                'image' => $mainImage,
                'images' => $roomImages,
                'periode' => is_string($room->periode) ? json_decode($room->periode, true) : [
                    'daily' => false,
                    'weekly' => false,
                    'monthly' => false
                ],
                'periode_daily' => $room->periode_daily,
                'periode_monthly' => $room->periode_monthly,
                'price_original_daily' => $room->price_original_daily,
                'price_original_monthly' => $room->price_original_monthly,
                'price_discounted_daily' => $room->price_discounted_daily,
                'price_discounted_monthly' => $room->price_discounted_monthly,
                'status' => $room->status,
                'rental_status'=>$room->rental_status
            ];
        })->toArray();
    }

    /**
     * Search houses with filters
     */
    public function search(Request $request)
    {
        try {
            $query = Property::query()->where('tags', 'House');

            // Apply search filters
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            }

            // Apply location filters
            if ($request->has('province')) {
                $query->where('province', $request->province);
            }
            if ($request->has('city')) {
                $query->where('city', $request->city);
            }

            // Apply price range filter
            if ($request->has('price_min')) {
                $query->where('price->original', '>=', $request->price_min);
            }
            if ($request->has('price_max')) {
                $query->where('price->original', '<=', $request->price_max);
            }

            // Apply status filter
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Order by created_at (newest first)
            $query->orderBy('idrec', 'asc');

            // Get paginated results
            $houses = $query->paginate(12)->withQueryString();

            return view('pages.house.index', compact('houses'));
        } catch (Exception $e) {
            return back()->with('error', 'Error searching houses: ' . $e->getMessage());
        }
    }
}