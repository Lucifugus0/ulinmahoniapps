<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Room;
use App\Models\Property;
use Illuminate\Http\Request;


class RoomController extends ApiController
{
    // GET /api/rooms/property/{property_id}
    public function byPropertyId($property_id)
    {
        try {
            // Get property parking fees and deposit fee
            $property = Property::find($property_id);
            $parkingFees = $property ? $property->parking_fees : [];
            $depositFee = $property ? $property->deposit_fee_amount : null;

            $query = Room::query()
                ->where('m_rooms.property_id', $property_id)
                ->leftJoin('m_room_images', 'm_room_images.room_id', '=', 'm_rooms.idrec')
                ->select([
                    'm_rooms.*',
                    'm_room_images.idrec as image_id',
                    'm_room_images.image as image_data',
                    'm_room_images.thumbnail as thumbnail_data',
                    'm_room_images.caption',
                ]);

            $rooms = $query->get();

            // Group images by room
            $groupedRooms = $rooms->groupBy('idrec')->map(function ($roomGroup) use ($parkingFees, $depositFee) {
                $room = $roomGroup->first();

                // Map and sort images - images with thumbnails come first
                $images = $roomGroup->filter(function ($item) {
                    return $item->image_id !== null;
                })->map(function ($imageItem) {
                    return [
                        'id' => $imageItem->image_id,
                        'image_data' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                        'thumbnail' => $imageItem->thumbnail_data ? env('ADMIN_URL') . '/storage/' . $imageItem->image_data : null,
                        'caption' => $imageItem->caption,
                        '_has_thumbnail' => !empty($imageItem->thumbnail_data), // Helper for sorting
                    ];
                })
                ->sortByDesc('_has_thumbnail') // Sort by thumbnail presence (true first)
                ->map(function($image) {
                    // Remove the helper field before returning
                    unset($image['_has_thumbnail']);
                    return $image;
                })
                ->values();

                $roomArray = $room->toArray();

                // Add thumbnail field to main room object (first image with thumbnail, or null)
                $firstImageWithThumbnail = $images->first(fn($img) => !empty($img['thumbnail']));
                $roomArray['thumbnail'] = $firstImageWithThumbnail['thumbnail'] ?? null;

                $roomArray['images'] = $images;
                $roomArray['parking_fees'] = $parkingFees;
                $roomArray['deposit_fee'] = $depositFee;

                /* Multi-Tier Pricing: add additive pricing fields to room response */
                $roomArray['price_weekday'] = $room->price_weekday;
                $roomArray['price_weekend'] = $room->price_weekend;
                $roomArray['price_original_annual'] = $room->price_original_annual;
                $roomArray['periode_annual'] = $room->periode_annual;
                // Check global calendar for any active seasonal/holiday dates
                $roomArray['has_seasonal_pricing'] = \DB::table('m_calendar_dates')
                    ->whereIn('date_type', ['holiday', 'high_season', 'low_season'])
                    ->where('status', 1)
                    ->exists();

                // <!-- Multi-language: add parsed descriptions for mobile app locale selection -->
                $roomArray['descriptions_parsed'] = \App\Helpers\DescriptionHelper::parse($room->descriptions ?? '');
                // <!-- Backward compat: strip XML tags from raw field so old apps show plain ID text -->
                $roomArray['descriptions'] = \App\Helpers\DescriptionHelper::get($room->descriptions ?? '', 'id');

                // Remove image-related fields from the main room object
                unset(
                    $roomArray['image_id'],
                    $roomArray['image_data'],
                    $roomArray['thumbnail_data'],
                    $roomArray['caption']
                );

                return $roomArray;
            })->values();

            return $this->respond([
                'data' => $groupedRooms
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }
    // GET /api/v1/rooms
    public function index(Request $request)
    {
        try {
            $query = Room::query();

            // Join with room images
            $query->leftJoin('m_room_images', 'm_room_images.room_id', '=', 'm_rooms.idrec')
                ->select([
                    'm_rooms.*',
                    'm_room_images.idrec as image_id',
                    'm_room_images.image as image_data',
                    'm_room_images.caption',
                ]);

            // Add filters if provided
            if ($request->has('idrec')) {
                $query->where('m_rooms.idrec', $request->idrec);
            }

            $rooms = $query->get();

            // Pre-load properties for parking fees
            $propertyIds = $rooms->pluck('property_id')->unique()->filter();
            $properties = Property::whereIn('idrec', $propertyIds)->get()->keyBy('idrec');

            // Group images by room
            $groupedRooms = $rooms->groupBy('idrec')->map(function ($roomGroup) use ($properties) {
                $room = $roomGroup->first();
                $images = $roomGroup->filter(function ($item) {
                    return $item->image_id !== null;
                })->map(function ($imageItem) {
                    return [
                        'id' => $imageItem->image_id,
                        'image_data' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                        'caption' => $imageItem->caption,
                    ];
                })->values();

                $roomArray = $room->toArray();
                $roomArray['images'] = $images;

                // Add parking fees and deposit fee from property
                $property = $properties->get($room->property_id);
                $roomArray['parking_fees'] = $property ? $property->parking_fees : [];
                $roomArray['deposit_fee'] = $property ? $property->deposit_fee_amount : null;

                /* Multi-Tier Pricing: add additive pricing fields to room response */
                $roomArray['price_weekday'] = $room->price_weekday;
                $roomArray['price_weekend'] = $room->price_weekend;
                $roomArray['price_original_annual'] = $room->price_original_annual;
                $roomArray['periode_annual'] = $room->periode_annual;
                // Check global calendar for any active seasonal/holiday dates
                $roomArray['has_seasonal_pricing'] = \DB::table('m_calendar_dates')
                    ->whereIn('date_type', ['holiday', 'high_season', 'low_season'])
                    ->where('status', 1)
                    ->exists();

                // <!-- Multi-language: add parsed descriptions for mobile app locale selection -->
                $roomArray['descriptions_parsed'] = \App\Helpers\DescriptionHelper::parse($room->descriptions ?? '');
                // <!-- Backward compat: strip XML tags from raw field so old apps show plain ID text -->
                $roomArray['descriptions'] = \App\Helpers\DescriptionHelper::get($room->descriptions ?? '', 'id');

                // Remove image-related fields from the main room object
                unset(
                    $roomArray['image_id'],
                    $roomArray['image_data'],
                    $roomArray['caption']
                );

                return $roomArray;
            })->values();

            return $this->respond([
                'data' => $groupedRooms
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // GET /api/v1/rooms/{id}
    public function show($id)
    {
        try {
            $room = Room::leftJoin('m_room_images', 'm_room_images.room_id', '=', 'm_rooms.idrec')
                ->where('m_rooms.idrec', $id)
                ->select([
                    'm_rooms.*',
                    'm_room_images.idrec as image_id',
                    'm_room_images.image as image_data',
                    'm_room_images.thumbnail as thumbnail_data',
                    'm_room_images.caption',
                ])
                ->get();

            if ($room->isEmpty()) {
                return $this->respondNotFound('Room not found');
            }

            // Get property for parking fees and deposit fee
            $propertyId = $room->first()->property_id;
            $property = Property::find($propertyId);
            $parkingFees = $property ? $property->parking_fees : [];
            $depositFee = $property ? $property->deposit_fee_amount : null;

            // Group images by room
            $groupedRoom = $room->groupBy('idrec')->map(function ($roomGroup) use ($parkingFees, $depositFee) {
                $room = $roomGroup->first();

                // Map and sort images - images with thumbnails come first
                $images = $roomGroup->filter(function ($item) {
                    return $item->image_id !== null;
                })->map(function ($imageItem) {
                    return [
                        'id' => $imageItem->image_id,
                        'image_data' => env('ADMIN_URL') . '/storage/' . $imageItem->image_data,
                        'thumbnail' => $imageItem->thumbnail_data ? env('ADMIN_URL') . '/storage/' . $imageItem->image_data : null,
                        'caption' => $imageItem->caption,
                        '_has_thumbnail' => !empty($imageItem->thumbnail_data), // Helper for sorting
                    ];
                })
                ->sortByDesc('_has_thumbnail') // Sort by thumbnail presence (true first)
                ->map(function($image) {
                    // Remove the helper field before returning
                    unset($image['_has_thumbnail']);
                    return $image;
                })
                ->values();

                $roomArray = $room->toArray();

                // Add thumbnail field to main room object (first image with thumbnail, or null)
                $firstImageWithThumbnail = $images->first(fn($img) => !empty($img['thumbnail']));
                $roomArray['thumbnail'] = $firstImageWithThumbnail['thumbnail'] ?? null;

                $roomArray['images'] = $images;
                $roomArray['parking_fees'] = $parkingFees;
                $roomArray['deposit_fee'] = $depositFee;

                // <!-- Multi-language: add parsed descriptions for mobile app locale selection -->
                $roomArray['descriptions_parsed'] = \App\Helpers\DescriptionHelper::parse($room->descriptions ?? '');
                // <!-- Backward compat: strip XML tags from raw field so old apps show plain ID text -->
                $roomArray['descriptions'] = \App\Helpers\DescriptionHelper::get($room->descriptions ?? '', 'id');

                // Remove image-related fields from the main room object
                unset(
                    $roomArray['image_id'],
                    $roomArray['image_data'],
                    $roomArray['thumbnail_data'],
                    $roomArray['caption']
                );

                return $roomArray;
            })->first();
            
            return $this->respond([
                'data' => $groupedRoom
            ]);
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    // POST /api/v1/rooms
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|integer',
            'property_name' => 'required|string',
            'name' => 'required|string',
            'slug' => 'nullable|string',
            'descriptions' => 'nullable|string',
            'type' => 'nullable|string',
            'level' => 'nullable|string',
            'facility' => 'nullable|array',
            'attachment' => 'nullable|array',
            'periode' => 'nullable|array',
            'status' => 'nullable|string',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
            'price' => 'nullable|array'
        ]);
        $room = Room::create($data);
        return response()->json($room, 201);
    }

    // PUT/PATCH /api/v1/rooms/{id}
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }
        $data = $request->validate([
            'property_id' => 'sometimes|integer',
            'property_name' => 'sometimes|string',
            'name' => 'sometimes|string',
            'slug' => 'nullable|string',
            'descriptions' => 'nullable|string',
            'type' => 'nullable|string',
            'level' => 'nullable|string',
            'facility' => 'nullable|array',
            'attachment' => 'nullable|array',
            'periode' => 'nullable|array',
            'status' => 'nullable|string',
            'created_by' => 'nullable|integer',
            'updated_by' => 'nullable|integer',
            'price' => 'nullable|array'
        ]);
        $room->update($data);
        return response()->json($room);
    }

    // DELETE /api/v1/rooms/{id}
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }
        $room->delete();
        return response()->json(['message' => 'Room deleted']);
    }

    /**
     * <!-- Multi-Tier Pricing: Price preview endpoint for daily bookings -->
     * <!-- Returns per-date price breakdown for a given check-in/check-out range -->
     * <!-- Used by Frontend Web and Mobile App to show itemized pricing before booking -->
     *
     * GET /api/v1/rooms/{roomId}/price-preview?check_in=2026-04-01&check_out=2026-04-05
     */
    public function pricePreview(Request $request, $roomId)
    {
        try {
            $request->validate([
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
            ]);

            $room = Room::find($roomId);
            if (!$room) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Room not found',
                ], 404);
            }

            $checkIn = \Carbon\Carbon::parse($request->check_in);
            $checkOut = \Carbon\Carbon::parse($request->check_out);

            /* Query per-date prices from m_room_prices */
            $datePrices = \DB::table('m_room_prices')
                ->where('room_id', $roomId)
                ->where('date', '>=', $checkIn->toDateString())
                ->where('date', '<', $checkOut->toDateString())
                ->where('status', 1)
                ->orderBy('date')
                ->get(['date', 'price', 'price_type']);

            if ($datePrices->isEmpty()) {
                /* Fallback: no per-date prices — use flat daily rate */
                $dailyPrice = $room->price_original_daily;
                $days = $checkIn->diffInDays($checkOut);
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'room_id' => (int) $roomId,
                        'check_in' => $checkIn->toDateString(),
                        'check_out' => $checkOut->toDateString(),
                        'total_days' => $days,
                        'total_price' => $dailyPrice * $days,
                        'is_flat_rate' => true,
                        'breakdown' => [],
                    ],
                ]);
            }

            /* Build per-date breakdown with day names and optional labels */
            // Use global calendar dates instead of per-room pricing rules for labels
            $calendarLabels = \DB::table('m_calendar_dates')
                ->where('status', 1)
                ->whereIn('date_type', ['holiday', 'high_season', 'low_season'])
                ->whereBetween('date', [$checkIn->toDateString(), $checkOut->toDateString()])
                ->pluck('label', 'date')
                ->toArray();

            $breakdown = $datePrices->map(function ($p) use ($calendarLabels) {
                $date = \Carbon\Carbon::parse($p->date);
                $dateStr = $date->toDateString();

                /* Find matching label from global calendar */
                $label = $calendarLabels[$dateStr] ?? null;

                return [
                    'date' => $dateStr,
                    'price' => (float) $p->price,
                    'type' => $p->price_type ?? 'weekday',
                    'day_name' => $date->format('l'),
                    'label' => $label,
                ];
            })->values()->toArray();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'room_id' => (int) $roomId,
                    'check_in' => $checkIn->toDateString(),
                    'check_out' => $checkOut->toDateString(),
                    'total_days' => count($breakdown),
                    'total_price' => array_sum(array_column($breakdown, 'price')),
                    'is_flat_rate' => false,
                    'breakdown' => $breakdown,
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error',
            ], 500);
        }
    }
}
