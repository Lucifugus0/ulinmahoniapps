<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="{
        activeTab: 'waiting-check-in',
        currentPage: 1
    }">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                    {{ __('ui.rented_rooms_report') }}
                </h1>
                <p class="text-gray-600 mt-1">{{ __('ui.rented_rooms_report_desc') }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <button onclick="printReport()"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    {{ __('ui.print') }}
                </button>
                <button onclick="exportReport()"
                    class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('ui.export_excel') }}
                </button>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mb-6 border-b border-gray-200">
            <div class="flex gap-1">
                <button @click="activeTab = 'waiting-check-in'; window.activeTab = 'waiting-check-in'; currentPage = 1; fetchReportData()"
                    :class="activeTab === 'waiting-check-in'
                        ? 'border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50'
                        : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-4 py-3 font-medium text-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ __('ui.waiting_check_in') }}</span>
                    </div>
                </button>

                <button @click="activeTab = 'checked-in'; window.activeTab = 'checked-in'; currentPage = 1; fetchReportData()"
                    :class="activeTab === 'checked-in'
                        ? 'border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50'
                        : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-4 py-3 font-medium text-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ __('ui.checked_in') }}</span>
                    </div>
                </button>

                <button @click="activeTab = 'rooms-occupied'; window.activeTab = 'rooms-occupied'; currentPage = 1; fetchReportData()"
                    :class="activeTab === 'rooms-occupied'
                        ? 'border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50'
                        : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-4 py-3 font-medium text-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>{{ __('ui.occupied_rooms') }}</span>
                    </div>
                </button>

                <button @click="activeTab = 'check-out'; window.activeTab = 'check-out'; currentPage = 1; fetchReportData()"
                    :class="activeTab === 'check-out'
                        ? 'border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50'
                        : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-4 py-3 font-medium text-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>{{ __('ui.checked_out') }}</span>
                    </div>
                </button>

                <button @click="activeTab = 'cancelled'; window.activeTab = 'cancelled'; currentPage = 1; fetchReportData()"
                    :class="activeTab === 'cancelled'
                        ? 'border-b-2 border-indigo-500 text-indigo-600 bg-indigo-50'
                        : 'text-gray-600 hover:text-indigo-600 hover:bg-gray-50'"
                    class="px-4 py-3 font-medium text-sm transition-all duration-200">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>{{ __('ui.canceled') }}</span>
                    </div>
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <form id="filterForm" class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <!-- Search -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.search') }}</label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-9"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.search_booking_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <!-- Single Date Picker (for checked-in, waiting-check-in, check-out) -->
                    <div class="lg:col-span-2" x-show="activeTab !== 'cancelled'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.date') }}</label>
                        <div class="relative">
                            <input type="text" id="single_date_picker" placeholder="{{ __('ui.select_date') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <input type="hidden" id="single_date" name="single_date" value="{{ $selectedDate }}">
                        </div>
                    </div>

                    <!-- Date Range Picker (for cancelled) -->
                    <div class="lg:col-span-2" x-show="activeTab === 'cancelled'" x-cloak>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.period') }}</label>
                        <div class="relative">
                            <input type="text" id="date_range_picker" placeholder="{{ __('ui.select_date_range') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                            <input type="hidden" id="start_date" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>

                    <!-- Property Filter (only for super admin and HO roles) -->
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isHORole())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.property') }}</label>
                        <select id="property_id" name="property_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">{{ __('ui.all_properties') }}</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->idrec }}"
                                    {{ $propertyId == $property->idrec ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <!-- Per Page -->
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                        <select name="per_page" id="per_page"
                            class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.property_and_room') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.tenant_name') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.booking_type') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.period') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.duration') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.room_price') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.service_fee') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.grand_total') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.payment_status') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.payment_date') }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.order_id') }}</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ __('ui.loading_report_data') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
                <!-- Pagination will be inserted here -->
            </div>
        </div>
    </div>

    <script>
        // Translations object for JavaScript
        const translations = {
            loading_data: '{{ __('ui.loading_data') }}',
            failed_load_data: '{{ __('ui.failed_load_data') }}',
            no_booking_data: '{{ __('ui.no_booking_data') }}',
            showing: '{{ __('ui.showing') }}',
            showing_page: '{{ __('ui.showing_page') }}',
            of_pages: '{{ __('ui.of_pages') }}',
            total_data: '{{ __('ui.total_data') }}',
            previous: '{{ __('ui.previous') }}',
            next: '{{ __('ui.next') }}',
            report_waiting_checkin: '{{ __('ui.report_waiting_checkin') }}',
            report_checked_in: '{{ __('ui.report_checked_in') }}',
            report_rooms_occupied: '{{ __('ui.report_rooms_occupied') }}',
            report_check_out: '{{ __('ui.report_check_out') }}',
            report_cancelled: '{{ __('ui.report_cancelled') }}',
            property: '{{ __('ui.property') }}',
            period: '{{ __('ui.period') }}',
            date: '{{ __('ui.date') }}',
            search: '{{ __('ui.search') }}',
            printed_on: '{{ __('ui.printed_on') }}',
            applied_filters: '{{ __('ui.applied_filters') }}',
            property_and_room: '{{ __('ui.property_and_room') }}',
            tenant_name: '{{ __('ui.tenant_name') }}',
            booking_type: '{{ __('ui.booking_type') }}',
            period_col: '{{ __('ui.period') }}',
            duration: '{{ __('ui.duration') }}',
            room_price: '{{ __('ui.room_price') }}',
            service_fee: '{{ __('ui.service_fee') }}',
            grand_total: '{{ __('ui.grand_total') }}',
            payment_status: '{{ __('ui.payment_status') }}',
            payment_date: '{{ __('ui.payment_date') }}',
            order_id: '{{ __('ui.order_id') }}',
            exporting_booking_report: '{{ __('ui.exporting_booking_report') }}'
        };

        let currentPage = 1;
        let searchTimeout;
        let singleDatePicker;
        let rangeDatePicker;
        window.activeTab = 'waiting-check-in'; // Global variable to track active tab

        document.addEventListener('DOMContentLoaded', function() {
            const selectedDateVal = '{{ $selectedDate }}';
            const startDateVal = '{{ $startDate }}';
            const endDateVal = '{{ $endDate }}';

            // Initialize Flatpickr for single date selection (no default - show all data)
            const singleDateOptions = {
                mode: 'single',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                onChange: function(selectedDates, dateStr, instance) {
                    document.getElementById('single_date').value = dateStr;
                    currentPage = 1;
                    fetchReportData();
                }
            };
            if (selectedDateVal) {
                singleDateOptions.defaultDate = new Date(selectedDateVal);
            }
            singleDatePicker = flatpickr('#single_date_picker', singleDateOptions);

            // Initialize Flatpickr for date range selection (no default - show all data)
            const rangeDateOptions = {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        document.getElementById('start_date').value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        document.getElementById('end_date').value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                        currentPage = 1;
                        fetchReportData();
                    }
                }
            };
            if (startDateVal && endDateVal) {
                rangeDateOptions.defaultDate = [new Date(startDateVal), new Date(endDateVal)];
            }
            rangeDatePicker = flatpickr('#date_range_picker', rangeDateOptions);

            // Auto-load data on page load
            fetchReportData();

            // Listen for select filter changes (instant)
            ['property_id', 'per_page'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', function() {
                        currentPage = 1;
                        fetchReportData();
                    });
                }
            });

            // Search input with debounce (300ms delay)
            document.getElementById('search').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    fetchReportData();
                }, 300);
            });

            // Search on Enter key (immediate)
            document.getElementById('search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchTimeout);
                    currentPage = 1;
                    fetchReportData();
                }
            });
        });

        function fetchReportData(page = 1) {
            currentPage = page;

            const formData = new FormData();
            formData.append('report_type', window.activeTab);
            formData.append('property_id', document.getElementById('property_id')?.value || '');
            formData.append('search', document.getElementById('search').value);
            formData.append('per_page', document.getElementById('per_page').value);
            formData.append('page', page);

            // Add date params based on active tab
            if (window.activeTab === 'cancelled') {
                formData.append('start_date', document.getElementById('start_date').value);
                formData.append('end_date', document.getElementById('end_date').value);
            } else {
                formData.append('selected_date', document.getElementById('single_date').value);
            }

            const tbody = document.getElementById('reportTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex justify-center items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            ${translations.loading_data}
                        </div>
                    </td>
                </tr>
            `;

            fetch('{{ route('reports.rented-rooms.data') }}?' + new URLSearchParams(Object.fromEntries(formData)))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderTable(data.data);
                        renderPagination(data.pagination);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="12" class="px-4 py-8 text-center text-red-500">
                                ${translations.failed_load_data}
                            </td>
                        </tr>
                    `;
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('reportTableBody');

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>${translations.no_booking_data}</span>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">${row.no}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">
                        <div class="font-medium">${row.property}</div>
                        <div class="text-gray-600">${row.room_number}</div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-900">${row.tenant_name}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getBookingTypeClass(row.booking_type)}">
                            ${row.booking_type}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">
                        <div class="text-xs">
                            <div><span class="font-medium">In:</span> ${row.check_in}</div>
                            <div><span class="font-medium">Out:</span> ${row.check_out}</div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">${row.duration}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${row.room_price}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${row.service_fee}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">${row.grand_total}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(row.payment_status)}">
                            ${row.payment_status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700">${row.payment_date}</td>
                    <td class="px-4 py-3 text-sm font-medium text-blue-600">${row.order_id}</td>
                </tr>
            `).join('');
        }

        function getBookingTypeClass(type) {
            const typeClasses = {
                'Daily': 'bg-blue-100 text-blue-800',
                'Monthly': 'bg-purple-100 text-purple-800',
            };
            return typeClasses[type] || 'bg-gray-100 text-gray-800';
        }

        function getStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'waiting': 'bg-orange-100 text-orange-800',
                'paid': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800',
                'expired': 'bg-gray-100 text-gray-800'
            };
            return statusClasses[status] || 'bg-gray-100 text-gray-800';
        }

        function renderPagination(pagination) {
            const container = document.getElementById('paginationContainer');

            if (pagination.last_page <= 1) {
                container.innerHTML = `
                    <div class="text-sm text-gray-700">
                        {{ __('ui.showing') }} ${pagination.total} {{ __('ui.entries') }}
                    </div>
                `;
                return;
            }

            let paginationHTML = `
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ __('ui.showing') }} ${pagination.current_page} {{ __('ui.of') }} ${pagination.last_page} (${pagination.total} {{ __('ui.entries') }})
                    </div>
                    <div class="flex gap-2">
            `;

            // Previous button
            if (pagination.current_page > 1) {
                paginationHTML += `
                    <button onclick="fetchReportData(${pagination.current_page - 1})"
                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                        {{ __('ui.previous') }}
                    </button>
                `;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (
                    i === 1 ||
                    i === pagination.last_page ||
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    const activeClass = i === pagination.current_page ?
                        'bg-green-600 text-white' :
                        'border border-gray-300 hover:bg-gray-50';
                    paginationHTML += `
                        <button onclick="fetchReportData(${i})"
                            class="px-3 py-1 rounded-md text-sm ${activeClass}">
                            ${i}
                        </button>
                    `;
                } else if (
                    i === pagination.current_page - 3 ||
                    i === pagination.current_page + 3
                ) {
                    paginationHTML += `<span class="px-2 py-1 text-gray-500">...</span>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                paginationHTML += `
                    <button onclick="fetchReportData(${pagination.current_page + 1})"
                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                        {{ __('ui.next') }}
                    </button>
                `;
            }

            paginationHTML += `
                    </div>
                </div>
            `;

            container.innerHTML = paginationHTML;
        }

        function printReport() {
            // Get report title based on active tab
            const reportTitles = {
                'waiting-check-in': translations.report_waiting_checkin,
                'checked-in': translations.report_checked_in,
                'rooms-occupied': translations.report_rooms_occupied,
                'check-out': translations.report_check_out,
                'cancelled': translations.report_cancelled
            };
            const reportTitle = reportTitles[window.activeTab] || translations.report_waiting_checkin;

            // Get filter info
            let filterInfo = '';
            const propertySelect = document.getElementById('property_id');
            if (propertySelect) {
                const selectedProperty = propertySelect.options[propertySelect.selectedIndex].text;
                if (propertySelect.value) {
                    filterInfo += `<p><strong>${translations.property}:</strong> ${selectedProperty}</p>`;
                }
            }

            // Add date info based on tab
            if (window.activeTab === 'cancelled') {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                if (startDate && endDate) {
                    filterInfo += `<p><strong>${translations.period}:</strong> ${formatDate(startDate)} - ${formatDate(endDate)}</p>`;
                }
            } else if (window.activeTab === 'check-out') {
                const selectedDate = document.getElementById('single_date').value;
                if (selectedDate) {
                    filterInfo += `<p><strong>${translations.date}:</strong> ${formatDate(selectedDate)}</p>`;
                }
            }

            const searchValue = document.getElementById('search').value;
            if (searchValue) {
                filterInfo += `<p><strong>${translations.search}:</strong> ${searchValue}</p>`;
            }

            // Get table content
            const tableContent = document.getElementById('reportTableBody').innerHTML;

            // Create print window
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #333;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                            border-bottom: 2px solid #4F46E5;
                            padding-bottom: 10px;
                        }
                        .header h1 {
                            margin: 0;
                            color: #4F46E5;
                            font-size: 24px;
                        }
                        .header p {
                            margin: 5px 0;
                            color: #666;
                        }
                        .filter-info {
                            background: #F3F4F6;
                            padding: 15px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                        }
                        .filter-info p {
                            margin: 5px 0;
                            font-size: 14px;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th {
                            background-color: #4F46E5;
                            color: white;
                            padding: 12px 8px;
                            text-align: left;
                            font-size: 11px;
                            font-weight: 600;
                            text-transform: uppercase;
                            border: 1px solid #4338CA;
                        }
                        td {
                            padding: 10px 8px;
                            border: 1px solid #E5E7EB;
                            font-size: 12px;
                        }
                        tr:nth-child(even) {
                            background-color: #F9FAFB;
                        }
                        tr:hover {
                            background-color: #F3F4F6;
                        }
                        .badge {
                            padding: 4px 8px;
                            border-radius: 9999px;
                            font-size: 11px;
                            font-weight: 500;
                            display: inline-block;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 12px;
                            color: #666;
                            border-top: 1px solid #E5E7EB;
                            padding-top: 15px;
                        }
                        @media print {
                            body {
                                margin: 0;
                                padding: 15px;
                            }
                            .filter-info {
                                background: #F9FAFB !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            th {
                                background-color: #4F46E5 !important;
                                color: white !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            tr:nth-child(even) {
                                background-color: #F9FAFB !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${reportTitle}</h1>
                        <p>${translations.printed_on}: ${new Date().toLocaleString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                    ${filterInfo ? '<div class="filter-info"><strong>' + translations.applied_filters + ':</strong>' + filterInfo + '</div>' : ''}
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>${translations.property_and_room}</th>
                                <th>${translations.tenant_name}</th>
                                <th>${translations.booking_type}</th>
                                <th>${translations.period_col}</th>
                                <th>${translations.duration}</th>
                                <th>${translations.room_price}</th>
                                <th>${translations.service_fee}</th>
                                <th>${translations.grand_total}</th>
                                <th>${translations.payment_status}</th>
                                <th>${translations.payment_date}</th>
                                <th>${translations.order_id}</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableContent}
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Booking Management System - Laporan Booking</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();

            // Wait for content to load then print
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
            };
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }

        function exportReport() {
            const params = new URLSearchParams({
                report_type: window.activeTab,
                property_id: document.getElementById('property_id')?.value || '',
                search: document.getElementById('search').value
            });

            // Add date based on tab
            if (window.activeTab === 'cancelled') {
                params.append('start_date', document.getElementById('start_date').value);
                params.append('end_date', document.getElementById('end_date').value);
            } else {
                params.append('selected_date', document.getElementById('single_date').value);
            }

            window.location.href = '{{ route('reports.rented-rooms.export') }}?' + params.toString();

            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: translations.exporting_booking_report,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    </script>
</x-app-layout>
