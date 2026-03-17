{{-- Shared checkout modal button + modal dialog --}}
{{-- Requires Alpine.js checkOutModal component to be registered on the page --}}
<div x-data="checkOutModal('{{ $booking->order_id }}')">
    <!-- Trigger Button -->
    <button type="button"
        @click="openModal('{{ $booking->idrec }}', '{{ $booking->order_id }}')"
        class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 21V3a1 1 0 011-1h5.5a1 1 0 011 1v2m0 0v14m0-14l7 2v14l-7-2"></path>
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h1"></path>
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18"></path>
        </svg>
        {{ __('ui.checkout_now') }}
    </button>

    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity"
        x-show="isOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        aria-hidden="true" x-cloak></div>

    <!-- Modal Dialog -->
    <div class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
        role="dialog" aria-modal="true" x-show="isOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in-out duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-auto w-full max-h-full flex flex-col text-left max-w-4xl"
            @click.outside="closeModal" @keydown.escape.window="closeModal">

            <!-- Modal Header -->
            <div class="checkout-modal-header px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-yellow-100">
                <div class="flex justify-between items-center">
                    <div class="font-bold text-xl text-gray-800 dark:text-gray-100">{{ __('ui.checkout_process_title') }}</div>
                    <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors duration-200" @click="closeModal">
                        <div class="sr-only">{{ __('ui.checkout_close') }}</div>
                        <svg class="w-6 h-6 fill-current">
                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ __('ui.checkout_verify_room') }}</p>
                <p class="text-lg font-bold mt-1"
                    :class="{ 'text-red-600 dark:text-red-400': isLateCheckout, 'text-gray-800 dark:text-gray-100': !isLateCheckout }"
                    x-text="currentDateTime"></p>
                <div x-show="isLateCheckout" class="mt-2 flex items-center text-red-600">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium">{{ __('ui.checkout_late_warning') }}</span>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Booking Details -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            {{ __('ui.checkout_booking_details') }}
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_order_id_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.order_id"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_checkin_date_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.check_in"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_checkout_date_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.check_out"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_guest_name_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.guest_name"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_property_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.property_name"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_room_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.room_name"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_duration_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.duration"></span></div>
                            <div class="flex justify-between"><span class="text-sm font-medium text-gray-600">{{ __('ui.checkout_total_payment_label') }}</span><span class="text-sm text-gray-800" x-text="bookingDetails.total_payment"></span></div>
                        </div>
                    </div>

                    <!-- Room Inventory Check -->
                    <div>
                        <h3 class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            {{ __('ui.checkout_inventory_check') }}
                        </h3>
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 mb-3">{{ __('ui.checkout_check_all_items') }}</p>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-sm text-gray-600">{{ __('ui.checkout_set_all') }}</span>
                                <button type="button" @click="setAllCondition('good')" class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200">{{ __('ui.checkout_good') }}</button>
                                <button type="button" @click="setAllCondition('damaged')" class="px-2 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded hover:bg-yellow-200">{{ __('ui.checkout_damaged') }}</button>
                                <button type="button" @click="setAllCondition('missing')" class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">{{ __('ui.checkout_missing') }}</button>
                            </div>
                            <div class="space-y-2">
                                <template x-for="(item, index) in roomInventory.filter(i => i.name !== 'Lain-lain')" :key="index">
                                    <div class="flex items-center justify-between">
                                        <label :for="'item-' + index" class="block text-sm text-gray-700 min-w-[120px]" x-text="item.name"></label>
                                        <select x-model="item.condition" class="block w-40 pl-3 pr-10 py-1 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                            :class="{ 'bg-green-50 text-green-700 border-green-300': item.condition === 'good', 'bg-yellow-50 text-yellow-700 border-yellow-300': item.condition === 'damaged', 'bg-red-50 text-red-700 border-red-300': item.condition === 'missing' }">
                                            <option value="good">{{ __('ui.checkout_good') }}</option>
                                            <option value="damaged">{{ __('ui.checkout_damaged') }}</option>
                                            <option value="missing">{{ __('ui.checkout_missing') }}</option>
                                        </select>
                                    </div>
                                </template>
                                <div class="border-t border-gray-200 pt-3 mt-3">
                                    <div class="flex items-center">
                                        <input type="checkbox" x-model="lainLainSelected" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label class="ml-2 block text-sm text-gray-700 font-medium">{{ __('ui.checkout_others') }}</label>
                                    </div>
                                    <div x-show="lainLainSelected" x-transition class="ml-6 mt-2 space-y-2">
                                        <input type="text" x-model="lainLainItem.customText" placeholder="{{ __('ui.checkout_specify_others') }}" class="block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <div x-show="lainLainItem.customText.length > 0" x-transition>
                                            <select x-model="lainLainItem.condition" class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                                :class="{ 'bg-yellow-50 text-yellow-700 border-yellow-300': lainLainItem.condition === 'damaged', 'bg-red-50 text-red-700 border-red-300': lainLainItem.condition === 'missing' }">
                                                <option value="damaged">{{ __('ui.checkout_damaged') }}</option>
                                                <option value="missing">{{ __('ui.checkout_missing') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">{{ __('ui.checkout_additional_notes') }}</label>
                            <textarea x-model="additionalNotes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm"></textarea>
                        </div>
                        <div class="mt-4" x-show="hasDamagedItems">
                            <label class="block text-sm font-medium text-gray-700">{{ __('ui.checkout_damage_charges') }}</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">Rp</span></div>
                                <input type="number" x-model="damageCharges" class="focus:ring-red-500 focus:border-red-500 block w-full pl-10 pr-12 sm:text-sm border-gray-300 rounded-md" placeholder="0">
                            </div>
                            <p class="mt-1 text-sm text-gray-500" x-show="damageCharges > 0">{{ __('ui.checkout_deduct_deposit') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between">
                <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md shadow-sm hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none">{{ __('ui.cancel') }}</button>
                <button type="button" @click="submitCheckOut" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none">{{ __('ui.checkout_complete') }}</button>
            </div>
        </div>
    </div>
</div>
