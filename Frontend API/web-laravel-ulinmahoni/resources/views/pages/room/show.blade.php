<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('properties.room_detail.room_details') }} - {{ $room['name'] }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/css/datepicker.min.css">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.property.dark-mode')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .fa-spin {
            animation: spin 1s linear infinite;
        }

        /* Dark mode overrides for booking form card, containers, and inputs */
        html.dark .bg-white { background-color: #1e293b !important; }
        html.dark .bg-blue-50 { background-color: #374151 !important; }
        html.dark .bg-purple-50 { background-color: #374151 !important; }
        html.dark .border-gray-100,
        html.dark .border-blue-200,
        html.dark .border-purple-200,
        html.dark .border-gray-300 { border-color: #4b5563 !important; }
        html.dark #dailyBookingComponent input,
        html.dark #dailyBookingComponent select,
        html.dark #monthlyBookingComponent input,
        html.dark #monthlyBookingComponent select,
        html.dark #bookingForm select {
            background-color: #374151 !important;
            color: #f3f4f6 !important;
            border-color: #4b5563 !important;
        }
        html.dark #dailyBookingComponent label,
        html.dark #monthlyBookingComponent label,
        html.dark #dailyBookingComponent h3,
        html.dark #monthlyBookingComponent h3,
        html.dark .text-gray-700 {
            color: #f3f4f6 !important;
        }
        html.dark .text-gray-800 { color: #f9fafb !important; }
        html.dark .text-gray-500 { color: #d1d5db !important; }
        html.dark .text-gray-400 { color: #9ca3af !important; }
        /* Hide scrollbar on thumbnail strip while keeping horizontal scroll */
        .no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Datepicker popup z-index — must be above the leafy background overlay
           (main::after z-index:1, main > * z-index:2) */
        .datepicker {
            z-index: 100 !important;
        }

        /* Dark mode: vanillajs-datepicker calendar popup.
           Targets .datepicker-picker (the actual calendar panel) since the CDN CSS
           sets background-color:#fff on .datepicker-picker, not .datepicker. */
        html.dark .datepicker-picker {
            background-color: #1f2937 !important;
            border: 1px solid #4b5563 !important;
        }
        html.dark .datepicker-header {
            background-color: #1f2937 !important;
        }
        html.dark .datepicker-controls .button {
            color: #f3f4f6 !important;
            background-color: transparent !important;
            border-color: #4b5563 !important;
        }
        html.dark .datepicker-controls .button:hover {
            background-color: #374151 !important;
        }
        /* Enabled dates — bright white for clear visibility */
        html.dark .datepicker-cell {
            color: #ffffff !important;
        }
        html.dark .datepicker-cell:hover {
            background-color: #374151 !important;
        }
        html.dark .datepicker-cell.focused,
        html.dark .datepicker-cell.selected {
            background-color: #2563eb !important;
            color: #ffffff !important;
        }
        html.dark .datepicker-cell.today:not(.selected) {
            background-color: #374151 !important;
            color: #60a5fa !important;
        }
        /* Disabled/out-of-range dates — dim grey to distinguish from enabled */
        html.dark .datepicker-cell.disabled,
        html.dark .datepicker-cell.prev,
        html.dark .datepicker-cell.next {
            color: #4b5563 !important;
        }
        html.dark .datepicker .dow {
            color: #9ca3af !important;
        }
        /* Price summary section dark mode */
        html.dark .bg-gray-50 {
            background-color: #1e293b !important;
        }
        html.dark .text-gray-900 {
            color: #ffffff !important;
        }
        html.dark .text-gray-600 {
            color: #e5e7eb !important;
        }
        /* Dark mode: price breakdown row hover */
        html.dark #priceBreakdownContainer .hover\:bg-gray-50:hover {
            background-color: #374151 !important;
        }
    </style>
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
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">{{ __('properties.navigation.home') }}</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ url()->previous() }}" class="hover:text-gray-700">{{ __('properties.room_detail.property') }}</a></li>
                    <li><span class="mx-2">/</span></li>

                    <li class="text-gray-900">{{ $room['name'] }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Room Details (left) -->
                <div class="lg:col-span-7">
                    <!-- Liquid glass room detail card — frosted container with glass tokens -->
                    <div class="overflow-hidden" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                        <!-- Room Image Gallery -->
                        @php
                            $roomImages = $room['images'] ?? [];
                            $mainImage = $room['image'] ?? null;
                            $totalImages = count($roomImages) > 0 ? count($roomImages) : ($mainImage ? 1 : 0);
                        @endphp

                        <div class="relative h-96" x-data="{
                            showModal: false,
                            modalImg: '',
                            modalAlt: '',
                            currentImageIndex: 0,
                            images: [
                                @if($mainImage)
                                    '{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}',
                                @endif
                                @foreach($roomImages as $image)
                                    '{{ env('ADMIN_URL') }}/storage/{{ $image['image'] }}',
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
                        }">
                            <!-- Modal Popup -->
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
                            <div class="absolute inset-0 bg-gray-100 rounded-lg overflow-hidden">
                                @if($mainImage)
                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}"
                                        alt="{{ $room['name'] }}"
                                        class="w-full h-full object-cover object-center cursor-pointer"
                                        @click.prevent="showImage(0); modalAlt='{{ $room['name'] }}'"
                                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-property.jpg') }}';">
                                @else
                                    <div class="bg-gray-100 w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                        <span class="ml-2 font-medium text-gray-500">{{ __('properties.images.no_image') }}</span>
                                    </div>
                                @endif

                                <!-- Image Counter -->
                                @if($totalImages > 1)
                                    <div class="absolute top-4 right-4">
                                        <span class="bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                                            <i class="fas fa-camera mr-1"></i> {{ $totalImages }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Type Badge -->
                                <span class="absolute top-4 left-4 bg-teal-600 text-white px-3 py-1 rounded-full text-sm">
                                    {{ ucfirst($room['type']) }}
                                </span>

                                <!-- Thumbnails (if multiple images) -->
                                @if($totalImages > 1)
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent py-4 px-6">
                                        <div class="flex space-x-3 overflow-x-auto no-scrollbar">
                                            @if($mainImage)
                                                <div class="flex-shrink-0 w-32 h-20 rounded overflow-hidden border-2 border-white shadow-md mb-2 flex-none">
                                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}"
                                                        alt="{{ $room['name'] }} - Main"
                                                        class="w-full h-full object-cover cursor-pointer hover:opacity-80"
                                                        @click.prevent="showImage(0); modalAlt='{{ $room['name'] }}'">
                                                </div>
                                            @endif
                                            @foreach($roomImages as $index => $image)
                                                <div class="flex-shrink-0 w-32 h-20 rounded overflow-hidden border-2 border-white shadow-md mb-2 flex-none">
                                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $image['image'] }}"
                                                        alt="{{ $room['name'] }} - Image {{ $index + 1 }}"
                                                        class="w-full h-full object-cover cursor-pointer hover:opacity-80"
                                                        @click.prevent="showImage({{ $mainImage ? $index + 1 : $index }}); modalAlt='{{ $room['name'] }} - Image {{ $index + 1 }}'">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-8">
                            <!-- Room Title and Price -->
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-2">{{ $room['no'] }} - {{ $room['name'] }}</h1>
                                    <p class="text-gray-700 dark:text-gray-200 font-semibold uppercase tracking-wide">{{ $room['type'] }}</p>
                                </div>
                                <div class="space-y-4 text-right">
                                @if(!empty($room['price_original_daily']) && $room['price_original_daily'] > 0)
                                <div class="mt-4">
                                    {{-- <!-- Multi-Tier Pricing: Show weekday/weekend prices if available --> --}}
                                    @if(!empty($room['price_weekday']) && !empty($room['price_weekend']) && $room['price_weekday'] != $room['price_weekend'])
                                    <div class="flex items-baseline gap-3">
                                        <div>
                                            <p class="text-2xl font-extrabold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_weekday'], 0, ',', '.') }}</p>
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Weekday</p>
                                        </div>
                                        <span class="text-gray-400 dark:text-gray-500">|</span>
                                        <div>
                                            <p class="text-2xl font-extrabold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_weekend'], 0, ',', '.') }}</p>
                                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">Weekend</p>
                                        </div>
                                    </div>
                                    @if(!empty($room['has_seasonal_pricing']))
                                    <p class="text-xs font-semibold text-orange-600 dark:text-orange-300 mt-1"><i class="fas fa-info-circle mr-1"></i>Harga dapat berbeda pada hari libur & musim tertentu</p>
                                    @endif
                                    @else
                                    <p class="text-3xl font-extrabold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_original_daily'], 0, ',', '.') }}</p>
                                    @endif
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('properties.room_detail.per_night') }}</p>
                                </div>
                                @else
                                <div class="mt-4">
                                    <p class="text-3xl font-extrabold text-green-700 dark:text-green-400">{{ __('properties.room_detail.contact_us') }}</p>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('properties.room_detail.for_price_night') }}</p>
                                </div>
                                @endif
                                @if(!empty($room['price_original_monthly']) && $room['price_original_monthly'] > 0)
                                <div class="mt-4">
                                    <p class="text-3xl font-extrabold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_original_monthly'], 0, ',', '.') }}</p>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('properties.room_detail.per_month') }}</p>
                                </div>
                                @else
                                <div class="mt-4">
                                    <p class="text-3xl font-extrabold text-green-700 dark:text-green-400">{{ __('properties.room_detail.contact_us') }}</p>
                                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('properties.room_detail.for_price_month') }}</p>
                                </div>
                                @endif
                            </div>
                            </div>

                            <!-- Room Facilities -->
                            @if(!empty($room['facility']) && is_array($room['facility']) && count($room['facility']) > 0)
                            <div class="mb-8">
                                <h2 class="text-xl font-extrabold text-gray-900 dark:text-white mb-4">{{ __('properties.room_detail.room_facilities') }}</h2>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach($room['facility'] as $facility)
                                        <div class="flex items-center space-x-2 py-1">
                                            @if(is_array($facility) && !empty($facility['icon']))
                                                <span class="iconify text-green-700 dark:text-green-400 text-xl" data-icon="{{ $facility['icon'] }}"></span>
                                            @else
                                                <i class="fas fa-check text-green-700 dark:text-green-400"></i>
                                            @endif
                                            <span class="text-gray-800 dark:text-gray-100 font-semibold">
                                                @if(is_array($facility))
                                                    {{ strtoupper($facility['name'] ?? '') }}
                                                @else
                                                    {{ strtoupper($facility) }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="mb-8">
                                <h2 class="text-xl font-extrabold text-gray-900 dark:text-white mb-4">{{ __('properties.room_detail.room_facilities') }}</h2>
                                <p class="text-gray-700 dark:text-gray-200 font-medium">{{ __('properties.room_detail.no_facilities_listed') }}</p>
                            </div>
                            @endif

                            <!-- Room Description -->
                            <div class="space-y-6">
                                <div class="prose prose-lg max-w-none">
                                    <h3 class="text-xl font-extrabold text-gray-900 dark:text-white mb-4">{{ __('properties.room_detail.room_description') }}</h3>
                                    <!-- Multi-language description with line break support -->
                                    <div class="text-gray-800 dark:text-gray-100 font-medium leading-relaxed">{!! \App\Helpers\DescriptionHelper::getHtml($room['descriptions'] ?? '', app()->getLocale()) !!}</div>
                                </div>
                            </div>

                            <!-- Deposit & Parking Fees Info -->
                            @if(($room['property']['deposit_fee'] ?? 0) > 0 || !empty($room['property']['parking_fees']))
                            <div class="mt-8 mb-8">
                                <h2 class="text-xl font-extrabold text-gray-900 dark:text-white mb-4">{{ __('Biaya Tambahan') }}</h2>
                                <div class="space-y-2">
                                    @if(($room['property']['deposit_fee'] ?? 0) > 0)
                                    <div class="flex items-center space-x-2 py-1">
                                        <i class="fas fa-money-bill-wave text-green-700 dark:text-green-400"></i>
                                        <span class="text-gray-800 dark:text-gray-100 font-medium">Deposit: <span class="font-bold">Rp {{ number_format($room['property']['deposit_fee'], 0, ',', '.') }}</span></span>
                                    </div>
                                    @endif
                                    @if(!empty($room['property']['parking_fees']))
                                        @foreach($room['property']['parking_fees'] as $parkingFee)
                                        <div class="flex items-center space-x-2 py-1">
                                            @if(strtolower($parkingFee['parking_type'] ?? '') == 'motor' || strtolower($parkingFee['parking_type'] ?? '') == 'motorcycle')
                                                <i class="fas fa-bicycle text-green-700 dark:text-green-400"></i>
                                            @else
                                                <i class="fas fa-car text-green-700 dark:text-green-400"></i>
                                            @endif
                                            <span class="text-gray-800 dark:text-gray-100 font-medium">Parkir {{ $parkingFee['parking_type'] ?? '' }}: <span class="font-bold">Rp {{ number_format($parkingFee['fee'] ?? 0, 0, ',', '.') }}/bulan</span></span>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                                <p class="text-xs font-semibold text-gray-600 dark:text-gray-300 mt-3">*Biaya akan ditambahkan saat pembayaran</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Booking Form (right) -->
                <div class="lg:col-span-5 lg:pl-4">
                    <!-- Liquid glass booking form card -->
                    <div class="p-8 sticky top-8" style="background: var(--glass-bg, rgba(255, 255, 255, 0.18)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-lg, 1.75rem); border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35)); box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10));">
                        <!-- Status and Price Summary -->
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white mb-2">{{ __('properties.booking.book_room') }}</h2>
                                <p class="text-gray-700 dark:text-gray-200 font-medium">{{ __('properties.booking.fill_details') }}</p>
                            </div>
                            <div class="text-right">
                                <!-- Room Status — green for available, gray for unavailable, with dark mode variants -->
                                @if(!($room['is_available'] ?? ($room['rental_status'] != 1)))
                                    <span id="roomStatus" class="px-4 py-2 rounded-full text-sm font-medium bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800">
                                        {{ __('properties.status.unavailable') }}
                                    </span>
                                @else
                                    <span id="roomStatus" class="px-4 py-2 rounded-full text-sm font-medium
                                        {{ $room['status'] == 1 ? 'bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' : 'bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' }}">
                                        {{ $room['status'] == 1 ? __('properties.status.available') : __('properties.status.unavailable') }}
                                    </span>
                                @endif

                                <!-- Availability Status -->

                            </div>
                        </div>

                        <!-- Profile Completion Warning -->
                        @auth
                            @php
                                $user = Auth::user();
                                $missingFields = [];

                                if (empty($user->first_name)) {
                                    $missingFields[] = __('properties.booking.first_name');
                                }
                                if (empty($user->last_name)) {
                                    $missingFields[] = __('properties.booking.last_name');
                                }
                                if (empty($user->name)) {
                                    $missingFields[] = __('properties.booking.full_name');
                                }
                                if (empty($user->phone_number)) {
                                    $missingFields[] = __('properties.booking.phone_number');
                                }
                            @endphp

                            @if(count($missingFields) > 0)
                                <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <h3 class="text-sm font-medium text-yellow-800">
                                                {{ __('properties.booking.complete_profile') }}
                                            </h3>
                                            <div class="mt-2 text-sm text-yellow-700">
                                                <p class="mb-2">{{ __('properties.booking.please_complete') }}</p>
                                                <ul class="list-disc list-inside space-y-1">
                                                    @foreach($missingFields as $field)
                                                        <li>{{ $field }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('profile.show') }}" class="inline-flex items-center text-sm font-medium text-yellow-800 hover:text-yellow-900 underline">
                                                    <i class="fas fa-user-edit mr-1"></i>
                                                    {{ __('properties.booking.complete_profile_now') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endauth
                        
                        <form id="bookingForm" class="space-y-6" method="POST" action="{{ route('bookings.store') }}" novalidate>
                            @csrf
                            <input type="hidden" name="property_name" value="{{ $room['property_name'] ?? ($property['name'] ?? $room['name']) }}">
                            <input type="hidden" name="room_name" value="{{ $room['name'] }}">
                            <input type="hidden" name="room_id" id="roomId" value="{{ $room['id'] }}">
                            <input type="hidden" name="property_id" id="propertyId" value="{{ $property['id'] ?? $room['property_id'] ?? '' }}">
                            <input type="hidden" name="price_daily" id="priceDaily" value="{{ $room['price_original_daily'] }}">
                            <input type="hidden" name="price_monthly" id="priceMonthly" value="{{ $room['price_original_monthly'] }}">
                            <input type="hidden" name="service_fees" id="serviceFees" value="{{ $room['service_fees'] }}">
                            <input type="hidden" name="booking_type" id="bookingType" value="">
                            {{-- <!-- Multi-Tier Pricing: annual price + room ID for price-preview API --> --}}
                            <input type="hidden" name="price_annual" id="priceAnnual" value="{{ $room['price_original_annual'] ?? 0 }}">
                            <input type="hidden" name="years" id="bookingYears" value="1">
                            {{-- <input type="hidden" name="tax_fees" id="taxFees" value="{{ $room['tax_fees'] ?? 0 }}"> --}}
                            <!-- Rental Type -->
                            <div class="mb-6">
                                <label for="rent_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('properties.booking.booking_type') }}</label>
                                <select id="rent_type" name="rent_type"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
                                    onchange="updateRentalType()">
                                    @if($room['periode_daily'] == 1)
                                        <option value="daily" {{ $room['periode_monthly'] == 1 ? '' : 'selected' }}>{{ __('properties.booking.daily') }}</option>
                                    @endif
                                    @if($room['periode_monthly'] == 1)
                                        <option value="monthly" {{ $room['periode_daily'] == 1 ? '' : 'selected' }}>{{ __('properties.booking.monthly') }}</option>
                                    @endif
                                    {{-- <!-- Multi-Tier Pricing: Annual booking option --> --}}
                                    @if(!empty($room['periode_annual']) && $room['periode_annual'] == 1)
                                        <option value="annual">Tahunan</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Daily Booking Component -->
                            <div id="dailyBookingComponent" class="space-y-4 hidden">
                                <div class="bg-blue-50 dark:bg-gray-700 border border-blue-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-calendar-day text-blue-600 mr-2"></i>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('properties.booking.daily_booking') }}</h3>
                                    </div>

                                    {{-- Check-in + Check-out side by side: two columns on tablet/desktop,
                                         stacked on phones (matches the monthly booking layout). --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Check-in Date -->
                                        <div>
                                            <label for="check_in" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <i class="fas fa-sign-in-alt mr-1 text-gray-500"></i>{{ __('properties.booking.check_in') }}
                                            </label>
                                            <input type="text" id="check_in" name="check_in"
                                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all"
                                                placeholder="Select check-in date" data-required="true" readonly>
                                            <div id="check_inError" class="text-red-500 text-xs mt-1 hidden error-message"></div>
                                        </div>

                                        <!-- Check-out Date -->
                                        <div>
                                            <label for="check_out" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <i class="fas fa-sign-out-alt mr-1 text-gray-500"></i>{{ __('properties.booking.check_out') }}
                                            </label>
                                            <input type="text" id="check_out" name="check_out"
                                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all"
                                                placeholder="Select check-out date" data-required="true" readonly>
                                            <div id="check_outError" class="text-red-500 text-xs mt-1 hidden error-message"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Booking Component -->
                            <div id="monthlyBookingComponent" class="space-y-4 hidden">
                                <div class="bg-purple-50 dark:bg-gray-700 border border-purple-200 dark:border-gray-600 rounded-lg p-4">
                                    <div class="flex items-center mb-3">
                                        <i class="fas fa-calendar-alt text-purple-600 mr-2"></i>
                                        <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ __('properties.booking.monthly_booking') }}</h3>
                                    </div>

                                    {{-- Check-in (editable) + Check-out (read-only, computed) side by side.
                                         Two columns on tablet/desktop, stacked on phones. The check-out is derived
                                         by updatePriceSummary() using the same clamped-month math the server applies
                                         in BookingController::store (so Jan 31 + 1 mo → Feb 28, not Mar 3). --}}
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <!-- Check-in Date -->
                                        <div>
                                            <label for="check_in_monthly" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <i class="fas fa-calendar-check mr-1 text-gray-500"></i>{{ __('properties.booking.check_in') }}
                                            </label>
                                            <input type="text" id="check_in_monthly" name="check_in_monthly"
                                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all"
                                                placeholder="Select check-in date" data-required="true" readonly>
                                            <div id="check_in_monthlyError" class="text-red-500 text-xs mt-1 hidden error-message"></div>
                                        </div>

                                        <!-- Check-out Date (computed, read-only) -->
                                        <div>
                                            <label for="check_out_monthly_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <i class="fas fa-calendar-times mr-1 text-gray-500"></i>{{ __('properties.booking.check_out') }}
                                            </label>
                                            <input type="text" id="check_out_monthly_display" readonly
                                                class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 cursor-not-allowed opacity-90"
                                                placeholder="—">
                                        </div>
                                    </div>

                                    <!-- Months Selection -->
                                    <div>
                                        <label for="months" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <i class="fas fa-hourglass-half mr-1 text-gray-500"></i>{{ __('properties.booking.duration') }}
                                        </label>
                                        <select id="months" name="months"
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 focus:border-teal-500 focus:ring-2 focus:ring-teal-200 transition-all"
                                            onchange="updatePriceSummary()">
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}">{{ $i }} {{ __('properties.booking.months') }}</option>
                                            @endfor
                                        </select>
                                        <input type="hidden" name="booking_months" id="bookingMonths" value="1">
                                    </div>
                                </div>
                            </div>
                            {{-- Availability Check - Commented out, now using roomStatus badge at top --}}
                            {{-- <div class="mt-3">
                                <div id="availabilityStatus" class="text-sm p-3 rounded-lg border border-gray-200 bg-gray-50 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                            <span class="text-gray-600 font-medium">{{ __('properties.booking.availability_status') }}</span>
                                        </div>
                                        <span class="text-xs text-gray-500">{{ __('properties.booking.waiting_date_selection') }}</span>
                                    </div>
                                </div>
                            </div> --}}

                            <!-- Price Summary -->
                            <!-- Base Rates Info -->
                             {{--  
                             <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                 <h4 class="font-medium text-gray-900 mb-3">Rate Information</h4>
                                 <div class="space-y-4">
                                     
                                    <div id="dailyRateInfo" class="hidden">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Daily Rate</span>
                                            @if($room['price']['discounted']['daily'] < $room['price']['original']['daily'])
                                                <div class="text-right">
                                                    <span class="line-through text-gray-400 text-sm">Rp {{ number_format($room['price']['original']['daily'], 0, ',', '.') }}</span>
                                                    <span class="font-semibold text-teal-600 ml-2">Rp {{ number_format($room['price']['discounted']['daily'], 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span class="font-semibold">Rp {{ number_format($room['price']['original']['daily'], 0, ',', '.') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">Rate per night stay</p>
                                        </div>
                                        
                                    
                                    <div id="monthlyRateInfo" class="hidden">
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-600">Monthly Rate</span>
                                            @if($room['price']['discounted']['monthly'] < $room['price']['original']['monthly'])
                                                <div class="text-right">
                                                    <span class="line-through text-gray-400 text-sm">Rp {{ number_format($room['price']['original']['monthly'], 0, ',', '.') }}</span>
                                                    <span class="font-semibold text-teal-600 ml-2">Rp {{ number_format($room['price']['discounted']['monthly'], 0, ',', '.') }}</span>
                                                </div>
                                            @else
                                                <span class="font-semibold">Rp {{ number_format($room['price']['original']['monthly'], 0, ',', '.') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">Rate per month stay</p>
                                    </div>
                                </div>
                            </div> 
                            --}}
                            
                            <!-- Price Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                <h4 class="font-bold text-gray-900 dark:text-white mb-3">{{ __('properties.booking.total_price') }}</h4>
                                <div class="space-y-3 text-sm">
                                    <!-- Rate row — hidden for daily bookings, shown for monthly -->
                                    <div class="flex items-center justify-between" id="rateRow">
                                        <span class="text-gray-700 dark:text-gray-200 font-medium" id="rateTypeDisplay">{{ __('properties.booking.daily_rate') }}: </span>
                                        <div class="text-right">
                                            <div class="text-gray-900 dark:text-white font-semibold" id="rateDisplay">
                                                <!-- Daily Rate Display -->
                                                <div id="dailyRateDisplay" class="hidden">
                                                    <span class="font-bold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_original_daily'], 0, ',', '.') }}</span>
                                                    <div class="text-xs font-semibold text-gray-600 dark:text-gray-300 mt-1">{{ __('properties.room_detail.per_night') }}</div>
                                                </div>
                                                <!-- Monthly Rate Display -->
                                                <div id="monthlyRateDisplay" class="hidden">
                                                    <span class="font-bold text-green-700 dark:text-green-400">Rp {{ number_format($room['price_original_monthly'], 0, ',', '.') }}</span>
                                                    <div class="text-xs font-semibold text-gray-600 dark:text-gray-300 mt-1">{{ __('properties.room_detail.per_month') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-200 font-medium">{{ __('properties.booking.duration_label') }} </span>
                                        <span class="text-gray-900 dark:text-white font-semibold" id="durationDisplay">-</span>
                                    </div>

                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-200 font-medium">{{ __('properties.booking.room_total') }}</span>
                                        <span class="text-gray-900 dark:text-white font-semibold" id="roomTotal">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-200 font-medium">{{ __('properties.booking.service_fees') }} </span>
                                        <span class="text-gray-900 dark:text-white font-semibold" id="serviceFeesDisplay">-</span>
                                    </div>
                                    {{-- <div class="flex justify-between">
                                        <span class="text-gray-600">Tax and Fees (20%): </span>
                                        <span class="text-gray-900" id="taxDisplay">-</span>
                                    </div> --}}

                                    {{-- <div class="flex justify-between">
                                        <span class="text-gray-600">Admin Fee:</span>
                                        <span class="text-gray-900" id="adminFee">{{ number_format($room['admin_fees'], 0, ',', '.') }}</span>
                                    </div> --}}

                                    <div class="flex justify-between font-bold text-lg pt-3 border-t border-gray-200 dark:border-gray-600 mt-3">
                                        <span class="text-gray-900 dark:text-white">{{ __('properties.booking.total') }}</span>
                                        <span class="text-green-700 dark:text-green-400" id="grandTotal">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Error Alert -->
                            <div id="errorAlert" class="hidden bg-red-50 text-red-800 p-4 rounded-lg text-sm mb-6"></div>

                            <!-- Rental Agreement Checkbox -->
                            @auth
                                <div id="rentalAgreementSection" class="hidden mb-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input id="agreementCheckbox" type="checkbox"
                                                class="w-4 h-4 text-teal-600 bg-gray-100 border-gray-300 rounded focus:ring-teal-500 focus:ring-2">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="agreementCheckbox" class="text-gray-800 dark:text-gray-100 font-medium">
                                                {{-- :terms_link / :privacy_link / :rental_link are substituted with anchor tags
                                                     pointing to the legal pages. {!! !!} is required so the HTML renders;
                                                     route() returns trusted URLs and link text is e()-escaped. --}}
                                                {!! __('properties.booking.rental_agreement', [
                                                    'terms_link'   => '<a href="' . route('terms-of-services') . '" target="_blank" rel="noopener" class="text-green-700 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-semibold underline">' . e(__('properties.booking.terms_link_text')) . '</a>',
                                                    'privacy_link' => '<a href="' . route('privacy-policy') . '" target="_blank" rel="noopener" class="text-green-700 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-semibold underline">' . e(__('properties.booking.privacy_link_text')) . '</a>',
                                                    'rental_link'  => '<a href="' . route('rental-agreement') . '" target="_blank" rel="noopener" class="text-green-700 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 font-semibold underline">' . e(__('properties.booking.rental_link_text')) . '</a>',
                                                ]) !!}
                                            </label>
                                        </div>
                                    </div>
                                    <div id="agreementError" class="hidden mt-2 text-sm text-red-600">
                                        <p><i class="fas fa-exclamation-circle mr-1"></i>{{ __('properties.booking.must_agree') }}</p>
                                    </div>
                                </div>
                            @endauth

                            <!-- Submit Button -->
                            <div>
                                @guest
                                    <button type="button" id="guestSubmitButton"
                                        class="w-full bg-teal-600 text-white py-4 px-6 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200 text-lg font-medium flex items-center justify-center gap-2"
                                        onclick="window.location.href = '/login'">
                                        <i class="fas fa-lock"></i>
                                        {{ __('properties.booking.login_to_book') }}
                                    </button>
                                    <p class="text-sm text-gray-500 text-center mt-2">{{ __('properties.booking.please_login') }}</p>
                                @else
                                    @if(!($room['is_available'] ?? ($room['rental_status'] != 1)))
                                        <button type="button" id="checkAvailabilityButton"
                                            class="w-full bg-gray-400 text-white py-4 px-6 rounded-lg text-lg font-medium cursor-not-allowed"
                                            disabled>
                                            {{ __('properties.booking.room_unavailable') }}
                                        </button>
                                    @else
                                        <button type="button" id="checkAvailabilityButton"
                                            onclick="checkRoomAvailability()"
                                            class="w-full {{ $room['status'] == 0 ? 'bg-gray-400' : 'bg-teal-600' }} text-white py-4 px-6 rounded-lg {{ $room['status'] == 0 ? '' : 'hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200' }} text-lg font-medium"
                                            {{ $room['status'] == 0 ? 'disabled' : '' }}>
                                            {{ $room['status'] == 1 ? __('properties.booking.check_availability') : __('properties.booking.room_unavailable') }}
                                        </button>
                                        <button type="submit" id="submitButton"
                                            class="w-full bg-teal-600 text-white py-4 px-6 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200 text-lg font-medium hidden">
                                            {{ __('properties.booking.book_now') }}
                                        </button>
                                    @endif
                                    <div id="unavailableMessage" class="hidden mt-2 text-sm text-red-600">
                                        <p><i class="fas fa-exclamation-triangle mr-1"></i>{{ __('properties.booking.room_unavailable_dates') }}</p>
                                    </div>
                                @endguest
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-teal-500"></div>
        </div>
    </div>

    <!-- Footer -->
    @include('components.homepage.footer')

    <script>
        // Clamped month addition: avoids overflow when target month has fewer days
        // e.g. Jan 31 + 1 month = Feb 28, Mar 31 + 1 month = Apr 30, Feb 29 + 1 month = Mar 29
        function addMonthsClamped(date, months) {
            const d = new Date(date);
            const day = d.getDate();
            d.setDate(1); // set to 1st to avoid overflow during setMonth
            d.setMonth(d.getMonth() + months);
            // clamp day to last day of target month
            const maxDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
            d.setDate(Math.min(day, maxDay));
            return d;
        }

        // --- Global variables ---
        let bookingForm, errorAlert, loadingOverlay, submitButton, monthInput, dateInputs, monthsSelect, rentTypeSelect;
        let checkInInput, checkOutInput, roomStatusSpan;
        let availabilityCheckTimeout;

        // --- Global Room Availability Function ---
        const isAvailable = {{ ($room['is_available'] ?? ($room['rental_status'] != 1)) ? 'true' : 'false' }};

        async function checkRoomAvailability() {
            // Skip availability check if room is not available
            if (!isAvailable) {
                showAvailabilityStatus('unavailable', 'Kamar sedang disewa');
                updateSubmitButton(false, 'Tidak Tersedia');
                return;
            }

            // console.log('=== CHECK ROOM AVAILABILITY STARTED ===');

            const propertyId = document.getElementById('propertyId').value;
            const roomId = document.querySelector('[name="room_id"]').value;
            const checkInDate = document.getElementById('check_in').value;
            const checkOutDate = document.getElementById('check_out').value;
            const rentType = document.getElementById('rent_type').value;
            
            // console.log('Input values:', {
            //     propertyId: propertyId,
            //     roomId: roomId,
            //     checkInDate: checkInDate,
            //     checkOutDate: checkOutDate,
            //     rentType: rentType,
            //     timestamp: new Date().toISOString()
            // });
            
            // Clear previous timeout to avoid multiple rapid calls
            if (availabilityCheckTimeout) {
                // console.log('Clearing previous timeout');
                clearTimeout(availabilityCheckTimeout);
            }
            
            // Check availability for both daily and monthly rentals (with dates)
            // console.log('Checking if should proceed with availability check');
            if (rentType === 'monthly') {
                if (!checkInDate) {
                    // console.log('Skipping - missing check-in date for monthly rental');
                    resetAvailabilityStatus();
                    return;
                }
                // console.log('Proceeding with monthly rental availability check');
            } else if (rentType === 'daily') {
                if (!checkInDate || !checkOutDate) {
                    // console.log('Skipping - missing dates for daily rental');
                    resetAvailabilityStatus();
                    return;
                }
                // console.log('Proceeding with daily rental availability check');
            } else {
                // console.log('Skipping - unknown rent type:', rentType);
                resetAvailabilityStatus();
                return;
            }
            
            // Prevent checking if check-out is before check-in
            // console.log('Validating date order');
            if (new Date(checkOutDate) <= new Date(checkInDate)) {
                console.log('Date validation failed - check-out before or same as check-in');
                showAvailabilityStatus('error', '{{ __("properties.booking.date_invalid") }}');
                updateSubmitButton(false, '{{ __("properties.booking.date_invalid") }}');
                return;
            }
            // console.log('Date validation passed');
            
            // Show loading state
            // console.log('Setting loading state');
            showAvailabilityStatus('loading', '{{ __("properties.booking.checking_availability") }}');
            
            availabilityCheckTimeout = setTimeout(async () => {
                // console.log('Starting async availability check');
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // console.log('CSRF Token retrieved:', csrfToken ? 'Present' : 'Missing');
                    
                    const apiUrl = '{{ route("api.booking.check-availability") }}';
                    
                    
                    const months = rentType === 'monthly'
                        ? (parseInt(document.getElementById('months')?.value, 10) || 1)
                        : undefined;

                    const requestBody = JSON.stringify({
                        property_id: propertyId,
                        room_id: roomId,
                        check_in: checkInDate,
                        check_out: checkOutDate,
                        period: rentType,
                        // booking_type is what the server uses to pick the type-specific cap
                        // (new daily ≤+90d / new monthly ≤+14d). period kept for backwards compat.
                        booking_type: rentType,
                        ...(months !== undefined && { months })
                    });
                    // console.log('Request body:', requestBody);
                    
                    const response = await fetch(apiUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-API-KEY': '{{ env('API_KEY') }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: requestBody
                    });
                    
                    // console.log('Response received:', {
                    //     status: response.status,
                    //     statusText: response.statusText,
                    //     headers: Object.fromEntries(response.headers.entries())
                    // });
                    
                    const data = await response.json();
                    // console.log('Response data:', data);
                    
                    if (!response.ok || data.status === 'error') {
                        const errorMessage = data.message || 'Gagal memeriksa ketersediaan';
                        console.log('Error response:', errorMessage);
                        if (data.errors) {
                            const errorMessages = Object.values(data.errors).flat();
                            console.log('Validation errors:', errorMessages);
                            showAvailabilityStatus('error', errorMessages[0] || errorMessage);
                        } else {
                            showAvailabilityStatus('error', errorMessage);
                        }
                        updateSubmitButton(false, 'Validasi gagal');
                        return;
                    }
                    
                    // Handle the availability response
                    if (data.data && data.data.is_available !== undefined) {
                        const isAvailable = data.data.is_available;
                        const conflictingBookings = data.data.conflicting_bookings || [];
                        
                        // console.log('Availability result:', {
                        //     isAvailable: isAvailable,
                        //     conflictingBookings: conflictingBookings
                        // });
                        
                        if (isAvailable) {
                            console.log('✓ Room is available');
                            showAvailabilityStatus('available', '✓ {{ __("properties.status.available") }}');
                            updateSubmitButton(true);
                        } else {
                            console.log('✗ Room is unavailable');
                            showAvailabilityStatus('unavailable', '✗ {{ __("properties.status.unavailable") }}');
                            if (conflictingBookings.length > 0) {
                                // console.log('Found conflicting bookings:', conflictingBookings);
                                showAvailabilityStatus('unavailable', `✗ {{ __("properties.status.unavailable") }} - ${conflictingBookings.length}`);
                            }
                            updateSubmitButton(false, '{{ __("properties.booking.room_unavailable") }}');
                        }
                    } else {
                        console.error('Invalid API response structure:', data);
                        showAvailabilityStatus('error', 'Respon API tidak valid');
                        updateSubmitButton(false, 'Kesalahan sistem');
                    }
                    
                } catch (error) {
                    console.error('Caught error in availability check:', {
                        message: error.message,
                        stack: error.stack,
                        name: error.name
                    });
                    showAvailabilityStatus('error', 'Gagal memeriksa ketersediaan kamar');
                    updateSubmitButton(false, 'Gagal mengecek');
                }
            }, 500); // Add small delay to prevent excessive API calls
            
            // console.log('=== CHECK ROOM AVAILABILITY SCHEDULED ===');
        }

        // --- Other Global Utility Functions ---
        function formatRupiah(num) {
            return `Rp ${num.toLocaleString('id-ID')}`;
        }
        
        /* Badge color map — dark mode aware via isDark flag */
        function showAvailabilityStatus(type, message) {
            if (!roomStatusSpan) return;

            const isDark = document.documentElement.classList.contains('dark');
            let className = 'px-4 py-2 rounded-full text-sm font-medium border ';
            let statusText = '';

            switch (type) {
                case 'loading':
                    className += isDark ? 'bg-blue-900/40 text-blue-300 border-blue-800' : 'bg-blue-100 text-blue-700 border-blue-200';
                    statusText = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("properties.booking.checking_availability") }}';
                    break;
                case 'available':
                    className += isDark ? 'bg-green-900/40 text-green-300 border-green-800' : 'bg-green-100 text-green-700 border-green-200';
                    statusText = '{{ __("properties.status.available") }}';
                    break;
                case 'unavailable':
                    className += isDark ? 'bg-red-900/40 text-red-300 border-red-800' : 'bg-red-100 text-red-700 border-red-200';
                    statusText = '{{ __("properties.status.unavailable") }}';
                    break;
                case 'error':
                    className += isDark ? 'bg-yellow-900/40 text-yellow-300 border-yellow-800' : 'bg-yellow-100 text-yellow-700 border-yellow-200';
                    statusText = 'Error';
                    break;
                default:
                    className += isDark ? 'bg-gray-700 text-gray-300 border-gray-600' : 'bg-gray-100 text-gray-600 border-gray-200';
                    statusText = '{{ __("properties.booking.waiting_date_selection") }}';
            }

            roomStatusSpan.className = className;
            roomStatusSpan.innerHTML = statusText;
        }

        function resetAvailabilityStatus() {
            if (!roomStatusSpan) return;
            const isRoomAvailable = {{ $room['status'] == 1 && ($room['is_available'] ?? ($room['rental_status'] != 1)) ? 'true' : 'false' }};
            const isDark = document.documentElement.classList.contains('dark');
            if (isAvailable) {
                roomStatusSpan.className = 'px-4 py-2 rounded-full text-sm font-medium border ' + (isDark ? 'bg-green-900/40 text-green-300 border-green-800' : 'bg-green-100 text-green-700 border-green-200');
                roomStatusSpan.innerHTML = '{{ __("properties.status.available") }}';
            } else {
                roomStatusSpan.className = 'px-4 py-2 rounded-full text-sm font-medium border ' + (isDark ? 'bg-red-900/40 text-red-300 border-red-800' : 'bg-red-100 text-red-700 border-red-200');
                roomStatusSpan.innerHTML = '{{ __("properties.status.unavailable") }}';
            }
        }

        function updateSubmitButton(isEnabled, customMessage = null) {
            const checkAvailabilityButton = document.getElementById('checkAvailabilityButton');
            const submitButton = document.getElementById('submitButton');
            const rentalAgreementSection = document.getElementById('rentalAgreementSection');

            if (!checkAvailabilityButton || !submitButton) return;

            if (isEnabled && {{ $room['status'] ?? 0 }} == 1) {
                // Room is available - show green submit button, rental agreement checkbox, and hide check availability button
                checkAvailabilityButton.classList.add('hidden');
                submitButton.classList.remove('hidden');
                submitButton.disabled = false;
                submitButton.className = 'w-full bg-teal-600 text-white py-4 px-6 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200 text-lg font-medium';
                submitButton.textContent = '{{ __("properties.booking.book_now") }}';

                // Show rental agreement checkbox
                if (rentalAgreementSection) {
                    rentalAgreementSection.classList.remove('hidden');
                }
            } else {
                // Room is not available - show grey submit button and hide check availability button and agreement
                checkAvailabilityButton.classList.add('hidden');
                submitButton.classList.remove('hidden');
                submitButton.disabled = true;
                submitButton.className = 'w-full bg-gray-400 text-white py-4 px-6 rounded-lg text-lg font-medium cursor-not-allowed';

                // Hide rental agreement checkbox
                if (rentalAgreementSection) {
                    rentalAgreementSection.classList.add('hidden');
                }

                if (customMessage) {
                    submitButton.textContent = customMessage;
                } else if ({{ $room['status'] ?? 0 }} == 0) {
                    submitButton.textContent = '{{ __("properties.booking.room_unavailable") }}';
                } else {
                    submitButton.textContent = '{{ __("properties.booking.room_unavailable") }}';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize element references
            bookingForm = document.getElementById('bookingForm');
            errorAlert = document.getElementById('errorAlert');
            loadingOverlay = document.getElementById('loadingOverlay');
            checkInInput = document.getElementById('check_in');
            checkOutInput = document.getElementById('check_out');
            monthInput = document.getElementById('monthInput');
            dateInputs = document.getElementById('dateInputs');
            monthsSelect = document.getElementById('months');
            rentTypeSelect = document.querySelector('select[name="rent_type"]');
            roomStatusSpan = document.getElementById('roomStatus');
            
            // Remove duplicate element references that were declared later
            const priceDailyInput = document.getElementById('priceDaily');
            const priceMonthlyInput = document.getElementById('priceMonthly');

            // --- Utility functions ---
            function getDaysBetweenDates(start, end) {
                const d1 = new Date(start); d1.setHours(0,0,0,0);
                const d2 = new Date(end); d2.setHours(0,0,0,0);
                return Math.ceil((d2 - d1) / (1000 * 60 * 60 * 24));
            }
            function formatRupiah(num) {
                return `Rp ${num.toLocaleString('id-ID')}`;
            }
            function resetSummary() {
                document.getElementById('durationDisplay').textContent = '-';
                document.getElementById('roomTotal').textContent = '-';
                // document.getElementById('taxDisplay').textContent = '-';
                document.getElementById('grandTotal').textContent = '-';
            }

            

            function showAvailabilityStatus(type, message) {
                if (!roomStatusSpan) return;

                const isDark = document.documentElement.classList.contains('dark');
                let className = 'px-4 py-2 rounded-full text-sm font-medium border ';
                let statusText = '';

                switch (type) {
                    case 'loading':
                        className += isDark ? 'bg-blue-900/40 text-blue-300 border-blue-800' : 'bg-blue-100 text-blue-700 border-blue-200';
                        statusText = '<i class="fas fa-spinner fa-spin mr-1"></i> {{ __("properties.booking.checking_availability") }}';
                        break;
                    case 'available':
                        className += isDark ? 'bg-green-900/40 text-green-300 border-green-800' : 'bg-green-100 text-green-700 border-green-200';
                        statusText = '{{ __("properties.status.available") }}';
                        break;
                    case 'unavailable':
                        className += isDark ? 'bg-red-900/40 text-red-300 border-red-800' : 'bg-red-100 text-red-700 border-red-200';
                        statusText = '{{ __("properties.status.unavailable") }}';
                        break;
                    case 'error':
                        className += isDark ? 'bg-yellow-900/40 text-yellow-300 border-yellow-800' : 'bg-yellow-100 text-yellow-700 border-yellow-200';
                        statusText = '{{ __("properties.booking.date_invalid") }}';
                        break;
                    default:
                        className += isDark ? 'bg-gray-700 text-gray-300 border-gray-600' : 'bg-gray-100 text-gray-600 border-gray-200';
                        statusText = '{{ __("properties.booking.waiting_date_selection") }}';
                }

                roomStatusSpan.className = className;
                roomStatusSpan.innerHTML = statusText;
            }

            function resetAvailabilityStatus() {
                if (!roomStatusSpan) return;
                const isRoomAvailable = {{ $room['status'] == 1 && ($room['is_available'] ?? ($room['rental_status'] != 1)) ? 'true' : 'false' }};
                const isDark = document.documentElement.classList.contains('dark');
                if (isAvailable) {
                    roomStatusSpan.className = 'px-4 py-2 rounded-full text-sm font-medium border ' + (isDark ? 'bg-green-900/40 text-green-300 border-green-800' : 'bg-green-100 text-green-700 border-green-200');
                    roomStatusSpan.innerHTML = '{{ __("properties.status.available") }}';
                } else {
                    roomStatusSpan.className = 'px-4 py-2 rounded-full text-sm font-medium border ' + (isDark ? 'bg-red-900/40 text-red-300 border-red-800' : 'bg-red-100 text-red-700 border-red-200');
                    roomStatusSpan.innerHTML = '{{ __("properties.status.unavailable") }}';
                }
            }

            function updateSubmitButton(isEnabled, customMessage = null) {
                const checkAvailabilityButton = document.getElementById('checkAvailabilityButton');
                const submitButton = document.getElementById('submitButton');

                if (!checkAvailabilityButton || !submitButton) return;

                if (isEnabled && {{ $room['status'] ?? 0 }} == 1) {
                    // Room is available - show green submit button and hide check availability button
                    checkAvailabilityButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                    submitButton.disabled = false;
                    submitButton.className = 'w-full bg-teal-600 text-white py-4 px-6 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200 text-lg font-medium';
                    submitButton.textContent = '{{ __("properties.booking.book_now") }}';
                } else {
                    // Room is not available - show grey submit button and hide check availability button
                    checkAvailabilityButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                    submitButton.disabled = true;
                    submitButton.className = 'w-full bg-gray-400 text-white py-4 px-6 rounded-lg text-lg font-medium cursor-not-allowed';

                    if (customMessage) {
                        submitButton.textContent = customMessage;
                    } else if ({{ $room['status'] ?? 0 }} == 0) {
                        submitButton.textContent = '{{ __("properties.booking.room_unavailable") }}';
                    } else {
                        submitButton.textContent = '{{ __("properties.booking.room_unavailable") }}';
                    }
                }
            }

            // --- Price summary logic ---
            /* Multi-Tier Pricing: Updated price summary — calls price-preview API for daily bookings */
            function updatePriceSummary() {
                checkRoomAvailability();
                const rentTypeSelect = document.getElementById('rent_type');
                const checkInInput = document.getElementById('check_in');
                const checkOutInput = document.getElementById('check_out');
                const monthsSelect = document.getElementById('months');
                const priceDailyInput = document.getElementById('priceDaily');
                const priceMonthlyInput = document.getElementById('priceMonthly');
                const priceAnnualInput = document.getElementById('priceAnnual');

                if (!rentTypeSelect || !checkInInput || !checkOutInput || !monthsSelect || !priceDailyInput || !priceMonthlyInput) {
                    return;
                }

                const rentType = rentTypeSelect.value;
                const rateTypeDisplay = document.getElementById('rateTypeDisplay');
                const dailyRateDisplay = document.getElementById('dailyRateDisplay');
                const monthlyRateDisplay = document.getElementById('monthlyRateDisplay');
                const durationDisplay = document.getElementById('durationDisplay');

                if (rateTypeDisplay) {
                    const labels = { daily: '{{ __("properties.booking.daily_rate") }}', monthly: '{{ __("properties.booking.monthly_rate") }}', annual: 'Harga Tahunan' };
                    rateTypeDisplay.textContent = labels[rentType] || '';
                }
                if (dailyRateDisplay) dailyRateDisplay.classList.toggle('hidden', rentType !== 'daily');
                if (monthlyRateDisplay) monthlyRateDisplay.classList.toggle('hidden', rentType !== 'monthly');
                // Hide the entire rate row for daily bookings (price breakdown per date is shown instead)
                const rateRow = document.getElementById('rateRow');
                if (rateRow) rateRow.classList.toggle('hidden', rentType === 'daily');

                let duration = 0, roomTotal = 0;

                try {
                    if (rentType === 'daily') {
                        if (!checkInInput.value || !checkOutInput.value) return resetSummary();
                        const nights = getDaysBetweenDates(checkInInput.value, checkOutInput.value);
                        if (nights <= 0) return resetSummary();

                        /* Call price-preview API for per-date breakdown */
                        const roomId = document.getElementById('roomId').value;
                        /* Daily Multi Tier Pricing: fetch per-date price breakdown with API key */
                        fetch(`/api/v1/rooms/${roomId}/price-preview?check_in=${checkInInput.value}&check_out=${checkOutInput.value}`, {
                            headers: { 'X-API-KEY': '{{ env('API_KEY') }}', 'Accept': 'application/json' }
                        })
                            .then(res => res.json())
                            .then(json => {
                                if (json.status === 'success' && json.data.breakdown.length > 0) {
                                    roomTotal = json.data.total_price;
                                    duration = json.data.total_days;
                                    if (durationDisplay) durationDisplay.textContent = `${duration} malam`;
                                    /* Show per-date breakdown */
                                    renderPriceBreakdown(json.data.breakdown);
                                } else {
                                    /* Fallback to flat rate */
                                    duration = nights;
                                    const rate = parseFloat(priceDailyInput.value) || 0;
                                    roomTotal = duration * rate;
                                    if (durationDisplay) durationDisplay.textContent = `${duration} malam`;
                                    hidePriceBreakdown();
                                }
                                updateTotals(roomTotal);
                            })
                            .catch(() => {
                                /* Fallback on error */
                                duration = nights;
                                const rate = parseFloat(priceDailyInput.value) || 0;
                                roomTotal = duration * rate;
                                if (durationDisplay) durationDisplay.textContent = `${duration} malam`;
                                hidePriceBreakdown();
                                updateTotals(roomTotal);
                            });
                        return; /* Don't update totals synchronously — handled in .then() */

                    } else if (rentType === 'annual') {
                        /* Multi-Tier Pricing: Annual booking — flat rate × years */
                        const years = parseInt(document.getElementById('bookingYears')?.value || '1', 10);
                        const annualRate = parseFloat(priceAnnualInput?.value) || 0;
                        duration = years;
                        roomTotal = duration * annualRate;
                        if (durationDisplay) durationDisplay.textContent = `${duration} tahun`;
                        hidePriceBreakdown();

                    } else {
                        duration = parseInt(monthsSelect.value || '1', 10);
                        const rate = parseFloat(priceMonthlyInput.value) || 0;
                        roomTotal = duration * rate;
                        if (durationDisplay) durationDisplay.textContent = `${duration} {{ __("properties.booking.months") }}`;
                        hidePriceBreakdown();

                        /* Compute the projected check-out date and surface it under "Durasi Sewa"
                           so the guest can sanity-check their stay length before paying. Mirrors
                           the clamp logic in BookingController::store() (Jan 31 + 1 month →
                           Feb 28, not Mar 3) so what we display equals what the server will store. */
                        const monthlyCheckInEl = document.getElementById('check_in_monthly');
                        const monthlyCheckOutDisplay = document.getElementById('check_out_monthly_display');
                        if (monthlyCheckInEl && monthlyCheckOutDisplay) {
                            const raw = (monthlyCheckInEl.value || '').trim();
                            if (raw) {
                                /* check_in_monthly is rendered by flatpickr as YYYY-MM-DD. Anything
                                   else (placeholder text, garbage) falls through to '—'. */
                                const parts = raw.split('-');
                                if (parts.length === 3) {
                                    const y0 = parseInt(parts[0], 10);
                                    const m0 = parseInt(parts[1], 10);
                                    const d0 = parseInt(parts[2], 10);
                                    if (!isNaN(y0) && !isNaN(m0) && !isNaN(d0)) {
                                        const targetMonthIdx = (m0 - 1) + duration; // 0-based month index
                                        const targetYear = y0 + Math.floor(targetMonthIdx / 12);
                                        const targetMonth = (targetMonthIdx % 12) + 1; // 1-based
                                        const lastDay = new Date(targetYear, targetMonth, 0).getDate();
                                        const clampedDay = Math.min(d0, lastDay);
                                        const mm = String(targetMonth).padStart(2, '0');
                                        const dd = String(clampedDay).padStart(2, '0');
                                        monthlyCheckOutDisplay.value = `${targetYear}-${mm}-${dd}`;
                                    } else {
                                        monthlyCheckOutDisplay.value = '';
                                    }
                                } else {
                                    monthlyCheckOutDisplay.value = '';
                                }
                            } else {
                                monthlyCheckOutDisplay.value = '';
                            }
                        }
                    }
                } catch (error) {
                    console.error('Error updating price summary:', error);
                    return resetSummary();
                }
                updateTotals(roomTotal);
            }

            /* Multi-Tier Pricing: Helper to update total displays */
            function updateTotals(roomTotal) {
                const serviceFees = 30000;
                const grandTotal = roomTotal + serviceFees;
                document.getElementById('roomTotal').textContent = formatRupiah(roomTotal);
                document.getElementById('serviceFeesDisplay').textContent = formatRupiah(serviceFees);
                document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
            }

            /* Multi-Tier Pricing: Render per-date price breakdown table */
            /* Inserted ABOVE the grand total line so user sees breakdown before final price */
            function renderPriceBreakdown(breakdown) {
                let container = document.getElementById('priceBreakdownContainer');
                if (!container) {
                    /* Create container and insert ABOVE the Total Price summary box */
                    container = document.createElement('div');
                    container.id = 'priceBreakdownContainer';
                    container.className = 'bg-gray-50 p-4 rounded-lg mb-4';
                    const priceSummaryBox = document.getElementById('grandTotal')?.closest('.bg-gray-50');
                    if (priceSummaryBox) {
                        priceSummaryBox.parentElement.insertBefore(container, priceSummaryBox);
                    }
                }

                /* Badge colors per price type — with dark mode support via inline styles */
                const typeColors = {
                    weekday: 'bg-blue-100 text-blue-700',
                    weekend: 'bg-purple-100 text-purple-700',
                    high_season: 'bg-red-100 text-red-700',
                    low_season: 'bg-green-100 text-green-700',
                    holiday: 'bg-orange-100 text-orange-700',
                    manual: 'bg-yellow-100 text-yellow-700',
                };

                /* Dark mode badge colors */
                const typeDarkColors = {
                    weekday: 'background-color: rgba(30,58,138,0.3); color: #93c5fd;',
                    weekend: 'background-color: rgba(88,28,135,0.3); color: #d8b4fe;',
                    high_season: 'background-color: rgba(127,29,29,0.3); color: #fca5a5;',
                    low_season: 'background-color: rgba(20,83,45,0.3); color: #86efac;',
                    holiday: 'background-color: rgba(154,52,18,0.3); color: #fdba74;',
                    manual: 'background-color: rgba(113,63,18,0.3); color: #fde047;',
                };

                const isDark = document.documentElement.classList.contains('dark');

                let html = '<h4 class="font-medium text-gray-900 mb-3">{{ __("properties.booking.price_breakdown_title") }}</h4>';
                html += '<div class="space-y-1 max-h-48 overflow-y-auto text-xs">';
                breakdown.forEach(d => {
                    const lightColor = typeColors[d.type] || 'bg-gray-100 text-gray-700';
                    const darkStyle = isDark ? (typeDarkColors[d.type] || 'background-color: rgba(55,65,81,0.4); color: #d1d5db;') : '';
                    const label = d.label ? ` — ${d.label}` : '';
                    html += `<div class="flex justify-between items-center py-1 px-2 rounded hover:bg-gray-50">
                        <div class="flex items-center gap-2">
                            <span>${d.date}</span>
                            <span class="text-gray-400">${d.day_name}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-medium ${lightColor}" ${isDark ? `style="${darkStyle}"` : ''}>${d.type}${label}</span>
                        </div>
                        <span class="font-medium">${formatRupiah(d.price)}</span>
                    </div>`;
                });
                html += '</div>';
                container.innerHTML = html;
                container.style.display = 'block';
            }

            /* Multi-Tier Pricing: Hide the breakdown container */
            function hidePriceBreakdown() {
                const container = document.getElementById('priceBreakdownContainer');
                if (container) container.style.display = 'none';
            }

            // --- Rental type toggle logic ---
            function updateRentalType() {
                const rentTypeSelect = document.getElementById('rent_type');
                const dailyBookingComponent = document.getElementById('dailyBookingComponent');
                const monthlyBookingComponent = document.getElementById('monthlyBookingComponent');
                const checkInInput = document.getElementById('check_in');
                const checkInMonthlyInput = document.getElementById('check_in_monthly');
                const checkOutInput = document.getElementById('check_out');
                const monthsSelect = document.getElementById('months');
                const dailyRateDisplay = document.getElementById('dailyRateDisplay');
                const monthlyRateDisplay = document.getElementById('monthlyRateDisplay');
                const rateTypeDisplay = document.getElementById('rateTypeDisplay');
                const bookingTypeInput = document.getElementById('bookingType');

                // Update booking_type based on rental selection
                if (bookingTypeInput) {
                    bookingTypeInput.value = rentTypeSelect.value;
                }

                if (rentTypeSelect.value === 'monthly') {
                    // Show monthly component, hide daily component
                    dailyBookingComponent.classList.add('hidden');
                    monthlyBookingComponent.classList.remove('hidden');

                    // Update rate display
                    dailyRateDisplay.classList.add('hidden');
                    monthlyRateDisplay.classList.remove('hidden');
                    rateTypeDisplay.textContent = '{{ __("properties.booking.monthly_rate") }}';

                    // Set months to default to 1
                    if (monthsSelect) {
                        monthsSelect.value = 1;
                        document.getElementById('bookingMonths').value = monthsSelect.value;
                    }

                    // Set check-in date for monthly (default to today)
                    const today = new Date();
                    const minCheckInDate = new Date(today);
                    // Default check-in is today (no offset)

                    if (checkInMonthlyInput) {
                        checkInMonthlyInput.value = minCheckInDate.toISOString().split('T')[0];
                        // Copy value to check_in for form submission
                        if (checkInInput) checkInInput.value = checkInMonthlyInput.value;
                        checkInMonthlyInput.addEventListener('change', function() {
                            if (checkInInput) checkInInput.value = checkInMonthlyInput.value;
                            updatePriceSummary();
                        });
                    }

                    // Calculate check-out date based on months
                    if (checkInMonthlyInput && checkOutInput) {
                        const checkInDate = new Date(checkInMonthlyInput.value);
                        const months = parseInt(monthsSelect.value, 10) || 1;
                        const checkOutDate = addMonthsClamped(checkInDate, months);
                        checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                    }

                } else if (rentTypeSelect.value === 'annual') {
                    /* Multi-Tier Pricing: Annual booking — reuse monthly component UI with years selector */
                    dailyBookingComponent.classList.add('hidden');
                    monthlyBookingComponent.classList.remove('hidden');
                    dailyRateDisplay.classList.add('hidden');
                    monthlyRateDisplay.classList.add('hidden');
                    rateTypeDisplay.textContent = 'Harga Tahunan';

                    if (monthsSelect) {
                        /* Repurpose months selector for years (1-5) */
                        monthsSelect.innerHTML = '';
                        for (let y = 1; y <= 5; y++) {
                            const opt = document.createElement('option');
                            opt.value = y;
                            opt.textContent = y + ' Tahun';
                            monthsSelect.appendChild(opt);
                        }
                        monthsSelect.value = 1;
                        document.getElementById('bookingYears').value = 1;
                        /* Sync years value when selector changes */
                        monthsSelect.onchange = function() {
                            document.getElementById('bookingYears').value = this.value;
                            updatePriceSummary();
                        };
                    }

                    const today = new Date();
                    if (checkInMonthlyInput) {
                        checkInMonthlyInput.value = today.toISOString().split('T')[0];
                        if (checkInInput) checkInInput.value = checkInMonthlyInput.value;
                    }
                    if (checkOutInput) {
                        const checkOutDate = new Date(today);
                        checkOutDate.setFullYear(checkOutDate.getFullYear() + 1);
                        checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                    }

                } else {
                    // Show daily component, hide monthly component
                    dailyBookingComponent.classList.remove('hidden');
                    monthlyBookingComponent.classList.add('hidden');

                    dailyRateDisplay.classList.remove('hidden');
                    monthlyRateDisplay.classList.add('hidden');
                    rateTypeDisplay.textContent = '{{ __("properties.booking.daily_rate") }}';

                    const today = new Date();
                    const minCheckInDate = new Date(today);
                    const minCheckOutDate = new Date(minCheckInDate);
                    minCheckOutDate.setDate(minCheckInDate.getDate() + 1);

                    if (checkInInput) {
                        checkInInput.value = minCheckInDate.toISOString().split('T')[0];
                        checkInInput.dispatchEvent(new Event('change'));
                    }
                    if (checkOutInput) {
                        checkOutInput.value = minCheckOutDate.toISOString().split('T')[0];
                        checkOutInput.dispatchEvent(new Event('change'));
                    }

                    /* Restore months selector if it was replaced by annual years */
                    if (monthsSelect && monthsSelect.options.length <= 5 && monthsSelect.options[0]?.textContent.includes('Tahun')) {
                        monthsSelect.innerHTML = '';
                        for (let m = 1; m <= 12; m++) {
                            const opt = document.createElement('option');
                            opt.value = m;
                            opt.textContent = m;
                            monthsSelect.appendChild(opt);
                        }
                    }
                }
                updatePriceSummary();
            }

            // --- Date input logic ---
            function handleCheckInChange() {
                const rentTypeSelect = document.getElementById('rent_type');
                const monthsSelect = document.getElementById('months');
                const checkInInput = document.getElementById('check_in');
                const checkOutInput = document.getElementById('check_out');

                if (!checkInInput.value) return;

                if (rentTypeSelect.value === 'monthly') {
                    // For monthly, update check-out based on months
                    const checkInDate = new Date(checkInInput.value);
                    const months = parseInt(monthsSelect.value, 10) || 1;
                    const checkOutDate = addMonthsClamped(checkInDate, months);
                    checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                } else {
                    // For daily, apply month boundary and 14-day limit
                    const checkInDate = new Date(checkInInput.value);
                    const minCheckout = new Date(checkInDate);
                    minCheckout.setDate(checkInDate.getDate() + 1);

                    // Calculate max checkout: 14 days OR end of month, whichever is earlier
                    const maxCheckoutDate = new Date(checkInDate);
                    maxCheckoutDate.setDate(checkInDate.getDate() + 60);

                    const actualMaxCheckout = maxCheckoutDate;

                    checkOutInput.min = minCheckout.toISOString().split('T')[0];
                    checkOutInput.max = actualMaxCheckout.toISOString().split('T')[0];

                    if (!checkOutInput.value ||
                        new Date(checkOutInput.value) <= new Date(checkInInput.value) ||
                        new Date(checkOutInput.value) > actualMaxCheckout) {
                        checkOutInput.value = minCheckout.toISOString().split('T')[0];
                    }
                }
                updatePriceSummary();
            }

            function handleCheckOutChange() {
                updatePriceSummary();
            }

            // --- Initialization ---
            function initializeForm() {
                // Get current date for default values (today)
                const today = new Date();
                const minCheckInDate = new Date(today);
                // Default check-in is today (no +14 days offset)

                // Use default dates
                const defaultCheckIn = minCheckInDate.toISOString().split('T')[0];
                const defaultCheckOut = new Date(minCheckInDate);

                if (typeof defaultCheckOut === 'string') {
                    // defaultCheckOut is already a string, use it directly
                } else {
                    defaultCheckOut.setDate(minCheckInDate.getDate() + 1);
                }
                const defaultCheckOutStr = typeof defaultCheckOut === 'string' ? defaultCheckOut : defaultCheckOut.toISOString().split('T')[0];
                const minCheckInStr = minCheckInDate.toISOString().split('T')[0];

                // Set initial values
                const checkInInput = document.getElementById('check_in');
                const checkInMonthlyInput = document.getElementById('check_in_monthly');
                const checkOutInput = document.getElementById('check_out');
                const rentTypeSelect = document.getElementById('rent_type');
                const monthsSelect = document.getElementById('months');

                if (checkInInput) {
                    checkInInput.value = defaultCheckIn;
                    checkInInput.min = minCheckInStr;

                    // Check-in change handler — only triggers availability check.
                    // Check-out constraints are managed by Datepicker changeDate handler.
                    checkInInput.addEventListener('change', function() {
                        checkRoomAvailability();
                    });
                }

                // Handle monthly check-in input
                if (checkInMonthlyInput) {
                    checkInMonthlyInput.value = defaultCheckIn;
                    checkInMonthlyInput.min = minCheckInStr;

                    checkInMonthlyInput.addEventListener('change', function() {
                        // Sync with main check_in input
                        if (checkInInput) {
                            checkInInput.value = checkInMonthlyInput.value;
                        }

                        // Update checkout based on months
                        if (checkOutInput && monthsSelect) {
                            const checkInDate = new Date(checkInMonthlyInput.value);
                            const months = parseInt(monthsSelect.value, 10) || 1;
                            const checkOutDate = addMonthsClamped(checkInDate, months);
                            checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                        }

                        checkRoomAvailability();
                    });
                }

                if (checkOutInput) {
                    checkOutInput.value = defaultCheckOutStr;
                    checkOutInput.min = defaultCheckOutStr;

                    // Handle check-out date changes
                    checkOutInput.addEventListener('change', function() {
                        checkRoomAvailability();
                    });
                }

                // Initialize other form elements
                if (rentTypeSelect) {
                    rentTypeSelect.addEventListener('change', updateRentalType);
                }

                if (monthsSelect) {
                    monthsSelect.addEventListener('change', function() {
                        // Update checkout date when months change
                        const rentType = document.getElementById('rent_type').value;
                        if (rentType === 'monthly') {
                            const checkInMonthlyInput = document.getElementById('check_in_monthly');
                            const checkOutInput = document.getElementById('check_out');

                            if (checkInMonthlyInput && checkOutInput && checkInMonthlyInput.value) {
                                const checkInDate = new Date(checkInMonthlyInput.value);
                                const months = parseInt(monthsSelect.value, 10) || 1;
                                const checkOutDate = addMonthsClamped(checkInDate, months);
                                checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                            }
                        }
                        updatePriceSummary();
                        checkRoomAvailability();
                    });
                }
                
                // Initialize form state
                updateRentalType();
                updatePriceSummary();
                
                // Add form submission handler
                if (bookingForm) {
                    bookingForm.addEventListener('submit', handleFormSubmit);
                }
            }

            // --- Form validation ---
            function validateForm() {
                let isValid = true;
                const rentType = document.getElementById('rent_type').value;

                // Reset all error states
                document.querySelectorAll('.error-message').forEach(el => el.classList.add('hidden'));
                document.querySelectorAll('input, select').forEach(input => {
                    input.classList.remove('border-red-500');
                });

                // Only validate date fields for daily rental
                if (rentType === 'daily') {
                    const checkInInput = document.getElementById('check_in');
                    const checkOutInput = document.getElementById('check_out');

                    if (!checkInInput.value) {
                        document.getElementById('check_inError').textContent = 'Check-in date is required';
                        document.getElementById('check_inError').classList.remove('hidden');
                        checkInInput.classList.add('border-red-500');
                        isValid = false;
                    }

                    if (!checkOutInput.value) {
                        document.getElementById('check_outError').textContent = 'Check-out date is required';
                        document.getElementById('check_outError').classList.remove('hidden');
                        checkOutInput.classList.add('border-red-500');
                        isValid = false;
                    } else if (checkInInput.value && new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
                        document.getElementById('check_outError').textContent = 'Check-out date must be after check-in date';
                        document.getElementById('check_outError').classList.remove('hidden');
                        checkOutInput.classList.add('border-red-500');
                        isValid = false;
                    }

                    // Validate max 60-day booking period (cross-month is allowed)
                    if (checkInInput.value && checkOutInput.value) {
                        const checkIn = new Date(checkInInput.value);
                        const checkOut = new Date(checkOutInput.value);
                        const daysDiff = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
                        if (daysDiff > 60) {
                            document.getElementById('check_outError').textContent = 'Booking period cannot exceed 60 days';
                            document.getElementById('check_outError').classList.remove('hidden');
                            checkOutInput.classList.add('border-red-500');
                            isValid = false;
                        }
                    }
                }

                // Validate rental agreement checkbox
                const agreementCheckbox = document.getElementById('agreementCheckbox');
                const agreementError = document.getElementById('agreementError');
                const rentalAgreementSection = document.getElementById('rentalAgreementSection');

                if (rentalAgreementSection && !rentalAgreementSection.classList.contains('hidden')) {
                    if (agreementCheckbox && !agreementCheckbox.checked) {
                        if (agreementError) {
                            agreementError.classList.remove('hidden');
                        }
                        isValid = false;
                    } else if (agreementError) {
                        agreementError.classList.add('hidden');
                    }
                }

                return isValid;
            }

            // --- Booking form submission ---
            async function handleFormSubmit(e) {
                e.preventDefault();
                
                if (!validateForm()) {
                    return;
                }

                errorAlert.classList.add('hidden');
                errorAlert.textContent = '';
                loadingOverlay.classList.remove('hidden');
                if (submitButton) submitButton.disabled = true;
                
                try {
                    const formData = new FormData(bookingForm);
                    // Ensure booking_months is set to the same value as months for monthly bookings
                    const rentType = document.getElementById('rent_type').value;
                    if (rentType === 'monthly') {
                        const months = document.getElementById('months').value;
                        formData.set('booking_months', months);
                    }
                    const formObject = Object.fromEntries(formData);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    const response = await fetch('{{ route("bookings.store") }}', {
                        referrerPolicy: 'unsafe-url',
                        method: 'POST',
                        headers: {
                            'Referrer-Policy': 'unsafe-url',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(formObject)
                    });

                    // First, check if the response is JSON
                    const contentType = response.headers.get('content-type');
                    let data;
                    
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        // If not JSON, get the response as text to see what it is
                        const text = await response.text();
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned an invalid response. Please try again.');
                    }
                    
                    if (response.ok && data.payment_options) {
                        // Directly redirect to payment page (modal disabled)
                        // Prioritize DOKU payment if available, otherwise use other payment option
                        if (data.payment_options.doku) {
                            window.location.href = data.payment_options.doku;
                        } else if (data.payment_options.other) {
                            window.location.href = data.payment_options.other;
                        }
                        return;

                        /* MODAL CODE DISABLED - Keep for future reference
                        // Create modal overlay
                        const modalOverlay = document.createElement('div');
                        modalOverlay.style.cssText = 'position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;';

                        // Create modal content
                        const modalContent = document.createElement('div');
                        modalContent.style.cssText = 'background: white; border-radius: 8px; padding: 24px; width: 90%; max-width: 400px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);';

                        // Add title
                        const title = document.createElement('h3');
                        title.textContent = 'Pilih Metode Pembayaran';
                        title.style.cssText = 'font-size: 1.25rem; font-weight: 600; margin-bottom: 1.5rem; color: #1a202c;';
                        modalContent.appendChild(title);

                        // Create payment options container
                        const optionsContainer = document.createElement('div');
                        optionsContainer.className = 'space-y-3';

                        // Add DOKU payment option if available
                        if (data.payment_options.doku) {
                            const dokuOption = document.createElement('button');
                            dokuOption.innerHTML = `
                                <div class="flex items-center justify-between p-4 border rounded-lg hover:border-teal-500 transition-colors">
                                    <div class="flex items-center">
                                        <img src="https://cdn.prod.website-files.com/674ab654de930b2731566389c1/6756a9f7ba44cad5543f87e2_doku-logo-red.svg" alt="DOKU" class="h-6 mr-3">
                                        <span>Bayar dengan DOKU</span>
                                    </div>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Direkomendasikan</span>
                                </div>
                            `;
                            dokuOption.className = 'w-full text-left';
                            dokuOption.addEventListener('click', () => {
                                window.location.href = data.payment_options.doku;
                            });
                            optionsContainer.appendChild(dokuOption);
                        }

                        // Add other payment option if available
                        if (data.payment_options.other) {
                            const otherOption = document.createElement('button');
                            otherOption.innerHTML = `
                                <div class="flex items-center p-4 border rounded-lg hover:border-teal-500 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="h-6 w-6 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <span>TRANSFER VA</span>
                                    </div>
                                </div>
                            `;
                            otherOption.className = 'w-full text-left';
                            otherOption.addEventListener('click', () => {
                                window.location.href = data.payment_options.other;
                            });
                            optionsContainer.appendChild(otherOption);
                        }

                        // Close button
                        const closeButton = document.createElement('button');
                        closeButton.textContent = 'Batal';
                        closeButton.className = 'mt-4 w-full py-2 px-4 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500';
                        closeButton.addEventListener('click', () => {
                            document.body.removeChild(modalOverlay);
                        });

                        // Assemble modal
                        modalContent.appendChild(optionsContainer);
                        modalContent.appendChild(closeButton);
                        modalOverlay.appendChild(modalContent);
                        document.body.appendChild(modalOverlay);

                        // Close modal when clicking outside
                        modalOverlay.addEventListener('click', (e) => {
                            if (e.target === modalOverlay) {
                                document.body.removeChild(modalOverlay);
                            }
                        });

                        return;
                        */
                    }

                    if (data.errors) {
                        // Handle validation errors
                        let errorMessages = [];
                        Object.entries(data.errors).forEach(([field, messages]) => {
                            const errorElement = document.getElementById(`${field}Error`);
                            const message = Array.isArray(messages) ? messages[0] : messages;
                            if (errorElement) {
                                errorElement.textContent = message;
                                errorElement.classList.remove('hidden');
                            }
                            errorMessages.push(message);
                        });
                        
                        // Display first error message in the main alert
                        if (errorMessages.length > 0) {
                            errorAlert.textContent = errorMessages[0];
                        } else {
                            errorAlert.textContent = 'Please fix the errors below';
                        }
                        errorAlert.classList.remove('hidden');
                    } else if (data.message) {
                        // Handle other error messages
                        errorAlert.textContent = data.message;
                        errorAlert.classList.remove('hidden');
                    } else {
                        errorAlert.textContent = 'An unexpected error occurred. Please try again.';
                        errorAlert.classList.remove('hidden');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    errorAlert.textContent = error.message || 'An error occurred while processing your booking. Please try again.';
                    errorAlert.classList.remove('hidden');
                } finally {
                    loadingOverlay.classList.add('hidden');
                    if (submitButton) submitButton.disabled = false;
                }
            }

            // Set default dates (check-in defaults to today)
            const today = new Date();
            const minCheckInDate = new Date(today);
            // Default check-in is today (no offset)
            const minCheckOutDate = new Date(minCheckInDate);
            minCheckOutDate.setDate(minCheckInDate.getDate() + 1);

            // Format date as YYYY-MM-DD
            const formatDate = (date) => date.toISOString().split('T')[0];

            // Set initial values
            if (checkInInput) {
                checkInInput.value = formatDate(minCheckInDate);
                checkInInput.min = formatDate(minCheckInDate);

                // Check-in change handler — only triggers price update.
                // Check-out constraints are managed by Datepicker changeDate handler.
                checkInInput.addEventListener('change', function() {
                    updatePriceSummary();
                });
            }

            if (checkOutInput) {
                checkOutInput.value = formatDate(minCheckOutDate);
                checkOutInput.min = formatDate(minCheckOutDate);
                
                // Handle check-out date changes
                checkOutInput.addEventListener('change', updatePriceSummary);
            }
            
            // Initialize other form elements
            if (rentTypeSelect) {
                rentTypeSelect.addEventListener('change', updateRentalType);
            }
            
            if (monthsSelect) {
                monthsSelect.addEventListener('change', function() {
                    const rentType = document.getElementById('rent_type').value;
                    if (rentType === 'monthly') {
                        const checkInMonthlyInput = document.getElementById('check_in_monthly');
                        const checkOutInput = document.getElementById('check_out');
                        if (checkInMonthlyInput && checkOutInput && checkInMonthlyInput.value) {
                            const checkInDate = new Date(checkInMonthlyInput.value);
                            const months = parseInt(monthsSelect.value, 10) || 1;
                            const checkOutDate = addMonthsClamped(checkInDate, months);
                            checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                        }
                    }
                    updatePriceSummary();
                });
            }
            
            // Initialize form state
            updateRentalType();
            updatePriceSummary();
            
            // Add form submission handler
            if (bookingForm) {
                bookingForm.addEventListener('submit', handleFormSubmit);
            }
            // Console log for debugging facility data
            // let roomFacility = <?php echo json_encode($room['facility'] ?? []); ?>;
            // console.log('Room facility data:', roomFacility);
            // console.log('Number of facilities:', <?php echo count($room['facility'] ?? []); ?>);
            // let roomFullData = <?php echo json_encode($room ?? (object)[]); ?>;
            // console.log('Room full data:', roomFullData);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/vanillajs-datepicker@1.3.4/dist/js/datepicker-full.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date();
            const maxDate = new Date();
            maxDate.setDate(today.getDate() + 365); // Allow booking up to 1 year ahead

            // Max check-in dates differ by booking type:
            // Daily booking — up to 90 days from today. Monthly — up to 14 days ahead.
            const maxCheckInDaily = new Date();
            maxCheckInDaily.setDate(today.getDate() + 90);
            const maxCheckInMonthly = new Date();
            maxCheckInMonthly.setDate(today.getDate() + 14);

            // Set default dates (check-in defaults to today)
            const minCheckInDate = new Date(today);
            // Default check-in is today (no offset)
            const minCheckOutDate = new Date(minCheckInDate);
            minCheckOutDate.setDate(minCheckInDate.getDate() + 1);

            // Initialize datepicker for daily check-in
            const checkInElem = document.getElementById('check_in');
            let checkInPicker = null;
            if (checkInElem) {
                checkInPicker = new Datepicker(checkInElem, {
                    format: 'yyyy-mm-dd',
                    minDate: today,
                    maxDate: maxCheckInDaily,
                    autohide: true,
                    todayHighlight: true,
                    weekStart: 0
                });

                // Set default date
                checkInPicker.setDate(minCheckInDate);

                // Update check-out picker when check-in changes.
                // Destroys and recreates the check-out datepicker because
                // vanillajs-datepicker's setOptions() does not reliably update
                // min/max constraints, leaving the picker unresponsive.
                checkInElem.addEventListener('changeDate', function(e) {
                    if (e.detail.date) {
                        const selectedCheckIn = new Date(e.detail.date);
                        const minCheckOut = new Date(selectedCheckIn);
                        minCheckOut.setDate(selectedCheckIn.getDate() + 1);

                        // Calculate max checkout: 14 days from check-in OR end of month
                        const maxCheckOutDate = new Date(selectedCheckIn);
                        maxCheckOutDate.setDate(selectedCheckIn.getDate() + 60);

                        const actualMaxCheckOut = maxCheckOutDate;

                        // Destroy and recreate check-out datepicker with new constraints.
                        // setOptions() breaks the picker in vanillajs-datepicker v1.3.4.
                        // Pass Date objects directly (not strings) to avoid timezone issues
                        // where toISOString() shifts dates back a day in Asian timezones.
                        if (checkOutPicker) {
                            checkOutPicker.destroy();
                        }
                        const coElem = document.getElementById('check_out');
                        if (coElem) {
                            checkOutPicker = new Datepicker(coElem, {
                                format: 'yyyy-mm-dd',
                                minDate: minCheckOut,
                                maxDate: actualMaxCheckOut,
                                autohide: true,
                                todayHighlight: true,
                                weekStart: 0
                            });
                            checkOutPicker.setDate(minCheckOut);
                        }

                        // Update price summary and availability directly
                        // (don't dispatch 'change' event — that would conflict with Datepicker)
                        updatePriceSummary();
                        checkRoomAvailability();
                    }
                });
            }

            // Initialize datepicker for daily check-out
            const checkOutElem = document.getElementById('check_out');
            let checkOutPicker = null;
            if (checkOutElem) {
                checkOutPicker = new Datepicker(checkOutElem, {
                    format: 'yyyy-mm-dd',
                    minDate: minCheckOutDate,
                    maxDate: maxDate,
                    autohide: true,
                    todayHighlight: true,
                    weekStart: 0
                });

                // Set default date
                checkOutPicker.setDate(minCheckOutDate);

                // Trigger change event when date changes
                checkOutElem.addEventListener('changeDate', function(e) {
                    if (e.detail.date) {
                        checkOutElem.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }

            // Initialize datepicker for monthly check-in
            const checkInMonthlyElem = document.getElementById('check_in_monthly');
            let checkInMonthlyPicker = null;
            if (checkInMonthlyElem) {
                checkInMonthlyPicker = new Datepicker(checkInMonthlyElem, {
                    format: 'yyyy-mm-dd',
                    minDate: today,
                    maxDate: maxCheckInMonthly,
                    autohide: true,
                    todayHighlight: true,
                    weekStart: 0
                });

                // Set default date
                checkInMonthlyPicker.setDate(minCheckInDate);

                // Trigger change event when date changes
                checkInMonthlyElem.addEventListener('changeDate', function(e) {
                    if (e.detail.date) {
                        // Sync with main check_in input
                        if (checkInElem && checkInPicker) {
                            checkInPicker.setDate(e.detail.date);
                        }

                        // Trigger change event for form validation
                        checkInMonthlyElem.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                });
            }
        });
    </script>
</body>
</html>