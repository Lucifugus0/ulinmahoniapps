<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.customer_management') }}
                </h1>
                <p class="text-gray-600 mt-2">{{ __('ui.manage_customers_desc') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <button type="button" onclick="openPreRegisterModal()"
                    class="inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    {{ __('ui.pre_register_account') }}
                </button>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('customers.index') }}" id="filterForm"
                class="flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                    <!-- Search Input -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('ui.search') }}
                        </label>
                        <div class="relative">
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="{{ __('ui.search_placeholder_name_email_phone') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Room Number Filter -->
                    <div>
                        <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('ui.room_number') }}
                        </label>
                        <div class="relative">
                            <input type="text" name="room_number" id="room_number" value="{{ request('room_number') }}"
                                placeholder="{{ __('ui.room_number_placeholder') }}"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm
                                focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Property Filter -->
                    <div>
                        <label for="property_id" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('ui.property') }}
                        </label>
                        <select name="property_id" id="property_id"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm
                            focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <option value="">{{ __('ui.all_properties') }}</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->idrec }}"
                                    {{ request('property_id') == $property->idrec ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Booking Status Filter -->
                    <div>
                        <label for="booking_status" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('ui.booking_status') }}
                        </label>
                        <select name="booking_status" id="booking_status"
                            class="w-full px-3.5 py-2.5 border border-gray-300 rounded-lg text-sm
                            focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <option value="" {{ request('booking_status') == '' ? 'selected' : '' }}>{{ __('ui.all_status') }}</option>
                            <option value="checked-in" {{ request('booking_status') == 'checked-in' ? 'selected' : '' }}>{{ __('ui.checked_in') }}</option>
                            <option value="completed" {{ request('booking_status') == 'completed' ? 'selected' : '' }}>{{ __('ui.completed') }}</option>
                            <option value="pending" {{ request('booking_status') == 'pending' ? 'selected' : '' }}>{{ __('ui.upcoming') }}</option>
                            <option value="cancelled" {{ request('booking_status') == 'cancelled' ? 'selected' : '' }}>{{ __('ui.cancelled') }}</option>
                        </select>
                    </div>

                    <!-- Per Page -->
                    <div class="flex items-center justify-end gap-2">
                        <label for="per_page" class="text-sm font-medium text-gray-700 whitespace-nowrap">{{ __('ui.show') }}</label>
                        <select name="per_page" id="per_page"
                            class="w-20 px-2 py-2 border border-gray-300 rounded-lg text-sm
                            focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                            <option value="8" {{ $perPage == 8 ? 'selected' : '' }}>8</option>
                            <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Customer Table -->
        <div id="customerTableContainer"
            class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            @include('pages.customers.partials.customer_table', [
                'customers' => $customers,
                'perPage' => $perPage,
            ])
        </div>
    </div>

    <!-- Customer Detail Modal -->
    <div x-data="customerModal">
        <!-- Modal backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity" x-show="modalOpen"
            x-transition.opacity aria-hidden="true" x-cloak>
        </div>

        <!-- Modal dialog -->
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6" x-show="modalOpen" x-transition
            x-cloak @keydown.escape.window="modalOpen = false">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-4xl max-h-[90vh] overflow-hidden relative z-50"
                @click.outside="modalOpen = false">

                <!-- Header -->
                <div
                    class="px-6 py-5 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white z-10">
                    <h2 class="text-xl font-semibold text-gray-900">
                        <span x-text="customerName"></span> - {{ __('ui.booking_history') }}
                    </h2>
                    <button class="text-gray-500 text-xl hover:text-gray-700 transition-colors"
                        @click="modalOpen = false">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <p class="text-gray-600 mt-2">{{ __('ui.loading_bookings') }}</p>
                    </div>

                    <!-- Bookings List -->
                    <div x-show="!loading && bookings.length > 0" class="space-y-4">
                        <template x-for="booking in bookings" :key="booking.order_id">
                            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-gray-900" x-text="booking.property_name"></h3>
                                        <p class="text-sm text-gray-600">
                                            <span x-text="booking.room_name"></span>
                                            <template x-if="booking.room_number">
                                                <span class="ml-1 px-1.5 py-0.5 rounded bg-gray-100 text-gray-500 text-xs font-mono" x-text="`#${booking.room_number}`"></span>
                                            </template>
                                        </p>
                                    </div>
                                    <div class="flex flex-col gap-1 items-end">
                                        <!-- Transaction Status -->
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-yellow-100 text-yellow-700': booking.transaction_status === 'pending',
                                                'bg-green-100 text-green-700': booking.transaction_status === 'completed',
                                                'bg-blue-100 text-blue-700': booking.transaction_status === 'confirmed',
                                                'bg-red-100 text-red-700': booking.transaction_status === 'cancelled'
                                            }"
                                            x-text="booking.transaction_status">
                                        </span>

                                        <!-- Booking Status Badge -->
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                            :class="{
                                                'bg-blue-100 text-blue-700': booking.booking_status === 'checked-in',
                                                'bg-gray-100 text-gray-700': booking.booking_status === 'checked-out' || booking.booking_status === 'completed',
                                                'bg-yellow-100 text-yellow-700': booking.booking_status === 'confirmed' || booking.booking_status === 'pending',
                                                'bg-red-100 text-red-700': booking.booking_status === 'cancelled'
                                            }">
                                            <span x-show="booking.booking_status === 'checked-in'">🏠 {{ __('ui.currently_checking_in') }}</span>
                                            <span x-show="booking.booking_status === 'checked-out' || booking.booking_status === 'completed'">✓ {{ __('ui.completed') }}</span>
                                            <span x-show="booking.booking_status === 'confirmed' || booking.booking_status === 'pending'">⏳ {{ __('ui.upcoming') }}</span>
                                            <span x-show="booking.booking_status === 'cancelled'">✗ {{ __('ui.cancelled') }}</span>
                                        </span>

                                        <!-- Renewal Status Badge -->
                                        <template x-if="booking.is_renewal == 1">
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                                :class="{
                                                    'bg-green-100 text-green-700': booking.renewal_status == 1,
                                                    'bg-yellow-100 text-yellow-700': booking.renewal_status == 0
                                                }">
                                                <span x-show="booking.renewal_status == 1">✓ {{ __('ui.already_renewed') }}</span>
                                                <span x-show="booking.renewal_status == 0">⏳ {{ __('ui.not_yet_renewed') }}</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.order_id') }}</p>
                                        <p class="font-medium" x-text="booking.order_id"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.transaction_date') }}</p>
                                        <p class="font-medium" x-text="booking.transaction_date"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.check_in') }}</p>
                                        <p class="font-medium" x-text="booking.check_in"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.check_out') }}</p>
                                        <p class="font-medium" x-text="booking.check_out"></p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.duration') }}</p>
                                        <p class="font-medium">
                                            <span x-show="booking.booking_days > 0"
                                                x-text="`${booking.booking_days} day(s)`"></span>
                                            <span x-show="booking.booking_months > 0"
                                                x-text="`${booking.booking_months} month(s)`"></span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('ui.total_price') }}</p>
                                        <p class="font-medium text-green-600">Rp <span
                                                x-text="booking.grandtotal_price"></span></p>
                                    </div>
                                </div>

                                <!-- Parking Info -->
                                <template x-if="booking.parking && booking.parking.length > 0">
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <p class="text-gray-500 text-xs mb-2">{{ __('ui.parking_information') }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="(park, index) in booking.parking" :key="index">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                    </svg>
                                                    <span x-text="park.type"></span>: <strong x-text="park.plate" class="ml-1"></strong>
                                                </span>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!loading && bookings.length === 0" class="text-center py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-600 font-medium">No bookings found</p>
                        <p class="text-gray-500 text-sm mt-1">This customer hasn't made any bookings yet.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)"
                    },
                    stopOnFocus: true,
                }).showToast();
            });
        </script>
    @endif

    <!-- Alpine.js Customer Modal Component -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('customerModal', () => ({
                modalOpen: false,
                customerName: '',
                bookings: [],
                loading: false,
                init() {
                    // Listen for custom event from dynamically loaded content
                    window.addEventListener('open-customer-modal', (event) => {
                        const {
                            identifier,
                            type,
                            name
                        } = event.detail;
                        this.openModal(identifier, type, name);
                    });
                },
                openModal(identifier, type, name) {
                    this.modalOpen = true;
                    this.customerName = name;
                    this.loading = true;
                    this.bookings = [];

                    fetch(`/customers/${identifier}/bookings?type=${type}`)
                        .then(response => response.json())
                        .then(data => {
                            this.customerName = data.customer_name;
                            this.bookings = data.bookings;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.loading = false;
                        });
                }
            }));
        });
    </script>

    <!-- Live Search Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const roomNumberInput = document.getElementById('room_number');
            const propertyFilter = document.getElementById('property_id');
            const bookingStatusFilter = document.getElementById('booking_status');
            const perPageSelect = document.getElementById('per_page');
            let searchTimeout;

            function submitFilter() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    const params = new URLSearchParams({
                        search: searchInput.value,
                        room_number: roomNumberInput.value,
                        property_id: propertyFilter.value,
                        booking_status: bookingStatusFilter.value,
                        per_page: perPageSelect.value,
                    });

                    // Update browser URL so pagination links work correctly
                    history.pushState(null, '', `{{ route('customers.index') }}?${params.toString()}`);

                    fetch(`{{ route('customers.filter') }}?${params.toString()}`)
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('customerTableContainer').innerHTML = html;
                        })
                        .catch(error => console.error('Error:', error));
                }, 500);
            }

            searchInput.addEventListener('input', submitFilter);
            roomNumberInput.addEventListener('input', submitFilter);
            propertyFilter.addEventListener('change', submitFilter);
            bookingStatusFilter.addEventListener('change', submitFilter);
            perPageSelect.addEventListener('change', submitFilter);
        });
    </script>

    <!-- Pre-Register Account Modal -->
    <div id="preRegisterModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closePreRegisterModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden relative">
                <!-- Header -->
                <div
                    class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-indigo-600 to-blue-600">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ __('ui.pre_register_account') }}</h3>
                        <p class="text-white/80 text-sm">{{ __('ui.create_account_desc') }}</p>
                    </div>
                    <button onclick="closePreRegisterModal()"
                        class="text-white hover:text-indigo-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form id="preRegisterForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div>
                                <label for="pre_first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.first_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="first_name" id="pre_first_name" required
                                    placeholder="{{ __('ui.enter_first_name_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_first_name_error"></p>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="pre_last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.last_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="last_name" id="pre_last_name" required
                                    placeholder="{{ __('ui.enter_last_name_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_last_name_error"></p>
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="pre_username" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.username') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="username" id="pre_username" required
                                    placeholder="{{ __('ui.enter_username_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_username_error"></p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="pre_email" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.email') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="pre_email" required
                                    placeholder="{{ __('ui.enter_email_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_email_error"></p>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="pre_phone_number" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.phone_number') }}
                                </label>
                                <input type="tel" name="phone_number" id="pre_phone_number"
                                    placeholder="{{ __('ui.phone_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_phone_number_error"></p>
                            </div>

                            <!-- NIK -->
                            <div>
                                <label for="pre_nik" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.nik') }}
                                </label>
                                <input type="text" name="nik" id="pre_nik"
                                    placeholder="{{ __('ui.nik_placeholder') }}" maxlength="16"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_nik_error"></p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="pre_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.password') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="pre_password" required
                                        placeholder="{{ __('ui.min_6_chars') }}"
                                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 pr-10">
                                    <button type="button" onclick="togglePreRegisterPassword()"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                            id="preRegisterEyeIcon" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-red-500 text-xs mt-1 hidden" id="pre_password_error"></p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closePreRegisterModal()"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                {{ __('ui.cancel') }}
                            </button>
                            <button type="submit" id="preRegisterSubmitBtn"
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span id="preRegisterSubmitText">{{ __('ui.register_account') }}</span>
                                <svg id="preRegisterSpinner" class="animate-spin h-4 w-4 text-white hidden"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="preRegisterSuccessModal" class="fixed inset-0 z-[60] hidden">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closePreRegisterSuccessModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 relative">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('ui.registration_successful') }}</h3>
                    <div id="preRegisterSuccessDetails" class="text-sm text-gray-600 mb-4"></div>
                    <button onclick="closePreRegisterSuccessModal()"
                        class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-colors">
                        {{ __('ui.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Customer Modal -->
    <div id="editCustomerModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm" onclick="closeEditCustomerModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden relative">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-amber-500 to-orange-500">
                    <div>
                        <h3 class="text-lg font-semibold text-white">{{ __('ui.edit_customer') }}</h3>
                        <p class="text-white/80 text-sm">{{ __('ui.update_customer_desc') }}</p>
                    </div>
                    <button onclick="closeEditCustomerModal()" class="text-white hover:text-amber-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form id="editCustomerForm">
                        <input type="hidden" id="edit_customer_id">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div>
                                <label for="edit_first_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.first_name') }}</label>
                                <input type="text" id="edit_first_name" name="first_name" placeholder="{{ __('ui.enter_first_name_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_first_name_error"></p>
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="edit_last_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.last_name') }}</label>
                                <input type="text" id="edit_last_name" name="last_name" placeholder="{{ __('ui.enter_last_name_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_last_name_error"></p>
                            </div>

                            <!-- Username -->
                            <div>
                                <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.username') }}</label>
                                <input type="text" id="edit_username" name="username" placeholder="{{ __('ui.enter_username_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_username_error"></p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.email') }}</label>
                                <input type="email" id="edit_email" name="email" placeholder="{{ __('ui.enter_email_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_email_error"></p>
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="edit_phone_number" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.phone_number') }}</label>
                                <input type="tel" id="edit_phone_number" name="phone_number" placeholder="{{ __('ui.phone_placeholder') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_phone_number_error"></p>
                            </div>

                            <!-- NIK -->
                            <div>
                                <label for="edit_nik" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.nik') }}</label>
                                <input type="text" id="edit_nik" name="nik" placeholder="{{ __('ui.nik_placeholder') }}" maxlength="16"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_nik_error"></p>
                            </div>

                            <!-- Password -->
                            <div class="md:col-span-2">
                                <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.password') }} <span class="text-gray-400 font-normal">({{ __('ui.password_optional') }})</span></label>
                                <input type="password" id="edit_password" name="password" placeholder="{{ __('ui.enter_new_password_6_chars') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:border-amber-500 focus:ring-2 focus:ring-amber-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="edit_password_error"></p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeEditCustomerModal()"
                                class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                {{ __('ui.cancel') }}
                            </button>
                            <button type="submit" id="editCustomerSubmitBtn"
                                class="px-4 py-2.5 bg-amber-500 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span id="editCustomerSubmitText">{{ __('ui.save_changes') }}</span>
                                <svg id="editCustomerSpinner" class="animate-spin h-4 w-4 text-white hidden"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Pre-Register Scripts -->
    <script>
        function openPreRegisterModal() {
            document.getElementById('preRegisterModal').classList.remove('hidden');
            document.getElementById('preRegisterForm').reset();
            clearPreRegisterErrors();
        }

        function closePreRegisterModal() {
            document.getElementById('preRegisterModal').classList.add('hidden');
        }

        function closePreRegisterSuccessModal() {
            document.getElementById('preRegisterSuccessModal').classList.add('hidden');
            // Refresh the customer table
            const params = new URLSearchParams({
                search:         document.getElementById('search').value,
                property_id:    document.getElementById('property_id').value,
                booking_status: document.getElementById('booking_status').value,
                per_page:       document.getElementById('per_page').value,
            });

            fetch(`{{ route('customers.filter') }}?${params.toString()}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('customerTableContainer').innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        }

        function togglePreRegisterPassword() {
            const passwordInput = document.getElementById('pre_password');
            const eyeIcon = document.getElementById('preRegisterEyeIcon');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }

        function clearPreRegisterErrors() {
            const errorElements = document.querySelectorAll('[id^="pre_"][id$="_error"]');
            errorElements.forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });

            const inputs = document.querySelectorAll('#preRegisterForm input');
            inputs.forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        function showPreRegisterError(field, message) {
            const errorEl = document.getElementById('pre_' + field + '_error');
            const inputEl = document.getElementById('pre_' + field);

            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }

            if (inputEl) {
                inputEl.classList.add('border-red-500');
            }
        }

        function setPreRegisterLoading(loading) {
            const submitBtn = document.getElementById('preRegisterSubmitBtn');
            const submitText = document.getElementById('preRegisterSubmitText');
            const spinner = document.getElementById('preRegisterSpinner');

            submitBtn.disabled = loading;
            if (loading) {
                submitText.textContent = '{{ __('ui.registering') }}';
                spinner.classList.remove('hidden');
            } else {
                submitText.textContent = '{{ __('ui.register_account') }}';
                spinner.classList.add('hidden');
            }
        }

        function showPreRegisterSuccess(data) {
            const modal = document.getElementById('preRegisterSuccessModal');
            const details = document.getElementById('preRegisterSuccessDetails');

            if (data && data.user) {
                let nikHtml = data.user.nik ? `<p class="mb-2"><span class="font-medium">NIK:</span> ${data.user.nik}</p>` : '';
                details.innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-4 text-left mb-4">
                        <p class="mb-2"><span class="font-medium">Username:</span> ${data.user.username}</p>
                        <p class="mb-2"><span class="font-medium">Email:</span> ${data.user.email}</p>
                        <p class="mb-2"><span class="font-medium">{{ __('ui.name') }}:</span> ${data.user.first_name} ${data.user.last_name}</p>
                        ${nikHtml}
                        <p><span class="font-medium">{{ __('ui.status') }}:</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                {{ __('ui.active_verified') }}
                            </span>
                        </p>
                    </div>
                    <p class="text-green-600 font-medium">{{ __('ui.account_created_verified') }}</p>
                `;
            } else {
                details.innerHTML = '<p class="text-green-600 font-medium">{{ __('ui.account_created') }}</p>';
            }

            closePreRegisterModal();
            modal.classList.remove('hidden');
        }

        // Form submission
        document.getElementById('preRegisterForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearPreRegisterErrors();
            setPreRegisterLoading(true);

            const formData = new FormData(this);

            try {
                const response = await fetch('{{ route('customers.pre-register') }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    showPreRegisterSuccess(result.data);

                    Toastify({
                        text: result.message || 'Registration successful!',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)"
                        },
                        stopOnFocus: true,
                    }).showToast();
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const messages = Array.isArray(result.errors[field]) ?
                                result.errors[field] :
                                [result.errors[field]];
                            showPreRegisterError(field, messages[0]);
                        });
                    }

                    Toastify({
                        text: result.message || '{{ __('ui.registration_failed') }}',
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff5f6d, #ffc371)"
                        },
                        stopOnFocus: true,
                    }).showToast();
                }
            } catch (error) {
                console.error('Error:', error);

                Toastify({
                    text: '{{ __('ui.unexpected_error') }}',
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff5f6d, #ffc371)"
                    },
                    stopOnFocus: true,
                }).showToast();
            } finally {
                setPreRegisterLoading(false);
            }
        });
    </script>
    <!-- Edit Customer Scripts -->
    <script>
        let editCustomerId = null;

        function openEditCustomerModal(customer) {
            editCustomerId = customer.id;
            document.getElementById('edit_first_name').value   = customer.first_name   || '';
            document.getElementById('edit_last_name').value    = customer.last_name    || '';
            document.getElementById('edit_username').value     = customer.username     || '';
            document.getElementById('edit_email').value        = customer.email        || '';
            document.getElementById('edit_phone_number').value = customer.phone_number || '';
            document.getElementById('edit_nik').value          = customer.nik          || '';
            document.getElementById('edit_password').value     = '';
            clearEditCustomerErrors();
            document.getElementById('editCustomerModal').classList.remove('hidden');
        }

        function closeEditCustomerModal() {
            document.getElementById('editCustomerModal').classList.add('hidden');
            editCustomerId = null;
        }

        function clearEditCustomerErrors() {
            document.querySelectorAll('[id^="edit_"][id$="_error"]').forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });
            document.querySelectorAll('#editCustomerForm input').forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        function showEditCustomerError(field, message) {
            const errorEl = document.getElementById('edit_' + field + '_error');
            const inputEl = document.getElementById('edit_' + field);
            if (errorEl) { errorEl.textContent = message; errorEl.classList.remove('hidden'); }
            if (inputEl) { inputEl.classList.add('border-red-500'); }
        }

        function setEditCustomerLoading(loading) {
            const btn     = document.getElementById('editCustomerSubmitBtn');
            const text    = document.getElementById('editCustomerSubmitText');
            const spinner = document.getElementById('editCustomerSpinner');
            btn.disabled = loading;
            text.textContent = loading ? '{{ __('ui.saving') }}' : '{{ __('ui.save_changes') }}';
            spinner.classList.toggle('hidden', !loading);
        }

        document.getElementById('editCustomerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            if (!editCustomerId) return;

            clearEditCustomerErrors();
            setEditCustomerLoading(true);

            const password = document.getElementById('edit_password').value;
            const data = {
                first_name:   document.getElementById('edit_first_name').value,
                last_name:    document.getElementById('edit_last_name').value,
                username:     document.getElementById('edit_username').value,
                email:        document.getElementById('edit_email').value,
                phone_number: document.getElementById('edit_phone_number').value,
                nik:          document.getElementById('edit_nik').value,
            };
            if (password) data.password = password;

            try {
                const response = await fetch(`/customers/${editCustomerId}/update`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok && result.status === 'success') {
                    closeEditCustomerModal();

                    Toastify({
                        text: result.message || '{{ __('ui.customer_updated') }}',
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: { background: "linear-gradient(to right, #00b09b, #96c93d)" },
                        stopOnFocus: true,
                    }).showToast();

                    // Refresh customer table
                    const params = new URLSearchParams({
                        search:         document.getElementById('search').value,
                        property_id:    document.getElementById('property_id').value,
                        booking_status: document.getElementById('booking_status').value,
                        per_page:       document.getElementById('per_page').value,
                    });
                    fetch(`{{ route('customers.filter') }}?${params.toString()}`)
                        .then(r => r.text())
                        .then(html => { document.getElementById('customerTableContainer').innerHTML = html; });
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const msg = Array.isArray(result.errors[field]) ? result.errors[field][0] : result.errors[field];
                            showEditCustomerError(field, msg);
                        });
                    }
                    Toastify({
                        text: result.message || '{{ __('ui.update_failed') }}',
                        duration: 4000,
                        gravity: "top",
                        position: "right",
                        style: { background: "linear-gradient(to right, #ff5f6d, #ffc371)" },
                        stopOnFocus: true,
                    }).showToast();
                }
            } catch (error) {
                console.error('Error:', error);
                Toastify({
                    text: '{{ __('ui.unexpected_error') }}',
                    duration: 4000,
                    gravity: "top",
                    position: "right",
                    style: { background: "linear-gradient(to right, #ff5f6d, #ffc371)" },
                    stopOnFocus: true,
                }).showToast();
            } finally {
                setEditCustomerLoading(false);
            }
        });
    </script>
</x-app-layout>
