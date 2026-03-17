<?php

namespace App\Http\Controllers\room;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoomController extends Controller {
    public function index(){
        return view("pages.room.index");
    }

    // Show rooms for a specific property
    public function showPropertyRooms($propertyId)
    {
        try {
            // Find property and eager load rooms
            $property = Property::with('rooms')->findOrFail($propertyId);
            
            // Format the rooms data
            $formattedRooms = $property->rooms->map(function($room) {
                return [
                    'id' => $room->idrec,
                    'name' => $room->name,
                    'type' => $room->type,
                    'descriptions' => $room->descriptions,
                    'facility' => is_string($room->facility) ? json_decode($room->facility, true) : ($room->facility ?? []),
                    'attachment' => is_string($room->attachment) ? json_decode($room->attachment, true) : ($room->attachment ?? []),
                    'periode' => is_string($room->periode) ? json_decode($room->periode, true) : ($room->periode ?? [
                        'daily' => false,
                        'monthly' => false
                    ]),
                    'status' => $room->status,
                    'rental_status' => $room->rental_status
                ];
            });

            // Format property data with null safety
            $formattedHouse = [
                'id' => $property->idrec ?? null,
                'name' => $property->name ?? null,
                'type' => $property->tags ?? null,
                'location' => $property->address ?? null,
                'subLocation' => ($property->subdistrict ?? '') . (isset($property->subdistrict, $property->city) ? ', ' : '') . ($property->city ?? ''),
                'distance' => isset($property->distance, $property->location) ? "{$property->distance} km dari {$property->location}" : null,
                'price_original_daily' => $property->price_original_daily,
                'price_original_monthly' => $property->price_original_monthly,
                'price_discounted_daily' => $property->price_discounted_daily,
                'price_discounted_monthly' => $property->price_discounted_monthly,
                'price' => [
                    'original' => (is_array($property->price ?? null) ? ($property->price['original'] ?? 0) : 0),
                    'discounted' => (is_array($property->price ?? null) ? ($property->price['discounted'] ?? 0) : 0)
                ],
                'features' => is_string($property->features ?? null) ? json_decode($property->features, true) ?? [] : ($property->features ?? []),
                'image' => $property->image ?? null,
                'image_2' => $property->image_2 ?? null,
                'image_3' => $property->image_3 ?? null,
                'attributes' => is_string($property->attributes ?? null) 
                    ? (json_decode($property->attributes, true) ?? [
                        'amenities' => [],
                        'room_facilities' => [],
                        'rules' => []
                    ]) 
                    : ($property->attributes ?? [
                        'amenities' => [],
                        'room_facilities' => [],
                        'rules' => []
                    ]),
                'description' => $property->description ?? null,
                'address' => [
                    'province' => $property->province ?? null,
                    'city' => $property->city ?? null,
                    'subdistrict' => $property->subdistrict ?? null,
                    'village' => $property->village ?? null,
                    'postal_code' => $property->postal_code ?? null,
                    'full_address' => $property->address ?? null
                ],
                'status' => $property->status ?? null,
                // 'rental_status' => $property->rental_status ?? null,
                'rooms' => $formattedRooms ?? []
            ];

            return view('pages.house.show', [
                'house' => $formattedHouse
            ]);

        } catch (Exception $e) {
            Log::error('Property not found or error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Property not found');
        }
    }

    // Store a new room for a property
    public function store(Request $request, $propertyId)
    {
        try {
            $property = Property::findOrFail($propertyId);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'descriptions' => 'nullable|string',
                'type' => 'required|string|max:50',
                'facility' => 'nullable|array',
                'attachment' => 'nullable|array',
                'status' => 'required|string|in:active,inactive'
            ]);

            $room = $property->rooms()->create([
                'property_name' => $property->name,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'descriptions' => $validated['descriptions'],
                'type' => $validated['type'],
                'facility' => json_encode($validated['facility'] ?? []),
                'attachment' => json_encode($validated['attachment'] ?? []),
                'status' => $validated['status'],
                'created_by' => Auth::user()->name ?? 'admin',
                'updated_by' => Auth::user()->name ?? 'admin'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update an existing room
    public function update(Request $request, $id)
    {
        try {
            $room = Room::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'descriptions' => 'nullable|string',
                'type' => 'sometimes|string|max:50',
                'facility' => 'nullable|array',
                'attachment' => 'nullable|array',
                'status' => 'sometimes|string|in:active,inactive'
            ]);

            $updateData = [
                ...$validated,
                'updated_by' => Auth::user()->name ?? 'admin',
                'updated_at' => Carbon::now()
            ];

            if (isset($validated['facility'])) {
                $updateData['facility'] = json_encode($validated['facility']);
            }

            if (isset($validated['attachment'])) {
                $updateData['attachment'] = json_encode($validated['attachment']);
            }

            if (isset($validated['name'])) {
                $updateData['slug'] = Str::slug($validated['name']);
            }

            $room->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete a room
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($slug)
    {
        try {
            // Find the room by slug and eager load the property relationship
            $room = Room::with('property')->where('slug', $slug)->firstOrFail();
            
            // Get room images using the accessor
            $roomImages = $room->images;
            $mainImage = !empty($roomImages[0]['image']) ? $roomImages[0]['image'] : $room->image;
            
            // Format the room data
            $formattedRoom = [
                'id' => $room->idrec,
                'property_id' => $room->property_id,
                'property_name' => $room->property_name,
                'slug' => $room->slug,
                'no' => $room->no,
                'name' => $room->name,
                'descriptions' => $room->descriptions,
                'type' => $room->type,
                'level' => $room->level,
                'price_original_daily' => $room->price_original_daily,
                'price_discounted_daily' => $room->price_discounted_daily,
                'price_original_monthly' => $room->price_original_monthly,
                'price_discounted_monthly' => $room->price_discounted_monthly,
                'admin_fees' => $room->admin_fees,
                'service_fees' => $room->service_fees,
                'facility' => $room->facility,
                'attachment' => is_string($room->attachment) ? json_decode(
                    $room->attachment, true
                    ) : (
                        $room->attachment ?? []
                    ),
                'image' => $mainImage,
                'images' => $roomImages,
                'periode' => is_string($room->periode) ? json_decode(
                    $room->periode, true
                    ) : (
                        $room->periode ?? [
                            'daily' => false,
                            'weekly' => false,
                            'monthly' => false
                        ]
                    ),
                'periode_daily' => $room->periode_daily,
                'periode_monthly' => $room->periode_monthly,
                'property' => [
                    'id' => $room->property->idrec,
                    'name' => $room->property_name,
                    'location' => $room->property->address,
                    'subLocation' => $room->property->subdistrict . ', ' . $room->property->city,
                    'deposit_fee' => $room->property->deposit_fee_amount ?? 0,
                    'parking_fees' => $room->property->parking_fees ?? [],
                ],
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'created_by' => $room->created_by,
                'updated_by' => $room->updated_by,
                'status' => $room->status,
                'rental_status' => $room->rental_status
            ];

            return view('pages.room.show', [
                'room' => $formattedRoom
            ]);

        } catch (Exception $e) {
            Log::error('Room not found or error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Room not found');
        }
    }
        public function showId($slug)
    {
        try {
            // Find the room by slug and eager load the property relationship
            $room = Room::with('property')->where('slug', $slug)->firstOrFail();
            
            // Get room images using the accessor
            $roomImages = $room->images;
            $mainImage = !empty($roomImages[0]['image']) ? $roomImages[0]['image'] : $room->image;
            
            // Format the room data
            $formattedRoom = [
                'id' => $room->idrec,
                'property_id' => $room->property_id,
                'property_name' => $room->property_name,
                'slug' => $room->slug,
                'name' => $room->name,
                'descriptions' => $room->descriptions,
                'type' => $room->type,
                'level' => $room->level,
                'price_original_daily' => $room->price_original_daily,
                'price_discounted_daily' => $room->price_discounted_daily,
                'price_original_monthly' => $room->price_original_monthly,
                'price_discounted_monthly' => $room->price_discounted_monthly,
                'admin_fees' => $room->admin_fees,
                'service_fees' => $room->service_fees,
                'facility' => $room->facilities,
                'attachment' => is_string($room->attachment) ? json_decode(
                    $room->attachment, true
                    ) : (
                        $room->attachment ?? []
                    ),
                'image' => $mainImage,
                'images' => $roomImages,
                'periode' => is_string($room->periode) ? json_decode(
                    $room->periode, true
                    ) : (
                        $room->periode ?? [
                            'daily' => false,
                            'weekly' => false,
                            'monthly' => false
                        ]
                    ),
                'periode_daily' => $room->periode_daily,
                'periode_monthly' => $room->periode_monthly,
                'property' => [
                    'id' => $room->property->idrec,
                    'name' => $room->property_name,
                    'location' => $room->property->address,
                    'subLocation' => $room->property->subdistrict . ', ' . $room->property->city,
                    'deposit_fee' => $room->property->deposit_fee_amount ?? 0,
                    'parking_fees' => $room->property->parking_fees ?? [],
                ],
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'created_by' => $room->created_by,
                'updated_by' => $room->updated_by,
                'status' => $room->status
            ];

            return view('pages.room.id.show', [
                'room' => $formattedRoom
            ]);

        } catch (Exception $e) {
            Log::error('Room not found or error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Room not found');
        }
    }
        public function showEn($slug)
    {
        try {
            // Find the room by slug and eager load the property relationship
            $room = Room::with('property')->where('slug', $slug)->firstOrFail();
            
            // Get room images using the accessor
            $roomImages = $room->images;
            $mainImage = !empty($roomImages[0]['image']) ? $roomImages[0]['image'] : $room->image;
            
            // Format the room data
            $formattedRoom = [
                'id' => $room->idrec,
                'property_id' => $room->property_id,
                'property_name' => $room->property_name,
                'slug' => $room->slug,
                'no' => $room->no,
                'name' => $room->name,
                'descriptions' => $room->descriptions,
                'type' => $room->type,
                'level' => $room->level,
                'price_original_daily' => $room->price_original_daily,
                'price_discounted_daily' => $room->price_discounted_daily,
                'price_original_monthly' => $room->price_original_monthly,
                'price_discounted_monthly' => $room->price_discounted_monthly,
                'admin_fees' => $room->admin_fees,
                'service_fees' => $room->service_fees,
                'facility' => $room->facility,
                'attachment' => is_string($room->attachment) ? json_decode(
                    $room->attachment, true
                    ) : (
                        $room->attachment ?? []
                    ),
                'image' => $mainImage,
                'images' => $roomImages,
                'periode' => is_string($room->periode) ? json_decode(
                    $room->periode, true
                    ) : (
                        $room->periode ?? [
                            'daily' => false,
                            'weekly' => false,
                            'monthly' => false
                        ]
                    ),
                'periode_daily' => $room->periode_daily,
                'periode_monthly' => $room->periode_monthly,
                'property' => [
                    'id' => $room->property->idrec,
                    'name' => $room->property_name,
                    'location' => $room->property->address,
                    'subLocation' => $room->property->subdistrict . ', ' . $room->property->city,
                    'deposit_fee' => $room->property->deposit_fee_amount ?? 0,
                    'parking_fees' => $room->property->parking_fees ?? [],
                ],
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'created_by' => $room->created_by,
                'updated_by' => $room->updated_by,
                'status' => $room->status
            ];

            return view('pages.room.en.show', [
                'room' => $formattedRoom
            ]);

        } catch (Exception $e) {
            Log::error('Room not found or error occurred: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Room not found');
        }
    }

    public function book(Request $request)
    {
        $validated = $request->validate([
            'check_in' => 'required|date|after:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:2',
        ]);

        // Add your booking logic here

        return redirect()->back()->with('success', 'Booking request submitted successfully!');
    }
}