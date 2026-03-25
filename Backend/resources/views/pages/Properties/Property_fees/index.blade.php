{{-- Unified Deposit & Parking Fee management page.
     Shows one row per property with deposit, motorcycle, and car fee columns.
     Add/Edit modals allow setting all fees for a property at once. --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.property_fees_management') }}
            </h1>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <!-- Search and Filter -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <form id="searchForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="w-full md:w-1/3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="{{ __('ui.search_placeholder') }}">
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="perPageSelect" class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.items_per_page') }}</label>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 25) == 50 ? 'selected' : '' }}>50</option>
                                <option value="all" {{ request('per_page', 25) == 'all' ? 'selected' : '' }}>{{ __('ui.all') }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto" id="tableContainer">
                @include('pages.Properties.Property_fees.partials.property-fees_table', ['properties' => $properties])
            </div>

            <!-- Pagination -->
            @if($properties instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4" id="paginationContainer">
                {{ $properties->appends(request()->input())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data="editPropertyFeesModal()" x-show="isOpen" class="fixed inset-0 z-50" x-cloak>
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" x-show="isOpen" x-transition.opacity @click="closeModal()"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            @keydown.escape.window="closeModal()">

            <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700"
                @click.outside="closeModal()">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('ui.edit_property_fees') }}</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form class="px-5 py-4 space-y-4" @submit.prevent="submitEditForm">
                    <!-- Property (read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.property') }}</label>
                        <input type="text" :value="form.property_name" disabled
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 rounded-md bg-gray-100" />
                    </div>

                    <!-- Deposit Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.deposit_fee_amount') }} (Rp)</label>
                        <input type="text" inputmode="numeric"
                            :value="formatRupiah(form.deposit_amount)"
                            @input="form.deposit_amount = $event.target.value.replace(/[^\d]/g, ''); $event.target.value = formatRupiah(form.deposit_amount)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="0" />
                    </div>

                    <!-- Motorcycle section -->
                    <div class="border-t dark:border-gray-700 pt-3">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.motorcycle') }}</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.parking_fee_amount') }} (Rp)</label>
                                <input type="text" inputmode="numeric"
                                    :value="formatRupiah(form.motorcycle_fee)"
                                    @input="form.motorcycle_fee = $event.target.value.replace(/[^\d]/g, ''); $event.target.value = formatRupiah(form.motorcycle_fee)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.parking_capacity') }}</label>
                                <input type="number" x-model="form.motorcycle_capacity" min="0" step="1"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <!-- Car section -->
                    <div class="border-t dark:border-gray-700 pt-3">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('ui.car') }}</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.parking_fee_amount') }} (Rp)</label>
                                <input type="text" inputmode="numeric"
                                    :value="formatRupiah(form.car_fee)"
                                    @input="form.car_fee = $event.target.value.replace(/[^\d]/g, ''); $event.target.value = formatRupiah(form.car_fee)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0" />
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.parking_capacity') }}</label>
                                <input type="number" x-model="form.car_capacity" min="0" step="1"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="0" />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t dark:border-gray-700">
                        <button type="button" @click="closeModal()"
                            class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 hover:bg-gray-50">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow disabled:opacity-50">
                            <span x-show="!isSubmitting">{{ __('ui.save_changes') }}</span>
                            <span x-show="isSubmitting">{{ __('ui.processing') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /**
         * Format a numeric string as Indonesian Rupiah (dot-separated thousands).
         */
        function formatRupiah(val) {
            if (!val && val !== 0) return '';
            return String(val).replace(/[^\d]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        /**
         * Open the edit modal with pre-populated values for a specific property.
         * Called from the table partial's edit button onclick.
         */
        function openEditPropertyFeesModal(propertyId, propertyName, depositAmount, motorcycleFee, motorcycleCapacity, carFee, carCapacity) {
            window.dispatchEvent(new CustomEvent('open-edit-property-fees-modal', {
                detail: {
                    property_id: propertyId,
                    property_name: propertyName,
                    deposit_amount: depositAmount !== null ? String(Math.round(Number(depositAmount))) : '',
                    motorcycle_fee: motorcycleFee !== null ? String(Math.round(Number(motorcycleFee))) : '',
                    motorcycle_capacity: motorcycleCapacity !== null ? motorcycleCapacity : '',
                    car_fee: carFee !== null ? String(Math.round(Number(carFee))) : '',
                    car_capacity: carCapacity !== null ? carCapacity : '',
                }
            }));
        }

        /**
         * AJAX filter — reloads table and pagination when search/perPage changes.
         */
        function applyFilters() {
            const search = document.getElementById('searchInput')?.value || '';
            const perPage = document.getElementById('perPageSelect')?.value || '25';

            fetch('/properties/property-fees/filter', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ search, per_page: perPage })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('tableContainer').innerHTML = data.html;
                const pag = document.getElementById('paginationContainer');
                if (pag) pag.innerHTML = data.pagination || '';

                const urlParams = new URLSearchParams();
                if (search) urlParams.append('search', search);
                if (perPage) urlParams.append('per_page', perPage);
                window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            });
        }

        /* Bind search input (debounced) and per-page selector to applyFilters */
        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            document.getElementById('searchInput')?.addEventListener('input', () => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyFilters, 500);
            });
            document.getElementById('perPageSelect')?.addEventListener('change', applyFilters);
        });

        /* Alpine.js component registrations */
        document.addEventListener('alpine:init', () => {
            /**
             * Edit Property Fees modal — pre-populated from the table row's edit button.
             * Listens for 'open-edit-property-fees-modal' custom event.
             */
            Alpine.data('editPropertyFeesModal', () => ({
                isOpen: false,
                isSubmitting: false,
                form: {
                    property_id: null,
                    property_name: '',
                    deposit_amount: '',
                    motorcycle_fee: '',
                    motorcycle_capacity: '',
                    car_fee: '',
                    car_capacity: '',
                },
                formatRupiah,
                init() {
                    window.addEventListener('open-edit-property-fees-modal', (event) => {
                        this.form = { ...event.detail };
                        this.isOpen = true;
                    });
                },
                closeModal() { this.isOpen = false; },
                async submitEditForm() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch('/properties/property-fees/store-or-update', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });
                        const data = await response.json();
                        if (!response.ok) throw new Error(data.message || 'Error');
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        this.closeModal();
                        applyFilters();
                    } catch (error) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: error.message, showConfirmButton: false, timer: 5000 });
                    } finally { this.isSubmitting = false; }
                }
            }));
        });
    </script>
</x-app-layout>
