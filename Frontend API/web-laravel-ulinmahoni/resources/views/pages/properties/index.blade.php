<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('properties.index.page_title') }} - Ulin Mahoni</title>
    <meta name="description" content="{{ __('properties.index.page_subtitle') }}. Browse by daily or monthly rates with real-time availability.">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.ico') }}">

    <!-- CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>

    @stack('styles')
    <style>
        /* Consistent room card sizing */
        .room-card {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .room-card .room-image {
            flex-shrink: 0;
            height: 180px;
        }
        .room-card .room-image > div {
            height: 100%;
        }
        .room-card .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .room-card .room-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 120px;
        }
        .room-card .room-price {
            margin-top: auto;
        }
        .room-card h4 {
            min-height: 1.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Dark mode overrides — targets gradients, bg-white, and all bright containers */
        html.dark body {
            background-color: #111827 !important;
            color: #e5e7eb !important;
        }
        html.dark .bg-white {
            background-color: #1f2937 !important;
            color: #e5e7eb !important;
        }
        html.dark .bg-gray-50,
        html.dark .bg-gray-100 {
            background-color: #1f2937 !important;
        }
        html.dark .bg-gray-200 {
            background-color: #374151 !important;
        }
        html.dark .bg-blue-50 {
            background-color: #1e3a5f !important;
        }
        html.dark .text-gray-900 { color: #f9fafb !important; }
        html.dark .text-gray-800 { color: #f3f4f6 !important; }
        html.dark .text-gray-700 { color: #e5e7eb !important; }
        html.dark .text-gray-600 { color: #d1d5db !important; }
        html.dark .text-gray-500 { color: #9ca3af !important; }
        html.dark .text-gray-400 { color: #9ca3af !important; }
        html.dark .border-gray-100 { border-color: #374151 !important; }
        html.dark .border-gray-200 { border-color: #4b5563 !important; }
        html.dark .border-gray-300 { border-color: #4b5563 !important; }
        html.dark input,
        html.dark select,
        html.dark textarea {
            background-color: #374151 !important;
            color: #e5e7eb !important;
            border-color: #4b5563 !important;
        }
        html.dark input::placeholder { color: #9ca3af !important; }
        html.dark .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0,0,0,0.3) !important; }
        html.dark .room-card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #e5e7eb !important;
        }

        /* Gradient containers used in categorical view (JS-rendered) —
           override both background-image (gradient) and background-color */
        html.dark [class*="bg-gradient"] {
            background-image: none !important;
        }
        html.dark [class*="from-blue"] {
            background-color: #1e3a5f !important;
        }
        html.dark [class*="from-green"] {
            background-color: #1a3a2a !important;
        }
        html.dark .border-blue-200 { border-color: #2d4a6f !important; }
        html.dark .border-blue-400 { border-color: #3b82f6 !important; }
        html.dark .border-green-200 { border-color: #2d5a3a !important; }
        html.dark .bg-teal-100 { background-color: rgba(13,148,136,0.2) !important; }
        html.dark .text-teal-800 { color: #5eead4 !important; }
        html.dark .border-teal-400 { border-color: #2dd4bf !important; }

        /* Gender badge — dark mode override */
        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            padding: 0.1rem 0.4rem;
            font-size: 0.875rem;
            font-weight: 700;
            color: #374151;
        }
        /* Gender badge dark mode — semi-transparent bg + brighter text for visibility on dark cards */
        html.dark .gender-badge {
            color: #f3f4f6 !important;
            border-color: #6b7280 !important;
            background-color: rgba(255, 255, 255, 0.12) !important;
        }

        /* Liquid glass — filter/search container panel */
        .bg-white.rounded-xl.shadow-sm.border.border-gray-100 {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--glass-blur-strong);
            -webkit-backdrop-filter: var(--glass-blur-strong);
            border: 1px solid var(--glass-border) !important;
            box-shadow: var(--glass-shadow);
        }
        /* Liquid glass — active filters banner */
        .bg-blue-50.border-l-4.border-blue-400 {
            background: rgba(59, 130, 246, 0.12) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
        }
        /* Liquid glass — room cards rendered by JS */
        .room-card {
            background: var(--glass-bg) !important;
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: 1px solid var(--glass-border) !important;
            box-shadow: var(--glass-shadow);
        }
        .room-card:hover {
            background: var(--glass-bg-hover) !important;
            box-shadow: var(--glass-shadow-hover);
        }
        /* Light mode text brightening — darker grays for glass readability */
        .text-gray-400 { color: #555570 !important; }
        .text-gray-500 { color: #4a4a68 !important; }
        input::placeholder { color: #555570 !important; opacity: 1 !important; }
        html.dark input::placeholder { color: #a0a0b8 !important; }
    </style>
</head>
<body style="background-color: #f8f7f4;">
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="container mx-auto px-4 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2">{{ __('properties.index.page_title') }}</h1>
            <p class="text-gray-600">{{ __('properties.index.page_subtitle') }}</p>

            <!-- Active Filters -->
            <div id="activeFilters" class="hidden mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex flex-wrap items-center gap-2 text-sm">
                    <span class="font-medium text-gray-700">{{ __('properties.index.active_filters') }}</span>
                    <div id="filterBadges" class="flex flex-wrap gap-2"></div>
                    <button onclick="clearAllFilters()" class="ml-auto text-sm text-blue-600 hover:text-blue-800 font-medium flex items-center">
                        <i class="fas fa-times mr-1"></i> {{ __('properties.index.clear_all_filters') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- View Toggle and Filters -->
        <div class="mb-8 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- View Options -->
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium text-gray-700">{{ __('properties.index.view') }}</span>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button type="button" onclick="setView('categorical')" id="viewCategorical" class="px-4 py-1.5 text-sm font-medium rounded transition-colors">
                            <i class="fas fa-th-large mr-1"></i> {{ __('properties.index.categorical_view') }}
                        </button>
                        <button type="button" onclick="setView('grid')" id="viewGrid" class="px-4 py-1.5 text-sm font-medium rounded transition-colors">
                            <i class="fas fa-th mr-1"></i> {{ __('properties.index.grid_view') }}
                        </button>
                        <button type="button" onclick="setView('list')" id="viewList" class="px-4 py-1.5 text-sm font-medium rounded transition-colors">
                            <i class="fas fa-list mr-1"></i> {{ __('properties.index.list_view') }}
                        </button>
                    </div>
                </div>
            </div>

            <form id="searchForm" class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Property Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('properties.index.property_type') }}</label>
                        <div class="relative">
                            <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select name="type" id="filterType" class="w-full pl-10 pr-3 h-11 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 text-sm">
                                <option value="">{{ __('properties.index.all_types') }}</option>
                                <option value="Kos" {{ request('type') == 'Kos' ? 'selected' : '' }}>{{ __('properties.types.kos') }}</option>
                                <option value="Apartment" {{ request('type') == 'Apartment' ? 'selected' : '' }}>{{ __('properties.types.apartment') }}</option>
                                <option value="Villa" {{ request('type') == 'Villa' ? 'selected' : '' }}>{{ __('properties.types.villa') }}</option>
                                <option value="Hotel" {{ request('type') == 'Hotel' ? 'selected' : '' }}>{{ __('properties.types.hotel') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Rent Period (hidden in categorical view) -->
                    <div id="periodFilterContainer">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('properties.index.rental_period') }}</label>
                        <div class="relative">
                            <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select name="period" id="filterPeriod" class="w-full pl-10 pr-3 h-11 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 text-sm">
                                <option value="monthly" {{ request('period') == 'monthly' || !request('period') ? 'selected' : '' }}>{{ __('properties.index.monthly') }}</option>
                                <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>{{ __('properties.index.daily') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Check-in Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('properties.index.check_in') }}</label>
                        <div class="relative">
                            <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="date"
                                name="check_in"
                                id="filterCheckIn"
                                value="{{ request('check_in') }}"
                                min="{{ now()->format('Y-m-d') }}"
                                class="w-full pl-10 pr-3 h-11 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 text-sm">
                        </div>
                    </div>

                    <!-- Check-out Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ __('properties.index.check_out') }}</label>
                        <div class="relative">
                            <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input
                                type="date"
                                name="check_out"
                                id="filterCheckOut"
                                value="{{ request('check_out') }}"
                                min="{{ request('check_in') ?? now()->format('Y-m-d') }}"
                                class="w-full pl-10 pr-3 h-11 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200 text-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-5 flex justify-between items-center">
                    <button
                        type="button"
                        onclick="clearAllFilters()"
                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                        {{ __('properties.index.reset') }}
                    </button>
                    <button
                        type="submit"
                        class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        {{ __('properties.index.search_rooms') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading Indicator -->
        <div id="loadingIndicator" class="hidden text-center py-16">
            <div class="inline-block animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-teal-600"></div>
            <p class="mt-4 text-gray-600">{{ __('properties.index.searching') }}</p>
        </div>

        <!-- Results Container -->
        <div id="resultsContainer"></div>

        <!-- Pagination Container -->
        <div id="paginationContainer" class="mt-10"></div>
    </main>

    @include('components.homepage.footer')

    <!-- Back to Top Button -->
    <button id="backToTop" class="fixed bottom-6 right-6 w-12 h-12 bg-teal-600 hover:bg-teal-700 text-white rounded-full shadow-lg flex items-center justify-center opacity-0 invisible transition-all duration-300 z-50">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts -->
    <script>
        // Translation strings for JavaScript
        const translations = {
            no_rooms_available: '{{ __("properties.js.no_rooms_available") }}',
            no_rooms_for_dates: '{{ __("properties.js.no_rooms_for_dates") }}',
            no_rooms_match_criteria: '{{ __("properties.js.no_rooms_match_criteria") }}',
            reset_filter: '{{ __("properties.js.reset_filter") }}',
            bed: '{{ __("properties.js.bed") }}',
            view_details: '{{ __("properties.js.view_details") }}',
            per_day: '{{ __("properties.js.per_day") }}',
            per_month: '{{ __("properties.js.per_month") }}',
            daily: '{{ __("properties.js.daily") }}',
            monthly: '{{ __("properties.js.monthly") }}',
            daily_rental: '{{ __("properties.index.daily_rental") }}',
            monthly_rental: '{{ __("properties.index.monthly_rental") }}',
            properties_available: '{{ __("properties.index.properties_available") }}',
            rooms_available: '{{ __("properties.index.rooms_available") }}',
            start_from: '{{ __("properties.index.start_from") }}',
            error_occurred: '{{ __("properties.index.error_occurred") }}',
            try_again: '{{ __("properties.index.try_again") }}',
            available: '{{ __("properties.index.available") }}',
            unavailable: '{{ __("properties.status.unavailable") }}',
            status_available: '{{ __("properties.status.available") }}',
        };

        // Global variables
        let currentPage = 1;
        let currentFilters = {};
        let currentView = 'categorical'; // categorical, grid, or list
        let allRoomsData = {}; // Store data for categorical view
        const apiUrl = '{{ route("api.search.rooms") }}';
        const apiKey = '{{ env("API_KEY") }}';
        const adminUrl = '{{ env("ADMIN_URL") }}';

        // Fallback images as data URIs
        const propertyPlaceholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="100" height="100"%3E%3Crect width="100" height="100" fill="%23f3f4f6"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="12" fill="%239ca3af"%3ENo Image%3C/text%3E%3C/svg%3E';
        const roomPlaceholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="300"%3E%3Crect width="400" height="300" fill="%23f3f4f6"/%3E%3Ctext x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-family="sans-serif" font-size="16" fill="%239ca3af"%3ENo Image%3C/text%3E%3C/svg%3E';

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize filters from URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            currentFilters = {
                type: urlParams.get('type') || '{{ request("type") }}' || '',
                period: urlParams.get('period') || '{{ request("period") }}' || 'monthly',
                check_in: urlParams.get('check_in') || '{{ request("check_in") }}' || '',
                check_out: urlParams.get('check_out') || '{{ request("check_out") }}' || ''
            };

            // Set form values
            if (currentFilters.type) document.getElementById('filterType').value = currentFilters.type;
            if (currentFilters.period) document.getElementById('filterPeriod').value = currentFilters.period;
            if (currentFilters.check_in) document.getElementById('filterCheckIn').value = currentFilters.check_in;
            if (currentFilters.check_out) document.getElementById('filterCheckOut').value = currentFilters.check_out;

            // Initialize view
            updateViewButtons();

            // Load initial results
            if (currentView === 'categorical') {
                searchRoomsCategorical();
            } else {
                searchRooms();
            }

            // Back to Top Button
            const backToTopBtn = document.getElementById('backToTop');
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTopBtn.classList.remove('opacity-0', 'invisible');
                    backToTopBtn.classList.add('opacity-100', 'visible');
                } else {
                    backToTopBtn.classList.remove('opacity-100', 'visible');
                    backToTopBtn.classList.add('opacity-0', 'invisible');
                }
            });

            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Set minimum checkout date based on check-in date
            const checkInInput = document.getElementById('filterCheckIn');
            const checkOutInput = document.getElementById('filterCheckOut');

            if (checkInInput && checkOutInput) {
                checkInInput.addEventListener('change', function() {
                    if (this.value) {
                        checkOutInput.min = this.value;
                        if (checkOutInput.value && new Date(checkOutInput.value) < new Date(this.value)) {
                            checkOutInput.value = this.value;
                        }
                    }
                });
            }

            // Handle form submission
            document.getElementById('searchForm').addEventListener('submit', function(e) {
                e.preventDefault();
                currentPage = 1;
                currentFilters = {
                    type: document.getElementById('filterType').value,
                    period: document.getElementById('filterPeriod').value,
                    check_in: document.getElementById('filterCheckIn').value,
                    check_out: document.getElementById('filterCheckOut').value
                };
                updateURL();

                if (currentView === 'categorical') {
                    searchRoomsCategorical();
                } else {
                    searchRooms();
                }
            });
        });

        // Set view function
        function setView(view) {
            currentView = view;
            updateViewButtons();

            // Show/hide period filter based on view
            const periodContainer = document.getElementById('periodFilterContainer');
            if (view === 'categorical') {
                periodContainer.style.display = 'none';
                searchRoomsCategorical();
            } else {
                periodContainer.style.display = 'block';
                searchRooms();
            }
        }

        // Update view buttons
        function updateViewButtons() {
            const buttons = {
                categorical: document.getElementById('viewCategorical'),
                grid: document.getElementById('viewGrid'),
                list: document.getElementById('viewList')
            };

            Object.keys(buttons).forEach(key => {
                if (key === currentView) {
                    buttons[key].classList.add('bg-white', 'text-teal-600', 'shadow-sm');
                    buttons[key].classList.remove('text-gray-600');
                } else {
                    buttons[key].classList.remove('bg-white', 'text-teal-600', 'shadow-sm');
                    buttons[key].classList.add('text-gray-600');
                }
            });

            // Show/hide period filter
            const periodContainer = document.getElementById('periodFilterContainer');
            if (currentView === 'categorical') {
                periodContainer.style.display = 'none';
            } else {
                periodContainer.style.display = 'block';
            }
        }

        // Search rooms via API
        async function searchRooms(page = 1) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const resultsContainer = document.getElementById('resultsContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            // Show loading
            loadingIndicator.classList.remove('hidden');
            resultsContainer.innerHTML = '';
            paginationContainer.innerHTML = '';

            try {
                // Build query parameters
                const params = new URLSearchParams();
                if (currentFilters.type) params.append('type', currentFilters.type);
                if (currentFilters.period) params.append('period', currentFilters.period);
                if (currentFilters.check_in) params.append('check_in', currentFilters.check_in);
                if (currentFilters.check_out) params.append('check_out', currentFilters.check_out);
                params.append('page', page);
                params.append('per_page', 12);

                const response = await fetch(`${apiUrl}?${params.toString()}`, {
                    method: 'GET',
                    headers: {
                        'X-API-KEY': apiKey,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (!response.ok || data.status === 'error') {
                    throw new Error(data.message || 'Failed to fetch rooms');
                }

                // Hide loading
                loadingIndicator.classList.add('hidden');

                // Update active filters display
                updateActiveFilters();

                // Render results
                if (data.data && data.data.length > 0) {
                    renderResults(data.data);
                    renderPagination(data.meta);
                } else {
                    renderNoResults();
                }

            } catch (error) {
                console.error('Error fetching rooms:', error);
                loadingIndicator.classList.add('hidden');
                resultsContainer.innerHTML = `
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="max-w-md mx-auto">
                            <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-700 mb-2">${translations.error_occurred}</h3>
                            <p class="text-gray-500 mb-6">${error.message}</p>
                            <button onclick="searchRooms()" class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-sync-alt mr-2"></i> ${translations.try_again}
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // Search rooms categorical (both daily and monthly)
        async function searchRoomsCategorical() {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const resultsContainer = document.getElementById('resultsContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            loadingIndicator.classList.remove('hidden');
            resultsContainer.innerHTML = '';
            paginationContainer.innerHTML = '';

            try {
                // Fetch both daily and monthly rooms
                const [dailyData, monthlyData] = await Promise.all([
                    fetchRoomsByPeriod('daily'),
                    fetchRoomsByPeriod('monthly')
                ]);

                loadingIndicator.classList.add('hidden');
                updateActiveFilters();

                // Render categorical view
                renderCategoricalView(dailyData, monthlyData);

            } catch (error) {
                console.error('Error fetching rooms:', error);
                loadingIndicator.classList.add('hidden');
                resultsContainer.innerHTML = `
                    <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="max-w-md mx-auto">
                            <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                            <h3 class="text-xl font-medium text-gray-700 mb-2">${translations.error_occurred}</h3>
                            <p class="text-gray-500 mb-6">${error.message}</p>
                            <button onclick="searchRoomsCategorical()" class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                                <i class="fas fa-sync-alt mr-2"></i> ${translations.try_again}
                            </button>
                        </div>
                    </div>
                `;
            }
        }

        // Fetch rooms by period
        async function fetchRoomsByPeriod(period) {
            const params = new URLSearchParams();
            if (currentFilters.type) params.append('type', currentFilters.type);
            params.append('period', period);
            if (currentFilters.check_in) params.append('check_in', currentFilters.check_in);
            if (currentFilters.check_out) params.append('check_out', currentFilters.check_out);
            params.append('per_page', 100); // Get all for categorical view

            const response = await fetch(`${apiUrl}?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-API-KEY': apiKey,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();
            if (!response.ok || data.status === 'error') {
                throw new Error(data.message || 'Failed to fetch rooms');
            }

            return data.data || [];
        }

        // Render categorical view
        function renderCategoricalView(dailyProperties, monthlyProperties) {
            const resultsContainer = document.getElementById('resultsContainer');
            let html = '<div class="space-y-8">';

            // Daily Section
            if (dailyProperties && dailyProperties.length > 0) {
                html += `
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-day text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">${translations.daily_rental}</h2>
                                <p class="text-sm text-gray-600">${dailyProperties.length} ${translations.properties_available}</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            ${dailyProperties.map(property => renderPropertyCard(property, 'daily')).join('')}
                        </div>
                    </div>
                `;
            }

            // Monthly Section
            if (monthlyProperties && monthlyProperties.length > 0) {
                html += `
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-white text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">${translations.monthly_rental}</h2>
                                <p class="text-sm text-gray-600">${monthlyProperties.length} ${translations.properties_available}</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            ${monthlyProperties.map(property => renderPropertyCard(property, 'monthly')).join('')}
                        </div>
                    </div>
                `;
            }

            // No results
            if ((!dailyProperties || dailyProperties.length === 0) && (!monthlyProperties || monthlyProperties.length === 0)) {
                html += renderNoResults();
            }

            html += '</div>';
            resultsContainer.innerHTML = html;
        }

        // Render property card (for categorical view)
        function renderPropertyCard(property, period) {
            const periodLabel = period === 'daily' ? 'hari' : 'bulan';
            const propertyRoute = getPropertyRoute(property);
            const thumbnail = getPropertyThumbnail(property);

            return `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <!-- Name + type badge -->
                                <span class="bg-teal-100 text-teal-800 text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 border border-teal-400 inline-block mb-2">${property.tags}</span>
                                <div class="flex flex-wrap items-center gap-2 mb-1">
                                    <a href="${propertyRoute}" class="text-xl font-bold text-gray-800 hover:text-teal-600 transition-colors truncate">
                                        ${property.name}
                                    </a>
                                </div>
                                <!-- Gender badge -->
                                ${property.gender ? `<div class="mb-2">${getGenderBadge(property.gender)}</div>` : ''}
                                <!-- Location -->
                                <div class="flex items-start text-gray-600 text-sm mb-3">
                                    <i class="fas fa-map-marker-alt mr-2 mt-0.5 flex-shrink-0"></i>
                                    <span>${property.address}</span>
                                </div>
                                <!-- Rooms + price -->
                                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-door-open"></i>
                                        <span>${property.available_rooms.filter(r => r.status === 1 && (r.is_available !== undefined ? r.is_available : r.rental_status !== 1)).length} kamar tersedia</span>
                                    </span>
                                    <!-- Daily Multi Tier Pricing: show total price at property level when dates selected -->
                                    ${property.lowest_total_price ? `
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-tag"></i>
                                            <span>Mulai dari <strong class="text-teal-600">Rp ${formatRupiah(property.lowest_total_price)}</strong> / ${property.available_rooms[0]?.total_days || ''} malam</span>
                                        </span>
                                    ` : property.lowest_price ? `
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-tag"></i>
                                            <span>Mulai dari <strong class="text-teal-600">Rp ${formatRupiah(property.lowest_price)}</strong>/${periodLabel}</span>
                                        </span>
                                    ` : ''}
                                </div>
                            </div>
                            ${thumbnail ? `
                                <a href="${propertyRoute}" class="hidden md:block flex-shrink-0">
                                    <img src="${adminUrl}/storage/${thumbnail}"
                                        alt="${property.name}"
                                        class="w-24 h-24 object-cover rounded-lg"
                                        onerror="this.onerror=null; this.src=propertyPlaceholder;">
                                </a>
                            ` : ''}
                        </div>
                    </div>
                    <div class="p-5 bg-gray-50">
                        ${(() => {
                            const availableRooms = property.available_rooms.filter(room => room.status === 1 && (room.is_available !== undefined ? room.is_available : room.rental_status !== 1));
                            if (availableRooms.length === 0) {
                                return `<p class="text-center text-gray-500 py-3 text-sm">{{ __('properties.room.no_rooms') }}</p>`;
                            }
                            return `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${availableRooms.map(room => renderRoomCard(room, property)).join('')}</div>`;
                        })()}
                    </div>
                </div>
            `;
        }

        // Render results (grid or list view)
        function renderResults(properties) {
            const resultsContainer = document.getElementById('resultsContainer');
            const periodLabel = currentFilters.period === 'daily' ? 'hari' : 'bulan';

            if (currentView === 'list') {
                renderListView(properties);
                return;
            }

            let html = '<div class="space-y-8">';

            properties.forEach(property => {
                const propertyRoute = getPropertyRoute(property);
                const thumbnail = getPropertyThumbnail(property);

                html += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Property Header -->
                        <div class="p-5 border-b border-gray-100">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Property Info -->
                                <div class="flex-1 min-w-0">
                                    <!-- Name + type badge -->
                                    <span class="bg-teal-100 text-teal-800 text-xs font-bold px-2.5 py-1 rounded-full flex-shrink-0 border border-teal-400 inline-block mb-2">${property.tags}</span>
                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                        <a href="${propertyRoute}" class="text-xl font-bold text-gray-800 hover:text-teal-600 transition-colors truncate">
                                            ${property.name}
                                        </a>
                                    </div>
                                    <!-- Gender badge -->
                                    ${property.gender ? `<div class="mb-2">${getGenderBadge(property.gender)}</div>` : ''}
                                    <!-- Location -->
                                    <div class="flex items-start text-gray-600 text-sm mb-3">
                                        <i class="fas fa-map-marker-alt mr-2 mt-0.5 flex-shrink-0"></i>
                                        <span>${property.address}</span>
                                    </div>
                                    <!-- Rooms + price -->
                                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-door-open"></i>
                                            <span>${property.available_rooms.filter(r => r.status === 1 && (r.is_available !== undefined ? r.is_available : r.rental_status !== 1)).length} ${translations.rooms_available}</span>
                                        </span>
                                        <!-- Daily Multi Tier Pricing: show total price when dates selected -->
                                        ${property.lowest_total_price ? `
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-tag"></i>
                                                <span>${translations.start_from} <strong class="text-teal-600">Rp ${formatRupiah(property.lowest_total_price)}</strong> / ${property.available_rooms[0]?.total_days || ''} malam</span>
                                            </span>
                                        ` : property.lowest_price ? `
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-tag"></i>
                                                <span>${translations.start_from} <strong class="text-teal-600">Rp ${formatRupiah(property.lowest_price)}</strong>/${periodLabel}</span>
                                            </span>
                                        ` : ''}
                                    </div>
                                </div>

                                <!-- Property Image -->
                                ${thumbnail ? `
                                    <a href="${propertyRoute}" class="hidden md:block flex-shrink-0">
                                        <img src="${adminUrl}/storage/${thumbnail}"
                                            alt="${property.name}"
                                            class="w-24 h-24 object-cover rounded-lg"
                                            onerror="this.onerror=null; this.src=propertyPlaceholder;">
                                    </a>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Available Rooms Grid -->
                        <div class="p-5 bg-gray-50">
                            ${(() => {
                                const availableRooms = property.available_rooms.filter(room => room.status === 1 && (room.is_available !== undefined ? room.is_available : room.rental_status !== 1));
                                if (availableRooms.length === 0) {
                                    return `<p class="text-center text-gray-500 py-3 text-sm">{{ __('properties.room.no_rooms') }}</p>`;
                                }
                                return `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${availableRooms.map(room => renderRoomCard(room, property)).join('')}</div>`;
                            })()}
                        </div>
                    </div>
                `;
            });

            html += '</div>';
            resultsContainer.innerHTML = html;
        }

        // Render list view
        function renderListView(properties) {
            const resultsContainer = document.getElementById('resultsContainer');
            const periodLabel = currentFilters.period === 'daily' ? 'hari' : 'bulan';

            let html = '<div class="space-y-4">';

            properties.forEach(property => {
                const propertyRoute = getPropertyRoute(property);
                const thumbnail = getPropertyThumbnail(property);

                const availableRooms = property.available_rooms.filter(room => room.status === 1 && (room.is_available !== undefined ? room.is_available : room.rental_status !== 1));
                availableRooms.forEach(room => {
                    const roomRoute = `/rooms/${room.slug || room.id}`;
                    const roomThumbnail = getRoomThumbnail(room, property);

                    html += `
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-300">
                            <div class="flex flex-col md:flex-row">
                                <!-- Room Image -->
                                <a href="${roomRoute}" class="room-link md:w-48 flex-shrink-0">
                                    <div class="relative aspect-[4/3] md:aspect-square bg-gray-100">
                                        ${roomThumbnail ? `
                                            <img src="${adminUrl}/storage/${roomThumbnail}"
                                                alt="${room.name}"
                                                class="w-full h-full object-cover"
                                                onerror="this.onerror=null; this.src=roomPlaceholder;">
                                        ` : `
                                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                                <i class="fas fa-door-open text-4xl"></i>
                                            </div>
                                        `}
                                        ${currentFilters.check_in && currentFilters.check_out ? `
                                            <span class="absolute top-3 right-3 bg-green-500 text-white text-xs px-3 py-1.5 rounded-full font-medium shadow-md">
                                                <i class="fas fa-check mr-1"></i>Tersedia
                                            </span>
                                        ` : ''}

                                        <!-- Availability Badge -->
                                        ${room.status === 1 && (room.is_available !== undefined ? room.is_available : room.rental_status !== 1) ? `
                                            <span class="absolute bottom-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                                                ${translations.status_available}
                                            </span>
                                        ` : `
                                            <span class="absolute bottom-3 left-3 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-sm">
                                                ${translations.unavailable}
                                            </span>
                                        `}
                                    </div>
                                </a>

                                <!-- Room Info -->
                                <div class="flex-1 p-5">
                                    <div class="flex flex-col h-full">
                                        <!-- Property Badge -->
                                        <div class="flex items-center gap-2 mb-2">
                                            <a href="${propertyRoute}" class="text-sm text-teal-600 hover:text-teal-700 font-medium">
                                                <i class="fas fa-building mr-1"></i>${property.name}
                                            </a>
                                            <span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded">
                                                ${property.tags}
                                            </span>
                                        </div>

                                        <!-- Room Name -->
                                        <a href="${roomRoute}" class="room-link">
                                            <h3 class="text-xl font-bold text-gray-800 mb-2 hover:text-teal-600 transition-colors">
                                                ${room.name}
                                            </h3>
                                        </a>

                                        <!-- Location -->
                                        <div class="flex items-center text-gray-600 text-sm mb-3">
                                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                            <span>${property.address}</span>
                                        </div>

                                        <!-- Room Details -->
                                        <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                                            ${room.no ? `
                                                <span><i class="fas fa-hashtag mr-1"></i>${room.no}</span>
                                            ` : ''}
                                            ${room.bed_count ? `
                                                <span><i class="fas fa-bed mr-1"></i>${room.bed_count} ${translations.bed}</span>
                                            ` : ''}
                                            ${room.room_size ? `
                                                <span><i class="fas fa-expand-arrows-alt mr-1"></i>${room.room_size}m²</span>
                                            ` : ''}
                                        </div>

                                        <!-- Price and Action: Daily Multi Tier Pricing — show total when dates selected -->
                                        <div class="mt-auto flex items-center justify-between pt-4 border-t border-gray-100">
                                            <div>
                                                ${room.total_price && room.total_days ? `
                                                    <div class="text-2xl font-bold text-teal-600">
                                                        Rp ${formatRupiah(room.total_price)}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        / ${room.total_days} malam
                                                    </div>
                                                ` : `
                                                    <div class="text-2xl font-bold text-teal-600">
                                                        Rp ${formatRupiah(room.current_price)}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        per ${periodLabel}
                                                    </div>
                                                `}
                                            </div>
                                            <a href="${roomRoute}" class="room-link bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                                ${translations.view_details}
                                                <i class="fas fa-arrow-right ml-2 text-sm"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });

            html += '</div>';
            resultsContainer.innerHTML = html;
        }

        // Render room card
        function renderRoomCard(room, property) {
            const roomRoute = `/rooms/${room.slug || room.id}`;
            const roomThumbnail = getRoomThumbnail(room, property);
            const periodLabel = room.current_period === 'daily' ? 'hari' : 'bulan';

            return `
                <div class="room-card bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border border-gray-200">
                    <!-- Room Image -->
                    <a href="${roomRoute}" class="room-link room-image block overflow-hidden">
                        <div class="relative aspect-[4/3] bg-gray-100">
                            ${roomThumbnail ? `
                                <img src="${adminUrl}/storage/${roomThumbnail}"
                                    alt="${room.name}"
                                    class="hover:scale-105 transition-transform duration-500"
                                    onerror="this.onerror=null; this.src=roomPlaceholder;">
                            ` : `
                                <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                    <i class="fas fa-door-open text-3xl"></i>
                                </div>
                            `}

                            ${currentFilters.check_in && currentFilters.check_out ? `
                                <span class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full font-medium shadow-md">
                                    <i class="fas fa-check mr-1"></i>Tersedia
                                </span>
                            ` : ''}

                            <!-- Availability Badge -->
                            ${room.status === 1 && (room.is_available !== undefined ? room.is_available : room.rental_status !== 1) ? `
                                <span class="absolute bottom-2 left-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                                    ${translations.status_available}
                                </span>
                            ` : `
                                <span class="absolute bottom-2 left-2 inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-500 text-white shadow-sm">
                                    ${translations.unavailable}
                                </span>
                            `}
                        </div>
                    </a>

                    <!-- Room Info -->
                    <div class="room-content p-3">
                        <a href="${roomRoute}" class="room-link">
                            <h4 class="font-semibold text-gray-800 mb-1 line-clamp-1 hover:text-teal-600 transition-colors">
                                ${room.name}
                            </h4>
                        </a>

                        <div class="flex items-center gap-3 text-xs text-gray-500 mb-2 min-h-[1.25rem]">
                            ${room.no ? `<span><i class="fas fa-hashtag mr-1"></i>${room.no}</span>` : ''}
                            ${room.bed_count ? `<span><i class="fas fa-bed mr-1"></i>${room.bed_count}</span>` : ''}
                            ${room.room_size ? `<span><i class="fas fa-expand-arrows-alt mr-1"></i>${room.room_size}m²</span>` : ''}
                        </div>

                        <!-- Price: Daily Multi Tier Pricing — show total when dates selected, else per-night rate -->
                        <div class="room-price flex items-center justify-between pt-2 border-t border-gray-100">
                            <div>
                                ${room.total_price && room.total_days ? `
                                    <div class="text-lg font-bold text-teal-600">
                                        Rp ${formatRupiah(room.total_price)}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        / ${room.total_days} malam
                                    </div>
                                ` : `
                                    <div class="text-lg font-bold text-teal-600">
                                        Rp ${formatRupiah(room.current_price)}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        /${periodLabel}
                                    </div>
                                `}
                            </div>
                            <a href="${roomRoute}" class="room-link text-teal-600 hover:text-teal-700 text-sm font-medium">
                                ${translations.view_details} <i class="fas fa-arrow-right text-xs ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            `;
        }

        // Render no results
        function renderNoResults() {
            const resultsContainer = document.getElementById('resultsContainer');
            const hasDateFilter = currentFilters.check_in && currentFilters.check_out;

            resultsContainer.innerHTML = `
                <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="max-w-md mx-auto">
                        <div class="w-20 h-20 mx-auto mb-4 text-gray-300">
                            <i class="fas fa-search text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">${translations.no_rooms_available}</h3>
                        <p class="text-gray-500 mb-6">
                            ${hasDateFilter
                                ? translations.no_rooms_for_dates
                                : translations.no_rooms_match_criteria}
                        </p>
                        <button onclick="clearAllFilters()" class="inline-flex items-center px-5 py-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>
                            ${translations.reset_filter}
                        </button>
                    </div>
                </div>
            `;
        }

        // Render pagination
        function renderPagination(meta) {
            if (meta.last_page <= 1) return;

            const paginationContainer = document.getElementById('paginationContainer');
            let html = '<div class="flex justify-center items-center gap-2">';

            // Previous button
            html += `
                <button
                    onclick="changePage(${meta.current_page - 1})"
                    ${meta.current_page === 1 ? 'disabled' : ''}
                    class="px-4 py-2 border rounded-lg ${meta.current_page === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 2 && i <= meta.current_page + 2)) {
                    html += `
                        <button
                            onclick="changePage(${i})"
                            class="px-4 py-2 border rounded-lg ${i === meta.current_page ? 'bg-teal-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                            ${i}
                        </button>
                    `;
                } else if (i === meta.current_page - 3 || i === meta.current_page + 3) {
                    html += '<span class="px-2">...</span>';
                }
            }

            // Next button
            html += `
                <button
                    onclick="changePage(${meta.current_page + 1})"
                    ${meta.current_page === meta.last_page ? 'disabled' : ''}
                    class="px-4 py-2 border rounded-lg ${meta.current_page === meta.last_page ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            html += '</div>';
            paginationContainer.innerHTML = html;
        }

        // Change page
        function changePage(page) {
            currentPage = page;
            searchRooms(page);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Update active filters display
        function updateActiveFilters() {
            const activeFiltersDiv = document.getElementById('activeFilters');
            const filterBadges = document.getElementById('filterBadges');

            const hasFilters = currentFilters.type || currentFilters.check_in || currentFilters.check_out;

            if (!hasFilters) {
                activeFiltersDiv.classList.add('hidden');
                return;
            }

            activeFiltersDiv.classList.remove('hidden');
            let badges = '';

            if (currentFilters.type) {
                badges += `
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center">
                        ${currentFilters.type}
                        <button onclick="removeFilter('type')" class="ml-1.5 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `;
            }

            if (currentFilters.period && currentFilters.period !== 'monthly') {
                badges += `
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center">
                        ${currentFilters.period === 'daily' ? translations.daily : translations.monthly}
                        <button onclick="removeFilter('period')" class="ml-1.5 text-green-600 hover:text-green-800">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `;
            }

            if (currentFilters.check_in && currentFilters.check_out) {
                badges += `
                    <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1.5 rounded-full flex items-center">
                        <i class="far fa-calendar-alt mr-1"></i>
                        ${formatDate(currentFilters.check_in)} - ${formatDate(currentFilters.check_out)}
                        <button onclick="removeFilter('dates')" class="ml-1.5 text-purple-600 hover:text-purple-800">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>
                `;
            }

            filterBadges.innerHTML = badges;
        }

        // Remove filter
        function removeFilter(filterType) {
            if (filterType === 'type') {
                currentFilters.type = '';
                document.getElementById('filterType').value = '';
            } else if (filterType === 'period') {
                currentFilters.period = 'monthly';
                document.getElementById('filterPeriod').value = 'monthly';
            } else if (filterType === 'dates') {
                currentFilters.check_in = '';
                currentFilters.check_out = '';
                document.getElementById('filterCheckIn').value = '';
                document.getElementById('filterCheckOut').value = '';
            }

            currentPage = 1;
            updateURL();
            searchRooms();
        }

        // Clear all filters
        function clearAllFilters() {
            currentFilters = {
                type: '',
                period: 'monthly',
                check_in: '',
                check_out: ''
            };

            document.getElementById('filterType').value = '';
            document.getElementById('filterPeriod').value = 'monthly';
            document.getElementById('filterCheckIn').value = '';
            document.getElementById('filterCheckOut').value = '';

            currentPage = 1;
            updateURL();
            searchRooms();
        }

        // Update URL
        function updateURL() {
            const params = new URLSearchParams();
            if (currentFilters.type) params.set('type', currentFilters.type);
            if (currentFilters.period) params.set('period', currentFilters.period);
            if (currentFilters.check_in) params.set('check_in', currentFilters.check_in);
            if (currentFilters.check_out) params.set('check_out', currentFilters.check_out);

            const newUrl = params.toString() ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
            window.history.pushState({}, '', newUrl);
        }

        function getGenderBadge(gender) {
            if (!gender) return '';
            const key = gender.toLowerCase();
            const labels = {
                'male':   '{{ __("properties.gender.male") }}',
                'female': '{{ __("properties.gender.female") }}',
                'mixed':  '{{ __("properties.gender.mixed") }}'
            };
            const icons = {
                'male':   '<i class="fas fa-mars" style="color:#3b82f6"></i>',
                'female': '<i class="fas fa-venus" style="color:#ef4444"></i>',
                'mixed':  '<i class="fas fa-venus-mars" style="background:linear-gradient(to right,#ef4444,#3b82f6);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text"></i>'
            };
            const label = labels[key] || gender;
            const icon  = icons[key]  || '';
            return `<div class="gender-badge">${icon}<span>${label}</span></div>`;
        }

        // Helper functions
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function getPropertyRoute(property) {
            const routes = {
                'Kos': `/houses/${property.id}`,
                'Apartment': `/apartments/${property.slug || property.id}`,
                'Villa': `/villas/${property.slug || property.id}`,
                'Hotel': `/hotels/${property.slug || property.id}`
            };
            return routes[property.tags] || `/properties/${property.slug || property.id}`;
        }

        function getPropertyThumbnail(property) {
            if (property.images && property.images.length > 0) {
                for (let image of property.images) {
                    if (image.image) return image.image;
                }
            }
            return property.image || null;
        }

        function getRoomThumbnail(room, property) {
            if (room.images && room.images.length > 0) {
                for (let image of room.images) {
                    if (image.image) return image.image;
                }
            }
            if (room.image) return room.image;
            return getPropertyThumbnail(property);
        }

        // Helper functions
        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        }

        function getPropertyRoute(property) {
            const routes = {
                'Kos': `/houses/${property.id}`,
                'Apartment': `/apartments/${property.slug || property.id}`,
                'Villa': `/villas/${property.slug || property.id}`,
                'Hotel': `/hotels/${property.slug || property.id}`
            };
            return routes[property.tags] || `/properties/${property.slug || property.id}`;
        }

        function getPropertyThumbnail(property) {
            if (property.images && property.images.length > 0) {
                for (let image of property.images) {
                    if (image.image) return image.image;
                }
            }
            return property.image || null;
        }

        function getRoomThumbnail(room, property) {
            if (room.images && room.images.length > 0) {
                for (let image of room.images) {
                    if (image.image) return image.image;
                }
            }
            if (room.image) return room.image;
            return getPropertyThumbnail(property);
        }
    </script>

    @stack('scripts')
</body>
</html>
