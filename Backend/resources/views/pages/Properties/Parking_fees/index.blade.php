<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.parking_fee_management') }}
            </h1>
            <div class="mt-4 md:mt-0">
                <div x-data="addParkingModal()" class="relative">
                    <button type="button" @click="modalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('ui.add_parking_fee') }}
                    </button>

                    <div x-show="modalOpen" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity" x-transition.opacity @click="modalOpen = false" x-cloak></div>

                    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak
                        @keydown.escape.window="modalOpen = false">

                        <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700"
                            @click.outside="modalOpen = false">
                            <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 border-b dark:border-gray-700 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('ui.add_parking_fee') }}</h3>
                                <button @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <form class="px-5 py-4 space-y-4" @submit.prevent="submitForm">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.select_property') }} <span class="text-red-500">*</span></label>
                                    <select x-model="form.property_id" required
                                        class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="" disabled>{{ __('ui.select_property') }}</option>
                                        @foreach($properties as $prop)
                                            <option value="{{ $prop->idrec }}">{{ $prop->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_type') }} <span class="text-red-500">*</span></label>
                                    <select x-model="form.parking_type" required
                                        class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="" disabled>{{ __('ui.select_parking_type') }}</option>
                                        <option value="car">{{ __('ui.car') }}</option>
                                        <option value="motorcycle">{{ __('ui.motorcycle') }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_fee_amount') }} (Rp) <span class="text-red-500">*</span></label>
                                    <input type="text" inputmode="numeric"
                                        :value="feeFormatted"
                                        @input="handleFeeInput($event)"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_capacity') }} <span class="text-red-500">*</span></label>
                                    <input type="number" x-model="form.capacity" min="0" step="1"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                        placeholder="0" required />
                                </div>

                                <div class="flex justify-end gap-2 pt-2 border-t dark:border-gray-700">
                                    <button type="button" @click="modalOpen = false"
                                        class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 hover:bg-gray-50">
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

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <form id="searchForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="w-full md:w-1/4">
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

                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.parking_type') }}:</span>
                            <select name="parking_type" id="parkingTypeFilter"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="">{{ __('ui.all') }}</option>
                                <option value="car" {{ request('parking_type') == 'car' ? 'selected' : '' }}>{{ __('ui.car') }}</option>
                                <option value="motorcycle" {{ request('parking_type') == 'motorcycle' ? 'selected' : '' }}>{{ __('ui.motorcycle') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.status_filter') }}</span>
                            <select name="status" id="statusFilter"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="all">{{ __('ui.all') }}</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('ui.active') }}</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('ui.inactive') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label for="perPageSelect" class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.items_per_page') }}</label>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="8" {{ request('per_page', 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page', 8) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 8) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="showDeletedFilter" class="sr-only peer" {{ request('show_deleted') == '1' ? 'checked' : '' }}>
                                <div class="w-9 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer-checked:bg-red-500 transition-all duration-300"></div>
                                <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-4"></div>
                            </label>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.show_deleted') }}</span>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto" id="tableContainer">
                @include('pages.Properties.Parking_fees.partials.parking-fee_table', ['parkingFees' => $parkingFees])
            </div>

            @if($parkingFees instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4" id="paginationContainer">
                {{ $parkingFees->appends(request()->input())->links() }}
            </div>
            @endif
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data="editParkingModal()" x-show="isOpen" class="fixed inset-0 z-50" x-cloak>
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

            <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-xl shadow-lg overflow-hidden border border-gray-100 dark:border-gray-700" @click.outside="closeModal()">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900 dark:to-indigo-900 border-b dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('ui.edit') }} {{ __('ui.parking_fee') }}</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <form class="px-5 py-4 space-y-4" @submit.prevent="submitEditForm">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.property') }}</label>
                        <input type="text" :value="parking.property_name" disabled
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 rounded-md bg-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_type') }}</label>
                        <input type="text" :value="parking.parking_type === 'car' ? '{{ __("ui.car") }}' : '{{ __("ui.motorcycle") }}'" disabled
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-600 dark:text-gray-300 rounded-md bg-gray-100" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_fee_amount') }} (Rp) <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric"
                            :value="feeFormatted"
                            @input="handleFeeInput($event)"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_capacity') }} <span class="text-red-500">*</span></label>
                        <input type="number" x-model="parking.capacity" min="0" step="1"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500" required />
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
        function formatRupiah(val) {
            if (!val && val !== 0) return '';
            return String(val).replace(/[^\d]/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function toggleParkingStatus(checkbox) {
            const id = checkbox.dataset.id;
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');

            fetch('/properties/parking-fees/toggle-status', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: newStatus })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    statusLabel.textContent = newStatus == 1 ? '{{ __("ui.active") }}' : '{{ __("ui.inactive") }}';
                    statusLabel.classList.remove('text-green-600', 'text-red-600');
                    statusLabel.classList.add(newStatus == 1 ? 'text-green-600' : 'text-red-600');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else { checkbox.checked = !checkbox.checked; }
            })
            .catch(() => { checkbox.checked = !checkbox.checked; });
        }

        function openEditParkingModal(parking) {
            window.dispatchEvent(new CustomEvent('open-edit-parking-modal', { detail: parking }));
        }

        function applyFilters() {
            const search = document.getElementById('searchInput')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';
            const parkingType = document.getElementById('parkingTypeFilter')?.value || '';
            const perPage = document.getElementById('perPageSelect')?.value || '8';
            const showDeleted = document.getElementById('showDeletedFilter')?.checked ? '1' : '0';

            fetch('/properties/parking-fees/filter', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ search, status, parking_type: parkingType, per_page: perPage, show_deleted: showDeleted })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('tableContainer').innerHTML = data.html;
                const pag = document.getElementById('paginationContainer');
                if (pag) pag.innerHTML = data.pagination || '';

                const urlParams = new URLSearchParams();
                if (search) urlParams.append('search', search);
                if (status) urlParams.append('status', status);
                if (parkingType) urlParams.append('parking_type', parkingType);
                if (perPage) urlParams.append('per_page', perPage);
                if (showDeleted === '1') urlParams.append('show_deleted', '1');
                window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            });
        }

        function adjustQuota(id, action) {
            fetch(`/properties/parking-fees/adjust-quota/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ action })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 });
                    applyFilters();
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                }
            })
            .catch(() => {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Terjadi kesalahan', showConfirmButton: false, timer: 3000 });
            });
        }

        function deleteParkingFee(id) {
            Swal.fire({
                title: '{{ __("ui.confirm_delete") }}',
                text: 'Parking fee ini akan dihapus dan dapat dipulihkan kembali.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("ui.delete") }}',
                cancelButtonText: '{{ __("ui.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/properties/parking-fees/destroy/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2500 });
                            applyFilters();
                        } else {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                        }
                    });
                }
            });
        }

        function restoreParkingFee(id) {
            fetch(`/properties/parking-fees/restore/${id}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2500 });
                    applyFilters();
                } else {
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let t;
            document.getElementById('searchInput')?.addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 500); });
            document.getElementById('statusFilter')?.addEventListener('change', applyFilters);
            document.getElementById('parkingTypeFilter')?.addEventListener('change', applyFilters);
            document.getElementById('perPageSelect')?.addEventListener('change', applyFilters);
            document.getElementById('showDeletedFilter')?.addEventListener('change', applyFilters);
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('addParkingModal', () => ({
                modalOpen: false,
                form: { property_id: '', parking_type: '', fee: '', capacity: '' },
                get feeFormatted() {
                    return formatRupiah(this.form.fee);
                },
                handleFeeInput(e) {
                    let raw = e.target.value.replace(/[^\d]/g, '');
                    this.form.fee = raw;
                    e.target.value = formatRupiah(raw);
                },
                async submitForm() {
                    try {
                        const r = await fetch('/properties/parking-fees/store', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ property_id: this.form.property_id, parking_type: this.form.parking_type, fee: this.form.fee, capacity: this.form.capacity })
                        });
                        const data = await r.json();
                        if (!r.ok) throw new Error(data.message || 'Error');
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        this.modalOpen = false;
                        this.form = { property_id: '', parking_type: '', fee: '', capacity: '' };
                        applyFilters();
                    } catch (e) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: e.message, showConfirmButton: false, timer: 5000 });
                    }
                },
            }));

            Alpine.data('editParkingModal', () => ({
                isOpen: false, isSubmitting: false,
                parking: { id: null, fee: '', capacity: '', property_name: '', parking_type: '' },
                get feeFormatted() {
                    return formatRupiah(this.parking.fee);
                },
                handleFeeInput(e) {
                    let raw = e.target.value.replace(/[^\d]/g, '');
                    this.parking.fee = raw;
                    e.target.value = formatRupiah(raw);
                },
                init() {
                    window.addEventListener('open-edit-parking-modal', (e) => {
                        const d = e.detail;
                        this.parking = { id: d.idrec, fee: String(Math.round(Number(d.fee))), capacity: d.capacity, property_name: d.property?.name || '-', parking_type: d.parking_type };
                        this.isOpen = true;
                    });
                },
                closeModal() { this.isOpen = false; },
                async submitEditForm() {
                    this.isSubmitting = true;
                    try {
                        const r = await fetch(`/properties/parking-fees/update/${this.parking.id}`, {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ fee: this.parking.fee, capacity: this.parking.capacity })
                        });
                        const data = await r.json();
                        if (!r.ok) throw new Error(data.message || 'Error');
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 3000 });
                        this.closeModal();
                        applyFilters();
                    } catch (e) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: e.message, showConfirmButton: false, timer: 5000 });
                    } finally { this.isSubmitting = false; }
                }
            }));
        });
    </script>
</x-app-layout>
