<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">
                    {{ __('ui.refund_report') }}
                </h1>
                <p class="text-gray-600 mt-1">{{ __('ui.refund_report_desc') }}</p>
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

        {{-- Filter Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <form id="filterForm" class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    {{-- Search --}}
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.search') }}</label>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-9"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="Order ID, Tenant, Reason..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    {{-- Date Range (refund_date) --}}
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.select_date_range') }}</label>
                        <div class="relative">
                            <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}" data-input
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-white">
                            <input type="hidden" id="start_date" name="start_date" value="{{ $startDate }}">
                            <input type="hidden" id="end_date" name="end_date" value="{{ $endDate }}">
                        </div>
                    </div>

                    {{-- Property Filter (super admin / HO only) --}}
                    @if(auth()->user()->isSuperAdmin() || auth()->user()->isHORole())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.property') }}</label>
                        <select id="property_id" name="property_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
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

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.status') }}</label>
                        <select id="status" name="status"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500">
                            <option value="">{{ __('ui.all_status') }}</option>
                            <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="refunded" {{ $status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                </div>

                {{-- Per Page --}}
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                        <select name="per_page" id="per_page"
                            class="border-gray-200 rounded-lg focus:ring-red-500 focus:border-red-500 text-sm">
                            <option value="8">8</option>
                            <option value="25" selected>25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        {{-- Report Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Refund Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tenant</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Property / Room</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reason</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Bank / Account</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Processed By</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" class="divide-y divide-gray-200">
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="animate-spin h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Loading refund data...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="paginationContainer" class="bg-gray-50 px-4 py-3 border-t border-gray-200"></div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let searchTimeout;

        document.addEventListener('DOMContentLoaded', function () {
            // <!-- Initialize Flatpickr range picker (no default range — show all) -->
            flatpickr('#date_picker', {
                mode: 'range',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'j M Y',
                allowInput: true,
                locale: { rangeSeparator: ' to ' },
                onChange: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        const startDate = selectedDates[0];
                        const endDate = selectedDates[1] || selectedDates[0];
                        document.getElementById('start_date').value = instance.formatDate(startDate, 'Y-m-d');
                        document.getElementById('end_date').value = instance.formatDate(endDate, 'Y-m-d');
                    }
                    currentPage = 1;
                    fetchReportData();
                },
                onClose: function (selectedDates) {
                    if (selectedDates.length === 0) {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        currentPage = 1;
                        fetchReportData();
                    }
                }
            });

            fetchReportData();

            ['status', 'property_id', 'per_page'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.addEventListener('change', function () {
                        currentPage = 1;
                        fetchReportData();
                    });
                }
            });

            document.getElementById('search').addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function () {
                    currentPage = 1;
                    fetchReportData();
                }, 300);
            });

            document.getElementById('search').addEventListener('keypress', function (e) {
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
                            <svg class="animate-spin h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Loading data...
                        </div>
                    </td>
                </tr>
            `;

            fetch('{{ route('reports.refund.data') }}?' + new URLSearchParams(Object.fromEntries(formData)))
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        renderTable(data.data);
                        renderPagination(data.pagination);
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    tbody.innerHTML = `
                        <tr><td colspan="10" class="px-4 py-8 text-center text-red-500">{{ __('ui.error_loading') }}</td></tr>
                    `;
                });
        }

        function renderTable(data) {
            const tbody = document.getElementById('reportTableBody');

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span>No refund records found</span>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => {
                let propertyRoom = '-';
                if (row.property_name && row.property_name !== '-') {
                    propertyRoom = `<div class="text-gray-900">${escapeHtml(row.property_name)}</div>`;
                    if (row.room && row.room !== '-') {
                        propertyRoom += `<div class="text-xs text-gray-500">${escapeHtml(row.room)}</div>`;
                    }
                }

                let bankAccount = '-';
                if ((row.bank_name && row.bank_name !== '-') || (row.account_no && row.account_no !== '-')) {
                    bankAccount = `<div class="text-gray-900">${escapeHtml(row.bank_name || '-')}</div>`;
                    if (row.account_no && row.account_no !== '-') {
                        bankAccount += `<div class="text-xs text-gray-700">${escapeHtml(row.account_no)}</div>`;
                    }
                    if (row.account_holder && row.account_holder !== '-') {
                        bankAccount += `<div class="text-xs text-gray-500">${escapeHtml(row.account_holder)}</div>`;
                    }
                }

                return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(row.refund_date)}</td>
                    <td class="px-4 py-3 text-sm font-medium text-blue-600">${escapeHtml(row.order_id)}</td>
                    <td class="px-4 py-3 text-sm text-gray-900">${escapeHtml(row.tenant_name)}</td>
                    <td class="px-4 py-3 text-sm">${propertyRoom}</td>
                    <td class="px-4 py-3 text-sm">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getTypeClass(row.refund_type)}">${escapeHtml(row.refund_type)}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-700 max-w-xs truncate" title="${escapeHtml(row.reason)}">${escapeHtml(row.reason)}</td>
                    <td class="px-4 py-3 text-sm font-semibold text-red-600 text-right">${escapeHtml(row.amount)}</td>
                    <td class="px-4 py-3 text-sm">${bankAccount}</td>
                    <td class="px-4 py-3 text-sm text-gray-700">${escapeHtml(row.processed_by)}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(row.raw_status)}">${escapeHtml(row.status)}</span>
                    </td>
                </tr>`;
            }).join('');
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getStatusClass(status) {
            const map = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'refunded': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
            };
            return map[status] || 'bg-gray-100 text-gray-800';
        }

        function getTypeClass(type) {
            const t = (type || '').toLowerCase();
            if (t === 'user') return 'bg-purple-100 text-purple-800';
            if (t === 'admin') return 'bg-blue-100 text-blue-800';
            return 'bg-gray-100 text-gray-800';
        }

        function renderPagination(pagination) {
            const container = document.getElementById('paginationContainer');

            if (pagination.last_page <= 1) {
                container.innerHTML = `<div class="text-sm text-gray-700">{{ __('ui.showing') }} ${pagination.total} {{ __('ui.entries') }}</div>`;
                return;
            }

            let html = `
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        {{ __('ui.showing') }} ${pagination.current_page} {{ __('ui.of') }} ${pagination.last_page} (${pagination.total} {{ __('ui.entries') }})
                    </div>
                    <div class="flex gap-2">
            `;

            if (pagination.current_page > 1) {
                html += `<button onclick="fetchReportData(${pagination.current_page - 1})" class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">{{ __('ui.previous') }}</button>`;
            }

            for (let i = 1; i <= pagination.last_page; i++) {
                if (
                    i === 1 ||
                    i === pagination.last_page ||
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    const activeClass = i === pagination.current_page
                        ? 'bg-red-600 text-white'
                        : 'border border-gray-300 hover:bg-gray-50';
                    html += `<button onclick="fetchReportData(${i})" class="px-3 py-1 rounded-md text-sm ${activeClass}">${i}</button>`;
                } else if (
                    i === pagination.current_page - 3 ||
                    i === pagination.current_page + 3
                ) {
                    html += `<span class="px-2 py-1 text-gray-500">...</span>`;
                }
            }

            if (pagination.current_page < pagination.last_page) {
                html += `<button onclick="fetchReportData(${pagination.current_page + 1})" class="px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50 text-sm">{{ __('ui.next') }}</button>`;
            }

            html += `</div></div>`;
            container.innerHTML = html;
        }

        function printReport() {
            const reportTitle = 'Laporan Refund';
            let filterInfo = '';

            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            if (startDate && endDate) {
                filterInfo += `<p><strong>Periode:</strong> ${formatDate(startDate)} - ${formatDate(endDate)}</p>`;
            }

            const propertySelect = document.getElementById('property_id');
            if (propertySelect && propertySelect.value) {
                filterInfo += `<p><strong>Properti:</strong> ${propertySelect.options[propertySelect.selectedIndex].text}</p>`;
            }

            const statusSelect = document.getElementById('status');
            if (statusSelect && statusSelect.value) {
                filterInfo += `<p><strong>Status:</strong> ${statusSelect.options[statusSelect.selectedIndex].text}</p>`;
            }

            const searchValue = document.getElementById('search').value;
            if (searchValue) {
                filterInfo += `<p><strong>Pencarian:</strong> ${searchValue}</p>`;
            }

            const tableContent = document.getElementById('reportTableBody').innerHTML;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #DC2626; padding-bottom: 10px; }
                        .header h1 { margin: 0; color: #DC2626; font-size: 24px; }
                        .header p { margin: 5px 0; color: #666; }
                        .filter-info { background: #F3F4F6; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                        .filter-info p { margin: 5px 0; font-size: 14px; }
                        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 10px; }
                        th { background-color: #DC2626; color: white; padding: 10px 6px; text-align: left; font-size: 9px; font-weight: 600; text-transform: uppercase; border: 1px solid #991B1B; }
                        td { padding: 8px 6px; border: 1px solid #E5E7EB; font-size: 10px; }
                        tr:nth-child(even) { background-color: #FFF5F5; }
                        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #E5E7EB; padding-top: 15px; }
                        .px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
                        .py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
                        .text-xs { font-size: 0.75rem; line-height: 1rem; }
                        .font-medium { font-weight: 500; }
                        .rounded-full { border-radius: 9999px; }
                        .bg-yellow-100 { background-color: #FEF3C7; } .text-yellow-800 { color: #92400E; }
                        .bg-green-100 { background-color: #D1FAE5; } .text-green-800 { color: #065F46; }
                        .bg-red-100 { background-color: #FEE2E2; } .text-red-800 { color: #991B1B; }
                        .bg-blue-100 { background-color: #DBEAFE; } .text-blue-800 { color: #1E40AF; }
                        .bg-purple-100 { background-color: #EDE9FE; } .text-purple-800 { color: #5B21B6; }
                        .bg-gray-100 { background-color: #F3F4F6; } .text-gray-800 { color: #1F2937; }
                        @media print {
                            body { margin: 0; padding: 10px; }
                            th { background-color: #DC2626 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                            tr:nth-child(even) { background-color: #FFF5F5 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>${reportTitle}</h1>
                        <p>Dicetak pada: ${new Date().toLocaleString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                    ${filterInfo ? '<div class="filter-info"><strong>Filter yang Diterapkan:</strong>' + filterInfo + '</div>' : ''}
                    <table>
                        <thead>
                            <tr>
                                <th>Refund Date</th>
                                <th>Order ID</th>
                                <th>Tenant</th>
                                <th>Property / Room</th>
                                <th>Type</th>
                                <th>Reason</th>
                                <th>Amount</th>
                                <th>Bank / Account</th>
                                <th>Processed By</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>${tableContent}</tbody>
                    </table>
                    <div class="footer"><p>Booking Management System - Laporan Refund</p></div>
                </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.onload = function () {
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
                status: document.getElementById('status').value,
                property_id: document.getElementById('property_id')?.value || '',
                search: document.getElementById('search').value
            });

            window.location.href = '{{ route('reports.refund.export') }}?' + params.toString();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Exporting refund report...',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }
    </script>
</x-app-layout>
