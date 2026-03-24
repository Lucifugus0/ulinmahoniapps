<div x-data="priceModal({{ $room->idrec }}, {{ $room->price_original_daily ?? 0 }})">
    <!-- Trigger Button -->
    <button @click="openModal()"
        class="p-2 text-green-600 hover:text-green-900 transition-colors duration-200 rounded-full hover:bg-green-50"
        title="{{ __('ui.room_price_edit_title') }}">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 7V3m8 4V3m-9 8h10m-12 8h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
    </button>

    <!-- Modal — teleported to body so ancestor overflow/backdrop-filter cannot clip it -->
    <template x-teleport="body">
    <div x-show="isOpen" x-cloak x-transition class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape="closeModal()">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" x-show="isOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        </div>

        <!-- Modal Container -->
        <div class="flex min-h-screen items-center justify-center p-4 text-center">
            <!-- Modal Panel -->
            <div x-show="isOpen" @click.away="closeModal()" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative w-full max-w-4xl max-h-[90vh] transform rounded-2xl bg-white text-left shadow-xl transition-all overflow-y-auto">

                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b">
                    <h3 class="text-xl font-semibold text-gray-900">
                        {{ __('ui.room_price_daily_management') }}
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Calendar Section -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <!-- Calendar Navigation -->
                            <div class="flex items-center justify-between mb-4">
                                <button @click="previousMonth()" class="p-2 rounded-lg hover:bg-white transition-colors"
                                    title="{{ __('ui.room_price_previous_month') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <h4 class="text-lg font-semibold text-gray-800" x-text="calendarTitle"></h4>

                                <button @click="nextMonth()" class="p-2 rounded-lg hover:bg-white transition-colors"
                                    title="{{ __('ui.room_price_next_month') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Calendar Grid -->
                            <div class="mb-3">
                                <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-gray-600">
                                    <div>{{ __('ui.room_price_day_sun') }}</div>
                                    <div>{{ __('ui.room_price_day_mon') }}</div>
                                    <div>{{ __('ui.room_price_day_tue') }}</div>
                                    <div>{{ __('ui.room_price_day_wed') }}</div>
                                    <div>{{ __('ui.room_price_day_thu') }}</div>
                                    <div>{{ __('ui.room_price_day_fri') }}</div>
                                    <div>{{ __('ui.room_price_day_sat') }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-7 gap-1">
                                <template x-for="(week, weekIndex) in calendarWeeks" :key="weekIndex">
                                    <template x-for="day in week" :key="day.date.getTime()">
                                        <button @click="selectDate(day)" :disabled="!day.isCurrentMonth || day.isPast"
                                            :class="[
                                                'h-10 rounded-lg text-sm font-medium transition-all duration-200',
                                                day.isSelected ? 'ring-2 ring-blue-500 ring-offset-2' : '',
                                                !day.isCurrentMonth ? 'text-gray-300 cursor-not-allowed' :
                                                day.isPast ? 'text-gray-400 cursor-not-allowed' :
                                                day.isToday ? 'bg-blue-100 text-blue-700 hover:bg-blue-200' :
                                                'text-gray-700 hover:bg-white hover:shadow-sm',
                                                getDayColor(day)
                                            ]"
                                            :title="day.isCurrentMonth ?
                                                (day.price !== null ? `Harga: ${formatCurrency(day.price)}` :
                                                    'Belum ada harga') :
                                                ''">
                                            <span x-text="day.date.getDate()"></span>
                                            <div class="w-1 h-1 mx-auto mt-1 rounded-full"
                                                :class="getPriceIndicatorColor(day)"></div>
                                        </button>
                                    </template>
                                </template>
                            </div>

                            <!-- Multi-Tier Pricing: Legend with price_type color coding -->
                            <div class="mt-6 flex flex-wrap justify-center gap-3 text-xs">
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-gray-300"></span>
                                    <span>{{ __('ui.room_price_no_price') }}</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-blue-500"></span>
                                    <span>Weekday</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-purple-500"></span>
                                    <span>Weekend</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-red-500"></span>
                                    <span>High Season</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-green-500"></span>
                                    <span>Low Season</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-orange-500"></span>
                                    <span>Holiday</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <span class="w-3 h-3 rounded bg-yellow-500"></span>
                                    <span>Manual</span>
                                </div>
                            </div>
                        </div>

                        <!-- Form Section -->
                        <div class="space-y-6">
                            <!-- Date Input -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.room_price_selected_date') }}
                                </label>
                                <input type="text" x-model="formattedSelectedDate"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                                    readonly>
                            </div>

                            <!-- Current Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('ui.room_price_current') }}
                                </label>
                                <input type="text" x-model="formattedDatePrice"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 font-medium"
                                    readonly>
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('ui.room_price_original') }} <span x-text="formattedBasePrice" class="font-medium"></span>
                                </p>
                            </div>

                            <!-- New Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Baru
                                </label>
                                <input type="text" x-ref="setPrice"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="{{ __('ui.room_price_new_placeholder') }}">
                                <p class="mt-1 text-xs text-gray-500">
                                    {{ __('ui.room_price_reset_hint') }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex justify-end space-x-3 pt-4">
                                <button @click="closeModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button @click="updatePrice()" :disabled="isLoading || !selectedDate"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-1 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="!isLoading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <svg x-show="isLoading" class="animate-spin h-5 w-5"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4">
                                        </circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span x-text="isLoading ? '{{ __('ui.facility_saving') }}' : '{{ __('ui.facility_save_changes') }}'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </template>
</div>
