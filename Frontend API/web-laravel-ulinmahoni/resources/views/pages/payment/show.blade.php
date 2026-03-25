<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('properties.payment.page_title') }} - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <!-- Dark mode + light mode overrides for payment page -->
    <style>
        /* ========== DARK MODE OVERRIDES ========== */

        /* --- Page background --- */
        html.dark body,
        html.dark main,
        html.dark section,
        html.dark .bg-gray-50 {
            background-color: #111827 !important; /* gray-900 */
        }

        /* --- Card / panel backgrounds --- */
        html.dark .bg-white {
            background-color: #1f2937 !important; /* gray-800 */
        }

        /* --- Text colors --- */
        html.dark .text-gray-900 {
            color: #f3f4f6 !important;
        }
        html.dark .text-gray-800 {
            color: #e5e7eb !important;
        }
        html.dark .text-gray-700 {
            color: #d1d5db !important;
        }
        html.dark .text-gray-600 {
            color: #9ca3af !important;
        }
        html.dark .text-gray-500 {
            color: #9ca3af !important;
        }
        html.dark .text-gray-400 {
            color: #6b7280 !important;
        }

        /* --- Borders --- */
        html.dark .border-gray-200 {
            border-color: #374151 !important;
        }
        html.dark .border-gray-300 {
            border-color: #4b5563 !important;
        }
        html.dark .border-t,
        html.dark .border-t.border-gray-200 {
            border-color: #374151 !important;
        }
        html.dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #374151 !important;
        }

        /* --- Shadows --- */
        html.dark .shadow,
        html.dark .shadow-sm,
        html.dark .shadow-lg,
        html.dark .shadow-md {
            box-shadow: none !important;
        }

        /* --- Form inputs --- */
        html.dark input[type="text"],
        html.dark input[type="number"],
        html.dark input[type="email"],
        html.dark input[type="tel"],
        html.dark .border-gray-300.shadow-sm {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        html.dark input::placeholder {
            color: #9ca3af !important;
        }
        html.dark input:disabled,
        html.dark input[readonly] {
            background-color: #1f2937 !important;
            color: #6b7280 !important;
        }

        /* --- Bank / payment cards --- */
        html.dark .bank-card,
        html.dark .payment-card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        html.dark .bank-card:hover,
        html.dark .payment-card:hover {
            border-color: #14b8a6 !important;
        }
        /* Bank logo container background */
        html.dark .bank-card .bg-gray-50,
        html.dark .payment-card .bg-gray-50 {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }

        /* --- Parking option cards --- */
        html.dark .parking-option {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }
        html.dark .parking-option:hover {
            border-color: #14b8a6 !important;
        }
        html.dark label.parking-option.bg-gray-100 {
            background-color: #111827 !important;
        }

        /* --- Voucher details box (green) --- */
        html.dark .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.15) !important;
        }
        html.dark .border-green-200 {
            border-color: rgba(34, 197, 94, 0.3) !important;
        }
        html.dark .text-green-800 {
            color: #86efac !important;
        }
        html.dark .text-green-700 {
            color: #86efac !important;
        }
        html.dark .text-green-600 {
            color: #4ade80 !important;
        }

        /* --- Blue info box (renewal parking info) --- */
        html.dark .bg-blue-50 {
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        html.dark .border-blue-200 {
            border-color: rgba(59, 130, 246, 0.3) !important;
        }
        html.dark .text-blue-800 {
            color: #93c5fd !important;
        }

        /* --- Red elements (error, full parking) --- */
        html.dark .bg-red-100 {
            background-color: rgba(239, 68, 68, 0.2) !important;
        }
        html.dark .text-red-600 {
            color: #fca5a5 !important;
        }

        /* --- Teal active parking badge --- */
        html.dark .bg-teal-100 {
            background-color: rgba(20, 184, 166, 0.2) !important;
        }

        /* --- Green success icon circle --- */
        html.dark .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.2) !important;
        }

        /* --- Modal backgrounds --- */
        html.dark .bg-gray-600.bg-opacity-50 {
            background-color: rgba(0, 0, 0, 0.7) !important;
        }
        html.dark .rounded-md.bg-white,
        html.dark .shadow-lg.rounded-md.bg-white {
            background-color: #1f2937 !important;
        }

        /* --- Modal close buttons --- */
        html.dark button.bg-white.border-gray-300,
        html.dark .border-gray-300.bg-white {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        html.dark button.bg-white.border-gray-300:hover,
        html.dark .border-gray-300.bg-white:hover {
            background-color: #4b5563 !important;
        }

        /* --- Vehicle details container --- */
        html.dark #vehicleDetailsContainer.bg-white {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        /* --- Deposit / parking info box --- */
        html.dark .bg-gray-50.rounded-lg.border.border-gray-200 {
            background-color: #111827 !important;
            border-color: #374151 !important;
        }

        /* --- Deposit fee row inside the box --- */
        html.dark .bg-gray-50.rounded-lg .bg-white.rounded-lg.border {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        /* --- Summary sidebar sticky card --- */
        html.dark .sticky.top-6.bg-white {
            background-color: #1f2937 !important;
        }

        /* --- Radio buttons --- */
        html.dark input[type="radio"] {
            border-color: #4b5563 !important;
            background-color: #374151 !important;
        }

        /* --- QR code image needs white bg to stay scannable in dark mode --- */
        html.dark #qrisCodeImage {
            background-color: #ffffff !important;
            padding: 8px;
            border-color: #4b5563 !important;
        }

        /* --- Gray-100 (parking full bg, etc.) --- */
        html.dark .bg-gray-100 {
            background-color: rgba(107, 114, 128, 0.15) !important;
        }

        /* --- Gray-200 (expired badge bg) --- */
        html.dark .bg-gray-200 {
            background-color: #374151 !important;
        }

        /* ========== LIGHT MODE GLASS OVERRIDES ========== */

        /* Payment section background — transparent for glass effect */
        section.py-12.bg-gray-50 {
            background: transparent !important;
        }

        /* Main payment card — frosted glass */
        .bg-white.rounded-lg.shadow-sm.p-6 {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--glass-blur-strong);
            -webkit-backdrop-filter: var(--glass-blur-strong);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        /* Bank/payment cards — subtle glass */
        .bank-card.bg-white,
        .payment-card.bg-white {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
        }

        /* Modal dialogs — glass panels */
        .shadow-lg.rounded-md.bg-white {
            background: var(--glass-bg-strong) !important;
            backdrop-filter: var(--glass-blur-strong);
            -webkit-backdrop-filter: var(--glass-blur-strong);
            border: 1px solid var(--glass-border);
        }

        /* Light mode text brightening for glass readability */
        .text-gray-400 { color: #555570 !important; }
        .text-gray-500 { color: #4a4a68 !important; }
    </style>
</head>

<body>
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <!-- Hero Section -->
        <div class="hero-section-booking h-72 relative">
            {{-- <img src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" 
                alt="Payment Hero" 
                class="w-full h-full object-cover"> --}}
            <div class="absolute inset-0 gradient-overlay flex items-center justify-center">
                <h1 class="text-4xl text-white font-medium">{{ __('properties.payment.payment_details') }}</h1>
            </div>
        </div>

        <!-- Payment Section -->
        <section class="py-12 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">{{ __('properties.payment.complete_payment') }}</h2>
                    <p class="text-gray-600 mt-1">{{ __('properties.payment.choose_payment_method') }}</p>
                </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Payment Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <form id="paymentForm" action="{{ route('bookings.update-payment', $booking->idrec) }}" method="POST" class="space-y-6">
                            @csrf
                            <input type="hidden" name="payment_method" id="payment_method">
                            <input type="hidden" name="voucher_id" id="voucher_id">
                            <input type="hidden" name="voucher_code" id="voucher_code_hidden">
                            <input type="hidden" name="discount_amount" id="discount_amount" value="0">

                            <!-- Voucher Section -->
                            <div class="mb-6">
                                <label class="block text-lg font-medium text-gray-700 mb-2">{{ __('properties.payment.voucher_code') }}</label>
                                <div class="flex gap-2">
                                    <input type="text" id="voucherCodeInput"
                                        class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500"
                                        placeholder="{{ __('properties.payment.enter_voucher') }}">
                                    <button type="button" id="applyVoucherBtn"
                                        class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 disabled:opacity-50"
                                        disabled>
                                        <span id="voucherBtnText">{{ __('properties.payment.apply') }}</span>
                                        <span id="voucherBtnLoading" class="hidden">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </div>
                                <div id="voucherMessage" class="mt-2 text-sm hidden"></div>
                                <div id="voucherDetails" class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg hidden">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-green-800" id="voucherName"></p>
                                            <p class="text-sm text-green-600" id="voucherDescription"></p>
                                            <p class="text-sm font-bold text-green-700 mt-1">{{ __('properties.payment.discount') }} <span id="voucherDiscount"></span></p>
                                        </div>
                                        <button type="button" id="removeVoucherBtn" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Deposit & Parking Fee Section -->
                            @php
                                $property = \App\Models\Property::find($booking->property_id);
                                // Deposit fee is 0 for renewals (is_renewal = 1)
                                $depositFeeAmount = ($booking->is_renewal == 1) ? 0 : ($property ? ($property->deposit_fee_amount ?? 0) : 0);
                                $parkingFees = $property ? $property->parkingFees()->where('status', 1)->get() : collect([]);

                                // For renewals, check existing parking record
                                $existingParking = null;
                                $isRenewalBooking = $booking->is_renewal == 1;
                                if ($isRenewalBooking) {
                                    $existingParking = \Illuminate\Support\Facades\DB::table('t_parking')
                                        ->where('user_id', auth()->id())
                                        ->where('property_id', $booking->property_id)
                                        ->orderBy('created_at', 'desc')
                                        ->first();
                                }
                            @endphp

                            @if($depositFeeAmount > 0 || $parkingFees->count() > 0)
                            <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h3 class="text-lg font-medium text-gray-700 mb-4">Biaya Tambahan</h3>

                                <!-- Deposit Fee (Read-only) -->
                                @if($depositFeeAmount > 0)
                                <div class="mb-4">
                                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                                        <div>
                                            <span class="font-medium text-gray-700">Deposit</span>
                                            <p class="text-sm text-gray-500">Deposit akan dikembalikan saat checkout</p>
                                        </div>
                                        <span class="font-bold text-gray-900">Rp {{ number_format($depositFeeAmount, 0, ',', '.') }}</span>
                                    </div>
                                    <input type="hidden" name="deposit_fee" id="deposit_fee" value="{{ $depositFeeAmount }}">
                                </div>
                                @endif

                                <!-- Parking Fee Selection -->
                                @if($parkingFees->count() > 0)
                                <div class="mb-2">
                                    <label class="block font-medium text-gray-700 mb-2">Pilih Parkir (Opsional)</label>

                                    @if($isRenewalBooking && $existingParking)
                                    <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Info Perpanjangan:</strong> Anda memiliki parkir
                                            <strong>{{ strtolower($existingParking->parking_type) == 'car' || strtolower($existingParking->parking_type) == 'mobil' ? 'Mobil' : (strtolower($existingParking->parking_type) == 'motorcycle' || strtolower($existingParking->parking_type) == 'motor' ? 'Motor' : ucfirst($existingParking->parking_type)) }}</strong>
                                            yang aktif. Pilih tipe yang sama untuk memperpanjang tanpa mengubah kuota, atau pilih tipe lain jika ingin mengganti.
                                        </p>
                                    </div>
                                    @endif

                                    <div class="space-y-2">
                                        <!-- No Parking Option -->
                                        <label class="parking-option flex items-center justify-between p-3 bg-white rounded-lg border-2 border-gray-200 cursor-pointer hover:border-teal-500 transition-all">
                                            <div class="flex items-center">
                                                <input type="radio" name="parking_selection" value="none" class="mr-3" {{ !$existingParking ? 'checked' : '' }}
                                                    data-parking-fee="0"
                                                    data-parking-type="none">
                                                <div>
                                                    <span class="font-medium text-gray-700">Tidak perlu parkir</span>
                                                </div>
                                            </div>
                                            <span class="font-bold text-gray-500">Rp 0</span>
                                        </label>

                                        @foreach($parkingFees as $parkingFee)
                                        @php
                                            $isRenewal = $booking->is_renewal == 1;
                                            // Don't lock parking for renewals - they're extending existing parking slot
                                            $isFull = !$isRenewal && $parkingFee->capacity && $parkingFee->quota_used >= $parkingFee->capacity;
                                            $remainingSlots = $parkingFee->capacity ? ($parkingFee->capacity - ($parkingFee->quota_used ?? 0)) : null;
                                            // Check if this is the existing parking type for renewals
                                            $isExistingParkingType = $existingParking && strtolower($existingParking->parking_type) === strtolower($parkingFee->parking_type);
                                        @endphp
                                        <label class="parking-option flex items-center justify-between p-3 rounded-lg border-2 transition-all {{ $isFull ? 'bg-gray-100 border-gray-300 cursor-not-allowed opacity-60' : ($isExistingParkingType ? 'bg-white border-teal-500 ring-2 ring-teal-200' : 'bg-white border-gray-200 cursor-pointer hover:border-teal-500') }}">
                                            <div class="flex items-center">
                                                <input type="radio" name="parking_selection" value="{{ $parkingFee->idrec }}" class="mr-3"
                                                    data-parking-fee="{{ $parkingFee->fee }}"
                                                    data-parking-type="{{ $parkingFee->parking_type }}"
                                                    {{ $isFull ? 'disabled' : '' }}
                                                    {{ $isExistingParkingType ? 'checked' : '' }}>
                                                <div>
                                                    <span class="font-medium {{ $isFull ? 'text-gray-400' : 'text-gray-700' }}">
                                                        @if(strtolower($parkingFee->parking_type) == 'car' || strtolower($parkingFee->parking_type) == 'mobil')
                                                            <i class="fas fa-car mr-2"></i>Parkir Mobil
                                                        @elseif(strtolower($parkingFee->parking_type) == 'motorcycle' || strtolower($parkingFee->parking_type) == 'motor')
                                                            <i class="fas fa-motorcycle mr-2"></i>Parkir Motor
                                                        @else
                                                            {{ ucfirst($parkingFee->parking_type) }}
                                                        @endif
                                                        @if($isFull)
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-red-600 bg-red-100 rounded">PENUH</span>
                                                        @elseif($isExistingParkingType)
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-teal-600 bg-teal-100 rounded">PARKIR AKTIF</span>
                                                        @endif
                                                    </span>
                                                    @if($parkingFee->capacity)
                                                    <p class="text-sm {{ $isFull ? 'text-gray-400' : 'text-gray-500' }}">
                                                        Kapasitas: {{ $parkingFee->quota_used ?? 0 }}/{{ $parkingFee->capacity }}
                                                        @if(!$isFull && $remainingSlots !== null)
                                                            <span class="text-green-600">({{ $remainingSlots }} tersisa)</span>
                                                        @endif
                                                    </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="font-bold {{ $isFull ? 'text-gray-400' : 'text-gray-900' }}">Rp {{ number_format($parkingFee->fee, 0, ',', '.') }}</span>
                                        </label>
                                        @endforeach
                                    </div>

                                    <!-- Parking Months Input -->
                                    <div id="parkingMonthsContainer" class="mt-3 {{ $existingParking ? '' : 'hidden' }}">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bulan Parkir</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="number" id="parking_months" name="parking_months"
                                                min="1" max="{{ $booking->booking_months > 0 ? $booking->booking_months : 1 }}" value="1"
                                                class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                                            <span class="text-sm text-gray-600">bulan (maks: {{ $booking->booking_months > 0 ? $booking->booking_months : 1 }} bulan)</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Total biaya parkir: <span id="parkingTotalDisplay" class="font-medium">Rp 0</span></p>
                                    </div>

                                    <!-- Vehicle Details (shown when parking is selected) -->
                                    <div id="vehicleDetailsContainer" class="mt-4 p-4 bg-white rounded-lg border border-gray-200 {{ $existingParking ? '' : 'hidden' }}">
                                        <h4 class="text-sm font-medium text-gray-700 mb-3">Detail Kendaraan</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <label for="vehicle_plate" class="block text-sm text-gray-600 mb-1">Nomor Plat Kendaraan</label>
                                                <input type="text" id="vehicle_plate" name="vehicle_plate"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 uppercase"
                                                    placeholder="Contoh: B 1234 ABC" maxlength="20"
                                                    value="{{ $existingParking->vehicle_plate ?? '' }}"
                                                    oninput="this.value = this.value.toUpperCase()">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Hidden fields for owner data (auto-filled from user) -->
                                    <input type="hidden" id="owner_name" name="owner_name" value="{{ $booking->user_name }}">
                                    <input type="hidden" id="owner_phone" name="owner_phone" value="{{ $booking->user_phone_number }}">

                                    <input type="hidden" name="parking_fee" id="parking_fee" value="0">
                                    <input type="hidden" name="parking_type" id="parking_type" value="none">
                                    <input type="hidden" name="parking_duration" id="parking_duration" value="0">
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Payment Method -->
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-lg font-medium text-gray-700 mb-4">{{ __('properties.payment.payment_method') }}</label>

                                    <!-- Manual Transfer Payment -->
                                    <!-- <div class="mb-6">
                                        <h3 class="text-md font-medium text-gray-700 mb-3">Transfer Manual (Direct)</h3>
                                        <p class="text-sm text-gray-600 mb-3">Transfer langsung ke rekening kami</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <label class="bank-card relative p-6 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-teal-500 hover:shadow-md bg-white">
                                                <input type="radio" name="payment_selection" value="bri_manual" class="sr-only"
                                                    data-payment-type="manual_transfer"
                                                    data-bank="bri_manual"
                                                    data-bank-name="BRI Manual"
                                                    data-bank-logo="https://images.seeklogo.com/logo-png/45/1/bank-bri-logo-png_seeklogo-459990.png">
                                                <div class="flex items-center">
                                                    <div class="w-16 h-16 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200 mr-4 flex-shrink-0">
                                                        <img src="https://images.seeklogo.com/logo-png/45/1/bank-bri-logo-png_seeklogo-459990.png" alt="BRI" class="w-12 h-12 object-contain">
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="font-bold text-gray-900 text-lg mb-1">BRI</div>
                                                        <div class="text-sm text-gray-600 font-mono">0505 01 001671 567</div>
                                                        <div class="text-xs text-gray-500 mt-1">Kelola Aset Properti</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div> -->

                                    <!-- Virtual Account Payment -->
                                    <div class="mb-6">
                                        <h3 class="text-md font-medium text-gray-700 mb-3">{{ __('properties.payment.bank_transfer_va') }}</h3>
                                        <p class="text-sm text-gray-600 mb-3">{{ __('properties.payment.select_bank_va') }}</p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @php
                                            $banks = config('services.doku.banks');
                                            $bankLogos = [
                                                'BRI' => 'https://images.seeklogo.com/logo-png/45/1/bank-bri-logo-png_seeklogo-459990.png',
                                                'BNI' => 'https://www.pinclipart.com/picdir/big/105-1051729_bank-negara-indonesia-logo-bank-bni-transparan-clipart.png',
                                                'MANDIRI' => 'https://freepngdesign.com/content/uploads/images/p-2813-2-bank-mandiri-logo-png-transparent-logo-699390155888.png',
                                                'BCA' => 'https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg',
                                                'CIMB' => 'https://www.buysolar.my/images/banklogo/cimb.png',
                                                'BTN' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/BTN_2024.svg/960px-BTN_2024.svg.png',
                                                'DANAMON' => 'https://www.clipartmax.com/png/middle/86-863868_danamon-bank-logo-bank-danamon.png',
                                                'BNC' => 'https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEiKM1U7jUvSqIDsJYruR_kCYRBzyjAojO62aFqFt2fd4n0Eg1z3pwgRD6jgQkBP7lqtNincIx7UCOuNC7fNhBx3iuJKRClkNADRTTHCoIUvZ8ff3k0nhwWBJaz3QhDOYY1VORkGehl_-_Zy0XO2N2IpqloOCXOZnyJk97WpRnRm9U0nFssUtO9mXRec/s400/GKL24_Bank%20Neo%20Commerce%20-%20Koleksilogo.com.jpg',
                                                'BSI' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Bank_Syariah_Indonesia.svg/960px-Bank_Syariah_Indonesia.svg.png',
                                                'MAYBANK' => 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d7/Maybank_logo.svg/960px-Maybank_logo.svg.png',
                                                'PERMATA' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/ff/Permata_Bank_%282024%29.svg/960px-Permata_Bank_%282024%29.svg.png',
                                            ];
                                        @endphp

                                        @foreach($banks as $bankCode => $bankConfig)
                                        <label class="bank-card relative p-6 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-teal-500 hover:shadow-md bg-white">
                                            <input type="radio" name="payment_selection" value="{{ strtolower($bankCode) }}" class="sr-only"
                                                data-payment-type="bank_transfer"
                                                data-bank="{{ strtolower($bankCode) }}"
                                                data-bank-name="{{ $bankCode }}"
                                                data-bank-logo="{{ $bankLogos[$bankCode] ?? '' }}"
                                                data-dgpc="{{ $bankConfig['dgpc'] }}"
                                                data-channel="{{ $bankConfig['channel'] }}">
                                            <div class="flex items-center">
                                                <div class="w-16 h-16 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200 mr-4 flex-shrink-0">
                                                    @if(isset($bankLogos[$bankCode]))
                                                        <img src="{{ $bankLogos[$bankCode] }}" alt="{{ $bankCode }}" class="w-12 h-12 object-contain">
                                                    @else
                                                        <div class="flex items-center justify-center">
                                                            <span class="text-xs font-bold text-gray-600">{{ $bankCode }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <div class="font-bold text-gray-900 text-lg mb-1">{{ $bankCode }}</div>
                                                    <div class="text-sm text-gray-600">{{ __('properties.payment.virtual_account') }}</div>
                                                </div>
                                            </div>
                                        </label>
                                        @endforeach
                                        </div>
                                    </div>

                                    <!-- QRIS Payment -->
                                    <div class="mb-6">
                                        <h3 class="text-md font-medium text-gray-700 mb-3">{{ __('properties.payment.ewallet_qris') }}</h3>
                                        <div>
                                            <label class="payment-card relative p-6 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-teal-500 hover:shadow-md bg-white block">
                                                <input type="radio" name="payment_selection" value="qris" class="sr-only" data-payment-type="qris">
                                                <div class="flex items-center">
                                                    <div class="w-16 h-16 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200 mr-4 flex-shrink-0">
                                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/QRIS_logo.svg/1200px-QRIS_logo.svg.png" alt="QRIS" class="w-12 h-12 object-contain">
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="font-bold text-gray-900 text-lg mb-1">QRIS</div>
                                                        <div class="text-sm text-gray-600">{{ __('properties.payment.scan_qr') }}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Credit Card Payment -->
                                    <div class="mb-6">
                                        <h3 class="text-md font-medium text-gray-700 mb-3">{{ __('properties.payment.credit_debit') }}</h3>
                                        <div>
                                            <label class="payment-card relative p-6 border-2 border-gray-200 rounded-xl cursor-pointer transition-all duration-200 hover:border-teal-500 hover:shadow-md bg-white block">
                                                <input type="radio" name="payment_selection" value="credit_card" class="sr-only" data-payment-type="credit_card">
                                                <div class="flex items-center">
                                                    <div class="w-20 h-16 flex items-center justify-center gap-1 bg-gray-50 rounded-lg border border-gray-200 mr-4 flex-shrink-0 px-2">
                                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Visa_Inc._logo.svg/1200px-Visa_Inc._logo.svg.png" alt="Visa" class="h-8 w-auto object-contain">
                                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Mastercard-logo.svg/1200px-Mastercard-logo.svg.png" alt="Mastercard" class="h-8 w-auto object-contain">
                                                    </div>
                                                    <div class="flex-1">
                                                        <div class="font-bold text-gray-900 text-lg mb-1">{{ __('properties.payment.credit_debit') }}</div>
                                                        <div class="text-sm text-gray-600">{{ __('properties.payment.other_cards') }}</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <button type="submit" id="submitPayment"
                                    class="w-full bg-teal-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <span id="submitText">{{ __('properties.payment.continue_payment') }}</span>
                                    <span id="loadingSpinner" class="hidden">
                                        <i class="fas fa-spinner fa-spin ml-2"></i>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">{{ __('properties.payment.booking_summary') }}</h2>

                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('properties.payment.transaction_details') }}</h4>
                                <p class="text-sm text-gray-600 font-bold mb-6">{{ __('properties.payment.order_id') }} {{ $booking->order_id }}</p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.code') }} {{ $booking->transaction_code }}</p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.date') }} {{ $booking->created_at->format('d M Y H:i') }}</p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.status') }} {{ ucfirst($booking->transaction_status) }}</p>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('properties.payment.guest_details') }}</h4>
                                <h4 class="font-medium text-gray-600">{{ __('properties.payment.name') }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $booking->user_name }}</p>
                                <h4 class="font-medium text-gray-600">{{ __('properties.payment.email') }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $booking->user_email }}</p>
                                <h4 class="font-medium text-gray-600">{{ __('properties.payment.phone_number') }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $booking->user_phone_number }}</p>
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('properties.payment.property_details') }}</h4>
                                <p class="text-sm text-gray-600">{{ $booking->property_name }}</p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.type') }} {{ $booking->room_name }}</p>
                                @if($booking->room?->no)
                                <p class="text-sm text-gray-600">No. Room: {{ $booking->room->no }}</p>
                                @endif
                            </div>

                            <div>
                                <h4 class="font-medium text-gray-900">{{ __('properties.payment.duration') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.check_in') }} {{ $booking->check_in->format('d F Y') }}</p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.check_out') }} {{ $booking->check_out->format('d F Y') }}</p>

                                @if($booking->booking_months > 0)
                                    <p class="text-sm text-gray-600">{{ str_replace('{count}', $booking->booking_months, __('properties.payment.duration_months')) }}</p>
                                @elseif($booking->booking_days > 0)
                                    <p class="text-sm text-gray-600">{{ str_replace('{count}', $booking->booking_days, __('properties.payment.duration_days')) }}</p>
                                @endif

                            </div>

                            <div class="pt-4 border-t border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-2">{{ __('properties.payment.pricing') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.room_price') }} <span id="summaryRoomPrice">{{ number_format($booking->room_price, 0) }}</span></p>
                                <p class="text-sm text-gray-600 mt-2 pt-2 border-t">{{ __('properties.payment.subtotal') }} <span id="summarySubtotal">{{ number_format($booking->room_price + ($booking->admin_fees ?? 0), 0) }}</span></p>
                                <p id="summaryDiscountRow" class="text-sm text-green-600 hidden">{{ __('properties.payment.voucher_discount') }} <span id="summaryDiscountAmount">0</span></p>
                                <p class="text-sm text-gray-600">{{ __('properties.payment.service_fee') }} <span id="summaryServiceFee">{{ number_format($booking->service_fees, 0) }}</span></p>
                                <p id="summaryDepositRow" class="text-sm text-gray-600 hidden">Deposit: +Rp <span id="summaryDepositAmount">0</span></p>
                                <p id="summaryParkingRow" class="text-sm text-gray-600 hidden">Parkir (<span id="summaryParkingType">-</span>): +Rp <span id="summaryParkingAmount">0</span></p>
                                <p class="text-sm font-bold text-gray-900 mt-2 pt-2 border-t">{{ __('properties.payment.grand_total') }} <span id="summaryTotal">{{ number_format($booking->grandtotal_price, 0) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Success Modal (Virtual Account) -->
        <div id="successModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">{{ __('properties.payment.va_success') }}</h3>
                    <div class="mt-4 px-4 py-3 bg-gray-50 rounded-lg">
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ __('properties.payment.bank') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="modalBank"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ __('properties.payment.va_number') }}</span>
                                <span class="text-sm font-bold text-gray-900" id="modalVANumber"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ __('properties.payment.amount') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="modalAmount"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 space-y-2">
                        <a id="howToPayLink" href="#" target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.how_to_pay') }}
                        </a>
                        <button id="closeModalBtn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div id="errorModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">{{ __('properties.payment.error_occurred') }}</h3>
                    <div class="mt-2 px-4 py-3">
                        <p class="text-sm text-gray-600" id="errorMessage"></p>
                    </div>
                    <div class="mt-4">
                        <button id="closeErrorModalBtn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- QRIS Modal -->
        <div id="qrisModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">{{ __('properties.payment.qris_success') }}</h3>
                    <div class="mt-4 px-4 py-3 bg-gray-50 rounded-lg">
                        <div class="text-center">
                            <p class="text-sm text-gray-600 mb-3">{{ __('properties.payment.qr_code') }}</p>
                            <div id="qrisCodeContainer" class="flex justify-center mb-3">
                                <img id="qrisCodeImage" src="" alt="QRIS Code" class="w-64 h-64 border-2 border-gray-300 rounded">
                            </div>
                            <div class="flex justify-center mb-3">
                                <button id="downloadQrisBtn" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download QR Code
                                </button>
                            </div>
                            <div class="text-left space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('properties.payment.amount') }}</span>
                                    <span class="text-sm font-medium text-gray-900" id="modalQrisAmount"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">{{ __('properties.payment.valid_until') }}</span>
                                    <span class="text-sm font-medium text-gray-900" id="modalQrisExpiry"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button id="closeQrisModalBtn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Credit Card Modal -->
        <div id="ccModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border border-gray-200 w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">{{ __('properties.payment.payment_page_ready') }}</h3>
                    <div class="mt-4 px-4 py-3 bg-gray-50 rounded-lg">
                        <div class="text-left space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ __('properties.payment.invoice') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="modalCCInvoice"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">{{ __('properties.payment.amount') }}</span>
                                <span class="text-sm font-medium text-gray-900" id="modalCCAmount"></span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3">{{ __('properties.payment.redirect_info') }}</p>
                    </div>
                    <div class="mt-4 space-y-2">
                        <a id="ccPaymentLink" href="#" target="_blank" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-teal-600 text-base font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.continue_to_payment') }}
                        </a>
                        <button id="closeCCModalBtn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            {{ __('properties.payment.close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    @include('components.homepage.footer')

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @include('components.homepage.scripts')

        // Translation strings for JavaScript
        const translations = {
            voucher_applied: @json(__('properties.payment.voucher_applied')),
            please_select_payment: @json(__('properties.payment.please_select_payment')),
            processing_payment: @json(__('properties.payment.processing_payment')),
            pay_with_qris: @json(__('properties.payment.pay_with_qris')),
            pay_with_card: @json(__('properties.payment.pay_with_card')),
            pay_with_bank: @json(__('properties.payment.pay_with_bank')),
            please_select_bank: @json(__('properties.payment.please_select_bank'))
        };

        const paymentForm = document.getElementById('paymentForm');
        const submitBtn = document.getElementById('submitPayment');
        const submitText = document.getElementById('submitText');
        const loadingSpinner = document.getElementById('loadingSpinner');
        let selectedPaymentMethod = '';
        let isSubmitting = false;

        // Voucher state
        let appliedVoucher = null;
        const roomPrice = {{ $booking->room_price ?? 0 }};
        const serviceFee = {{ $booking->service_fees ?? 0 }};
        const adminFees = {{ $booking->admin_fees ?? 0 }};

        // Deposit and Parking fee state
        const depositFee = {{ $depositFeeAmount ?? 0 }};
        let selectedParkingFee = 0;
        let selectedParkingType = 'none';
        let parkingUnitPrice = 0; // Price per month
        let parkingMonthsSelected = 1;
        const maxParkingMonths = {{ $booking->booking_months > 0 ? $booking->booking_months : 1 }};

        // Initialize parking state for renewals with existing parking
        @if($existingParking)
        (function initExistingParking() {
            const preSelectedRadio = document.querySelector('input[name="parking_selection"]:checked');
            if (preSelectedRadio && preSelectedRadio.value !== 'none') {
                parkingUnitPrice = parseFloat(preSelectedRadio.dataset.parkingFee) || 0;
                selectedParkingType = preSelectedRadio.dataset.parkingType || 'none';
                parkingMonthsSelected = parseInt(document.getElementById('parking_months')?.value) || 1;
                selectedParkingFee = parkingUnitPrice * parkingMonthsSelected;

                // Update hidden fields
                const parkingFeeInput = document.getElementById('parking_fee');
                const parkingTypeInput = document.getElementById('parking_type');
                const parkingDurationInput = document.getElementById('parking_duration');
                if (parkingFeeInput) parkingFeeInput.value = selectedParkingFee;
                if (parkingTypeInput) parkingTypeInput.value = selectedParkingType;
                if (parkingDurationInput) parkingDurationInput.value = parkingMonthsSelected;

                // Update parking total display
                const parkingTotalDisplay = document.getElementById('parkingTotalDisplay');
                if (parkingTotalDisplay) {
                    parkingTotalDisplay.textContent = 'Rp ' + selectedParkingFee.toLocaleString('id-ID');
                }
            }
        })();
        @endif

        // Calculate subtotal before service fee (for voucher calculation)
        // Formula: Subtotal = Room Price + Admin Fees (without service fee, deposit, parking)
        const subtotalBeforeServiceFee = roomPrice + adminFees;
        const originalTotal = {{ $booking->grandtotal_price ?? 0 }};

        // console.log('Calculated Values:', {
        //     subtotalBeforeServiceFee: subtotalBeforeServiceFee,
        //     originalTotal: originalTotal
        // });

        // Voucher UI elements
        const voucherCodeInput = document.getElementById('voucherCodeInput');
        const applyVoucherBtn = document.getElementById('applyVoucherBtn');
        const voucherMessage = document.getElementById('voucherMessage');
        const voucherDetails = document.getElementById('voucherDetails');
        const removeVoucherBtn = document.getElementById('removeVoucherBtn');

        // Enable voucher button when input has value
        voucherCodeInput.addEventListener('input', function() {
            applyVoucherBtn.disabled = this.value.trim().length === 0;
        });

        // Apply voucher
        applyVoucherBtn.addEventListener('click', async function() {
            const voucherCode = voucherCodeInput.value.trim();
            if (!voucherCode) return;

            // Show loading
            document.getElementById('voucherBtnText').classList.add('hidden');
            document.getElementById('voucherBtnLoading').classList.remove('hidden');
            applyVoucherBtn.disabled = true;

            try {
                const response = await fetch('/api/v1/voucher/validate', {
                    method: 'POST',
                    headers: {
                        'X-API-KEY': '{{ env("API_KEY") }}',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        voucher_code: voucherCode,
                        user_id: {{ auth()->id() }},
                        transaction_amount: subtotalBeforeServiceFee,
                        property_id: {{ $booking->property_id }},
                        room_id: {{ $booking->room_id }}
                    })
                });

                const data = await response.json();

                if (!response.ok || data.status !== 'success') {
                    // Handle specific validation errors from the API
                    let errorMessage = data.message || 'Voucher tidak valid';
                    if (data.errors && Array.isArray(data.errors) && data.errors.length > 0) {
                        errorMessage = data.errors.join(', ');
                    }
                    throw new Error(errorMessage);
                }

                // Apply voucher
                appliedVoucher = {
                    code: voucherCode,
                    ...data.data.voucher,
                    calculation: data.data.calculation
                };

                // Update UI
                document.getElementById('voucherName').textContent = appliedVoucher.name;
                document.getElementById('voucherDescription').textContent = appliedVoucher.description || '';
                document.getElementById('voucherDiscount').textContent = `Rp ${appliedVoucher.calculation.discount_amount.toLocaleString('id-ID')}`;

                voucherDetails.classList.remove('hidden');
                voucherCodeInput.disabled = true;
                applyVoucherBtn.classList.add('hidden');

                // Update hidden fields
                document.getElementById('voucher_code_hidden').value = voucherCode;
                document.getElementById('discount_amount').value = appliedVoucher.calculation.discount_amount;

                // Update summary
                updatePriceSummary();

                showVoucherMessage(translations.voucher_applied, 'success');

            } catch (error) {
                showVoucherMessage(error.message, 'error');
            } finally {
                document.getElementById('voucherBtnText').classList.remove('hidden');
                document.getElementById('voucherBtnLoading').classList.add('hidden');
                applyVoucherBtn.disabled = false;
            }
        });

        // Remove voucher
        removeVoucherBtn.addEventListener('click', function() {
            appliedVoucher = null;
            voucherCodeInput.value = '';
            voucherCodeInput.disabled = false;
            voucherDetails.classList.add('hidden');
            applyVoucherBtn.classList.remove('hidden');
            voucherMessage.classList.add('hidden');

            // Clear hidden fields
            document.getElementById('voucher_code_hidden').value = '';
            document.getElementById('discount_amount').value = '0';

            // Reset summary
            updatePriceSummary();
        });

        // Handle parking fee selection
        document.querySelectorAll('.parking-option input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                parkingUnitPrice = parseFloat(this.dataset.parkingFee) || 0;
                selectedParkingType = this.dataset.parkingType || 'none';

                const parkingMonthsContainer = document.getElementById('parkingMonthsContainer');
                const vehicleDetailsContainer = document.getElementById('vehicleDetailsContainer');
                const parkingMonthsInput = document.getElementById('parking_months');

                if (parkingUnitPrice > 0) {
                    // Show months input and vehicle details when parking is selected
                    parkingMonthsContainer.classList.remove('hidden');
                    vehicleDetailsContainer.classList.remove('hidden');
                    parkingMonthsSelected = Math.max(1, parseInt(parkingMonthsInput.value) || 1);
                    parkingMonthsInput.min = '1';
                    parkingMonthsInput.value = parkingMonthsSelected;
                } else {
                    // Hide months input and vehicle details when "no parking" is selected
                    parkingMonthsContainer.classList.add('hidden');
                    vehicleDetailsContainer.classList.add('hidden');
                    parkingMonthsSelected = 0;
                }

                // Calculate total parking fee
                selectedParkingFee = parkingUnitPrice * parkingMonthsSelected;

                // Update hidden fields
                const parkingFeeInput = document.getElementById('parking_fee');
                const parkingTypeInput = document.getElementById('parking_type');
                const parkingDurationInput = document.getElementById('parking_duration');
                if (parkingFeeInput) parkingFeeInput.value = selectedParkingFee;
                if (parkingTypeInput) parkingTypeInput.value = selectedParkingType;
                if (parkingDurationInput) parkingDurationInput.value = parkingMonthsSelected;

                // Update parking total display
                const parkingTotalDisplay = document.getElementById('parkingTotalDisplay');
                if (parkingTotalDisplay) {
                    parkingTotalDisplay.textContent = 'Rp ' + selectedParkingFee.toLocaleString('id-ID');
                }

                // Update visual selection
                document.querySelectorAll('.parking-option').forEach(option => {
                    option.classList.remove('border-teal-500', 'ring-2', 'ring-teal-200');
                    option.classList.add('border-gray-200');
                });
                this.closest('.parking-option').classList.add('border-teal-500', 'ring-2', 'ring-teal-200');
                this.closest('.parking-option').classList.remove('border-gray-200');

                // Update price summary
                updatePriceSummary();
            });
        });

        // Handle parking months input change
        const parkingMonthsInput = document.getElementById('parking_months');
        if (parkingMonthsInput) {
            parkingMonthsInput.addEventListener('input', function() {
                let months = parseInt(this.value) || 1;
                // Clamp value between 1 and max
                months = Math.max(1, Math.min(months, maxParkingMonths));
                this.value = months;
                parkingMonthsSelected = months;

                // Calculate total parking fee
                selectedParkingFee = parkingUnitPrice * parkingMonthsSelected;

                // Update hidden fields
                const parkingFeeInput = document.getElementById('parking_fee');
                const parkingDurationInput = document.getElementById('parking_duration');
                if (parkingFeeInput) parkingFeeInput.value = selectedParkingFee;
                if (parkingDurationInput) parkingDurationInput.value = parkingMonthsSelected;

                // Update parking total display
                const parkingTotalDisplay = document.getElementById('parkingTotalDisplay');
                if (parkingTotalDisplay) {
                    parkingTotalDisplay.textContent = 'Rp ' + selectedParkingFee.toLocaleString('id-ID');
                }

                // Update price summary
                updatePriceSummary();
            });
        }

        // Initialize deposit fee display and recalculate totals on page load
        if (depositFee > 0) {
            document.getElementById('summaryDepositRow').classList.remove('hidden');
            document.getElementById('summaryDepositAmount').textContent = depositFee.toLocaleString('id-ID');
        }

        // Call updatePriceSummary on page load to include deposit in Grand Total
        updatePriceSummary();

        // Update price summary
        // Formula: Grandtotal = Subtotal - Voucher + Service Fee + Deposit + Parking
        function updatePriceSummary() {
            const discountAmount = appliedVoucher ? appliedVoucher.calculation.discount_amount : 0;

            // Calculate final total: Subtotal - Discount + Service Fee + Deposit + Parking
            const subtotalAfterDiscount = subtotalBeforeServiceFee - discountAmount;
            const finalTotal = subtotalAfterDiscount + serviceFee + depositFee + selectedParkingFee;

            // Update all price elements
            document.getElementById('summarySubtotal').textContent = subtotalBeforeServiceFee.toLocaleString('id-ID');
            document.getElementById('summaryDiscountAmount').textContent = discountAmount.toLocaleString('id-ID');
            document.getElementById('summaryTotal').textContent = finalTotal.toLocaleString('id-ID');

            // Update discount row visibility
            if (discountAmount > 0) {
                document.getElementById('summaryDiscountRow').classList.remove('hidden');
            } else {
                document.getElementById('summaryDiscountRow').classList.add('hidden');
            }

            // Update deposit row
            if (depositFee > 0) {
                document.getElementById('summaryDepositRow').classList.remove('hidden');
                document.getElementById('summaryDepositAmount').textContent = depositFee.toLocaleString('id-ID');
            }

            // Update parking row
            if (selectedParkingFee > 0) {
                document.getElementById('summaryParkingRow').classList.remove('hidden');
                document.getElementById('summaryParkingAmount').textContent = selectedParkingFee.toLocaleString('id-ID');
                const parkingTypeLabel = selectedParkingType === 'car' || selectedParkingType === 'mobil' ? 'Mobil' :
                                        selectedParkingType === 'motorcycle' || selectedParkingType === 'motor' ? 'Motor' :
                                        selectedParkingType;
                // Show type with months info
                const monthsLabel = parkingMonthsSelected > 0 ? ` x ${parkingMonthsSelected} bln` : '';
                document.getElementById('summaryParkingType').textContent = parkingTypeLabel + monthsLabel;
            } else {
                document.getElementById('summaryParkingRow').classList.add('hidden');
            }
        }

        // Show voucher message
        function showVoucherMessage(message, type) {
            voucherMessage.textContent = message;
            voucherMessage.classList.remove('hidden', 'text-red-600', 'text-green-600');
            voucherMessage.classList.add(type === 'error' ? 'text-red-600' : 'text-green-600');
        }

        // Handle payment method selection
        document.querySelectorAll('.payment-card, .bank-card').forEach(card => {
            const radio = card.querySelector('input[type="radio"]');

            card.addEventListener('click', () => {
                if (isSubmitting) return;

                const paymentType = radio.dataset.paymentType;
                const bankName = radio.dataset.bankName;

                // Update the hidden payment method input
                selectedPaymentMethod = paymentType;
                document.getElementById('payment_method').value = radio.value;

                // Update submit button text
                submitBtn.disabled = false;
                if (paymentType === 'qris') {
                    submitText.textContent = translations.pay_with_qris;
                } else if (paymentType === 'credit_card') {
                    submitText.textContent = translations.pay_with_card;
                } else if (paymentType === 'bank_transfer') {
                    submitText.textContent = translations.pay_with_bank.replace('{bank}', bankName);
                    // Store bank info for VA generation
                    let bankInput = paymentForm.querySelector('input[name="bank"]');
                    if (!bankInput) {
                        bankInput = document.createElement('input');
                        bankInput.type = 'hidden';
                        bankInput.name = 'bank';
                        paymentForm.appendChild(bankInput);
                    }
                    bankInput.value = radio.value;
                } else if (paymentType === 'manual_transfer') {
                    submitText.textContent = 'Bayar dengan Transfer Manual';
                }

                // Update visual selection
                document.querySelectorAll('.payment-card, .bank-card').forEach(c => {
                    c.classList.remove('border-teal-500', 'ring-2', 'ring-teal-200');
                    c.classList.add('border-gray-200');
                });
                card.classList.add('border-teal-500', 'ring-2', 'ring-teal-200');
                card.classList.remove('border-gray-200');

                // Ensure the radio is checked for form submission
                radio.checked = true;
            });
        });
        
        // Handle form submission
        paymentForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (isSubmitting) return;

            if (!selectedPaymentMethod) {
                alert(translations.please_select_payment);
                return;
            }

            // Show loading state
            isSubmitting = true;
            submitBtn.disabled = true;
            submitText.textContent = translations.processing_payment;
            loadingSpinner.classList.remove('hidden');

            try {
                // Calculate final amount using formula: Subtotal - Voucher + Service Fee + Deposit + Parking
                const discountAmount = appliedVoucher ? appliedVoucher.calculation.discount_amount : 0;
                const finalAmount = subtotalBeforeServiceFee - discountAmount + serviceFee + depositFee + selectedParkingFee;

                console.log('Payment Calculation:', {
                    subtotalBeforeServiceFee: subtotalBeforeServiceFee,
                    discountAmount: discountAmount,
                    serviceFee: serviceFee,
                    depositFee: depositFee,
                    parkingFee: selectedParkingFee,
                    parkingType: selectedParkingType,
                    finalAmount: finalAmount
                });

                // Base request payload
                const basePayload = {
                    order_id: '{{ $booking->order_id }}',
                    customer_name: `{{ $booking->user_name }}`,
                    customer_email: '{{ $booking->user_email }}',
                    customer_phone: '{{ $booking->user_phone_number }}',
                    amount: parseFloat(finalAmount)
                };

                console.log('Base Payload for Payment:', basePayload);

                let paymentData, endpoint, updateParams;

                // Handle different payment types
                if (selectedPaymentMethod === 'qris') {
                    // QRIS Payment
                    console.log('Generating QRIS:', basePayload);
                    endpoint = '/api/v1/doku/test-generate-qris';

                    const qrisResponse = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-API-KEY': '{{ env("API_KEY") }}',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(basePayload)
                    });

                    paymentData = await qrisResponse.json();

                    if (!qrisResponse.ok || !paymentData.data?.success) {
                        throw new Error(paymentData.error || paymentData.message || 'Gagal membuat QRIS');
                    }

                    updateParams = {
                        payment_method: 'qris',
                        order_id: '{{ $booking->order_id }}',
                        qr_content: paymentData.data.qr_content,
                        qris_data: JSON.stringify(paymentData.data),
                        deposit_fee: depositFee,
                        parking_fee: selectedParkingFee,
                        parking_type: selectedParkingType,
                        parking_duration: parkingMonthsSelected,
                        vehicle_plate: document.getElementById('vehicle_plate')?.value || '',
                        owner_name: document.getElementById('owner_name')?.value || '',
                        owner_phone: document.getElementById('owner_phone')?.value || '',
                        parking_notes: document.getElementById('parking_notes')?.value || ''
                    };

                    // Add voucher data if applied
                    if (appliedVoucher) {
                        updateParams.voucher_code = appliedVoucher.code;
                        updateParams.discount_amount = appliedVoucher.calculation.discount_amount;
                    }

                    // Update booking
                    await updateBookingPayment(updateParams);

                    // Show QRIS modal
                    showQrisModal(paymentData.data);

                } else if (selectedPaymentMethod === 'credit_card') {
                    // Credit Card Payment
                    console.log('Generating Credit Card Payment Page:', basePayload);
                    endpoint = '/api/v1/doku/test-generate-cc';

                    const ccResponse = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-API-KEY': '{{ env("API_KEY") }}',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(basePayload)
                    });

                    paymentData = await ccResponse.json();

                    if (!ccResponse.ok || !paymentData.success) {
                        throw new Error(paymentData.error || paymentData.message || 'Gagal membuat halaman pembayaran');
                    }

                    updateParams = {
                        payment_method: 'credit_card',
                        order_id: '{{ $booking->order_id }}',
                        payment_url: paymentData.payment_url,
                        invoice_number: paymentData.invoice_number,
                        cc_data: JSON.stringify(paymentData),
                        deposit_fee: depositFee,
                        parking_fee: selectedParkingFee,
                        parking_type: selectedParkingType,
                        parking_duration: parkingMonthsSelected,
                        vehicle_plate: document.getElementById('vehicle_plate')?.value || '',
                        owner_name: document.getElementById('owner_name')?.value || '',
                        owner_phone: document.getElementById('owner_phone')?.value || '',
                        parking_notes: document.getElementById('parking_notes')?.value || ''
                    };

                    // Add voucher data if applied
                    if (appliedVoucher) {
                        updateParams.voucher_code = appliedVoucher.code;
                        updateParams.discount_amount = appliedVoucher.calculation.discount_amount;
                    }

                    // Update booking
                    await updateBookingPayment(updateParams);

                    // Show CC modal
                    showCCModal(paymentData);

                } else if (selectedPaymentMethod === 'bank_transfer') {
                    // Virtual Account Payment
                    const selectedBank = document.querySelector('input[name="payment_selection"]:checked');
                    if (!selectedBank || !selectedBank.dataset.bankName) {
                        throw new Error('Silakan pilih bank');
                    }

                    const vaPayload = {
                        ...basePayload,
                        user_name: basePayload.customer_name,
                        user_email: basePayload.customer_email,
                        user_phone: basePayload.customer_phone,
                        bank: selectedBank.dataset.bankName
                    };

                    console.log('Generating VA:', vaPayload);
                    endpoint = '/api/v1/doku/test-generate-va';

                    const vaResponse = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'X-API-KEY': '{{ env("API_KEY") }}',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(vaPayload)
                    });

                    paymentData = await vaResponse.json();

                    if (!vaResponse.ok || !paymentData.data?.success) {
                        throw new Error(paymentData.error || paymentData.message || 'Gagal membuat Virtual Account');
                    }

                    updateParams = {
                        payment_method: selectedBank.value,
                        order_id: '{{ $booking->order_id }}',
                        bank: selectedBank.dataset.bankName,
                        virtual_account_no: paymentData.data.virtual_account_no,
                        va_data: JSON.stringify(paymentData.data),
                        deposit_fee: depositFee,
                        parking_fee: selectedParkingFee,
                        parking_type: selectedParkingType,
                        parking_duration: parkingMonthsSelected,
                        vehicle_plate: document.getElementById('vehicle_plate')?.value || '',
                        owner_name: document.getElementById('owner_name')?.value || '',
                        owner_phone: document.getElementById('owner_phone')?.value || '',
                        parking_notes: document.getElementById('parking_notes')?.value || ''
                    };

                    // Add voucher data if applied
                    if (appliedVoucher) {
                        updateParams.voucher_code = appliedVoucher.code;
                        updateParams.discount_amount = appliedVoucher.calculation.discount_amount;
                    }

                    // Update booking
                    await updateBookingPayment(updateParams);

                    // Show VA modal
                    showSuccessModal(paymentData.data);

                } else if (selectedPaymentMethod === 'manual_transfer') {
                    // Manual Transfer Payment - Direct update without API call
                    updateParams = {
                        payment_method: 'bri_manual',
                        order_id: '{{ $booking->order_id }}',
                        bank: 'BRI Manual',
                        virtual_account_no: '0505 01 001671 567',
                        manual_transfer_data: JSON.stringify({
                            bank_name: 'BRI',
                            account_number: '0505 01 001671 567',
                            account_name: 'Kelola Aset Properti'
                        }),
                        deposit_fee: depositFee,
                        parking_fee: selectedParkingFee,
                        parking_type: selectedParkingType,
                        parking_duration: parkingMonthsSelected,
                        vehicle_plate: document.getElementById('vehicle_plate')?.value || '',
                        owner_name: document.getElementById('owner_name')?.value || '',
                        owner_phone: document.getElementById('owner_phone')?.value || '',
                        parking_notes: document.getElementById('parking_notes')?.value || ''
                    };

                    // Add voucher data if applied
                    if (appliedVoucher) {
                        updateParams.voucher_code = appliedVoucher.code;
                        updateParams.discount_amount = appliedVoucher.calculation.discount_amount;
                    }

                    // Update booking directly
                    await updateBookingPayment(updateParams);

                    // Show manual transfer confirmation modal
                    showManualTransferModal();
                }

            } catch (error) {
                console.error('Error:', error);
                showErrorModal(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
                isSubmitting = false;
                submitBtn.disabled = false;
                submitText.textContent = 'Lanjutkan Pembayaran';
                loadingSpinner.classList.add('hidden');
            }
        });

        // Helper function to update booking payment
        async function updateBookingPayment(params) {
            const updateResponse = await fetch(paymentForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(params)
            });

            const updateData = await updateResponse.json();

            if (!updateResponse.ok) {
                throw new Error(updateData.message || 'Gagal memperbarui pembayaran');
            }

            return updateData;
        }

        // Modal functions
        function showQrisModal(qrisData) {
            // Calculate expiry time
            const expiryMinutes = qrisData.validity_period || '60M';
            const minutes = parseInt(expiryMinutes.replace('M', ''));
            const expiresAt = new Date(Date.now() + minutes * 60000);

            // Save to localStorage so qris-show page survives refresh
            const storageKey = 'qris_data_{{ $booking->order_id }}';
            localStorage.setItem(storageKey, JSON.stringify({
                qr_content: qrisData.qr_content,
                amount: qrisData.amount,
                expiresAt: expiresAt.toISOString(),
                order_id: '{{ $booking->order_id }}'
            }));

            // Redirect to dedicated QRIS page
            window.location.href = '{{ route("payment.qris-show") }}?order_id={{ $booking->order_id }}';
        }

        function showCCModal(ccData) {
            document.getElementById('modalCCInvoice').textContent = ccData.invoice_number || ccData.order_id;
            document.getElementById('modalCCAmount').textContent = `Rp ${parseFloat(ccData.amount || 0).toLocaleString('id-ID')}`;
            document.getElementById('ccPaymentLink').href = ccData.payment_url;

            // Show modal
            document.getElementById('ccModal').classList.remove('hidden');
        }

        // Modal functions
        function showSuccessModal(vaData) {
            // Populate modal with VA data
            document.getElementById('modalBank').textContent = vaData.bank;
            document.getElementById('modalVANumber').textContent = vaData.virtual_account_no;

            // Format the amount properly
            const amount = parseFloat(vaData.total_amount || vaData.amount || 0);
            document.getElementById('modalAmount').textContent = `Rp ${amount.toLocaleString('id-ID')}`;

            // Set how to pay link
            const howToPayLink = document.getElementById('howToPayLink');
            if (vaData.how_to_pay_page) {
                howToPayLink.href = vaData.how_to_pay_page;
                howToPayLink.classList.remove('hidden');
            } else if (vaData.how_to_pay_api) {
                howToPayLink.href = vaData.how_to_pay_api;
                howToPayLink.classList.remove('hidden');
            } else {
                howToPayLink.classList.add('hidden');
            }

            // Show modal
            document.getElementById('successModal').classList.remove('hidden');
        }

        function showManualTransferModal() {
            // Calculate final amount using formula: Subtotal - Voucher + Service Fee + Deposit + Parking
            const discountAmount = appliedVoucher ? appliedVoucher.calculation.discount_amount : 0;
            const finalAmount = subtotalBeforeServiceFee - discountAmount + serviceFee + depositFee + selectedParkingFee;

            // Create modal HTML dynamically
            const modalHtml = `
                <div id="manualTransferModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
                        <div class="mt-3 text-center">
                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Transfer Manual Berhasil</h3>
                            <div class="mt-4 px-4 py-3 bg-gray-50 rounded-lg">
                                <div class="text-left space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Bank</span>
                                        <span class="text-sm font-medium">BRI</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">No. Rekening</span>
                                        <span class="text-sm font-bold font-mono">0505 01 001671 567</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Atas Nama</span>
                                        <span class="text-sm font-medium">Kelola Aset Properti</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Jumlah</span>
                                        <span class="text-sm font-medium">Rp ${finalAmount.toLocaleString('id-ID')}</span>
                                    </div>
                                </div>
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p class="text-xs text-yellow-800">
                                        <strong>Catatan:</strong> Silakan transfer sesuai jumlah di atas dan simpan bukti transfer untuk konfirmasi.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 space-y-2">
                                <button id="closeManualTransferModalBtn" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Add modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Show modal
            document.getElementById('manualTransferModal').classList.remove('hidden');

            // Add close handler
            document.getElementById('closeManualTransferModalBtn').addEventListener('click', function() {
                document.getElementById('manualTransferModal').classList.add('hidden');
                document.getElementById('manualTransferModal').remove();
                window.location.href = '{{ route("bookings.index") }}';
            });

            // Close modal when clicking outside
            document.getElementById('manualTransferModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    this.remove();
                    window.location.href = '{{ route("bookings.index") }}';
                }
            });
        }

        function showErrorModal(message) {
            document.getElementById('errorMessage').textContent = message;
            document.getElementById('errorModal').classList.remove('hidden');
        }

        // Close modal handlers
        document.getElementById('closeModalBtn').addEventListener('click', function() {
            document.getElementById('successModal').classList.add('hidden');
            window.location.href = '{{ route("bookings.index") }}';
        });

        document.getElementById('closeErrorModalBtn').addEventListener('click', function() {
            document.getElementById('errorModal').classList.add('hidden');
        });

        document.getElementById('downloadQrisBtn').addEventListener('click', function() {
            const img = document.getElementById('qrisCodeImage');
            const src = img.src;
            if (!src) return;

            if (src.startsWith('data:image')) {
                const a = document.createElement('a');
                a.href = src;
                a.download = 'qris-code.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            } else {
                fetch(src)
                    .then(res => res.blob())
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'qris-code.png';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);
                    })
                    .catch(() => window.open(src, '_blank'));
            }
        });

        document.getElementById('closeQrisModalBtn').addEventListener('click', function() {
            document.getElementById('qrisModal').classList.add('hidden');
            window.location.href = '{{ route("bookings.index") }}';
        });

        document.getElementById('closeCCModalBtn').addEventListener('click', function() {
            document.getElementById('ccModal').classList.add('hidden');
            window.location.href = '{{ route("bookings.index") }}';
        });

        // Close modal when clicking outside
        document.getElementById('successModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                window.location.href = '{{ route("bookings.index") }}';
            }
        });

        document.getElementById('errorModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('qrisModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                window.location.href = '{{ route("bookings.index") }}';
            }
        });

        document.getElementById('ccModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                window.location.href = '{{ route("bookings.index") }}';
            }
        });
    });
</script>
</body>
</html>
