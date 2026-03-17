<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.booking_report') }}
                </h1>
                <p class="text-gray-600 mt-1">{{ __('ui.booking_report') }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <button onclick="printReport()"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Print
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

        <!-- Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <form id="filterForm" class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <!-- Search -->
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.search') }}</label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-9"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="No. Booking, Nama, Kamar..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Date Range -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.select_date_range') }}</label>
                        <div class="relative">
                            <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}" data-input
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <input type="hidden" id="start_date" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>

                    <!-- Property Filter (only for super admin and HO roles) -->
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isHORole())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.property') }}</label>
                        <select id="property_id" name="property_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.status') }}</label>
                        <select id="status" name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('ui.all_status') }}</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>{{ __('ui.pending_payment') }}
                            </option>
                            <option value="waiting" {{ $status == 'waiting' ? 'selected' : '' }}>{{ __('ui.waiting_confirmation') }}
                            </option>
                            <option value="waiting-check-in" {{ $status == 'waiting-check-in' ? 'selected' : '' }}>
                                {{ __('ui.ready_for_checkin') }}</option>
                            <option value="checked-in" {{ $status == 'checked-in' ? 'selected' : '' }}>{{ __('ui.checked_in') }}
                            </option>
                            <option value="checked-out" {{ $status == 'checked-out' ? 'selected' : '' }}>{{ __('ui.checked_out') }}
                            </option>
                            <option value="canceled" {{ $status == 'canceled' ? 'selected' : '' }}>{{ __('ui.canceled') }}</option>
                            <option value="expired" {{ $status == 'expired' ? 'selected' : '' }}>{{ __('ui.expired') }}</option>
                        </select>
                    </div>
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
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Booking Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Booking Number</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Room</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Stay Period</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Payment Date & Time</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Payment Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Notes</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Loading report data...</span>
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
        let currentPage = 1;
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Flatpickr without localStorage persistence (show all data by default)
            flatpickr('#date_picker', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                allowInput: true,
                locale: { rangeSeparator: ' to ' },
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1] || selectedDates[0];
                        document.getElementById('start_date').value = instance.formatDate(startDate, 'Y-m-d');
                        document.getElementById('end_date').value = instance.formatDate(endDate, 'Y-m-d');
                    }
                    currentPage = 1;
                    fetchReportData();
                },
                onClose: function(selectedDates) {
                    if (selectedDates.length === 0) {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        currentPage = 1;
                        fetchReportData();
                    }
                }
            });

            // Auto-load data on page load
            fetchReportData();

            // Listen for select filter changes (instant)
            ['status', 'property_id', 'per_page'].forEach(id => {
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
            formData.append('start_date', document.getElementById('start_date').value);
            formData.append('end_date', document.getElementById('end_date').value);
            formData.append('status', document.getElementById('status').value);
            formData.append('property_id', document.getElementById('property_id')?.value || '');
            formData.append('search', document.getElementById('search').value);
            formData.append('per_page', document.getElementById('per_page').value);
            formData.append('page', page);

            const tbody = document.getElementById('reportTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex justify-center items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading data...
                        </div>
                    </td>
                </tr>
            `;

            fetch('{{ route('reports.booking.data') }}?' + new URLSearchParams(Object.fromEntries(formData)))
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
                            <td colspan="10" class="px-4 py-8 text-center text-red-500">
                                {{ __('ui.error_loading') }}
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
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span>No booking records found</span>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => {
                const stayPeriod = row.check_in && row.check_out ? `${row.check_in} to ${row.check_out}` : '-';

                let addressDisplay = '-';
                if (row.property_name && row.property_name !== '-' && row.address && row.address !== '-') {
                    addressDisplay = `<div class="text-gray-900">${row.property_name}</div><div class="text-xs text-gray-500">${row.address}</div>`;
                } else if (row.property_name && row.property_name !== '-') {
                    addressDisplay = row.property_name;
                } else if (row.address && row.address !== '-') {
                    addressDisplay = row.address;
                }

                return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">${row.booking_date}</td>
                    <td class="px-4 py-3 text-sm font-medium text-blue-600">${row.booking_number}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${row.name}</td>
                    <td class="px-4 py-3 text-sm">${addressDisplay}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${row.room}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${stayPeriod}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${row.payment_datetime || '-'}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${row.payment_type || '-'}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(row.raw_status)}">
                            ${row.status}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600">${row.notes || '-'}</td>
                </tr>
            `}).join('');
        }

        function getStatusClass(status) {
            const statusClasses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'waiting': 'bg-orange-100 text-orange-800',
                'paid': 'bg-green-100 text-green-800',
                'canceled': 'bg-red-100 text-red-800',
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
            const reportTitle = 'Laporan Pemesanan';

            // Get filter info
            let filterInfo = '';

            // Date range
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (startDate && endDate) {
                filterInfo += `<p><strong>Periode:</strong> ${formatDate(startDate)} - ${formatDate(endDate)}</p>`;
            }

            // Property filter
            const propertySelect = document.getElementById('property_id');
            if (propertySelect) {
                const selectedProperty = propertySelect.options[propertySelect.selectedIndex].text;
                if (propertySelect.value) {
                    filterInfo += `<p><strong>Alamat:</strong> ${selectedProperty}</p>`;
                }
            }

            // Status filter
            const statusSelect = document.getElementById('status');
            if (statusSelect && statusSelect.value) {
                const selectedStatus = statusSelect.options[statusSelect.selectedIndex].text;
                filterInfo += `<p><strong>Status:</strong> ${selectedStatus}</p>`;
            }

            // Search filter
            const searchValue = document.getElementById('search').value;
            if (searchValue) {
                filterInfo += `<p><strong>Pencarian:</strong> ${searchValue}</p>`;
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
                            border-bottom: 2px solid #2563EB;
                            padding-bottom: 10px;
                        }
                        .header h1 {
                            margin: 0;
                            color: #2563EB;
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
                            font-size: 10px;
                        }
                        th {
                            background-color: #2563EB;
                            color: white;
                            padding: 10px 6px;
                            text-align: left;
                            font-size: 9px;
                            font-weight: 600;
                            text-transform: uppercase;
                            border: 1px solid #1E40AF;
                        }
                        td {
                            padding: 8px 6px;
                            border: 1px solid #E5E7EB;
                            font-size: 10px;
                        }
                        tr:nth-child(even) {
                            background-color: #F9FAFB;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 12px;
                            color: #666;
                            border-top: 1px solid #E5E7EB;
                            padding-top: 15px;
                        }
                        /* Badge styling */
                        .px-2 {
                            padding-left: 0.5rem;
                            padding-right: 0.5rem;
                        }
                        .py-1 {
                            padding-top: 0.25rem;
                            padding-bottom: 0.25rem;
                        }
                        .text-xs {
                            font-size: 0.75rem;
                            line-height: 1rem;
                        }
                        .font-medium {
                            font-weight: 500;
                        }
                        .rounded-full {
                            border-radius: 9999px;
                        }
                        .bg-yellow-100 { background-color: #FEF3C7; }
                        .text-yellow-800 { color: #92400E; }
                        .bg-orange-100 { background-color: #FFEDD5; }
                        .text-orange-800 { color: #9A3412; }
                        .bg-green-100 { background-color: #D1FAE5; }
                        .text-green-800 { color: #065F46; }
                        .bg-red-100 { background-color: #FEE2E2; }
                        .text-red-800 { color: #991B1B; }
                        .bg-gray-100 { background-color: #F3F4F6; }
                        .text-gray-800 { color: #1F2937; }
                        @media print {
                            body {
                                margin: 0;
                                padding: 10px;
                            }
                            .filter-info {
                                background: #F9FAFB !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            th {
                                background-color: #2563EB !important;
                                color: white !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            tr:nth-child(even) {
                                background-color: #F9FAFB !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .bg-yellow-100 {
                                background-color: #FEF3C7 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .bg-orange-100 {
                                background-color: #FFEDD5 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .bg-green-100 {
                                background-color: #D1FAE5 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .bg-red-100 {
                                background-color: #FEE2E2 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                            .bg-gray-100 {
                                background-color: #F3F4F6 !important;
                                -webkit-print-color-adjust: exact;
                                print-color-adjust: exact;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${reportTitle}</h1>
                        <p>Dicetak pada: ${new Date().toLocaleString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        })}</p>
                    </div>
                    ${filterInfo ? '<div class="filter-info"><strong>Filter yang Diterapkan:</strong>' + filterInfo + '</div>' : ''}
                    <table>
                        <thead>
                            <tr>
                                <th>Booking Date</th>
                                <th>Booking Number</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Room</th>
                                <th>Stay Period</th>
                                <th>Payment Date & Time</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableContent}
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Booking Management System - Laporan Pemesanan</p>
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
                start_date: document.getElementById('start_date').value,
                end_date: document.getElementById('end_date').value,
                status: document.getElementById('status').value,
                property_id: document.getElementById('property_id')?.value || '',
                search: document.getElementById('search').value
            });

            window.location.href = '{{ route('reports.booking.export') }}?' + params.toString();

            // Show success message
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Exporting report...',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    </script>
</x-app-layout>
