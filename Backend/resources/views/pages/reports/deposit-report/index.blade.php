<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-600 to-purple-800">
                    {{ __('ui.deposit_report') }}
                </h1>
                <p class="text-gray-600 mt-1">{{ __('ui.deposit_report_desc') }}</p>
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
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.search_deposit_report_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Date Range -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.payment_date_range') }}</label>
                        <div class="relative">
                            <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}" data-input
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 bg-white">
                            <input type="hidden" id="start_date" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>

                    <!-- Property Filter -->
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isHO())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.property') }}</label>
                        <select id="property_id" name="property_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                            class="border-gray-200 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-sm">
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
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.invoice_id') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.order_id') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.property_name') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.room') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.tenant_name') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.phone_number') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.fee_amount') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.transaction_date') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.payment_date') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.payment_status') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.verified_by') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.verified_date') }}</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">{{ __('ui.notes') }}</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="animate-spin h-8 w-8 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ __('ui.loading') }}</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="paginationContainer" class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let searchTimeout;
        let reportData = [];

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

            fetchReportData();

            ['property_id', 'per_page'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', function() {
                        currentPage = 1;
                        fetchReportData();
                    });
                }
            });

            document.getElementById('search').addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    currentPage = 1;
                    fetchReportData();
                }, 300);
            });

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
            formData.append('property_id', document.getElementById('property_id')?.value || '');
            formData.append('search', document.getElementById('search').value);
            formData.append('per_page', document.getElementById('per_page').value);
            formData.append('page', page);

            const tbody = document.getElementById('reportTableBody');
            tbody.innerHTML = `
                <tr>
                    <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                        <div class="flex justify-center items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            {{ __('ui.loading') }}
                        </div>
                    </td>
                </tr>
            `;

            fetch('{{ route('reports.deposit.data') }}?' + new URLSearchParams(Object.fromEntries(formData)))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        reportData = data.data;
                        renderTable(data.data);
                        renderPagination(data.pagination);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-red-500">
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
                        <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ __('ui.no_data') }}</span>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => {
                return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-3 py-3 text-xs text-gray-900">${row.no}</td>
                    <td class="px-3 py-3 text-xs font-medium text-purple-600">${row.invoice_id}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.order_id}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.property_name}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.room_name}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.tenant_name}</td>
                    <td class="px-3 py-3 text-xs text-gray-700">${row.phone}</td>
                    <td class="px-3 py-3 text-xs font-semibold text-gray-900">${row.fee_amount}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.transaction_date}</td>
                    <td class="px-3 py-3 text-xs text-gray-900">${row.paid_at}</td>
                    <td class="px-3 py-3 text-xs text-gray-700">${row.payment_status}</td>
                    <td class="px-3 py-3 text-xs text-gray-700">${row.verified_by}</td>
                    <td class="px-3 py-3 text-xs text-gray-700">${row.verified_at}</td>
                    <td class="px-3 py-3 text-xs text-gray-600">${row.notes || '-'}</td>
                </tr>
            `}).join('');
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

            if (pagination.current_page > 1) {
                paginationHTML += `
                    <button onclick="fetchReportData(${pagination.current_page - 1})"
                        class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">
                        {{ __('ui.previous') }}
                    </button>
                `;
            }

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
            const reportTitle = '{{ __('ui.deposit_report') }}';

            let filterInfo = '';
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (startDate && endDate) {
                filterInfo += `<p><strong>{{ __('ui.period') }}:</strong> ${formatDate(startDate)} - ${formatDate(endDate)}</p>`;
            }

            const propertySelect = document.getElementById('property_id');
            if (propertySelect && propertySelect.value) {
                const selectedProperty = propertySelect.options[propertySelect.selectedIndex].text;
                filterInfo += `<p><strong>{{ __('ui.property') }}:</strong> ${selectedProperty}</p>`;
            }

            const searchValue = document.getElementById('search').value;
            if (searchValue) {
                filterInfo += `<p><strong>{{ __('ui.search') }}:</strong> ${searchValue}</p>`;
            }

            let tableRows = '';
            if (reportData && reportData.length > 0) {
                tableRows = reportData.map(row => {
                    return `
                        <tr>
                            <td>${row.no}</td>
                            <td>${row.invoice_id}</td>
                            <td>${row.order_id}</td>
                            <td>${row.property_name}</td>
                            <td>${row.room_name}</td>
                            <td>${row.tenant_name}</td>
                            <td>${row.phone}</td>
                            <td>${row.fee_amount}</td>
                            <td>${row.transaction_date}</td>
                            <td>${row.paid_at}</td>
                            <td>${row.payment_status}</td>
                            <td>${row.verified_by}</td>
                            <td>${row.verified_at}</td>
                            <td>${row.notes || '-'}</td>
                        </tr>
                    `;
                }).join('');
            }

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        @page { size: landscape; margin: 10mm; }
                        body { font-family: Arial, sans-serif; margin: 10px; color: #333; font-size: 9px; }
                        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #7C3AED; padding-bottom: 10px; }
                        .header h1 { margin: 0; color: #7C3AED; font-size: 18px; }
                        .header p { margin: 5px 0; color: #666; font-size: 10px; }
                        .filter-info { background: #F3F4F6; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
                        .filter-info p { margin: 3px 0; font-size: 10px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                        th { background-color: #7C3AED; color: white; padding: 5px 3px; text-align: left; font-size: 8px; font-weight: 600; text-transform: uppercase; border: 1px solid #6D28D9; white-space: nowrap; }
                        td { padding: 4px 3px; border: 1px solid #E5E7EB; font-size: 8px; white-space: nowrap; }
                        tr:nth-child(even) { background-color: #F9FAFB; }
                        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #E5E7EB; padding-top: 10px; }
                        @media print {
                            body { margin: 0; padding: 5px; }
                            th { background-color: #7C3AED !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                            tr:nth-child(even) { background-color: #F9FAFB !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${reportTitle}</h1>
                        <p>Dicetak pada: ${new Date().toLocaleString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                    ${filterInfo ? '<div class="filter-info"><strong>{{ __('ui.filter') }}:</strong>' + filterInfo + '</div>' : ''}
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>{{ __('ui.invoice_id') }}</th>
                                <th>{{ __('ui.order_id') }}</th>
                                <th>{{ __('ui.property') }}</th>
                                <th>{{ __('ui.room') }}</th>
                                <th>{{ __('ui.tenant_name') }}</th>
                                <th>{{ __('ui.phone_number') }}</th>
                                <th>{{ __('ui.fee_amount') }}</th>
                                <th>{{ __('ui.transaction_date') }}</th>
                                <th>{{ __('ui.payment_date') }}</th>
                                <th>{{ __('ui.status') }}</th>
                                <th>{{ __('ui.verified_by') }}</th>
                                <th>{{ __('ui.verified_date') }}</th>
                                <th>{{ __('ui.notes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${tableRows}
                        </tbody>
                    </table>
                    <div class="footer">
                        <p>Booking Management System - {{ __('ui.deposit_report') }}</p>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.focus();
                printWindow.print();
            };
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        }

        function exportReport() {
            const params = new URLSearchParams({
                start_date: document.getElementById('start_date').value,
                end_date: document.getElementById('end_date').value,
                property_id: document.getElementById('property_id')?.value || '',
                search: document.getElementById('search').value
            });

            window.location.href = '{{ route('reports.deposit.export') }}?' + params.toString();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: '{{ __('ui.exporting_report') }}',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    </script>
</x-app-layout>
