{{-- Maintenance Mode management page --}}
{{-- Allows super admins to toggle maintenance mode for the frontend website and mobile app --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.maintenance_mode') }}
                </h1>
                <p class="text-sm text-gray-600 mt-2">{{ __('ui.maintenance_mode_desc') }}</p>
            </div>
        </div>

        {{-- Maintenance Mode Card --}}
        <div class="search-filter-container bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden"
            x-data="{
                maintenanceMode: false,
                loading: true,
                toggling: false,
                init() {
                    // <!-- Fetch current maintenance mode status on page load -->
                    fetch('{{ route('maintenance.status') }}', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.maintenanceMode = data.data.maintenance_mode;
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
                },
                toggle() {
                    this.toggling = true;
                    const newState = !this.maintenanceMode;
                    // <!-- Toggle maintenance mode via POST endpoint -->
                    fetch('{{ route('maintenance.toggle') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ enabled: newState })
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.maintenanceMode = data.data.maintenance_mode;
                        this.toggling = false;
                        Toastify({
                            text: data.message,
                            duration: 3000,
                            close: true,
                            gravity: 'bottom',
                            position: 'right',
                            style: { background: newState ? '#EF4444' : '#10B981' }
                        }).showToast();
                    })
                    .catch(() => { this.toggling = false; });
                }
            }">

            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            {{-- Warning icon --}}
                            <svg class="w-7 h-7 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('ui.maintenance_mode') }}</h3>
                        </div>

                        <ul class="text-sm text-gray-500 list-disc list-inside space-y-1 mt-3">
                            <li>{{ __('ui.maintenance_mode_frontend') }}</li>
                            <li>{{ __('ui.maintenance_mode_mobile') }}</li>
                            <li>{{ __('ui.maintenance_mode_admin_note') }}</li>
                        </ul>
                    </div>

                    <div class="flex items-center ml-6 pt-1">
                        {{-- Loading spinner --}}
                        <template x-if="loading">
                            <svg class="animate-spin h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>

                        {{-- Toggle switch --}}
                        <template x-if="!loading">
                            <button @click="toggle()" :disabled="toggling"
                                class="relative inline-flex h-7 w-14 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2"
                                :class="maintenanceMode ? 'bg-red-500 focus:ring-red-500' : 'bg-gray-300 focus:ring-gray-400'">
                                <span
                                    class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-transform duration-300"
                                    :class="maintenanceMode ? 'translate-x-8' : 'translate-x-1'"></span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Status indicator --}}
                <div class="mt-5 pt-5 border-t border-gray-100" x-show="!loading">
                    <div class="flex items-center gap-2">
                        <span class="inline-block w-2.5 h-2.5 rounded-full"
                            :class="maintenanceMode ? 'bg-red-500 animate-pulse' : 'bg-green-500'"></span>
                        <span class="text-sm font-medium"
                            :class="maintenanceMode ? 'text-red-600' : 'text-green-600'"
                            x-text="maintenanceMode ? '{{ __('ui.maintenance_status_on') }}' : '{{ __('ui.maintenance_status_off') }}'"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
