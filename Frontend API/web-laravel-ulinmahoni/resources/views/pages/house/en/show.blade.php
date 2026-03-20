<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $house['name'] }} - Property Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
    .image-gallery {
            --gap: 1rem;
            --transition: all 0.3s ease;
        }

        /* Responsive height for main image container */
        .mobile-responsive-height {
            height: 45rem; /* Desktop default */
        }

        @media (max-width: 768px) {
            .mobile-responsive-height {
                height: 20rem !important; /* Mobile height */
            }
        }

        /* Liquid glass gallery item — rounded corners and glass shadow tokens */
        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: var(--radius-lg, 1.75rem);
            box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gallery-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--glass-shadow-hover, 0 16px 48px rgba(0, 0, 0, 0.15));
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            transition: var(--transition);
        }

        .gallery-item.main-image {
            height: 400px;
        }

        .gallery-item.main-image img {
            width: 100%;
        }
        

        .gallery-item.side-image {
            height: 295px;
        }

        .gallery-item.side-image img {
            width: 70%;
        }

        .gallery-item::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.2) 100%);
            opacity: 0;
            transition: var(--transition);
            pointer-events: none;
        }

        .gallery-item:hover::after {
            opacity: 1;
        }

        .type-badge {
            position: absolute;
            top: 1rem;
            z-index: 10;
            left: 1rem;
            padding: 0.375rem 0.75rem;
            background-color: rgb(13 148 136);
            color: white;
            border-radius: 9999px;
            font-size: 0.875rem;
            line-height: 1.25rem;
            transform: translateY(-5px);
            opacity: 0;
            transition: var(--transition);
        }

        .gallery-item:hover .type-badge {
            transform: translateY(0);
            opacity: 1;
        }

        .image-count {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
            z-index: 10;
            padding: 0.25rem 0.5rem;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            opacity: 0;
            transform: translateY(5px);
            transition: var(--transition);
        }

        .gallery-item:hover .image-count {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('en.homepage') }}" class="hover:text-gray-700">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('en.homepage') }}" class="hover:text-gray-700">Properties</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">{{ $house['name'] }}</li>
                </ol>
            </nav>

            <!-- Image Gallery Section -->
            <div x-data="{ showModal: false, modalImg: '', modalAlt: '' }" class="image-gallery relative mb-8">
                <!-- Image Popup Modal -->
                <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" style="display: none;" @click.away="showModal=false" @click.self="showModal=false" @keydown.escape.window="showModal=false">
                    <img :src="modalImg" :alt="modalAlt" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white object-contain" @click.stop>
                    <button @click="showModal=false" class="absolute top-4 right-6 text-white text-3xl font-bold focus:outline-none">&times;</button>
                </div>
                
                <!-- Main Image with overlay badges -->
                <div class="relative bg-gray-100 rounded-lg overflow-hidden w-full mobile-responsive-height">
                    @if($primaryImage)
                        {{-- <img src="data:image/jpeg;base64,{{ $primaryImage }}"
                            alt="{{ $house['name'] ?? 'Property Image' }}"
                            class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                            @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'"
                            onerror="this.onerror=null;
                            this.src='{{ asset('images/placeholder-property.jpg') }}';"> --}}
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage ?? $primaryImage }}"
                            alt="{{ $house['name'] ?? 'Property Image' }}"
                            class="w-full h-full object-fill object-center"
                            @click.prevent="showModal=true; modalImg='{{ env('ADMIN_URL') }}/storage/{{ $mainImage ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                            <span class="ml-2 font-medium text-gray-500">No Image</span>
                        </div>
                    @endif

                    @if($totalImages > 1)
                        <div class="absolute bottom-4 right-4">
                            <span class="bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                                <i class="fas fa-camera mr-1"></i> {{ $totalImages }}
                            </span>
                        </div>
                    @endif

                    @if(isset($house['type']))
                        <span class="absolute top-4 left-4 bg-teal-600 text-white px-3 py-1 rounded-full text-sm">
                            {{ ucfirst($house['type']) }}
                        </span>
                    @endif

                    <!-- Thumbnails (if multiple images) -->
                    @if(count($secondaryImages) > 0)
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent py-4 px-6">
                            <div class="flex space-x-3 overflow-x-auto">
                                <!-- Main image as first thumb -->
                                <div class="flex-shrink-0 w-32 h-20 rounded overflow-hidden border-2 border-white shadow-md mb-2 flex-none">
                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage ?? $primaryImage }}"
                                        alt="{{ $house['name'] ?? 'Property Image' }} - Main"
                                        @click.prevent="showModal=true; modalImg='{{ env('ADMIN_URL') }}/storage/{{ $mainImage ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'"
                                        class="w-full h-full object-cover cursor-pointer hover:opacity-80">
                                </div>
                                @foreach($secondaryImages as $index => $image)
                                    <div class="flex-shrink-0 w-32 h-20 rounded overflow-hidden border-2 border-white shadow-md mb-2 flex-none">
                                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $image['image'] ?? $mainImage ?? $primaryImage }}"
                                            alt="{{ $house['name'] ?? 'Property Image' }} - Image {{ $index + 2 }}"
                                            @click.prevent="showModal=true; modalImg='{{ env('ADMIN_URL') }}/storage/{{ $image['image'] ?? $mainImage ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }} - Image {{ $index + 2 }}'"
                                            class="w-full h-full object-cover cursor-pointer hover:opacity-80">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Property Info Section — liquid glass content panel -->
            <div class="p-6 mb-8" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Left Column - Property Info -->
                    <div class="lg:w-1/2">
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $house['name'] }}</h1>
                            <p class="text-gray-600 mb-1">{{ $house['location'] }}</p>
                            <p class="text-gray-500 text-sm">{{ $house['distance'] }}</p>
                        </div>

                        <!-- Features/Promotions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Facilities</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($house['general'] as $generalFeature)
                                    <span class="inline-flex items-center border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-gray-50">
                                        {{ $generalFeature }}
                                    </span>
                                @endforeach
                                @foreach($house['security'] as $securityFeature)
                                    <span class="inline-flex items-center border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-gray-50">
                                        {{ $securityFeature }}
                                    </span>
                                @endforeach
                                @foreach($house['amenities'] as $amenityFeature)
                                    <span class="inline-flex items-center border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-gray-50">
                                        {{ $amenityFeature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8">
                            <button onclick="scrollToRooms()" class="flex-1 bg-teal-600 text-white py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-calendar-check mr-2"></i> Book Now
                            </button>
                            <a href="https://wa.me/6281188099700/" target="_blank" class="flex-1 border border-teal-600 text-teal-600 py-3 px-6 rounded-lg hover:bg-teal-50 transition-colors flex items-center justify-center">
                                <i class="fab fa-whatsapp mr-2"></i> Contact for More Information
                            </a>
                        </div>
                    </div>

                    <!-- Right Column - Price Section -->
                    <div class="lg:w-1/2 lg:border-l lg:pl-8 lg:border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Rental Price</h3>

                        @php
                            $hasDailyPrice = !empty($house['price_original_daily']) && $house['price_original_daily'] > 0;
                            $hasMonthlyPrice = !empty($house['price_original_monthly']) && $house['price_original_monthly'] > 0;
                        @endphp

                        @if(!$hasDailyPrice && !$hasMonthlyPrice)
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <div class="text-gray-400 mb-2">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-medium">Rental price not available</p>
                                <p class="text-sm text-gray-500 mt-1">Please contact us for more information</p>
                            </div>
                        @else
                            @if($hasDailyPrice)
                            <!-- Daily Price -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <p class="text-base text-gray-500 mb-1">
                                    <i class="far fa-calendar mr-2"></i>Daily
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900">
                                            Rp{{ number_format($house['price_original_daily'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 ml-2">/night</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($hasMonthlyPrice)
                            <!-- Monthly Price -->
                            <div class="{{ $hasDailyPrice ? 'mt-4' : '' }} bg-teal-50 border border-teal-100 p-4 rounded-lg">
                                <p class="text-base text-gray-600 mb-1">
                                    <i class="far fa-calendar-alt mr-2"></i>Monthly
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-teal-700">
                                            Rp{{ number_format($house['price_original_monthly'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 ml-2">/month</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Additional Info -->
                            <div class="mt-4 text-sm text-gray-500">
                                <p class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-teal-600"></i>
                                    Price includes VAT
                                </p>
                                <p class="flex items-center mt-1">
                                    <i class="fas fa-credit-card mr-2 text-teal-600"></i>
                                    Payment via bank transfer
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Property Description -->
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">About the Property</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-600">
                            {{ $house['description'] }}
                        </p>
                    </div>

                    <!-- Room Facilities -->
                    <!-- <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Room Facilities</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        </div>
                    </div> -->

                    <!-- House Rules -->
                    <!-- <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">House Rules</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            
                        </div>
                    </div> -->

                    <!-- Location Map -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Location</h3>
                        <!-- Liquid glass location card -->
                        <div class="p-6" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                            <div class="flex flex-col lg:flex-row gap-8">
                                <!-- Left Column - Map -->
                                <div class="lg:w-1/2 flex flex-col">
                                    @php
                                        // Default Jakarta coordinates (Monas)
                                        $defaultLat = -6.1754;
                                        $defaultLng = 106.8272;

                                        // Initialize variables with default values
                                        $lat = $defaultLat;
                                        $lng = $defaultLng;

                                        // Check if location exists and is in correct format
                                        // if (!empty($house['location']) && str_contains($house['location'], ',')) {
                                        //     $coordinates = explode(',', $house['location']);
                                        //     if (count($coordinates) >= 2) {
                                        //         $parsedLat = trim($coordinates[0]);
                                        //         $parsedLng = trim($coordinates[1]);

                                        //         // Validate if coordinates are numeric
                                        //         if (is_numeric($parsedLat) && is_numeric($parsedLng)) {
                                        //             $lat = (float)$parsedLat;
                                        //             $lng = (float)$parsedLng;
                                        //         }
                                        //     }
                                        // }
                                        if(!empty($house['latitude']) && !empty($house['longitude'])) {
                                            $lat = (float)$house['latitude'];
                                            $lng = (float)$house['longitude'];
                                        }

                                        // Set bounding box with padding
                                        $bboxPadding = 0.01; // Adjust this value to control the zoom level
                                        $bbox = sprintf(
                                            '%f,%f,%f,%f',
                                            $lng - $bboxPadding,
                                            $lat - $bboxPadding,
                                            $lng + $bboxPadding,
                                            $lat + $bboxPadding
                                        );
                                    @endphp
                                    <div class="w-full flex-1">
                                        <iframe
                                            class="w-full h-full rounded-lg border border-gray-200"
                                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&amp;layer=mapnik&amp;marker={{ $lat }}%2C{{ $lng }}">
                                        </iframe>
                                    </div>
                                    <div class="mt-2 text-right">
                                        <small class="text-sm">
                                            <a href="https://www.openstreetmap.org/?mlat={{ $lat }}&amp;mlon={{ $lng }}#map=18/{{ $lat }}/{{ $lng }}" target="_blank" class="text-teal-600 hover:underline">
                                                View Larger Map
                                            </a>
                                        </small>
                                    </div>
                                </div>

                                <!-- Right Column - Address Details -->
                                <div class="lg:w-1/2 lg:border-l lg:pl-8 lg:border-gray-200">
                                    <h4 class="text-xl font-semibold text-gray-900 mb-4">Address</h4>

                                    @if(!empty($house['address']))
                                        <div class="space-y-4">
                                            @if(!empty($house['address']['full_address']))
                                                <div class="pb-3 border-b border-gray-100">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Full Address</h5>
                                                    <p class="text-base text-gray-900 leading-relaxed">{{ $house['address']['full_address'] }}</p>
                                                </div>
                                            @endif

                                            <div class="grid grid-cols-2 gap-4">
                                                @if(!empty($house['address']['village']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Village</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['village'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['subdistrict']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Subdistrict</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['subdistrict'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['city']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">City/Regency</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['city'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['province']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Province</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['province'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['postal_code']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Postal Code</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['postal_code'] }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-600 mb-6">{{ $house['location'] }}</p>
                                    @endif

                                    @if(!empty($house['distance']))
                                        <div class="mt-4 pt-4 border-t border-gray-200">
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2">Distance</h5>
                                            <p class="text-gray-600 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $house['distance'] }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Section -->
                    <div id="rooms-section" class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Available Rooms</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @forelse($house['rooms'] as $room)
                                <a href="{{ route('en.rooms.show', $room['slug']) }}" class="group">
                                    <!-- Liquid glass room card -->
                                    <div class="overflow-hidden transition-shadow group-hover:ring-2 group-hover:ring-teal-500" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                                    <div class="relative pb-[56.25%] h-48">
                                        <div class="absolute inset-0">
                                            @php
                                                $roomImages = $room['images'] ?? [];
                                                $roomMainImage = null;
                                                
                                                // Safely get the first image if it exists
                                                if (!empty($roomImages) && is_array($roomImages) && isset($roomImages[0]['image'])) {
                                                    $roomMainImage = $roomImages[0]['image'];
                                                } elseif (isset($room['image'])) {
                                                    // Fallback to room's main image if available
                                                    $roomMainImage = $room['image'];
                                                }
                                                
                                                $roomTotalImages = count($roomImages) > 0 ? count($roomImages) : ($roomMainImage ? 1 : 0);
                                            @endphp
                                            
                                            @if(!empty($roomMainImage) && is_string($roomMainImage))
                                                {{-- <img src="data:image/jpeg;base64,{{ $roomMainImage }}" 
                                                    alt="{{ $room['name'] ?? 'Room image' }}" 
                                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                    onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iI2YzZjRmNSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTZhYSI+SW1hZ2Ugbm90IGF2YWlsYWJsZTwvdGV4dD48L3N2Zz4=';"
                                                    onclick="if(window.openRoomGallery) { openRoomGallery({{ $loop->index }}) }"> --}}
                                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $roomMainImage }}" 
                                                    alt="{{ $room['name'] ?? 'Room image' }}" 
                                                    class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                    onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iI2YzZjRmNSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTZhYSI+SW1hZ2Ugbm90IGF2YWlsYWJsZTwvdGV4dD48L3N2Zz4=';"
                                                    onclick="if(window.openRoomGallery) { openRoomGallery({{ $loop->index }}) }">
                                            @else
                                                <div class="bg-gray-100 w-full h-full flex items-center justify-center">
                                                    <i class="fas fa-image text-4xl text-gray-400"></i>
                                                    <span class="ml-2 text-gray-500">No Image</span>
                                                </div>
                                            @endif
                                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent h-16">
                                                <span class="absolute top-2 left-2 bg-teal-600 text-white px-2 py-1 rounded-full text-sm">
                                                    {{ ucfirst($room['type']) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $room['no'] }} - {{ $room['name'] }}</h3>
                                        <p class="text-gray-600 text-sm mb-4">{{ $room['descriptions'] }}</p>

                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Room Facilities:</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @if(!empty($room['facility']))
                                                    @foreach($room['facility'] as $facility)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 uppercase">
                                                            {{ strtoupper($facility) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-500 text-sm">No Facilities Available</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Rental Periods -->
                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mt-8 mb-4">Rental Period &amp; Price:</h4>
                                            <ul class="space-y-2">
                                                @php
                                                    $hasValidPeriod = false;
                                                @endphp

                                                @if($room['price_original_daily'] > 0)
                                                    @php $hasValidPeriod = true; @endphp
                                                    <li class="flex items-center">
                                                        <span class="w-24 text-sm font-semibold text-gray-800">Daily</span>
                                                        <span class="font-medium">
                                                            Rp{{ number_format($room['price_original_daily'], 0, ',', '.') }}
                                                        </span>
                                                    </li>
                                                @endif
                                                @if($room['price_original_monthly'] > 0 )
                                                    @php $hasValidPeriod = true; @endphp
                                                    <li class="flex items-center">
                                                        <span class="w-24 text-sm font-semibold text-gray-800">Monthly</span>
                                                        <span class="font-medium">
                                                            Rp{{ number_format($room['price_original_monthly'], 0, ',', '.') }}
                                                        </span>
                                                    </li>
                                                @endif

                                                @if(!$hasValidPeriod)
                                                    <li>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Period Not Available
                                                            <i class="fas fa-exclamation-circle ml-1"></i>
                                                        </span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        @if($room['status'] === 1)
                                            <div class="flex items-center justify-between mt-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Available
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                </a>
                            @empty
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gray-500">No rooms available at this time.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <!-- <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Property Amenities</h2>
                    <ul class="space-y-3">
                        
                    </ul>
                </div> -->
            </div>
        </div>
    </section>

    <!-- Debug: Room Images in House View -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rooms = @json($house['rooms'] ?? []);
            
            // console.log('=== ROOM IMAGES DEBUG ===');
            // console.log('Total Rooms:', rooms.length);
            
            // rooms.forEach((room, roomIndex) => {
            //     const roomImages = room.images || [];
            //     console.group(`Room ${roomIndex + 1}: ${room.name || 'Unnamed Room'}`);
            //     console.log('Room ID:', room.id || 'N/A');
            //     console.log('Total Images:', roomImages.length);
                
            //     if (roomImages.length > 0) {
            //         console.log('Image Details:');
            //         roomImages.forEach((img, imgIndex) => {
            //             console.group(`Image ${imgIndex + 1}`);
            //             console.log('Has Image Data:', !!img.image ? 'Yes' : 'No');
            //             console.log('Caption:', img.caption || 'No caption');
            //             console.log('Image Preview:', img.image ? img.image.substring(0, 30) + '...' : 'No image data');
            //             console.groupEnd();
            //         });
            //     } else {
            //         console.log('No images found for this room');
            //     }
                
            //     console.groupEnd();
            // });
            // console.log('=========================');
        });
    </script>

    <!-- Footer -->
    @include('components.homepage.footer')

    <!-- Include Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @include('components.homepage.scripts')
        });

        function scrollToRooms() {
            const roomsSection = document.getElementById('rooms-section');
            if (roomsSection) {
                roomsSection.scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    </script>
</body>
</html> 