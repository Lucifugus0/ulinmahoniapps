<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.parking_management') }}
            </h1>
            <button type="button" onclick="openAddParkingModal()"
                class="mt-4 md:mt-0 inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New
            </button>
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
                                    placeholder="{{ __('ui.search_parking_management_placeholder') }}">
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
                @include('pages.Properties.Parking.partials.parking_table', ['parkings' => $parkings])
            </div>

            @if($parkings instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="bg-gray-50 dark:bg-gray-800 rounded p-4" id="paginationContainer">
                {{ $parkings->appends(request()->input())->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Add New Parking Modal --}}
    <div id="addParkingModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddParkingModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-600 to-blue-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Add New Parking</h3>
                            <p class="text-white/80 text-sm">Register a new vehicle from checked-in booking</p>
                        </div>
                        <button onclick="closeAddParkingModal()" class="text-white hover:text-indigo-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form id="addParkingForm">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Booking Order (Check-in) -->
                            <div class="md:col-span-2">
                                <label for="add_prk_order_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Booking Order (Checked-In) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mb-1.5">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="prk_order_search_input" placeholder="Search order ID, name, or room..."
                                        oninput="filterPrkOrderOptions(this.value)"
                                        onfocus="openPrkOrderDropdown()"
                                        onblur="setTimeout(() => closePrkOrderDropdown(), 200)"
                                        class="w-full pl-9 pr-9 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <button type="button" id="prk_order_search_clear" onclick="clearPrkOrderSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <select name="order_id" id="add_prk_order_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <option value="">Loading checked-in orders...</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Search then select a booking that is currently checked-in</p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_prk_order_id_error"></p>
                            </div>

                            <!-- User Info Display -->
                            <div class="md:col-span-2 bg-blue-50 dark:bg-gray-700 p-3 rounded-lg hidden" id="prk_order_info_section">
                                <div class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">Booking Information</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                            <p><span class="font-medium">Customer:</span> <span id="prk_display_user_name">-</span></p>
                                            <p><span class="font-medium">Phone:</span> <span id="prk_display_user_phone">-</span></p>
                                            <p><span class="font-medium">Room:</span> <span id="prk_display_room_name">-</span></p>
                                            <p><span class="font-medium">Property:</span> <span id="prk_display_property_name">-</span></p>
                                            <p><span class="font-medium">Check-in - Check-out:</span> <span id="prk_display_checkin_checkout">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parking Status Info -->
                            <div class="md:col-span-2 hidden" id="prk_parking_status_section"></div>

                            <!-- Parking Type -->
                            <div class="md:col-span-2">
                                <label for="add_prk_parking_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Parking Type <span class="text-red-500">*</span>
                                </label>
                                <select name="parking_type" id="add_prk_parking_type" required
                                    onchange="checkPrkParkingQuota()"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <option value="">Select Type</option>
                                    <option value="car">{{ __('ui.car') }}</option>
                                    <option value="motorcycle">{{ __('ui.motorcycle') }}</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_prk_parking_type_error"></p>
                            </div>

                            <!-- Parking Quota Information -->
                            <div class="md:col-span-2 hidden" id="prk_quota_info_section">
                                <div class="rounded-lg border-2 p-4" id="prk_quota_info_container"></div>
                            </div>

                            <!-- Vehicle Plate and Parking Duration side by side -->
                            <div>
                                <label for="add_prk_vehicle_plate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Vehicle Plate <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="vehicle_plate" id="add_prk_vehicle_plate" required
                                    placeholder="e.g., B 1234 XYZ" oninput="this.value = this.value.toUpperCase()"
                                    style="text-transform: uppercase;"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_prk_vehicle_plate_error"></p>
                            </div>

                            <div>
                                <label for="add_prk_parking_duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Parking Duration (months) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="parking_duration" id="add_prk_parking_duration" required
                                    min="1" value="1" placeholder="e.g., 1"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-xs text-blue-600 mt-1 hidden" id="prk_parking_duration_max_hint"></p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_prk_parking_duration_error"></p>
                            </div>

                            <!-- Fee Amount (readonly, full width) -->
                            <div class="md:col-span-2">
                                <label for="add_prk_fee_amount_display" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fee Amount (Rp)
                                </label>
                                <input type="text" id="add_prk_fee_amount_display" readonly
                                    placeholder="Select parking type first"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm bg-gray-50 dark:bg-gray-600 cursor-not-allowed">
                                <input type="hidden" name="fee_amount" id="add_prk_fee_amount">
                                <p class="text-xs text-gray-500 mt-1" id="prk_fee_source_info">Fee will be loaded from parking fee configuration</p>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="add_prk_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Notes (Optional)
                                </label>
                                <textarea name="notes" id="add_prk_notes" rows="3" placeholder="Enter additional notes..."
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeAddParkingModal()"
                                class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" id="addParkingSubmitBtn"
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span id="addParkingSubmitText">Add Parking</span>
                                <svg id="addParkingSpinner" class="animate-spin h-4 w-4 text-white hidden"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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
                    <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('ui.edit_parking') }}</h3>
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
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.parking_type') }} <span class="text-red-500">*</span></label>
                        <select x-model="parking.parking_type" required @change="fetchFeeInfo()"
                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                            <option value="car">{{ __('ui.car') }}</option>
                            <option value="motorcycle">{{ __('ui.motorcycle') }}</option>
                        </select>
                    </div>
                    <!-- Fee Info Display -->
                    <div x-show="feeInfo.show" x-transition class="rounded-lg border p-3"
                        :class="feeInfo.quotaFull ? 'border-red-300 bg-red-50 dark:border-red-600 dark:bg-red-900/20' : (feeInfo.changed ? 'border-amber-300 bg-amber-50 dark:border-amber-600 dark:bg-amber-900/20' : 'border-blue-200 bg-blue-50 dark:border-blue-700 dark:bg-blue-900/20')">
                        <div class="flex items-start gap-2">
                            <svg class="h-5 w-5 mt-0.5 flex-shrink-0"
                                :class="feeInfo.quotaFull ? 'text-red-600 dark:text-red-400' : (feeInfo.changed ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400')"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1 text-sm">
                                <p class="font-medium"
                                    :class="feeInfo.quotaFull ? 'text-red-900 dark:text-red-100' : (feeInfo.changed ? 'text-amber-900 dark:text-amber-100' : 'text-blue-900 dark:text-blue-100')"
                                    x-text="feeInfo.quotaFull ? 'Kuota penuh - tidak dapat mengubah ke type ini' : feeInfo.title"></p>
                                <p class="mt-1" :class="feeInfo.quotaFull ? 'text-red-700 dark:text-red-300' : (feeInfo.changed ? 'text-amber-700 dark:text-amber-300' : 'text-blue-700 dark:text-blue-300')">
                                    Fee: <strong x-text="feeInfo.feeFormatted"></strong>/bulan
                                </p>
                                <template x-if="feeInfo.changed">
                                    <div>
                                        <p class="mt-1 text-xs" :class="feeInfo.quotaFull ? 'text-red-600 dark:text-red-400' : 'text-amber-600 dark:text-amber-400'">
                                            Sebelumnya (<span x-text="feeInfo.oldTypeLabel"></span>): <strong x-text="feeInfo.oldFeeFormatted"></strong>/bulan
                                        </p>
                                        <template x-if="feeInfo.capacity > 0">
                                            <div class="mt-2">
                                                <p class="text-xs font-medium" :class="feeInfo.quotaFull ? 'text-red-700 dark:text-red-300' : 'text-amber-700 dark:text-amber-300'">
                                                    Kuota: <strong x-text="feeInfo.availableQuota + ' slot tersedia'"></strong>
                                                    <span class="font-normal" x-text="'dari ' + feeInfo.capacity + ' total'"></span>
                                                </p>
                                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-1.5 mt-1">
                                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                                        :class="feeInfo.quotaFull ? 'bg-red-500' : (feeInfo.quotaPercentage >= 80 ? 'bg-yellow-500' : 'bg-green-500')"
                                                        :style="'width: ' + feeInfo.quotaPercentage + '%'"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.vehicle_plate') }} <span class="text-red-500">*</span></label>
                        <input type="text" x-model="parking.vehicle_plate" required
                            @input="parking.vehicle_plate = parking.vehicle_plate.toUpperCase()"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.owner_name') }}</label>
                        <input type="text" x-model="parking.owner_name"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.enter_owner_name') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.owner_phone') }}</label>
                        <input type="text" x-model="parking.owner_phone"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.enter_owner_phone') }}" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.notes') }}</label>
                        <textarea x-model="parking.notes" rows="2"
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md focus:ring-blue-500 focus:border-blue-500"
                            placeholder="{{ __('ui.enter_additional_notes') }}"></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t dark:border-gray-700">
                        <button type="button" @click="closeModal()"
                            class="px-3 py-1.5 text-sm text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 hover:bg-gray-50">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit" :disabled="isSubmitting || feeInfo.quotaFull"
                            class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">{{ __('ui.save_changes') }}</span>
                            <span x-show="isSubmitting">{{ __('ui.processing') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ==================== Add New Parking ====================
        let prkCheckedInOrdersData = [];

        function openAddParkingModal() {
            document.getElementById('addParkingModal').classList.remove('hidden');
            document.getElementById('addParkingForm').reset();
            clearAddParkingErrors();
            document.getElementById('prk_order_info_section').classList.add('hidden');
            document.getElementById('prk_quota_info_section').classList.add('hidden');
            document.body.style.overflow = 'hidden';
            // Re-enable submit button
            const submitBtn = document.getElementById('addParkingSubmitBtn');
            if (submitBtn) { submitBtn.disabled = false; submitBtn.classList.remove('opacity-50', 'cursor-not-allowed'); }
            // Reset duration, fee, and notes
            prkResetDurationAndFee();
            document.getElementById('add_prk_notes').value = '';
            // Clear order search
            const searchInput = document.getElementById('prk_order_search_input');
            if (searchInput) {
                searchInput.value = '';
                document.getElementById('prk_order_search_clear').classList.add('hidden');
            }
            closePrkOrderDropdown();
            loadPrkCheckedInOrders();
        }

        function closeAddParkingModal() {
            document.getElementById('addParkingModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function loadPrkCheckedInOrders() {
            const orderSelect = document.getElementById('add_prk_order_id');
            orderSelect.innerHTML = '<option value="">Loading checked-in orders...</option>';

            fetch('/payment/parking/checked-in-orders', { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        prkCheckedInOrdersData = data.data;
                        orderSelect.innerHTML = '<option value="">Select a booking order</option>';
                        data.data.forEach(order => {
                            const option = document.createElement('option');
                            option.value = order.order_id;
                            option.textContent = order.display_text;
                            option.dataset.propertyId = order.property_id;
                            option.dataset.userName = order.user_name;
                            option.dataset.userPhone = order.user_phone;
                            option.dataset.roomName = order.room_name;
                            option.dataset.propertyName = order.property_name;
                            option.dataset.checkIn = order.check_in;
                            option.dataset.checkOut = order.check_out;
                            option.dataset.maxParkingMonths = order.max_parking_months || '';
                            option.dataset.parkingStatus = order.parking_status || 'new';
                            option.dataset.parkingInfo = order.parking_info ? JSON.stringify(order.parking_info) : '';
                            option.dataset.activeParkings = order.active_parkings ? JSON.stringify(order.active_parkings) : '[]';
                            orderSelect.appendChild(option);
                        });
                    } else {
                        orderSelect.innerHTML = '<option value="">No checked-in bookings available</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    orderSelect.innerHTML = '<option value="">Error loading orders</option>';
                });
        }

        function openPrkOrderDropdown() {
            const orderSelect = document.getElementById('add_prk_order_id');
            const count = orderSelect.options.length;
            orderSelect.size = Math.min(Math.max(count, 2), 7);
        }

        function closePrkOrderDropdown() {
            const orderSelect = document.getElementById('add_prk_order_id');
            orderSelect.size = 1;
        }

        function selectPrkActiveParkingVehicle(vehiclePlate, parkingType) {
            const plateInput = document.getElementById('add_prk_vehicle_plate');
            const typeSelect = document.getElementById('add_prk_parking_type');
            if (plateInput) plateInput.value = vehiclePlate;
            if (typeSelect) {
                typeSelect.value = parkingType;
                typeSelect.dispatchEvent(new Event('change'));
            }
            // Highlight selected chip
            document.querySelectorAll('.prk-active-parking-chip').forEach(el => {
                el.classList.remove('ring-2', 'ring-orange-500');
            });
            event.currentTarget.classList.add('ring-2', 'ring-orange-500');
        }

        function showPrkParkingStatusBadge(status, parkingInfoStr, activeParkingsStr) {
            const section = document.getElementById('prk_parking_status_section');
            if (!section) return;

            let badgeHtml = '';
            if (status === 'active') {
                let activeParkings = [];
                try { activeParkings = activeParkingsStr ? JSON.parse(activeParkingsStr) : []; } catch(e) {}

                const vehicleRows = activeParkings.length > 0
                    ? activeParkings.map(p => {
                        const durationText = p.parking_duration ? `${p.parking_duration} bln` : '';
                        const expiryText   = p.expiry_date ? `s/d ${p.expiry_date}` : '';
                        const infoText     = [durationText, expiryText].filter(Boolean).join(' • ');
                        const expiredBadge = p.is_expired === true
                            ? `<span class="ml-1 text-red-500 font-bold">✗ Expired</span>`
                            : (p.is_expired === false ? `<span class="ml-1 text-green-600 font-bold">✓ Aktif</span>` : '');
                        return `<button type="button" onclick="selectPrkActiveParkingVehicle('${p.vehicle_plate}', '${p.parking_type}')"
                            class="prk-active-parking-chip inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-100 rounded text-xs font-medium hover:bg-orange-200 dark:hover:bg-orange-700 transition-colors cursor-pointer border border-orange-300 dark:border-orange-600">
                            <span class="font-semibold">${p.vehicle_plate || '-'}</span>
                            <span class="font-normal opacity-75">(${p.parking_type || '-'})</span>
                            ${infoText ? `<span class="opacity-60">• ${infoText}</span>` : ''}
                            ${expiredBadge}
                        </button>`;
                    }).join('')
                    : '<span class="text-xs text-orange-600 dark:text-orange-300 opacity-75">(no records)</span>';

                badgeHtml = `
                    <div class="flex flex-col gap-1.5 px-3 py-2 bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-700 rounded-lg">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-100">PARKIR MASIH AKTIF</span>
                            <span class="text-xs text-orange-700 dark:text-orange-300">Klik kendaraan untuk mengisi form:</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5">${vehicleRows}</div>
                    </div>`;
            } else if (status === 'renewal') {
                badgeHtml = `
                    <div class="flex items-center gap-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100">PERPANJANGAN</span>
                        <span class="text-xs text-blue-700 dark:text-blue-300">Customer has had parking before</span>
                    </div>`;
            } else {
                badgeHtml = `
                    <div class="flex items-center gap-2 px-3 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">PARKIR BARU</span>
                        <span class="text-xs text-green-700 dark:text-green-300">No active parking registered for this customer</span>
                    </div>`;
            }

            section.innerHTML = badgeHtml;
            section.classList.remove('hidden');
        }

        function filterPrkOrderOptions(keyword) {
            const orderSelect = document.getElementById('add_prk_order_id');
            const clearBtn = document.getElementById('prk_order_search_clear');
            const q = keyword.trim().toLowerCase();

            if (clearBtn) clearBtn.classList.toggle('hidden', q === '');

            orderSelect.value = '';
            document.getElementById('prk_order_info_section').classList.add('hidden');
            const quotaSection = document.getElementById('prk_quota_info_section');
            if (quotaSection) quotaSection.classList.add('hidden');
            document.getElementById('prk_parking_status_section').classList.add('hidden');

            const filtered = q === ''
                ? prkCheckedInOrdersData
                : prkCheckedInOrdersData.filter(o =>
                    o.order_id.toLowerCase().includes(q) ||
                    (o.user_name && o.user_name.toLowerCase().includes(q)) ||
                    (o.room_name && o.room_name.toLowerCase().includes(q)) ||
                    (o.property_name && o.property_name.toLowerCase().includes(q))
                );

            orderSelect.innerHTML = '';
            const placeholder = document.createElement('option');
            placeholder.value = '';
            placeholder.textContent = filtered.length > 0
                ? 'Select a booking order'
                : 'No matching orders found';
            orderSelect.appendChild(placeholder);

            filtered.forEach(order => {
                const option = document.createElement('option');
                option.value = order.order_id;
                option.textContent = order.display_text;
                option.dataset.propertyId = order.property_id;
                option.dataset.userName = order.user_name;
                option.dataset.userPhone = order.user_phone;
                option.dataset.roomName = order.room_name;
                option.dataset.propertyName = order.property_name;
                option.dataset.checkIn = order.check_in;
                option.dataset.checkOut = order.check_out;
                option.dataset.maxParkingMonths = order.max_parking_months || '';
                option.dataset.parkingStatus = order.parking_status || 'new';
                option.dataset.parkingInfo = order.parking_info ? JSON.stringify(order.parking_info) : '';
                option.dataset.activeParkings = order.active_parkings ? JSON.stringify(order.active_parkings) : '[]';
                orderSelect.appendChild(option);
            });

            // Keep dropdown open while search is active
            const count = orderSelect.options.length;
            orderSelect.size = Math.min(Math.max(count, 2), 7);
        }

        function clearPrkOrderSearch() {
            const searchInput = document.getElementById('prk_order_search_input');
            searchInput.value = '';
            filterPrkOrderOptions('');
            searchInput.focus();
        }

        // Handle order selection for add parking
        document.getElementById('add_prk_order_id')?.addEventListener('change', function() {
            closePrkOrderDropdown();
            const selectedOption = this.options[this.selectedIndex];
            const orderInfoSection = document.getElementById('prk_order_info_section');

            if (this.value && selectedOption.dataset.userName) {
                document.getElementById('prk_display_user_name').textContent = selectedOption.dataset.userName || '-';
                document.getElementById('prk_display_user_phone').textContent = selectedOption.dataset.userPhone || '-';
                document.getElementById('prk_display_room_name').textContent = selectedOption.dataset.roomName || '-';
                document.getElementById('prk_display_property_name').textContent = selectedOption.dataset.propertyName || '-';
                document.getElementById('prk_display_checkin_checkout').textContent =
                    (selectedOption.dataset.checkIn || '-') + ' - ' + (selectedOption.dataset.checkOut || '-');
                orderInfoSection.classList.remove('hidden');

                // Auto-calculate parking duration
                prkCalculateParkingDuration(selectedOption.dataset.checkIn, selectedOption.dataset.checkOut, selectedOption.dataset.maxParkingMonths);

                // Show parking status badge (informational only)
                showPrkParkingStatusBadge(selectedOption.dataset.parkingStatus || 'new', selectedOption.dataset.parkingInfo || '', selectedOption.dataset.activeParkings || '[]');

                checkPrkParkingQuota();
            } else {
                orderInfoSection.classList.add('hidden');
                document.getElementById('prk_quota_info_section').classList.add('hidden');
                // Hide parking status badge
                document.getElementById('prk_parking_status_section').classList.add('hidden');
                // Reset duration and fee
                prkResetDurationAndFee();
            }
        });

        // Calculate parking duration from check-in/check-out
        function prkCalculateParkingDuration(checkInStr, checkOutStr, maxParkingMonths) {
            const durationInput = document.getElementById('add_prk_parking_duration');
            const maxHint = document.getElementById('prk_parking_duration_max_hint');
            const durationError = document.getElementById('add_prk_parking_duration_error');

            if (!checkInStr || !checkOutStr || checkOutStr === '-') {
                durationInput.value = 1;
                durationInput.removeAttribute('max');
                if (maxHint) maxHint.classList.add('hidden');
                return;
            }

            const checkIn = new Date(checkInStr);
            const checkOut = new Date(checkOutStr);

            if (isNaN(checkIn.getTime()) || isNaN(checkOut.getTime())) {
                durationInput.value = 1;
                durationInput.removeAttribute('max');
                if (maxHint) maxHint.classList.add('hidden');
                return;
            }

            let months = (checkOut.getFullYear() - checkIn.getFullYear()) * 12 +
                (checkOut.getMonth() - checkIn.getMonth());
            if (checkOut.getDate() > checkIn.getDate()) months++;
            months = Math.max(1, months);

            const maxMonths = maxParkingMonths ? parseInt(maxParkingMonths) : months;
            durationInput.value = Math.min(months, maxMonths);
            durationInput.setAttribute('max', maxMonths);

            if (maxHint) {
                maxHint.textContent = `Max ${maxMonths} month(s) based on stay period (${checkInStr} - ${checkOutStr})`;
                maxHint.classList.remove('hidden');
            }
            if (durationError) durationError.classList.add('hidden');
        }

        // Validate parking duration on input
        document.getElementById('add_prk_parking_duration')?.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const val = parseInt(this.value);
            const errorEl = document.getElementById('add_prk_parking_duration_error');

            if (max && val > max) {
                if (errorEl) {
                    errorEl.textContent = `Parking duration cannot exceed ${max} month(s) (stay duration)`;
                    errorEl.classList.remove('hidden');
                }
                this.classList.add('border-red-500');
            } else {
                if (errorEl) errorEl.classList.add('hidden');
                this.classList.remove('border-red-500');
            }
        });

        function prkSetFeeAmount(fee) {
            const displayInput = document.getElementById('add_prk_fee_amount_display');
            const hiddenInput = document.getElementById('add_prk_fee_amount');
            const feeSourceInfo = document.getElementById('prk_fee_source_info');

            if (fee !== null && fee !== undefined) {
                displayInput.value = parseInt(fee).toLocaleString('id-ID');
                hiddenInput.value = fee;
                if (feeSourceInfo) {
                    feeSourceInfo.textContent = 'Fee loaded from parking fee configuration';
                    feeSourceInfo.classList.remove('text-red-500');
                    feeSourceInfo.classList.add('text-gray-500');
                }
            } else {
                displayInput.value = '';
                hiddenInput.value = '';
                if (feeSourceInfo) {
                    feeSourceInfo.textContent = 'Fee will be loaded from parking fee configuration';
                    feeSourceInfo.classList.remove('text-red-500');
                    feeSourceInfo.classList.add('text-gray-500');
                }
            }
        }

        function prkResetDurationAndFee() {
            const durationInput = document.getElementById('add_prk_parking_duration');
            durationInput.value = 1;
            durationInput.removeAttribute('max');
            const maxHint = document.getElementById('prk_parking_duration_max_hint');
            if (maxHint) maxHint.classList.add('hidden');
            prkSetFeeAmount(null);
        }

        // Check parking quota availability
        function checkPrkParkingQuota() {
            const orderSelect = document.getElementById('add_prk_order_id');
            const parkingTypeSelect = document.getElementById('add_prk_parking_type');
            const quotaInfoSection = document.getElementById('prk_quota_info_section');
            const quotaInfoContainer = document.getElementById('prk_quota_info_container');

            if (!orderSelect.value || !parkingTypeSelect.value) {
                quotaInfoSection.classList.add('hidden');
                return;
            }

            const selectedOption = orderSelect.options[orderSelect.selectedIndex];
            const propertyId = selectedOption.dataset.propertyId;
            if (!propertyId) { quotaInfoSection.classList.add('hidden'); return; }

            quotaInfoContainer.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Checking parking quota...</span>
                </div>`;
            quotaInfoSection.classList.remove('hidden');

            fetch(`/api/parking-fees/${propertyId}`)
                .then(r => r.json())
                .then(data => {
                    const parkingType = parkingTypeSelect.value;
                    const quotaData = data.find(pf => pf.parking_type === parkingType);

                    if (quotaData) {
                        displayPrkQuotaInfo(quotaData, parkingType);
                    } else {
                        displayPrkNoQuotaData(parkingType);
                    }
                })
                .catch(error => {
                    console.error('Error fetching quota:', error);
                    quotaInfoContainer.innerHTML = `<div class="text-sm text-yellow-600 dark:text-yellow-400">Unable to fetch quota information</div>`;
                });
        }

        function displayPrkQuotaInfo(quotaData, parkingType) {
            const container = document.getElementById('prk_quota_info_container');
            const typeLabel = parkingType === 'car' ? '{{ __("ui.car") }}' : '{{ __("ui.motorcycle") }}';

            if (quotaData.capacity === 0) {
                container.className = 'rounded-lg border-2 border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-4';
                container.innerHTML = `
                    <div class="flex items-start gap-3">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-green-900 dark:text-green-100">Unlimited Parking (${typeLabel})</h4>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">This property has unlimited parking quota.</p>
                        </div>
                    </div>`;
                prkSetFeeAmount(quotaData.fee);
            } else {
                const available = quotaData.available_quota;
                const percentage = quotaData.quota_percentage;

                if (available > 0) {
                    container.className = 'rounded-lg border-2 border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 p-4';
                    container.innerHTML = `
                        <div class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Parking Quota Available (${typeLabel})</h4>
                                <div class="mt-2 space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-blue-700 dark:text-blue-300">Available:</span>
                                        <span class="text-sm font-bold text-blue-900 dark:text-blue-100">${available} / ${quotaData.capacity}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-blue-700 dark:text-blue-300">In Use:</span>
                                        <span class="text-sm font-semibold text-blue-800 dark:text-blue-200">${quotaData.quota_used}</span>
                                    </div>
                                    <div class="w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                        <div class="h-2 rounded-full transition-all ${percentage >= 90 ? 'bg-red-600' : percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500'}"
                                             style="width: ${Math.min(percentage, 100)}%"></div>
                                    </div>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">${Math.round(percentage)}% used</p>
                                </div>
                            </div>
                        </div>`;
                    prkSetFeeAmount(quotaData.fee);
                } else {
                    // Quota full (info only, not blocking)
                    container.className = 'rounded-lg border-2 border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 p-4';
                    container.innerHTML = `
                        <div class="flex items-start gap-3">
                            <svg class="h-6 w-6 text-orange-600 dark:text-orange-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-orange-900 dark:text-orange-100">⚠️ Parking Quota Full (${typeLabel})</h4>
                                <p class="text-xs text-orange-700 dark:text-orange-300 mt-1"><strong>Capacity: ${quotaData.capacity}</strong> (All slots occupied)</p>
                            </div>
                        </div>`;
                    prkSetFeeAmount(quotaData.fee);
                }
            }
        }

        function displayPrkNoQuotaData(parkingType) {
            const container = document.getElementById('prk_quota_info_container');
            const typeLabel = parkingType === 'car' ? '{{ __("ui.car") }}' : '{{ __("ui.motorcycle") }}';

            container.className = 'rounded-lg border-2 border-yellow-200 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 p-4';
            container.innerHTML = `
                <div class="flex items-start gap-3">
                    <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">⚠️ Parking Fee Not Configured (${typeLabel})</h4>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">Parking fee for this property has not been configured yet. Fee amount must be entered manually.</p>
                    </div>
                </div>`;
            prkSetFeeAmount(null);
        }

        function clearAddParkingErrors() {
            document.querySelectorAll('[id^="add_prk_"][id$="_error"]').forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });
            document.querySelectorAll('#addParkingForm input, #addParkingForm select').forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        function showAddParkingError(field, message) {
            const errorEl = document.getElementById('add_prk_' + field + '_error');
            const inputEl = document.getElementById('add_prk_' + field);
            if (errorEl) { errorEl.textContent = message; errorEl.classList.remove('hidden'); }
            if (inputEl) { inputEl.classList.add('border-red-500'); }
        }

        // Form submission
        document.getElementById('addParkingForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAddParkingErrors();

            const submitBtn = document.getElementById('addParkingSubmitBtn');
            const submitText = document.getElementById('addParkingSubmitText');
            const spinner = document.getElementById('addParkingSpinner');

            // Validate duration max before submit
            const durationInput = document.getElementById('add_prk_parking_duration');
            const maxDuration = parseInt(durationInput.getAttribute('max'));
            const durationVal = parseInt(durationInput.value);
            if (maxDuration && durationVal > maxDuration) {
                showAddParkingError('parking_duration', `Parking duration cannot exceed ${maxDuration} month(s) (stay duration)`);
                return;
            }
            if (!durationVal || durationVal < 1) {
                showAddParkingError('parking_duration', 'Parking duration must be at least 1 month');
                return;
            }

            submitBtn.disabled = true;
            submitText.textContent = 'Processing...';
            spinner.classList.remove('hidden');

            try {
                const formData = new FormData(this);

                const response = await fetch('/properties/parking/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        order_id: formData.get('order_id'),
                        parking_type: formData.get('parking_type'),
                        vehicle_plate: formData.get('vehicle_plate'),
                        parking_duration: formData.get('parking_duration'),
                        fee_amount: formData.get('fee_amount'),
                        notes: formData.get('notes'),
                    })
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeAddParkingModal();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: result.message, showConfirmButton: false, timer: 3000, timerProgressBar: true });
                    applyFilters();
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const messages = Array.isArray(result.errors[field]) ? result.errors[field] : [result.errors[field]];
                            showAddParkingError(field, messages[0]);
                        });
                    }
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: result.message || 'Failed to add parking.', showConfirmButton: false, timer: 3000, timerProgressBar: true });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'An unexpected error occurred.', showConfirmButton: false, timer: 3000, timerProgressBar: true });
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Add Parking';
                spinner.classList.add('hidden');
            }
        });

        // ESC key for add parking modal
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !document.getElementById('addParkingModal').classList.contains('hidden')) {
                closeAddParkingModal();
            }
        });

        // ==================== Existing Functions ====================
        function toggleParkingStatus(checkbox) {
            const id = checkbox.dataset.id;
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');

            fetch('/properties/parking/toggle-status', {
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

        function deleteParking(id) {
            Swal.fire({
                title: '{{ __("ui.confirm_delete") }}',
                text: '{{ __("ui.confirm_delete_parking") }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("ui.yes_delete") }}',
                cancelButtonText: '{{ __("ui.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/properties/parking/destroy/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                            applyFilters();
                        } else {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                        }
                    });
                }
            });
        }

        function restoreParking(id) {
            Swal.fire({
                title: '{{ __("ui.confirmation") }}',
                text: '{{ __("ui.confirm_restore_parking") }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __("ui.yes_restore") }}',
                cancelButtonText: '{{ __("ui.cancel") }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/properties/parking/restore/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                            applyFilters();
                        } else {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: data.message, showConfirmButton: false, timer: 3000 });
                        }
                    });
                }
            });
        }

        function applyFilters(page = 1) {
            const search = document.getElementById('searchInput')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';
            const parkingType = document.getElementById('parkingTypeFilter')?.value || '';
            const perPage = document.getElementById('perPageSelect')?.value || '8';
            const showDeleted = document.getElementById('showDeletedFilter')?.checked ? '1' : '0';

            fetch('/properties/parking/filter', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ search, status, parking_type: parkingType, per_page: perPage, show_deleted: showDeleted, page })
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('tableContainer').innerHTML = data.html;
                const pag = document.getElementById('paginationContainer');
                if (pag) {
                    pag.innerHTML = data.pagination || '';
                    // Intercept pagination link clicks to keep filters
                    pag.querySelectorAll('a[href]').forEach(link => {
                        link.addEventListener('click', function(e) {
                            e.preventDefault();
                            const url = new URL(this.href);
                            const p = url.searchParams.get('page') || 1;
                            applyFilters(parseInt(p));
                        });
                    });
                }

                const urlParams = new URLSearchParams();
                if (search) urlParams.append('search', search);
                if (status) urlParams.append('status', status);
                if (parkingType) urlParams.append('parking_type', parkingType);
                if (perPage) urlParams.append('per_page', perPage);
                if (page > 1) urlParams.append('page', page);

                window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let t;
            document.getElementById('searchInput')?.addEventListener('input', () => { clearTimeout(t); t = setTimeout(() => applyFilters(1), 500); });
            document.getElementById('statusFilter')?.addEventListener('change', () => applyFilters(1));
            document.getElementById('parkingTypeFilter')?.addEventListener('change', () => applyFilters(1));
            document.getElementById('perPageSelect')?.addEventListener('change', () => applyFilters(1));
            document.getElementById('showDeletedFilter')?.addEventListener('change', () => applyFilters(1));

            // Intercept pagination links on initial page load
            const pag = document.getElementById('paginationContainer');
            if (pag) {
                pag.querySelectorAll('a[href]').forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = new URL(this.href);
                        const p = url.searchParams.get('page') || 1;
                        applyFilters(parseInt(p));
                    });
                });
            }
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('editParkingModal', () => ({
                isOpen: false, isSubmitting: false,
                parking: { id: null, parking_type: '', vehicle_plate: '', owner_name: '', owner_phone: '', notes: '', property_name: '', property_id: null, original_parking_type: '' },
                feeInfo: { show: false, changed: false, title: '', feeFormatted: '', oldFeeFormatted: '', oldTypeLabel: '' },
                feesCache: {},
                init() {
                    window.addEventListener('open-edit-parking-modal', (e) => {
                        const d = e.detail;
                        this.parking = {
                            id: d.idrec,
                            parking_type: d.parking_type,
                            vehicle_plate: d.vehicle_plate,
                            owner_name: d.owner_name || '',
                            owner_phone: d.owner_phone || '',
                            notes: d.notes || '',
                            property_name: d.property?.name || '-',
                            property_id: d.property_id,
                            original_parking_type: d.parking_type
                        };
                        this.feeInfo = { show: false, changed: false, title: '', feeFormatted: '', oldFeeFormatted: '', oldTypeLabel: '', availableQuota: 0, capacity: 0, quotaPercentage: 0, quotaFull: false };
                        this.feesCache = {};
                        this.isOpen = true;
                        this.fetchFeeInfo();
                    });
                },
                closeModal() { this.isOpen = false; },
                async fetchFeeInfo() {
                    if (!this.parking.property_id || !this.parking.parking_type) return;

                    try {
                        let fees = this.feesCache[this.parking.property_id];
                        if (!fees) {
                            const r = await fetch(`/api/parking-fees/${this.parking.property_id}`);
                            fees = await r.json();
                            this.feesCache[this.parking.property_id] = fees;
                        }

                        const currentFee = fees.find(f => f.parking_type === this.parking.parking_type);
                        const oldFee = fees.find(f => f.parking_type === this.parking.original_parking_type);
                        const typeChanged = this.parking.parking_type !== this.parking.original_parking_type;
                        const typeLabel = (t) => t === 'car' ? '{{ __("ui.car") }}' : '{{ __("ui.motorcycle") }}';
                        const formatRp = (v) => 'Rp ' + parseInt(v).toLocaleString('id-ID');

                        if (currentFee) {
                            const quotaFull = typeChanged && currentFee.capacity > 0 && currentFee.available_quota === 0;
                            this.feeInfo = {
                                show: true,
                                changed: typeChanged,
                                title: typeChanged
                                    ? 'Perubahan type ke ' + typeLabel(this.parking.parking_type)
                                    : 'Fee ' + typeLabel(this.parking.parking_type),
                                feeFormatted: formatRp(currentFee.fee),
                                oldFeeFormatted: oldFee ? formatRp(oldFee.fee) : '-',
                                oldTypeLabel: typeLabel(this.parking.original_parking_type),
                                availableQuota: currentFee.available_quota ?? 0,
                                capacity: currentFee.capacity ?? 0,
                                quotaPercentage: currentFee.quota_percentage ?? 0,
                                quotaFull: quotaFull
                            };
                        } else {
                            this.feeInfo = {
                                show: true,
                                changed: true,
                                title: 'Fee ' + typeLabel(this.parking.parking_type) + ' belum dikonfigurasi',
                                feeFormatted: '-',
                                oldFeeFormatted: oldFee ? formatRp(oldFee.fee) : '-',
                                oldTypeLabel: typeLabel(this.parking.original_parking_type),
                                availableQuota: 0,
                                capacity: 0,
                                quotaPercentage: 0,
                                quotaFull: false
                            };
                        }
                    } catch (e) {
                        console.error('Error fetching fee info:', e);
                    }
                },
                async submitEditForm() {
                    this.isSubmitting = true;
                    try {
                        const r = await fetch(`/properties/parking/update/${this.parking.id}`, {
                            method: 'PUT',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                parking_type: this.parking.parking_type,
                                vehicle_plate: this.parking.vehicle_plate,
                                owner_name: this.parking.owner_name,
                                owner_phone: this.parking.owner_phone,
                                notes: this.parking.notes
                            })
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
