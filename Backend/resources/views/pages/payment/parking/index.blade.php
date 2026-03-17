<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.parking_payments') }}
            </h1>
            <button type="button" onclick="openAddPaymentModal()"
                class="mt-4 md:mt-0 inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New Payment
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <!-- Filter Section -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <form id="filterForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="w-full md:w-1/4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md sm:text-sm"
                                    placeholder="{{ __('ui.search_parking_placeholder') }}">
                            </div>
                        </div>

                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.parking_type') }}:</span>
                            <select name="parking_type" id="parkingTypeFilter"
                                class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-md text-sm">
                                <option value="">{{ __('ui.all') }}</option>
                                <option value="car" {{ request('parking_type') == 'car' ? 'selected' : '' }}>
                                    {{ __('ui.car') }}</option>
                                <option value="motorcycle"
                                    {{ request('parking_type') == 'motorcycle' ? 'selected' : '' }}>
                                    {{ __('ui.motorcycle') }}</option>
                            </select>
                        </div>

                        <div class="flex items-center gap-2">
                            <label
                                class="text-sm text-gray-600 dark:text-gray-400">{{ __('ui.items_per_page') }}</label>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-200 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm">
                                <option value="8" {{ request('per_page', 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page', 8) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 8) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto" id="tableContainer">
                @include('pages.payment.parking.partials.parking-payment_table', [
                    'parkingTransactions' => $parkingTransactions,
                ])
            </div>

            @if ($parkingTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="bg-gray-50 dark:bg-gray-800 rounded p-4" id="paginationContainer">
                    {{ $parkingTransactions->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Proof + Konfirmasi Modal --}}
    <div id="proofModal" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeParkingProofModal()">
        </div>

        {{-- Dialog --}}
        <div class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[95vh] flex flex-col"
                onclick="event.stopPropagation()">

                {{-- Header --}}
                <div
                    class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        {{ __('ui.payment_proof') }} - {{ __('ui.order_id') }} #<span id="proofOrderId"></span>
                    </h3>
                    <button onclick="closeParkingProofModal()"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="overflow-y-auto flex-1 p-6" id="proofContent">
                    <div class="flex justify-center items-center h-64">
                        <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                </div>

                {{-- Footer with Approve/Reject buttons (shown for waiting status) --}}
                <div id="proofFooter"
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-between items-center hidden">
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span>{{ __('ui.press_esc_to_close') }}</span>
                    </div>
                    <div class="flex space-x-3">
                        {{-- Approve --}}
                        <button type="button" id="btnApproveParking"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors"
                            onclick="confirmApproveParkingPayment()">
                            {{ __('ui.approve') }}
                        </button>
                        {{-- Reject --}}
                        <button type="button" id="btnRejectParking"
                            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors"
                            onclick="openRejectParkingModal()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            <span>{{ __('ui.reject') }}</span>
                        </button>
                    </div>
                </div>

                {{-- View-only footer (shown for paid/verified status) --}}
                <div id="proofFooterViewOnly"
                    class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-end hidden">
                    <button type="button" onclick="closeParkingProofModal()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                        {{ __('ui.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add New Payment Modal --}}
    <div id="addPaymentModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeAddPaymentModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden"
                onclick="event.stopPropagation()">
                <!-- Header -->
                <div
                    class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-indigo-600 to-blue-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Add New Parking Payment</h3>
                            <p class="text-white/80 text-sm">Create a new parking payment record</p>
                        </div>
                        <button onclick="closeAddPaymentModal()"
                            class="text-white hover:text-indigo-200 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
                    <form id="addPaymentForm" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Booking Order (Check-in) -->
                            <div class="md:col-span-2">
                                <label for="add_order_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Booking Order (Checked-In) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mb-1.5">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="order_search_input" placeholder="Search order ID, name, or room..."
                                        oninput="filterOrderOptions(this.value)"
                                        onfocus="openOrderDropdown()"
                                        onblur="setTimeout(() => closeOrderDropdown(), 200)"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <button type="button" id="order_search_clear" onclick="clearOrderSearch()" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <select name="order_id" id="add_order_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <option value="">Loading checked-in orders...</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Search then select a booking that is currently checked-in</p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_order_id_error"></p>
                            </div>

                            <!-- User Info Display (Read-only) -->
                            <div class="md:col-span-2 bg-blue-50 dark:bg-gray-700 p-3 rounded-lg hidden"
                                id="order_info_section">
                                <div class="flex items-start gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">Booking
                                            Information</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                            <p><span class="font-medium">Customer:</span> <span
                                                    id="display_user_name">-</span></p>
                                            <p><span class="font-medium">Phone:</span> <span
                                                    id="display_user_phone">-</span></p>
                                            <p><span class="font-medium">Room:</span> <span
                                                    id="display_room_name">-</span></p>
                                            <p><span class="font-medium">Property:</span> <span
                                                    id="display_property_name">-</span></p>
                                            <p><span class="font-medium">Check-in - Check-out:</span> <span
                                                    id="display_checkin_checkout">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parking Status Info -->
                            <div class="md:col-span-2 hidden" id="parking_status_section"></div>

                            <!-- Parking Type -->
                            <div class="md:col-span-2">
                                <label for="add_parking_type"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Parking Type <span class="text-red-500">*</span>
                                </label>
                                <select name="parking_type" id="add_parking_type" required
                                    onchange="checkParkingQuota()"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <option value="">Select Type</option>
                                    <option value="car">{{ __('ui.car') }}</option>
                                    <option value="motorcycle">{{ __('ui.motorcycle') }}</option>
                                </select>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_parking_type_error"></p>
                            </div>

                            <!-- Parking Quota Information -->
                            <div class="md:col-span-2 hidden" id="quota_info_section">
                                <div class="rounded-lg border-2 p-4" id="quota_info_container">
                                    <!-- Content will be filled by JavaScript -->
                                </div>
                            </div>

                            <!-- Vehicle Plate and Parking Duration side by side -->
                            <div>
                                <label for="add_vehicle_plate"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Vehicle Plate <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="vehicle_plate" id="add_vehicle_plate" required
                                    placeholder="e.g., B 1234 XYZ" oninput="this.value = this.value.toUpperCase()"
                                    style="text-transform: uppercase;"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_vehicle_plate_error"></p>
                            </div>

                            <div>
                                <label for="add_parking_duration"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Parking Duration (months) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="parking_duration" id="add_parking_duration" required
                                    min="1" value="1" placeholder="e.g., 1"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-xs text-blue-600 mt-1 hidden" id="parking_duration_max_hint"></p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_parking_duration_error"></p>
                            </div>

                            <!-- Fee Amount (readonly, full width) -->
                            <div class="md:col-span-2">
                                <label for="add_fee_amount_display"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Fee Amount (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="fee_amount_display" id="add_fee_amount_display" required
                                    placeholder="Select parking type first" readonly
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm bg-gray-50 dark:bg-gray-600 cursor-not-allowed">
                                <input type="hidden" name="fee_amount" id="add_fee_amount">
                                <p class="text-xs text-gray-500 mt-1" id="fee_source_info">Fee will be loaded from parking fee configuration</p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_fee_amount_error"></p>
                            </div>

                            <!-- Transaction Date and Payment Proof in one row -->
                            <div>
                                <label for="add_transaction_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Transaction Date <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="transaction_date" id="add_transaction_date"
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_transaction_date_error"></p>
                            </div>

                            <!-- Payment Proof -->
                            <div>
                                <label for="add_payment_proof"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Payment Proof (JPG only, max 5MB) <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="payment_proof" id="add_payment_proof" required
                                    accept="image/jpeg,image/jpg" onchange="validateImage(this)"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-xs text-gray-500 mt-1">Only JPG/JPEG format, maximum 5MB</p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_payment_proof_error"></p>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="add_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Notes (Optional)
                                </label>
                                <textarea name="notes" id="add_notes" rows="3" placeholder="Enter additional notes..."
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeAddPaymentModal()"
                                class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" id="addPaymentSubmitBtn"
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span id="addPaymentSubmitText">Add Payment</span>
                                <svg id="addPaymentSpinner" class="animate-spin h-4 w-4 text-white hidden"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Reject Reason Modal --}}
    <div id="rejectModal" class="fixed inset-0 z-[60] hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRejectParkingModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-md w-full"
                onclick="event.stopPropagation()">
                <div
                    class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 rounded-t-xl flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('ui.reject') }}
                        {{ __('ui.parking_payments') }}</h3>
                    <button onclick="closeRejectParkingModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <form id="rejectForm" class="max-w-full overflow-hidden">
                    <input type="hidden" id="rejectTransactionId">
                    <div class="p-6 space-y-4">
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ __('ui.confirm_reject_parking_msg') }}
                            <strong class="text-gray-900 dark:text-gray-100" id="rejectOrderIdLabel"></strong>?
                        </p>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('ui.rejection_reason') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea id="rejectNotes" rows="4" required minlength="10"
                                class="w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                                placeholder="{{ __('ui.enter_rejection_reason') }}"></textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ __('ui.min_10_chars') }}</p>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-end p-6 space-x-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                        <button type="button" onclick="closeRejectParkingModal()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                            {{ __('ui.cancel') }}
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ __('ui.confirm_reject') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let currentProofTransactionId = null;
        let currentProofOrderId = null;

        // Open proof modal with images loaded via AJAX
        function openParkingProofModal(id, orderId, viewOnly = false) {
            currentProofTransactionId = id;
            currentProofOrderId = orderId;

            const modal = document.getElementById('proofModal');
            const content = document.getElementById('proofContent');
            const footer = document.getElementById('proofFooter');
            const footerViewOnly = document.getElementById('proofFooterViewOnly');

            modal.classList.remove('hidden');
            document.getElementById('proofOrderId').textContent = orderId;
            document.body.style.overflow = 'hidden';

            // Show appropriate footer
            if (viewOnly) {
                footer.classList.add('hidden');
                footerViewOnly.classList.remove('hidden');
            } else {
                footer.classList.remove('hidden');
                footerViewOnly.classList.add('hidden');
            }

            // Loading spinner
            content.innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <svg class="animate-spin h-12 w-12 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>`;

            fetch(`/payment/parking/proof/${id}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.images.length > 0) {
                        content.innerHTML = data.images.map(img => {
                            return `<div class="mb-4">
                                <img src="${img.image_url}" alt="{{ __('ui.payment_proof') }}" class="mx-auto max-h-[70vh] max-w-full object-contain rounded-lg shadow" onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22200%22 height=%22200%22><text x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 dy=%22.3em%22>Image not found</text></svg>';" />
                                ${img.description ? `<p class="text-sm text-gray-500 mt-1 text-center">${img.description}</p>` : ''}
                            </div>`;
                        }).join('');
                    } else {
                        content.innerHTML = `<div class="text-center py-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-gray-500">{{ __('ui.no_proof_available') }}</p>
                        </div>`;
                    }
                })
                .catch(() => {
                    content.innerHTML =
                        `<div class="text-center py-10 text-red-500">{{ __('ui.error_loading') }}</div>`;
                });
        }

        function closeParkingProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentProofTransactionId = null;
            currentProofOrderId = null;
        }

        // Approve with SweetAlert confirm
        function confirmApproveParkingPayment() {
            if (!currentProofTransactionId) return;
            const id = currentProofTransactionId;

            Swal.fire({
                title: '{{ __('ui.confirm_approve') }}',
                text: '{{ __('ui.confirm_approve_msg') }}',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                confirmButtonText: '{{ __('ui.yes_approve') }}',
                cancelButtonText: '{{ __('ui.cancel') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('btnApproveParking');
                    const origHTML = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML =
                        `<svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('ui.processing') }}`;

                    fetch(`/payment/parking/approve/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeParkingProofModal();
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                applyFilters();
                            } else {
                                throw new Error(data.message || '{{ __('ui.error_loading') }}');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: error.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            btn.innerHTML = origHTML;
                            btn.disabled = false;
                        });
                }
            });
        }

        // Open reject reason modal (from proof modal footer)
        function openRejectParkingModal() {
            document.getElementById('rejectTransactionId').value = currentProofTransactionId;
            document.getElementById('rejectOrderIdLabel').textContent = currentProofOrderId;
            document.getElementById('rejectNotes').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('rejectNotes').focus(), 150);
        }

        function closeRejectParkingModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }

        // Reject form submit
        document.getElementById('rejectForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('rejectTransactionId').value;
            const notes = document.getElementById('rejectNotes').value.trim();

            if (notes.length < 10) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: '{{ __('ui.min_10_chars') }}',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }

            Swal.fire({
                title: '{{ __('ui.confirm_reject') }}',
                text: '{{ __('ui.confirm_reject_msg') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                confirmButtonText: '{{ __('ui.yes_reject') }}',
                cancelButtonText: '{{ __('ui.cancel') }}',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = document.querySelector('#rejectForm button[type="submit"]');
                    const origHTML = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML =
                        `<svg class="animate-spin h-4 w-4 mr-1 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('ui.processing') }}`;

                    fetch(`/payment/parking/reject/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                notes: notes
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeRejectParkingModal();
                                closeParkingProofModal();
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: data.message,
                                    showConfirmButton: false,
                                    timer: 3000,
                                    timerProgressBar: true
                                });
                                applyFilters();
                            } else {
                                throw new Error(data.message || '{{ __('ui.error_loading') }}');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: error.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            submitBtn.innerHTML = origHTML;
                            submitBtn.disabled = false;
                        });
                }
            });
        });

        // AJAX filter using POST /filter endpoint
        function applyFilters() {
            const search = document.getElementById('searchInput')?.value || '';
            const parkingType = document.getElementById('parkingTypeFilter')?.value || '';
            const perPage = document.getElementById('perPageSelect')?.value || '8';

            fetch('/payment/parking/filter', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        search,
                        parking_type: parkingType,
                        per_page: perPage
                    })
                })
                .then(r => r.json())
                .then(data => {
                    document.getElementById('tableContainer').innerHTML = data.html;
                    const pag = document.getElementById('paginationContainer');
                    if (pag) pag.innerHTML = data.pagination || '';

                    const urlParams = new URLSearchParams();
                    if (search) urlParams.append('search', search);
                    if (parkingType) urlParams.append('parking_type', parkingType);
                    if (perPage) urlParams.append('per_page', perPage);
                    window.history.pushState({}, '', `${window.location.pathname}?${urlParams.toString()}`);
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let t;
            document.getElementById('searchInput')?.addEventListener('input', () => {
                clearTimeout(t);
                t = setTimeout(applyFilters, 500);
            });
            document.getElementById('parkingTypeFilter')?.addEventListener('change', applyFilters);
            document.getElementById('perPageSelect')?.addEventListener('change', applyFilters);
        });

        // ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('rejectModal').classList.contains('hidden')) {
                    closeRejectParkingModal();
                } else if (!document.getElementById('proofModal').classList.contains('hidden')) {
                    closeParkingProofModal();
                } else if (!document.getElementById('addPaymentModal').classList.contains('hidden')) {
                    closeAddPaymentModal();
                }
            }
        });

        // Store checked-in orders data
        let checkedInOrdersData = [];

        // Add Payment Modal Functions
        function openAddPaymentModal() {
            document.getElementById('addPaymentModal').classList.remove('hidden');
            document.getElementById('addPaymentForm').reset();
            clearAddPaymentErrors();
            // Clear fee amount fields
            document.getElementById('add_fee_amount_display').value = '';
            document.getElementById('add_fee_amount').value = '';
            // Hide order info, quota, and status badge sections
            document.getElementById('order_info_section').classList.add('hidden');
            document.getElementById('quota_info_section').classList.add('hidden');
            document.getElementById('parking_status_section').classList.add('hidden');
            // Reset parking duration max hint
            const maxHint = document.getElementById('parking_duration_max_hint');
            if (maxHint) maxHint.classList.add('hidden');
            document.getElementById('add_parking_duration').removeAttribute('max');
            document.getElementById('add_parking_duration').value = 1;
            // Clear order search
            const searchInput = document.getElementById('order_search_input');
            if (searchInput) {
                searchInput.value = '';
                document.getElementById('order_search_clear').classList.add('hidden');
            }
            closeOrderDropdown();
            // Set default transaction date to now
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('add_transaction_date').value = now.toISOString().slice(0, 16);
            document.body.style.overflow = 'hidden';

            // Load checked-in orders
            loadCheckedInOrders();
        }

        function loadCheckedInOrders() {
            const orderSelect = document.getElementById('add_order_id');
            orderSelect.innerHTML = '<option value="">Loading checked-in orders...</option>';

            fetch('/payment/parking/checked-in-orders', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        checkedInOrdersData = data.data;
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

        function openOrderDropdown() {
            const orderSelect = document.getElementById('add_order_id');
            const count = orderSelect.options.length;
            orderSelect.size = Math.min(Math.max(count, 2), 7);
        }

        function closeOrderDropdown() {
            const orderSelect = document.getElementById('add_order_id');
            orderSelect.size = 1;
        }

        function selectActiveParkingVehicle(vehiclePlate, parkingType) {
            const plateInput = document.getElementById('add_vehicle_plate');
            const typeSelect = document.getElementById('add_parking_type');
            if (plateInput) plateInput.value = vehiclePlate;
            if (typeSelect) {
                typeSelect.value = parkingType;
                typeSelect.dispatchEvent(new Event('change'));
            }
            // Highlight selected chip
            document.querySelectorAll('.active-parking-chip').forEach(el => {
                el.classList.remove('ring-2', 'ring-orange-500');
            });
            event.currentTarget.classList.add('ring-2', 'ring-orange-500');
        }

        function showParkingStatusBadge(status, parkingInfoStr, activeParkingsStr) {
            const section = document.getElementById('parking_status_section');
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
                        return `<button type="button" onclick="selectActiveParkingVehicle('${p.vehicle_plate}', '${p.parking_type}')"
                            class="active-parking-chip inline-flex items-center gap-1.5 px-2.5 py-1 bg-orange-100 dark:bg-orange-800 text-orange-800 dark:text-orange-100 rounded text-xs font-medium hover:bg-orange-200 dark:hover:bg-orange-700 transition-colors cursor-pointer border border-orange-300 dark:border-orange-600">
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

        function filterOrderOptions(keyword) {
            const orderSelect = document.getElementById('add_order_id');
            const clearBtn = document.getElementById('order_search_clear');
            const q = keyword.trim().toLowerCase();

            // Toggle clear button
            if (clearBtn) clearBtn.classList.toggle('hidden', q === '');

            // Reset selection & info when filter changes
            orderSelect.value = '';
            document.getElementById('order_info_section').classList.add('hidden');
            document.getElementById('quota_info_section').classList.add('hidden');
            document.getElementById('parking_status_section').classList.add('hidden');

            const filtered = q === ''
                ? checkedInOrdersData
                : checkedInOrdersData.filter(o =>
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

        function clearOrderSearch() {
            const searchInput = document.getElementById('order_search_input');
            searchInput.value = '';
            filterOrderOptions('');
            searchInput.focus();
        }

        // Handle order selection
        document.getElementById('add_order_id')?.addEventListener('change', function() {
            closeOrderDropdown();
            const selectedOption = this.options[this.selectedIndex];
            const orderInfoSection = document.getElementById('order_info_section');

            if (this.value && selectedOption.dataset.userName) {
                // Show and populate order info
                document.getElementById('display_user_name').textContent = selectedOption.dataset.userName || '-';
                document.getElementById('display_user_phone').textContent = selectedOption.dataset.userPhone || '-';
                document.getElementById('display_room_name').textContent = selectedOption.dataset.roomName || '-';
                document.getElementById('display_property_name').textContent = selectedOption.dataset
                    .propertyName || '-';
                document.getElementById('display_checkin_checkout').textContent =
                    (selectedOption.dataset.checkIn || '-') + ' - ' + (selectedOption.dataset.checkOut || '-');
                orderInfoSection.classList.remove('hidden');

                // Auto-calculate parking duration in months from check-in to check-out
                calculateParkingDuration(selectedOption.dataset.checkIn, selectedOption.dataset.checkOut, selectedOption.dataset.maxParkingMonths);

                // Show parking status badge (informational only)
                showParkingStatusBadge(selectedOption.dataset.parkingStatus || 'new', selectedOption.dataset.parkingInfo || '', selectedOption.dataset.activeParkings || '[]');

                // Check parking quota when order is selected
                checkParkingQuota();
            } else {
                // Hide order info
                orderInfoSection.classList.add('hidden');
                // Hide quota info
                document.getElementById('quota_info_section').classList.add('hidden');
                // Hide parking status badge
                document.getElementById('parking_status_section').classList.add('hidden');
                // Clear fee amount
                setFeeAmount(null);
                // Reset parking duration max
                const durationInput = document.getElementById('add_parking_duration');
                durationInput.removeAttribute('max');
                durationInput.value = 1;
                const maxHint = document.getElementById('parking_duration_max_hint');
                if (maxHint) maxHint.classList.add('hidden');
            }
        });

        // Calculate parking duration in months from check-in and check-out dates
        function calculateParkingDuration(checkInStr, checkOutStr, maxParkingMonths) {
            const durationInput = document.getElementById('add_parking_duration');
            const durationError = document.getElementById('add_parking_duration_error');
            const maxHint = document.getElementById('parking_duration_max_hint');

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

            // Calculate difference in months
            let months = (checkOut.getFullYear() - checkIn.getFullYear()) * 12 +
                (checkOut.getMonth() - checkIn.getMonth());

            // If there are remaining days, round up to next month
            if (checkOut.getDate() > checkIn.getDate()) {
                months++;
            }

            // Minimum 1 month
            months = Math.max(1, months);

            // Set max parking months from server calculation
            const maxMonths = maxParkingMonths ? parseInt(maxParkingMonths) : months;
            durationInput.value = Math.min(months, maxMonths);
            durationInput.setAttribute('max', maxMonths);

            // Show max hint
            if (maxHint) {
                maxHint.textContent = `Max ${maxMonths} month(s) based on stay period (${checkInStr} - ${checkOutStr})`;
                maxHint.classList.remove('hidden');
            }

            // Clear any previous error
            if (durationError) durationError.classList.add('hidden');
        }

        // Validate parking duration on input change
        document.getElementById('add_parking_duration')?.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const val = parseInt(this.value);
            const errorEl = document.getElementById('add_parking_duration_error');

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

        // Check parking quota availability
        function checkParkingQuota() {
            const orderSelect = document.getElementById('add_order_id');
            const parkingTypeSelect = document.getElementById('add_parking_type');
            const quotaInfoSection = document.getElementById('quota_info_section');
            const quotaInfoContainer = document.getElementById('quota_info_container');

            // Need both order and parking type selected
            if (!orderSelect.value || !parkingTypeSelect.value) {
                quotaInfoSection.classList.add('hidden');
                setFeeAmount(null);
                return;
            }

            // Get property_id from selected option
            const selectedOption = orderSelect.options[orderSelect.selectedIndex];
            const propertyId = selectedOption.dataset.propertyId;

            if (!propertyId) {
                quotaInfoSection.classList.add('hidden');
                return;
            }

            // Show loading
            quotaInfoContainer.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Checking parking quota...</span>
                </div>
            `;
            quotaInfoSection.classList.remove('hidden');

            // Fetch parking quota info
            fetch(`/api/parking-fees/${propertyId}`)
                .then(r => r.json())
                .then(data => {
                    const parkingType = parkingTypeSelect.value;
                    const quotaData = data.find(pf => pf.parking_type === parkingType);

                    if (quotaData) {
                        displayQuotaInfo(quotaData, parkingType);
                    } else {
                        displayNoQuotaData(parkingType);
                    }
                })
                .catch(error => {
                    console.error('Error fetching quota:', error);
                    quotaInfoContainer.innerHTML = `
                        <div class="text-sm text-yellow-600 dark:text-yellow-400">
                            ⚠️ Unable to fetch quota information
                        </div>
                    `;
                });
        }

        function setFeeAmount(fee) {
            const displayInput = document.getElementById('add_fee_amount_display');
            const hiddenInput = document.getElementById('add_fee_amount');
            const feeSourceInfo = document.getElementById('fee_source_info');

            if (fee !== null && fee !== undefined) {
                const formatted = parseInt(fee).toLocaleString('id-ID');
                displayInput.value = formatted;
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

        function displayQuotaInfo(quotaData, parkingType) {
            const quotaInfoContainer = document.getElementById('quota_info_container');
            const typeLabel = parkingType === 'car' ? '{{ __('ui.car') }}' : '{{ __('ui.motorcycle') }}';
            const submitBtn = document.getElementById('addPaymentSubmitBtn');

            // Auto-fill fee amount from parking fee config
            setFeeAmount(quotaData.fee);

            if (quotaData.capacity === 0) {
                // Unlimited parking
                quotaInfoContainer.className =
                    'rounded-lg border-2 border-green-200 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-4';
                quotaInfoContainer.innerHTML = `
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-green-900 dark:text-green-100">Unlimited Parking (${typeLabel})</h4>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                This property has unlimited parking quota. You can create parking payment without restriction.
                            </p>
                            <p class="text-xs text-green-600 dark:text-green-400 mt-2">
                                💡 <strong>Note:</strong> Quota management is not enabled. Set capacity in Parking Fee Management to enable quota control.
                            </p>
                        </div>
                    </div>
                `;

            } else {
                // Limited parking with quota
                const available = quotaData.available_quota;
                const percentage = quotaData.quota_percentage;
                const isAvailable = available > 0;

                if (isAvailable) {
                    quotaInfoContainer.className =
                        'rounded-lg border-2 border-blue-200 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20 p-4';
                    quotaInfoContainer.innerHTML = `
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
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
                        </div>
                    `;
                } else {
                    // Quota full (info only, not blocking)
                    quotaInfoContainer.className =
                        'rounded-lg border-2 border-orange-200 dark:border-orange-700 bg-orange-50 dark:bg-orange-900/20 p-4';
                    quotaInfoContainer.innerHTML = `
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-orange-900 dark:text-orange-100">⚠️ Parking Quota Full (${typeLabel})</h4>
                                <p class="text-xs text-orange-700 dark:text-orange-300 mt-1">
                                    <strong>Capacity: ${quotaData.capacity}</strong> (All slots occupied)
                                </p>
                            </div>
                        </div>
                    `;
                }
            }
        }

        function displayNoQuotaData(parkingType) {
            const quotaInfoContainer = document.getElementById('quota_info_container');
            const typeLabel = parkingType === 'car' ? '{{ __('ui.car') }}' : '{{ __('ui.motorcycle') }}';

            // Clear fee amount when no parking fee configured
            setFeeAmount(null);

            quotaInfoContainer.className =
                'rounded-lg border-2 border-yellow-200 dark:border-yellow-700 bg-yellow-50 dark:bg-yellow-900/20 p-4';
            quotaInfoContainer.innerHTML = `
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-600 dark:text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-yellow-900 dark:text-yellow-100">⚠️ Parking Fee Not Configured (${typeLabel})</h4>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                            Parking fee for this property has not been configured yet. Fee amount must be entered manually.
                        </p>
                    </div>
                </div>
            `;
        }

        function closeAddPaymentModal() {
            document.getElementById('addPaymentModal').classList.add('hidden');
            document.body.style.overflow = '';
        }

        function formatFeeAmount(input) {
            // Remove all non-digit characters
            let value = input.value.replace(/\D/g, '');

            // Format with thousand separator (dot)
            let formatted = '';
            if (value) {
                formatted = parseInt(value).toLocaleString('id-ID');
            }

            // Update display input
            input.value = formatted;

            // Update hidden input with raw number
            document.getElementById('add_fee_amount').value = value;
        }

        function validateImage(input) {
            const file = input.files[0];
            const errorEl = document.getElementById('add_payment_proof_error');

            if (file) {
                // Check file type
                const validTypes = ['image/jpeg', 'image/jpg'];
                if (!validTypes.includes(file.type)) {
                    errorEl.textContent = 'Only JPG/JPEG format is allowed';
                    errorEl.classList.remove('hidden');
                    input.value = '';
                    return false;
                }

                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    errorEl.textContent = 'File size must not exceed 5MB';
                    errorEl.classList.remove('hidden');
                    input.value = '';
                    return false;
                }

                errorEl.classList.add('hidden');
                return true;
            }
        }

        function clearAddPaymentErrors() {
            const errorElements = document.querySelectorAll('[id^="add_"][id$="_error"]');
            errorElements.forEach(el => {
                el.textContent = '';
                el.classList.add('hidden');
            });

            const inputs = document.querySelectorAll(
                '#addPaymentForm input, #addPaymentForm select, #addPaymentForm textarea');
            inputs.forEach(input => {
                input.classList.remove('border-red-500');
            });
        }

        function showAddPaymentError(field, message) {
            const errorEl = document.getElementById('add_' + field + '_error');
            let inputEl = document.getElementById('add_' + field);

            // Special handling for fee_amount (use display input)
            if (field === 'fee_amount' && !inputEl) {
                inputEl = document.getElementById('add_fee_amount_display');
            }

            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }

            if (inputEl) {
                inputEl.classList.add('border-red-500');
            }
        }

        function setAddPaymentLoading(loading) {
            const submitBtn = document.getElementById('addPaymentSubmitBtn');
            const submitText = document.getElementById('addPaymentSubmitText');
            const spinner = document.getElementById('addPaymentSpinner');

            submitBtn.disabled = loading;
            if (loading) {
                submitText.textContent = 'Processing...';
                spinner.classList.remove('hidden');
            } else {
                submitText.textContent = 'Add Payment';
                spinner.classList.add('hidden');
            }
        }

        // Form submission
        document.getElementById('addPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAddPaymentErrors();

            // Validate parking duration does not exceed max stay duration
            const durationInput = document.getElementById('add_parking_duration');
            const maxDuration = parseInt(durationInput.getAttribute('max'));
            const currentDuration = parseInt(durationInput.value);
            if (maxDuration && currentDuration > maxDuration) {
                showAddPaymentError('parking_duration', `Parking duration cannot exceed ${maxDuration} month(s) (stay duration)`);
                return;
            }

            setAddPaymentLoading(true);

            const formData = new FormData(this);

            try {
                const response = await fetch('/payment/parking/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    closeAddPaymentModal();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: result.message || 'Payment added successfully!',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    applyFilters();
                } else {
                    // Handle validation errors
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const messages = Array.isArray(result.errors[field]) ?
                                result.errors[field] : [result.errors[field]];
                            showAddPaymentError(field, messages[0]);
                        });
                    }

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: result.message || 'Failed to add payment. Please check the form.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            } catch (error) {
                console.error('Error:', error);

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: 'An unexpected error occurred. Please try again.',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            } finally {
                setAddPaymentLoading(false);
            }
        });
    </script>
</x-app-layout>
