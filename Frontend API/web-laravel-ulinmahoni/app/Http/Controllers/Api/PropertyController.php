<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends ApiController
{
    /**
     * Get all properties with optional filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $query = Property::query();
            
            // Join with property images
            $query->leftJoin('m_property_images', 'm_property_images.property_id', '=', 'm_properties.idrec')
                ->select([
                    'm_properties.*',
                    'm_property_images.idrec as image_id',
                    'm_property_images.image as image_data',
                    'm_property_images.thumbnail as thumbnail_data',
                    'm_property_images.caption',
                ]);

            
            // Add filters if provided
            if ($request->has('idrec')) {
                $query->where('m_properties.idrec', $request->idrec);
            }
            if ($request->has('tags')) {
                $query->where('tags', $request->tags);
            }

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
                $query->where('price', '>=', $request->price_min);
            }

            if ($request->has('price_max')) {
                $query->where('price', '<=', $request->price_max);
            }
            
            // Order by created_at (newest first)
            $query->orderBy('m_properties.created_at', 'desc');
            
            // Get all properties with their images
            $properties = $query->get();
            
            // Group images by property
            $groupedProperties = $properties->groupBy('idrec')->map(function ($propertyGroup) {
                $property = $propertyGroup->first();

                // Map and sort images - images with thumbnails come first
                $images = $propertyGroup->filter(fn($item) => $item->image_id !== null)
                    ->map(fn($imageItem) => [
                        'id' => $imageItem->image_id,
                        'image_data' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                        'thumbnail' => $imageItem->thumbnail_data ? env('ADMIN_URL') . '/storage/' . $imageItem->image_data : null,
                        'caption' => $imageItem->caption,
                        '_has_thumbnail' => !empty($imageItem->thumbnail_data), // Helper for sorting
                    ])
                    ->sortByDesc('_has_thumbnail') // Sort by thumbnail presence (true first)
                    ->map(function($image) {
                        // Remove the helper field before returning
                        unset($image['_has_thumbnail']);
                        return $image;
                    })
                    ->values();

                $propertyArray = $property->toArray();

                // Add thumbnail field to main property object (first image with thumbnail, or null)
                $firstImageWithThumbnail = $images->first(fn($img) => !empty($img['thumbnail']));
                $propertyArray['thumbnail'] = $firstImageWithThumbnail['thumbnail'] ?? null;

                $propertyArray['images'] = $images;

                // Add deposit fee and parking fees
                $propertyArray['deposit_fee_amount'] = $property->deposit_fee_amount;
                $propertyArray['parking_fees'] = $property->parking_fees;

                // Get total rooms count
                $totalRooms = $property->rooms()->where('status', 1)->count();

                // Get available rooms count (status = 1 and rental_status != 1)
                $availableRooms = $property->rooms()
                    ->where('status', 1)
                    ->where(function($query) {
                        $query->where('rental_status', '!=', 1)
                              ->orWhereNull('rental_status');
                    })
                    ->count();

                $propertyArray['total_rooms'] = $totalRooms;
                $propertyArray['available_rooms'] = $availableRooms;

                // Remove image-related fields from the main property object
                unset(
                    $propertyArray['image_id'],
                    $propertyArray['image_data'],
                    $propertyArray['thumbnail_data'],
                    $propertyArray['caption']
                );

                return $propertyArray;
            })->values();
            
            // Handle pagination if requested
            if ($request->has('limit') && $request->has('page')) {
                $page = $request->page;
                $perPage = $request->limit;
                $offset = ($page - 1) * $perPage;
                
                $paginatedItems = $groupedProperties->slice($offset, $perPage)->values();
                
                $response = new \Illuminate\Pagination\LengthAwarePaginator(
                    $paginatedItems,
                    $groupedProperties->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                
                return $this->respondWithPagination($response, [
                    'data' => $response->items()
                ]);
            }
            
            return $this->respond([
                'data' => $groupedProperties
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
    
    /**
     * Get a specific property by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $property = Property::leftJoin('m_property_images', 'm_property_images.property_id', '=', 'm_properties.idrec')
                ->where('m_properties.idrec', $id)
                ->select([
                    'm_properties.*',
                    'm_property_images.idrec as image_id',
                    'm_property_images.image as image_data',
                    'm_property_images.thumbnail as thumbnail_data',
                    'm_property_images.caption',
                ])
                ->get();

            if ($property->isEmpty()) {
                return $this->respondNotFound('Property not found');
            }

            // Group images by property
            $groupedProperty = $property->groupBy('idrec')->map(function ($propertyGroup) {
                $property = $propertyGroup->first();

                // Map and sort images - images with thumbnails come first
                $images = $propertyGroup->filter(fn($item) => $item->image_id !== null)
                    ->map(fn($imageItem) => [
                        'id' => $imageItem->image_id,
                        'image_data' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                        'thumbnail' => $imageItem->thumbnail_data ? env('ADMIN_URL') . '/storage/' . $imageItem->image_data : null,
                        'caption' => $imageItem->caption,
                        '_has_thumbnail' => !empty($imageItem->thumbnail_data), // Helper for sorting
                    ])
                    ->sortByDesc('_has_thumbnail') // Sort by thumbnail presence (true first)
                    ->map(function($image) {
                        // Remove the helper field before returning
                        unset($image['_has_thumbnail']);
                        return $image;
                    })
                    ->values();

                $propertyArray = $property->toArray();

                // Add thumbnail field to main property object (first image with thumbnail, or null)
                $firstImageWithThumbnail = $images->first(fn($img) => !empty($img['thumbnail']));
                $propertyArray['thumbnail'] = $firstImageWithThumbnail['thumbnail'] ?? null;

                $propertyArray['images'] = $images;

                // Add deposit fee and parking fees
                $propertyArray['deposit_fee_amount'] = $property->deposit_fee_amount;
                $propertyArray['parking_fees'] = $property->parking_fees;

                // Get total rooms count
                $totalRooms = $property->rooms()->where('status', 1)->count();

                // Get available rooms count (status = 1 and rental_status != 1)
                $availableRooms = $property->rooms()
                    ->where('status', 1)
                    ->where(function($query) {
                        $query->where('rental_status', '!=', 1)
                              ->orWhereNull('rental_status');
                    })
                    ->count();

                $propertyArray['total_rooms'] = $totalRooms;
                $propertyArray['available_rooms'] = $availableRooms;

                // Remove image-related fields from the main property object
                unset(
                    $propertyArray['image_id'],
                    $propertyArray['image_data'],
                    $propertyArray['thumbnail_data'],
                    $propertyArray['caption']
                );

                return $propertyArray;
            })->first();
            
            return $this->respond([
                'data' => $groupedProperty
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
    
    /**
     * Create a new property
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'tags' => 'nullable|array',
                'province' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'subdistrict' => 'required|string|max:100',
                'village' => 'required|string|max:100',
                'postal_code' => 'required|string|max:10',
                'address' => 'required|string',
                'location' => 'required|string',
                'distance' => 'nullable|numeric',
                'price' => 'required|numeric',
                'features' => 'nullable|array',
                'attributes' => 'nullable|array',
                'image' => 'required|string',
                'status' => 'required|in:active,inactive',
                'created_by' => 'required|integer'
            ]);
            
            $property = Property::create($validated);
            
            return response()->json([
                'message' => 'Property created successfully',
                'created_id' => $property->idrec
            ], 201);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }
    
    /**
     * Update an existing property
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $property = Property::find($id);
            
            if (!$property) {
                return $this->respondNotFound('Property not found');
            }
            
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'tags' => 'nullable|array',
                'province' => 'sometimes|string|max:100',
                'city' => 'sometimes|string|max:100',
                'subdistrict' => 'sometimes|string|max:100',
                'village' => 'sometimes|string|max:100',
                'postal_code' => 'sometimes|string|max:10',
                'address' => 'sometimes|string',
                'location' => 'sometimes|string',
                'distance' => 'nullable|numeric',
                'price' => 'sometimes|numeric',
                'features' => 'nullable|array',
                'attributes' => 'nullable|array',
                'image' => 'sometimes|string',
                'status' => 'sometimes|in:active,inactive',
                'updated_by' => 'required|integer'
            ]);
            
            $property->update($validated);
            
            return $this->respond([
                'message' => 'Property updated successfully',
                'data' => $property
            ]);
        } catch (\Exception $e) {
            return $this->respondBadRequest($e->getMessage());
        }
    }
    
    /**
     * Delete a property
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $property = Property::find($id);

            if (!$property) {
                return $this->respondNotFound('Property not found');
            }

            $property->delete();

            return $this->respondDeleted('Property deleted successfully');
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * Get all property facilities with optional filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFacilities(Request $request)
    {
        try {
            $query = \DB::table('m_property_facility')
                ->select([
                    'idrec',
                    'facility',
                    'icon',
                    'description',
                    'category',
                    'status'
                ]);

            // Filter by status (default to active only)
            if ($request->has('status')) {
                $query->where('status', $request->status);
            } else {
                $query->where('status', 1);
            }

            // Filter by category if provided
            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            // Order by category then facility name
            $query->orderBy('category')->orderBy('facility');

            $facilities = $query->get();

            return $this->respond([
                'data' => $facilities
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    /**
     * Get a specific property facility by ID
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFacility($id)
    {
        try {
            $facility = \DB::table('m_property_facility')
                ->select([
                    'idrec',
                    'facility',
                    'icon',
                    'description',
                    'category',
                    'status'
                ])
                ->where('idrec', $id)
                ->first();

            if (!$facility) {
                return $this->respondNotFound('Facility not found');
            }

            return $this->respond([
                'data' => $facility
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
}
