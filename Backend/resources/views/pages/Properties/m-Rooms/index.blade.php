<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.room_management') }}
            </h1>
            <!-- New Input Room -->
            <div x-data="modalRoom()">
                <!-- Trigger Button -->
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center"
                    type="button" @click.prevent="modalOpen = true;" aria-controls="room-modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    Tambah Kamar
                </button>

                <!-- Modal -->
                <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
                    x-cloak></div>

                <div id="room-modal"
                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                    role="dialog" aria-modal="true" x-show="modalOpen"
                    x-transition:enter="transition ease-in-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in-out duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

                    <div class="bg-white rounded-2xl shadow-lg overflow-auto w-3/4 max-h-full flex flex-col text-left"
                        @click.outside="modalOpen = true" @keydown.escape.window="modalOpen = false">

                        <!-- Modal header with step indicator -->
                        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                            <div class="flex justify-between items-center mb-4">
                                <div class="font-bold text-xl text-gray-800">{{ __('ui.room_add_title') }}</div>
                                <button type="button"
                                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                    @click="modalOpen = false">
                                    <div class="sr-only">Close</div>
                                    <svg class="w-6 h-6 fill-current">
                                        <path
                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Step Indicator -->
                            <div class="flex items-center justify-center space-x-4">
                                <!-- Step 1 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                        :class="step > 1 ? 'bg-blue-600 border-blue-600 text-white' : step === 1 ?
                                            'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span class="text-sm font-semibold" x-show="step === 1">1</span>
                                        <span class="text-sm font-semibold" x-show="step < 1">1</span>
                                        <svg x-show="step > 1" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <p class="font-medium transition-colors duration-300"
                                            :class="step >= 1 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_basic_info') }}</p>
                                    </div>
                                </div>

                                <!-- Connector -->
                                <div class="w-16 h-0.5 transition-colors duration-300"
                                    :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                                <!-- Step 2 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                        :class="step > 2 ? 'bg-blue-600 border-blue-600 text-white' : step === 2 ?
                                            'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span class="text-sm font-semibold" x-show="step === 2 || step < 2">2</span>
                                        <svg x-show="step > 2" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <p class="font-medium transition-colors duration-300"
                                            :class="step >= 2 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_pricing') }}</p>
                                    </div>
                                </div>

                                <!-- Connector -->
                                <div class="w-16 h-0.5 transition-colors duration-300"
                                    :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                                <!-- Step 3 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                        :class="step > 3 ? 'bg-blue-600 border-blue-600 text-white' : step === 3 ?
                                            'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                                        <span class="text-sm font-semibold" x-show="step === 3 || step < 3">3</span>
                                        <svg x-show="step > 3" class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <p class="font-medium transition-colors duration-300"
                                            :class="step >= 3 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_facilities') }}</p>
                                    </div>
                                </div>

                                <!-- Connector -->
                                <div class="w-16 h-0.5 transition-colors duration-300"
                                    :class="step >= 4 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                                <!-- Step 4 -->
                                <div class="flex items-center">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                        :class="step === 4 ? 'bg-blue-600 border-blue-600 text-white' :
                                            'border-gray-300 text-gray-500'">
                                        <span class="text-sm font-semibold">4</span>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <p class="font-medium transition-colors duration-300"
                                            :class="step >= 4 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_photos') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal content -->
                        <div class="flex-1 overflow-y-auto px-6 py-6">
                            <form id="roomForm" method="POST" action="{{ route('rooms.store') }}"
                                enctype="multipart/form-data" @submit.prevent="submitForm">
                                @csrf

                                <!-- Step 1 - Basic Information -->
                                <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0">
                                    <div class="space-y-6">
                                        <!-- Property Selector -->
                                        <div>
                                            <label for="property_id"
                                                class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('ui.room_property_label') }} <span class="text-red-500">*</span>
                                            </label>
                                            <select id="property_id" name="property_id" required
                                                class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                                <option value="" disabled selected>{{ __('ui.room_select_property') }}</option>
                                                @foreach ($properties as $property)
                                                    <option value="{{ $property->idrec }}">{{ $property->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-4 md:grid-cols-4 gap-4">
                                            <div>
                                                <label for="room_no"
                                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                                    {{ __('ui.room_number_label') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="text" id="room_no" name="room_no" required
                                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4"
                                                    placeholder="{{ __('ui.room_number_placeholder') }}">
                                            </div>

                                            <div>
                                                <label for="room_name"
                                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                                    {{ __('ui.room_name_type_label') }} <span class="text-red-500">*</span>
                                                </label>
                                                <select id="room_name" name="room_name" required
                                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4">
                                                    <option value="" disabled selected>{{ __('ui.room_select_type') }}</option>
                                                    <option value="Standar">Standar</option>
                                                    <option value="Superior">Superior</option>
                                                    <option value="Deluxe">Deluxe</option>
                                                    <option value="Suite">Suite</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="room_bed"
                                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                                    {{ __('ui.room_bed_type_label') }} <span class="text-red-500">*</span>
                                                </label>
                                                <select id="room_bed" name="room_bed" required
                                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4">
                                                    <option value="">{{ __('ui.room_select_bed_type') }}</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Twin">Twin</option>
                                                    <option value="Double">Double</option>
                                                    <option value="Queen">Queen</option>
                                                    <option value="King">King</option>
                                                </select>
                                            </div>

                                            <div>
                                                <label for="room_capacity"
                                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                                    {{ __('ui.room_capacity_label') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" id="room_capacity" name="room_capacity"
                                                    required
                                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4"
                                                    placeholder="{{ __('ui.room_capacity_placeholder') }}">
                                            </div>
                                        </div>

                                        <!-- Hidden Room Size Input -->
                                        <div class="hidden">
                                            <label for="room_size"
                                                class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('ui.room_size_label') }}²) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" id="room_size" name="room_size" value="0"
                                                required
                                                class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4"
                                                placeholder="Ukuran">
                                        </div>

                                        <div>
                                            <label for="description_id"
                                                class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('ui.room_description_label') }} <span class="text-red-500">*</span>
                                            </label>
                                            <textarea id="description_id" name="description_id" rows="4" required
                                                class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                placeholder="{{ __('ui.room_description_placeholder') }}"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2 - Pricing -->
                                <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                    <div class="space-y-6">
                                        <div class="mb-4">
                                            <h3 class="text-md font-semibold text-gray-700 mb-2">{{ __('ui.room_price_type_label') }} <span
                                                    class="text-red-500">*</span></h3>
                                            <p class="text-xs text-gray-500 mb-3">{{ __('ui.room_price_type_hint') }}
                                            </p>
                                            <div class="flex space-x-6">
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="price_type" value="daily"
                                                        x-model="priceType" class="form-radio text-blue-600 h-4 w-4"
                                                        required>
                                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ __('ui.room_daily_label') }}</span>
                                                </label>
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="price_type" value="monthly"
                                                        x-model="priceType" class="form-radio text-blue-600 h-4 w-4"
                                                        required>
                                                    <span class="ml-2 text-sm font-medium text-gray-700">{{ __('ui.room_monthly_label') }}</span>
                                                </label>
                                                {{-- <!-- Multi-Tier Pricing: Annual price type option --> --}}
                                                <label class="inline-flex items-center cursor-pointer">
                                                    <input type="radio" name="price_type" value="annual"
                                                        x-model="priceType" class="form-radio text-blue-600 h-4 w-4"
                                                        required>
                                                    <span class="ml-2 text-sm font-medium text-gray-700">Tahunan</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div x-show="priceType === 'daily'" x-transition>
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('ui.room_daily_price') }} <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" x-ref="dailyPriceInput"
                                                    class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                    placeholder="{{ __('ui.room_daily_price_placeholder') }}" x-model="dailyPriceFormatted"
                                                    @input="updateDailyPrice($event.target.value)">
                                                <!-- Hidden input untuk backend -->
                                                <input type="hidden" name="daily_price" x-model="dailyPrice">
                                            </div>
                                            <p x-show="dailyPriceError" class="text-red-500 text-xs mt-1"
                                                x-text="dailyPriceError"></p>

                                            {{-- <!-- Multi-Tier Pricing: Weekday/Weekend price fields for daily rooms --> --}}
                                            <div class="grid grid-cols-2 gap-4 mt-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1">Harga Weekday (Sen-Jum)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                                        <input type="text" x-model="weekdayPriceFormatted"
                                                            @input="updateWeekdayPrice($event.target.value)"
                                                            class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-2 px-4 focus:ring-2 focus:ring-blue-500 text-sm"
                                                            placeholder="Sama dengan harga harian">
                                                        <input type="hidden" name="weekday_price" x-model="weekdayPrice">
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1">Kosongkan = sama dengan harga harian</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-600 mb-1">Harga Weekend (Sab-Min)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                                        <input type="text" x-model="weekendPriceFormatted"
                                                            @input="updateWeekendPrice($event.target.value)"
                                                            class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-2 px-4 focus:ring-2 focus:ring-blue-500 text-sm"
                                                            placeholder="Sama dengan harga harian">
                                                        <input type="hidden" name="weekend_price" x-model="weekendPrice">
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-1">Kosongkan = sama dengan harga harian</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div x-show="priceType === 'monthly'" x-transition class="mt-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                {{ __('ui.room_monthly_price') }} <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div
                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text" x-ref="monthlyPriceInput"
                                                    class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                    placeholder="{{ __('ui.room_monthly_price_placeholder') }}"
                                                    x-model="monthlyPriceFormatted"
                                                    @input="updateMonthlyPrice($event.target.value)">
                                                <!-- Hidden input untuk backend -->
                                                <input type="hidden" name="monthly_price" x-model="monthlyPrice">
                                            </div>
                                            <p x-show="monthlyPriceError" class="text-red-500 text-xs mt-1"
                                                x-text="monthlyPriceError"></p>
                                        </div>

                                        {{-- <!-- Multi-Tier Pricing: Annual price input --> --}}
                                        <div x-show="priceType === 'annual'" x-transition class="mt-4">
                                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                                Harga Tahunan <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">Rp</span>
                                                </div>
                                                <input type="text"
                                                    class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                                    placeholder="Masukkan harga per tahun"
                                                    x-model="annualPriceFormatted"
                                                    @input="updateAnnualPrice($event.target.value)">
                                                <input type="hidden" name="annual_price" x-model="annualPrice">
                                            </div>
                                            <p x-show="annualPriceError" class="text-red-500 text-xs mt-1" x-text="annualPriceError"></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3 - Facilities -->
                                <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                    <div class="space-y-6">
                                        <div>
                                            <h3 class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                                </svg>
                                                Fasilitas Kamar
                                            </h3>

                                            <!-- Facilities Grid -->
                                            <div
                                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2">
                                                @foreach ($facilities as $facility)
                                                    <div class="relative">
                                                        <label
                                                            class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors duration-200 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                                                            <input type="checkbox" name="general_facilities[]"
                                                                value="{{ $facility->idrec }}"
                                                                id="facility_{{ $facility->idrec }}"
                                                                class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">

                                                            <div class="ml-3 flex-1">
                                                                <span class="block text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                                                    @if (!empty($facility->icon))
                                                                        <span class="iconify text-lg" data-icon="{{ $facility->icon }}"></span>
                                                                    @endif
                                                                    {{ $facility->facility }}
                                                                </span>
                                                                @if (!empty($facility->description))
                                                                    <span class="block text-xs text-gray-500 mt-1">
                                                                        {{ $facility->description }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- No Facilities Available Message -->
                                            @if ($facilities->isEmpty())
                                                <div class="text-center py-8">
                                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <p class="mt-2 text-sm text-gray-500">Tidak ada fasilitas tersedia
                                                    </p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 4 - Photos -->
                                <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-x-4"
                                    x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                    <div class="space-y-6">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                                Foto Kamar <span class="text-red-500">*</span>
                                                <span class="text-sm font-normal text-gray-500">
                                                    (Minimal 3 foto, maksimal 5 foto - <span
                                                        x-text="remainingSlots"></span> slot tersisa)
                                                </span>
                                            </label>

                                            <!-- Thumbnail Selection Area -->
                                            <div class="mb-6">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                                    Pilih Thumbnail <span class="text-red-500">*</span>
                                                    <span class="text-xs font-normal text-gray-500">(Foto utama yang
                                                        akan ditampilkan)</span>
                                                </h4>

                                                <div class="flex items-center space-x-4">
                                                    <!-- Thumbnail Preview -->
                                                    <div class="w-32 h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 overflow-hidden relative"
                                                        x-show="images.length > 0">
                                                        <template x-if="thumbnailIndex !== null && images[thumbnailIndex]">
                                                            <img :src="images[thumbnailIndex]?.url"
                                                                alt="Selected Thumbnail"
                                                                class="w-full h-full object-cover">
                                                        </template>
                                                        <div class="absolute inset-0 flex items-center justify-center text-gray-400"
                                                            x-show="thumbnailIndex === null">
                                                            <svg class="w-10 h-10" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                </path>
                                                            </svg>
                                                        </div>
                                                    </div>

                                                    <!-- Thumbnail Selection Instructions -->
                                                    <div class="flex-1">
                                                        <p class="text-sm text-gray-600 mb-2">
                                                            <span x-show="thumbnailIndex === null"
                                                                class="font-medium text-red-500">Belum ada thumbnail
                                                                dipilih!</span>
                                                            <span x-show="thumbnailIndex !== null"
                                                                class="font-medium text-green-600">Thumbnail sudah
                                                                dipilih.</span>
                                                            Klik salah satu foto di bawah untuk memilih sebagai
                                                            thumbnail.
                                                        </p>
                                                        <p class="text-xs text-gray-500">
                                                            Pastikan memilih foto terbaik sebagai thumbnail karena ini
                                                            akan menjadi gambar utama kamar Anda.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Upload Area -->
                                            <div x-show="canUploadMore" @drop="handleDrop($event)" @dragover.prevent
                                                @dragenter.prevent
                                                class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                                :class="{ 'border-blue-400 bg-blue-50': canUploadMore }">
                                                <div class="space-y-2">
                                                    <svg class="w-12 h-12 mx-auto text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <div class="flex text-sm text-gray-600 justify-center">
                                                        <label for="room_images"
                                                            class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                            <span>Upload foto</span>
                                                            <input id="room_images" name="room_images[]"
                                                                type="file" multiple accept="image/*"
                                                                @change="handleFileSelect($event)" class="sr-only">
                                                        </label>
                                                        <p class="pl-1">atau drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                                    <p class="text-xs text-blue-600"
                                                        x-text="`Dapat upload ${remainingSlots} foto lagi`"></p>
                                                </div>
                                            </div>

                                            <!-- Full Upload Message -->
                                            <div x-show="!canUploadMore"
                                                class="border-2 border-green-300 rounded-lg p-8 text-center bg-green-50">
                                                <div class="space-y-2">
                                                    <svg class="w-12 h-12 mx-auto text-green-500" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    <p class="text-sm text-green-600 font-medium">5 foto telah
                                                        diupload!</p>
                                                    <p class="text-xs text-green-500">Maksimal foto telah tercapai</p>
                                                </div>
                                            </div>

                                            <!-- Image Preview Grid -->
                                            <div x-show="images.length > 0" class="mt-4">
                                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Foto Terupload
                                                </h4>
                                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"
                                                    x-transition:enter="transition ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 scale-95"
                                                    x-transition:enter-end="opacity-100 scale-100">
                                                    <template x-for="(image, index) in images" :key="index">
                                                        <div class="relative group" @click="setThumbnail(index)">
                                                            <!-- Image Container -->
                                                            <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                                                :class="thumbnailIndex === index ?
                                                                    'border-blue-600 ring-2 ring-blue-400' :
                                                                    'border-gray-200 hover:border-blue-400'">
                                                                <img :src="image?.url || ''"
                                                                    :alt="`Preview ${index + 1}`"
                                                                   class="w-full h-full object-cover"
                                                                    x-show="image?.url">

                                                                <!-- Thumbnail badge -->
                                                                <div x-show="thumbnailIndex === index"
                                                                    class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                    Thumbnail
                                                                </div>
                                                            </div>

                                                            <!-- Remove Button -->
                                                            <button @click.stop="removeImage(index, $event)"
                                                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                            </button>

                                                            <!-- Image Number Badge -->
                                                            <div
                                                                class="absolute bottom-1 left-1 bg-gray-800 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                <span x-text="index + 1"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Progress Indicator -->
                                            <div class="mt-4">
                                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                                    <span>Progress Upload</span>
                                                    <span x-text="`${images.length}/${maxImages} foto`"></span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                        :style="`width: ${(images.length / maxImages) * 100}%`">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Validation Messages -->
                                            <div class="mt-3 space-y-2">
                                                <p class="text-sm text-red-600" x-show="images.length < 3">
                                                    <span class="font-medium">Perhatian:</span>
                                                    Anda harus mengupload tepat 3 foto untuk melanjutkan.
                                                </p>

                                                <p class="text-sm text-red-600"
                                                    x-show="images.length >= 3 && thumbnailIndex === null">
                                                    <span class="font-medium">Perhatian:</span>
                                                    Anda harus memilih thumbnail untuk melanjutkan.
                                                </p>

                                                <p class="text-sm text-green-600"
                                                    x-show="images.length === 3 && thumbnailIndex !== null">
                                                    <span class="font-medium">Sempurna!</span>
                                                    Semua foto telah diupload dan thumbnail telah dipilih.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-6 flex justify-end">
                                    <div>
                                        <button type="button" x-show="step > 1" @click="step--"
                                            class="px-6 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"></path>
                                            </svg>
                                            Sebelumnya
                                        </button>
                                        <button type="button" x-show="step < 4"
                                            @click="validateStep(step) && step++"
                                            class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                            Selanjutnya
                                            <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                        <button type="submit" x-show="step === 4"
                                            class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Simpan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">

                    <!-- Filter Properti -->
                    <div>
                        <select id="room-filter"
                            class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="" hidden>{{ __('ui.room_select_property') }}</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->idrec }}"
                                    {{ request('property_id') == $property->idrec ? 'selected' : '' }}>
                                    {{ $property->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Kamar -->
                    <div class="flex-1">
                        <input type="text" id="search-input" placeholder="Cari kamar..."
                            value="{{ request('search') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Filter Status -->
                    <div>
                        <select id="status-filter"
                            class="w-full md:w-40 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="1" {{ ($statusFilter ?? '1') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ ($statusFilter ?? '1') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="all" {{ ($statusFilter ?? '1') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        </select>
                    </div>

                    <!-- Per Page Dropdown -->
                    <div class="flex items-center gap-2">
                        <label for="per-page-filter" class="text-sm text-gray-600">Tampilkan:</label>
                        <select id="per-page-filter"
                            class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="8" {{ request('per_page', 8) == 8 ? 'selected' : '' }}>8</option>
                            <option value="25" {{ request('per_page', 8) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 8) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>

                </div>
            </div>


            <div class="overflow-x-auto" id="roomTableContainer">
                @include('pages.Properties.m-Rooms.partials.room_table', [
                    'properties' => $properties,
                    'per_page' => request('per_page', 8),
                ])
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 rounded p-4" id="paginationContainer">
                {{ $rooms->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modalRoom', () => ({
                modalOpen: false,
                step: 1,
                images: [],
                maxImages: 5,
                minImages: 3,
                priceType: '',
                dailyPrice: 0,
                monthlyPrice: 0,
                dailyPriceError: '',
                monthlyPriceError: '',
                /* Multi-Tier Pricing: annual + weekday/weekend price state */
                annualPrice: 0,
                annualPriceFormatted: '',
                annualPriceError: '',
                weekdayPrice: '',
                weekdayPriceFormatted: '',
                weekendPrice: '',
                weekendPriceFormatted: '',
                isCheckingRoomNo: false,
                roomNoError: '',
                thumbnailIndex: null,
                facilities: [],
                formErrors: {},
                isLoading: false,
                dailyPriceFormatted: '',
                monthlyPriceFormatted: '',

                init() {
                    // Watch for modal close to reset form
                    this.$watch('modalOpen', (value) => {
                        if (!value) {
                            this.resetForm();
                        }
                    });

                    // Initialize price input formatting
                    this.$nextTick(() => {
                        try {
                            setTimeout(() => {
                                // Initialize Cleave for daily price
                                if (this.$refs.dailyPriceInput) {
                                    this.initCleave(this.$refs.dailyPriceInput,
                                        'dailyPrice');
                                }

                                // Initialize Cleave for monthly price
                                if (this.$refs.monthlyPriceInput) {
                                    this.initCleave(this.$refs.monthlyPriceInput,
                                        'monthlyPrice');
                                }
                            }, 300);
                        } catch (error) {
                            console.error('Price input initialization error:', error);
                        }
                    });

                    // Set up global error handler
                    this.setupGlobalErrorHandling();
                },

                initCleave(inputRef, priceType) {
                    try {
                        if (!inputRef || inputRef._cleave) return;

                        const cleave = new Cleave(inputRef, {
                            numeral: true,
                            numeralThousandsGroupStyle: 'thousand',
                            numeralDecimalMark: ',',
                            delimiter: '.',
                            numeralDecimalScale: 0,
                            onValueChanged: (e) => {
                                this.validatePriceInput(inputRef, priceType);
                            }
                        });

                        // Store reference
                        inputRef._cleave = cleave;
                    } catch (error) {
                        console.error('Cleave init error:', error);
                    }
                },

                resetForm() {
                    // Reset all form data to initial state
                    this.step = 1;
                    this.images = [];
                    this.thumbnailIndex = null;
                    this.priceType = '';
                    this.dailyPrice = 0;
                    this.monthlyPrice = 0;
                    this.dailyPriceError = '';
                    this.monthlyPriceError = '';
                    this.formErrors = {};
                    this.isLoading = false;

                    // Reset form element
                    const form = document.getElementById('roomForm');
                    if (form) {
                        form.reset();
                    }

                    // Reset price inputs
                    if (this.$refs.dailyPriceInput) {
                        this.$refs.dailyPriceInput.value = '';
                        if (this.$refs.dailyPriceInput._cleave) {
                            this.$refs.dailyPriceInput._cleave.setRawValue('');
                        }
                    }
                    if (this.$refs.monthlyPriceInput) {
                        this.$refs.monthlyPriceInput.value = '';
                        if (this.$refs.monthlyPriceInput._cleave) {
                            this.$refs.monthlyPriceInput._cleave.setRawValue('');
                        }
                    }

                    // Clear error borders
                    this.clearFormErrors();
                },

                setupGlobalErrorHandling() {
                    window.addEventListener('unhandledrejection', (event) => {
                        console.error('Unhandled promise rejection:', event.reason);
                        this.showErrorAlert(
                            'Terjadi kesalahan sistem: ' + (event.reason?.message ||
                                'Unknown error'),
                            'Kesalahan Sistem'
                        );
                        event.preventDefault();
                    });

                    window.addEventListener('error', (event) => {
                        console.error('JavaScript error:', event.error);
                        this.showErrorAlert(
                            'Terjadi kesalahan JavaScript: ' + (event.error?.message ||
                                'Unknown error'),
                            'Kesalahan Aplikasi'
                        );
                    });
                },

                validatePriceInput(inputRef, priceType) {
                    try {
                        if (!inputRef) return;

                        // Get raw value from Cleave
                        let rawValue = inputRef.value;
                        if (inputRef._cleave) {
                            rawValue = inputRef._cleave.getRawValue() || '';
                        }

                        // Remove formatting characters
                        let numericString = rawValue.replace(/[^\d]/g, '');

                        // Convert to number
                        let numericValue = numericString ? parseInt(numericString, 10) : 0;

                        // Validate
                        if (numericValue > 999999999999) {
                            this[priceType + 'Error'] = 'Maksimum Rp 999.999.999.999';
                            numericValue = 999999999999;
                            if (inputRef._cleave) {
                                inputRef._cleave.setRawValue('999999999999');
                            }
                        } else {
                            this[priceType + 'Error'] = '';
                        }

                        // Update model
                        this[priceType] = numericValue;

                        // Update hidden input
                        const hiddenInput = document.querySelector(`input[name="${priceType}"]`);
                        if (hiddenInput) {
                            hiddenInput.value = numericValue;
                        }

                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan validasi harga: ' + error.message);
                    }
                },

                // Photo upload methods
                handleFileSelect(event) {
                    try {
                        if (!event.target.files || event.target.files.length === 0) {
                            return;
                        }

                        const files = Array.from(event.target.files);
                        this.processFiles(files, event);

                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan saat memproses file: ' + error.message);
                    }
                },

                handleDrop(event) {
                    try {
                        event.preventDefault();

                        if (!event.dataTransfer.files || event.dataTransfer.files.length === 0) {
                            return;
                        }

                        const files = Array.from(event.dataTransfer.files);
                        this.processFiles(files, event);
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan saat drag & drop: ' + error.message);
                    }
                },

                processFiles(files, event) {
                    try {
                        const imageFiles = files.filter(file => {
                            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png',
                                'image/gif', 'image/webp'
                            ];
                            const isAllowedType = allowedTypes.includes(file.type);
                            const isWithinSizeLimit = file.size <= 5 * 1024 * 1024;

                            if (!isAllowedType) {
                                this.showErrorAlert(
                                    `File ${file.name} bukan format gambar yang didukung (hanya JPEG, PNG, JPG, GIF, WebP)`
                                );
                            }
                            if (!isWithinSizeLimit) {
                                this.showErrorAlert(
                                    `File ${file.name} melebihi ukuran maksimal 5MB`);
                            }

                            return isAllowedType && isWithinSizeLimit;
                        });

                        const availableSlots = this.maxImages - this.images.length;

                        if (availableSlots <= 0) {
                            this.showErrorAlert(`Maksimal ${this.maxImages} foto`);
                            return;
                        }

                        const filesToProcess = imageFiles.slice(0, availableSlots);

                        // Process valid files
                        filesToProcess.forEach(file => {
                            const reader = new FileReader();

                            reader.onload = (e) => {
                                // **PERBAIKAN: Pastikan properti url ada**
                                const imageObject = {
                                    file: file,
                                    url: e.target.result || URL.createObjectURL(
                                        file), // Fallback ke createObjectURL
                                    name: file.name,
                                    size: file.size,
                                    type: file.type
                                };

                                console.log('Image processed:', imageObject); // Debug

                                this.images.push(imageObject);

                                // Auto-set first image as thumbnail
                                if (this.thumbnailIndex === null) {
                                    this.thumbnailIndex = 0;
                                }
                            };

                            reader.onerror = () => {
                                console.error('Gagal membaca file:', file.name);
                                // Fallback dengan createObjectURL
                                const fallbackImage = {
                                    file: file,
                                    url: URL.createObjectURL(file),
                                    name: file.name,
                                    size: file.size,
                                    type: file.type
                                };

                                this.images.push(fallbackImage);

                                if (this.thumbnailIndex === null) {
                                    this.thumbnailIndex = 0;
                                }
                            };

                            reader.readAsDataURL(file);
                        });

                        // Clear file input
                        if (event.target) {
                            event.target.value = '';
                        }

                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan saat memproses file: ' + error.message);
                    }
                },

                setThumbnail(index) {
                    try {
                        if (index < 0 || index >= this.images.length) return;

                        // **PERBAIKAN: Validasi objek image**
                        const image = this.images[index];
                        if (!image) {
                            console.error('Image tidak ditemukan di index:', index);
                            return;
                        }

                        // Validasi URL
                        if (!image.url) {
                            console.warn('Image tanpa URL, mencoba generate ulang...');
                            if (image.file) {
                                image.url = URL.createObjectURL(image.file);
                            } else {
                                console.error('Tidak bisa set thumbnail: image tidak valid');
                                return;
                            }
                        }

                        this.thumbnailIndex = index;
                        console.log('Thumbnail set to index:', index, 'URL:', image.url);
                    } catch (error) {
                        console.error('Gagal memilih thumbnail:', error);
                        this.showErrorAlert('Gagal memilih thumbnail: ' + error.message);
                    }
                },

                removeImage(index, event) {
                    try {
                        if (event) event.preventDefault();

                        if (index < 0 || index >= this.images.length) return;

                        // Adjust thumbnail index
                        if (this.thumbnailIndex === index) {
                            this.thumbnailIndex = null;
                        } else if (this.thumbnailIndex > index) {
                            this.thumbnailIndex--;
                        }

                        this.images.splice(index, 1);

                        // Auto-select new thumbnail if exists
                        if (this.thumbnailIndex === null && this.images.length > 0) {
                            this.thumbnailIndex = 0;
                        }
                    } catch (error) {
                        this.showErrorAlert('Gagal menghapus foto: ' + error.message);
                    }
                },

                // Alert Methods
                showErrorAlert(message, title = 'Error') {
                    if (typeof Swal !== 'undefined') {
                        return Swal.fire({
                            icon: 'error',
                            title: title,
                            html: typeof message === 'string' ? message.replace(/\n/g, '<br>') :
                                message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626',
                        });
                    } else {
                        alert(title + ': ' + message);
                    }
                },

                showSuccessAlert(message, title = 'Sukses') {
                    if (typeof Swal !== 'undefined') {
                        return Swal.fire({
                            icon: 'success',
                            title: title,
                            text: message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#059669',
                        });
                    } else {
                        alert(title + ': ' + message);
                    }
                },

                showWarningAlert(title, message) {
                    if (typeof Swal !== 'undefined') {
                        return Swal.fire({
                            icon: 'warning',
                            title: title,
                            html: typeof message === 'string' ? message.replace(/\n/g, '<br>') :
                                message,
                            confirmButtonText: 'Mengerti',
                            confirmButtonColor: '#d97706'
                        });
                    } else {
                        alert(title + ': ' + message);
                    }
                },

                showLoadingAlert(title = 'Memproses...') {
                    if (typeof Swal !== 'undefined') {
                        return Swal.fire({
                            title: title,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                },

                updateDailyPrice(value) {
                    // Format angka
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.dailyPrice = numericValue ? parseInt(numericValue, 10) : 0;

                    // Format untuk display
                    if (this.dailyPrice > 0) {
                        this.dailyPriceFormatted = new Intl.NumberFormat('id-ID').format(this
                            .dailyPrice);
                    } else {
                        this.dailyPriceFormatted = '';
                    }
                },

                updateMonthlyPrice(value) {
                    // Format angka
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.monthlyPrice = numericValue ? parseInt(numericValue, 10) : 0;

                    // Format untuk display
                    if (this.monthlyPrice > 0) {
                        this.monthlyPriceFormatted = new Intl.NumberFormat('id-ID').format(this
                            .monthlyPrice);
                    } else {
                        this.monthlyPriceFormatted = '';
                    }
                },

                /* Multi-Tier Pricing: format functions for annual/weekday/weekend price inputs */
                updateAnnualPrice(value) {
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.annualPrice = numericValue ? parseInt(numericValue, 10) : 0;
                    this.annualPriceFormatted = this.annualPrice > 0 ? new Intl.NumberFormat('id-ID').format(this.annualPrice) : '';
                },
                updateWeekdayPrice(value) {
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.weekdayPrice = numericValue ? parseInt(numericValue, 10) : '';
                    this.weekdayPriceFormatted = this.weekdayPrice ? new Intl.NumberFormat('id-ID').format(this.weekdayPrice) : '';
                },
                updateWeekendPrice(value) {
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.weekendPrice = numericValue ? parseInt(numericValue, 10) : '';
                    this.weekendPriceFormatted = this.weekendPrice ? new Intl.NumberFormat('id-ID').format(this.weekendPrice) : '';
                },

                // Perbaiki validateStep2()
                validateStep2() {
                    try {
                        console.log('Validating price - Type:', this.priceType, 'Daily:', this
                            .dailyPrice, 'Monthly:', this.monthlyPrice);

                        if (!this.priceType) {
                            this.showErrorAlert('Pilih jenis harga (harian atau bulanan)',
                                'Jenis Harga Belum Dipilih');
                            return false;
                        }

                        if (this.priceType === 'daily') {
                            if (!this.dailyPrice || this.dailyPrice <= 0) {
                                this.showErrorAlert('Harga harian harus diisi dengan nilai yang valid',
                                    'Harga Tidak Valid');
                                return false;
                            }
                        } else if (this.priceType === 'monthly') {
                            if (!this.monthlyPrice || this.monthlyPrice <= 0) {
                                this.showErrorAlert('Harga bulanan harus diisi dengan nilai yang valid',
                                    'Harga Tidak Valid');
                                return false;
                            }
                        } else if (this.priceType === 'annual') {
                            /* Multi-Tier Pricing: validate annual price */
                            if (!this.annualPrice || this.annualPrice <= 0) {
                                this.showErrorAlert('Harga tahunan harus diisi dengan nilai yang valid',
                                    'Harga Tidak Valid');
                                return false;
                            }
                        }

                        return true;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan validasi harga: ' + error.message);
                        return false;
                    }
                },

                // Validasi step
                async validateStep(step) {
                    try {
                        this.clearFormErrors();

                        let isValid = false;

                        switch (step) {
                            case 1:
                                isValid = await this.validateStep1();
                                break;
                            case 2:
                                isValid = this.validateStep2();
                                break;
                            case 3:
                                isValid = this.validateStep3();
                                break;
                            case 4:
                                isValid = this.validateStep4();
                                break;
                            default:
                                throw new Error('Step tidak valid: ' + step);
                        }

                        if (!isValid) {
                            this.highlightErrorFields();
                        }

                        return isValid;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan saat validasi: ' + error.message);
                        return false;
                    }
                },

                clearFormErrors() {
                    const errorFields = document.querySelectorAll('.border-red-500');
                    errorFields.forEach(field => {
                        field.classList.remove('border-red-500');
                    });
                    this.formErrors = {};
                },

                highlightErrorFields() {
                    Object.keys(this.formErrors).forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) {
                            field.classList.add('border-red-500');
                        }
                    });
                },

                async validateStep1() {
                    try {
                        const requiredFields = [{
                                id: 'property_id',
                                name: 'Properti'
                            },
                            {
                                id: 'room_no',
                                name: 'Nomor Kamar'
                            },
                            {
                                id: 'room_name',
                                name: 'Nama Kamar'
                            },
                            {
                                id: 'room_size',
                                name: 'Ukuran Kamar'
                            },
                            {
                                id: 'room_bed',
                                name: 'Jenis Tempat Tidur'
                            },
                            {
                                id: 'room_capacity',
                                name: 'Kapasitas'
                            },
                            {
                                id: 'description_id',
                                name: 'Deskripsi'
                            }
                        ];

                        const missingFields = [];
                        this.formErrors = {};

                        // Validate required fields
                        requiredFields.forEach(field => {
                            const el = document.getElementById(field.id);
                            if (el && !el.value.trim()) {
                                this.formErrors[field.id] = `${field.name} harus diisi`;
                                missingFields.push(field.name);
                            }
                        });

                        if (missingFields.length > 0) {
                            await this.showErrorAlert(
                                `<strong>Field berikut harus diisi:</strong><br>${missingFields.map(field => `• ${field}`).join('<br>')}`,
                                'Data Belum Lengkap'
                            );
                            return false;
                        }

                        // Validate room number uniqueness
                        const propertyId = document.getElementById('property_id').value;
                        const roomNo = document.getElementById('room_no').value;

                        if (!propertyId || !roomNo) return false;

                        this.isCheckingRoomNo = true;
                        this.roomNoError = '';

                        // Check room number availability
                        try {
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                                ?.content;
                            const response = await fetch('/properties/rooms/check-room-number', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken || ''
                                },
                                body: JSON.stringify({
                                    property_id: propertyId,
                                    room_no: roomNo
                                })
                            });

                            const data = await response.json();
                            this.isCheckingRoomNo = false;

                            if (data.exists) {
                                this.formErrors['room_no'] = data.message;
                                document.getElementById('room_no').classList.add('border-red-500');
                                await this.showErrorAlert(data.message, 'Nomor Kamar Sudah Ada');
                                return false;
                            }

                            return true;
                        } catch (fetchError) {
                            this.isCheckingRoomNo = false;
                            console.error('Error checking room number:', fetchError);
                            // Continue even if check fails
                            return true;
                        }

                    } catch (error) {
                        this.isCheckingRoomNo = false;
                        console.error('Validate step 1 error:', error);
                        return false;
                    }
                },

                validateStep2() {
                    try {
                        if (!this.priceType) {
                            this.showErrorAlert('Pilih jenis harga (harian atau bulanan)',
                                'Jenis Harga Belum Dipilih');
                            return false;
                        }

                        const errors = [];

                        // Validate price based on selected type
                        if (this.priceType === 'daily') {
                            if (!this.dailyPrice || this.dailyPrice <= 0) {
                                errors.push('• Harga harian harus diisi');
                            }
                        } else if (this.priceType === 'monthly') {
                            if (!this.monthlyPrice || this.monthlyPrice <= 0) {
                                errors.push('• Harga bulanan harus diisi');
                            }
                        }

                        if (errors.length > 0) {
                            this.showErrorAlert(
                                `<strong>Validasi harga gagal:</strong><br>${errors.join('<br>')}`,
                                'Kesalahan Input Harga'
                            );
                            return false;
                        }

                        return true;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan validasi step 2: ' + error.message);
                        return false;
                    }
                },

                validateStep3() {
                    // Facilities are optional
                    return true;
                },

                validateStep4() {
                    try {
                        if (this.images.length < this.minImages) {
                            this.showErrorAlert(
                                `Minimal ${this.minImages} foto kamar harus diupload!<br>Saat ini: ${this.images.length} foto`,
                                'Foto Tidak Mencukupi'
                            );
                            return false;
                        }

                        if (this.thumbnailIndex === null) {
                            this.showErrorAlert(
                                'Pilih foto sebagai thumbnail!<br>Klik pada foto yang ingin dijadikan thumbnail.',
                                'Thumbnail Belum Dipilih'
                            );
                            return false;
                        }

                        return true;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan validasi step 4: ' + error.message);
                        return false;
                    }
                },

                get canUploadMore() {
                    return this.images.length < this.maxImages;
                },

                get remainingSlots() {
                    return this.maxImages - this.images.length;
                },

                async submitForm() {
                    let submitBtn = null;
                    let originalBtnContent = null;

                    try {
                        // Final validation
                        if (!await this.validateStep(4)) {
                            return;
                        }

                        // Set loading state
                        this.isLoading = true;
                        submitBtn = document.querySelector('#roomForm button[type="submit"]');

                        if (submitBtn) {
                            originalBtnContent = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan Data...
                            `;
                        }

                        const loadingAlert = this.showLoadingAlert('Menyimpan data kamar...');

                        // Get form and create FormData
                        const form = document.getElementById('roomForm');
                        const formData = new FormData(form);

                        // Debug: Log data sebelum dikirim
                        console.log('Price Type:', this.priceType);
                        console.log('Daily Price:', this.dailyPrice);
                        console.log('Monthly Price:', this.monthlyPrice);

                        // Clear existing file inputs
                        formData.delete('room_images[]');

                        // Add thumbnail index
                        formData.append('thumbnail_index', this.thumbnailIndex);

                        // Add images
                        this.images.forEach((image, index) => {
                            if (image.file) {
                                formData.append('room_images[]', image.file);
                            }
                        });

                        // **PERBAIKAN: Pastikan data harga dikirim dengan benar**
                        // Hapus dulu input harga yang mungkin sudah ada
                        formData.delete('daily_price');
                        formData.delete('monthly_price');
                        formData.delete('price_type');

                        // Tambahkan data harga berdasarkan jenis yang dipilih
                        if (this.priceType === 'daily') {
                            // Untuk harga harian
                            formData.append('daily_price', this.dailyPrice.toString());
                            formData.append('monthly_price', '0');
                            formData.append('annual_price', '0');
                            formData.append('price_type', 'daily');
                            /* Multi-Tier Pricing: send weekday/weekend prices (fall back to daily price if empty) */
                            formData.append('weekday_price', (this.weekdayPrice || this.dailyPrice).toString());
                            formData.append('weekend_price', (this.weekendPrice || this.dailyPrice).toString());
                        } else if (this.priceType === 'monthly') {
                            // Untuk harga bulanan
                            formData.append('monthly_price', this.monthlyPrice.toString());
                            formData.append('daily_price', '0');
                            formData.append('annual_price', '0');
                            formData.append('price_type', 'monthly');
                        } else if (this.priceType === 'annual') {
                            /* Multi-Tier Pricing: annual price submission */
                            formData.append('annual_price', this.annualPrice.toString());
                            formData.append('daily_price', '0');
                            formData.append('monthly_price', '0');
                            formData.append('price_type', 'annual');
                        } else {
                            // Jika tidak ada jenis harga yang dipilih
                            throw new Error('Jenis harga belum dipilih');
                        }

                        // **OPTIONAL: Tambahkan field untuk debugging**
                        formData.append('_debug', 'true');

                        // **DEBUG: Log semua data FormData**
                        console.log('=== FormData yang akan dikirim ===');
                        for (let pair of formData.entries()) {
                            console.log(pair[0] + ': ', pair[1]);
                        }

                        // Submit form
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        // Close loading alert
                        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                            Swal.close();
                        }

                        // Handle response
                        if (!response.ok) {
                            let errorMessage = 'Gagal menyimpan data';

                            try {
                                const data = await response.json();
                                console.log('Error Response:', data); // Debug log

                                if (data.errors) {
                                    // Format validation errors
                                    if (typeof data.errors === 'object') {
                                        errorMessage = Object.entries(data.errors)
                                            .map(([field, errors]) =>
                                                `${field}: ${Array.isArray(errors) ? errors.join(', ') : errors}`
                                            )
                                            .join('\n');
                                    } else {
                                        errorMessage = JSON.stringify(data.errors);
                                    }
                                } else if (data.message) {
                                    errorMessage = data.message;
                                }
                            } catch (e) {
                                // If response is not JSON
                                console.error('Response parsing error:', e);
                                errorMessage = `Error ${response.status}: ${response.statusText}`;
                            }

                            throw new Error(errorMessage);
                        }

                        const data = await response.json();
                        console.log('Success Response:', data); // Debug log

                        // Success
                        await this.showSuccessAlert(
                            data.message || 'Kamar berhasil disimpan!',
                            'Berhasil'
                        );

                        // Close modal
                        this.modalOpen = false;

                        // Refresh or redirect
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (window.Livewire) {
                            // If using Livewire
                            Livewire.dispatch('room-saved');
                        } else if (typeof reloadRoomsTable === 'function') {
                            reloadRoomsTable();
                        } else {
                            // Default: reload page after delay
                            setTimeout(() => window.location.reload(), 1500);
                        }

                    } catch (error) {
                        console.error('Submission Error:', error);

                        // Restore button state
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent || `
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Simpan
                            `;
                        }

                        await this.showErrorAlert(
                            error.message || 'Terjadi kesalahan saat menyimpan data',
                            'Gagal Menyimpan'
                        );

                    } finally {
                        this.isLoading = false;
                    }
                },

                // Navigation
                nextStep() {
                    try {
                        this.validateStep(this.step).then(isValid => {
                            if (isValid && this.step < 4) {
                                this.step++;
                            }
                        }).catch(error => {
                            console.error('Next step error:', error);
                        });
                    } catch (error) {
                        console.error('Navigation error:', error);
                    }
                },

                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                    }
                }
            }));

            // Modal Room Edit Component
            Alpine.data('modalRoomEdit', () => ({
                // REACTIVE STATE
                editModalOpen: false,
                editStep: 1,
                selectedPriceType: '',
                priceTypes: {
                    daily: false,
                    monthly: false
                },
                dailyPrice: 0,
                monthlyPrice: 0,
                dailyPriceError: '',
                monthlyPriceError: '',
                selectedFacilities: [],
                formErrors: {},
                isLoading: false,
                dailyPriceFormatted: '',
                monthlyPriceFormatted: '',
                editFormAction: '',
                roomId: null,

                // ROOM DATA
                roomData: {
                    idrec: null,
                    property_id: '',
                    property_name: '',
                    room_no: '',
                    room_name: '',
                    room_bed: '',
                    room_capacity: '',
                    description: '',
                    daily_price: '',
                    monthly_price: '',
                    facilities: [],
                    periode: {}
                },

                // IMAGE HANDLING FOR EDIT
                editExistingImages: [],
                editNewImages: [],
                editDeletedImageIds: [],
                editThumbnailIndex: null,
                editMaxImages: 5,

                // COMPUTED PROPERTIES FOR IMAGES
                get editAllImages() {
                    return [...this.editExistingImages, ...this.editNewImages];
                },
                get editRemainingSlots() {
                    return this.editMaxImages - this.editAllImages.length;
                },
                get editCanUploadMore() {
                    return this.editAllImages.length < this.editMaxImages;
                },

                init() {},

                openModal(roomData) {
                    this.editStep = 1;
                    this.selectedFacilities = [];
                    this.selectedPriceType = '';
                    this.priceTypes = { daily: false, monthly: false };
                    this.dailyPrice = 0;
                    this.monthlyPrice = 0;
                    this.dailyPriceFormatted = '';
                    this.monthlyPriceFormatted = '';

                    this.roomData = {
                        idrec: roomData.idrec,
                        property_id: roomData.property?.idrec || roomData.property_id,
                        property_name: roomData.property?.name || roomData.property_name || 'N/A',
                        room_no: roomData.no || '',
                        room_name: roomData.name || '',
                        room_bed: roomData.bed_type || '',
                        room_capacity: roomData.capacity || '',
                        description: roomData.descriptions || '',
                        daily_price: roomData.price_original_daily || '',
                        monthly_price: roomData.price_original_monthly || '',
                        facilities: roomData.facility || [],
                        periode: roomData.periode || {}
                    };

                    this.roomId = roomData.idrec;
                    this.editFormAction = `/properties/rooms/update/${roomData.idrec}`;

                    try {
                        const periode = typeof this.roomData.periode === 'string'
                            ? JSON.parse(this.roomData.periode)
                            : this.roomData.periode || {};

                        this.priceTypes.daily = Boolean(periode.daily);
                        this.priceTypes.monthly = Boolean(periode.monthly);

                        if (this.priceTypes.daily) {
                            this.selectedPriceType = 'daily';
                        } else if (this.priceTypes.monthly) {
                            this.selectedPriceType = 'monthly';
                        }
                    } catch (e) {
                        console.error('Error parsing periode:', e);
                        this.priceTypes = { daily: false, monthly: false };
                        this.selectedPriceType = '';
                    }

                    if (this.roomData.daily_price) {
                        this.dailyPrice = parseFloat(this.roomData.daily_price) || 0;
                        this.dailyPriceFormatted = this.formatRupiah(this.dailyPrice.toString());
                    }

                    if (this.roomData.monthly_price) {
                        this.monthlyPrice = parseFloat(this.roomData.monthly_price) || 0;
                        this.monthlyPriceFormatted = this.formatRupiah(this.monthlyPrice.toString());
                    }

                    let facilityData = this.roomData.facilities;
                    if (typeof facilityData === 'string') {
                        try {
                            facilityData = JSON.parse(facilityData);
                        } catch (e) {
                            facilityData = [];
                        }
                    }
                    if (facilityData && Array.isArray(facilityData)) {
                        this.selectedFacilities = facilityData.map(f => f.toString());
                    }

                    this.editExistingImages = [];
                    this.editNewImages = [];
                    this.editDeletedImageIds = [];
                    this.editThumbnailIndex = null;

                    this.loadExistingImages(roomData);
                    this.editModalOpen = true;

                    this.$nextTick(() => {
                        if (this.$refs.dailyPriceInput) {
                            this.$refs.dailyPriceInput.value = this.dailyPriceFormatted;
                        }
                        if (this.$refs.monthlyPriceInput) {
                            this.$refs.monthlyPriceInput.value = this.monthlyPriceFormatted;
                        }
                    });
                },

                loadExistingImages(roomData) {
                    const images = roomData.room_images || roomData.roomImages || [];

                    if (images && Array.isArray(images)) {
                        this.editExistingImages = images.map((img, index) => {
                            const imageUrl = img.image.startsWith('http') ? img.image : `/storage/${img.image}`;
                            return {
                                id: img.idrec,
                                url: imageUrl,
                                isExisting: true,
                                isThumbnail: img.thumbnail == 1 || img.thumbnail === true
                            };
                        });

                        const thumbnailIdx = this.editExistingImages.findIndex(img => img.isThumbnail);
                        if (thumbnailIdx !== -1) {
                            this.editThumbnailIndex = thumbnailIdx;
                        }
                    }
                },

                formatRupiah(amount) {
                    if (!amount) return '';
                    const num = typeof amount === 'string' ?
                        Number(amount.replace(/[^\d]/g, '')) :
                        Number(amount);

                    if (isNaN(num)) return '';

                    return new Intl.NumberFormat('id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(num);
                },

                updateDailyPrice(value) {
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.dailyPrice = numericValue ? parseInt(numericValue, 10) : 0;

                    if (this.dailyPrice > 0) {
                        this.dailyPriceFormatted = this.formatRupiah(this.dailyPrice.toString());
                    } else {
                        this.dailyPriceFormatted = '';
                    }
                },

                updateMonthlyPrice(value) {
                    const numericValue = value.replace(/[^\d]/g, '');
                    this.monthlyPrice = numericValue ? parseInt(numericValue, 10) : 0;

                    if (this.monthlyPrice > 0) {
                        this.monthlyPriceFormatted = this.formatRupiah(this.monthlyPrice.toString());
                    } else {
                        this.monthlyPriceFormatted = '';
                    }
                },

                onPriceTypeChange(type) {
                    this.selectedPriceType = type;

                    if (type === 'daily') {
                        this.priceTypes.daily = true;
                        this.priceTypes.monthly = false;
                        this.monthlyPrice = 0;
                        this.monthlyPriceFormatted = '';
                    } else if (type === 'monthly') {
                        this.priceTypes.daily = false;
                        this.priceTypes.monthly = true;
                        this.dailyPrice = 0;
                        this.dailyPriceFormatted = '';
                    }
                },

                async validateEditStep(step) {
                    try {
                        this.clearFormErrors();
                        let isValid = false;

                        switch (step) {
                            case 1:
                                isValid = await this.validateStep1();
                                break;
                            case 2:
                                isValid = this.validateStep2();
                                break;
                            case 3:
                                isValid = true;
                                break;
                            case 4:
                                isValid = this.validateStep4();
                                break;
                            default:
                                throw new Error('Step tidak valid: ' + step);
                        }

                        if (!isValid) {
                            this.highlightErrorFields();
                        }

                        return isValid;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan saat validasi: ' + error.message);
                        return false;
                    }
                },

                async validateStep1() {
                    try {
                        const requiredFields = [
                            { id: 'edit_room_no', name: 'Nomor Kamar', value: this.roomData.room_no },
                            { id: 'edit_room_name', name: 'Nama Kamar', value: this.roomData.room_name },
                            { id: 'edit_room_bed', name: 'Jenis Tempat Tidur', value: this.roomData.room_bed },
                            { id: 'edit_room_capacity', name: 'Kapasitas', value: this.roomData.room_capacity },
                            { id: 'edit_description', name: 'Deskripsi', value: this.roomData.description }
                        ];

                        const missingFields = [];
                        this.formErrors = {};

                        requiredFields.forEach(field => {
                            if (!field.value || field.value.toString().trim() === '') {
                                this.formErrors[field.id] = `${field.name} harus diisi`;
                                missingFields.push(field.name);
                            }
                        });

                        if (missingFields.length > 0) {
                            await this.showErrorAlert(
                                `<strong>Field berikut harus diisi:</strong><br>${missingFields.map(field => '• ' + field).join('<br>')}`,
                                'Data Belum Lengkap'
                            );
                            return false;
                        }

                        return true;
                    } catch (error) {
                        console.error('Validate step 1 error:', error);
                        return false;
                    }
                },

                validateStep2() {
                    try {
                        if (!this.selectedPriceType) {
                            this.showErrorAlert('Pilih jenis periode (Harian atau Bulanan)', 'Jenis Periode Belum Dipilih');
                            return false;
                        }

                        if (this.selectedPriceType === 'daily' && (!this.dailyPrice || this.dailyPrice <= 0)) {
                            this.showErrorAlert('Harga harian harus diisi dan lebih dari 0', 'Harga Harian Tidak Valid');
                            return false;
                        }

                        if (this.selectedPriceType === 'monthly' && (!this.monthlyPrice || this.monthlyPrice <= 0)) {
                            this.showErrorAlert('Harga bulanan harus diisi dan lebih dari 0', 'Harga Bulanan Tidak Valid');
                            return false;
                        }

                        return true;
                    } catch (error) {
                        this.showErrorAlert('Terjadi kesalahan validasi harga: ' + error.message);
                        return false;
                    }
                },

                clearFormErrors() {
                    const errorFields = document.querySelectorAll('.border-red-500');
                    errorFields.forEach(field => {
                        field.classList.remove('border-red-500');
                    });
                    this.formErrors = {};
                },

                highlightErrorFields() {
                    Object.keys(this.formErrors).forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        if (field) {
                            field.classList.add('border-red-500');
                        }
                    });
                },

                handleEditFileSelect(event) {
                    const files = event.target.files;
                    if (files) {
                        this.processEditFiles(Array.from(files));
                    }
                    event.target.value = '';
                },

                handleEditDrop(event) {
                    event.preventDefault();
                    const files = event.dataTransfer.files;
                    if (files) {
                        this.processEditFiles(Array.from(files));
                    }
                },

                processEditFiles(files) {
                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024;

                    for (const file of files) {
                        if (this.editAllImages.length >= this.editMaxImages) {
                            this.showErrorAlert(`Maksimal ${this.editMaxImages} foto yang dapat diupload`, 'Batas Foto Tercapai');
                            break;
                        }

                        if (!validTypes.includes(file.type)) {
                            this.showErrorAlert(`File ${file.name} bukan format gambar yang valid. Gunakan JPEG, PNG, atau WEBP.`, 'Format Tidak Valid');
                            continue;
                        }

                        if (file.size > maxSize) {
                            this.showErrorAlert(`File ${file.name} melebihi ukuran maksimal 5MB`, 'Ukuran File Terlalu Besar');
                            continue;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.editNewImages.push({
                                id: `new_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
                                url: e.target.result,
                                file: file,
                                isExisting: false
                            });

                            if (this.editThumbnailIndex === null && this.editAllImages.length === 1) {
                                this.editThumbnailIndex = 0;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                },

                setEditThumbnail(index) {
                    this.editThumbnailIndex = index;
                },

                removeEditImage(index, event) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    const image = this.editAllImages[index];

                    if (image.isExisting) {
                        this.editDeletedImageIds.push(image.id);
                        this.editExistingImages = this.editExistingImages.filter(img => img.id !== image.id);
                    } else {
                        const newIndex = index - this.editExistingImages.length;
                        this.editNewImages.splice(newIndex, 1);
                    }

                    if (this.editThumbnailIndex === index) {
                        this.editThumbnailIndex = this.editAllImages.length > 0 ? 0 : null;
                    } else if (this.editThumbnailIndex > index) {
                        this.editThumbnailIndex--;
                    }
                },

                validateStep4() {
                    if (this.editAllImages.length < 3) {
                        this.showErrorAlert('Minimal 3 foto harus diupload', 'Foto Belum Lengkap');
                        return false;
                    }

                    if (this.editThumbnailIndex === null) {
                        this.showErrorAlert('Silakan pilih satu foto sebagai thumbnail', 'Thumbnail Belum Dipilih');
                        return false;
                    }

                    return true;
                },

                showErrorAlert(message, title = 'Error') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: title,
                            html: typeof message === 'string' ? message.replace(/\n/g, '<br>') : message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc2626',
                        });
                    } else {
                        alert(title + ': ' + message);
                    }
                },

                showSuccessAlert(message, title = 'Sukses') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: title,
                            text: message,
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#059669',
                        });
                    } else {
                        alert(title + ': ' + message);
                    }
                },

                showLoadingAlert(title = 'Memproses...') {
                    if (typeof Swal !== 'undefined') {
                        return Swal.fire({
                            title: title,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                    }
                },

                async submitEditForm() {
                    let submitBtn = null;
                    let originalBtnContent = null;

                    try {
                        if (!await this.validateStep1() || !this.validateStep2() || !this.validateStep4()) {
                            return;
                        }

                        this.isLoading = true;
                        submitBtn = this.$refs.submitBtn;

                        if (submitBtn) {
                            originalBtnContent = submitBtn.innerHTML;
                            submitBtn.disabled = true;
                            submitBtn.innerHTML = `
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memperbarui Data...
                            `;
                        }

                        const loadingAlert = this.showLoadingAlert('Memperbarui data kamar...');

                        const form = this.$refs.editForm;
                        const formData = new FormData(form);

                        formData.delete('general_facilities[]');
                        formData.delete('daily_price');
                        formData.delete('monthly_price');
                        formData.delete('room_images[]');

                        this.selectedFacilities.forEach(facilityId => {
                            formData.append('general_facilities[]', facilityId);
                        });

                        const periode = {
                            daily: this.selectedPriceType === 'daily',
                            monthly: this.selectedPriceType === 'monthly'
                        };

                        formData.append('periode', JSON.stringify(periode));

                        if (this.selectedPriceType === 'daily') {
                            formData.append('daily_price', this.dailyPrice.toString());
                            formData.append('monthly_price', '0');
                        } else if (this.selectedPriceType === 'monthly') {
                            formData.append('daily_price', '0');
                            formData.append('monthly_price', this.monthlyPrice.toString());
                        }

                        this.editExistingImages.forEach(img => {
                            formData.append('existing_images[]', img.id);
                        });

                        this.editNewImages.forEach(img => {
                            if (img.file) {
                                formData.append('room_images[]', img.file);
                            }
                        });

                        this.editDeletedImageIds.forEach(id => {
                            formData.append('delete_images[]', id);
                        });

                        formData.append('thumbnail_index', this.editThumbnailIndex.toString());

                        const response = await fetch(this.editFormAction, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (typeof Swal !== 'undefined' && Swal.isVisible()) {
                            Swal.close();
                        }

                        if (!response.ok) {
                            let errorMessage = 'Gagal memperbarui data';

                            try {
                                const data = await response.json();

                                if (data.errors) {
                                    if (typeof data.errors === 'object') {
                                        errorMessage = Object.entries(data.errors)
                                            .map(([field, errors]) =>
                                                `${field}: ${Array.isArray(errors) ? errors.join(', ') : errors}`
                                            )
                                            .join('\n');
                                    } else {
                                        errorMessage = JSON.stringify(data.errors);
                                    }
                                } else if (data.message) {
                                    errorMessage = data.message;
                                }
                            } catch (e) {
                                console.error('Response parsing error:', e);
                                errorMessage = `Error ${response.status}: ${response.statusText}`;
                            }

                            throw new Error(errorMessage);
                        }

                        const data = await response.json();

                        await this.showSuccessAlert(
                            data.message || 'Kamar berhasil diperbarui!',
                            'Berhasil'
                        );

                        this.editModalOpen = false;

                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else if (window.Livewire) {
                            Livewire.dispatch('room-updated');
                        } else if (typeof reloadRoomsTable === 'function') {
                            reloadRoomsTable();
                        } else {
                            setTimeout(() => window.location.reload(), 1500);
                        }

                    } catch (error) {
                        console.error('Edit Submission Error:', error);

                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnContent || `
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Update
                            `;
                        }

                        await this.showErrorAlert(
                            error.message || 'Terjadi kesalahan saat memperbarui data',
                            'Gagal Memperbarui'
                        );

                    } finally {
                        this.isLoading = false;
                    }
                }
            }));

            // Modal View Room Component (moved from room-detail-modal.blade.php)
            Alpine.data('modalViewRoom', () => ({
                selectedRoom: {
                    currentImageIndex: 0,
                    images: [],
                    facilities: []
                },
                modalOpenDetail: false,
                isLoading: false,
                touchStartX: 0,
                touchEndX: 0,

                // Facility data from controller
                facilityData: @json($facilityData ?? []),

                getFacilityName(id, type) {
                    // For room facilities
                    if (type === 'room') {
                        const facility = this.facilityData.find(f => f.id == id);
                        return facility ? facility.name : `Facility #${id}`;
                    }
                    return `Facility #${id}`;
                },

                getFacilityIcon(id) {
                    const facility = this.facilityData.find(f => f.id == id);
                    return facility && facility.icon ? facility.icon : '';
                },

                getFacilityDescription(id) {
                    const facility = this.facilityData.find(f => f.id == id);
                    return facility ? facility.description : '';
                },

                openModal(room) {
                    this.isLoading = true;
                    this.modalOpenDetail = true;
                    this.disableBodyScroll();

                    this.$nextTick(() => {
                        // Filter gambar yang valid (URL atau path storage)
                        const validImages = Array.isArray(room.images) ?
                            room.images.filter(img => {
                                if (!img) return false;
                                // Terima URL (http/https) atau path yang mengandung 'room_images'
                                return img.startsWith('http') ||
                                    img.startsWith('/storage') ||
                                    img.includes('room_images');
                            }) : [];

                        this.selectedRoom = {
                            ...room,
                            currentImageIndex: 0,
                            images: validImages,
                            facilities: Array.isArray(room.facilities) ? room.facilities : []
                        };
                        this.isLoading = false;
                        // Scan for Iconify icons in facility display
                        setTimeout(() => { if (window.Iconify) Iconify.scan(); }, 200);
                    });
                },

                closeModal() {
                    this.modalOpenDetail = false;
                    this.enableBodyScroll();
                    setTimeout(() => {
                        this.selectedRoom = {
                            currentImageIndex: 0,
                            images: [],
                            facilities: []
                        };
                    }, 300);
                },

                nextImage() {
                    if (this.hasMultipleImages) {
                        this.selectedRoom.currentImageIndex =
                            (this.selectedRoom.currentImageIndex + 1) % this.selectedRoom.images.length;
                    }
                },

                prevImage() {
                    if (this.hasMultipleImages) {
                        this.selectedRoom.currentImageIndex =
                            (this.selectedRoom.currentImageIndex - 1 + this.selectedRoom.images.length) %
                            this.selectedRoom.images.length;
                    }
                },

                goToImage(index) {
                    if (this.hasMultipleImages && index >= 0 && index < this.selectedRoom.images.length) {
                        this.selectedRoom.currentImageIndex = index;
                    }
                },

                get hasMultipleImages() {
                    return this.selectedRoom.images?.length > 1;
                },

                get currentImage() {
                    return this.selectedRoom.images[this.selectedRoom.currentImageIndex];
                },

                handleTouchStart(e) {
                    this.touchStartX = e.changedTouches[0].screenX;
                },

                handleTouchEnd(e) {
                    this.touchEndX = e.changedTouches[0].screenX;
                    this.handleSwipe();
                },

                handleSwipe() {
                    const threshold = 50;
                    const diff = this.touchStartX - this.touchEndX;

                    if (diff > threshold) {
                        this.nextImage();
                    } else if (diff < -threshold) {
                        this.prevImage();
                    }
                },

                disableBodyScroll() {
                    document.body.style.overflow = 'hidden';
                    document.body.style.paddingRight = this.scrollbarWidth + 'px';
                },

                enableBodyScroll() {
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                },

                get scrollbarWidth() {
                    return window.innerWidth - document.documentElement.clientWidth;
                },

                init() {
                    const handleKeyDown = (e) => {
                        if (!this.modalOpenDetail) return;

                        switch (e.key) {
                            case 'Escape':
                                this.closeModal();
                                break;
                            case 'ArrowRight':
                                this.nextImage();
                                break;
                            case 'ArrowLeft':
                                this.prevImage();
                                break;
                        }
                    };

                    document.addEventListener('keydown', handleKeyDown);

                    // Cleanup when component is destroyed
                    this.$watch('modalOpenDetail', (value) => {
                        if (!value) {
                            this.enableBodyScroll();
                        }
                    });
                },

                destroy() {
                    this.enableBodyScroll();
                }
            }));

            // Price Modal Component (moved from edit-price-daily-modal.blade.php)
            Alpine.data('priceModal', (roomId = null, basePrice = 0) => ({
                isOpen: false,
                isLoading: false,
                selectedDate: null,
                currentMonth: new Date().getMonth(),
                currentYear: new Date().getFullYear(),
                priceMap: {},
                roomId: roomId,
                basePrice: basePrice,
                cleaveInstance: null,

                // Computed properties
                get calendarTitle() {
                    const months = [
                        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                    ];
                    return `${months[this.currentMonth]} ${this.currentYear}`;
                },

                get formattedSelectedDate() {
                    if (!this.selectedDate) return 'Pilih tanggal';
                    return this.selectedDate.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                },

                get formattedDatePrice() {
                    if (!this.selectedDate) return '-';
                    const dateKey = this.formatDateKey(this.selectedDate);
                    const price = this.priceMap[dateKey];
                    if (price === undefined || price === null || isNaN(price)) return '-';
                    return this.formatCurrency(price);
                },

                get formattedBasePrice() {
                    const price = this.basePrice;
                    if (price === undefined || price === null || isNaN(price)) return '-';
                    return this.formatCurrency(price);
                },

                get calendarWeeks() {
                    const weeks = [];
                    const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                    const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);

                    // Start from Sunday (0)
                    let startDate = new Date(firstDay);
                    startDate.setDate(startDate.getDate() - startDate.getDay());

                    for (let week = 0; week < 6; week++) {
                        const days = [];
                        for (let day = 0; day < 7; day++) {
                            const currentDate = new Date(startDate);
                            const dateKey = this.formatDateKey(currentDate);
                            /* Multi-Tier Pricing: priceMap now returns {price, type} objects */
                            const priceData = this.priceMap[dateKey];
                            const price = priceData ? (priceData.price ?? priceData) : undefined;
                            const priceType = priceData ? (priceData.type ?? 'weekday') : null;
                            const isCurrentMonth = currentDate.getMonth() === this.currentMonth;
                            const isToday = this.isToday(currentDate);
                            const isPast = this.isPastDate(currentDate);
                            const isSelected = this.selectedDate && this.isSameDate(currentDate, this.selectedDate);

                            days.push({
                                date: currentDate,
                                isCurrentMonth,
                                isToday,
                                isPast,
                                isSelected,
                                price: price,
                                priceType: priceType,
                            });

                            startDate.setDate(startDate.getDate() + 1);
                        }
                        weeks.push(days);
                    }
                    return weeks;
                },

                // Methods
                openModal() {
                    this.isOpen = true;
                    document.body.classList.add('overflow-hidden');
                    this.$nextTick(() => {
                        this.fetchMonthPrices();
                    });
                },

                closeModal() {
                    this.isOpen = false;
                    document.body.classList.remove('overflow-hidden');
                },

                formatDateKey(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);
                },

                isToday(date) {
                    const today = new Date();
                    return date.toDateString() === today.toDateString();
                },

                isPastDate(date) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const compareDate = new Date(date);
                    compareDate.setHours(0, 0, 0, 0);
                    return compareDate < today;
                },

                isSameDate(date1, date2) {
                    return date1.toDateString() === date2.toDateString();
                },

                async fetchMonthPrices() {
                    try {
                        const res = await fetch(
                            `/properties/rooms/${this.roomId}/prices?year=${this.currentYear}&month=${this.currentMonth + 1}`
                        );

                        if (!res.ok) throw new Error('Failed to fetch prices');

                        const data = await res.json();
                        this.priceMap = data;
                    } catch (error) {
                        console.error('Error fetching prices:', error);
                        this.showAlert('error', 'Gagal memuat data harga');
                    }
                },

                selectDate(day) {
                    if (!day.isCurrentMonth || day.isPast) return;
                    this.selectedDate = day.date;

                    // Set current price in the input field if available
                    if (day.price !== undefined && day.price !== null && this.cleaveInstance) {
                        this.cleaveInstance.setRawValue(day.price.toString());
                    } else if (this.cleaveInstance) {
                        this.cleaveInstance.setRawValue('');
                    }
                },

                previousMonth() {
                    if (this.currentMonth === 0) {
                        this.currentMonth = 11;
                        this.currentYear--;
                    } else {
                        this.currentMonth--;
                    }
                    this.fetchMonthPrices();
                },

                nextMonth() {
                    if (this.currentMonth === 11) {
                        this.currentMonth = 0;
                        this.currentYear++;
                    } else {
                        this.currentMonth++;
                    }
                    this.fetchMonthPrices();
                },

                /* Multi-Tier Pricing: color-code calendar days by price_type */
                getDayColor(day) {
                    if (!day.isCurrentMonth || day.price === undefined || day.price === null) {
                        return 'bg-gray-100';
                    }
                    /* Color by price_type: blue=weekday, purple=weekend, red=high_season, green=low_season, orange=holiday, yellow=manual */
                    const colorMap = {
                        'weekday': 'bg-blue-500 text-white',
                        'weekend': 'bg-purple-500 text-white',
                        'high_season': 'bg-red-500 text-white',
                        'low_season': 'bg-green-500 text-white',
                        'holiday': 'bg-orange-500 text-white',
                        'manual': 'bg-yellow-500 text-white',
                    };
                    return colorMap[day.priceType] || 'bg-blue-500 text-white';
                },

                getPriceIndicatorColor(day) {
                    if (!day.isCurrentMonth || day.price === undefined || day.price === null) {
                        return 'bg-gray-400';
                    }
                    const colorMap = {
                        'weekday': 'bg-blue-200',
                        'weekend': 'bg-purple-200',
                        'high_season': 'bg-red-200',
                        'low_season': 'bg-green-200',
                        'holiday': 'bg-orange-200',
                        'manual': 'bg-yellow-200',
                    };
                    return colorMap[day.priceType] || 'bg-blue-200';
                },

                async updatePrice() {
                    if (!this.selectedDate) {
                        this.showAlert('warning', 'Silakan pilih tanggal terlebih dahulu');
                        return;
                    }

                    this.isLoading = true;

                    try {
                        const priceValue = this.cleaveInstance.getRawValue() ?
                            parseFloat(this.cleaveInstance.getRawValue()) : null;

                        const res = await fetch(`/properties/rooms/${this.roomId}/update-price`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                start_date: this.formatDateKey(this.selectedDate),
                                end_date: this.formatDateKey(this.selectedDate),
                                price: priceValue
                            })
                        });

                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || 'Gagal memperbarui harga');
                        }

                        this.showAlert('success', 'Harga berhasil diperbarui!');
                        await this.fetchMonthPrices();

                        // Reset form
                        this.cleaveInstance.setRawValue('');
                    } catch (error) {
                        console.error('Update error:', error);
                        this.showAlert('error', error.message || 'Terjadi kesalahan saat memperbarui harga');
                    } finally {
                        this.isLoading = false;
                    }
                },

                showAlert(type, message) {
                    Swal.fire({
                        icon: type,
                        title: message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },

                init() {
                    // Initialize price input formatter
                    this.$nextTick(() => {
                        if (this.$refs.setPrice) {
                            this.cleaveInstance = new Cleave(this.$refs.setPrice, {
                                numeral: true,
                                numeralDecimalMark: ',',
                                delimiter: '.',
                                numeralThousandsGroupStyle: 'thousand',
                                onValueChanged: (e) => {
                                    // Value is handled by getRawValue()
                                }
                            });
                        }
                    });
                }
            }));
        });

        // Kapasitas (Pax) is now manual input - auto-fill functionality removed
        // document.getElementById('room_bed').addEventListener('change', function() {
        //     const bed = this.value;
        //     const capacityField = document.getElementById('room_capacity');

        //     if (bed === "Single") {
        //         capacityField.value = 1;
        //         capacityField.readOnly = true;
        //     } else if (bed === "Twin" || bed === "Double" || bed === "Queen" || bed === "King") {
        //         capacityField.value = 2;
        //         capacityField.readOnly = true;
        //     } else {
        //         capacityField.value = "";
        //         capacityField.readOnly = false;
        //     }
        // });

        function toggleRoomStatus(checkbox) {
            const roomId = checkbox.getAttribute('data-id');
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');

            fetch(`/properties/rooms/${roomId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        status: newStatus
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error("Gagal update status");
                    return res.json();
                })
                .then(() => {
                    // Update label status
                    statusLabel.textContent = newStatus === 1 ? 'Active' : 'Inactive';
                    statusLabel.classList.remove('text-green-600', 'text-red-600');
                    statusLabel.classList.add(newStatus === 1 ? 'text-green-600' : 'text-red-600');

                    // Show success toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: newStatus === 1 ? 'Kamar berhasil diaktifkan' : 'Kamar berhasil dinonaktifkan',
                        showConfirmButton: false,
                        timer: 2000
                    });
                })
                .catch(err => {
                    console.error(err);
                    checkbox.checked = !checkbox.checked;

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Gagal memperbarui status kamar',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
        }

        function deleteRoom(id) {
            Swal.fire({
                title: 'Hapus kamar ini?',
                text: 'Data kamar tidak akan tampil lagi setelah dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/properties/rooms/${id}/destroy`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                        })
                        .then(async response => {
                            const data = await response.json();
                            if (!response.ok) {
                                throw new Error(data.message || 'Gagal menghapus data');
                            }
                            return data;
                        })
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                if (typeof reloadRoomsTable === 'function') {
                                    reloadRoomsTable();
                                } else {
                                    location.reload();
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: error.message || 'Terjadi kesalahan saat menghapus!',
                            });
                        });
                }
            });
        }

        // Global function untuk reload rooms table
        function reloadRoomsTable() {
            const roomFilter = document.getElementById('room-filter');
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-input');
            const perPageFilter = document.getElementById('per-page-filter');

            if (!roomFilter || !statusFilter || !searchInput || !perPageFilter) {
                location.reload();
                return;
            }

            const propertyId = roomFilter.value;
            const status = statusFilter.value;
            const searchQuery = searchInput.value.trim();
            const perPage = perPageFilter.value;

            const url = new URL(window.location.href);
            const params = new URLSearchParams(url.search);

            if (propertyId) params.set('property_id', propertyId);
            else params.delete('property_id');

            if (status !== '') params.set('status', status);
            else params.delete('status');

            if (searchQuery) params.set('search', searchQuery);
            else params.delete('search');

            if (perPage) params.set('per_page', perPage);
            else params.set('per_page', '8');

            window.history.replaceState({}, '', `${window.location.pathname}?${params}`);

            document.getElementById('roomTableContainer').innerHTML =
                '<div class="p-4 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><p class="mt-2 text-gray-600">Memuat data...</p></div>';

            fetch(`{{ route('rooms.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const tableContainer = document.getElementById('roomTableContainer');
                    const paginationContainer = document.getElementById('paginationContainer');

                    if (tableContainer) {
                        tableContainer.innerHTML = data.html || '';
                        // Re-initialize Alpine.js components for newly added HTML
                        Alpine.initTree(tableContainer);
                    }
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagination || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    const container = document.getElementById('roomTableContainer');
                    if (container) {
                        container.innerHTML =
                            '<div class="p-4 text-center text-red-600">Terjadi kesalahan saat memuat data.</div>';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Elemen filter
            const roomFilter = document.getElementById('room-filter');
            const statusFilter = document.getElementById('status-filter');
            const searchInput = document.getElementById('search-input');
            const perPageFilter = document.getElementById('per-page-filter');

            // Timer untuk debounce
            let searchTimer;

            // Fungsi untuk memproses filter
            function applyFilters() {
                const propertyId = roomFilter.value;
                const status = statusFilter.value;
                const searchQuery = searchInput.value.trim();
                const perPage = perPageFilter.value;

                // Kirim permintaan AJAX
                fetchRooms(propertyId, status, searchQuery, perPage);
            }

            // Event listeners untuk filter real-time
            roomFilter.addEventListener('change', applyFilters);
            statusFilter.addEventListener('change', applyFilters);
            perPageFilter.addEventListener('change', applyFilters);

            // Debounce untuk search input (menunggu 500ms setelah user berhenti mengetik)
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(applyFilters, 500);
            });

            // Juga bisa trigger filter saat enter di search input
            searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimer);
                    applyFilters();
                }
            });

            // Fungsi AJAX untuk mengambil data
            function fetchRooms(propertyId, status, searchQuery, perPage) {
                const url = new URL(window.location.href);
                const params = new URLSearchParams(url.search);

                // Update parameter
                if (propertyId) params.set('property_id', propertyId);
                else params.delete('property_id');

                if (status !== '') params.set('status', status);
                else params.delete('status');

                if (searchQuery) params.set('search', searchQuery);
                else params.delete('search');

                // Set per_page value
                if (perPage) params.set('per_page', perPage);
                else params.set('per_page', '8');

                // Update URL tanpa reload halaman
                window.history.replaceState({}, '', `${window.location.pathname}?${params}`);

                // Tampilkan loading indicator
                document.getElementById('roomTableContainer').innerHTML =
                    '<div class="p-4 text-center"><div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div><p class="mt-2 text-gray-600">Memuat data...</p></div>';

                // Kirim permintaan
                fetch(`{{ route('rooms.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        // Update tabel dan pagination
                        const tableContainer = document.getElementById('roomTableContainer');
                        const paginationContainer = document.getElementById('paginationContainer');

                        if (tableContainer) {
                            tableContainer.innerHTML = data.html || '';
                            // Re-initialize Alpine.js components for newly added HTML
                            Alpine.initTree(tableContainer);
                        }
                        if (paginationContainer) {
                            paginationContainer.innerHTML = data.pagination || '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const container = document.getElementById('roomTableContainer');
                        if (container) {
                            container.innerHTML =
                                '<div class="p-4 text-center text-red-500">Error loading data. Please try again.</div>';
                        }
                    });
            }

            // Inisialisasi filter dari URL jika ada
            function initializeFiltersFromUrl() {
                const urlParams = new URLSearchParams(window.location.search);

                if (urlParams.has('property_id')) {
                    roomFilter.value = urlParams.get('property_id');
                }

                if (urlParams.has('status')) {
                    statusFilter.value = urlParams.get('status');
                }

                if (urlParams.has('search')) {
                    searchInput.value = urlParams.get('search');
                }

                if (urlParams.has('per_page')) {
                    perPageFilter.value = urlParams.get('per_page');
                }
            }

            initializeFiltersFromUrl();
        });
    </script>

</x-app-layout>
