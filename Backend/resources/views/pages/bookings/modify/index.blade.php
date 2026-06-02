<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        {{-- Page header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.modify_booking_title') }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">{{ __('ui.modify_booking_desc') }}</p>
            </div>
        </div>

        {{-- Search + per-page filter — mirrors Confirmed Bookings minimal toolbar. --}}
        <div class="search-filter-container bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('modifyBooking.filter') }}"
                  onsubmit="event.preventDefault(); fetchFilteredBookings();"
                  class="search-filter-form flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div class="md:col-span-3 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.order_id_or_guest') }}"
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="{{ request('search') }}">
                    </div>
                    <div class="md:col-span-1">
                        <label for="per_page" class="text-xs text-gray-500">{{ __('ui.show') }}</label>
                        <select id="per_page" name="per_page"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" @if (request('per_page', 25) == $size) selected @endif>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            <div class="overflow-x-auto" id="bookings-table-container">
                @include('pages.bookings.modify.partials.modify_table', [
                    'bookings' => $bookings,
                    'per_page' => $per_page,
                ])
            </div>

            <div class="px-6 py-3 border-t border-gray-200" id="pagination-container">
                {{ $bookings->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    {{-- Single shared modal — populated via fetch on click. Per-row data lives in the
         button's data attributes; the modal's Alpine x-data tracks current order_id and
         the form values, plus the history list for the history panel. --}}
    @include('pages.bookings.modify.partials.modify_modal')

    {{-- Inherit the same dark-mode badge styles other booking pages use, so the row badges
         (Deposit / Renewed / Checked Out) render with proper contrast. --}}
    @include('pages.bookings.partials.dark-badge-styles')

    @push('scripts')
        <script>
            const debounce = (fn, delay = 300) => {
                let t;
                return function (...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            };

            function fetchFilteredBookings(url = null) {
                const params = new URLSearchParams();
                if (url && typeof url === 'string' && url.startsWith('http')) {
                    new URL(url).searchParams.forEach((v, k) => params.append(k, v));
                } else {
                    const search = document.getElementById('search').value;
                    if (search) params.append('search', search);
                    params.append('per_page', document.getElementById('per_page').value);
                }

                const tableContainer = document.getElementById('bookings-table-container');
                tableContainer.innerHTML = `<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div></div>`;

                fetch(`{{ route('modifyBooking.filter') }}?${params.toString()}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(r => r.json())
                .then(data => {
                    tableContainer.innerHTML = data.table;
                    const pag = document.getElementById('pagination-container');
                    if (pag) pag.innerHTML = data.pagination;
                    // Re-bind pagination clicks to the AJAX flow.
                    document.querySelectorAll('#pagination-container a').forEach(a => {
                        a.addEventListener('click', e => {
                            e.preventDefault();
                            fetchFilteredBookings(a.href);
                        });
                    });
                })
                .catch(err => {
                    console.error('Modify Booking filter failed', err);
                    tableContainer.innerHTML = `<p class="p-6 text-red-500 text-center">{{ __('ui.modify_booking_save_failed') }}</p>`;
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('search');
                const perPageSelect = document.getElementById('per_page');
                searchInput?.addEventListener('input', debounce(fetchFilteredBookings, 300));
                perPageSelect?.addEventListener('change', () => fetchFilteredBookings());

                // Bind initial pagination clicks to AJAX.
                document.querySelectorAll('#pagination-container a').forEach(a => {
                    a.addEventListener('click', e => {
                        e.preventDefault();
                        fetchFilteredBookings(a.href);
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
