<?php

namespace App\Http\Controllers\homepage;

/**
 * HomeController manages the main homepage of the Ulin Mahoni property website.
 * 
 * This controller is responsible for:
 * - Displaying all property types (Houses, Apartments, Villas, and Hotels)
 * - Managing the hero section with video/image support
 * - Formatting and categorizing property data for display
 * - Error handling and logging for property data retrieval
 * 
 * @package App\Http\Controllers\homepage
 */

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Property;
use App\Http\Controllers\promo\PromoController;

class HomeController extends Controller {
    protected $promoController;

    public function __construct(PromoController $promoController)
    {
        $this->promoController = $promoController;
    }

    /**
     * Display the homepage with categorized properties and hero media.
     *
     * This method:
     * - Sets up the hero media section (video/image)
     * - Fetches all active properties from the database
     * - Categorizes properties by type (House, Apartment, Villa, Hotel)
     * - Formats property data for display
     * - Returns a single view that adapts to the current locale set by SetLocale middleware
     * - The view uses app()->getLocale() and __() helper for translations
     * - Handles and logs any errors that occur during data retrieval
     *
     * @return \Illuminate\View\View Returns the homepage view with formatted property data
     */
    public function index()
    {
        $heroMedia = [
            'type' => 'video',
            'sources' => [
                'image' => 'images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg',
                'video' => 'images/assets/My_Movie.mp4'
                // 'video' => ''
            ]
        ];

        // Get promo banners data from DB
        $promos = \App\Models\PromoBanner::where('status', 1)
            ->with(['primaryImage', 'images'])
            ->orderByDesc('idrec')
            ->get()
            ->map(function ($promo) {
                // Get primary image or first image from images relationship
                $image = null;
                if ($promo->primaryImage) {
                    $image = $promo->primaryImage->image;
                } elseif ($promo->images->isNotEmpty()) {
                    $image = $promo->images->first()->image;
                }

                return [
                    'id' => $promo->idrec,
                    'title' => $promo->title,
                    'image' => $image,
                    'badge' => 'Promo',
                    'description' => $promo->descriptions,
                ];
            });

        try {
            // Get active properties (status = 1)
            $properties = Property::where('status', 1)
                ->orderBy('created_at', 'asc')
                ->get();

            // Prepare property data by type
            $propertyTypes = [
                'Kos' => [],
                'House' => [],
                'Apartment' => [],
                'Villa' => [],
                'Hotel' => [],
            ];

            foreach ($properties as $property) {
                $normalizedTag = ucfirst(strtolower(trim($property->tags)));

                if (array_key_exists($normalizedTag, $propertyTypes)) {
                    $propertyTypes[$normalizedTag][] = $this->formatProperty($property);
                }
            }

            // Prepare property data by city/area
            $propertyAreas = $this->getPropertiesByArea($properties);

            // Prepare property data by type and location for property-types component
            $propertyTypesByLocation = $this->getPropertiesByTypeAndLocation($propertyTypes);

            // Use the same view for all locales - the view will detect locale via app()->getLocale()
            return view("pages.homepage.index", [
                'kos' => $propertyTypes['Kos'],
                'houses' => $propertyTypes['House'],
                'apartments' => $propertyTypes['Apartment'],
                'villas' => $propertyTypes['Villa'],
                'hotels' => $propertyTypes['Hotel'],
                'heroMedia' => $heroMedia,
                'promos' => $promos,
                'propertyAreas' => $propertyAreas,
                // Property types by location
                'kosJakarta' => $propertyTypesByLocation['kos']['jakarta'],
                'kosBogor' => $propertyTypesByLocation['kos']['bogor'],
                'housesJakarta' => $propertyTypesByLocation['house']['jakarta'],
                'housesBogor' => $propertyTypesByLocation['house']['bogor'],
                'apartmentsJakarta' => $propertyTypesByLocation['apartment']['jakarta'],
                'apartmentsBogor' => $propertyTypesByLocation['apartment']['bogor'],
                'villasJakarta' => $propertyTypesByLocation['villa']['jakarta'],
                'villasBogor' => $propertyTypesByLocation['villa']['bogor'],
                'hotelsJakarta' => $propertyTypesByLocation['hotel']['jakarta'],
                'hotelsBogor' => $propertyTypesByLocation['hotel']['bogor'],
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching properties: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            // Use the same view for all locales - the view will detect locale via app()->getLocale()
            return view("pages.homepage.index", [
                'kos' => [],
                'houses' => [],
                'apartments' => [],
                'villas' => [],
                'hotels' => [],
                'heroMedia' => $heroMedia,
                'promos' => $promos,
                'propertyAreas' => [
                    'jakarta' => [],
                    'bogor' => [],
                    'tangerang' => [],
                    'depok' => [],
                    'bekasi' => []
                ],
                // Property types by location
                'kosJakarta' => [],
                'kosBogor' => [],
                'housesJakarta' => [],
                'housesBogor' => [],
                'apartmentsJakarta' => [],
                'apartmentsBogor' => [],
                'villasJakarta' => [],
                'villasBogor' => [],
                'hotelsJakarta' => [],
                'hotelsBogor' => [],
            ]);
        }
    }

    /**
     * Format a property model instance into a standardized array structure.
     *
     * @param Property $property The property model instance to format
     * @return array Formatted property data with the following structure:
     *               - id: int (Property ID)
     *               - name: string (Property name)
     *               - type: string (Property type - House/Apartment/Villa/Hotel)
     *               - location: string (Full address)
     *               - subLocation: string (Subdistrict and city)
     *               - distance: string|null (Distance from landmark)
     *               - price: array (Original and discounted prices)
     *               - features: array (Property features)
     *               - image: string (Image path or base64)
     *               - thumbnail: string (Thumbnail image path)
     *               - images: array (All property images)
     *               - status: int (Property status)
     */
    private function formatProperty($property)
    {
        // Get the lowest room price if available
        $roomPrice = $property->rooms()
            ->whereNotNull('price_original_monthly')
            ->where('price_original_monthly', '>', 0)
            ->min('price_original_monthly');

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

        // Get price data (already cast to array by the model)
        $price = is_array($property->price) ? $property->price : [];

        // Get features data (already cast to array by the model)
        $features = is_array($property->features) ? $property->features : [];

        // Get all property images using the accessor
        $images = $property->images;

        // Use the first image from the images array as the main image, fallback to property image
        $mainImage = !empty($images) ? $images[0]['image'] : $property->image;

        // Get thumbnail - use thumbnail_image accessor (where thumbnail = 1)
        $thumbnailImage = $property->thumbnail_image;
        $thumbnail = $thumbnailImage['image'] ?? null;

        // Fallback to first image if no thumbnail found
        if (!$thumbnail && !empty($images[0]['image'])) {
            $thumbnail = $images[0]['image'];
        }

        // Fallback to property image if still no thumbnail
        if (!$thumbnail) {
            $thumbnail = $property->image;
        }

        return [
            'id' => $property->idrec,
            'name' => $property->name,
            'type' => $property->tags,
            'location' => $property->address,
            'subLocation' => $property->subdistrict . ', ' . $property->city,
            'distance' => $property->distance ? "{$property->distance} km dari {$property->location}" : null,
            'price_original_daily' => $property->price_original_daily,
            'price_original_monthly' => $property->price_original_monthly,
            'price_discounted_daily' => $property->price_discounted_daily,
            'price_discounted_monthly' => $property->price_discounted_monthly,
            'room_price_original_monthly' => $roomPrice ?? $property->price_original_monthly,
            'price' => [
                'original' => $price['original'] ?? 0,
                'discounted' => $price['discounted'] ?? 0
            ],
            'features' => $features,
            'image' => $mainImage,  // Use the first image as main image
            'thumbnail' => $thumbnail,  // Thumbnail for listing/cards
            'images' => $images,    // Keep all images array for gallery
            'gender' => $property->gender,
            'status' => $property->status,
            'total_rooms' => $totalRooms,
            'available_rooms' => $availableRooms
        ];
    }

    /**
     * Get properties grouped by type and location.
     *
     * @param array $propertyTypes Array of properties grouped by type
     * @return array Properties grouped by type and location
     */
    private function getPropertiesByTypeAndLocation($propertyTypes)
    {
        $result = [];
        $cities = ['jakarta', 'bogor'];

        foreach ($propertyTypes as $type => $properties) {
            $typeLower = strtolower($type);
            $result[$typeLower] = [];

            foreach ($cities as $city) {
                $result[$typeLower][$city] = array_values(array_filter($properties, function($property) use ($city) {
                    return stripos($property['subLocation'] ?? '', $city) !== false;
                }));
            }
        }

        return $result;
    }

    /**
     * Get properties grouped by city/area.
     *
     * @param \Illuminate\Database\Eloquent\Collection $properties
     * @return array Properties grouped by area (jakarta, bogor, tangerang, depok, bekasi)
     */
    private function getPropertiesByArea($properties)
    {
        $areas = [
            'jakarta' => [],
            'bogor' => [],
            'tangerang' => [],
            'depok' => [],
            'bekasi' => []
        ];

        foreach ($properties as $property) {
            $city = strtolower(trim($property->city ?? ''));

            // Check which area the property belongs to
            if (str_contains($city, 'jakarta')) {
                $areas['jakarta'][] = $this->formatPropertyForArea($property);
            } elseif (str_contains($city, 'bogor')) {
                $areas['bogor'][] = $this->formatPropertyForArea($property);
            } elseif (str_contains($city, 'tangerang')) {
                $areas['tangerang'][] = $this->formatPropertyForArea($property);
            } elseif (str_contains($city, 'depok')) {
                $areas['depok'][] = $this->formatPropertyForArea($property);
            } elseif (str_contains($city, 'bekasi')) {
                $areas['bekasi'][] = $this->formatPropertyForArea($property);
            }
        }

        return $areas;
    }

    /**
     * Format a property for area cards display.
     *
     * @param Property $property
     * @return array
     */
    private function formatPropertyForArea($property)
    {
        // Get room count for the property
        $roomCount = $property->rooms()->count();

        // Get thumbnail image
        $thumbnailImage = $property->thumbnail_image;
        $thumbnail = $thumbnailImage['image'] ?? null;

        // Fallback to first image or property image
        if (!$thumbnail) {
            $images = $property->images;
            $thumbnail = !empty($images[0]['image']) ? $images[0]['image'] : $property->image;
        }

        return [
            'id' => $property->idrec,
            'name' => $property->name,
            'subdistrict' => $property->subdistrict,
            'city' => $property->city,
            'thumbnail' => $thumbnail,
            'room_count' => $roomCount
        ];
    }

    /**
     * Display the coming soon page.
     *
     * @return \Illuminate\View\View Returns the coming soon page view
     */
    public function comingSoon()
    {
        return view("pages.coming-soon.index");
    }
}