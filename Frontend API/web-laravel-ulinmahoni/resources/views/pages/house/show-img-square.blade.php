<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $house['name'] }} - Property Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
    <style>
    .image-gallery {
            --gap: 1rem;
            --transition: all 0.3s ease;
        }

        .gallery-item {
            position: relative;
            overflow: hidden;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: var(--transition);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .gallery-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .gallery-item img {
            width: 60%;
            height: 100%;
            object-fit: cover;
            display: block;
            margin: 0 auto;
            transition: var(--transition);
        }

        .gallery-item.main-image {
            height: 400px;
        }

        .gallery-item.main-image img {
            width: 80%;
        }

        .gallery-item.side-image {
            height: 195px;
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
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Properties</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">{{ $house['name'] }}</li>
                </ol>
            </nav>

            <!-- Image Gallery Section -->
            <div x-data="{ showModal: false, modalImg: '', modalAlt: '' }" class="image-gallery overflow-x-auto flex gap-4 mb-8 pb-2">
                <!-- Image Popup Modal -->
                <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" style="display: none;" @click.away="showModal=false" @keydown.escape.window="showModal=false">
                    <img :src="modalImg" :alt="modalAlt" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white object-contain" @click.stop>
                    <button @click="showModal=false" class="absolute top-4 right-6 text-white text-3xl font-bold focus:outline-none">&times;</button>
                </div>
                
                <!-- Main and Secondary Images as Scrollable Row -->
                @if($primaryImage)
                    <div class="gallery-item bg-gray-100 rounded-lg overflow-hidden relative aspect-[16/9] min-w-[384px] max-w-lg cursor-pointer flex-shrink-0"
                        @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'">
                        <img src="data:image/jpeg;base64,{{ $primaryImage }}"
                            alt="{{ $house['name'] ?? 'Property Image' }}"
                            class="w-full h-full object-cover transition-opacity duration-300 hover:opacity-90"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                        @if(isset($house['type']))
                            <span class="type-badge bg-primary-600 text-white px-3 py-1 text-sm font-medium rounded-full absolute top-4 left-4">
                                {{ $house['type'] }}
                            </span>
                        @endif
                        @if($totalImages > 0)
                            <span class="image-count bg-black bg-opacity-60 text-white px-3 py-1 text-sm rounded-full absolute bottom-4 right-4">
                                <i class="fas fa-camera mr-1"></i> {{ $totalImages }}
                            </span>
                        @endif
                    </div>
                @endif
                @if(!empty($secondaryImages))
                    @foreach($secondaryImages as $index => $image)
                        <div class="gallery-item bg-gray-100 rounded-lg overflow-hidden relative aspect-[16/9] min-w-[384px] max-w-lg cursor-pointer flex-shrink-0"
                            @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $image['image'] ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'">
                            <img src="data:image/jpeg;base64,{{ $image['image'] ?? $primaryImage }}"
                                alt="{{ $house['name'] ?? 'Property Image' }}"
                                class="w-full h-full object-cover transition-opacity duration-300 hover:opacity-90"
                                onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                            <span class="image-count bg-black bg-opacity-60 text-white px-2 py-1 text-xs rounded-full absolute bottom-2 right-2">
                                {{ $index + 2 }}/{{ $totalImages }}
                            </span>
                        </div>
                    @endforeach
                @endif

                <!-- Right Side Smaller Images -->
                @if(count($secondaryImages) > 0 || $totalImages > 1)
                    <div class="flex flex-col gap-4">
                        @foreach($secondaryImages as $index => $image)
                        <!-- Limit to 2 secondary images -->
                        @if($index < 2) 
                                <div class="gallery-item side-image bg-gray-100 rounded-lg overflow-hidden relative h-full cursor-pointer"
                                    @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $image['image'] ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'">
                                    <img src="data:image/jpeg;base64,{{ $image['image'] ?? $primaryImage }}"
                                         alt="{{ $house['name'] ?? 'Property Image' }}"
                                         class="w-full h-full object-cover transition-opacity duration-300 hover:opacity-90"
                                         onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                                    <span class="image-count bg-black bg-opacity-60 text-white px-2 py-1 text-xs rounded-full absolute bottom-2 right-2">
                                        {{ $index + 2 }}/{{ $totalImages }}
                                    </span>
                                </div>
                            @endif
                        @endforeach
                        
                        @if(count($secondaryImages) === 0 && $primaryImage)
                            <!-- Fallback if no secondary images but main image exists -->
                            <div class="gallery-item side-image bg-gray-100 rounded-lg overflow-hidden relative h-full cursor-pointer"
        @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $image['image'] ?? $primaryImage }}'; modalAlt='{{ $house['name'] ?? 'Property Image' }}'">
                                <img src="data:image/jpeg;base64,{{ $primaryImage }}"
                                     alt="{{ $house['name'] ?? 'Property Image' }}"
                                     class="w-full h-full object-cover transition-opacity duration-300 hover:opacity-90"
                                     onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Property Info Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 mb-8">
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
                            <h3 class="text-lg font-semibold text-gray-800 mb-3">Fasilitas</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($house['features'] as $feature)
                                    <span class="inline-flex items-center border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-700 bg-gray-50">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 mt-8">
                            <button onclick="scrollToRooms()" class="flex-1 bg-teal-600 text-white py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors flex items-center justify-center">
                                <i class="fas fa-calendar-check mr-2"></i> Pesan Sekarang
                            </button>
                            <a href="https://wa.me/6289699670912/" target="_blank" class="flex-1 border border-teal-600 text-teal-600 py-3 px-6 rounded-lg hover:bg-teal-50 transition-colors flex items-center justify-center">
                                <i class="fab fa-whatsapp mr-2"></i> Hubungi untuk Info Lebih Lanjut
                            </a>
                        </div>
                    </div>

                    <!-- Right Column - Price Section -->
                    <div class="lg:w-1/2 lg:border-l lg:pl-8 lg:border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6">Harga Sewa</h3>
                        
                        @php
                            $hasDailyPrice = !empty($house['price_original_daily']) && $house['price_original_daily'] > 0;
                            $hasMonthlyPrice = !empty($house['price_original_monthly']) && $house['price_original_monthly'] > 0;
                        @endphp
                        
                        @if(!$hasDailyPrice && !$hasMonthlyPrice)
                            <div class="bg-gray-50 p-6 rounded-lg text-center">
                                <div class="text-gray-400 mb-2">
                                    <i class="fas fa-info-circle text-2xl"></i>
                                </div>
                                <p class="text-gray-600 font-medium">Harga sewa belum tersedia</p>
                                <p class="text-sm text-gray-500 mt-1">Silakan hubungi kami untuk informasi lebih lanjut</p>
                            </div>
                        @else
                            @if($hasDailyPrice)
                            <!-- Daily Price -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <p class="text-base text-gray-500 mb-1">
                                    <i class="far fa-calendar mr-2"></i>Harian
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900">
                                            Rp{{ number_format($house['price_original_daily'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 ml-2">/malam</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            @if($hasMonthlyPrice)
                            <!-- Monthly Price -->
                            <div class="{{ $hasDailyPrice ? 'mt-4' : '' }} bg-teal-50 border border-teal-100 p-4 rounded-lg">
                                <p class="text-base text-gray-600 mb-1">
                                    <i class="far fa-calendar-alt mr-2"></i>Bulanan
                                </p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-teal-700">
                                            Rp{{ number_format($house['price_original_monthly'], 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-500 ml-2">/bulan</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Additional Info -->
                            <div class="mt-4 text-sm text-gray-500">
                                <p class="flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-teal-600"></i>
                                    Harga sudah termasuk PPN
                                </p>
                                <p class="flex items-center mt-1">
                                    <i class="fas fa-credit-card mr-2 text-teal-600"></i>
                                    Pembayaran dengan metode transfer bank
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Tentang Properti</h2>
                    <div class="prose max-w-none">
                        <div class="text-gray-600">
                            {!! \App\Helpers\DescriptionHelper::getHtml($house['description'] ?? '', app()->getLocale()) !!}
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
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Lokasi</h3>
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8">
                            <div class="aspect-w-16 aspect-h-9">
                                @php
                                    // Default Jakarta coordinates (Monas)
                                    $defaultLat = -6.1754;
                                    $defaultLng = 106.8272;
                                    
                                    // Initialize variables with default values
                                    $lat = $defaultLat;
                                    $lng = $defaultLng;
                                    
                                    // Check if location exists and is in correct format
                                    if (!empty($house['location']) && str_contains($house['location'], ',')) {
                                        $coordinates = explode(',', $house['location']);
                                        if (count($coordinates) >= 2) {
                                            $parsedLat = trim($coordinates[0]);
                                            $parsedLng = trim($coordinates[1]);
                                            
                                            // Validate if coordinates are numeric
                                            if (is_numeric($parsedLat) && is_numeric($parsedLng)) {
                                                $lat = (float)$parsedLat;
                                                $lng = (float)$parsedLng;
                                            }
                                        }
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
                                <iframe 
                                    class="w-full h-[1400px] rounded-lg border border-gray-200"
                                    src="https://www.openstreetmap.org/export/embed.html?bbox={{ $bbox }}&amp;layer=mapnik&amp;marker={{ $lat }}%2C{{ $lng }}">
                                </iframe>
                                <div class="mt-2 text-right">
                                    <small class="text-sm">
                                        <a href="https://www.openstreetmap.org/?mlat={{ $lat }}&amp;mlon={{ $lng }}#map=18/{{ $lat }}/{{ $lng }}" target="_blank" class="text-teal-600 hover:underline">
                                            View Larger Map
                                        </a>
                                    </small>
                                </div>
                            </div>
                            <div class="mt-4">
                                <h4 class="font-semibold text-gray-900">Address</h4>
                                <p class="text-gray-600 mt-1">{{ $house['location'] }}</p>
                                <div class="mt-4 flex items-center text-sm text-gray-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $house['distance'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rooms Section -->
                    <div id="rooms-section" class="mt-12">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Kamar Tersedia</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @forelse($house['rooms'] as $room)
                                <a href="{{ route('rooms.show', $room['slug']) }}" class="group">
                                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group-hover:ring-2 group-hover:ring-teal-500">
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
                                                <img src="data:image/jpeg;base64,{{ $roomMainImage }}" 
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
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $room['name'] }}</h3>
                                        <div class="text-gray-600 text-sm mb-4">{!! \App\Helpers\DescriptionHelper::getHtml($room['descriptions'] ?? '', app()->getLocale()) !!}</div>

                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mb-2">Room Facilities:</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @if(!empty($room['facility']))
                                                    @foreach($room['facility'] as $facility)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 uppercase">
                                                            @if(is_array($facility) && !empty($facility['icon']))
                                                                <span class="iconify mr-1" data-icon="{{ $facility['icon'] }}"></span>
                                                            @endif
                                                            {{ is_array($facility) ? strtoupper($facility['name'] ?? '') : strtoupper($facility) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-500 text-sm">No facilities listed</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Rental Periods -->
                                        <div class="mb-4">
                                            <h4 class="text-sm font-semibold text-gray-700 mt-8 mb-4">Available Rental Periods &amp; Prices:</h4>
                                            <ul class="space-y-2">
                                                @php
                                                    $hasValidPeriod = false;
                                                @endphp
                                                
                                                @if(!empty($room['periode']['daily']) && !empty($room['price_original_daily']))
                                                    @php $hasValidPeriod = true; @endphp
                                                    <li class="flex items-center">
                                                        <span class="w-24 text-sm font-semibold text-gray-800">Daily</span>
                                                        <span class="font-medium">
                                                            Rp{{ number_format($room['price_original_daily'], 0, ',', '.') }}
                                                        </span>
                                                    </li>
                                                @endif
                                                @if(!empty($room['periode']['monthly']) && !empty($room['price_original_monthly']))
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
                                                            No periods available
                                                        </span>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <div class="flex items-center justify-between mt-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $room['status'] === 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $room['status'] === 1 ? 'Available' : 'Unavailable' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                </a>
                            @empty
                                <div class="col-span-full text-center py-8">
                                    <p class="text-gray-500">No rooms available for this property at the moment.</p>
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
            
            console.log('=== ROOM IMAGES DEBUG ===');
            console.log('Total Rooms:', rooms.length);
            
            rooms.forEach((room, roomIndex) => {
                const roomImages = room.images || [];
                console.group(`Room ${roomIndex + 1}: ${room.name || 'Unnamed Room'}`);
                console.log('Room ID:', room.id || 'N/A');
                console.log('Total Images:', roomImages.length);
                
                if (roomImages.length > 0) {
                    console.log('Image Details:');
                    roomImages.forEach((img, imgIndex) => {
                        console.group(`Image ${imgIndex + 1}`);
                        console.log('Has Image Data:', !!img.image ? 'Yes' : 'No');
                        console.log('Caption:', img.caption || 'No caption');
                        console.log('Image Preview:', img.image ? img.image.substring(0, 30) + '...' : 'No image data');
                        console.groupEnd();
                    });
                } else {
                    console.log('No images found for this room');
                }
                
                console.groupEnd();
            });
            console.log('=========================');
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