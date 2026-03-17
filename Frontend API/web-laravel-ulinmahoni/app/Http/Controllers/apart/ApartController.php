<?php

namespace App\Http\Controllers\apart;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Models\Room;

class ApartController extends Controller {
    public function index(Request $request){
        try {
            // Get apartments from the database
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

            // Filter for apartments
            $query->where('tags', 'Apartment');

            // Order by created_at (newest first)
            $query->orderBy('created_at', 'desc');

            // Get paginated results
            $apartments = $query->paginate(12); // Show 12 apartments per page

            return view("pages.apartment.index", compact('apartments'));
        } catch (Exception $e) {
            return back()->with('error', 'Error loading apartments: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $property = Property::where('tags', 'Apartment')->findOrFail($id);

            $propertyImages = $property->images;
            $mainImage = !empty($propertyImages[0]['image']) ? $propertyImages[0]['image'] : $property->image;
            $secondaryImages = array_slice($propertyImages, !empty($propertyImages[0]['image']) ? 1 : 0);

            $formattedApartment = [
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

            return view('pages.apartment.show', [
                'apartment' => $formattedApartment,
                'primaryImage' => $mainImage,
                'secondaryImages' => $secondaryImages,
                'totalImages' => count($propertyImages) > 0 ? count($propertyImages) : ($mainImage ? 1 : 0)
            ]);

        } catch (Exception $e) {
            Log::error('Apartment not found or error occurred: ' . $e->getMessage());
            abort(404);
        }
    }

    protected function getPropertyRooms($propertyId)
    {
        $rooms = Room::where('property_id', $propertyId)->where('status', 1)->get();

        return $rooms->map(function($room) {
            $roomImages = $room->images;
            $mainImage = !empty($roomImages[0]['image']) ? $roomImages[0]['image'] : $room->image;

            return [
                'id' => $room->idrec,
                'property_id' => $room->property_id,
                'slug' => $room->slug,
                'no' => $room->no,
                'name' => $room->name,
                'descriptions' => $room->descriptions,
                'type' => $room->type,
                'facility' => is_string($room->facility) ? (json_decode($room->facility, true) ?? []) : ($room->facility ?? []),
                'image' => $mainImage,
                'images' => $roomImages,
                'price_original_daily' => $room->price_original_daily,
                'price_original_monthly' => $room->price_original_monthly,
                'status' => $room->status,
                'rental_status' => $room->rental_status
            ];
        })->toArray();
    }

    /**
     * Search apartments with filters
     */
    public function search(Request $request)
    {
        try {
            $query = Property::query()->where('tags', 'Apartment');

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
            $query->orderBy('created_at', 'desc');

            // Get paginated results
            $apartments = $query->paginate(12)->withQueryString();

            return view('pages.apartment.index', compact('apartments'));
        } catch (Exception $e) {
            return back()->with('error', 'Error searching apartments: ' . $e->getMessage());
        }
    }
}