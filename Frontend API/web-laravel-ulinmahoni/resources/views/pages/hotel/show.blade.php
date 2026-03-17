<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hotel['name'] }} - Property Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
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
                    <li><a href="{{ route('hotels') }}" class="hover:text-gray-700">Hotels</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">{{ $hotel['name'] }}</li>
                </ol>
            </nav>

            <!-- Property Details -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Image Section -->
                <div class="grid grid-cols-2 grid-rows-2 gap-4 h-[500px]">
                    <!-- Main Large Image -->
                    <div class="relative col-span-2 row-span-1 rounded-lg overflow-hidden">
                        <img src="{{ asset($hotel['image']) }}" 
                             alt="{{ $hotel['name'] }}" 
                             class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="bg-teal-600 text-white px-3 py-1.5 rounded-full text-sm">
                                {{ $hotel['type'] }}
                            </span>
                        </div>
                    </div>
                    <!-- Smaller Images -->
                    <div class="relative rounded-lg overflow-hidden">
                        <img src="{{ asset($hotel['image_2'] ?? $hotel['image']) }}" 
                             alt="{{ $hotel['name'] }}" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="relative rounded-lg overflow-hidden">
                        <img src="{{ asset($hotel['image_3'] ?? $hotel['image']) }}" 
                             alt="{{ $hotel['name'] }}" 
                             class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Info Section -->
                <div class="space-y-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $hotel['name'] }}</h1>
                        <p class="text-gray-600 mb-1">{{ $hotel['location'] }}</p>
                        <p class="text-gray-500 text-sm">{{ $hotel['distance'] }}</p>
                    </div>

                    <!-- Price Section -->
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                        <p class="text-sm text-gray-500 mb-2">
                            mulai dari <span class="line-through">Rp{{ number_format($hotel['price']['original'], 0, ',', '.') }}</span>
                        </p>
                        <div class="flex items-center space-x-2">
                            <p class="text-2xl font-bold text-gray-900">
                                Rp{{ number_format($hotel['price']['discounted'], 0, ',', '.') }}
                            </p>
                            <span class="text-sm text-gray-500">/bulan</span>
                        </div>

                        <!-- Features/Promotions -->
                        <div class="mt-4 space-y-2">
                            @foreach($hotel['features'] as $feature)
                                <div class="inline-block mr-2 mb-2">
                                    <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm text-gray-600">
                                        {{ $feature }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-4">
                        <button class="flex-1 bg-teal-600 text-white py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors">
                            Book Now
                        </button>
                        <button class="flex-1 border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-colors">
                            Contact Agent
                        </button>
                    </div>
                </div>
            </div>

            <!-- Additional Details -->
            <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Property Description -->
                <div class="lg:col-span-2">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">About this property</h2>
                    <div class="prose max-w-none">
                        <p class="text-gray-600">
                            {{ $hotel['description'] }}
                        </p>
                    </div>

                    <!-- Room Facilities -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Room Facilities</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($hotel['attributes']['room_facilities'] as $facility)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ $facility }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Hotel Rules -->
                    <div class="mt-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Hotel Rules</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($hotel['attributes']['rules'] as $rule)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    {{ $rule }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Hotel Amenities</h2>
                    <ul class="space-y-3">
                        @foreach($hotel['attributes']['amenities'] as $amenity)
                            <li class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $amenity }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @include('components.homepage.scripts')
        });
    </script>
</body>
</html> 