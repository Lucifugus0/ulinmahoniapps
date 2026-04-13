<!DOCTYPE html>
<html class="" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('booking.index.page_title') }} - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // API Key from .env
        const API_KEY = '{{ config("services.api.key") }}';

        // Translation strings for JavaScript
        const translations = {
            invalid_file_type: '{{ __("booking.js.invalid_file_type") }}',
            invalid_file_type_text: '{{ __("booking.js.invalid_file_type_text") }}',
            file_too_large: '{{ __("booking.js.file_too_large") }}',
            file_too_large_text: '{{ __("booking.js.file_too_large_text") }}',
            confirm_upload: '{{ __("booking.js.confirm_upload") }}',
            confirm_upload_text: '{{ __("booking.js.confirm_upload_text") }}',
            upload_failed: '{{ __("booking.js.upload_failed") }}',
            upload_failed_text: '{{ __("booking.js.upload_failed_text") }}',
            upload_payment_proof: '{{ __("booking.js.upload_payment_proof") }}',
            upload_payment_proof_text: '{{ __("booking.js.upload_payment_proof_text") }}',
            invalid_file_type_js: '{{ __("booking.js.invalid_file_type_js") }}',
            invalid_file_type_js_text: '{{ __("booking.js.invalid_file_type_js_text") }}',
            file_too_large_js: '{{ __("booking.js.file_too_large_js") }}',
            file_too_large_js_text: '{{ __("booking.js.file_too_large_js_text") }}',
            booking_details: '{{ __("booking.js.booking_details") }}',
            booking_information: '{{ __("booking.js.booking_information") }}',
            property_details: '{{ __("booking.js.property_details") }}',
            booking_dates: '{{ __("booking.js.booking_dates") }}',
            order_id: '{{ __("booking.js.order_id") }}',
            transaction_type: '{{ __("booking.js.transaction_type") }}',
            user_name: '{{ __("booking.js.user_name") }}',
            phone: '{{ __("booking.js.phone") }}',
            email: '{{ __("booking.js.email") }}',
            status_label: '{{ __("booking.js.status_label") }}',
            virtual_account_number: '{{ __("booking.js.virtual_account_number") }}',
            transfer_to_va: '{{ __("booking.js.transfer_to_va") }}',
            property_label: '{{ __("booking.js.property_label") }}',
            room: '{{ __("booking.js.room") }}',
            type: '{{ __("booking.js.type") }}',
            total_price: '{{ __("booking.js.total_price") }}',
            check_in_label: '{{ __("booking.js.check_in_label") }}',
            check_out_label: '{{ __("booking.js.check_out_label") }}',
            expired_suffix: '{{ __("booking.js.expired_suffix") }}',
            days: '{{ __("booking.js.days") }}',
            months: '{{ __("booking.js.months") }}',
            paid_label: '{{ __("booking.js.paid_label") }}',
            no_attachment: '{{ __("booking.js.no_attachment") }}',
            transaction: '{{ __("booking.js.transaction") }}',
            virtual_account: '{{ __("booking.js.virtual_account") }}',
            bank: '{{ __("booking.js.bank") }}',
            close: '{{ __("booking.actions.close") }}',
            cancel: '{{ __("booking.actions.cancel") }}',
            confirm_upload_btn: '{{ __("booking.actions.confirm_upload") }}',
            // Renewal translations
            renew_modal_title: '{{ __("booking.js.renew_modal_title") }}',
            renew_modal_subtitle: '{{ __("booking.js.renew_modal_subtitle") }}',
            confirm_renew: '{{ __("booking.js.confirm_renew") }}',
            processing: '{{ __("booking.js.processing") }}',
            renew_success: '{{ __("booking.js.renew_success") }}',
            renew_success_text: '{{ __("booking.js.renew_success_text") }}',
            renew_error: '{{ __("booking.js.renew_error") }}',
            renew_error_text: '{{ __("booking.js.renew_error_text") }}',
            room_unavailable: '{{ __("booking.js.room_unavailable") }}',
            room_unavailable_text: '{{ __("booking.js.room_unavailable_text") }}',
            invalid_dates: '{{ __("booking.js.invalid_dates") }}',
            invalid_dates_text: '{{ __("booking.js.invalid_dates_text") }}',
        };

    // Make confirmFileUpload available globally
    window.confirmFileUpload = async function(input) {
        if (!input.files || input.files.length === 0) {
            return;
        }

        const file = input.files[0];
        const validTypes = ['image/jpeg', 'image/png'];
        const maxSize = 10 * 1024 * 1024; // 10MB

        // Check file type
        if (!validTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: translations.invalid_file_type,
                text: translations.invalid_file_type_text,
                confirmButtonColor: '#0d9488',
            });
            input.value = '';
            return;
        }

        // Check file size
        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: translations.file_too_large,
                text: translations.file_too_large_text,
                confirmButtonColor: '#0d9488',
            });
            input.value = '';
            return;
        }

        // Show preview and confirmation
        const reader = new FileReader();
        reader.onload = async function(e) {
            try {
                const result = await Swal.fire({
                    title: translations.confirm_upload,
                    html: `
                        <div class="text-center">
                            <p class="mb-4">${translations.confirm_upload_text}</p>
                            <img src="${e.target.result}" class="max-w-full h-auto rounded-lg mx-auto mb-4" style="max-height: 300px;" alt="Preview">
                            <p class="text-sm text-gray-600">${file.name} (${(file.size / 1024).toFixed(2)} KB)</p>
                            <div id="upload-progress" class="hidden mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div id="progress-bar" class="bg-teal-600 h-2.5 rounded-full" style="width: 0%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">Uploading...</p>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonColor: '#0d9488',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: translations.confirm_upload_btn,
                    cancelButtonText: translations.cancel,
                    reverseButtons: true,
                    showLoaderOnConfirm: false,
                    allowOutsideClick: false,
                    preConfirm: async () => {
                        // Show progress bar
                        document.getElementById('upload-progress').classList.remove('hidden');
                        const progressBar = document.getElementById('progress-bar');
                        
                        // Simulate progress (in a real app, you'd use actual upload progress events)
                        for (let i = 0; i <= 100; i += 10) {
                            await new Promise(resolve => setTimeout(resolve, 100));
                            progressBar.style.width = `${i}%`;
                        }
                        
                        // Submit the form
                        return new Promise((resolve) => {
                            input.form.submit();
                            // The form submission will navigate away, so we don't need to resolve
                        });
                    }
                });

                if (result.dismiss === Swal.DismissReason.cancel) {
                    // Reset the input if canceled
                    input.value = '';
                }
            } catch (error) {
                console.error('Upload error:', error);
                Swal.fire({
                    icon: 'error',
                    title: translations.upload_failed,
                    text: translations.upload_failed_text,
                    confirmButtonColor: '#0d9488',
                });
                input.value = '';
            }
        };
        reader.readAsDataURL(file);
    };
    </script>
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
    <!-- Dark mode + liquid glass overrides for booking page -->
    <style>
        /* Dark mode overrides for booking page */
        html.dark body,
        html.dark main,
        html.dark section,
        html.dark .bg-gray-50 {
            background-color: #111827 !important; /* gray-900 */
        }
        html.dark .bg-white {
            background-color: #1f2937 !important; /* gray-800 */
        }
        html.dark .text-gray-900 {
            color: #f3f4f6 !important;
        }
        html.dark .text-gray-700 {
            color: #d1d5db !important;
        }
        html.dark .text-gray-500 {
            color: #9ca3af !important;
        }
        html.dark .text-gray-600 {
            color: #9ca3af !important;
        }
        html.dark .text-gray-400 {
            color: #6b7280 !important;
        }
        html.dark .border-gray-200,
        html.dark .divide-gray-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #374151 !important;
        }
        html.dark .shadow,
        html.dark .shadow-lg {
            box-shadow: none !important;
        }
        html.dark thead.bg-gray-50 {
            background-color: #1f2937 !important;
        }
        html.dark tbody.bg-white {
            background-color: #1f2937 !important;
        }
        html.dark tr.hover\:bg-gray-50:hover {
            background-color: #374151 !important;
        }
        html.dark .bg-yellow-50 {
            background-color: rgba(253, 224, 71, 0.1) !important;
        }
        html.dark .text-yellow-800 {
            color: #fbbf24 !important;
        }
        html.dark .border-yellow-300 {
            border-color: rgba(253, 224, 71, 0.3) !important;
        }

        /* Dark mode status badge overrides — deeper tinted backgrounds for dark cards */
        html.dark .bg-red-50 {
            background-color: rgba(239, 68, 68, 0.15) !important;
        }
        html.dark .text-red-700 {
            color: #fca5a5 !important; /* red-300 */
        }
        html.dark .bg-yellow-50 {
            background-color: rgba(234, 179, 8, 0.15) !important;
        }
        html.dark .text-yellow-700 {
            color: #fde047 !important; /* yellow-300 */
        }
        html.dark .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.15) !important;
        }
        html.dark .text-green-700 {
            color: #86efac !important; /* green-300 */
        }
        html.dark .bg-gray-100 {
            background-color: rgba(107, 114, 128, 0.15) !important;
        }
        /* Badge border in dark mode */
        html.dark span.border.border-gray-200 {
            border-color: #374151 !important;
        }

        /* --- Dark mode: blue tinted elements (VA box, info button, modal sections) --- */
        html.dark .bg-blue-50 {
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        html.dark .text-blue-900 {
            color: #93c5fd !important; /* blue-300 */
        }
        html.dark .text-blue-700 {
            color: #93c5fd !important;
        }
        html.dark .text-blue-600 {
            color: #60a5fa !important;
        }
        html.dark .border-blue-300 {
            border-color: rgba(59, 130, 246, 0.3) !important;
        }

        /* --- Dark mode: teal tinted elements (view attachment, checked-in badge) --- */
        html.dark .bg-teal-50 {
            background-color: rgba(20, 184, 166, 0.15) !important;
        }
        html.dark .text-teal-700 {
            color: #5eead4 !important; /* teal-300 */
        }
        html.dark .text-teal-600 {
            color: #2dd4bf !important;
        }

        /* --- Dark mode: amber/orange elements (re-upload button) --- */
        html.dark .bg-amber-50 {
            background-color: rgba(245, 158, 11, 0.15) !important;
        }
        html.dark .text-amber-600 {
            color: #fbbf24 !important;
        }

        /* --- Dark mode: purple elements (checked-out badge) --- */
        html.dark .bg-purple-50 {
            background-color: rgba(168, 85, 247, 0.15) !important;
        }
        html.dark .text-purple-700 {
            color: #c4b5fd !important; /* purple-300 */
        }

        /* --- Dark mode: green tinted modal section --- */
        html.dark .bg-green-50 {
            background-color: rgba(34, 197, 94, 0.15) !important;
        }

        /* --- Dark mode: form inputs and borders --- */
        html.dark input[type="date"],
        html.dark input[type="text"],
        html.dark input[type="number"] {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }
        html.dark input[readonly],
        html.dark input.bg-gray-100 {
            background-color: #1f2937 !important;
            border-color: #4b5563 !important;
            color: #9ca3af !important;
        }
        html.dark .border-gray-300 {
            border-color: #4b5563 !important;
        }

        /* --- Dark mode: modal footer and cancel buttons --- */
        html.dark .bg-gray-200 {
            background-color: #4b5563 !important;
        }
        html.dark button.bg-gray-200 {
            background-color: #4b5563 !important;
            color: #e5e7eb !important;
        }
        html.dark button.bg-gray-200:hover {
            background-color: #6b7280 !important;
        }

        /* --- Dark mode: upload button --- */
        html.dark label.border-gray-300.bg-white {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }
        html.dark label.border-gray-300.bg-white:hover {
            background-color: #4b5563 !important;
        }

        /* --- Dark mode: radio button labels --- */
        html.dark .border-gray-300[type="radio"] {
            border-color: #4b5563 !important;
        }

        /* --- Dark mode: modal status badge colors (JS-generated) --- */
        html.dark .bg-red-100 {
            background-color: rgba(239, 68, 68, 0.2) !important;
        }
        html.dark .text-red-800 {
            color: #fca5a5 !important;
        }
        html.dark .bg-yellow-100 {
            background-color: rgba(234, 179, 8, 0.2) !important;
        }
        html.dark .text-yellow-800 {
            color: #fde047 !important;
        }
        html.dark .bg-green-100 {
            background-color: rgba(34, 197, 94, 0.2) !important;
        }
        html.dark .text-green-800 {
            color: #86efac !important;
        }
        html.dark .text-gray-800 {
            color: #e5e7eb !important;
        }

        /* --- Dark mode: disabled buttons --- */
        html.dark button.bg-gray-400 {
            background-color: #4b5563 !important;
        }
        html.dark button.bg-blue-400 {
            background-color: rgba(59, 130, 246, 0.4) !important;
        }

        /* --- Dark mode: modal overlay backdrop --- */
        html.dark .bg-black.bg-opacity-50 {
            background-color: rgba(0, 0, 0, 0.7) !important;
        }

        /* Main booking card — frosted glass panel */
        section.py-12 > div > .bg-white.rounded-xl {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--glass-blur-strong);
            -webkit-backdrop-filter: var(--glass-blur-strong);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }
        /* Booking section background — transparent instead of opaque gray */
        section.py-12.bg-gray-50 {
            background: transparent !important;
        }
        /* Table styling — unified with Room Availability reference */
        /* Light mode: gradient header, white body, blue-tinted hover */
        .booking-table thead {
            background: linear-gradient(to right, #f9fafb, #f1f5f9) !important;
        }
        .booking-table tbody {
            background: var(--glass-bg) !important;
        }
        .booking-table tbody tr {
            transition: all 0.2s;
        }
        .booking-table tbody tr:hover {
            background: rgba(59, 130, 246, 0.06) !important;
        }
        .booking-table thead th {
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        /* Dark mode: solid dark backgrounds matching Room Availability */
        html.dark .booking-table { border-color: #334155 !important; }
        html.dark .booking-table thead { background: #334155 !important; }
        html.dark .booking-table thead th { color: #cbd5e1 !important; }
        html.dark .booking-table tbody { background-color: #1e293b !important; }
        html.dark .booking-table tbody tr { background-color: #1e293b !important; border-color: #334155 !important; }
        html.dark .booking-table tbody tr:nth-child(even) { background-color: #1e293b !important; }
        html.dark .booking-table tbody tr:hover { background-color: #334155 !important; }
        html.dark .booking-table .text-gray-900 { color: #f1f5f9 !important; }
        html.dark .booking-table .text-gray-500 { color: #94a3b8 !important; }
        html.dark .booking-table .divide-y > :not([hidden]) ~ :not([hidden]) { border-color: #334155 !important; }
        /* Modal dialogs — glass panels */
        .bg-white.rounded-lg.shadow-xl {
            background: var(--glass-bg-strong) !important;
            backdrop-filter: var(--glass-blur-strong);
            -webkit-backdrop-filter: var(--glass-blur-strong);
            border: 1px solid var(--glass-border);
        }
        /* Modal footer — glass instead of opaque gray */
        .border-t.border-gray-200.bg-gray-50 {
            background: var(--glass-bg) !important;
        }
        /* Payment reminder banner — glass tinted yellow */
        .bg-yellow-50.border.border-yellow-300 {
            background: rgba(253, 224, 71, 0.15) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid rgba(253, 224, 71, 0.3) !important;
        }
        /* Light mode text brightening — darker grays for glass readability */
        .text-gray-400 { color: #555570 !important; }
        .text-gray-500 { color: #4a4a68 !important; }
        input::placeholder { color: #555570 !important; opacity: 1 !important; }
        html.dark input::placeholder { color: #a0a0b8 !important; }

        /* Fix SweetAlert2 positioning — body > * rule from homepage styles
           sets position:relative which overrides Swal's position:fixed container */
        .swal2-container {
            position: fixed !important;
            z-index: 10000 !important;
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    @include('components.homepage.header')

    <main>
        <!-- Hero Section — starts at top of page, header overlays on top -->
        <div class="hero-section relative" style="height: 22rem;">
            <img src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" 
                alt="Bookings Hero" 
                class="w-full h-full object-cover" >
            <div class="absolute inset-0 gradient-overlay flex items-center justify-center">
                <h1 class="text-4xl text-white font-medium">{{ __('booking.index.page_subtitle') }}</h1>
            </div>
        </div>

        <!-- Bookings Section -->

        
    <section class="py-12 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Payment reminder banner removed --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg dark:shadow-none overflow-hidden">
            <div class="overflow-x-auto">
                <div x-data="{ tab: 'all' }">
                    <div class="flex border-b border-gray-200 mb-4">
                        <button @click="tab = 'all'" :class="tab === 'all' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-3 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition">{{ __('booking.index.tabs.all') }}</button>
                        <button @click="tab = 'upcoming'" :class="tab === 'upcoming' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-3 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition">{{ __('booking.index.tabs.upcoming') }}</button>
                        <button @click="tab = 'completed'" :class="tab === 'completed' ? 'border-teal-500 text-teal-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="w-1/3 py-3 px-1 text-center border-b-2 font-medium text-sm focus:outline-none transition">{{ __('booking.index.tabs.completed') }}</button>
                    </div>

                    <!-- All Bookings Tab -->
                    <div x-show="tab === 'all'">
                        <table class="min-w-full divide-y divide-gray-200 booking-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.booking_id') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.property') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.check_in_out') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.paid_amount') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.status') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php $filteredBookings = $allBookings; @endphp
                                @forelse ($filteredBookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        {{-- Booking ID --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->order_id }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div class="flex items-center"><i class="fas fa-user mr-1"></i> {{ $booking->user_name }}</div>
                                                <div class="flex items-center"><i class="fas fa-phone mr-1"></i> {{ $booking->user_phone_number }}</div>
                                            </div>
                                        </td>
                                        {{-- Property: Room No → Type → Property Name --}}
                                        <td class="px-6 py-4">
                                            @if($booking->room?->no)
                                            <div class="text-sm font-semibold text-gray-900">No. {{ $booking->room->no }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500">{{ $booking->room_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->property_name }}</div>
                                        </td>
                                        {{-- Check In/Out --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 flex items-center">
                                                <i class="far fa-calendar-check mr-1 text-teal-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="far fa-calendar-times mr-1 text-red-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                            </div>
                                        </td>
                                        {{-- Paid Amount --}}
                                        <td class="px-6 py-4">
                                            @php
                                                $roomPrice = $booking->room_price ?? 0;
                                                $parkingFee = $booking->parking_fee ?? 0;
                                                $serviceFees = $booking->service_fees ?? 0;
                                                $depositFee = $booking->deposit_fee ?? 0;
                                                $totalStay = $roomPrice + $parkingFee + $serviceFees;
                                            @endphp
                                            <div class="text-sm text-gray-900"><span class="font-medium">{{ __('booking.index.labels.total_stay') }}:</span> Rp {{ number_format($totalStay, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500">{{ __('booking.index.labels.deposit') }}: Rp {{ number_format($depositFee, 0, ',', '.') }}</div>
                                            @if($booking->paid_at)
                                                <div class="text-xs text-green-600 flex items-center mt-1">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->paid_at)->format('d M Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        {{-- Status + Payment Method Badge --}}
                                        <td class="px-6 py-4">
                                            @php
                                                $status = strtolower($booking->transaction_status);
                                                $isCheckedIn = $booking->booking && $booking->booking->check_in_at;
                                                $isCheckedOut = $booking->booking && $booking->booking->check_out_at;
                                                $isAlreadyRenewed = $booking->renewal_status == 1;
                                            @endphp
                                            {{-- Cancelled/expired checked first — overrides check-in/check-out state --}}
                                            @if(in_array($status, ['cancelled', 'canceled']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700"><span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span>{{ __('booking.index.labels.cancelled') }}</span>
                                            @elseif($status === 'expired')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600"><span class="w-1.5 h-1.5 rounded-full bg-gray-400 mr-1.5"></span>{{ __('booking.index.labels.expired') }}</span>
                                            @elseif($isAlreadyRenewed)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800"><i class="fas fa-redo mr-1"></i>  {{ __('booking.index.labels.renewed') }}</span>
                                            @elseif($isCheckedOut)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700"><span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-1.5"></span>{{ __('booking.index.labels.checked_out') }}</span>
                                            @elseif($isCheckedIn)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>Checked In</span>
                                            @elseif($status === 'paid')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>{{ __('booking.index.labels.paid') }}</span>
                                            @elseif($status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1.5"></span>{{ __('booking.index.labels.pending') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ ucfirst($status) }}</span>
                                            @endif
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ paymentMethodBadge($booking->transaction_type) }}
                                                </span>
                                            </div>
                                        </td>
                                        {{-- Actions --}}
                                        <td class="px-6 py-4">
                                            @if(in_array($status, ['pending', 'waiting']))
                                                <button onclick="cancelBooking('{{ $booking->order_id }}', '{{ $status }}')"
                                                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white rounded-md hover:bg-red-700 text-xs font-medium">
                                                    <i class="fas fa-times-circle mr-1"></i> {{ __('booking.actions.cancel_booking') }}
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                            <p>{{ __('booking.index.empty_states.no_bookings') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>


                    @php
                        // Payment method badge alias helper
                        function paymentMethodBadge($type) {
                            $t = $type ?? '';
                            $map = [
                                'QRIS' => 'QRIS - App',
                                'qris' => 'QRIS - Web',
                                'CREDITCARD' => 'CC - App',
                                'credit_card' => 'CC - Web',
                                'BRI Manual' => 'BRI Direct - App',
                                'bri_manual' => 'BRI Direct - Web',
                                'Transfer VA' => 'VA - App',
                                'promo' => 'Promo',
                            ];
                            return $map[$t] ?? 'VA - Web';
                        }
                    @endphp

                    <!-- Upcoming Bookings Tab: paid, not yet checked out -->
                    <div x-show="tab === 'upcoming'">
                        <table class="min-w-full divide-y divide-gray-200 booking-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.booking_id') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.property') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.check_in_out') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.paid_amount') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.status') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $upcomingBookings = $allBookings->filter(function($booking) {
                                        $status = strtolower($booking->transaction_status);
                                        if (!in_array($status, ['paid', 'completed'])) return false;
                                        if ($booking->booking && $booking->booking->check_out_at) return false;
                                        if ($booking->renewal_status == 1 && $booking->check_out && \Carbon\Carbon::parse($booking->check_out)->lt(now())) return false;
                                        return true;
                                    });
                                @endphp
                                @forelse ($upcomingBookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        {{-- Booking ID --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->order_id }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div class="flex items-center"><i class="fas fa-user mr-1"></i> {{ $booking->user_name }}</div>
                                                <div class="flex items-center"><i class="fas fa-phone mr-1"></i> {{ $booking->user_phone_number }}</div>
                                            </div>
                                        </td>
                                        {{-- Property: Room No → Type → Property Name --}}
                                        <td class="px-6 py-4">
                                            @if($booking->room?->no)
                                            <div class="text-sm font-semibold text-gray-900">No. {{ $booking->room->no }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500">{{ $booking->room_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->property_name }}</div>
                                        </td>
                                        {{-- Check In/Out --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 flex items-center">
                                                <i class="far fa-calendar-check mr-1 text-teal-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="far fa-calendar-times mr-1 text-red-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                            </div>
                                        </td>
                                        {{-- Paid Amount: Total Stay, Deposit, Paid date --}}
                                        <td class="px-6 py-4">
                                            @php
                                                $roomPrice = $booking->room_price ?? 0;
                                                $parkingFee = $booking->parking_fee ?? 0;
                                                $serviceFees = $booking->service_fees ?? 0;
                                                $depositFee = $booking->deposit_fee ?? 0;
                                                $totalStay = $roomPrice + $parkingFee + $serviceFees;
                                            @endphp
                                            <div class="text-sm text-gray-900">
                                                <span class="font-medium">{{ __('booking.index.labels.total_stay') }}:</span> Rp {{ number_format($totalStay, 0, ',', '.') }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ __('booking.index.labels.deposit') }}: Rp {{ number_format($depositFee, 0, ',', '.') }}
                                            </div>
                                            @if($booking->paid_at)
                                                <div class="text-xs text-green-600 flex items-center mt-1">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->paid_at)->format('d M Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        {{-- Status + Payment Method Badge --}}
                                        <td class="px-6 py-4">
                                            @php
                                                $isCheckedIn = $booking->booking && $booking->booking->check_in_at;
                                                $isAlreadyRenewed = $booking->renewal_status == 1;
                                            @endphp
                                            {{-- Cancelled/expired checked first — overrides check-in state --}}
                                            @if(in_array(strtolower($booking->transaction_status), ['cancelled', 'canceled']))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> {{ __('booking.index.labels.cancelled') }}
                                                </span>
                                            @elseif($isAlreadyRenewed)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                                                    <i class="fas fa-redo mr-1.5 text-xs"></i> {{ __('booking.index.labels.renewed') }}
                                                </span>
                                            @elseif($isCheckedIn)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Checked In
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span> Paid
                                                </span>
                                            @endif
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ paymentMethodBadge($booking->transaction_type) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $checkOutDate = \Carbon\Carbon::parse($booking->check_out)->startOfDay();
                                                $today = now()->startOfDay();
                                                $isTooLate = $today > $checkOutDate;
                                                $canRenew = $isCheckedIn && !$isTooLate && !$isAlreadyRenewed;
                                            @endphp
                                            @if(!$isCheckedIn && strtolower($booking->transaction_status) === 'paid')
                                                <button onclick="cancelBooking('{{ $booking->order_id }}', 'paid')"
                                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-200 text-sm font-medium mb-2">
                                                    <i class="fas fa-times-circle mr-2"></i> Batalkan
                                                </button>
                                            @endif
                                            @if($canRenew)
                                                @php
                                                    /* Renewal check-in must come from the latest PAID/COMPLETED non-renewed
                                                       booking for this room+user. Cancelled rows are intentionally excluded —
                                                       a cancelled renewal must not push the renewal start date forward. */
                                                    $renewalCheckOut = \DB::table('t_transactions')
                                                        ->where('room_id', $booking->room_id)
                                                        ->where('user_id', $booking->user_id)
                                                        ->whereRaw('LOWER(transaction_status) IN (?, ?)', ['paid', 'completed'])
                                                        ->where('renewal_status', 0)
                                                        ->whereNull('cancel_at')
                                                        ->orderBy('check_out', 'desc')
                                                        ->value('check_out')
                                                        ?? $booking->check_out;
                                                    $renewalCheckOutStr = \Carbon\Carbon::parse($renewalCheckOut)->format('Y-m-d');
                                                @endphp
                                                <button onclick="openRenewModal({
                                                    orderId: '{{ $booking->order_id }}',
                                                    roomId: {{ $booking->room_id }},
                                                    bookingType: '{{ $booking->booking_type }}',
                                                    months: {{ $booking->booking_months ?? 1 }},
                                                    previousCheckOut: '{{ $renewalCheckOutStr }}',
                                                    originalCheckinDay: {{ $booking->original_checkin_day ?? $booking->check_in->day }},
                                                    userId: {{ $booking->user_id }},
                                                    userName: '{{ addslashes($booking->user_name) }}',
                                                    userPhone: '{{ $booking->user_phone_number }}',
                                                    userEmail: '{{ $booking->user_email }}',
                                                    propertyId: {{ $booking->property_id }},
                                                    propertyName: '{{ addslashes($booking->property_name) }}',
                                                    propertyType: '{{ $booking->property_type }}',
                                                    roomName: '{{ addslashes($booking->room_name) }}'
                                                })"
                                                    class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors duration-200 text-sm font-medium">
                                                    <i class="fas fa-redo mr-2"></i> {{ __('booking.actions.renew_booking') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-calendar-check text-4xl mb-4"></i>
                                                <p class="text-lg">No upcoming bookings</p>
                                                <p class="text-sm mt-2">Your active and future bookings will appear here</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Completed Bookings Tab -->
                    <div x-show="tab === 'completed'">
                        <table class="min-w-full divide-y divide-gray-200 booking-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.booking_id') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.property') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.check_in_out') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.paid_amount') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.status') }}</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('booking.index.table_headers.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $filteredBookings = $allBookings->filter(function($booking) {
                                        $status = strtolower($booking->transaction_status);
                                        if (!in_array($status, ['paid', 'completed'])) return false;
                                        if ($booking->booking && $booking->booking->check_out_at) return true;
                                        if ($booking->renewal_status == 1 && $booking->check_out && \Carbon\Carbon::parse($booking->check_out)->lt(now())) return true;
                                        return false;
                                    });
                                @endphp
                                @forelse ($filteredBookings as $booking)
                                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                                        {{-- Booking ID --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->order_id }}</div>
                                            <div class="text-xs text-gray-500 mt-1">
                                                <div class="flex items-center"><i class="fas fa-user mr-1"></i> {{ $booking->user_name }}</div>
                                                <div class="flex items-center"><i class="fas fa-phone mr-1"></i> {{ $booking->user_phone_number }}</div>
                                            </div>
                                        </td>
                                        {{-- Property: Room No → Type → Property Name --}}
                                        <td class="px-6 py-4">
                                            @if($booking->room?->no)
                                            <div class="text-sm font-semibold text-gray-900">No. {{ $booking->room->no }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500">{{ $booking->room_name }}</div>
                                            <div class="text-xs text-gray-400">{{ $booking->property_name }}</div>
                                        </td>
                                        {{-- Check In/Out --}}
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 flex items-center">
                                                <i class="far fa-calendar-check mr-1 text-teal-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('d M Y') }}
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="far fa-calendar-times mr-1 text-red-500"></i>
                                                {{ \Carbon\Carbon::parse($booking->check_out)->format('d M Y') }}
                                            </div>
                                        </td>
                                        {{-- Paid Amount --}}
                                        <td class="px-6 py-4">
                                            @php
                                                $roomPrice = $booking->room_price ?? 0;
                                                $parkingFee = $booking->parking_fee ?? 0;
                                                $serviceFees = $booking->service_fees ?? 0;
                                                $depositFee = $booking->deposit_fee ?? 0;
                                                $totalStay = $roomPrice + $parkingFee + $serviceFees;
                                            @endphp
                                            <div class="text-sm text-gray-900"><span class="font-medium">{{ __('booking.index.labels.total_stay') }}:</span> Rp {{ number_format($totalStay, 0, ',', '.') }}</div>
                                            <div class="text-xs text-gray-500">{{ __('booking.index.labels.deposit') }}: Rp {{ number_format($depositFee, 0, ',', '.') }}</div>
                                            @if($booking->paid_at)
                                                <div class="text-xs text-green-600 flex items-center mt-1">
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($booking->paid_at)->format('d M Y H:i') }}
                                                </div>
                                            @endif
                                        </td>
                                        {{-- Status + Payment Method Badge --}}
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col items-center gap-1">
                                                @if($booking->renewal_status == 1)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800"><i class="fas fa-redo mr-1.5"></i>  {{ __('booking.index.labels.renewed') }}</span>
                                                @elseif($booking->booking && $booking->booking->check_out_at)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700"><span class="w-1.5 h-1.5 rounded-full bg-purple-500 mr-1.5"></span>{{ __('booking.index.labels.checked_out') }}</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700"><span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>{{ __('booking.index.labels.completed') }}</span>
                                                @endif
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                                    {{ paymentMethodBadge($booking->transaction_type) }}
                                                </span>
                                            </div>
                                        </td>
                                        {{-- Actions --}}
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-gray-400">—</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <i class="fas fa-calendar-times text-4xl mb-4"></i>
                                            <p>{{ __('booking.index.empty_states.no_completed_bookings') }}</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    @include('components.homepage.footer')

    <!-- Renewal Booking Modal -->
    <div id="renewBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('booking.js.renew_modal_title') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('booking.js.renew_modal_subtitle') }}</p>
                        </div>
                        <button onclick="closeRenewModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <form id="renewBookingForm" class="px-6 py-4">
                    <input type="hidden" id="renew_order_id" name="order_id">

                    <!-- Booking Type Selector -->
                    <div id="bookingTypeSelector" class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Booking Type *
                        </label>
                        <div class="flex gap-4">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="booking_type_selector" value="daily" checked
                                       class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500"
                                       onchange="toggleBookingTypeFields()">
                                <span class="ml-2 text-sm text-gray-700">{{ __('booking.js.daily_booking') }}</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="booking_type_selector" value="monthly"
                                       class="w-4 h-4 text-teal-600 border-gray-300 focus:ring-teal-500"
                                       onchange="toggleBookingTypeFields()">
                                <span class="ml-2 text-sm text-gray-700">{{ __('booking.js.monthly_booking') }}</span>
                            </label>
                        </div>
                    </div>

                    <div id="dailyFields" class="space-y-4">
                        <div>
                            <label for="renew_check_in" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('booking.js.new_check_in') }} *
                            </label>
                            <input type="date" id="renew_check_in" name="check_in" required readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Check-in otomatis berdasarkan check-out sebelumnya</p>
                        </div>
                        <div>
                            <label for="renew_check_out" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('booking.js.new_check_out') }} *
                            </label>
                            <input type="date" id="renew_check_out" name="check_out" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500">
                        </div>
                    </div>

                    <div id="monthlyFields" class="space-y-4 hidden">
                        <!-- Hidden check-in input (auto-set from previous check-out) -->
                        <input type="hidden" id="renew_check_in_monthly" name="check_in_monthly">

                        <div>
                            <label for="renew_months" class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah Bulan *
                            </label>
                            <input type="number" id="renew_months" name="months" min="1" value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                                   oninput="updateCheckOutDate()">
                        </div>
                        <div>
                            <label for="renew_check_out_monthly" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('booking.js.new_check_out') }}
                            </label>
                            <input type="date" id="renew_check_out_monthly" name="check_out_monthly" readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100 cursor-not-allowed">
                            <p class="text-xs text-gray-500 mt-1">Dihitung otomatis dari check-out sebelumnya + jumlah bulan</p>
                        </div>
                    </div>

                    {{-- Voucher hidden for renewals --}}
                    {{-- <div class="mt-4">
                        <label for="renew_voucher_code" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('booking.js.voucher_code') }}
                        </label>
                        <input type="text" id="renew_voucher_code" name="voucher_code" maxlength="20"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500"
                               placeholder="Enter voucher code">
                    </div> --}}
                </form>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end space-x-3">
                    <button onclick="closeRenewModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        {{ __('booking.actions.cancel') }}
                    </button>
                    <button onclick="submitRenewal()" id="renewSubmitBtn"
                            class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors">
                        <span id="renewBtnText">{{ __('booking.js.confirm_renew') }}</span>
                        <span id="renewBtnLoader" class="hidden">
                            <i class="fas fa-spinner fa-spin"></i> {{ __('booking.js.processing') }}
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Details Modal -->
    <div id="bookingDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('booking.js.booking_details') }}</h3>
                        <button onclick="closeBookingDetails()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div class="px-6 py-4 overflow-y-auto max-h-[calc(90vh-4rem)]">
                    <div id="modalContent">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <button onclick="closeBookingDetails()" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                        {{ __('booking.actions.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
            
        document.addEventListener('DOMContentLoaded', function() {
            @include('components.homepage.scripts')
            
            // Initialize any global SweetAlert2 defaults if needed
            window.Swal = Swal.mixin({
                customClass: {
                    confirmButton: 'px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500',
                    cancelButton: 'px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 ml-3'
                },
                buttonsStyling: false
            });

            // Function to mark a booking as expired via AJAX
            function markBookingAsExpired(bookingId) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                fetch(`/bookings/${bookingId}/mark-expired`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ _method: 'POST' })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        // Reload the page to reflect the changes
                        window.location.reload();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to update booking status',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'An error occurred while updating the booking status',
                        confirmButtonText: 'OK'
                    });
                });
            }

            // Check for expired bookings on page load
            document.querySelectorAll('.booking-row[data-expired="true"]').forEach(row => {
                const bookingId = row.dataset.bookingId;
                if (bookingId) {
                    markBookingAsExpired(bookingId);
                }
            });

            // Simple ticking timer for pending bookings (similar to your example)
            function updateTimers() {
                document.querySelectorAll('[data-expires-at]').forEach(element => {
                    const expiresAt = new Date(element.dataset.expiresAt);
                    const now = new Date();
                    const remainingMs = expiresAt.getTime() - now.getTime();
                    
                    if (remainingMs <= 0) {
                        // Timer has expired
                        const bookingId = element.dataset.bookingId;
                        const timerText = element.querySelector('.timer-text');
                        if (timerText) {
                            timerText.textContent = element.dataset.initialText + ' (Expired)';
                        }
                        
                        // Update styling to expired state
                        element.classList.remove('bg-red-50', 'text-red-700', 'bg-yellow-50', 'text-yellow-700');
                        element.classList.add('bg-gray-100', 'text-gray-500');
                        const dot = element.querySelector('span:first-child');
                        if (dot) {
                            dot.classList.remove('bg-red-400', 'bg-yellow-400');
                            dot.classList.add('bg-gray-400');
                        }

                        // Mark as expired in backend if not already done (pending or waiting)
                        if (element.dataset.status === 'pending' || element.dataset.status === 'waiting') {
                            markBookingAsExpired(bookingId);
                            element.dataset.status = 'expired';
                        }
                    } else {
                        // Calculate remaining time with zero padding like your example
                        const hours = Math.floor(remainingMs / (1000 * 60 * 60));
                        const minutes = Math.floor((remainingMs % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((remainingMs % (1000 * 60)) / 1000);
                        
                        // Format time with zero padding
                        const h = ("0" + hours).substr(-2);
                        const m = ("0" + minutes).substr(-2);
                        const s = ("0" + seconds).substr(-2);
                        
                        let timeString = '';
                        if (hours > 0) {
                            timeString = `${h}:${m}:${s}`;
                        } else if (minutes > 0) {
                            timeString = `00:${m}:${s}`;
                        } else {
                            timeString = `00:00:${s}`;
                        }
                        
                        const timerText = element.querySelector('.timer-text');
                        if (timerText) {
                            timerText.textContent = element.dataset.initialText + ' (' + timeString + ')';
                        }
                    }
                });
            }

            // Start the ticking timer (like your example)
            updateTimers(); // Initial update
            setInterval(updateTimers, 1000); // Update every second (ticking)

        
    });
    </script>

    <script>
    // Make functions global by declaring them outside DOMContentLoaded
    function showBookingDetails(
        bookingId, orderId, transactionType, userName, userPhone, userEmail,
        propertyName, roomName, propertyType, checkIn, checkOut,
        grandtotalPrice, transactionStatus, virtualAccountNo, paymentBank
    ) {
        const modal = document.getElementById('bookingDetailsModal');
        const modalContent = document.getElementById('modalContent');
        
        // Show modal
        modal.classList.remove('hidden');
        
        // Get status class function
        function getStatusClass(status) {
            const statusClassMap = {
                'pending': 'bg-red-100 text-red-800',
                'waiting': 'bg-yellow-100 text-yellow-800',
                'success': 'bg-green-100 text-green-800',
                'paid': 'bg-green-100 text-green-800',
                'canceled': 'bg-gray-100 text-gray-800',
                'expired': 'bg-gray-200 text-gray-600'
            };
            return statusClassMap[status] || 'bg-gray-100 text-gray-800';
        }
        
        // Format and display booking details
        modalContent.innerHTML = `
            <div class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">${translations.booking_information}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-semibold text-gray-500">{{ __('booking.js.order_id') }}</span>
                            <p class="text-gray-900 font-semibold">${orderId}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">{{ __('booking.js.transaction_type') }}</p>
                            <p class="text-gray-900">${transactionType.toUpperCase()}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">{{ __('booking.js.user_name') }}</p>
                            <p class="text-gray-900">${userName}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">{{ __('booking.js.phone') }}</p>
                            <p class="text-gray-900">${userPhone}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">{{ __('booking.js.email') }}</p>
                            <p class="text-gray-900">${userEmail}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">{{ __('booking.js.status_label') }}</p>
                            <p class="px-3 py-1 rounded-full inline-block ${getStatusClass(transactionStatus)}">${transactionStatus.toUpperCase()}</p>
                        </div>
                    </div>
                    ${virtualAccountNo ? `
                        <div class="mt-3 p-3 bg-blue-50 border border-blue-300 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">${translations.virtual_account_number}</p>
                            <p class="text-lg font-bold text-blue-900 tracking-wider">${virtualAccountNo}</p>
                            ${paymentBank ? `<p class="text-xs text-blue-700 mt-1">Bank ${paymentBank.toUpperCase()}</p>` : ''}
                            <p class="text-xs text-gray-600 mt-2">${translations.transfer_to_va}</p>
                        </div>
                    ` : ''}
                </div>

                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">${translations.property_details}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-500">${translations.property_label}</p>
                            <p class="text-gray-900">${propertyName}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">${translations.room}</p>
                            <p class="text-gray-900">${roomName}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">${translations.type}</p>
                            <p class="text-gray-900">${propertyType}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">${translations.total_price}</p>
                            <p class="text-gray-900 font-semibold">${grandtotalPrice}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">${translations.booking_dates}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-500">${translations.check_in_label}</p>
                            <p class="text-gray-900">${checkIn}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-500">${translations.check_out_label}</p>
                            <p class="text-gray-900">${checkOut}</p>
                        </div>
                    </div>
                </div>
                
            </div>
        `;
    }

    function closeBookingDetails() {
        const modal = document.getElementById('bookingDetailsModal');
        modal.classList.add('hidden');
    }

    // Store room data globally for validation
    let currentRoomData = null;
    // Store original booking data for renewal payload
    let currentBookingData = null;

    // Renewal Modal Functions
    async function openRenewModal(bookingData) {
        const { orderId, roomId, bookingType, months, previousCheckOut, userId, userName, userPhone, userEmail, propertyId, propertyName, propertyType, roomName } = bookingData;

        // Store booking data for submitRenewal
        currentBookingData = {
            orderId,
            userId,
            userName,
            userPhone,
            userEmail,
            propertyId,
            propertyName,
            propertyType,
            roomId,
            roomName,
            bookingType,
            months,
            previousCheckOut,
            originalCheckinDay: bookingData.originalCheckinDay
        };

        // Store originalCheckinDay for updateCheckOutDate() to use
        window._renewOriginalCheckinDay = bookingData.originalCheckinDay || null;

        const modal = document.getElementById('renewBookingModal');

        // Add a small delay before showing loading to avoid flash
        let loadingShown = false;
        const loadingTimeout = setTimeout(() => {
            loadingShown = true;
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching room details',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }, 200);

        try {
            // Fetch room details via API
            // console.log('Fetching room details for roomId:', roomId);
            // console.log('API_KEY:', API_KEY ? 'present' : 'missing');

            const response = await fetch(`/api/v1/rooms/${roomId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'x-api-key': API_KEY,
                }
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('API Error:', errorData);
                throw new Error(errorData.message || 'Failed to fetch room details');
            }

            const data = await response.json();
            const roomData = data.data;

            // Store room data for validation
            currentRoomData = roomData;

            // Clear the loading timeout and close dialog if it was shown
            clearTimeout(loadingTimeout);
            if (loadingShown) {
                // Add small delay before closing to avoid flash
                await new Promise(resolve => setTimeout(resolve, 300));
                Swal.close();
            }

            // Set order ID
            document.getElementById('renew_order_id').value = orderId;

            // Reset form first
            document.getElementById('renewBookingForm').reset();

            // Check which booking types are available
            const hasDailyPrice = roomData.price_original_daily && parseFloat(roomData.price_original_daily) > 0;
            const hasMonthlyPrice = roomData.price_original_monthly && parseFloat(roomData.price_original_monthly) > 0;

            // Get booking type selector elements
            const dailyRadio = document.querySelector('input[name="booking_type_selector"][value="daily"]');
            const monthlyRadio = document.querySelector('input[name="booking_type_selector"][value="monthly"]');
            const dailyLabel = dailyRadio.closest('label');
            const monthlyLabel = monthlyRadio.closest('label');
            const bookingTypeContainer = document.getElementById('bookingTypeSelector');

            // Handle booking type availability
            if (!hasDailyPrice && !hasMonthlyPrice) {
                // No pricing available - show error with slight delay
                await new Promise(resolve => setTimeout(resolve, 300));
                Swal.fire({
                    icon: 'error',
                    title: 'Room Unavailable',
                    text: 'This room has no pricing information available for renewal.',
                    confirmButtonColor: '#0d9488',
                });
                return;
            } else if (hasDailyPrice && !hasMonthlyPrice) {
                // Only daily available
                bookingTypeContainer.classList.add('hidden');
                dailyRadio.checked = true;
                monthlyRadio.disabled = true;
            } else if (!hasDailyPrice && hasMonthlyPrice) {
                // Only monthly available
                bookingTypeContainer.classList.add('hidden');
                monthlyRadio.checked = true;
                dailyRadio.disabled = true;
            } else {
                // Both available - let user choose
                bookingTypeContainer.classList.remove('hidden');
                dailyRadio.disabled = false;
                monthlyRadio.disabled = false;

                // Set default based on original booking type
                if (bookingType === 'monthly') {
                    monthlyRadio.checked = true;
                    document.getElementById('renew_months').value = months;
                } else {
                    dailyRadio.checked = true;
                }
            }

            // Set check-in based on previous booking's check-out date
            // For renewals, check-in = previous check-out (user cannot change)
            console.log('Setting check-in from previousCheckOut:', previousCheckOut);
            document.getElementById('renew_check_in').value = previousCheckOut || '';
            document.getElementById('renew_check_in_monthly').value = previousCheckOut || '';

            // Set minimum date for check-out (must be after check-in)
            document.getElementById('renew_check_out').min = previousCheckOut;

            // Show appropriate fields
            toggleBookingTypeFields();

            // Auto-calculate check-out for monthly
            updateCheckOutDate();

            modal.classList.remove('hidden');
        } catch (error) {
            console.error('Error fetching room details:', error);

            // Clear the loading timeout and close dialog if it was shown
            clearTimeout(loadingTimeout);
            if (loadingShown) {
                Swal.close();
            }

            // Add small delay before showing error
            await new Promise(resolve => setTimeout(resolve, 300));
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to load room details. Please try again.',
                confirmButtonColor: '#0d9488',
            });
            console.error('Full error:', error);
        }
    }

    function toggleBookingTypeFields() {
        const selectedType = document.querySelector('input[name="booking_type_selector"]:checked').value;
        const dailyFields = document.getElementById('dailyFields');
        const monthlyFields = document.getElementById('monthlyFields');

        if (selectedType === 'monthly') {
            // Show monthly fields, hide daily fields
            dailyFields.classList.add('hidden');
            monthlyFields.classList.remove('hidden');

            // Update required attributes
            document.getElementById('renew_check_in').removeAttribute('required');
            document.getElementById('renew_check_out').removeAttribute('required');
            document.getElementById('renew_check_in_monthly').setAttribute('required', 'required');
            document.getElementById('renew_months').setAttribute('required', 'required');
        } else {
            // Show daily fields, hide monthly fields
            dailyFields.classList.remove('hidden');
            monthlyFields.classList.add('hidden');

            // Update required attributes
            document.getElementById('renew_check_in').setAttribute('required', 'required');
            document.getElementById('renew_check_out').setAttribute('required', 'required');
            document.getElementById('renew_check_in_monthly').removeAttribute('required');
            document.getElementById('renew_months').removeAttribute('required');
        }
    }

    function closeRenewModal() {
        const modal = document.getElementById('renewBookingModal');
        modal.classList.add('hidden');
        document.getElementById('renewBookingForm').reset();
    }

    // Clamped month addition: avoids overflow when target month has fewer days.
    // originalDay: use the original check-in day for renewals to preserve days across chain.
    // e.g. Jan 31→Feb 28→Mar 31 (not Mar 28)
    function addMonthsClamped(date, months, originalDay) {
        const d = new Date(date);
        const day = originalDay || d.getDate();
        d.setDate(1);
        d.setMonth(d.getMonth() + months);
        const maxDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
        d.setDate(Math.min(day, maxDay));
        return d;
    }

    function updateCheckOutDate() {
        const checkIn = document.getElementById('renew_check_in_monthly').value;
        const months = parseInt(document.getElementById('renew_months').value) || 1;

        console.log('updateCheckOutDate - checkIn:', checkIn, 'months:', months);

        if (checkIn && checkIn.trim() !== '') {
            const checkInDate = new Date(checkIn);

            // Check if date is valid
            if (isNaN(checkInDate.getTime())) {
                console.error('Invalid check-in date:', checkIn);
                return;
            }

            const coDate = addMonthsClamped(checkInDate, months, window._renewOriginalCheckinDay);

            // Format as YYYY-MM-DD
            const year = coDate.getFullYear();
            const month = String(coDate.getMonth() + 1).padStart(2, '0');
            const day = String(coDate.getDate()).padStart(2, '0');
            const checkOutDate = `${year}-${month}-${day}`;

            document.getElementById('renew_check_out_monthly').value = checkOutDate;
            console.log('updateCheckOutDate - checkOut:', checkOutDate);
        }
    }

    // Auto-update check-out date when check-in or months change for monthly bookings
    document.addEventListener('DOMContentLoaded', function() {
        const checkInMonthly = document.getElementById('renew_check_in_monthly');
        if (checkInMonthly) {
            checkInMonthly.addEventListener('change', updateCheckOutDate);
        }
    });

    async function submitRenewal() {
        const orderId = document.getElementById('renew_order_id').value;
        const bookingType = document.querySelector('input[name="booking_type_selector"]:checked').value;

        let checkIn, checkOut;

        if (bookingType === 'monthly') {
            checkIn = document.getElementById('renew_check_in_monthly').value;
            checkOut = document.getElementById('renew_check_out_monthly').value;

            if (!checkIn) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("booking.js.invalid_dates") }}',
                    text: '{{ __("booking.js.invalid_dates_text") }}',
                    confirmButtonColor: '#0d9488',
                });
                return;
            }

            // Auto-calculate check-out if not set
            if (!checkOut) {
                updateCheckOutDate();
                checkOut = document.getElementById('renew_check_out_monthly').value;
            }
        } else {
            checkIn = document.getElementById('renew_check_in').value;
            checkOut = document.getElementById('renew_check_out').value;

            if (!checkIn || !checkOut) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __("booking.js.invalid_dates") }}',
                    text: '{{ __("booking.js.invalid_dates_text") }}',
                    confirmButtonColor: '#0d9488',
                });
                return;
            }
        }

        // Voucher code disabled for renewals
        const voucherCodeEl = document.getElementById('renew_voucher_code');
        const voucherCode = voucherCodeEl ? voucherCodeEl.value : null;

        // Validate dates
        if (new Date(checkIn) >= new Date(checkOut)) {
            Swal.fire({
                icon: 'error',
                title: '{{ __("booking.js.invalid_dates") }}',
                text: 'Check-out date must be after check-in date.',
                confirmButtonColor: '#0d9488',
            });
            return;
        }

        // Show loading state
        const submitBtn = document.getElementById('renewSubmitBtn');
        const btnText = document.getElementById('renewBtnText');
        const btnLoader = document.getElementById('renewBtnLoader');
        submitBtn.disabled = true;
        btnText.classList.add('hidden');
        btnLoader.classList.remove('hidden');

        try {
            // Get booking type from the selector
            const selectedBookingType = document.querySelector('input[name="booking_type_selector"]:checked').value;
            const isMonthly = selectedBookingType === 'monthly';

            // Calculate booking duration
            let bookingMonths = null;
            let bookingDays = null;

            if (isMonthly) {
                bookingMonths = parseInt(document.getElementById('renew_months').value) || 1;
            } else {
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                bookingDays = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            }

            // Get pricing from currentRoomData
            const dailyPrice = currentRoomData?.price_original_daily || 0;
            const monthlyPrice = currentRoomData?.price_original_monthly || 0;
            const serviceFees = currentRoomData?.service_fees || 30000;
            const adminFees = currentRoomData?.admin_fees || 0;

            // Construct full payload matching renewBooking API requirements
            const payload = {
                // USER INFO
                user_id: currentBookingData.userId,
                user_name: currentBookingData.userName,
                user_phone_number: currentBookingData.userPhone,
                user_email: currentBookingData.userEmail,
                // PROPERTY INFO
                property_id: currentBookingData.propertyId,
                property_name: currentBookingData.propertyName,
                property_type: currentBookingData.propertyType,
                // ROOM INFO
                room_id: currentBookingData.roomId,
                room_name: currentBookingData.roomName,
                // BOOKING TYPE
                booking_type: selectedBookingType,
                check_in: checkIn,
                check_out: checkOut,
                // PRICING
                daily_price: parseFloat(dailyPrice),
                monthly_price: parseFloat(monthlyPrice),
                booking_days: bookingDays,
                booking_months: bookingMonths,
                admin_fees: parseFloat(adminFees),
                service_fees: parseFloat(serviceFees),
                // RENEWAL FLAG
                is_renewal: 1
            };

            if (voucherCode) {
                payload.voucher_code = voucherCode;
            }

            console.log('Renewal payload:', payload);

            const response = await fetch(`/api/v1/booking/${orderId}/renew`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'x-api-key': API_KEY,
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                closeRenewModal();

                // Get the new order ID from the response
                const newOrderId = data.data.new_order_id;

                await Swal.fire({
                    icon: 'success',
                    title: '{{ __("booking.js.renew_success") }}',
                    text: '{{ __("booking.js.renew_success_text") }}',
                    confirmButtonColor: '#0d9488',
                    timer: 2000,
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });

                // Redirect to payment page for the new booking
                window.location.href = `/payment/${newOrderId}`;
            } else {
                let errorMessage = data.message || '{{ __("booking.js.renew_error_text") }}';

                // Handle specific error cases
                if (response.status === 409) {
                    errorMessage = '{{ __("booking.js.room_unavailable_text") }}';
                }

                Swal.fire({
                    icon: 'error',
                    title: '{{ __("booking.js.renew_error") }}',
                    text: errorMessage,
                    confirmButtonColor: '#0d9488',
                });
            }
        } catch (error) {
            console.error('Renewal error:', error);
            Swal.fire({
                icon: 'error',
                title: '{{ __("booking.js.renew_error") }}',
                text: '{{ __("booking.js.renew_error_text") }}',
                confirmButtonColor: '#0d9488',
            });
        } finally {
            // Reset loading state
            submitBtn.disabled = false;
            btnText.classList.remove('hidden');
            btnLoader.classList.add('hidden');
        }
    }
    </script>

    <script>
    /**
     * Cancel booking flow — shows refund preview for paid bookings,
     * simple confirmation for pending/waiting bookings.
     */
    // Translation strings for cancel modal (rendered server-side for current locale)
    const cancelT = {
        title: '{{ __("booking.js.cancel_title") }}',
        confirmText: '{{ __("booking.js.cancel_confirm_text") }}',
        yes: '{{ __("booking.js.cancel_yes") }}',
        no: '{{ __("booking.js.cancel_no") }}',
        processing: '{{ __("booking.js.cancel_processing") }}',
        success: '{{ __("booking.js.cancel_success") }}',
        failed: '{{ __("booking.js.cancel_failed") }}',
        error: '{{ __("booking.js.cancel_error") }}',
        calculating: '{{ __("booking.js.cancel_calculating") }}',
        refundFailed: '{{ __("booking.js.cancel_refund_failed") }}',
        yesRefund: '{{ __("booking.js.cancel_yes_refund") }}',
        processingCancel: '{{ __("booking.js.cancel_processing_cancel") }}',
        bankRequired: '{{ __("booking.js.cancel_bank_required") }}',
        refundRoom: '{{ __("booking.js.refund_room") }}',
        refund{{ __('booking.index.labels.deposit') }}: '{{ __("booking.js.refund_deposit") }}',
        refundParking: '{{ __("booking.js.refund_parking") }}',
        refundTotal: '{{ __("booking.js.refund_total") }}',
        daysBefore: '{{ __("booking.js.refund_days_before") }}',
        days: '{{ __("booking.js.refund_days") }}',
        processTime: '{{ __("booking.js.refund_process_time") }}',
        bankTitle: '{{ __("booking.js.refund_bank_title") }}',
        bankName: '{{ __("booking.js.refund_bank_name") }}',
        accountNo: '{{ __("booking.js.refund_account_no") }}',
        accountHolder: '{{ __("booking.js.refund_account_holder") }}',
    };
    const isDarkMode = document.documentElement.classList.contains('dark');
    const swalDark = isDarkMode ? { background: '#1e293b', color: '#e2e8f0' } : {};
    const refundBoxBg = isDarkMode ? '#064e3b' : '#f0fdf4';
    const refundBoxBorder = isDarkMode ? '#065f46' : '#bbf7d0';
    const bankBoxBg = isDarkMode ? '#78350f' : '#fef3c7';
    const bankBoxBorder = isDarkMode ? '#92400e' : '#fde68a';
    const bankTitleColor = isDarkMode ? '#fbbf24' : '#92400e';
    const labelColor = isDarkMode ? '#94a3b8' : '#6b7280';
    const inputBg = isDarkMode ? '#334155' : '#fff';
    const inputColor = isDarkMode ? '#e2e8f0' : '#111';
    const inputBorder = isDarkMode ? '#475569' : '#d1d5db';

    async function cancelBooking(orderId, status) {
        const apiBase = '{{ rtrim(config("app.url"), "/") }}/api/v1';

        if (status === 'pending' || status === 'waiting') {
            const result = await Swal.fire({
                title: cancelT.title,
                text: `${cancelT.confirmText} ${orderId}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: cancelT.yes,
                cancelButtonText: cancelT.no,
                ...swalDark,
            });
            if (!result.isConfirmed) return;

            try {
                Swal.fire({ title: cancelT.processing, allowOutsideClick: false, ...swalDark, didOpen: () => Swal.showLoading() });
                const res = await fetch(`${apiBase}/booking/${orderId}/cancel`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'x-api-key': API_KEY },
                    body: JSON.stringify({})
                });
                const data = await res.json();
                if (res.ok) {
                    await Swal.fire({ icon: 'success', title: cancelT.success, text: data.message, confirmButtonColor: '#0d9488', ...swalDark });
                    window.location.reload();
                } else {
                    Swal.fire({ icon: 'error', title: cancelT.failed, text: data.message || cancelT.error, confirmButtonColor: '#0d9488', ...swalDark });
                }
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#0d9488', ...swalDark });
            }
            return;
        }

        try {
            Swal.fire({ title: cancelT.calculating, allowOutsideClick: false, ...swalDark, didOpen: () => Swal.showLoading() });

            const previewRes = await fetch(`${apiBase}/booking/${orderId}/cancel-preview`, {
                headers: { 'Accept': 'application/json', 'x-api-key': API_KEY }
            });
            const previewData = await previewRes.json();
            Swal.close();

            if (!previewRes.ok) {
                Swal.fire({ icon: 'error', title: cancelT.failed, text: previewData.message || cancelT.refundFailed, confirmButtonColor: '#0d9488', ...swalDark });
                return;
            }

            const refund = previewData.data.refund;
            const fmt = (n) => 'Rp ' + Number(n).toLocaleString('id-ID');

            let breakdownHtml = `
                <div style="text-align:left; font-size:14px; margin-top:10px;">
                    <div style="background:${refundBoxBg}; border:1px solid ${refundBoxBorder}; border-radius:8px; padding:16px; margin-bottom:12px;">
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span>${cancelT.refundRoom} (${refund.refund_percentage}%)</span>
                            <strong>${fmt(refund.room_refund)}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span>${cancelT.refundDeposit} (100%)</span>
                            <strong>${fmt(refund.deposit_refund)}</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span>${cancelT.refundParking} (${refund.refund_percentage}%)</span>
                            <strong>${fmt(refund.other_refund)}</strong>
                        </div>
                        <hr style="border-color:${refundBoxBorder}; margin:8px 0;">
                        <div style="display:flex; justify-content:space-between; font-size:16px;">
                            <strong>${cancelT.refundTotal}</strong>
                            <strong style="color:#059669;">${fmt(refund.total_refund)}</strong>
                        </div>
                    </div>
                    <p style="color:${labelColor}; font-size:12px;">${cancelT.daysBefore}: ${refund.days_before_checkin} ${cancelT.days}</p>
                    <p style="color:${labelColor}; font-size:12px;">${cancelT.processTime}</p>
            `;

            if (refund.requires_bank_account) {
                breakdownHtml += `
                    <div style="background:${bankBoxBg}; border:1px solid ${bankBoxBorder}; border-radius:8px; padding:12px; margin-top:12px;">
                        <p style="font-weight:600; color:${bankTitleColor}; margin-bottom:8px;">${cancelT.bankTitle}</p>
                        <div style="margin-bottom:8px;">
                            <label style="display:block; font-size:12px; color:${labelColor}; margin-bottom:2px;">${cancelT.bankName}</label>
                            <input type="text" id="refund-bank-name" class="swal2-input" placeholder="BCA, Mandiri, BNI..." style="width:100%; margin:0; font-size:14px; background:${inputBg}; color:${inputColor}; border:1px solid ${inputBorder};">
                        </div>
                        <div style="margin-bottom:8px;">
                            <label style="display:block; font-size:12px; color:${labelColor}; margin-bottom:2px;">${cancelT.accountNo}</label>
                            <input type="text" id="refund-account-no" class="swal2-input" placeholder="1234567890" style="width:100%; margin:0; font-size:14px; background:${inputBg}; color:${inputColor}; border:1px solid ${inputBorder};">
                        </div>
                        <div>
                            <label style="display:block; font-size:12px; color:${labelColor}; margin-bottom:2px;">${cancelT.accountHolder}</label>
                            <input type="text" id="refund-account-holder" class="swal2-input" placeholder="" style="width:100%; margin:0; font-size:14px; background:${inputBg}; color:${inputColor}; border:1px solid ${inputBorder};">
                        </div>
                    </div>
                `;
            }
            breakdownHtml += '</div>';

            const confirmResult = await Swal.fire({
                title: cancelT.title,
                html: breakdownHtml,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: cancelT.yesRefund,
                cancelButtonText: cancelT.no,
                width: '500px',
                ...swalDark,
                preConfirm: () => {
                    if (refund.requires_bank_account) {
                        const bankName = document.getElementById('refund-bank-name').value.trim();
                        const accountNo = document.getElementById('refund-account-no').value.trim();
                        const accountHolder = document.getElementById('refund-account-holder').value.trim();
                        if (!bankName || !accountNo || !accountHolder) {
                            Swal.showValidationMessage(cancelT.bankRequired);
                            return false;
                        }
                        return { bank_name: bankName, account_no: accountNo, account_holder: accountHolder };
                    }
                    return {};
                }
            });

            if (!confirmResult.isConfirmed) return;

            // Execute cancellation
            Swal.fire({ title: cancelT.processingCancel, allowOutsideClick: false, ...swalDark, didOpen: () => Swal.showLoading() });

            const cancelBody = confirmResult.value || {};
            const cancelRes = await fetch(`${apiBase}/booking/${orderId}/cancel`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'x-api-key': API_KEY },
                body: JSON.stringify(cancelBody)
            });
            const cancelData = await cancelRes.json();

            if (cancelRes.ok) {
                const totalRefund = cancelData.data?.refund?.total_refund;
                const successMsg = totalRefund
                    ? '{!! __("booking.js.cancel_refund_success") !!}'.replace(':amount', fmt(totalRefund))
                    : cancelData.message;
                await Swal.fire({
                    icon: 'success',
                    title: cancelT.success,
                    html: successMsg,
                    confirmButtonColor: '#0d9488',
                    ...swalDark,
                });
                window.location.reload();
            } else {
                Swal.fire({ icon: 'error', title: cancelT.failed, text: cancelData.message || cancelT.error, confirmButtonColor: '#0d9488', ...swalDark });
            }

        } catch (e) {
            console.error('Cancel booking error:', e);
            Swal.fire({ icon: 'error', title: 'Error', text: e.message, confirmButtonColor: '#0d9488' });
        }
    }
    </script>

</body>
</html>
