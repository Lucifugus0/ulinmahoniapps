<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.completed') }}
                </h1>
            </div>
        </div>
        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('completed.filter') }}"
                onsubmit="event.preventDefault(); fetchFilteredBookings();"
                class="flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <!-- Search Booking -->
                    <div class="md:col-span-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.order_id_or_guest') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <div class="flex-1">
                            <div class="relative z-10">
                                <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}"
                                    data-input
                                    class="w-full min-w-[320px] px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <input type="hidden" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Show Per Page (aligned to the right) -->
                    <div class="md:col-span-1 md:col-start-5 flex justify-end items-end">
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
                </div>
            </form>
        </div>


        <!-- Bookings Table -->
        <div class="overflow-x-auto">
            @include('pages.bookings.allbookings.partials.allbookings_table', [
                'bookings' => $bookings,
                'per_page' => request('per_page', 8),
            ])
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 rounded p-4" id="paginationContainer">
            {{ $bookings->appends(request()->input())->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            const datePicker = flatpickr("#date_picker", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j M Y",
                allowInput: true,
                static: true,
                monthSelectorType: 'static',
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        document.getElementById('start_date').value = formatDate(selectedDates[0]);
                        document.getElementById('end_date').value = formatDate(selectedDates[1] || selectedDates[0]);
                        fetchFilteredBookings();
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 0) {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        fetchFilteredBookings();
                    }
                }
            });

            // Get all filter elements
            const searchInput = document.getElementById('search');
            const perPageSelect = document.getElementById('per_page');

            // Debounce function for search
            const debounce = (func, delay) => {
                let timeout;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            };

            // Event listeners
            searchInput.addEventListener('input', debounce(fetchFilteredBookings, 300));
            perPageSelect.addEventListener('change', fetchFilteredBookings);

            // Function to fetch filtered bookings
            function fetchFilteredBookings(url = null) {
                // Collect all filter values
                const params = new URLSearchParams();

                // If URL is provided (from pagination) and it's a valid string, extract params from it
                if (url && typeof url === 'string' && url.startsWith('http')) {
                    const urlObj = new URL(url);
                    urlObj.searchParams.forEach((value, key) => {
                        params.append(key, value);
                    });
                } else {
                    // Get search value
                    const search = document.getElementById('search').value;
                    if (search) params.append('search', search);

                    // Get date range values
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    if (startDate) params.append('start_date', startDate);
                    if (endDate) params.append('end_date', endDate);

                    // Get per page value
                    const perPage = document.getElementById('per_page').value;
                    params.append('per_page', perPage);
                }

                // Show loading state
                const tableContainer = document.querySelector('.overflow-x-auto');
                tableContainer.innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            `;

                // Make AJAX request to the filter endpoint
                fetch(`{{ route('completed.filter') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        document.querySelector('.overflow-x-auto').innerHTML = data.table;
                        document.getElementById('paginationContainer').innerHTML = data.pagination;

                        // Re-initialize Alpine.js components for new DOM elements
                        if (typeof Alpine !== 'undefined') {
                            Alpine.initTree(document.querySelector('.overflow-x-auto'));
                        }

                        // Re-attach event listeners for new pagination links
                        attachPaginationListeners();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.querySelector('.overflow-x-auto').innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            {{ __('ui.error_loading') }}
                        </div>
                    `;
                    });
            }

            // Function to attach event listeners to pagination links
            function attachPaginationListeners() {
                const paginationContainer = document.getElementById('paginationContainer');
                if (!paginationContainer) return;

                // Find all pagination links
                const paginationLinks = paginationContainer.querySelectorAll('a[href]');

                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url) {
                            fetchFilteredBookings(url);
                        }
                    });
                });
            }

            // Initial attach of pagination listeners
            attachPaginationListeners();
        });
    </script>
</x-app-layout>
