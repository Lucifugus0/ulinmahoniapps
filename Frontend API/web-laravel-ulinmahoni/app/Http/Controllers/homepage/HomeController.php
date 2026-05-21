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
use Illuminate\Support\Facades\DB;

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
        // <!-- Fetch random active tagline from database, fallback to translation key -->
        // Wrapped in try-catch: m_taglines/m_hero_videos may not exist on local DB (imported from prod without migrations)
        try {
            $taglineRow = DB::table('m_taglines')->where('status', 1)->inRandomOrder()->first();
        } catch (\Exception $e) {
            $taglineRow = null;
        }
        $heroTagline = $taglineRow ? $taglineRow->tagline : __('homepage.hero.subtitle');

        // <!-- Fetch random active tagline description from m_tagline_desc, fallback to translation key -->
        // Mirrors the m_taglines fetch above; paired with the tagline on the hero section.
        // Wrapped in try-catch: m_tagline_desc may not exist on local DB (imported from prod without migrations).
        try {
            $taglineDescRow = DB::table('m_tagline_desc')->where('status', 1)->inRandomOrder()->first();
        } catch (\Exception $e) {
            $taglineDescRow = null;
        }
        $heroDescription = $taglineDescRow ? $taglineDescRow->description : __('homepage.hero.description');

        // <!-- Fetch active hero video from database, fallback to default bundled video -->
        try {
            $activeVideo = DB::table('m_hero_videos')->where('status', 1)->first();
        } catch (\Exception $e) {
            $activeVideo = null;
        }
        $adminUrl = rtrim(env('ADMIN_URL', ''), '/');
        $heroMedia = [
            'type' => 'video',
            'sources' => [
                'image' => 'images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg',
                'video' => $activeVideo && $adminUrl ? $adminUrl . '/storage/' . $activeVideo->file_path : 'images/assets/My_Movie.mp4'
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

                /* Normalize how_to_claim to canonical [{title, desc}, ...] shape so the modal JS
                   can treat all banners uniformly. Legacy banners stored as ["string", ...] get
                   auto-titled "Langkah N" until the admin re-saves with explicit titles. */
                $rawSteps = $promo->how_to_claim ?? [];
                $steps = [];
                if (is_array($rawSteps)) {
                    foreach (array_values($rawSteps) as $i => $step) {
                        if (is_array($step) && (isset($step['title']) || isset($step['desc']))) {
                            $steps[] = [
                                'title' => isset($step['title']) ? (string) $step['title'] : '',
                                'desc' => isset($step['desc']) ? (string) $step['desc'] : '',
                            ];
                        } elseif (is_string($step) && trim($step) !== '') {
                            $steps[] = [
                                'title' => 'Langkah ' . ($i + 1),
                                'desc' => $step,
                            ];
                        }
                    }
                }

                return [
                    'id' => $promo->idrec,
                    'title' => $promo->title,
                    'image' => $image,
                    'badge' => 'Promo',
                    'description' => $promo->descriptions,
                    // <!-- promo_code is required by the homepage carousel modal so the
                    //      "Promo Code" copy-to-clipboard section + Claim button render. -->
                    'promo_code' => $promo->promo_code,
                    // <!-- how_to_claim is now [{title, desc}, ...] shape (admin-defined per step).
                    //      Legacy ["string", ...] auto-promoted with "Langkah N" titles above. -->
                    'how_to_claim' => $steps,
                    // <!-- terms_conditions added 2026-05-06 — falls back to hardcoded i18n
                    //      when null/empty so existing banners without terms still render. -->
                    'terms_conditions' => $promo->terms_conditions ?? [],
                ];
            });

        // <!-- Fetch active cities from the m_cities master table (admin-managed location list).
        //      Drives the homepage location filter tabs instead of a hardcoded Jakarta/Bogor list.
        //      Wrapped in try-catch: m_cities is owned by the Backend admin app and may be missing
        //      on a local DB imported without that migration — falls back to an empty list. -->
        try {
            $cities = DB::table('m_cities')
                ->where('status', '1')
                ->orderBy('idrec', 'asc')
                ->get(['city_name', 'slug']);
        } catch (\Exception $e) {
            $cities = collect();
        }

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

            // Prepare flat list of all properties with lat/lng for nearby sorting
            $nearbyProperties = $properties->map(function ($property) {
                return $this->formatPropertyForArea($property);
            })->values()->toArray();

            // Merge all property types into a single array for the "All" type tab
            $allProperties = array_merge(
                $propertyTypes['Kos'],
                $propertyTypes['House'],
                $propertyTypes['Apartment'],
                $propertyTypes['Villa'],
                $propertyTypes['Hotel']
            );

            // <!-- Build the property-by-location matrix keyed by city slug, driven by m_cities.
            //      Each property card is queried against its city name so the homepage location
            //      tabs filter live data instead of the previous hardcoded Jakarta/Bogor split. -->
            $propertiesByLocation = $this->getPropertiesByLocation($propertyTypes, $allProperties, $cities);

            // Use the same view for all locales - the view will detect locale via app()->getLocale()
            return view("pages.homepage.index", [
                'allProperties' => $allProperties,
                'kos' => $propertyTypes['Kos'],
                'houses' => $propertyTypes['House'],
                'apartments' => $propertyTypes['Apartment'],
                'villas' => $propertyTypes['Villa'],
                'hotels' => $propertyTypes['Hotel'],
                'heroMedia' => $heroMedia,
                'heroTagline' => $heroTagline,
                // <!-- heroDescription pairs with heroTagline; sourced from m_tagline_desc -->
                'heroDescription' => $heroDescription,
                'promos' => $promos,
                'propertyAreas' => $propertyAreas,
                'nearbyProperties' => $nearbyProperties,
                // <!-- Active cities from m_cities — render the location filter tabs -->
                'cities' => $cities,
                // <!-- Properties grouped by city slug then property type — render tab contents -->
                'propertiesByLocation' => $propertiesByLocation,
            ]);

        } catch (Exception $e) {
            Log::error('Error fetching properties: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            // <!-- Empty-state fallback: still expose the "all" location bucket so the
            //      location filter renders with just the "Semua Kota" tab. -->
            return view("pages.homepage.index", [
                'allProperties' => [],
                'kos' => [],
                'houses' => [],
                'apartments' => [],
                'villas' => [],
                'hotels' => [],
                'heroMedia' => $heroMedia,
                'heroTagline' => $heroTagline,
                // <!-- heroDescription pairs with heroTagline; sourced from m_tagline_desc -->
                'heroDescription' => $heroDescription,
                'promos' => $promos,
                'propertyAreas' => [
                    'jakarta' => [],
                    'bogor' => [],
                    'tangerang' => [],
                    'depok' => [],
                    'bekasi' => []
                ],
                'nearbyProperties' => [],
                'cities' => collect(),
                'propertiesByLocation' => [
                    'all' => [
                        'all' => [], 'kos' => [], 'house' => [],
                        'apartment' => [], 'villa' => [], 'hotel' => [],
                    ],
                ],
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

        /* Availability: daily rooms always available, monthly-only rooms check active bookings */
        $availableRooms = $property->rooms()
            ->where('status', 1)
            ->availableRooms()
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
     * Get properties grouped by city slug and property type for the homepage location filter.
     *
     * Builds a matrix the property-types component iterates over:
     *   [ 'all' => ['all' => [...], 'kos' => [...], ...],
     *     '<city-slug>' => ['all' => [...], 'kos' => [...], ...] ]
     *
     * The 'all' bucket holds every property; each city bucket holds only the properties
     * whose city name (matched against the formatted subLocation string) contains the
     * m_cities `city_name`. Matching is case-insensitive so "Jakarta" still captures
     * properties stored as "Jakarta Selatan", "Jakarta Pusat", etc.
     *
     * @param array $propertyTypes  Properties grouped by type (Kos/House/Apartment/Villa/Hotel)
     * @param array $allProperties  Flat list of every formatted property
     * @param \Illuminate\Support\Collection $cities  Active rows from m_cities
     * @return array Properties grouped by city slug then property type
     */
    private function getPropertiesByLocation($propertyTypes, $allProperties, $cities)
    {
        // 'all' location bucket — every property, untouched by city filtering
        $result = [
            'all' => [
                'all' => $allProperties,
                'kos' => $propertyTypes['Kos'],
                'house' => $propertyTypes['House'],
                'apartment' => $propertyTypes['Apartment'],
                'villa' => $propertyTypes['Villa'],
                'hotel' => $propertyTypes['Hotel'],
            ],
        ];

        // One bucket per active city from m_cities — cards queried by city name
        foreach ($cities as $city) {
            $cityName = $city->city_name ?? '';
            $slug = $city->slug ?? '';
            if ($slug === '') {
                continue;
            }

            // Filter a list of formatted properties down to those in this city
            $filterByCity = function ($properties) use ($cityName) {
                return array_values(array_filter($properties, function ($property) use ($cityName) {
                    return $cityName !== ''
                        && stripos($property['subLocation'] ?? '', $cityName) !== false;
                }));
            };

            $result[$slug] = [
                'all' => $filterByCity($allProperties),
                'kos' => $filterByCity($propertyTypes['Kos']),
                'house' => $filterByCity($propertyTypes['House']),
                'apartment' => $filterByCity($propertyTypes['Apartment']),
                'villa' => $filterByCity($propertyTypes['Villa']),
                'hotel' => $filterByCity($propertyTypes['Hotel']),
            ];
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
            'room_count' => $roomCount,
            // GPS coordinates for nearby sorting on the client side
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
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