<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $house['name'] }} - {{ __('properties.page.property_details') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.property.dark-mode')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
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
    <!-- Alpine.js loaded via header component — no duplicate needed here -->
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
</head>
<body class="font-inter antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">{{ __('properties.navigation.home') }}</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">{{ __('properties.navigation.properties') }}</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">{{ $house['name'] }}</li>
                </ol>
            </nav>

            <!-- Image Gallery Section -->
            <div x-data="{
                showModal: false,
                modalImg: '',
                modalAlt: '',
                currentImageIndex: 0,
                images: [
                    '{{ env('ADMIN_URL') }}/storage/{{ $mainImage ?? $primaryImage }}',
                    @foreach($secondaryImages as $image)
                        '{{ env('ADMIN_URL') }}/storage/{{ $image['image'] ?? $mainImage ?? $primaryImage }}',
                    @endforeach
                ],
                showImage(index) {
                    this.currentImageIndex = index;
                    this.modalImg = this.images[index];
                    this.showModal = true;
                },
                nextImage() {
                    this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length;
                    this.modalImg = this.images[this.currentImageIndex];
                },
                prevImage() {
                    this.currentImageIndex = this.currentImageIndex > 0 ? this.currentImageIndex - 1 : this.images.length - 1;
                    this.modalImg = this.images[this.currentImageIndex];
                }
            }" class="image-gallery relative mb-8">
                <!-- Image Popup Modal -->
                <div x-show="showModal"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70"
                    style="display: none;"
                    @click.away="showModal=false"
                    @click.self="showModal=false"
                    @keydown.escape.window="showModal=false"
                    @keydown.arrow-right.window="showModal && nextImage()"
                    @keydown.arrow-left.window="showModal && prevImage()">

                    <!-- Modal Container with consistent border -->
                    <div class="relative w-[90vw] h-[80vh] max-w-5xl bg-black rounded-lg border-4 border-white shadow-2xl flex items-center justify-center overflow-hidden" @click.stop>
                        <img :src="modalImg" :alt="modalAlt" class="max-h-full max-w-full object-contain">

                        <!-- Arrow Navigation Buttons -->
                        <template x-if="images.length > 1">
                            <div>
                                <!-- Previous Button -->
                                <button @click.stop="prevImage()"
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <i class="fas fa-chevron-left text-teal-600 text-xl"></i>
                                </button>

                                <!-- Next Button -->
                                <button @click.stop="nextImage()"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <i class="fas fa-chevron-right text-teal-600 text-xl"></i>
                                </button>

                                <!-- Image Counter -->
                                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                                    <span x-text="currentImageIndex + 1"></span> / <span x-text="images.length"></span>
                                </div>
                            </div>
                        </template>
                    </div>

                    <button @click="showModal=false" class="absolute top-4 right-6 text-white text-3xl font-bold focus:outline-none hover:text-gray-300 transition-colors">&times;</button>
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
                            class="w-full h-full object-cover object-center cursor-pointer"
                            @click.prevent="showImage(0); modalAlt='{{ $house['name'] ?? 'Property Image' }}'"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                            <span class="ml-2 font-medium text-gray-500">{{ __('properties.images.no_image') }}</span>
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
                                        @click.prevent="showImage(0); modalAlt='{{ $house['name'] ?? 'Property Image' }}'"
                                        class="w-full h-full object-cover cursor-pointer hover:opacity-80">
                                </div>
                                @foreach($secondaryImages as $index => $image)
                                    <div class="flex-shrink-0 w-32 h-20 rounded overflow-hidden border-2 border-white shadow-md mb-2 flex-none">
                                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $image['image'] ?? $mainImage ?? $primaryImage }}"
                                            alt="{{ $house['name'] ?? 'Property Image' }} - Image {{ $index + 2 }}"
                                            @click.prevent="showImage({{ $index + 1 }}); modalAlt='{{ $house['name'] ?? 'Property Image' }} - Image {{ $index + 2 }}'"
                                            class="w-full h-full object-cover cursor-pointer hover:opacity-80">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Property Info Section -->
            <div class="p-6 mb-8 rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-md">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Left Column - Property Info -->
                    <div class="lg:w-1/2">
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $house['name'] }}</h1>
                            @if(!empty($house['gender']))
                            <p class="text-gray-500 text-sm mb-1"><i class="fas fa-venus-mars mr-1"></i>{{ __('properties.gender.' . strtolower($house['gender']), ['default' => $house['gender']]) }}</p>
                            @endif
                            <p class="text-gray-600 mb-1">{{ $house['location'] }}</p>
                            <p class="text-gray-500 text-sm">{{ $house['distance'] }}</p>
                        </div>

                        <!-- Features/Promotions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ __('properties.sections.facilities') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @if(!empty($house['facility']))
                                    @foreach($house['facility'] as $facility)
                                        <span class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm text-gray-700">
                                            @if(!empty($facility['icon']))
                                                <span class="iconify mr-1.5 text-teal-600" data-icon="{{ $facility['icon'] }}"></span>
                                            @endif
                                            {{ $facility['name'] }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8">
                            <button onclick="scrollToRooms()" class="flex-1 bg-teal-600 text-white py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-calendar-check mr-2"></i> {{ __('properties.buttons.book_now') }}
                            </button>
                            <a href="https://wa.me/6281188099700/" target="_blank" class="flex-1 border border-teal-600 text-teal-600 py-3 px-6 rounded-lg hover:bg-teal-50 transition-colors flex items-center justify-center">
                                <i class="fab fa-whatsapp mr-2"></i> {{ __('properties.buttons.contact_info') }}
                            </a>
                        </div>
                    </div>

                    <!-- Right Column - Price Section -->
                    <div class="lg:w-1/2 lg:border-l lg:pl-8 lg:border-gray-200 dark:lg:border-gray-700">
                        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-6">{{ __('properties.sections.rental_price') }}</h3>

                        @php
                            $hasDailyPrice = !empty($house['price_original_daily']) && $house['price_original_daily'] > 0;
                            $hasMonthlyPrice = !empty($house['price_original_monthly']) && $house['price_original_monthly'] > 0;
                        @endphp

                        @if(!$hasDailyPrice && !$hasMonthlyPrice)
                            <div class="bg-gray-50 dark:bg-gray-800 p-6 rounded-lg text-center">
                                <div class="text-gray-400 dark:text-gray-500 mb-2">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-300 font-medium">{{ __('properties.price_info.not_available') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ __('properties.price_info.contact_us') }}</p>
                            </div>
                        @else
                            @if($hasDailyPrice)
                            <!-- Daily Price -->
                            <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg mb-4">
                                <p class="text-base text-gray-500 dark:text-gray-400 mb-1">
                                    <i class="far fa-calendar mr-2"></i>{{ __('properties.price_info.daily') }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                            Rp{{ number_format($house['price_original_daily'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-2">{{ __('properties.price_info.per_night') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($hasMonthlyPrice)
                            <!-- Monthly Price -->
                            <div class="{{ $hasDailyPrice ? 'mt-4' : '' }} bg-teal-50 dark:bg-teal-900/30 border border-teal-100 dark:border-teal-800 p-4 rounded-lg">
                                <p class="text-base text-gray-600 dark:text-gray-300 mb-1">
                                    <i class="far fa-calendar-alt mr-2"></i>{{ __('properties.price_info.monthly') }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-teal-700 dark:text-teal-400">
                                            Rp{{ number_format($house['price_original_monthly'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400 ml-2">{{ __('properties.price_info.per_month_full') }}</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Additional Info -->
                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                                <p class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-teal-600 dark:text-teal-400"></i>
                                    {{ __('properties.price_info.includes_tax') }}
                                </p>
                                <p class="flex items-center mt-1">
                                    <i class="fas fa-credit-card mr-2 text-teal-600 dark:text-teal-400"></i>
                                    {{ __('properties.price_info.payment_method') }}
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('properties.sections.about_property') }}</h2>
                    <div class="prose max-w-none">
                        <div class="text-gray-600">
                            {!! nl2br(e(\App\Helpers\DescriptionHelper::get($house['description'] ?? '', app()->getLocale()))) !!}
                        </div>
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
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('properties.details.location') }}</h3>
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
                                                {{ __('properties.buttons.view_larger_map') }}
                                            </a>
                                        </small>
                                    </div>
                                </div>

                                <!-- Right Column - Address Details -->
                                <div class="lg:w-1/2 lg:border-l lg:pl-8 lg:border-gray-200">
                                    <h4 class="text-xl font-semibold text-gray-900 mb-4">{{ __('properties.address.title') }}</h4>

                                    @if(!empty($house['address']))
                                        <div class="space-y-4">
                                            @if(!empty($house['address']['full_address']))
                                                <div class="pb-3 border-b border-gray-100">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">{{ __('properties.address.full_address') }}</h5>
                                                    <p class="text-base text-gray-900 leading-relaxed">{{ $house['address']['full_address'] }}</p>
                                                </div>
                                            @endif

                                            <div class="grid grid-cols-2 gap-4">
                                                @if(!empty($house['address']['village']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('properties.address.village') }}</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['village'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['subdistrict']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('properties.address.subdistrict') }}</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['subdistrict'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['city']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('properties.address.city') }}</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['city'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['province']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('properties.address.province') }}</h5>
                                                        <p class="text-sm text-gray-900">{{ $house['address']['province'] }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($house['address']['postal_code']))
                                                    <div>
                                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ __('properties.address.postal_code') }}</h5>
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
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2">{{ __('properties.details.distance') }}</h5>
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

                    <!-- Nearby Locations -->
                    @if(!empty($house['nearby_locations']))
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">{{ __('properties.details.nearby_locations') }}</h3>
                        <!-- Liquid glass nearby locations card -->
                        <div class="p-6" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                            @php
                                $groupedLocations = collect($house['nearby_locations'])->groupBy('category');
                                $categoryIcons = [
                                    'transport' => 'fas fa-bus',
                                    'health' => 'fas fa-hospital',
                                    'food_drink' => 'fas fa-utensils',
                                    'finance' => 'fas fa-university',
                                    'education' => 'fas fa-graduation-cap',
                                    'worship' => 'fas fa-mosque',
                                    'shopping' => 'fas fa-shopping-bag',
                                ];
                            @endphp
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($groupedLocations as $category => $locations)
                                    <div class="space-y-2">
                                        <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wide flex items-center">
                                            <i class="{{ $categoryIcons[$category] ?? 'fas fa-map-marker-alt' }} text-teal-600 mr-2"></i>
                                            {{ __("properties.nearby_categories.{$category}") }}
                                        </h4>
                                        <ul class="space-y-1">
                                            @foreach($locations as $location)
                                                <li class="text-sm text-gray-600 flex items-center justify-between">
                                                    <span>{{ $location['name'] }}</span>
                                                    <span class="text-xs text-gray-400">{{ $location['distance_text'] ?? '' }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Rooms Section -->
                    <div id="rooms-section" class="mt-12" x-data="{ openCategories: [] }">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('properties.sections.available_rooms') }}</h2>

                        @php
                            // Group rooms by name
                            $groupedRooms = collect($house['rooms'])->groupBy('name');
                        @endphp

                        @forelse($groupedRooms as $roomName => $rooms)
                            @php
                                // Count available rooms in this category (status=1 and rental_status!=1)
                                $availableInCategory = $rooms->filter(function($r) {
                                    return $r['status'] === 1 && $r['rental_status'] !== 1;
                                })->count();
                            @endphp
                            <!-- Room Category Section — collapsed by default -->
                            <div class="mb-4 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden" x-data="{ isOpen: false }">
                                <!-- Accordion Header -->
                                <button
                                    @click="isOpen = !isOpen"
                                    class="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">
                                        {{ $roomName }}
                                        <span class="ml-2 text-sm font-normal text-gray-600 dark:text-gray-400">({{ $rooms->count() }} {{ __('properties.room.rooms_count') }})</span>
                                        <span class="ml-1 text-sm font-normal text-green-600 dark:text-green-400">· {{ $availableInCategory }} {{ __('properties.room.available_count') }}</span>
                                    </h3>
                                    <svg
                                        class="w-6 h-6 text-gray-600 dark:text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': isOpen }"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <!-- Accordion Content — proper background for light and dark modes -->
                                <div
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 transform translate-y-0"
                                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                                    class="p-6 bg-gray-100 dark:bg-gray-900"
                                >
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    @foreach($rooms as $room)
                                        <a href="{{ route('rooms.show', $room['slug']) }}" class="group">
                                            <!-- Room card — white card with rounded corners and shadow -->
                            <div class="overflow-hidden rounded-2xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-md transition-shadow group-hover:ring-2 group-hover:ring-teal-500">
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
                                                            <img src="{{ env('ADMIN_URL') }}/storage/{{ $roomMainImage }}"
                                                            alt="{{ $room['name'] ?? 'Room image' }}"
                                                            class="w-full h-full object-cover transition-transform duration-300 hover:scale-105"
                                                            onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0MDAiIGhlaWdodD0iMzAwIj48cmVjdCB3aWR0aD0iNDAwIiBoZWlnaHQ9IjMwMCIgZmlsbD0iI2YzZjRmNSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBkb21pbmFudC1iYXNlbGluZT0ibWlkZGxlIiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBmb250LWZhbWlseT0ic2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzljYTZhYSI+SW1hZ2Ugbm90IGF2YWlsYWJsZTwvdGV4dD48L3N2Zz4=';">
                                                    @else
                                                        <div class="bg-gray-100 w-full h-full flex items-center justify-center">
                                                            <i class="fas fa-image text-4xl text-gray-400"></i>
                                                            <span class="ml-2 text-gray-500">{{ __('properties.images.no_image') }}</span>
                                                        </div>
                                                    @endif

                                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent h-16">
                                                    
                                                        <!-- Availability Status Overlay -->
                                                        @if($room['status'] === 1 && $room['rental_status'] !== 1)
                                                            <span class="absolute bottom-2 left-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                                                                
                                                                {{ __('properties.status.available') }}
                                                            </span>
                                                        @else
                                                            <span class="absolute bottom-2 left-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-sm">
                                                                
                                                                {{ __('properties.status.unavailable') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="p-6">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $room['no'] }} - {{ $room['name'] }}</h3>
                                                <!-- <p class="text-gray-600 text-sm mb-4">{{ $room['descriptions'] }}</p> -->

                                                <!-- Rental Periods & View Details -->
                                                <div class="flex items-end justify-between mt-4">
                                                    <div>
                                                        <!-- <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('properties.room.period_price') }}</h4> -->
                                                        <ul class="space-y-1">
                                                            @php
                                                                $hasValidPeriod = false;
                                                            @endphp

                                                            @if($room['price_original_daily'] > 0)
                                                                @php $hasValidPeriod = true; @endphp
                                                                <li class="flex flex-col md:flex-row md:items-center">
                                                                    <span class="text-lg font-bold text-green-600">
                                                                        Rp{{ number_format($room['price_original_daily'], 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="text-base font-bold text-gray-800 md:ml-1">{{ __('properties.price.per_day') }}</span>
                                                                </li>
                                                            @endif
                                                            @if($room['price_original_monthly'] > 0 )
                                                                @php $hasValidPeriod = true; @endphp
                                                                <li class="flex flex-col md:flex-row md:items-center">
                                                                    <span class="text-lg font-bold text-green-600">
                                                                        Rp{{ number_format($room['price_original_monthly'], 0, ',', '.') }}
                                                                    </span>
                                                                    <span class="text-base font-bold text-gray-800 md:ml-1">{{ __('properties.price.per_month') }}</span>
                                                                </li>
                                                            @endif

                                                            @if(!$hasValidPeriod)
                                                                <li>
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                        {{ __('properties.room.period_not_available') }}
                                                                        <i class="fas fa-exclamation-circle ml-1"></i>
                                                                    </span>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>

                                                    <!-- View Details Button -->
                                                    <span class="inline-flex items-center px-4 py-2 bg-teal-600 text-white text-sm font-medium rounded-lg hover:bg-teal-700 transition-colors">
                                                        {{ __('properties.view_details') }}
                                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        </a>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500">{{ __('properties.room.no_rooms') }}</p>
                            </div>
                        @endforelse
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