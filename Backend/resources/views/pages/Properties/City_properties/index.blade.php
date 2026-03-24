{{-- City management page — CRUD for master cities used across properties --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.city_management') }}
            </h1>
            <div class="mt-4 md:mt-0">
                {{-- Alpine component for the "Add City" modal --}}
                <div x-data="modalCity()" class="relative">
                    <!-- Trigger Button -->
                    <button type="button" @click="modalOpenDetail = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('ui.city_add') }}
                    </button>

                    <!-- Backdrop -->
                    <div x-show="modalOpenDetail"
                        class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity" x-transition.opacity
                        @click="modalOpenDetail = false" x-cloak></div>

                    <!-- Add City Modal -->
                    <div x-show="modalOpenDetail" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak
                        @keydown.escape.window="modalOpenDetail = false">

                        <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-visible border border-gray-100"
                            @click.outside="modalOpenDetail = false">
                            <!-- Header -->
                            <div
                                class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b flex items-center justify-between rounded-t-xl">
                                <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.city_add') }}</h3>
                                <button @click="modalOpenDetail = false"
                                    class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Form -->
                            <form class="px-5 py-4 space-y-4" @submit.prevent="submitForm">
                                @csrf
                                <!-- City Name -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_name_label') }} <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="city_name" x-model="currentCity.city_name"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('ui.city_name_placeholder') }}" required />
                                </div>

                                <!-- Province -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_province_label') }} <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="province" x-model="currentCity.province"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('ui.city_province_placeholder') }}" required />
                                </div>

                                <!-- Slug (optional — auto-generated if left blank) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_slug_label') }}</label>
                                    <input type="text" name="slug" x-model="currentCity.slug"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="{{ __('ui.city_slug_placeholder') }}" />
                                </div>

                                <!-- Actions -->
                                <div class="flex justify-end gap-2 pt-2 border-t">
                                    <button type="button" @click="modalOpenDetail = false"
                                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                                        {{ __('ui.cancel') }}
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow">
                                        {{ __('ui.save') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- City Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-4 border-b border-gray-200">
                <form id="searchForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <!-- Search Input -->
                        <div class="w-full md:w-1/3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="{{ __('ui.city_search_placeholder') }}">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ __('ui.city_status_label') }}</span>
                            <select name="status" id="statusFilter"
                                class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">{{ __('ui.city_all') }}</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <!-- Items per Page -->
                        <div class="flex items-center gap-2">
                            <label for="perPageSelect" class="text-sm text-gray-600">{{ __('ui.city_show_label') }}</label>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="8" {{ request('per_page', 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page', 8) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 8) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                {{-- Include the city table partial --}}
                @include('pages.Properties.City_properties.partials.city_table', [
                    'cities' => $cities,
                ])
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 rounded p-4" id="paginationContainer">
                {{ $cities->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Modal (outside table, uses Alpine event) -->
    <div x-data="editCityModal()" x-show="isOpen" class="fixed inset-0 z-50" x-cloak>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity"
            x-show="isOpen" x-transition.opacity @click="closeModal()"></div>

        <!-- Modal -->
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
            x-show="isOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            @keydown.escape.window="closeModal()">

            <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-visible border border-gray-100"
                @click.outside="closeModal()">
                <!-- Header -->
                <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b flex items-center justify-between rounded-t-xl">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.city_edit') }}</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form class="px-5 py-4 space-y-4" @submit.prevent="submitEditForm">
                    <!-- City Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_name_label') }} <span class="text-red-500">*</span></label>
                        <input type="text" x-model="city.city_name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.city_name_placeholder') }}" required />
                    </div>

                    <!-- Province -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_province_label') }} <span class="text-red-500">*</span></label>
                        <input type="text" x-model="city.province"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.city_province_placeholder') }}" required />
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.city_slug_label') }}</label>
                        <input type="text" x-model="city.slug"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.city_slug_placeholder') }}" />
                    </div>

                    <!-- Status Toggle -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Inactive</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" x-model="city.status"
                                        :checked="city.status == 1">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm text-gray-600">Active</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <button type="button" @click="closeModal()"
                            class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit" :disabled="isSubmitting"
                            class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow disabled:opacity-50">
                            <span x-show="!isSubmitting">{{ __('ui.city_save_changes') }}</span>
                            <span x-show="isSubmitting">{{ __('ui.city_saving') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        /**
         * Toggle city status via AJAX — updates status label and shows toast notification.
         */
        function toggleCityStatus(checkbox) {
            const cityId = checkbox.dataset.id;
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');

            fetch('/properties/m-properties/cities/toggle-status', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: cityId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusLabel.textContent = newStatus == 1 ? 'Active' : 'Inactive';
                    statusLabel.classList.remove('text-green-600', 'text-red-600');
                    statusLabel.classList.add(newStatus == 1 ? 'text-green-600' : 'text-red-600');

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || '{{ __('ui.city_status_changed') }}',
                        showConfirmButton: false,
                        timer: 2000
                    });
                } else {
                    checkbox.checked = !checkbox.checked;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: data.message || '{{ __('ui.city_status_change_failed') }}',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                checkbox.checked = !checkbox.checked;
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ __('ui.city_error_occurred') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        }

        /**
         * Dispatch event to open the edit city modal with pre-filled data.
         */
        function openEditCityModal(city) {
            window.dispatchEvent(new CustomEvent('open-edit-city-modal', {
                detail: city
            }));
        }

        /**
         * AJAX filtering — fetches filtered table partial and updates DOM.
         */
        function applyFilters() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const perPageSelect = document.getElementById('perPageSelect');

            if (!searchInput || !statusFilter || !perPageSelect) {
                return;
            }

            const params = new URLSearchParams();

            if (searchInput.value) params.append('search', searchInput.value);
            if (statusFilter.value) params.append('status', statusFilter.value);
            if (perPageSelect.value) params.append('per_page', perPageSelect.value);

            showLoading();

            fetch(`/properties/m-properties/cities?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newTable = doc.querySelector('#tableContainer table');
                    const currentTable = document.querySelector('.overflow-x-auto table');
                    if (newTable && currentTable) {
                        currentTable.querySelector('tbody').innerHTML = newTable.querySelector('tbody').innerHTML;
                    }

                    const newPagination = doc.getElementById('paginationContainer');
                    const currentPagination = document.getElementById('paginationContainer');
                    if (newPagination && currentPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }

                    const newUrl = `${window.location.pathname}?${params.toString()}`;
                    window.history.pushState({}, '', newUrl);

                    hideLoading();
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideLoading();
                });
        }

        function showLoading() {
            const tableBody = document.querySelector('tbody');
            if (!tableBody) return;

            const existingOverlay = document.getElementById('loadingOverlay');
            if (existingOverlay) {
                existingOverlay.remove();
            }

            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loadingOverlay';
            loadingOverlay.className = 'absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10';
            loadingOverlay.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600">Loading...</span>
                </div>
            `;

            const tableContainer = document.querySelector('.overflow-x-auto');
            if (tableContainer) {
                tableContainer.style.position = 'relative';
                tableContainer.appendChild(loadingOverlay);
            }
        }

        function hideLoading() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }

        /**
         * Attach event listeners for search, status filter, and per-page select.
         */
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const perPageSelect = document.getElementById('perPageSelect');

            let searchTimeout;

            searchInput.addEventListener('input', function(e) {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    applyFilters();
                }, 500);
            });

            statusFilter.addEventListener('change', applyFilters);
            perPageSelect.addEventListener('change', applyFilters);

            window.addEventListener('popstate', function() {
                applyFilters();
            });
        });

        /**
         * Alpine.js components for add and edit city modals.
         */
        document.addEventListener('alpine:init', () => {
            // Add city modal component
            Alpine.data('modalCity', () => ({
                modalOpenDetail: false,
                currentCity: {
                    city_name: '',
                    province: '',
                    slug: '',
                    status: 1,
                },

                showSuccessToast(message) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                },

                showErrorToast(message) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: message,
                        showConfirmButton: false,
                        timer: 5000
                    });
                },

                async submitForm() {
                    try {
                        const formData = {
                            city_name: this.currentCity.city_name,
                            province: this.currentCity.province,
                            slug: this.currentCity.slug || '',
                            status: this.currentCity.status ? 1 : 0
                        };

                        const response = await fetch('/properties/m-properties/cities/store', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            if (data.errors) {
                                let errorMessages = [];
                                for (const [field, errors] of Object.entries(data.errors)) {
                                    errorMessages.push(`${errors.join(', ')}`);
                                }
                                throw new Error(errorMessages.join('\n'));
                            }
                            throw new Error(data.message || '{{ __('ui.city_error_saving') }}');
                        }

                        this.modalOpenDetail = false;
                        this.currentCity = {
                            city_name: '',
                            province: '',
                            slug: '',
                            status: 1,
                        };

                        this.showSuccessToast('{{ __('ui.city_added_success') }}');
                        window.location.reload();

                    } catch (error) {
                        console.error('Error:', error);
                        this.showErrorToast(error.message || '{{ __('ui.city_error_saving') }}');
                    }
                },
            }));

            // Edit city modal component
            Alpine.data('editCityModal', () => ({
                isOpen: false,
                isSubmitting: false,
                city: {
                    id: null,
                    city_name: '',
                    province: '',
                    slug: '',
                    status: true
                },

                init() {
                    window.addEventListener('open-edit-city-modal', (event) => {
                        this.openModal(event.detail);
                    });
                },

                openModal(cityData) {
                    this.city = {
                        id: cityData.idrec || cityData.id,
                        city_name: cityData.city_name || '',
                        province: cityData.province || '',
                        slug: cityData.slug || '',
                        status: parseInt(cityData.status) === 1
                    };
                    this.isOpen = true;
                },

                closeModal() {
                    this.isOpen = false;
                    this.city = {
                        id: null,
                        city_name: '',
                        province: '',
                        slug: '',
                        status: true
                    };
                },

                async submitEditForm() {
                    this.isSubmitting = true;

                    try {
                        const formData = {
                            city_name: this.city.city_name,
                            province: this.city.province,
                            slug: this.city.slug || '',
                            status: this.city.status ? 1 : 0
                        };

                        const response = await fetch(`/properties/m-properties/cities/update/${this.city.id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(formData)
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || '{{ __('ui.city_error_saving') }}');
                        }

                        this.closeModal();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: '{{ __('ui.city_updated_success') }}',
                            showConfirmButton: false,
                            timer: 3000
                        });
                        window.location.reload();

                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: error.message || '{{ __('ui.city_error_saving') }}',
                            showConfirmButton: false,
                            timer: 5000
                        });
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</x-app-layout>
