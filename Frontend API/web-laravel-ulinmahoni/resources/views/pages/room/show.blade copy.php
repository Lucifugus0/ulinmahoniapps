<!DOCTYPE html>
<html lang="en">
<head>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Room Details - {{ $room['name'] }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles -->
    @include('components.property.styles')
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .fa-spin {
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li><a href="{{ url()->previous() }}" class="hover:text-gray-700">Property</a></li>
                    <li><span class="mx-2">/</span></li>
                    
                    <li class="text-gray-900">{{ $room['name'] }}</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Room Details (left) -->
                <div class="lg:col-span-7">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Room Image Gallery -->
                        <div class="relative h-96" x-data="{ showModal: false, modalImg: '', modalAlt: '' }">
                            <!-- Modal Popup -->
                            <div x-show="showModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70" style="display: none;" @click.away="showModal=false" @click.self="showModal=false" @keydown.escape.window="showModal=false">
                                <img :src="modalImg" :alt="modalAlt" class="max-h-[80vh] max-w-[90vw] rounded shadow-lg border-4 border-white object-contain" @click.stop>
                                <button @click="showModal=false" class="absolute top-4 right-6 text-white text-3xl font-bold focus:outline-none">&times;</button>
                            </div>
                            @php
                                $roomImages = $room['images'] ?? [];
                                $mainImage = $room['image'] ?? null;
                                $totalImages = count($roomImages) > 0 ? count($roomImages) : ($mainImage ? 1 : 0);
                            @endphp
                            
                            <!-- Main Image -->
                            <div class="absolute inset-0">
                                @if($mainImage)
                                    {{-- <img src="data:image/jpeg;base64,{{ $mainImage }}" 
                                        alt="{{ $room['name'] }}" 
                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                                        @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $mainImage }}'; modalAlt='{{ $room['name'] }}'"> --}}
                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}" 
                                        alt="{{ $room['name'] }}" 
                                        class="w-full h-full object-cover transition-transform duration-300 hover:scale-105 cursor-pointer"
                                        @click.prevent="showModal=true; modalImg='{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}'; modalAlt='{{ $room['name'] }}'">
                                @else
                                    <div class="bg-gray-100 w-full h-full flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                        <span class="ml-2 font-medium text-gray-500">No Image</span>
                                    </div>
                                @endif
                                
                                <!-- Image Counter -->
                                @if($totalImages > 1)
                                    <div class="absolute bottom-4 right-4">
                                        <span class="bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm">
                                            <i class="fas fa-camera mr-1"></i> {{ $totalImages }}
                                        </span>
                                    </div>
                                @endif
                                
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/50 to-transparent h-16">
                                    <span class="absolute top-4 left-4 bg-teal-600 text-white px-3 py-1 rounded-full text-sm">
                                        {{ ucfirst($room['type']) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Thumbnails (if multiple images) -->
                            @if(count($roomImages) > 1)
                                <div class="absolute bottom-4 left-4 right-4 flex space-x-2 overflow-x-auto pb-2">
                                    @foreach($roomImages as $index => $image)
                                        <div class="flex-shrink-0 w-16 h-16 rounded overflow-hidden border-2 border-white shadow-md">
                                            {{-- <img src="data:image/jpeg;base64,{{ $image['image'] }}" 
                                                alt="{{ $room['name'] }} - Image {{ $index + 1 }}" 
                                                class="w-full h-full object-cover cursor-pointer hover:opacity-80"
                                                @click.prevent="showModal=true; modalImg='data:image/jpeg;base64,{{ $image['image'] }}'; modalAlt='{{ $room['name'] }} - Image {{ $index + 1 }}'"> --}}
                                            <img src="{{ env('ADMIN_URL') }}/storage/{{ $image['image'] }}" 
                                                alt="{{ $room['name'] }} - Image {{ $index + 1 }}" 
                                                class="w-full h-full object-cover cursor-pointer hover:opacity-80"
                                                @click.prevent="showModal=true; modalImg='{{ env('ADMIN_URL') }}/storage/{{ $image['image'] }}'; modalAlt='{{ $room['name'] }} - Image {{ $index + 1 }}'">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="p-8">
                            <!-- Room Title and Price -->
                            <div class="flex justify-between items-start mb-8">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $room['no'] }} - {{ $room['name'] }}</h1>
                                    <p class="text-gray-500 uppercase tracking-wide">{{ $room['type'] }}</p>
                                </div>
                                <div class="space-y-4 text-right">
                                @if(!empty($room['price_original_daily']) && $room['price_original_daily'] > 0)
                                <div class="mt-4">
                                    <p class="text-3xl font-bold text-teal-600">Rp {{ number_format($room['price_original_daily'], 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">per malam</p>
                                </div>
                                @else
                                <div class="mt-4">
                                    <p class="text-3xl font-bold text-teal-600">Hubungi Kami</p>
                                    <p class="text-sm text-gray-500">untuk harga per malam</p>
                                </div>
                                @endif
                                @if(!empty($room['price_original_monthly']) && $room['price_original_monthly'] > 0)
                                <div class="mt-4">
                                    <p class="text-3xl font-bold text-teal-600">Rp {{ number_format($room['price_original_monthly'], 0, ',', '.') }}</p>
                                    <p class="text-sm text-gray-500">per bulan</p>
                                </div>
                                @else
                                <div class="mt-4">
                                    <p class="text-3xl font-bold text-teal-600">Hubungi Kami</p>
                                    <p class="text-sm text-gray-500">untuk harga per bulan</p>
                                </div>
                                @endif
                            </div>
                            </div>

                            <!-- Room Facilities -->
                            @if(!empty($room['facility']) && is_array($room['facility']) && count($room['facility']) > 0)
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Fasilitas Kamar</h2>
                                <div class="grid grid-cols-2 gap-6">
                                    @foreach($room['facility'] as $facility)
                                        <div class="flex items-center space-x-3 py-2">
                                            <i class="fas fa-check text-teal-600"></i>
                                            <span class="text-gray-600">{{ strtoupper($facility) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <div class="mb-8">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Fasilitas Kamar</h2>
                                <p class="text-gray-500">Tidak ada fasilitas yang tercantum</p>
                            </div>
                            @endif

                            <!-- Room Description -->
                            <div class="space-y-6">
                                <div class="prose prose-lg max-w-none">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi Kamar</h3>
                                    <p class="text-gray-600 leading-relaxed">{{ $room['descriptions'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Form (right) -->
                <div class="lg:col-span-5 lg:pl-4">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-8 sticky top-8">
                        <!-- Status and Price Summary -->
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">Pesan Kamar</h2>
                                <p class="text-gray-500">Isi detail dibawah ini</p>
                            </div>
                            <div class="text-right">
                                <!-- Room Status -->
                                <span id="roomStatus" class="px-4 py-2 rounded-full text-sm font-medium
                                    {{ $room['status'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $room['status'] == 1 ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                                
                                <!-- Availability Status -->
                                
                            </div>
                        </div>
                        
                        <form id="bookingForm" class="space-y-6" method="POST" action="{{ route('bookings.store') }}" novalidate>
                            @csrf
                            <input type="hidden" name="property_name" value="{{ $room['property_name'] ?? ($property['name'] ?? $room['name']) }}">
                            <input type="hidden" name="room_name" value="{{ $room['name'] }}">
                            <input type="hidden" name="room_id" id="roomId" value="{{ $room['id'] }}">
                            <input type="hidden" name="property_id" id="propertyId" value="{{ $property['id'] ?? $room['property_id'] ?? '' }}">
                            <input type="hidden" name="price_daily" id="priceDaily" value="{{ $room['price_original_daily'] }}">
                            <input type="hidden" name="price_monthly" id="priceMonthly" value="{{ $room['price_original_monthly'] }}">
                            <input type="hidden" name="service_fees" id="serviceFees" value="{{ $room['service_fees'] }}">
                            {{-- <input type="hidden" name="tax_fees" id="taxFees" value="{{ $room['tax_fees'] ?? 0 }}"> --}}
                            <!-- Rental Type -->
                            <div class="mb-6">
                                <label for="rent_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Pemesanan</label>
                                <select id="rent_type" name="rent_type" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
                                    onchange="updateRentalType()">
                                    @if($room['periode_daily'] == 1)
                                        <option value="daily" {{ $room['periode_monthly'] == 1 ? '' : 'selected' }}>Harian</option>
                                    @endif
                                    @if($room['periode_monthly'] == 1)
                                        <option value="monthly" {{ $room['periode_daily'] == 1 ? '' : 'selected' }}>Bulanan</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Months Selection (Hidden by default) -->
                            <div id="monthInput" class="hidden">
                                <label for="months" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bulan</label>
                                <select id="months" name="months" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
                                    onchange="updatePriceSummary()">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Bulan' : 'Bulan' }}</option>
                                    @endfor
                                </select>
                                <input type="hidden" name="booking_months" id="bookingMonths" value="1">
                            </div>

                            <!-- Dates (Visible by default) -->
                            <div id="dateInputs">
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Check-in Date -->
                                    <div>
                                        <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">Check-in</label>
                                        <input type="date" id="check_in" name="check_in" 
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
                                            min="{{ date('Y-m-d') }}" data-required="true">
                                        <div id="check_inError" class="text-red-500 text-xs mt-1 hidden error-message"></div>
                                    </div>

                                    <!-- Check-out Date -->
                                    <div id="checkOutGroup">
                                        <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">Check-out</label>
                                        <input type="date" id="check_out" name="check_out" 
                                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-teal-500 focus:ring-1 focus:ring-teal-500"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" data-required="true">
                                        <div id="check_outError" class="text-red-500 text-xs mt-1 hidden error-message"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Availability Check -->
                            <div class="mt-3">
                                <div id="availabilityStatus" class="text-sm p-3 rounded-lg border border-gray-200 bg-gray-50 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                            <span class="text-gray-600 font-medium">Status Ketersediaan:</span>
                                        </div>
                                        <span class="text-xs text-gray-500">Menunggu pemilihan tanggal...</span>
                                    </div>
                                </div>
                            </div>

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
                                <h4 class="font-medium text-gray-900 mb-3">Harga Keseluruhan</h4>
                                <div class="space-y-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600" id="rateTypeDisplay">Daily Rate: </span>
                                        <div class="text-right">
                                            <div class="text-gray-900" id="rateDisplay">
                                                <!-- Daily Rate Display -->
                                                <div id="dailyRateDisplay" class="hidden">
                                                    <span class="text-black-600">Rp {{ number_format($room['price_original_daily'], 0, ',', '.') }}</span>
                                                    <div class="text-xs text-gray-500 mt-1">per malam</div>
                                                </div>
                                                <!-- Monthly Rate Display -->
                                                <div id="monthlyRateDisplay" class="hidden">
                                                    <span class="text-black-600">Rp {{ number_format($room['price_original_monthly'], 0, ',', '.') }}</span>
                                                    <div class="text-xs text-gray-500 mt-1">per bulan</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Durasi: </span>
                                        <span class="text-gray-900" id="durationDisplay">-</span>
                                    </div>
                                    
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Harga Kamar:</span>
                                        <span class="text-gray-900" id="roomTotal">-</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Biaya Layanan: </span>
                                        <span class="text-gray-900" id="serviceFeesDisplay">-</span>
                                    </div>
                                    {{-- <div class="flex justify-between">
                                        <span class="text-gray-600">Tax and Fees (20%): </span>
                                        <span class="text-gray-900" id="taxDisplay">-</span>
                                    </div> --}}
                                    
                                    {{-- <div class="flex justify-between">
                                        <span class="text-gray-600">Admin Fee:</span>
                                        <span class="text-gray-900" id="adminFee">{{ number_format($room['admin_fees'], 0, ',', '.') }}</span>
                                    </div> --}}
                                    
                                    <div class="flex justify-between font-medium text-lg pt-3 border-t mt-3">
                                        <span>Total:</span>
                                        <span class="text-teal-600" id="grandTotal">-</span>
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
                                            <label for="agreementCheckbox" class="text-gray-700">
                                                Saya setuju dengan <a href="/rental-agreement" target="_blank" class="text-teal-600 hover:text-teal-700 underline">Perjanjian Sewa Menyewa</a>
                                            </label>
                                        </div>
                                    </div>
                                    <div id="agreementError" class="hidden mt-2 text-sm text-red-600">
                                        <p><i class="fas fa-exclamation-circle mr-1"></i>Anda harus menyetujui Perjanjian Sewa Menyewa untuk melanjutkan.</p>
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
                                        Masuk untuk memesan
                                    </button>
                                    <p class="text-sm text-gray-500 text-center mt-2">Mohon login atau register untuk membuat pemesanan</p>
                                @else
                                    <button type="button" id="checkAvailabilityButton"
                                        onclick="checkRoomAvailability()"
                                        class="w-full {{ $room['status'] == 0 ? 'bg-gray-400' : 'bg-teal-600' }} text-white py-4 px-6 rounded-lg {{ $room['status'] == 0 ? '' : 'hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200' }} text-lg font-medium"
                                        {{ $room['status'] == 0 ? 'disabled' : '' }}>
                                        {{ $room['status'] == 1 ? 'Cek Ketersediaan Kamar' : 'Kamar Tidak Tersedia' }}
                                    </button>
                                    <button type="submit" id="submitButton"
                                        class="w-full bg-teal-600 text-white py-4 px-6 rounded-lg hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition-colors duration-200 text-lg font-medium hidden">
                                        Pesan Sekarang
                                    </button>
                                    <div id="unavailableMessage" class="hidden mt-2 text-sm text-red-600">
                                        <p><i class="fas fa-exclamation-triangle mr-1"></i>Ruang tidak tersedia untuk tanggal yang dipilih. Silakan pilih tanggal lain.</p>
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
        // --- Global variables ---
        let bookingForm, errorAlert, loadingOverlay, submitButton, monthInput, dateInputs, monthsSelect, rentTypeSelect;
        let checkInInput, checkOutInput, availabilityStatusDiv;
        let availabilityCheckTimeout;

        // --- Global Room Availability Function ---
        async function checkRoomAvailability() {
            console.log('=== CHECK ROOM AVAILABILITY STARTED ===');
            
            const propertyId = document.getElementById('propertyId').value;
            const roomId = document.querySelector('[name="room_id"]').value;
            const checkInDate = document.getElementById('check_in').value;
            const checkOutDate = document.getElementById('check_out').value;
            const rentType = document.getElementById('rent_type').value;
            
            console.log('Input values:', {
                propertyId: propertyId,
                roomId: roomId,
                checkInDate: checkInDate,
                checkOutDate: checkOutDate,
                rentType: rentType,
                timestamp: new Date().toISOString()
            });
            
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
            console.log('Validating date order');
            if (new Date(checkOutDate) <= new Date(checkInDate)) {
                console.log('Date validation failed - check-out before or same as check-in');
                showAvailabilityStatus('error', 'Tanggal check-out harus setelah check-in');
                updateSubmitButton(false, 'Tanggal tidak valid');
                return;
            }
            // console.log('Date validation passed');
            
            // Show loading state
            // console.log('Setting loading state');
            showAvailabilityStatus('loading', 'Memeriksa ketersediaan...');
            
            availabilityCheckTimeout = setTimeout(async () => {
                // console.log('Starting async availability check');
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    // console.log('CSRF Token retrieved:', csrfToken ? 'Present' : 'Missing');
                    
                    const apiUrl = '{{ route("api.booking.check-availability") }}';
                    
                    
                    const requestBody = JSON.stringify({
                        property_id: propertyId,
                        room_id: roomId,
                        check_in: checkInDate,
                        check_out: checkOutDate
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
                            showAvailabilityStatus('available', '✓ Kamar tersedia');
                            updateSubmitButton(true);
                        } else {
                            console.log('✗ Room is unavailable');
                            showAvailabilityStatus('unavailable', '✗ Kamar tidak tersedia untuk tanggal ini');
                            if (conflictingBookings.length > 0) {
                                // console.log('Found conflicting bookings:', conflictingBookings);
                                showAvailabilityStatus('unavailable', `✗ Tidak tersedia - ${conflictingBookings.length} pemesanan konflik`);
                            }
                            updateSubmitButton(false, 'Kamar tidak tersedia');
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
            
            console.log('=== CHECK ROOM AVAILABILITY SCHEDULED ===');
        }

        // --- Other Global Utility Functions ---
        function formatRupiah(num) {
            return `Rp ${num.toLocaleString('id-ID')}`;
        }
        
        function showAvailabilityStatus(type, message) {
            if (!availabilityStatusDiv) return;
            
            let className = 'text-sm p-3 rounded-lg border transition-all duration-300 ';
            let statusIcon = '';
            let statusMessage = '';
            
            switch (type) {
                case 'loading':
                    className += 'bg-blue-50 border-blue-200 text-blue-800';
                    statusIcon = '<i class="fas fa-spinner fa-spin mr-2"></i>';
                    statusMessage = 'Memeriksa ketersediaan...';
                    break;
                case 'available':
                    className += 'bg-green-50 border-green-200 text-green-800';
                    statusIcon = '<i class="fas fa-check-circle mr-2 text-green-500"></i>';
                    statusMessage = 'Tersedia';
                    break;
                case 'unavailable':
                    className += 'bg-red-50 border-red-200 text-red-700';
                    statusIcon = '<i class="fas fa-times-circle mr-2 text-red-500"></i>';
                    statusMessage = 'Tidak Tersedia';
                    break;
                case 'error':
                    className += 'bg-yellow-50 border-yellow-200 text-yellow-800';
                    statusIcon = '<i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>';
                    statusMessage = 'Validasi Gagal';
                    break;
                default:
                    className += 'bg-gray-50 border-gray-200 text-gray-600';
                    statusIcon = '<i class="fas fa-info-circle mr-2 text-blue-500"></i>';
                    statusMessage = 'Menunggu pemilihan tanggal...';
            }
            
            // Special handling for conflicting bookings
            if (type === 'unavailable' && message && message.includes('pemesanan konflik')) {
                const match = message.match(/(\d+)\s+pemesanan konflik/);
                if (match) {
                    const count = match[1];
                    statusMessage = `Kamar sudah dipesan`;
                }
            }
            
            availabilityStatusDiv.className = className;
            availabilityStatusDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        ${statusIcon}
                        <span class="font-medium">Status Ketersediaan:</span>
                    </div>
                    <span>${statusMessage}</span>
                </div>
                ${message && type !== 'loading' && type !== 'available' && type !== 'unavailable' ? `
                    <div class="mt-2 text-xs opacity-80">
                        <span>${message}</span>
                    </div>
                ` : ''}
            `;
        }
        
        function resetAvailabilityStatus() {
            if (!availabilityStatusDiv) return;
            availabilityStatusDiv.className = 'text-sm p-3 rounded-lg border border-gray-200 bg-gray-50 transition-all duration-300';
            availabilityStatusDiv.innerHTML = `
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                        <span class="text-gray-600 font-medium">Status Ketersediaan:</span>
                    </div>
                    <span class="text-gray-500 text-xs">Menunggu pemilihan tanggal...</span>
                </div>
            `;
        }

        function updateSubmitButton(isEnabled, customMessage = null) {
            const checkAvailabilityButton = document.getElementById('checkAvailabilityButton');
            const submitButton = document.getElementById('submitButton');
            const rentalAgreementSection = document.getElementById('rentalAgreementSection');

            if (!checkAvailabilityButton || !submitButton) return;

            if (isEnabled && {{ $room['status'] ?? 0 }} == 1) {
                // Room is available - show submit button, rental agreement checkbox, and hide check availability button
                checkAvailabilityButton.classList.add('hidden');
                submitButton.classList.remove('hidden');
                submitButton.disabled = false;

                // Show rental agreement checkbox
                if (rentalAgreementSection) {
                    rentalAgreementSection.classList.remove('hidden');
                }
            } else {
                // Room is not available - show check availability button and hide submit button and agreement
                checkAvailabilityButton.classList.remove('hidden');
                submitButton.classList.add('hidden');
                submitButton.disabled = true;

                // Hide rental agreement checkbox
                if (rentalAgreementSection) {
                    rentalAgreementSection.classList.add('hidden');
                }

                if (customMessage) {
                    checkAvailabilityButton.textContent = customMessage;
                } else if ({{ $room['status'] ?? 0 }} == 0) {
                    checkAvailabilityButton.textContent = 'Kamar Tidak Tersedia';
                } else {
                    checkAvailabilityButton.textContent = 'Cek Ketersediaan Kamar';
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
            availabilityStatusDiv = document.getElementById('availabilityStatus');
            
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

            // --- Room Availability Check ---
            // async function checkRoomAvailability() {
            //     const propertyId = document.getElementById('propertyId').value;
            //     const roomId = document.getElementById('roomId').value;
            //     const checkInDate = document.getElementById('check_in').value;
            //     const checkOutDate = document.getElementById('check_out').value;
            //     const rentType = document.getElementById('rent_type').value;
                
            //     // Clear previous timeout to avoid multiple rapid calls
            //     if (availabilityCheckTimeout) {
            //         clearTimeout(availabilityCheckTimeout);
            //     }
                
            //     // Only check availability for daily booking with both dates
            //     if (rentType !== 'daily' || !checkInDate || !checkOutDate) {
            //         resetAvailabilityStatus();
            //         return;
            //     }
                
            //     // Prevent checking if check-out is before check-in
            //     if (new Date(checkOutDate) <= new Date(checkInDate)) {
            //         showAvailabilityStatus('error', 'Tanggal check-out harus setelah check-in');
            //         updateSubmitButton(false, 'Tanggal tidak valid');
            //         return;
            //     }
                
            //     // Show loading state
            //     showAvailabilityStatus('loading', 'Memeriksa ketersediaan...');
                
            //     availabilityCheckTimeout = setTimeout(async () => {
            //         try {
            //             const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            //             const response = await fetch(`http://localhost:8000/api/booking/check-availability`, {
            //                 method: 'POST',
            //                 headers: {
            //                     'X-CSRF-TOKEN': csrfToken,
            //                     'Accept': 'application/json',
            //                     'Content-Type': 'application/json',
            //                     'X-Requested-With': 'XMLHttpRequest'
            //                 },
            //                 body: JSON.stringify({
            //                     property_id: propertyId,
            //                     room_id: roomId,
            //                     check_in: checkInDate,
            //                     check_out: checkOutDate
            //                 })
            //             });
                        
            //             const data = await response.json();
                        
            //             if (!response.ok || data.status === 'error') {
            //                 const errorMessage = data.message || 'Gagal memeriksa ketersediaan';
            //                 if (data.errors) {
            //                     const errorMessages = Object.values(data.errors).flat();
            //                     showAvailabilityStatus('error', errorMessages[0] || errorMessage);
            //                 } else {
            //                     showAvailabilityStatus('error', errorMessage);
            //                 }
            //                 updateSubmitButton(false, 'Validasi gagal');
            //                 return;
            //             }
                        
            //             // Handle the availability response
            //             if (data.data && data.data.is_available !== undefined) {
            //                 const isAvailable = data.data.is_available;
            //                 const conflictingBookings = data.data.conflicting_bookings || [];
                            
            //                 if (isAvailable) {
            //                     showAvailabilityStatus('available', '✓ Kamar tersedia');
            //                     updateSubmitButton(true);
            //                 } else {
            //                     showAvailabilityStatus('unavailable', '✗ Kamar tidak tersedia untuk tanggal ini');
            //                     if (conflictingBookings.length > 0) {
            //                         showAvailabilityStatus('unavailable', `✗ Tidak tersedia - ${conflictingBookings.length} pemesanan konflik`);
            //                     }
            //                     updateSubmitButton(false, 'Kamar tidak tersedia');
            //                 }
            //             } else {
            //                 showAvailabilityStatus('error', 'Respon API tidak valid');
            //                 updateSubmitButton(false, 'Kesalahan sistem');
            //             }
                        
            //         } catch (error) {
            //             console.error('Availability check error:', error);
            //             showAvailabilityStatus('error', 'Gagal memeriksa ketersediaan kamar');
            //             updateSubmitButton(false, 'Gagal mengecek');
            //         }
            //     }, 500); // Add small delay to prevent excessive API calls
            // }

            function showAvailabilityStatus(type, message) {
                if (!availabilityStatusDiv) return;
                
                let className = 'text-sm p-3 rounded-lg border transition-all duration-300 ';
                let statusIcon = '';
                let statusMessage = '';
                
                switch (type) {
                    case 'loading':
                        className += 'bg-blue-50 border-blue-200 text-blue-800';
                        statusIcon = '<i class="fas fa-spinner fa-spin mr-2"></i>';
                        statusMessage = 'Memeriksa ketersediaan...';
                        break;
                    case 'available':
                        className += 'bg-green-50 border-green-200 text-green-800';
                        statusIcon = '<i class="fas fa-check-circle mr-2 text-green-500"></i>';
                        statusMessage = 'Tersedia';
                        break;
                    case 'unavailable':
                        className += 'bg-red-50 border-red-200 text-red-700';
                        statusIcon = '<i class="fas fa-times-circle mr-2 text-red-500"></i>';
                        statusMessage = 'Tidak Tersedia';
                        break;
                    case 'error':
                        className += 'bg-yellow-50 border-yellow-200 text-yellow-800';
                        statusIcon = '<i class="fas fa-exclamation-triangle mr-2 text-yellow-500"></i>';
                        statusMessage = 'Validasi Gagal';
                        break;
                    default:
                        className += 'bg-gray-50 border-gray-200 text-gray-600';
                        statusIcon = '<i class="fas fa-info-circle mr-2 text-blue-500"></i>';
                        statusMessage = 'Menunggu pemilihan tanggal...';
                }
                
                // Special handling for conflicting bookings
                if (type === 'unavailable' && message && message.includes('pemesanan konflik')) {
                    const match = message.match(/(\d+)\s+pemesanan konflik/);
                    if (match) {
                        const count = match[1];
                        statusMessage = `Konflik ${count} pemesanan`;
                    }
                }
                
                availabilityStatusDiv.className = className;
                availabilityStatusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            ${statusIcon}
                            <span class="font-medium">Status Ketersediaan:</span>
                        </div>
                        <span>${statusMessage}</span>
                    </div>
                    ${message && type !== 'loading' && type !== 'available' && type !== 'unavailable' ? `
                        <div class="mt-2 text-xs opacity-80">
                            <span>${message}</span>
                        </div>
                    ` : ''}
                `;
            }

            function resetAvailabilityStatus() {
                if (!availabilityStatusDiv) return;
                availabilityStatusDiv.className = 'text-sm p-3 rounded-lg border border-gray-200 bg-gray-50 transition-all duration-300';
                availabilityStatusDiv.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-gray-600 font-medium">Status Ketersediaan:</span>
                        </div>
                        <span class="text-gray-500 text-xs">Menunggu pemilihan tanggal...</span>
                    </div>
                `;
            }

            function updateSubmitButton(isEnabled, customMessage = null) {
                const checkAvailabilityButton = document.getElementById('checkAvailabilityButton');
                const submitButton = document.getElementById('submitButton');
                
                if (!checkAvailabilityButton || !submitButton) return;
                
                if (isEnabled && {{ $room['status'] ?? 0 }} == 1) {
                    // Room is available - show submit button and hide check availability button
                    checkAvailabilityButton.classList.add('hidden');
                    submitButton.classList.remove('hidden');
                    submitButton.disabled = false;
                } else {
                    // Room is not available - show check availability button and hide submit button
                    checkAvailabilityButton.classList.remove('hidden');
                    submitButton.classList.add('hidden');
                    submitButton.disabled = true;
                    
                    if (customMessage) {
                        checkAvailabilityButton.textContent = customMessage;
                    } else if ({{ $room['status'] ?? 0 }} == 0) {
                        checkAvailabilityButton.textContent = 'Kamar Tidak Tersedia';
                    } else {
                        checkAvailabilityButton.textContent = 'Cek Ketersediaan Kamar';
                    }
                }
            }

            // --- Price summary logic ---
            function updatePriceSummary() {
                // Check availability automatically
                checkRoomAvailability();
                // Get all required elements
                const rentTypeSelect = document.getElementById('rent_type');
                const checkInInput = document.getElementById('check_in');
                const checkOutInput = document.getElementById('check_out');
                const monthsSelect = document.getElementById('months');
                const priceDailyInput = document.getElementById('priceDaily');
                const priceMonthlyInput = document.getElementById('priceMonthly');
                
                // Check if all required elements exist
                if (!rentTypeSelect || !checkInInput || !checkOutInput || !monthsSelect || !priceDailyInput || !priceMonthlyInput) {
                    console.error('Required form elements not found');
                    return;
                }
                
                const rentType = rentTypeSelect.value;
                const rateTypeDisplay = document.getElementById('rateTypeDisplay');
                const dailyRateDisplay = document.getElementById('dailyRateDisplay');
                const monthlyRateDisplay = document.getElementById('monthlyRateDisplay');
                const durationDisplay = document.getElementById('durationDisplay');
                
                // Toggle rate display in summary
                if (rateTypeDisplay) rateTypeDisplay.textContent = rentType === 'monthly' ? 'Harga Bulanan' : 'Harga Harian';
                if (dailyRateDisplay) dailyRateDisplay.classList.toggle('hidden', rentType !== 'daily');
                if (monthlyRateDisplay) monthlyRateDisplay.classList.toggle('hidden', rentType !== 'monthly');

                let duration = 0, rate = 0, roomTotal = 0;
                
                try {
                    if (rentType === 'daily') {
                        if (!checkInInput.value || !checkOutInput.value) return resetSummary();
                        const nights = getDaysBetweenDates(checkInInput.value, checkOutInput.value);
                        if (nights <= 0) return resetSummary();
                        duration = nights;
                        rate = parseFloat(priceDailyInput.value) || 0;
                        roomTotal = duration * rate;
                        if (durationDisplay) durationDisplay.textContent = `${duration} malam`;
                    } else {
                        duration = parseInt(monthsSelect.value || '1', 10);
                        rate = parseFloat(priceMonthlyInput.value) || 0;
                        roomTotal = duration * rate;
                        if (durationDisplay) durationDisplay.textContent = `${duration} bulan`;
                    }
                } catch (error) {
                    console.error('Error updating price summary:', error);
                    return resetSummary();
                }
                // Get admin fee value from the hidden input or use the default
                const adminFee = 0;
                const serviceFees= 30000;
                const taxFees = 0;
                const grandTotal = roomTotal + serviceFees +adminFee + taxFees;
                document.getElementById('roomTotal').textContent = formatRupiah(roomTotal);
                document.getElementById('serviceFeesDisplay').textContent = formatRupiah(serviceFees);
                // document.getElementById('taxDisplay').textContent = formatRupiah(taxFees);
                // document.getElementById('adminFee').textContent = formatRupiah(adminFee);
                document.getElementById('grandTotal').textContent = formatRupiah(grandTotal);
            }

            // --- Rental type toggle logic ---
            function updateRentalType() {
                const rentTypeSelect = document.getElementById('rent_type');
                const monthInput = document.getElementById('monthInput');
                const dateInputs = document.getElementById('dateInputs');
                const checkInInput = document.getElementById('check_in');
                const checkOutInput = document.getElementById('check_out');
                const monthsSelect = document.getElementById('months');
                const dailyRateDisplay = document.getElementById('dailyRateDisplay');
                const monthlyRateDisplay = document.getElementById('monthlyRateDisplay');
                const rateTypeDisplay = document.getElementById('rateTypeDisplay');
                const savedSearch = localStorage.getItem('propertySearch');
                const searchState = savedSearch ? JSON.parse(savedSearch) : null;

                if (rentTypeSelect.value === 'monthly') {
                    monthInput.classList.remove('hidden');
                    // Show only check-in date for monthly rentals
                    dateInputs.classList.remove('hidden');
                    const checkOutGroup = document.getElementById('checkOutGroup');
                    if (checkOutGroup) {
                        checkOutGroup.classList.add('hidden');
                        // Set months from saved search or default to 1
                        if (monthsSelect) {
                            monthsSelect.value = searchState?.period === 'monthly' && searchState?.months
                                ? searchState.months
                                : 1;
                            document.getElementById('bookingMonths').value = monthsSelect.value;
                        }
                    }
                    dailyRateDisplay.classList.add('hidden');
                    monthlyRateDisplay.classList.remove('hidden');
                    rateTypeDisplay.textContent = 'Harga Bulanan';
                    
                } else {
                    monthInput.classList.add('hidden');
                    dateInputs.classList.remove('hidden');
                    dailyRateDisplay.classList.remove('hidden');
                    monthlyRateDisplay.classList.add('hidden');
                    rateTypeDisplay.textContent = 'Harga Harian';
                    
                    // Set dates from saved search or use defaults
                    const today = new Date();
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    
                    if (checkInInput) {
                        checkInInput.value = searchState?.check_in || today.toISOString().split('T')[0];
                        checkInInput.dispatchEvent(new Event('change'));
                    }
                    
                    if (checkOutInput) {
                        checkOutInput.value = searchState?.check_out || tomorrow.toISOString().split('T')[0];
                        checkOutInput.dispatchEvent(new Event('change'));
                    }
                }
                updatePriceSummary();
            }

            // --- Date input logic ---
            function handleCheckInChange() {
                const rentTypeSelect = document.getElementById('rent_type');
                const monthsSelect = document.getElementById('months');
                if (!checkInInput.value) return;
                if (rentTypeSelect.value === 'monthly') {
                    // For monthly, update check-out based on months
                    const checkInDate = new Date(checkInInput.value);
                    const months = parseInt(monthsSelect.value, 10) || 1;
                    const checkOutDate = new Date(checkInDate);
                    checkOutDate.setMonth(checkOutDate.getMonth() + months);
                    checkOutInput.value = checkOutDate.toISOString().split('T')[0];
                    
                } else {
                    // For daily, normal logic
                    const minCheckout = new Date(checkInInput.value);
                    minCheckout.setDate(minCheckout.getDate() + 1);
                    checkOutInput.min = minCheckout.toISOString().split('T')[0];
                    if (!checkOutInput.value || new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
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
                // Get saved search state
                const savedSearch = localStorage.getItem('propertySearch');
                const searchState = savedSearch ? JSON.parse(savedSearch) : null;
                
                // Get current date for default values
                const today = new Date();
                const defaultCheckIn = searchState?.check_in || today.toISOString().split('T')[0];
                const defaultCheckOut = searchState?.check_out || new Date(today);
                
                // Set rent type from saved search if available
                if (searchState?.period) {
                    const rentTypeSelect = document.getElementById('rent_type');
                    if (rentTypeSelect) {
                        rentTypeSelect.value = searchState.period;
                        updateRentalType(); // Update UI based on rent type
                    }
                }
                defaultCheckOut.setDate(today.getDate() + 1);
                const defaultCheckOutStr = defaultCheckOut.toISOString().split('T')[0];
                
                // Set initial values
                const checkInInput = document.getElementById('check_in');
                const checkOutInput = document.getElementById('check_out');
                const rentTypeSelect = document.getElementById('rent_type');
                const monthsSelect = document.getElementById('months');
                
                if (checkInInput) {
                    checkInInput.value = defaultCheckIn;
                    checkInInput.min = defaultCheckIn;
                    
                    // Handle check-in date changes
                    checkInInput.addEventListener('change', function() {
                        if (checkInInput.value) {
                            const minCheckout = new Date(checkInInput.value);
                            minCheckout.setDate(minCheckout.getDate() + 1);
                            
                            if (checkOutInput) {
                                checkOutInput.min = formatDate(minCheckout);
                                // If current check-out is before new min date, update it
                                if (!checkOutInput.value || new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
                                    checkOutInput.value = formatDate(minCheckout);
                                }
                            }
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
                    monthsSelect.addEventListener('change', updatePriceSummary);
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
                
                // Clear the search state from localStorage when booking is submitted
                localStorage.removeItem('propertySearch');
                
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

            // Set default dates
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            
            // Format date as YYYY-MM-DD
            const formatDate = (date) => date.toISOString().split('T')[0];
            
            // Set initial values
            if (checkInInput) {
                checkInInput.value = formatDate(today);
                checkInInput.min = formatDate(today);
                
                // Handle check-in date changes
                checkInInput.addEventListener('change', function() {
                    if (checkInInput.value) {
                        const minCheckout = new Date(checkInInput.value);
                        minCheckout.setDate(minCheckout.getDate() + 1);
                        
                        if (checkOutInput) {
                            checkOutInput.min = formatDate(minCheckout);
                            // If current check-out is before new min date, update it
                            if (!checkOutInput.value || new Date(checkOutInput.value) <= new Date(checkInInput.value)) {
                                checkOutInput.value = formatDate(minCheckout);
                            }
                        }
                    }
                    updatePriceSummary();
                });
            }
            
            if (checkOutInput) {
                checkOutInput.value = formatDate(tomorrow);
                checkOutInput.min = formatDate(tomorrow);
                
                // Handle check-out date changes
                checkOutInput.addEventListener('change', updatePriceSummary);
            }
            
            // Initialize other form elements
            if (rentTypeSelect) {
                rentTypeSelect.addEventListener('change', updateRentalType);
            }
            
            if (monthsSelect) {
                monthsSelect.addEventListener('change', updatePriceSummary);
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
</body>
</html>