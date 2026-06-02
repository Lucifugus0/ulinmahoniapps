<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'm_rooms';
    protected $primaryKey = 'idrec';

    protected $fillable = [
        'idrec',
        'property_id',
        'property_name',
        'name',
        'slug',
        'descriptions',
        'type',
        'level',
        'facility',
        'image',
        'image2',
        'image3',
        'periode',
        'periode_daily',
        'periode_monthly',
        'status',
        'rental_status',
        'created_by',
        'updated_by',
        'price',
        'price_original_daily',
        'price_discounted_daily',
        'price_original_monthly',
        'price_discounted_monthly',
        /* Multi-Tier Pricing: annual pricing + weekday/weekend base prices */
        'periode_annual',
        'price_original_annual',
        'price_discounted_annual',
        'price_weekday',
        'price_weekend',
        'admin_fees',
    ];

    protected $casts = [
        'facility' => 'json',
        'periode' => 'json',
        'price' => 'json'
    ];

    /**
     * Get the price_original_daily attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getPriceOriginalDailyAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get the price_original_monthly attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getPriceOriginalMonthlyAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get the price_discounted_daily attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getPriceDiscountedDailyAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get the price_discounted_monthly attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getPriceDiscountedMonthlyAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get the admin_fees attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getAdminFeesAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }


    /**
     * Compute whether a room is available based on rental type.
     * Daily rooms (periode_daily = 1) are always available.
     * Monthly-only rooms check t_booking + t_transactions for active bookings.
     *
     * @param int $roomId
     * @param bool|int $periodDaily
     * @param \Carbon\Carbon|string|null $checkIn
     * @param \Carbon\Carbon|string|null $checkOut
     * @return bool
     */
    public static function computeAvailability($roomId, $periodDaily, $checkIn = null, $checkOut = null)
    {
        // Daily rooms are always considered available
        if ($periodDaily) {
            return true;
        }

        // Monthly-only rooms: check for conflicting active bookings
        $query = \DB::table('t_booking')
            ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
            ->where('t_booking.room_id', $roomId)
            ->where('t_booking.status', 1)
            ->whereNull('t_booking.check_out_at')
            ->whereNotIn('t_transactions.transaction_status', ['cancelled', 'expired', 'checked_out', 'rejected']);

        if ($checkIn && $checkOut) {
            // Date range specified: check for overlapping bookings
            $query->where('t_transactions.check_in', '<', $checkOut)
                  ->where('t_transactions.check_out', '>', $checkIn);
        }
        // No dates: any active not-yet-checked-out booking blocks the room.
        // The scheduled check_out passing alone does NOT release the room —
        // admin must perform the physical checkout (sets check_out_at) first.
        // Renewals naturally extend occupancy because the renewal is its own
        // active row in t_booking with check_out_at NULL.

        return !$query->exists();
    }

    /**
     * Query scope: filter to available rooms only.
     * Daily rooms (periode_daily = 1) always pass.
     * Monthly-only rooms must have no active not-yet-checked-out booking
     * (i.e. admin must have performed the physical checkout).
     */
    public function scopeAvailableRooms($query)
    {
        return $query->where(function ($q) {
            // Daily rooms are always available
            $q->where('m_rooms.periode_daily', 1)
              // Monthly rooms: no active booking still occupying the room.
              // Past check_out alone does NOT free the room — admin must check out.
              ->orWhereNotExists(function ($sub) {
                  $sub->select(\DB::raw(1))
                      ->from('t_booking')
                      ->join('t_transactions', 't_booking.order_id', '=', 't_transactions.order_id')
                      ->whereColumn('t_booking.room_id', 'm_rooms.idrec')
                      ->where('t_booking.status', 1)
                      ->whereNull('t_booking.check_out_at')
                      ->whereNotIn('t_transactions.transaction_status', ['cancelled', 'expired', 'checked_out', 'rejected']);
              });
        });
    }

    /**
     * Get the property that owns the room.
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id', 'idrec');
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get all images for the room.
     *
     * @return array
     */
    public function getImagesAttribute()
    {
        try {
            $images = \DB::select("
                SELECT 
                    idrec,
                    room_id,
                    image,
                    thumbnail,
                    caption
                FROM m_room_images 
                WHERE room_id = ? 
            ", [$this->idrec]);

            if (empty($images)) {
                return [];
            }

            // Process each image and add thumbnail field
            $processedImages = array_filter(array_map(function($image) {
                try {
                    if (!is_object($image) || !isset($image->idrec)) {
                        return null;
                    }

                    return [
                        'id' => $image->idrec ?? null,
                        'room_id' => $image->room_id ?? $this->idrec,
                        // 'image' => $this->getProcessedImage($image->image ?? null),
                        'image' => $image->image ?? null,
                        'thumbnail' => $image->thumbnail ?? null,
                        'caption' => $image->caption ?? '',
                        '_has_thumbnail' => !empty($image->thumbnail) // Helper for sorting
                    ];
                } catch (\Exception $e) {
                    // Log error and skip this image
                    \Log::error('Error processing room image: ' . $e->getMessage());
                    return null;
                }
            }, $images));

            // Sort images - images with thumbnails come first
            usort($processedImages, function($a, $b) {
                return ($b['_has_thumbnail'] ?? false) <=> ($a['_has_thumbnail'] ?? false);
            });

            // Remove the helper field from final result
            return array_map(function($image) {
                unset($image['_has_thumbnail']);
                return $image;
            }, $processedImages);

        } catch (\Exception $e) {
            \Log::error('Error fetching room images: ' . $e->getMessage());
            return [];
        }
    }

    // public function getFacilitiesAttribute($value)
    // {
    //     try {
    //         // Get the facility IDs from the facility column (JSON array)
    //         $facilityIds = json_decode($value, true) ?? [];
            
    //         if (empty($facilityIds) || !is_array($facilityIds)) {
    //             return [];
    //         }

    //         // Convert to integers for safe database query
    //         $facilityIds = array_map('intval', $facilityIds);

    //         // Try to get facility names from a facilities table if it exists
    //         try {
    //             $facilityRecords = \DB::select("
    //                 SELECT idrec, facility_name
    //                 FROM m_facilities
    //                 WHERE idrec IN (" . implode(',', array_fill(0, count($facilityIds), '?')) . ")
    //             ", $facilityIds);
                
    //             $facilities = [];
    //             foreach ($facilityRecords as $record) {
    //                 $facilities[] = $record->facility_name;
    //             }
                
    //             // If we found facilities, return them
    //             if (!empty($facilities)) {
    //                 return $facilities;
    //             }
    //         } catch (\Exception $e) {
    //             // Fallback to predefined mapping if facility table doesn't exist
    //         }
            
    //         // Fallback mapping for common facility IDs
    //         $facilityNames = [
    //             '1' => 'AC',
    //             '2' => 'Wi-Fi',
    //             '3' => 'Parkir',
    //             '4' => 'TV',
    //             '5' => 'Kunci Digital',
    //             '6' => 'Kolam Renang',
    //             '7' => 'Gym',
    //             '8' => 'Dapur',
    //             '9' => 'Kulkas',
    //             '10' => 'Mesin Cuci'
    //         ];
            
    //         $facilities = [];
    //         foreach ($facilityIds as $id) {
    //             if (isset($facilityNames[(string)$id])) {
    //                 $facilities[] = $facilityNames[(string)$id];
    //             }
    //         }
            
    //         return $facilities;
            
    //     } catch (\Exception $e) {
    //         \Log::error('Error processing room facilities: ' . $e->getMessage() . ' for room_id: ' . $this->idrec);
    //         return [];
    //     }
    // }

    /**
     * Process the image data to ensure it's properly base64 encoded.
     *
     * @param  mixed  $imageData
     * @return string|null
     */
    protected function getProcessedImage($imageData)
    {
        if (!$imageData) {
            return null;
        }

        // If already base64, return as is
        if (base64_encode(base64_decode($imageData, true)) === $imageData) {
            return $imageData;
        }

        // If it's a file path, read and encode it
        if (is_string($imageData) && file_exists($imageData)) {
            $imageData = file_get_contents($imageData);
            return base64_encode($imageData);
        }

        // If it's binary data, encode it
        return base64_encode($imageData);
    }

    /**
     * Get the facility attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getFacilityAttribute($value)
    {
        // return json_decode($value, true) ?? [];
        try {
            // Get the facility IDs from the facility column (JSON array)
            $facilityIds = json_decode($value, true) ?? [];
            // \Log::info('Facility IDs: ', $facilityIds);
            if (empty($facilityIds) || !is_array($facilityIds)) {
                return [];
            }

            // Convert to integers for safe database query
            $facilityIds = array_map('intval', $facilityIds);

            // Try to get facility names and icons from a facilities table if it exists
            try {
                $placeholders = implode(',', array_fill(0, count($facilityIds), '?'));

                $facilityRecords = \DB::select("
                    SELECT idrec, facility, icon
                    FROM m_room_facility
                    WHERE idrec IN ($placeholders)
                ", $facilityIds);

                // <!-- Multi-language: parse facility name by current locale with fallback -->
                $locale = app()->getLocale();
                $facilities = [];
                foreach ($facilityRecords as $record) {
                    $facilities[] = [
                        'name' => \App\Helpers\DescriptionHelper::get($record->facility ?? '', $locale),
                        'name_parsed' => \App\Helpers\DescriptionHelper::parse($record->facility ?? ''),
                        'icon' => $record->icon ?? null
                    ];
                }

                // If we found facilities, return them
                if (!empty($facilities)) {
                    return $facilities;
                }
            } catch (\Exception $e) {
                // Fallback to predefined mapping if facility table doesn't exist
            }
            
            // Fallback mapping for common facility IDs
            $facilityNames = [
                '1' => ['name' => 'AC', 'icon' => 'mynaui:air-conditioner'],
                '2' => ['name' => 'Wi-Fi', 'icon' => 'mdi:wifi'],
                '3' => ['name' => 'TV Kabel', 'icon' => 'mdi:television'],
                '4' => ['name' => 'Kamar Mandi', 'icon' => 'mdi:shower'],
                '5' => ['name' => 'Meja & Kursi', 'icon' => 'mdi:desk'],
                '6' => ['name' => 'F', 'icon' => null],
                '7' => ['name' => 'G', 'icon' => null],
                '8' => ['name' => 'H', 'icon' => null],
                '9' => ['name' => 'I', 'icon' => null],
                '10' => ['name' => 'J', 'icon' => null]
            ];

            $facilities = [];
            foreach ($facilityIds as $id) {
                if (isset($facilityNames[(string)$id])) {
                    $facilities[] = $facilityNames[(string)$id];
                }
            }

            return $facilities;
            
        } catch (\Exception $e) {
            \Log::error('Error processing room facilities: ' . $e->getMessage() . ' for room_id: ' . $this->idrec);
            return [];
        }
    }

    /**
     * Get the attachment attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getAttachmentAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Get the periode attribute.
     *
     * @param  string  $value
     * @return array
     */
    public function getPeriodeAttribute($value)
    {
        if (empty($value)) {
            return [
                'daily' => false,
                'weekly' => false,
                'monthly' => false
            ];
        }

        // If it's a JSON string, decode it
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        // If we have an array, use its values
        if (is_array($value)) {
            return [
                'daily' => $value['daily'] ?? false,
                'weekly' => $value['weekly'] ?? false,
                'monthly' => $value['monthly'] ?? false
            ];
        }
        
        return [
            'daily' => false,
            'weekly' => false,
            'monthly' => false
        ];
    }
} 