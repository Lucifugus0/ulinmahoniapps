<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.deposit_payments') }}
            </h1>
            <button type="button" onclick="openAddPaymentModal()"
                class="mt-4 md:mt-0 inline-flex items-center px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('ui.add_new_payment') }}
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
                                    placeholder="{{ __('ui.search_deposit_placeholder') }}">
                            </div>
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
                @include('pages.payment.deposit.partials.deposit-payment_table', [
                    'depositTransactions' => $depositTransactions,
                ])
            </div>

            @if ($depositTransactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="bg-gray-50 dark:bg-gray-800 rounded p-4" id="paginationContainer">
                    {{ $depositTransactions->appends(request()->input())->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Proof + Konfirmasi Modal --}}
    <div id="proofModal" class="fixed inset-0 z-50 hidden">
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="closeDepositProofModal()">
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
                    <button onclick="closeDepositProofModal()"
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
                        <button type="button" id="btnApproveDeposit"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium transition-colors"
                            onclick="confirmApproveDepositPayment()">
                            {{ __('ui.approve') }}
                        </button>
                        {{-- Reject --}}
                        <button type="button" id="btnRejectDeposit"
                            class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium transition-colors"
                            onclick="openRejectDepositModal()">
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
                    <button type="button" onclick="closeDepositProofModal()"
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
                            <h3 class="text-lg font-semibold text-white">{{ __('ui.add_new_deposit_payment') }}</h3>
                            <p class="text-white/80 text-sm">{{ __('ui.create_deposit_payment_record') }}</p>
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
                                    {{ __('ui.booking_order_checked_in') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="order_id" id="add_order_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                    <option value="">{{ __('ui.loading_checked_in_orders') }}</option>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">{{ __('ui.select_booking_checked_in') }}</p>
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
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-1">{{ __('ui.booking_information') }}</h4>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                                            <p><span class="font-medium">{{ __('ui.customer') }}:</span> <span
                                                    id="display_user_name">-</span></p>
                                            <p><span class="font-medium">{{ __('ui.phone') }}:</span> <span
                                                    id="display_user_phone">-</span></p>
                                            <p><span class="font-medium">{{ __('ui.room') }}:</span> <span
                                                    id="display_room_name">-</span></p>
                                            <p><span class="font-medium">{{ __('ui.property') }}:</span> <span
                                                    id="display_property_name">-</span></p>
                                            <p><span class="font-medium">{{ __('ui.check_in_check_out') }}:</span> <span
                                                    id="display_checkin_checkout">-</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Fee Amount -->
                            <div class="md:col-span-2">
                                <label for="add_fee_amount_display"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('ui.fee_amount_rp') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="fee_amount_display" id="add_fee_amount_display" required
                                    placeholder="{{ __('ui.enter_fee_amount') }}" oninput="formatFeeAmount(this)"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <input type="hidden" name="fee_amount" id="add_fee_amount">
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_fee_amount_error"></p>
                            </div>

                            <!-- Transaction Date and Payment Proof in one row -->
                            <div>
                                <label for="add_transaction_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('ui.transaction_date') }} <span class="text-red-500">*</span>
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
                                    {{ __('ui.payment_proof_jpg_only') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="payment_proof" id="add_payment_proof" required
                                    accept="image/jpeg,image/jpg" onchange="validateImage(this)"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200">
                                <p class="text-xs text-gray-500 mt-1">{{ __('ui.jpg_format_max_5mb') }}</p>
                                <p class="text-red-500 text-xs mt-1 hidden" id="add_payment_proof_error"></p>
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <label for="add_notes"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('ui.notes_optional') }}
                                </label>
                                <textarea name="notes" id="add_notes" rows="3" placeholder="{{ __('ui.enter_additional_notes') }}"
                                    class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded-lg text-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all duration-200 resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" onclick="closeAddPaymentModal()"
                                class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                {{ __('ui.cancel') }}
                            </button>
                            <button type="submit" id="addPaymentSubmitBtn"
                                class="px-4 py-2.5 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                <span id="addPaymentSubmitText">{{ __('ui.add_payment') }}</span>
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
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeRejectDepositModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg max-w-md w-full"
                onclick="event.stopPropagation()">
                <div
                    class="px-6 py-4 border-b dark:border-gray-700 bg-gradient-to-r from-red-50 to-pink-50 dark:from-gray-700 dark:to-gray-700 rounded-t-xl flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-200">{{ __('ui.reject') }}
                        {{ __('ui.deposit_payments') }}</h3>
                    <button onclick="closeRejectDepositModal()" class="text-gray-400 hover:text-gray-600">
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
                            {{ __('ui.confirm_reject_deposit_msg') }}
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
                        <button type="button" onclick="closeRejectDepositModal()"
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
        function openDepositProofModal(id, orderId, viewOnly = false) {
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

            fetch(`/payment/deposit/proof/${id}`, {
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

        function closeDepositProofModal() {
            document.getElementById('proofModal').classList.add('hidden');
            document.body.style.overflow = '';
            currentProofTransactionId = null;
            currentProofOrderId = null;
        }

        // Approve with SweetAlert confirm
        function confirmApproveDepositPayment() {
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
                    const btn = document.getElementById('btnApproveDeposit');
                    const origHTML = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML =
                        `<svg class="animate-spin h-4 w-4 mr-2 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> {{ __('ui.processing') }}`;

                    fetch(`/payment/deposit/approve/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                closeDepositProofModal();
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
        function openRejectDepositModal() {
            document.getElementById('rejectTransactionId').value = currentProofTransactionId;
            document.getElementById('rejectOrderIdLabel').textContent = currentProofOrderId;
            document.getElementById('rejectNotes').value = '';
            document.getElementById('rejectModal').classList.remove('hidden');
            setTimeout(() => document.getElementById('rejectNotes').focus(), 150);
        }

        function closeRejectDepositModal() {
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

                    fetch(`/payment/deposit/reject/${id}`, {
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
                                closeRejectDepositModal();
                                closeDepositProofModal();
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
            const perPage = document.getElementById('perPageSelect')?.value || '8';

            fetch('/payment/deposit/filter', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        search,
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
            document.getElementById('perPageSelect')?.addEventListener('change', applyFilters);
        });

        // ESC key handler
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (!document.getElementById('rejectModal').classList.contains('hidden')) {
                    closeRejectDepositModal();
                } else if (!document.getElementById('proofModal').classList.contains('hidden')) {
                    closeDepositProofModal();
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
            // Hide order info section
            document.getElementById('order_info_section').classList.add('hidden');
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
            orderSelect.innerHTML = '<option value="">{{ __('ui.loading_checked_in_orders') }}</option>';

            fetch('/payment/deposit/checked-in-orders', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        checkedInOrdersData = data.data;
                        orderSelect.innerHTML = '<option value="">{{ __('ui.select_booking_order') }}</option>';
                        data.data.forEach(order => {
                            const option = document.createElement('option');
                            option.value = order.order_id;
                            option.textContent = order.display_text;
                            option.dataset.userName = order.user_name;
                            option.dataset.userPhone = order.user_phone;
                            option.dataset.roomName = order.room_name;
                            option.dataset.propertyName = order.property_name;
                            option.dataset.checkIn = order.check_in;
                            option.dataset.checkOut = order.check_out;
                            orderSelect.appendChild(option);
                        });
                    } else {
                        orderSelect.innerHTML = '<option value="">{{ __('ui.no_checked_in_bookings') }}</option>';
                    }
                })
                .catch(error => {
                    console.error('Error loading orders:', error);
                    orderSelect.innerHTML = '<option value="">{{ __('ui.error_loading_orders') }}</option>';
                });
        }

        // Handle order selection
        document.getElementById('add_order_id')?.addEventListener('change', function() {
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
            } else {
                // Hide order info
                orderInfoSection.classList.add('hidden');
            }
        });

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
                    errorEl.textContent = '{{ __('ui.only_jpg_format_allowed') }}';
                    errorEl.classList.remove('hidden');
                    input.value = '';
                    return false;
                }

                // Check file size (5MB = 5 * 1024 * 1024 bytes)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    errorEl.textContent = '{{ __('ui.file_size_max_5mb') }}';
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
                submitText.textContent = '{{ __('ui.processing') }}';
                spinner.classList.remove('hidden');
            } else {
                submitText.textContent = '{{ __('ui.add_payment') }}';
                spinner.classList.add('hidden');
            }
        }

        // Form submission
        document.getElementById('addPaymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            clearAddPaymentErrors();
            setAddPaymentLoading(true);

            const formData = new FormData(this);

            try {
                const response = await fetch('/payment/deposit/store', {
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
                        title: result.message || '{{ __('ui.payment_added_successfully') }}',
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
                        title: result.message || '{{ __('ui.failed_add_payment') }}',
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
                    title: '{{ __('ui.unexpected_error') }}',
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
