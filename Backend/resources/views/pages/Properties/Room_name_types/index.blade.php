{{-- Room Name Type management page — CRUD for master room types --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.room_name_type_management') }}
            </h1>
            <div class="mt-4 md:mt-0">
                <div x-data="modalRoomNameType()" class="relative">
                    <button type="button" @click="modalOpenDetail = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        {{ __('ui.room_name_type_add') }}
                    </button>

                    <!-- Backdrop -->
                    <div x-show="modalOpenDetail" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity" x-transition.opacity @click="modalOpenDetail = false" x-cloak></div>

                    <!-- Add Modal -->
                    <div x-show="modalOpenDetail" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                        x-cloak @keydown.escape.window="modalOpenDetail = false">
                        <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-visible border border-gray-100" @click.outside="modalOpenDetail = false">
                            <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b flex items-center justify-between rounded-t-xl">
                                <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.room_name_type_add') }}</h3>
                                <button @click="modalOpenDetail = false" class="text-gray-400 hover:text-gray-600 transition">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" /></svg>
                                </button>
                            </div>
                            <form class="px-5 py-4 space-y-4" @submit.prevent="submitForm">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.room_name_type_name_label') }} <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="currentType.name" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="{{ __('ui.room_name_type_name_placeholder') }}" required />
                                </div>
                                <div class="flex justify-end gap-2 pt-2 border-t">
                                    <button type="button" @click="modalOpenDetail = false" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">{{ __('ui.cancel') }}</button>
                                    <button type="submit" class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow">{{ __('ui.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="w-full md:w-1/3">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                            </div>
                            <input type="text" id="searchInput" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="{{ __('ui.room_name_type_search') }}">
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ __('ui.facility_status_label') }}</span>
                        <select id="statusFilter" class="border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                            <option value="">{{ __('ui.facility_all') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                @include('pages.Properties.Room_name_types.partials.room_name_type_table')
            </div>

            <div class="bg-gray-50 rounded p-4" id="paginationContainer">
                {{ $roomNameTypes->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data="editRoomNameTypeModal()" x-show="isOpen" class="fixed inset-0 z-50" x-cloak>
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity" x-show="isOpen" x-transition.opacity @click="closeModal()"></div>
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" x-show="isOpen"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            @keydown.escape.window="closeModal()">
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg overflow-visible border border-gray-100" @click.outside="closeModal()">
                <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b flex items-center justify-between rounded-t-xl">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.room_name_type_edit') }}</h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z" clip-rule="evenodd" /></svg>
                    </button>
                </div>
                <form class="px-5 py-4 space-y-4" @submit.prevent="submitEditForm">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.room_name_type_name_label') }} <span class="text-red-500">*</span></label>
                        <input type="text" x-model="typeData.name" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required />
                    </div>
                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <label class="text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">Inactive</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer" x-model="typeData.status" :checked="typeData.status == 1">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm text-gray-600">Active</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <button type="button" @click="closeModal()" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">{{ __('ui.cancel') }}</button>
                        <button type="submit" :disabled="isSubmitting" class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow disabled:opacity-50">
                            <span x-show="!isSubmitting">{{ __('ui.facility_save_changes') }}</span>
                            <span x-show="isSubmitting">{{ __('ui.facility_saving') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRoomNameTypeStatus(checkbox) {
            const id = checkbox.dataset.id;
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');
            fetch('/rooms/room-name-types/toggle-status', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, status: newStatus })
            }).then(r => r.json()).then(data => {
                if (data.success) {
                    statusLabel.textContent = newStatus == 1 ? 'Active' : 'Inactive';
                    statusLabel.classList.remove('text-green-600', 'text-red-600');
                    statusLabel.classList.add(newStatus == 1 ? 'text-green-600' : 'text-red-600');
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else { checkbox.checked = !checkbox.checked; }
            }).catch(() => { checkbox.checked = !checkbox.checked; });
        }

        function openEditRoomNameTypeModal(data) {
            window.dispatchEvent(new CustomEvent('open-edit-room-name-type-modal', { detail: data }));
        }

        function applyFilters() {
            const search = document.getElementById('searchInput')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (status) params.append('status', status);
            window.location.href = `${window.location.pathname}?${params.toString()}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            let t; document.getElementById('searchInput').addEventListener('input', () => { clearTimeout(t); t = setTimeout(applyFilters, 500); });
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('modalRoomNameType', () => ({
                modalOpenDetail: false,
                currentType: { name: '' },
                async submitForm() {
                    try {
                        const res = await fetch('/rooms/room-name-types/store', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ name: this.currentType.name, status: 1 })
                        });
                        const data = await res.json();
                        if (data.success) { this.modalOpenDetail = false; this.currentType = { name: '' }; window.location.reload(); }
                        else { alert(data.message || 'Error'); }
                    } catch (e) { alert('Error'); }
                },
            }));

            Alpine.data('editRoomNameTypeModal', () => ({
                isOpen: false, isSubmitting: false,
                typeData: { id: null, name: '', status: true },
                init() { window.addEventListener('open-edit-room-name-type-modal', (e) => this.openModal(e.detail)); },
                openModal(data) { this.typeData = { id: data.idrec, name: data.name, status: parseInt(data.status) === 1 }; this.isOpen = true; },
                closeModal() { this.isOpen = false; },
                async submitEditForm() {
                    this.isSubmitting = true;
                    try {
                        const res = await fetch(`/rooms/room-name-types/update/${this.typeData.id}`, {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ name: this.typeData.name, status: this.typeData.status ? 1 : 0 })
                        });
                        const data = await res.json();
                        if (data.success) { this.closeModal(); window.location.reload(); }
                        else { alert(data.message || 'Error'); }
                    } catch (e) { alert('Error'); }
                    this.isSubmitting = false;
                }
            }));
        });
    </script>
</x-app-layout>
