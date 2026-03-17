<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1
                class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-indigo-500 dark:from-blue-400 dark:to-indigo-400">
                {{ __('ui.property_management') }}
            </h1>
            <div class="mt-4 md:mt-0">
                {{-- New Input Property --}}
                <div x-data="modalProperty()">
                    <!-- Trigger Button -->
                    <button
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200"
                        type="button" @click.prevent="modalOpenDetail = true;" aria-controls="feedback-modal1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                                clip-rule="evenodd" />
                        </svg>
                        {{ __('ui.add_property') }}
                    </button>

                    <!-- Modal backdrop -->
                    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity"
                        x-show="modalOpenDetail" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-out duration-200" x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak></div>

                    <!-- Modal dialog -->
                    <div id="feedback-modal1"
                        class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                        role="dialog" aria-modal="true" x-show="modalOpenDetail"
                        x-transition:enter="transition ease-in-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in-out duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-auto w-3/4 max-h-full flex flex-col text-left"
                            @click.outside="modalOpenDetail = false" @keydown.escape.window="modalOpenDetail = false">

                            <!-- Modal header with step indicator -->
                            <div
                                class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="font-bold text-xl text-gray-800 dark:text-white">
                                        {{ __('ui.add_property') }}
                                    </div>
                                    <button type="button"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                        @click="modalOpenDetail = false">
                                        <div class="sr-only">{{ __('ui.close') }}</div>
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
                                                'bg-blue-600 border-blue-600 text-white' :
                                                'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                            <span class="text-sm font-semibold" x-show="step === 1 || step < 1">1</span>
                                            <svg x-show="step > 1" class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <p class="font-medium transition-colors duration-300"
                                                :class="step >= 1 ? 'text-blue-600 dark:text-blue-400' :
                                                    'text-gray-500 dark:text-gray-400'">
                                                {{ __('ui.basic_information') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Connector -->
                                    <div class="w-16 h-0.5 transition-colors duration-300"
                                        :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                                    <!-- Step 2 -->
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                            :class="step > 2 ? 'bg-blue-600 border-blue-600 text-white' : step === 2 ?
                                                'bg-blue-600 border-blue-600 text-white' :
                                                'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                            <span class="text-sm font-semibold" x-show="step === 2 || step < 2">2</span>
                                            <svg x-show="step > 2" class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <p class="font-medium transition-colors duration-300"
                                                :class="step >= 2 ? 'text-blue-600 dark:text-blue-400' :
                                                    'text-gray-500 dark:text-gray-400'">
                                                {{ __('ui.location_details') }}</p>
                                        </div>
                                    </div>

                                    <!-- Connector -->
                                    <div class="w-16 h-0.5 transition-colors duration-300"
                                        :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                                    <!-- Step 3 (Fasilitas) -->
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                            :class="step > 3 ? 'bg-blue-600 border-blue-600 text-white' : step === 3 ?
                                                'bg-blue-600 border-blue-600 text-white' :
                                                'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                            <span class="text-sm font-semibold" x-show="step === 3 || step < 3">3</span>
                                            <svg x-show="step > 3" class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <p class="font-medium transition-colors duration-300"
                                                :class="step >= 3 ? 'text-blue-600 dark:text-blue-400' :
                                                    'text-gray-500 dark:text-gray-400'">
                                                {{ __('ui.facilities') }}</p>
                                        </div>
                                    </div>

                                    <!-- Connector -->
                                    <div class="w-16 h-0.5 transition-colors duration-300"
                                        :class="step >= 4 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'"></div>

                                    <!-- Step 4 (Foto) -->
                                    <div class="flex items-center">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                            :class="step === 4 ? 'bg-blue-600 border-blue-600 text-white' :
                                                'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                            <span class="text-sm font-semibold">4</span>
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <p class="font-medium transition-colors duration-300"
                                                :class="step >= 4 ? 'text-blue-600 dark:text-blue-400' :
                                                    'text-gray-500 dark:text-gray-400'">
                                                {{ __('ui.photos') }}</p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Modal content -->
                            <div class="flex-1 overflow-y-auto px-6 py-6">
                                <form id="propertyForm" method="POST" action="{{ route('properties.store') }}"
                                    enctype="multipart/form-data" @submit.prevent="submitForm">
                                    @csrf
                                    <!-- Step 1 - Basic Information -->
                                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0">
                                        <div class="space-y-6">
                                            <!-- Nama Properti & Initial -->
                                            <div class="grid grid-cols-12 gap-4">
                                                <!-- Nama Properti -->
                                                <div class="col-span-10">
                                                    <label for="property_name"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.property_name') }} <span
                                                            class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="property_name" name="property_name"
                                                        required
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="{{ __('ui.enter_property_name') }}">
                                                </div>

                                                <!-- Initial -->
                                                <div class="col-span-2">
                                                    <label for="initial"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        Initial <span class="text-red-500">*</span>
                                                    </label>
                                                    <div class="flex items-center">
                                                        <input type="text" id="initial" name="initial" required
                                                            maxlength="3"
                                                            class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white uppercase text-center"
                                                            placeholder="ABC"
                                                            oninput="this.value = this.value.toUpperCase()">
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                        {{ __('ui.max_3_chars') }}</p>
                                                </div>
                                            </div>

                                            <!-- Jenis Properti -->
                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                    {{ __('ui.property_type') }} <span class="text-red-500">*</span>
                                                </label>
                                                <div class="grid grid-cols-2 gap-4">
                                                    <template x-for="type in types" :key="type.value">
                                                        <div class="relative">
                                                            <input :id="'type-' + type.value" name="property_type"
                                                                type="radio" :value="type.value"
                                                                x-model="selectedPropertyType"
                                                                class="sr-only peer" required>
                                                            <label :for="'type-' + type.value"
                                                                class="flex items-center justify-center p-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all duration-200">
                                                                <span x-text="type.label"></span>
                                                            </label>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Gender Penghuni (khusus Kos) -->
                                            <div x-show="selectedPropertyType === 'Kos'"
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="opacity-0 -translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="opacity-100 translate-y-0"
                                                x-transition:leave-end="opacity-0 -translate-y-2">
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                    {{ __('ui.gender_tenant') }} <span class="text-xs font-normal text-gray-500 ml-1">{{ __('ui.gender_optional') }}</span>
                                                </label>
                                                <div class="flex gap-3 flex-wrap">
                                                    <!-- Laki-laki -->
                                                    <button type="button"
                                                        @click="gender = (gender === 'male' ? null : 'male')"
                                                        :class="gender === 'male'
                                                            ? 'border-green-500 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 shadow-sm'
                                                            : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-green-300 hover:bg-green-50/50'"
                                                        class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                        <span class="text-xl font-bold leading-none" :class="gender === 'male' ? 'text-green-500' : 'text-gray-400'">♂</span>
                                                        <span>{{ __('ui.gender_male') }}</span>
                                                    </button>

                                                    <!-- Perempuan -->
                                                    <button type="button"
                                                        @click="gender = (gender === 'female' ? null : 'female')"
                                                        :class="gender === 'female'
                                                            ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 shadow-sm'
                                                            : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-pink-300 hover:bg-pink-50/50'"
                                                        class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                        <span class="text-xl font-bold leading-none" :class="gender === 'female' ? 'text-pink-500' : 'text-gray-400'">♀</span>
                                                        <span>{{ __('ui.gender_female') }}</span>
                                                    </button>

                                                    <!-- Campur -->
                                                    <button type="button"
                                                        @click="gender = (gender === 'mixed' ? null : 'mixed')"
                                                        :class="gender === 'mixed'
                                                            ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 shadow-sm'
                                                            : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-purple-300 hover:bg-purple-50/50'"
                                                        class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                        <span class="text-base font-bold leading-none" :class="gender === 'mixed' ? 'text-green-500' : 'text-gray-400'">♂</span><span class="text-base font-bold leading-none -ml-1" :class="gender === 'mixed' ? 'text-pink-500' : 'text-gray-400'">♀</span>
                                                        <span>{{ __('ui.gender_mixed') }}</span>
                                                    </button>
                                                </div>
                                                <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ __('ui.gender_hint') }}</p>
                                                <input type="hidden" name="gender" :value="gender ?? ''">
                                            </div>

                                            <!-- Deskripsi -->
                                            <div>
                                                <label for="description"
                                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ __('ui.description') }} <span class="text-red-500">*</span>
                                                </label>
                                                <textarea id="description" name="description" rows="4" required
                                                    class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                    placeholder="{{ __('ui.describe_your_property') }}"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 2 - Location Details -->
                                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                        <div class="space-y-6">
                                            <div class="relative">
                                                <label for="full_address"
                                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ __('ui.full_address') }} <span class="text-red-500">*</span>
                                                    <span
                                                        class="text-xs font-normal text-gray-500 ml-2">({{ __('ui.type_to_search_address') }})</span>
                                                </label>
                                                <div class="relative">
                                                    <textarea id="full_address" name="full_address" rows="3" required
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="{{ __('ui.enter_full_address') }}" @input="searchAddress($event.target.value)"
                                                        @focus="showAddressSuggestions = addressSuggestions.length > 0" @click.outside="showAddressSuggestions = false"></textarea>
                                                    <!-- Loading indicator -->
                                                    <div x-show="isAddressSearching" class="absolute right-3 top-3">
                                                        <svg class="animate-spin h-5 w-5 text-blue-500"
                                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12"
                                                                r="10" stroke="currentColor" stroke-width="4">
                                                            </circle>
                                                            <path class="opacity-75" fill="currentColor"
                                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <!-- Address Suggestions Dropdown -->
                                                <div x-show="showAddressSuggestions && addressSuggestions.length > 0"
                                                    x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 translate-y-1"
                                                    x-transition:enter-end="opacity-100 translate-y-0"
                                                    x-transition:leave="transition ease-in duration-150"
                                                    x-transition:leave-start="opacity-100 translate-y-0"
                                                    x-transition:leave-end="opacity-0 translate-y-1"
                                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                                    <template x-for="(suggestion, index) in addressSuggestions"
                                                        :key="index">
                                                        <div @click="selectAddressSuggestion(suggestion)"
                                                            class="px-4 py-3 cursor-pointer hover:bg-blue-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 transition-colors duration-150">
                                                            <div class="flex items-start">
                                                                <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                                    </path>
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                                <span class="text-sm text-gray-700 dark:text-gray-200"
                                                                    x-text="suggestion.display_name"></span>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ __('ui.pinpoint_location') }} <span
                                                        class="text-red-500 ml-1">*</span>
                                                    <span
                                                        class="text-gray-500 dark:text-gray-400 text-sm font-normal ml-2">({{ __('ui.click_to_mark_on_map') }})</span>
                                                </label>
                                                <div id="map"
                                                    class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                                    <div class="text-gray-500 dark:text-gray-400 text-center">
                                                        <svg class="w-12 h-12 mx-auto mb-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                            </path>
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                            </path>
                                                        </svg>
                                                        <p>{{ __('ui.click_to_set_location') }}</p>
                                                    </div>
                                                </div>
                                                <div id="coordinates"
                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400"></div>
                                                <input type="hidden" id="latitude" name="latitude">
                                                <input type="hidden" id="longitude" name="longitude">
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="province"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.province') }} <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="province" name="province" required
                                                        placeholder="{{ __('ui.enter_province') }}"
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </div>

                                                <div>
                                                    <label for="city"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.city_regency') }} <span
                                                            class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="city" name="city" required
                                                        placeholder="{{ __('ui.enter_city_regency') }}"
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="district"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.district') }} <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="district" name="district" required
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="{{ __('ui.enter_district') }}">
                                                </div>

                                                <div>
                                                    <label for="village"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.village') }} <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="text" id="village" name="village" required
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="{{ __('ui.enter_village') }}">
                                                </div>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label for="postal_code"
                                                        class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.postal_code') }}
                                                    </label>
                                                    <input type="text" id="postal_code" name="postal_code"
                                                        class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                        placeholder="{{ __('ui.enter_postal_code') }}">
                                                </div>
                                            </div>

                                            <!-- Nearby Locations Section -->
                                            <div class="mt-6">
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                                                        {{ __('ui.nearby_locations') }}
                                                        <span class="text-xs font-normal text-gray-500 ml-2">({{ __('ui.nearby_locations_desc') }})</span>
                                                    </label>
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button"
                                                            @click="if(document.getElementById('latitude').value && document.getElementById('longitude').value) { fetchNearbyLocations(parseFloat(document.getElementById('latitude').value), parseFloat(document.getElementById('longitude').value)); }"
                                                            :disabled="isFetchingNearby"
                                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-blue-500 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 transition-colors disabled:opacity-50">
                                                            <svg class="w-3.5 h-3.5 mr-1" :class="isFetchingNearby ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                            </svg>
                                                            <span x-text="isFetchingNearby ? '{{ __('ui.fetching_nearby') }}' : '{{ __('ui.refetch_nearby') }}'"></span>
                                                        </button>
                                                        <button type="button"
                                                            @click="showAddCustomForm = !showAddCustomForm"
                                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-green-500 text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/30 transition-colors">
                                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                            </svg>
                                                            {{ __('ui.add_custom_location') }}
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Loading Spinner -->
                                                <div x-show="isFetchingNearby" class="flex items-center justify-center py-8">
                                                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span class="ml-3 text-sm text-gray-500 dark:text-gray-400">{{ __('ui.fetching_nearby') }}</span>
                                                </div>

                                                <!-- Custom Location Form -->
                                                <div x-show="showAddCustomForm" x-transition
                                                    class="mb-4 p-4 border-2 border-dashed border-green-300 dark:border-green-700 rounded-lg bg-green-50/50 dark:bg-green-900/20">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.location_name') }}</label>
                                                            <input type="text" x-model="customLocationName"
                                                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                                placeholder="{{ __('ui.enter_location_name') }}">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.location_category') }}</label>
                                                            <select x-model="customLocationCategory"
                                                                class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                                                <template x-for="(label, key) in nearbyCategories" :key="key">
                                                                    <option :value="key" x-text="label"></option>
                                                                </template>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">{{ __('ui.location_distance') }}</label>
                                                            <div class="flex items-center space-x-2">
                                                                <input type="text" x-model="customLocationDistance"
                                                                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                                                    placeholder="{{ __('ui.enter_distance') }}">
                                                                <button type="button" @click="addCustomLocation()"
                                                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors whitespace-nowrap">
                                                                    {{ __('ui.add') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Nearby Locations List -->
                                                <div x-show="!isFetchingNearby && nearbyLocations.length > 0"
                                                    class="space-y-3 max-h-80 overflow-y-auto">
                                                    <template x-for="(locations, catKey) in nearbyGrouped" :key="catKey">
                                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-2 flex items-center">
                                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="nearbyCategoryIcons[catKey] || nearbyCategoryIcons['custom']"></path>
                                                                </svg>
                                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="nearbyCategories[catKey] || catKey"></span>
                                                                <span class="ml-2 text-xs text-gray-500" x-text="'(' + locations.length + ')'"></span>
                                                            </div>
                                                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                                                <template x-for="(loc, locIdx) in locations" :key="locIdx">
                                                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                                                        <div class="flex items-center min-w-0 flex-1">
                                                                            <span class="text-sm text-gray-800 dark:text-gray-200 truncate" x-text="loc.name"></span>
                                                                        </div>
                                                                        <div class="flex items-center space-x-3 ml-3 flex-shrink-0">
                                                                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded-full" x-text="loc.distance_text"></span>
                                                                            <button type="button" @click="removeNearbyLocation(nearbyLocations.indexOf(loc))"
                                                                                class="text-red-400 hover:text-red-600 transition-colors">
                                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>

                                                <!-- Empty State -->
                                                <div x-show="!isFetchingNearby && nearbyLocations.length === 0"
                                                    class="text-center py-6 border-2 border-dashed border-gray-200 dark:border-gray-600 rounded-lg">
                                                    <svg class="w-10 h-10 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('ui.no_nearby_found') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Step 3 - Facilities -->
                                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-x-4"
                                        x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                        <div class="space-y-8">
                                            <!-- General Facilities -->
                                            <div>
                                                <h3
                                                    class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    {{ __('ui.general_facilities') }}
                                                </h3>
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto p-2">
                                                    @foreach ($generalFacilities as $facility)
                                                        <div class="relative">
                                                            <input id="general-{{ $facility->idrec }}"
                                                                name="general_facilities[]" type="checkbox"
                                                                value="{{ $facility->idrec }}" class="sr-only peer">
                                                            <label for="general-{{ $facility->idrec }}"
                                                                class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all duration-200">
                                                                <div class="flex-1 overflow-hidden">
                                                                    <span
                                                                        class="block break-words flex items-center gap-1.5">
                                                                        @if (!empty($facility->icon))
                                                                            <span class="iconify text-lg" data-icon="{{ $facility->icon }}"></span>
                                                                        @endif
                                                                        {{ $facility->facility }}
                                                                    </span>
                                                                    @if (!empty($facility->description))
                                                                        <span
                                                                            class="block text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                                                                            {{ $facility->description }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if ($generalFacilities->isEmpty())
                                                    <div
                                                        class="text-center py-6 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                                        <svg class="w-10 h-10 mx-auto text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ __('ui.no_general_facilities') }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Security Facilities -->
                                            <div>
                                                <h3
                                                    class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                    </svg>
                                                    {{ __('ui.security_facilities') }}
                                                </h3>
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto p-2">
                                                    @foreach ($securityFacilities as $facility)
                                                        <div class="relative">
                                                            <input id="security-{{ $facility->idrec }}"
                                                                name="security_facilities[]" type="checkbox"
                                                                value="{{ $facility->idrec }}" class="sr-only peer">
                                                            <label for="security-{{ $facility->idrec }}"
                                                                class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/30 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all duration-200">
                                                                <div class="flex-1 overflow-hidden">
                                                                    <span
                                                                        class="block break-words flex items-center gap-1.5">
                                                                        @if (!empty($facility->icon))
                                                                            <span class="iconify text-lg" data-icon="{{ $facility->icon }}"></span>
                                                                        @endif
                                                                        {{ $facility->facility }}
                                                                    </span>
                                                                    @if (!empty($facility->description))
                                                                        <span
                                                                            class="block text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                                                                            {{ $facility->description }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if ($securityFacilities->isEmpty())
                                                    <div
                                                        class="text-center py-6 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                                        <svg class="w-10 h-10 mx-auto text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ __('ui.no_security_facilities') }}</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Amenities -->
                                            <div>
                                                <h3
                                                    class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                                    </svg>
                                                    {{ __('ui.additional_services') }}
                                                </h3>
                                                <div
                                                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto p-2">
                                                    @foreach ($amenitiesFacilities as $facility)
                                                        <div class="relative">
                                                            <input id="amenities-{{ $facility->idrec }}"
                                                                name="amenities_facilities[]" type="checkbox"
                                                                value="{{ $facility->idrec }}" class="sr-only peer">
                                                            <label for="amenities-{{ $facility->idrec }}"
                                                                class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/30 peer-checked:text-purple-600 dark:peer-checked:text-purple-400 transition-all duration-200">
                                                                <div class="flex-1 overflow-hidden">
                                                                    <span
                                                                        class="block break-words flex items-center gap-1.5">
                                                                        @if (!empty($facility->icon))
                                                                            <span class="iconify text-lg" data-icon="{{ $facility->icon }}"></span>
                                                                        @endif
                                                                        {{ $facility->facility }}
                                                                    </span>
                                                                    @if (!empty($facility->description))
                                                                        <span
                                                                            class="block text-xs text-gray-500 dark:text-gray-400 mt-1 break-words">
                                                                            {{ $facility->description }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                @if ($amenitiesFacilities->isEmpty())
                                                    <div
                                                        class="text-center py-6 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg">
                                                        <svg class="w-10 h-10 mx-auto text-gray-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                            {{ __('ui.no_additional_services') }}</p>
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
                                                <label
                                                    class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                    {{ __('ui.property_photos') }} <span class="text-red-500">*</span>
                                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                        ({{ __('ui.min_3_max_10_photos') }} - <span
                                                            x-text="remainingSlots"></span>
                                                        {{ __('ui.slots_remaining') }})
                                                    </span>
                                                </label>

                                                <!-- Thumbnail Selection Area -->
                                                <div class="mb-6">
                                                    <h4
                                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        {{ __('ui.select_thumbnail') }} <span
                                                            class="text-red-500">*</span>
                                                        <span
                                                            class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ __('ui.main_photo_displayed') }})</span>
                                                    </h4>

                                                    <div class="flex items-center space-x-4">
                                                        <!-- Thumbnail Preview -->
                                                        <div class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 overflow-hidden relative"
                                                            x-show="images.length > 0">
                                                            <template
                                                                x-if="thumbnailIndex !== null && images[thumbnailIndex]">
                                                                <img :src="images[thumbnailIndex]?.url"
                                                                    alt="Selected Thumbnail"
                                                                    class="w-full h-full object-cover">
                                                            </template>
                                                            <div class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-500"
                                                                x-show="thumbnailIndex === null">
                                                                <svg class="w-10 h-10" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                            </div>
                                                        </div>

                                                        <!-- Thumbnail Selection Instructions -->
                                                        <div class="flex-1">
                                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                                <span x-show="thumbnailIndex === null"
                                                                    class="font-medium text-red-500">{{ __('ui.no_thumbnail_selected') }}</span>
                                                                <span x-show="thumbnailIndex !== null"
                                                                    class="font-medium text-green-600">{{ __('ui.thumbnail_selected') }}</span>
                                                                {{ __('ui.click_photo_for_thumbnail') }}
                                                            </p>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                {{ __('ui.choose_best_thumbnail_property') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Upload Area -->
                                                <div x-show="canUploadMore" @drop="handleDrop($event)"
                                                    @dragover.prevent @dragenter.prevent
                                                    class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200 cursor-pointer"
                                                    :class="{ 'border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20': canUploadMore }">
                                                    <div class="space-y-2">
                                                        <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        <div
                                                            class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                                            <label for="property_images"
                                                                class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                <span>Upload foto</span>
                                                                <input id="property_images" name="property_images[]"
                                                                    type="file" multiple accept="image/*"
                                                                    @change="handleFileSelect($event)"
                                                                    class="sr-only">
                                                            </label>
                                                            <p class="pl-1">atau drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG,
                                                            JPEG up to 5MB</p>
                                                        <p class="text-xs text-blue-600 dark:text-blue-400"
                                                            x-text="`Dapat upload ${remainingSlots} foto lagi`"></p>
                                                    </div>
                                                </div>

                                                <!-- Full Upload Message -->
                                                <div x-show="!canUploadMore"
                                                    class="border-2 border-green-300 dark:border-green-600 rounded-lg p-8 text-center bg-green-50 dark:bg-green-900/20">
                                                    <div class="space-y-2">
                                                        <svg class="w-12 h-12 mx-auto text-green-500" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <p
                                                            class="text-sm text-green-600 dark:text-green-400 font-medium">
                                                            10 foto telah
                                                            diupload!</p>
                                                        <p class="text-xs text-green-500 dark:text-green-400">Maksimal
                                                            foto telah tercapai
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Image Preview Grid -->
                                                <div x-show="images.length > 0" class="mt-4">
                                                    <h4
                                                        class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                        Foto Terupload
                                                    </h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"
                                                        x-transition:enter="transition ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 scale-95"
                                                        x-transition:enter-end="opacity-100 scale-100">
                                                        <template x-for="(image, index) in images"
                                                            :key="index">
                                                            <div class="relative group" @click="setThumbnail(index)">
                                                                <!-- Image Container -->
                                                                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                                                    :class="thumbnailIndex === index ?
                                                                        'border-blue-600 ring-2 ring-blue-400' :
                                                                        'border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500'">
                                                                    <img :src="image.url"
                                                                        :alt="`Preview ${index + 1}`"
                                                                        class="w-full h-full object-cover">

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
                                                    <div
                                                        class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                        <span>Progress Upload</span>
                                                        <span x-text="`${images.length}/${maxImages} foto`"></span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
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
                                                class="px-6 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                                </svg>
                                                Sebelumnya
                                            </button>
                                            <button type="button" x-show="step < 4"
                                                @click="validateStep(step) && step++"
                                                class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                Selanjutnya
                                                <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </button>
                                            <button type="submit" x-show="step === 4"
                                                class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
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
        </div>

        <!-- Property Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <!-- Search and Filter Section -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
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
                                <input type="text" name="search" id="searchInput"
                                    value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-white"
                                    placeholder="Cari properti...">
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ __('ui.status_filter') }}
                            </span>
                            <select name="status" id="statusFilter"
                                class="border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="1" {{ ($statusFilter ?? '1') == '1' ? 'selected' : '' }}>
                                    {{ __('ui.active') }}
                                </option>
                                <option value="0" {{ ($statusFilter ?? '1') == '0' ? 'selected' : '' }}>
                                    {{ __('ui.inactive') }}
                                </option>
                                <option value="all" {{ ($statusFilter ?? '1') == 'all' ? 'selected' : '' }}>
                                    {{ __('ui.all') }}
                                </option>
                            </select>
                        </div>

                        <!-- Items per Page -->
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">
                                {{ __('ui.items_per_page') }}
                            </span>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="5" {{ request('per_page', 5) == 5 ? 'selected' : '' }}>
                                    {{ __('ui.5') }}
                                </option>
                                <option value="10" {{ request('per_page', 5) == 10 ? 'selected' : '' }}>
                                    {{ __('ui.10') }}
                                </option>
                                <option value="15" {{ request('per_page', 5) == 15 ? 'selected' : '' }}>
                                    {{ __('ui.15') }}
                                </option>
                                <option value="20" {{ request('per_page', 5) == 20 ? 'selected' : '' }}>
                                    {{ __('ui.20') }}
                                </option>
                                <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>
                                    {{ __('ui.all') }}
                                </option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="hidden p-4 text-center">
                <div
                    class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm text-blue-700 transition ease-in-out duration-150">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Memuat data...
                </div>
            </div>

            <div class="overflow-x-auto" id="propertyTableContainer">
                @include('pages.Properties.m-Properties.partials.property_table', [
                    'properties' => $properties,
                    'per_page' => request('per_page', 5),
                ])
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded p-4" id="paginationContainer">
                {{ $properties->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <script>
        // Fungsi global untuk memuat data dengan AJAX
        function loadPropertiesData() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const perPageSelect = document.getElementById('perPageSelect');
            const loadingIndicator = document.getElementById('loadingIndicator');
            const propertyTableContainer = document.getElementById('propertyTableContainer');
            const paginationContainer = document.getElementById('paginationContainer');

            if (!searchInput || !statusFilter || !perPageSelect || !loadingIndicator || !propertyTableContainer || !
                paginationContainer) {
                return;
            }

            // Tampilkan loading indicator
            loadingIndicator.classList.remove('hidden');
            propertyTableContainer.classList.add('opacity-50');

            // Siapkan data form
            const formData = new FormData();
            formData.append('search', searchInput.value);
            formData.append('status', statusFilter.value);
            formData.append('per_page', perPageSelect.value);
            formData.append('_token', '{{ csrf_token() }}');

            // Kirim request AJAX
            fetch('{{ route('properties.filter') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Destroy existing Alpine components before replacing content
                    const existingAlpineElements = propertyTableContainer.querySelectorAll('[x-data]');
                    existingAlpineElements.forEach(el => {
                        if (el._x_dataStack) {
                            Alpine.destroyTree(el);
                        }
                    });

                    // Update tabel
                    propertyTableContainer.innerHTML = data.html;

                    // Reinitialize Alpine on new content
                    Alpine.initTree(propertyTableContainer);

                    // Update pagination jika ada
                    if (data.pagination) {
                        paginationContainer.innerHTML = data.pagination;
                    } else {
                        paginationContainer.innerHTML = '';
                    }

                    // Sembunyikan loading indicator
                    loadingIndicator.classList.add('hidden');
                    propertyTableContainer.classList.remove('opacity-50');
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingIndicator.classList.add('hidden');
                    propertyTableContainer.classList.remove('opacity-50');
                    alert('Terjadi kesalahan saat memuat data.');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            let searchTimeout;
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const perPageSelect = document.getElementById('perPageSelect');

            // Event listener untuk search input dengan debounce
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(loadPropertiesData, 500); // Delay 500ms
            });

            // Event listener untuk status filter
            statusFilter.addEventListener('change', loadPropertiesData);

            // Event listener untuk per page select
            perPageSelect.addEventListener('change', loadPropertiesData);
        });

        function togglePropertyStatus(checkbox) {
            const propertyId = checkbox.getAttribute('data-id');
            const newStatus = checkbox.checked ? 1 : 0;
            const row = checkbox.closest('tr');
            const statusLabel = row.querySelector('.status-label');

            fetch(`/properties/m-properties/${propertyId}/status`, {
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
                    statusLabel.classList.remove('text-green-600', 'text-red-600', 'dark:text-green-400',
                        'dark:text-red-400');
                    statusLabel.classList.add(newStatus === 1 ? 'text-green-600' : 'text-red-600');
                    statusLabel.classList.add(newStatus === 1 ? 'dark:text-green-400' : 'dark:text-red-400');

                    // Show success toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: newStatus === 1 ? 'Properti berhasil diaktifkan' :
                            'Properti berhasil dinonaktifkan',
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
                        title: 'Gagal memperbarui status properti',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('modalProperty', () => ({
                selectedProperty: {},
                modalOpenDetail: false,
                step: 1,
                selectedPropertyType: '',
                gender: null,
                types: [
                    { label: 'Kos', value: 'Kos' },
                    { label: 'Apartment', value: 'Apartment' },
                    { label: 'Villa', value: 'Villa' },
                    { label: 'Hotel', value: 'Hotel' }
                ],
                images: [],
                maxImages: 10,
                minImages: 3,
                map: null,
                marker: null,
                searchQuery: '',
                searchResults: [],
                isSearching: false,
                thumbnailIndex: null,
                // Address autocomplete properties
                addressSuggestions: [],
                showAddressSuggestions: false,
                addressSearchTimeout: null,
                isAddressSearching: false,

                // Nearby locations properties
                nearbyLocations: [],
                isFetchingNearby: false,
                showAddCustomForm: false,
                customLocationName: '',
                customLocationCategory: 'custom',
                customLocationDistance: '',
                nearbyCategories: {
                    'transport': '{!! __("ui.category_transport") !!}',
                    'education': '{!! __("ui.category_education") !!}',
                    'health': '{!! __("ui.category_health") !!}',
                    'shopping': '{!! __("ui.category_shopping") !!}',
                    'worship': '{!! __("ui.category_worship") !!}',
                    'food_drink': '{!! __("ui.category_food_drink") !!}',
                    'finance': '{!! __("ui.category_finance") !!}',
                    'public_service': '{!! __("ui.category_public_service") !!}',
                    'custom': '{!! __("ui.category_custom") !!}'
                },
                nearbyCategoryIcons: {
                    'transport': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    'education': 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                    'health': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'shopping': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
                    'worship': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'food_drink': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'finance': 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    'public_service': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'custom': 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'
                },

                openModal(property) {
                    this.selectedProperty = property;
                    this.modalOpenDetail = true;
                },

                setThumbnail(index) {
                    this.thumbnailIndex = index;
                },

                removeImage(index, event) {
                    if (event) event.preventDefault();

                    // Adjust thumbnail index if we're removing the current thumbnail
                    if (this.thumbnailIndex === index) {
                        this.thumbnailIndex = null;
                    } else if (this.thumbnailIndex > index) {
                        // Decrement thumbnail index if it's after the removed image
                        this.thumbnailIndex--;
                    }

                    this.images.splice(index, 1);
                },

                init() {
                    const provinceSelect = document.getElementById('province');
                    const citySelect = document.getElementById('city');

                    // Watch for modal close to reset form
                    this.$watch('modalOpenDetail', (value) => {
                        if (!value) {
                            this.resetForm();
                        }
                    });

                    // Reset gender when property type changes away from Kos
                    this.$watch('selectedPropertyType', (value) => {
                        if (value !== 'Kos') {
                            this.gender = null;
                        }
                    });

                    this.$watch('step', (value) => {
                        if (value === 2 && typeof L === 'undefined') {
                            this.loadLeaflet().then(() => {
                                // Add a small delay to ensure DOM is ready
                                setTimeout(() => {
                                    this.initMap();
                                }, 100);
                            });
                        } else if (value === 2 && typeof L !== 'undefined' && !this.map) {
                            // Add a small delay to ensure DOM is ready
                            setTimeout(() => {
                                this.initMap();
                            }, 100);
                        }
                    });
                },

                resetForm() {
                    // Reset all form data to initial state
                    this.step = 1;
                    this.selectedPropertyType = '';
                    this.gender = null;
                    this.images = [];
                    this.thumbnailIndex = null;
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.isSearching = false;
                    this.nearbyLocations = [];
                    this.isFetchingNearby = false;
                    this.showAddCustomForm = false;

                    // Reset form element
                    const form = document.getElementById('propertyForm');
                    if (form) {
                        form.reset();
                    }

                    // Clean up map
                    if (this.map) {
                        this.map.remove();
                        this.map = null;
                        this.marker = null;
                    }

                    // Clear coordinates display
                    const coordsElement = document.getElementById('coordinates');
                    if (coordsElement) {
                        coordsElement.textContent = '';
                    }
                },

                loadLeaflet() {
                    return new Promise((resolve) => {
                        if (typeof L !== 'undefined') {
                            resolve();
                            return;
                        }

                        // Load Leaflet CSS
                        const css = document.createElement('link');
                        css.rel = 'stylesheet';
                        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        css.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                        css.crossOrigin = '';
                        document.head.appendChild(css);

                        // Load Leaflet JS
                        const js = document.createElement('script');
                        js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        js.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                        js.crossOrigin = '';
                        js.onload = () => {
                            // Ensure DOM is fully loaded before resolving
                            setTimeout(resolve, 50);
                        };
                        document.head.appendChild(js);
                    });
                },

                initMap() {
                    try {
                        const mapElement = document.getElementById('map');
                        if (!mapElement) {
                            console.error('Map element not found');
                            return;
                        }

                        // Ensure the map element has proper dimensions
                        if (mapElement.offsetHeight === 0) {
                            mapElement.style.height = '400px';
                        }

                        // Default to Jakarta coordinates if no marker set
                        const defaultLat = -6.2088;
                        const defaultLng = 106.8456;

                        // Initialize map
                        this.map = L.map('map', {
                            preferCanvas: true,
                            zoomControl: true
                        }).setView([defaultLat, defaultLng], 13);

                        // Add OpenStreetMap tile layer
                        const tileLayer = L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                                maxZoom: 19,
                                minZoom: 1
                            });

                        tileLayer.on('tileerror', (e) => {
                            console.warn('Tile loading error:', e);
                        });

                        tileLayer.addTo(this.map);

                        // Force map to invalidate size after initialization
                        setTimeout(() => {
                            if (this.map) {
                                this.map.invalidateSize();
                            }
                        }, 200);

                        // Add click event to the map
                        this.map.on('click', (e) => {
                            this.placeMarker(e.latlng);
                            this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                        });

                        // Initialize marker if coordinates exist
                        const latInput = document.getElementById('latitude');
                        const lngInput = document.getElementById('longitude');

                        if (latInput && lngInput && latInput.value && lngInput.value) {
                            const lat = parseFloat(latInput.value);
                            const lng = parseFloat(lngInput.value);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                this.placeMarker({
                                    lat,
                                    lng
                                });
                                this.map.setView([lat, lng], 15);
                            }
                        }

                        console.log('Map initialized successfully');
                    } catch (error) {
                        console.error('Error initializing map:', error);
                    }
                },

                placeMarker(latlng) {
                    if (this.marker) {
                        this.map.removeLayer(this.marker);
                    }

                    this.marker = L.marker(latlng, {
                        draggable: true
                    }).addTo(this.map);

                    // Update coordinates display
                    const coordsElement = document.getElementById('coordinates');
                    if (coordsElement) {
                        coordsElement.textContent =
                            `Latitude: ${latlng.lat.toFixed(6)}, Longitude: ${latlng.lng.toFixed(6)}`;
                    }

                    // Update hidden inputs
                    const latInput = document.getElementById('latitude');
                    const lngInput = document.getElementById('longitude');
                    if (latInput) latInput.value = latlng.lat;
                    if (lngInput) lngInput.value = latlng.lng;

                    // Auto-fetch nearby locations
                    this.fetchNearbyLocations(latlng.lat, latlng.lng);

                    // Update marker position on drag
                    this.marker.on('dragend', (e) => {
                        const newLatLng = e.target.getLatLng();
                        this.reverseGeocode(newLatLng.lat, newLatLng.lng);

                        // Update coordinates and inputs
                        if (coordsElement) {
                            coordsElement.textContent =
                                `Latitude: ${newLatLng.lat.toFixed(6)}, Longitude: ${newLatLng.lng.toFixed(6)}`;
                        }
                        if (latInput) latInput.value = newLatLng.lat;
                        if (lngInput) lngInput.value = newLatLng.lng;

                        // Re-fetch nearby locations after drag
                        this.fetchNearbyLocations(newLatLng.lat, newLatLng.lng);
                    });
                },

                async searchLocation() {
                    if (!this.searchQuery.trim()) return;

                    this.isSearching = true;
                    this.searchResults = [];

                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=5&countrycodes=id&addressdetails=1`
                        );

                        if (!response.ok) throw new Error('Search failed');

                        const results = await response.json();
                        this.searchResults = results;
                    } catch (error) {
                        console.error('Search error:', error);
                        alert('Gagal melakukan pencarian lokasi');
                    } finally {
                        this.isSearching = false;
                    }
                },

                selectSearchResult(result) {
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        this.placeMarker({
                            lat,
                            lng
                        });
                        this.map.setView([lat, lng], 15);

                        // Parse Nominatim result to fill address fields
                        this.parseNominatimResult(result);

                        this.searchResults = [];
                        this.searchQuery = result.display_name;
                    }
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
                        );

                        if (!response.ok) throw new Error('Reverse geocoding failed');

                        const data = await response.json();
                        if (data) {
                            this.parseNominatimResult(data);
                        }
                    } catch (error) {
                        console.error('Reverse geocoding error:', error);
                    }
                },

                parseNominatimResult(result) {
                    // Helper function to safely update form fields
                    const updateField = (id, value) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = value || '';
                        }
                    };

                    const address = result.address || {};

                    // Build full address from display_name or components
                    const fullAddress = result.display_name || [address.road, address.hamlet, address
                            .village, address.town, address.city
                        ]
                        .filter(Boolean).join(', ');
                    updateField('full_address', fullAddress);

                    // Update form fields based on Nominatim response
                    updateField('province', address.state || address.region || '');
                    updateField('city', address.city || address.town || address.county || address
                        .regency || '');
                    updateField('district', address.suburb || address.city_district || address
                        .district || '');
                    updateField('village', address.village || address.hamlet || address.neighbourhood ||
                        address.subdistrict || '');
                    updateField('postal_code', address.postcode || '');
                },

                // Address autocomplete with debounce
                searchAddress(query) {
                    // Clear previous timeout
                    if (this.addressSearchTimeout) {
                        clearTimeout(this.addressSearchTimeout);
                    }

                    // Don't search if query is too short
                    if (!query || query.length < 3) {
                        this.addressSuggestions = [];
                        this.showAddressSuggestions = false;
                        return;
                    }

                    // Debounce 500ms
                    this.addressSearchTimeout = setTimeout(async () => {
                        this.isAddressSearching = true;
                        try {
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id&addressdetails=1`
                            );

                            if (!response.ok) throw new Error('Search failed');

                            const results = await response.json();
                            this.addressSuggestions = results;
                            this.showAddressSuggestions = this.addressSuggestions.length >
                            0;
                        } catch (error) {
                            console.error('Address search error:', error);
                            this.addressSuggestions = [];
                            this.showAddressSuggestions = false;
                        } finally {
                            this.isAddressSearching = false;
                        }
                    }, 500);
                },

                selectAddressSuggestion(suggestion) {
                    const lat = parseFloat(suggestion.lat);
                    const lng = parseFloat(suggestion.lon);

                    // Update full address field
                    const fullAddressField = document.getElementById('full_address');
                    if (fullAddressField) {
                        fullAddressField.value = suggestion.display_name;
                    }

                    // Parse and fill other address fields
                    this.parseNominatimResult(suggestion);

                    // Move map marker if map is initialized
                    if (this.map && !isNaN(lat) && !isNaN(lng)) {
                        this.placeMarker({
                            lat,
                            lng
                        });
                        this.map.setView([lat, lng], 15);
                    } else {
                        // Store coordinates for later when map is initialized
                        const latInput = document.getElementById('latitude');
                        const lngInput = document.getElementById('longitude');
                        if (latInput) latInput.value = lat;
                        if (lngInput) lngInput.value = lng;
                    }

                    // Hide suggestions
                    this.addressSuggestions = [];
                    this.showAddressSuggestions = false;
                },

                // Force map resize when step changes or container becomes visible
                resizeMap() {
                    if (this.map) {
                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 100);
                    }
                },

                // Nearby locations methods
                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                },

                formatDistance(meters) {
                    if (meters < 1000) {
                        return Math.round(meters) + ' m';
                    }
                    return (meters / 1000).toFixed(1) + ' km';
                },

                mapAmenityToCategory(tags) {
                    if (['bus_station', 'bus_stop', 'taxi', 'ferry_terminal'].includes(tags.amenity) ||
                        tags.railway === 'station' || tags.railway === 'halt' ||
                        tags.aeroway === 'aerodrome' || tags.highway === 'bus_stop') {
                        return 'transport';
                    }
                    if (['school', 'university', 'college', 'kindergarten', 'library'].includes(tags.amenity)) {
                        return 'education';
                    }
                    if (['hospital', 'clinic', 'doctors', 'dentist', 'pharmacy'].includes(tags.amenity)) {
                        return 'health';
                    }
                    if (['marketplace', 'supermarket'].includes(tags.amenity) ||
                        ['mall', 'supermarket', 'convenience', 'department_store'].includes(tags.shop)) {
                        return 'shopping';
                    }
                    if (tags.amenity === 'place_of_worship') {
                        return 'worship';
                    }
                    if (['restaurant', 'cafe', 'fast_food', 'food_court'].includes(tags.amenity)) {
                        return 'food_drink';
                    }
                    if (['bank', 'atm'].includes(tags.amenity)) {
                        return 'finance';
                    }
                    if (['police', 'post_office', 'fire_station', 'townhall'].includes(tags.amenity)) {
                        return 'public_service';
                    }
                    return 'custom';
                },

                async fetchNearbyLocations(lat, lng) {
                    if (!lat || !lng) return;
                    this.isFetchingNearby = true;

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 15000);

                    try {
                        const radius = 1500;
                        const query = `[out:json][timeout:10];(node["amenity"~"hospital|clinic|pharmacy|school|university|marketplace|place_of_worship|bus_station|restaurant|cafe|bank|atm|supermarket"](around:${radius},${lat},${lng});node["shop"~"mall|supermarket|department_store"](around:${radius},${lat},${lng});node["railway"~"station|halt"](around:${radius},${lat},${lng}););out body;`;

                        const response = await fetch('https://overpass-api.de/api/interpreter', {
                            method: 'POST',
                            body: 'data=' + encodeURIComponent(query),
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            signal: controller.signal
                        });

                        if (!response.ok) throw new Error('Overpass API request failed');
                        const data = await response.json();

                        const seen = new Set();
                        const locations = [];

                        data.elements.forEach(element => {
                            const elLat = element.lat || (element.center && element.center.lat);
                            const elLng = element.lon || (element.center && element.center.lon);
                            if (!elLat || !elLng) return;

                            const name = element.tags && element.tags.name;
                            if (!name) return;

                            const category = this.mapAmenityToCategory(element.tags);
                            const key = name + '_' + category;
                            if (seen.has(key)) return;
                            seen.add(key);

                            const distance = this.calculateDistance(lat, lng, elLat, elLng);
                            locations.push({
                                name: name,
                                category: category,
                                distance: Math.round(distance),
                                distance_text: this.formatDistance(distance),
                                lat: elLat,
                                lng: elLng,
                                auto: true
                            });
                        });

                        locations.sort((a, b) => a.distance - b.distance);

                        const grouped = {};
                        const filtered = [];
                        locations.forEach(loc => {
                            if (!grouped[loc.category]) grouped[loc.category] = 0;
                            if (grouped[loc.category] < 5) {
                                filtered.push(loc);
                                grouped[loc.category]++;
                            }
                        });

                        const customEntries = this.nearbyLocations.filter(loc => !loc.auto);
                        this.nearbyLocations = [...filtered, ...customEntries];

                        if (filtered.length > 0) {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: '{{ __("ui.nearby_auto_fetched") }}',
                                showConfirmButton: false, timer: 3000, timerProgressBar: true
                            });
                        }
                    } catch (error) {
                        console.error('Overpass API error:', error);
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'warning',
                            title: '{{ __("ui.nearby_fetch_failed") }}',
                            showConfirmButton: false, timer: 3000, timerProgressBar: true
                        });
                    } finally {
                        clearTimeout(timeoutId);
                        this.isFetchingNearby = false;
                    }
                },

                removeNearbyLocation(index) {
                    this.nearbyLocations.splice(index, 1);
                },

                addCustomLocation() {
                    if (!this.customLocationName.trim() || !this.customLocationDistance.trim()) return;

                    let distanceMeters = 0;
                    const distText = this.customLocationDistance.trim().toLowerCase();
                    if (distText.endsWith('km')) {
                        distanceMeters = parseFloat(distText) * 1000;
                    } else {
                        distanceMeters = parseFloat(distText.replace('m', ''));
                    }

                    this.nearbyLocations.push({
                        name: this.customLocationName.trim(),
                        category: this.customLocationCategory,
                        distance: Math.round(distanceMeters) || 0,
                        distance_text: this.formatDistance(distanceMeters || 0),
                        lat: null, lng: null, auto: false
                    });

                    this.customLocationName = '';
                    this.customLocationCategory = 'custom';
                    this.customLocationDistance = '';
                    this.showAddCustomForm = false;
                },

                get nearbyGrouped() {
                    const groups = {};
                    this.nearbyLocations.forEach(loc => {
                        const cat = loc.category || 'custom';
                        if (!groups[cat]) groups[cat] = [];
                        groups[cat].push(loc);
                    });
                    return groups;
                },

                // Enhanced photo upload methods
                handleFileSelect(event) {
                    const files = Array.from(event.target.files);
                    const wasEmpty = this.images.length === 0;
                    this.processFiles(files);

                    if (wasEmpty && this.images.length > 0) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'Foto pertama akan menjadi thumbnail properti',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                },

                handleDrop(event) {
                    event.preventDefault();
                    const files = Array.from(event.dataTransfer.files);
                    this.processFiles(files);
                },

                processFiles(files) {
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    const availableSlots = this.maxImages - this.images.length;

                    if (availableSlots <= 0) {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: `Maksimal hanya ${this.maxImages} foto yang dapat diupload.`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                        return;
                    }


                    const filesToProcess = imageFiles.slice(0, availableSlots);

                    if (imageFiles.length > availableSlots) {
                        Swal.fire({
                            toast: true,
                            icon: 'warning',
                            title: `Hanya ${availableSlots} foto yang dapat ditambahkan.`,
                            text: `Sisa slot: ${availableSlots}`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }


                    filesToProcess.forEach(file => {
                        if (file.size <= 5 * 1024 * 1024) { // 5MB limit
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.images.push({
                                    file: file,
                                    url: e.target.result,
                                    name: file.name
                                });
                            };
                            reader.readAsDataURL(file);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: `File ${file.name} terlalu besar. Maksimal 5MB.`,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal
                                        .stopTimer)
                                    toast.addEventListener('mouseleave', Swal
                                        .resumeTimer)
                                }
                            });
                        }
                    });

                    // Clear the file input to allow re-selection
                    if (event.target) {
                        event.target.value = '';
                    }
                },

                removeImage(index, event) {
                    if (event) event.preventDefault(); // mencegah form submit
                    this.images.splice(index, 1);
                },

                get canUploadMore() {
                    return this.images.length < this.maxImages;
                },

                get remainingSlots() {
                    return this.maxImages - this.images.length;
                },

                get imageUploadStatus() {
                    const current = this.images.length;
                    if (current < this.minImages) {
                        return `Minimal ${this.minImages} foto diperlukan (${current}/${this.minImages})`;
                    } else if (current >= this.minImages && current < this.maxImages) {
                        return `${current}/${this.maxImages} foto (dapat menambah ${this.remainingSlots} lagi)`;
                    } else {
                        return `${current}/${this.maxImages} foto (maksimal tercapai)`;
                    }
                },

                get isImageRequirementMet() {
                    return this.images.length >= this.minImages;
                },

                validateStep(step) {
                    let isValid = true;

                    if (step === 1) {
                        const requiredFields = ['property_name'];

                        requiredFields.forEach(field => {
                            const el = document.getElementById(field);
                            if (el) {
                                if (!el.value) {
                                    el.classList.add('border-red-500');
                                    isValid = false;
                                } else {
                                    el.classList.remove('border-red-500');
                                }
                            }
                        });

                        if (!document.querySelector('input[name="property_type"]:checked')) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Pilih jenis properti terlebih dahulu!',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                            });
                            isValid = false;
                        }

                    } else if (step === 2) {
                        const requiredFields = ['full_address', 'province', 'city', 'district',
                            'village'
                        ];

                        requiredFields.forEach(field => {
                            const el = document.getElementById(field);
                            if (el) {
                                if (!el.value) {
                                    el.classList.add('border-red-500');
                                    isValid = false;
                                } else {
                                    el.classList.remove('border-red-500');
                                }
                            }
                        });

                        if (!this.marker || !this.marker.getLatLng()) {
                            alert('Pinpoint lokasi wajib dipilih');
                            isValid = false;
                        }
                    } else if (step === 3) {
                        // No validation needed for step 3 (facilities) as they're optional
                    } else if (step === 4) {
                        // Updated validation for new image requirements
                        if (this.images.length < this.minImages) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: `Minimal ${this.minImages} foto properti harus diupload. Saat ini: ${this.images.length} foto.`,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            isValid = false;
                        } else if (this.thumbnailIndex === null) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: 'Anda harus memilih thumbnail untuk melanjutkan',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal.stopTimer)
                                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                                }
                            });
                            isValid = false;
                        }

                    }

                    return isValid;
                },

                getImageFiles() {
                    return this.images.map(img => img.file);
                },

                resetImages() {
                    this.images = [];
                },

                // Enhanced submit form with better image handling
                submitForm() {
                    if (!this.validateStep(4)) return;

                    // Store original button state
                    const submitBtn = document.querySelector('#propertyForm button[type="submit"]');
                    const originalBtnContent = submitBtn?.innerHTML;
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Menyimpan...
                                            `;
                    }

                    const form = document.getElementById('propertyForm');
                    const formData = new FormData(form);

                    // Clear any existing file inputs
                    formData.delete('property_images[]');
                    formData.append('thumbnail_index', this.thumbnailIndex);

                    // Add each selected image
                    this.images.forEach((image, index) => {
                        formData.append('property_images[]', image.file);
                    });

                    // Add image count for backend validation
                    formData.append('image_count', this.images.length);

                    // Add nearby locations as JSON
                    formData.append('nearby_locations', JSON.stringify(this.nearbyLocations));

                    // Submit the form
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(async response => {
                            // First check if response is JSON
                            const contentType = response.headers.get('content-type');
                            if (!contentType || !contentType.includes('application/json')) {
                                const text = await response.text();
                                throw new Error(
                                    `Expected JSON but got: ${text.substring(0, 100)}...`);
                            }

                            const data = await response.json();

                            if (!response.ok) {
                                // Handle server-side validation errors
                                let errorMsg = data.message || 'Submission failed';
                                if (data.errors) {
                                    errorMsg = Object.values(data.errors).join('\n');
                                }
                                throw new Error(errorMsg);
                            }

                            return data;
                        })
                        .then(data => {
                            // Close the modal first before showing success message
                            this.modalOpenDetail = false;

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: `Property berhasil disimpan dengan ${this.images.length} foto!`,
                                showConfirmButton: false,
                                timer: 1000,
                                timerProgressBar: true,
                                didClose: () => {
                                    // Reload table only, no full page refresh
                                    if (typeof loadPropertiesData === 'function') {
                                        loadPropertiesData();
                                    } else {
                                        window.location.href =
                                            '{{ route('properties.index') }}';
                                    }
                                }
                            });
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: error.message || 'Failed to submit form',
                            });
                        })
                        .finally(() => {
                            if (submitBtn) {
                                submitBtn.disabled = false;
                                submitBtn.innerHTML = originalBtnContent;
                            }
                        });
                },

                // Helper method to get upload progress info
                getUploadInfo() {
                    return {
                        current: this.images.length,
                        min: this.minImages,
                        max: this.maxImages,
                        remaining: this.remainingSlots,
                        canUpload: this.canUploadMore,
                        isValid: this.isImageRequirementMet
                    };
                }
            }));
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('modalPropertyEdit', (property = {}) => ({
                editModalOpen: false,
                editStep: 1,
                editMinImages: 3,
                editMaxImages: 10,
                editImages: [],
                map: null,
                marker: null,
                searchQuery: '',
                searchResults: [],
                isSearching: false,
                isSubmitting: false,
                originalPropertyData: {},
                thumbnailIndex: 0,
                isDragging: false,
                propertyIdrec: property.idrec || null,
                // Address autocomplete properties
                addressSuggestions: [],
                showAddressSuggestions: false,
                addressSearchTimeout: null,
                isAddressSearching: false,

                // Nearby locations properties
                nearbyLocations: Array.isArray(property.nearby_locations) ? property.nearby_locations : [],
                isFetchingNearby: false,
                showAddCustomForm: false,
                customLocationName: '',
                customLocationCategory: 'custom',
                customLocationDistance: '',
                nearbyCategories: {
                    'transport': '{!! __("ui.category_transport") !!}',
                    'education': '{!! __("ui.category_education") !!}',
                    'health': '{!! __("ui.category_health") !!}',
                    'shopping': '{!! __("ui.category_shopping") !!}',
                    'worship': '{!! __("ui.category_worship") !!}',
                    'food_drink': '{!! __("ui.category_food_drink") !!}',
                    'finance': '{!! __("ui.category_finance") !!}',
                    'public_service': '{!! __("ui.category_public_service") !!}',
                    'custom': '{!! __("ui.category_custom") !!}'
                },
                nearbyCategoryIcons: {
                    'transport': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    'education': 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                    'health': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'shopping': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
                    'worship': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'food_drink': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'finance': 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    'public_service': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'custom': 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'
                },

                propertyData: {
                    name: property.name || '',
                    initial: property.initial || '',
                    tags: property.tags || 'Kos',
                    gender: property.gender || null,
                    description: property.description || '',
                    address: property.address || '',
                    latitude: property.latitude || null,
                    longitude: property.longitude || null,
                    province: property.province || '',
                    city: property.city || '',
                    subdistrict: property.subdistrict || '',
                    village: property.village || '',
                    postal_code: property.postal_code || '',
                    general: Array.isArray(property.general) ? property.general : [],
                    security: Array.isArray(property.security) ? property.security : [],
                    amenities: Array.isArray(property.amenities) ? property.amenities : [],
                    existingImages: Array.isArray(property.existingImages) ? property.existingImages :
                    []
                },

                init() {
                    this.originalPropertyData = JSON.parse(JSON.stringify(this.propertyData));

                    this.$watch('editStep', (value) => {
                        if (value === 2) {
                            this.$nextTick(() => {
                                this.initMap();
                            });
                        }
                    });

                    // Reset gender when property type changes away from Kos
                    this.$watch('propertyData.tags', (value) => {
                        if (value !== 'Kos') {
                            this.propertyData.gender = null;
                        }
                    });

                    const firstNonDeleted = this.propertyData.existingImages.findIndex(img => !img
                        .markedForDeletion);
                    if (firstNonDeleted !== -1) {
                        this.thumbnailIndex = firstNonDeleted;
                    }
                },

                get editRemainingSlots() {
                    return this.editMaxImages - (this.propertyData.existingImages.filter(img => !img
                        .markedForDeletion).length + this.editImages.length);
                },

                get editCanUploadMore() {
                    return (this.propertyData.existingImages.filter(img => !img.markedForDeletion)
                        .length + this.editImages.length) < this.editMaxImages;
                },

                get editUploadProgress() {
                    const totalCurrentImages = this.propertyData.existingImages.filter(img => !img
                        .markedForDeletion).length + this.editImages.length;
                    const percentage = Math.min(100, (totalCurrentImages / this.editMaxImages) *
                        100);

                    return {
                        percentage,
                        status: totalCurrentImages < this.editMinImages ? 'danger' :
                            totalCurrentImages >= this.editMinImages && totalCurrentImages < this
                            .editMaxImages ? 'warning' : 'success'
                    };
                },

                get editImageUploadStatus() {
                    const totalCurrentImages = this.propertyData.existingImages.filter(img => !img
                        .markedForDeletion).length + this.editImages.length;

                    if (totalCurrentImages < this.editMinImages) {
                        return {
                            class: 'text-red-600',
                            message: `Minimal ${this.editMinImages} foto diperlukan (${totalCurrentImages}/${this.editMinImages})`
                        };
                    } else if (totalCurrentImages >= this.editMinImages && totalCurrentImages < this
                        .editMaxImages) {
                        return {
                            class: 'text-yellow-600',
                            message: `${totalCurrentImages}/${this.editMaxImages} foto (dapat menambah ${this.editMaxImages - totalCurrentImages} lagi)`
                        };
                    } else {
                        return {
                            class: 'text-green-600',
                            message: `${totalCurrentImages}/${this.editMaxImages} foto (maksimal tercapai)`
                        };
                    }
                },

                get hasMinimumImages() {
                    const totalCurrentImages = this.editImages.length +
                        this.propertyData.existingImages.filter(img => !img.markedForDeletion)
                        .length;
                    return totalCurrentImages >= this.editMinImages;
                },

                openModal(data = null) {
                    // If data is provided, merge it with existing propertyData
                    if (data) {
                        this.propertyData = {
                            ...this.propertyData,
                            ...data,
                            general: Array.isArray(data.general) ? data.general : [],
                            security: Array.isArray(data.security) ? data.security : [],
                            amenities: Array.isArray(data.amenities) ? data.amenities : []
                        };
                    }

                    this.editModalOpen = true;
                    this.editStep = 1;
                    this.editImages = [];
                    this.searchResults = [];
                    this.searchQuery = '';

                    // Find the existing thumbnail index
                    const thumbnailIndex = this.propertyData.existingImages.findIndex(
                        img => img.is_thumbnail
                    );
                    if (thumbnailIndex !== -1) {
                        this.thumbnailIndex = thumbnailIndex;
                    } else {
                        this.thumbnailIndex = 0;
                    }

                    this.$nextTick(() => {
                        if (this.editStep === 2) {
                            this.initMap();
                        }
                    });
                },

                async initMap() {
                    try {
                        // Load Leaflet if not already loaded
                        if (typeof L === 'undefined') {
                            await this.loadLeaflet();
                        }

                        const mapId = `map_edit_${this.propertyIdrec}`;
                        const mapElement = document.getElementById(mapId);

                        if (!mapElement) {
                            console.error('Map element not found');
                            return;
                        }

                        // Ensure the map element has proper dimensions
                        if (mapElement.offsetHeight === 0) {
                            mapElement.style.height = '400px';
                        }

                        // Default to Jakarta coordinates if no coordinates are set
                        const defaultLat = -6.1754;
                        const defaultLng = 106.8272;

                        // Use property coordinates if available, otherwise use default
                        const initialLat = this.propertyData.latitude || defaultLat;
                        const initialLng = this.propertyData.longitude || defaultLng;

                        // Initialize map
                        this.map = L.map(mapId, {
                            preferCanvas: true,
                            zoomControl: true
                        }).setView([initialLat, initialLng], 15);

                        // Add OpenStreetMap tile layer
                        const tileLayer = L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                                maxZoom: 19,
                                minZoom: 1
                            });

                        tileLayer.on('tileerror', (e) => {
                            console.warn('Tile loading error:', e);
                        });

                        tileLayer.addTo(this.map);

                        // Force map to invalidate size after initialization
                        setTimeout(() => {
                            if (this.map) {
                                this.map.invalidateSize();
                            }
                        }, 200);

                        // Add click event to the map
                        this.map.on('click', (e) => {
                            this.placeMarker(e.latlng);
                            this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                        });

                        // Initialize marker if coordinates exist
                        if (this.propertyData.latitude && this.propertyData.longitude) {
                            const lat = parseFloat(this.propertyData.latitude);
                            const lng = parseFloat(this.propertyData.longitude);
                            if (!isNaN(lat) && !isNaN(lng)) {
                                this.placeMarker({
                                    lat,
                                    lng
                                });
                                this.map.setView([lat, lng], 15);
                            }
                        }

                        console.log('Map initialized successfully');
                    } catch (error) {
                        console.error('Error initializing map:', error);
                    }
                },

                async loadLeaflet() {
                    return new Promise((resolve) => {
                        if (typeof L !== 'undefined') {
                            resolve();
                            return;
                        }

                        // Load Leaflet CSS
                        const css = document.createElement('link');
                        css.rel = 'stylesheet';
                        css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        css.integrity =
                            'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
                        css.crossOrigin = '';
                        document.head.appendChild(css);

                        // Load Leaflet JS
                        const js = document.createElement('script');
                        js.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        js.integrity =
                            'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
                        js.crossOrigin = '';
                        js.onload = () => {
                            setTimeout(resolve, 50);
                        };
                        document.head.appendChild(js);
                    });
                },

                placeMarker(latlng) {
                    // Parse coordinates as floats to ensure they're numbers
                    const lat = parseFloat(latlng.lat);
                    const lng = parseFloat(latlng.lng);

                    if (isNaN(lat) || isNaN(lng)) {
                        console.error('Invalid coordinates:', latlng);
                        return;
                    }

                    if (this.marker) {
                        this.map.removeLayer(this.marker);
                    }

                    this.marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(this.map);

                    // Update coordinates display
                    const coordsElement = document.getElementById(
                        `coordinates_edit_${this.propertyIdrec}`);
                    if (coordsElement) {
                        coordsElement.innerHTML =
                            `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    }

                    // Update property data
                    this.propertyData.latitude = lat;
                    this.propertyData.longitude = lng;

                    // Auto-fetch nearby locations
                    this.fetchNearbyLocations(lat, lng);

                    // Update marker position on drag
                    this.marker.on('dragend', (e) => {
                        const newLatLng = e.target.getLatLng();
                        this.reverseGeocode(newLatLng.lat, newLatLng.lng);

                        // Update coordinates display
                        if (coordsElement) {
                            coordsElement.innerHTML =
                                `Koordinat: ${newLatLng.lat.toFixed(6)}, ${newLatLng.lng.toFixed(6)}`;
                        }

                        // Update property data
                        this.propertyData.latitude = newLatLng.lat;
                        this.propertyData.longitude = newLatLng.lng;

                        // Re-fetch nearby locations after drag
                        this.fetchNearbyLocations(newLatLng.lat, newLatLng.lng);
                    });
                },

                async searchLocation() {
                    if (!this.searchQuery.trim()) return;

                    this.isSearching = true;
                    this.searchResults = [];

                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=5&countrycodes=id&addressdetails=1`
                        );

                        if (!response.ok) throw new Error('Search failed');

                        const results = await response.json();
                        this.searchResults = results;
                    } catch (error) {
                        console.error('Search error:', error);
                        alert('Gagal melakukan pencarian lokasi');
                    } finally {
                        this.isSearching = false;
                    }
                },

                selectSearchResult(result) {
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        this.placeMarker({
                            lat,
                            lng
                        });
                        this.map.setView([lat, lng], 15);

                        // Parse Nominatim result to fill address fields
                        this.parseNominatimResult(result);

                        this.searchResults = [];
                        this.searchQuery = result.display_name;
                    }
                },

                async reverseGeocode(lat, lng) {
                    try {
                        const response = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
                        );

                        if (!response.ok) throw new Error('Reverse geocoding failed');

                        const data = await response.json();
                        if (data) {
                            this.parseNominatimResult(data);
                        }
                    } catch (error) {
                        console.error('Reverse geocoding error:', error);
                    }
                },

                parseNominatimResult(result) {
                    // Helper function to safely update form fields
                    const updateField = (id, value) => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.value = value || '';
                        }
                    };

                    const address = result.address || {};

                    // Build full address from display_name or components
                    const fullAddress = result.display_name || [address.road, address.hamlet, address
                            .village, address.town, address.city
                        ]
                        .filter(Boolean).join(', ');
                    updateField(`full_address_edit_${property.idrec}`, fullAddress);
                    this.propertyData.address = fullAddress;

                    // Extract address components
                    const province = address.state || address.region || '';
                    const city = address.city || address.town || address.county || address.regency ||
                    '';
                    const district = address.suburb || address.city_district || address.district || '';
                    const village = address.village || address.hamlet || address.neighbourhood ||
                        address.subdistrict || '';
                    const postalCode = address.postcode || '';

                    // Update form fields and propertyData
                    updateField(`province_edit_${property.idrec}`, province);
                    this.propertyData.province = province;

                    updateField(`city_edit_${property.idrec}`, city);
                    this.propertyData.city = city;

                    updateField(`district_edit_${property.idrec}`, district);
                    this.propertyData.subdistrict = district;

                    updateField(`village_edit_${property.idrec}`, village);
                    this.propertyData.village = village;

                    updateField(`postal_code_edit_${property.idrec}`, postalCode);
                    this.propertyData.postal_code = postalCode;
                },

                // Address autocomplete with debounce
                searchAddress(query) {
                    // Clear previous timeout
                    if (this.addressSearchTimeout) {
                        clearTimeout(this.addressSearchTimeout);
                    }

                    // Don't search if query is too short
                    if (!query || query.length < 3) {
                        this.addressSuggestions = [];
                        this.showAddressSuggestions = false;
                        return;
                    }

                    // Debounce 500ms
                    this.addressSearchTimeout = setTimeout(async () => {
                        this.isAddressSearching = true;
                        try {
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=id&addressdetails=1`
                            );

                            if (!response.ok) throw new Error('Search failed');

                            const results = await response.json();
                            this.addressSuggestions = results;
                            this.showAddressSuggestions = this.addressSuggestions.length >
                            0;
                        } catch (error) {
                            console.error('Address search error:', error);
                            this.addressSuggestions = [];
                            this.showAddressSuggestions = false;
                        } finally {
                            this.isAddressSearching = false;
                        }
                    }, 500);
                },

                selectAddressSuggestion(suggestion) {
                    const lat = parseFloat(suggestion.lat);
                    const lng = parseFloat(suggestion.lon);

                    // Update full address field
                    const fullAddressField = document.getElementById(
                        `full_address_edit_${property.idrec}`);
                    if (fullAddressField) {
                        fullAddressField.value = suggestion.display_name;
                    }
                    this.propertyData.address = suggestion.display_name;

                    // Parse and fill other address fields
                    this.parseNominatimResult(suggestion);

                    // Move map marker if map is initialized
                    if (this.map && !isNaN(lat) && !isNaN(lng)) {
                        this.placeMarker({
                            lat,
                            lng
                        });
                        this.map.setView([lat, lng], 15);
                    } else {
                        // Store coordinates for later
                        this.propertyData.latitude = lat;
                        this.propertyData.longitude = lng;
                    }

                    // Hide suggestions
                    this.addressSuggestions = [];
                    this.showAddressSuggestions = false;
                },

                resizeMap() {
                    if (this.map) {
                        setTimeout(() => {
                            this.map.invalidateSize();
                        }, 100);
                    }
                },

                // Nearby locations methods
                calculateDistance(lat1, lon1, lat2, lon2) {
                    const R = 6371000;
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                        Math.sin(dLon / 2) * Math.sin(dLon / 2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                    return R * c;
                },

                formatDistance(meters) {
                    if (meters < 1000) {
                        return Math.round(meters) + ' m';
                    }
                    return (meters / 1000).toFixed(1) + ' km';
                },

                mapAmenityToCategory(tags) {
                    if (['bus_station', 'bus_stop', 'taxi', 'ferry_terminal'].includes(tags.amenity) ||
                        tags.railway === 'station' || tags.railway === 'halt' ||
                        tags.aeroway === 'aerodrome' || tags.highway === 'bus_stop') {
                        return 'transport';
                    }
                    if (['school', 'university', 'college', 'kindergarten', 'library'].includes(tags.amenity)) {
                        return 'education';
                    }
                    if (['hospital', 'clinic', 'doctors', 'dentist', 'pharmacy'].includes(tags.amenity)) {
                        return 'health';
                    }
                    if (['marketplace', 'supermarket'].includes(tags.amenity) ||
                        ['mall', 'supermarket', 'convenience', 'department_store'].includes(tags.shop)) {
                        return 'shopping';
                    }
                    if (tags.amenity === 'place_of_worship') {
                        return 'worship';
                    }
                    if (['restaurant', 'cafe', 'fast_food', 'food_court'].includes(tags.amenity)) {
                        return 'food_drink';
                    }
                    if (['bank', 'atm'].includes(tags.amenity)) {
                        return 'finance';
                    }
                    if (['police', 'post_office', 'fire_station', 'townhall'].includes(tags.amenity)) {
                        return 'public_service';
                    }
                    return 'custom';
                },

                async fetchNearbyLocations(lat, lng) {
                    if (!lat || !lng) return;
                    this.isFetchingNearby = true;

                    const controller = new AbortController();
                    const timeoutId = setTimeout(() => controller.abort(), 15000);

                    try {
                        const radius = 1500;
                        const query = `[out:json][timeout:10];(node["amenity"~"hospital|clinic|pharmacy|school|university|marketplace|place_of_worship|bus_station|restaurant|cafe|bank|atm|supermarket"](around:${radius},${lat},${lng});node["shop"~"mall|supermarket|department_store"](around:${radius},${lat},${lng});node["railway"~"station|halt"](around:${radius},${lat},${lng}););out body;`;

                        const response = await fetch('https://overpass-api.de/api/interpreter', {
                            method: 'POST',
                            body: 'data=' + encodeURIComponent(query),
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            signal: controller.signal
                        });

                        if (!response.ok) throw new Error('Overpass API request failed');
                        const data = await response.json();

                        const seen = new Set();
                        const locations = [];

                        data.elements.forEach(element => {
                            const elLat = element.lat || (element.center && element.center.lat);
                            const elLng = element.lon || (element.center && element.center.lon);
                            if (!elLat || !elLng) return;

                            const name = element.tags && element.tags.name;
                            if (!name) return;

                            const category = this.mapAmenityToCategory(element.tags);
                            const key = name + '_' + category;
                            if (seen.has(key)) return;
                            seen.add(key);

                            const distance = this.calculateDistance(lat, lng, elLat, elLng);
                            locations.push({
                                name: name,
                                category: category,
                                distance: Math.round(distance),
                                distance_text: this.formatDistance(distance),
                                lat: elLat,
                                lng: elLng,
                                auto: true
                            });
                        });

                        locations.sort((a, b) => a.distance - b.distance);

                        const grouped = {};
                        const filtered = [];
                        locations.forEach(loc => {
                            if (!grouped[loc.category]) grouped[loc.category] = 0;
                            if (grouped[loc.category] < 5) {
                                filtered.push(loc);
                                grouped[loc.category]++;
                            }
                        });

                        const customEntries = this.nearbyLocations.filter(loc => !loc.auto);
                        this.nearbyLocations = [...filtered, ...customEntries];

                        if (filtered.length > 0) {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: '{{ __("ui.nearby_auto_fetched") }}',
                                showConfirmButton: false, timer: 3000, timerProgressBar: true
                            });
                        }
                    } catch (error) {
                        console.error('Overpass API error:', error);
                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'warning',
                            title: '{{ __("ui.nearby_fetch_failed") }}',
                            showConfirmButton: false, timer: 3000, timerProgressBar: true
                        });
                    } finally {
                        clearTimeout(timeoutId);
                        this.isFetchingNearby = false;
                    }
                },

                removeNearbyLocation(index) {
                    this.nearbyLocations.splice(index, 1);
                },

                addCustomLocation() {
                    if (!this.customLocationName.trim() || !this.customLocationDistance.trim()) return;

                    let distanceMeters = 0;
                    const distText = this.customLocationDistance.trim().toLowerCase();
                    if (distText.endsWith('km')) {
                        distanceMeters = parseFloat(distText) * 1000;
                    } else {
                        distanceMeters = parseFloat(distText.replace('m', ''));
                    }

                    this.nearbyLocations.push({
                        name: this.customLocationName.trim(),
                        category: this.customLocationCategory,
                        distance: Math.round(distanceMeters) || 0,
                        distance_text: this.formatDistance(distanceMeters || 0),
                        lat: null, lng: null, auto: false
                    });

                    this.customLocationName = '';
                    this.customLocationCategory = 'custom';
                    this.customLocationDistance = '';
                    this.showAddCustomForm = false;
                },

                get nearbyGrouped() {
                    const groups = {};
                    this.nearbyLocations.forEach(loc => {
                        const cat = loc.category || 'custom';
                        if (!groups[cat]) groups[cat] = [];
                        groups[cat].push(loc);
                    });
                    return groups;
                },

                handleEditFileSelect(event) {
                    const files = Array.from(event.target.files);
                    this.processFiles(files);
                },

                processFiles(files) {
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    const availableSlots = this.editMaxImages - this.editImages.length;

                    if (availableSlots <= 0) {
                        Swal.fire({
                            toast: true,
                            icon: 'error',
                            title: `Maksimal hanya ${this.editMaxImages} foto yang dapat diupload.`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                        return;
                    }

                    const filesToProcess = imageFiles.slice(0, availableSlots);

                    if (imageFiles.length > availableSlots) {
                        Swal.fire({
                            toast: true,
                            icon: 'warning',
                            title: `Hanya ${availableSlots} foto yang dapat ditambahkan.`,
                            text: `Sisa slot: ${availableSlots}`,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }

                    filesToProcess.forEach(file => {
                        if (file.size <= 5 * 1024 * 1024) { // 5MB limit
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.editImages.push({
                                    file: file,
                                    url: e.target.result,
                                    name: file.name,
                                    isNew: true
                                });
                            };
                            reader.readAsDataURL(file);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: `File ${file.name} terlalu besar. Maksimal 5MB.`,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true,
                                didOpen: (toast) => {
                                    toast.addEventListener('mouseenter', Swal
                                        .stopTimer)
                                    toast.addEventListener('mouseleave', Swal
                                        .resumeTimer)
                                }
                            });
                        }

                    });

                    // Clear the file input to allow re-selection
                    if (event.target) {
                        event.target.value = '';
                    }
                },

                // Get the actual index in the combined array
                getImageIndex(type, index) {
                    if (type === 'existing') {
                        // For existing images, count only non-deleted ones up to this index
                        let actualIndex = 0;
                        for (let i = 0; i < index; i++) {
                            if (!this.propertyData.existingImages[i].markedForDeletion) {
                                actualIndex++;
                            }
                        }
                        return actualIndex;
                    } else {
                        // For new images, add after all non-deleted existing images
                        const existingCount = this.propertyData.existingImages.filter(img => !img
                            .markedForDeletion).length;
                        return existingCount + index;
                    }
                },

                // Get display index (sequential numbering for UI)
                getDisplayIndex(type, index) {
                    if (type === 'existing') {
                        // Count display position among non-deleted existing images
                        let displayIndex = 0;
                        for (let i = 0; i < index; i++) {
                            if (!this.propertyData.existingImages[i].markedForDeletion) {
                                displayIndex++;
                            }
                        }
                        return displayIndex;
                    } else {
                        // For new images, continue numbering after existing images
                        const existingCount = this.propertyData.existingImages.filter(img => !img
                            .markedForDeletion).length;
                        return existingCount + index;
                    }
                },

                // Get all images in correct order for processing
                getAllImages() {
                    const existing = this.propertyData.existingImages.filter(img => !img
                        .markedForDeletion);
                    return [...existing, ...this.editImages];
                },

                // Get current thumbnail with proper index handling
                getCurrentThumbnail() {
                    if (this.thumbnailIndex === null) {
                        // Try to find the existing thumbnail if none is selected
                        const allImages = this.getAllImages();
                        const thumbnailIndex = allImages.findIndex(img => img.is_thumbnail);
                        if (thumbnailIndex !== -1) {
                            this.thumbnailIndex = thumbnailIndex;
                            return allImages[thumbnailIndex];
                        }
                        return null;
                    }

                    const allImages = this.getAllImages();
                    return allImages[this.thumbnailIndex] || null;
                },

                // Set thumbnail with proper index validation
                setThumbnail(index) {
                    const allImages = this.getAllImages();
                    if (index >= 0 && index < allImages.length) {
                        this.thumbnailIndex = index;
                    }
                },

                // Remove existing image with proper index handling
                removeEditExistingImage(index) {
                    const currentThumbnailIndex = this.thumbnailIndex;
                    const imageGlobalIndex = this.getImageIndex('existing', index);

                    // Mark image for deletion
                    this.propertyData.existingImages[index].markedForDeletion = true;

                    // Update thumbnail index if the deleted image was the thumbnail
                    if (currentThumbnailIndex === imageGlobalIndex) {
                        this.updateThumbnailAfterDeletion();
                    }
                },

                // Remove new image with proper index handling
                removeEditImage(index) {
                    const currentThumbnailIndex = this.thumbnailIndex;
                    const imageGlobalIndex = this.getImageIndex('new', index);

                    // Remove the image
                    this.editImages.splice(index, 1);

                    // Update thumbnail index if the deleted image was the thumbnail
                    if (currentThumbnailIndex === imageGlobalIndex) {
                        this.updateThumbnailAfterDeletion();
                    }
                },

                // Update thumbnail index after image deletion
                updateThumbnailAfterDeletion() {
                    const allImages = this.getAllImages();
                    if (allImages.length === 0) {
                        this.thumbnailIndex = null;
                    } else {
                        // Set thumbnail to first available image
                        this.thumbnailIndex = 0;
                    }
                },

                nextStep() {
                    if (this.validateEditStep()) {
                        this.editStep++;
                        if (this.editStep === 2) {
                            this.$nextTick(() => {
                                this.resizeMap();
                            });
                        }
                    }
                },

                prevStep() {
                    this.editStep--;
                    if (this.editStep === 2) {
                        this.$nextTick(() => {
                            this.resizeMap();
                        });
                    }
                },

                validateEditStep() {
                    if (this.editStep === 1) {
                        if (!this.propertyData.name.trim()) {
                            alert('Nama properti harus diisi');
                            return false;
                        }
                        if (!this.propertyData.description.trim()) {
                            alert('Deskripsi properti harus diisi');
                            return false;
                        }
                    } else if (this.editStep === 2) {
                        if (!this.propertyData.address.trim()) {
                            alert('Alamat lengkap harus diisi');
                            return false;
                        }

                        if (!this.propertyData.province.trim() || !this.propertyData.city.trim() ||
                            !this.propertyData.subdistrict.trim() || !this.propertyData.village.trim()
                        ) {
                            alert('Semua detail lokasi harus diisi');
                            return false;
                        }

                        if (!this.propertyData.latitude || !this.propertyData.longitude) {
                            alert('Pinpoint lokasi wajib dipilih');
                            return false;
                        }
                    } else if (this.editStep === 4) {
                        const totalImages = this.editImages.length +
                            this.propertyData.existingImages.filter(img => !img.markedForDeletion)
                            .length;

                        if (totalImages < this.editMinImages) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'warning',
                                title: `Harap unggah minimal ${this.editMinImages} foto properti (Saat ini: ${totalImages})`,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            return false;
                        }

                    }
                    return true;
                },

                closeModal() {
                    this.editModalOpen = false;
                    this.editStep = 1;
                    this.editImages = [];
                    this.searchResults = [];

                    // Clean up map
                    if (this.map) {
                        this.map.remove();
                        this.map = null;
                        this.marker = null;
                    }
                },

                handleEditDrop(event) {
                    event.preventDefault();
                    this.isDragging = false;
                    const files = Array.from(event.dataTransfer.files);
                    this.processEditFiles(files);
                },

                handleEditDragOver(event) {
                    event.preventDefault();
                    this.isDragging = true;
                },

                handleEditDragLeave(event) {
                    event.preventDefault();
                    this.isDragging = false;
                },

                handleEditFileSelect(event) {
                    const files = Array.from(event.target.files);
                    this.processEditFiles(files);

                    // Auto-select first image as thumbnail if none selected
                    if (this.thumbnailIndex === null && files.length > 0) {
                        const existingCount = this.propertyData.existingImages.filter(img => !img
                            .markedForDeletion).length;
                        this.thumbnailIndex = existingCount; // Select first new image
                    }
                },

                processEditFiles(files) {
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    const availableSlots = this.editMaxImages - this.getAllImages().length;

                    if (availableSlots <= 0) {
                        this.showAlert('error',
                            `Maksimal hanya ${this.editMaxImages} foto yang dapat diupload.`);
                        return;
                    }

                    const filesToProcess = imageFiles.slice(0, availableSlots);

                    filesToProcess.forEach(file => {
                        if (file.size <= 5 * 1024 * 1024) { // 5MB limit
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.editImages.push({
                                    file: file,
                                    url: e.target.result,
                                    name: file.name,
                                    isNew: true
                                });
                            };
                            reader.readAsDataURL(file);
                        } else {
                            this.showAlert('error',
                                `File ${file.name} terlalu besar. Maksimal 5MB.`);
                        }
                    });

                    // Clear the file input
                    event.target.value = '';
                },

                showAlert(type, message) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: type,
                        title: message,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },

                async submitEditForm() {
                    if (!this.validateEditStep() || this.isSubmitting) return;
                    this.isSubmitting = true;

                    const submitBtn = document.querySelector(
                        `#propertyFormEdit-${property.idrec} button[type="submit"]`);
                    const originalText = submitBtn?.innerHTML;

                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        `;
                    }

                    try {
                        const formData = new FormData();

                        // Add basic property data
                        formData.append('name', this.propertyData.name);
                        formData.append('initial', this.propertyData.initial);
                        formData.append('tags', this.propertyData.tags);
                        formData.append('gender', this.propertyData.gender || '');
                        formData.append('description', this.propertyData.description);
                        formData.append('address', this.propertyData.address);
                        formData.append('latitude', this.propertyData.latitude);
                        formData.append('longitude', this.propertyData.longitude);
                        formData.append('province', this.propertyData.province);
                        formData.append('city', this.propertyData.city);
                        formData.append('subdistrict', this.propertyData.subdistrict);
                        formData.append('village', this.propertyData.village);
                        formData.append('postal_code', this.propertyData.postal_code);

                        // Add nearby locations as JSON
                        formData.append('nearby_locations', JSON.stringify(this.nearbyLocations));

                        // Handle array fields - FIXED: Only append if array is not empty
                        const generalArray = Array.isArray(this.propertyData.general) ? this
                            .propertyData.general.map(v => parseInt(v, 10)).filter(v => !isNaN(v)) :
                            [];
                        const securityArray = Array.isArray(this.propertyData.security) ? this
                            .propertyData.security.map(v => parseInt(v, 10)).filter(v => !isNaN(
                                v)) : [];
                        const amenitiesArray = Array.isArray(this.propertyData.amenities) ? this
                            .propertyData.amenities.map(v => parseInt(v, 10)).filter(v => !isNaN(
                                v)) : [];

                        // Append only if array has items
                        if (generalArray.length > 0) {
                            generalArray.forEach(item => {
                                formData.append('general[]', item);
                            });
                        }

                        if (securityArray.length > 0) {
                            securityArray.forEach(item => {
                                formData.append('security[]', item);
                            });
                        }

                        if (amenitiesArray.length > 0) {
                            amenitiesArray.forEach(item => {
                                formData.append('amenities[]', item);
                            });
                        }

                        // Append new images
                        this.editImages.forEach((image) => {
                            formData.append('property_images[]', image.file);
                        });

                        // Append existing images that are not marked for deletion
                        this.propertyData.existingImages
                            .filter(img => !img.markedForDeletion)
                            .forEach(img => {
                                formData.append('existing_images[]', img.id);
                            });

                        // Append images to delete
                        const deleteImages = this.propertyData.existingImages
                            .filter(img => img.markedForDeletion)
                            .map(img => img.id);

                        deleteImages.forEach(imgId => {
                            formData.append('delete_images[]', imgId);
                        });

                        formData.append('thumbnail_index', this.thumbnailIndex);

                        // Add CSRF token and method spoofing
                        formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                            .content);
                        formData.append('_method', 'PUT');

                        const response = await fetch(
                            document.getElementById(`propertyFormEdit-${property.idrec}`)
                            .action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                        let data;
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            data = await response.json();
                        } else {
                            const text = await response.text();
                            throw new Error('Server returned non-JSON response: ' + text.substring(
                                0, 100));
                        }

                        if (!response.ok) {
                            if (data.errors) {
                                const errorMessages = Object.values(data.errors).flat().join(', ');
                                throw new Error(errorMessages);
                            }
                            throw new Error(data.message || 'Gagal memperbarui properti');
                        }

                        // Close the modal first before showing success message
                        this.closeModal();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Properti berhasil diperbarui!',
                            showConfirmButton: false,
                            timer: 1000,
                            timerProgressBar: true,
                            didClose: () => {
                                if (typeof loadPropertiesData === 'function') {
                                    loadPropertiesData();
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: `Error: ${error.message}`,
                            showConfirmButton: false,
                            timer: 5000,
                            timerProgressBar: true,
                        });
                    } finally {
                        this.isSubmitting = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    }
                }
            }));
        });

        document.addEventListener('alpine:init', () => {
            Alpine.data('modalView', () => ({
                selectedProperty: {
                    currentImageIndex: 0,
                    images: [],
                    general: [],
                    security: [],
                    amenities: [],
                    nearby_locations: []
                },
                viewNearbyCategories: {
                    'transport': '{!! __("ui.category_transport") !!}',
                    'education': '{!! __("ui.category_education") !!}',
                    'health': '{!! __("ui.category_health") !!}',
                    'shopping': '{!! __("ui.category_shopping") !!}',
                    'worship': '{!! __("ui.category_worship") !!}',
                    'food_drink': '{!! __("ui.category_food_drink") !!}',
                    'finance': '{!! __("ui.category_finance") !!}',
                    'public_service': '{!! __("ui.category_public_service") !!}',
                    'custom': '{!! __("ui.category_custom") !!}'
                },
                viewNearbyCategoryIcons: {
                    'transport': 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4',
                    'education': 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z',
                    'health': 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                    'shopping': 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z',
                    'worship': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'food_drink': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'finance': 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                    'public_service': 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                    'custom': 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'
                },
                facilities: {
                    general: [],
                    security: [],
                    amenities: []
                },
                modalOpenDetail: false,
                isLoading: false,

                openModal(property) {
                    this.isLoading = true;
                    this.modalOpenDetail = true;
                    this.disableBodyScroll();

                    if (property.facilities) {
                        this.facilities = {
                            general: property.facilities.general || [],
                            security: property.facilities.security || [],
                            amenities: property.facilities.amenities || []
                        };
                    }

                    // Use nextTick to ensure DOM is ready before setting properties
                    this.$nextTick(() => {
                        this.selectedProperty = {
                            ...property,
                            currentImageIndex: 0,
                            images: Array.isArray(property.images) ? property.images.filter(
                                img => img) : [],
                            general: Array.isArray(property.general) ?
                                property.general : (property.general ? JSON.parse(property
                                    .general) : []),
                            security: Array.isArray(property.security) ?
                                property.security : (property.security ? JSON.parse(property
                                    .security) : []),
                            amenities: Array.isArray(property.amenities) ?
                                property.amenities : (property.amenities ? JSON.parse(
                                    property.amenities) : []),
                            nearby_locations: Array.isArray(property.nearby_locations) ?
                                property.nearby_locations : []
                        };
                        this.isLoading = false;
                        // Scan for Iconify icons in facility badges
                        setTimeout(() => { if (window.Iconify) Iconify.scan(); }, 200);
                    });
                },

                closeModal() {
                    this.modalOpenDetail = false;
                    this.enableBodyScroll();
                    // Reset for next opening
                    setTimeout(() => {
                        this.selectedProperty = {
                            currentImageIndex: 0,
                            images: [],
                            features: []
                        };
                    }, 300); // Match this with your CSS transition duration
                },

                nextImage() {
                    if (this.hasMultipleImages) {
                        this.selectedProperty.currentImageIndex =
                            (this.selectedProperty.currentImageIndex + 1) % this.selectedProperty.images
                            .length;
                    }
                },

                getFacilityName(id, category) {
                    if (!this.facilities[category]) return 'Unknown Facility';

                    const facility = this.facilities[category].find(f => f.idrec == id);
                    return facility ? facility.facility : 'Unknown Facility';
                },

                getFacilityIcon(id, category) {
                    if (!this.facilities[category]) return '';
                    const facility = this.facilities[category].find(f => f.idrec == id);
                    return facility && facility.icon ? facility.icon : '';
                },

                prevImage() {
                    if (this.hasMultipleImages) {
                        this.selectedProperty.currentImageIndex =
                            (this.selectedProperty.currentImageIndex - 1 + this.selectedProperty.images
                                .length) %
                            this.selectedProperty.images.length;
                    }
                },

                goToImage(index) {
                    if (this.hasMultipleImages && index >= 0 && index < this.selectedProperty.images
                        .length) {
                        this.selectedProperty.currentImageIndex = index;
                    }
                },

                // Getters for computed properties
                get hasMultipleImages() {
                    return this.selectedProperty.images?.length > 1;
                },

                get currentImage() {
                    return this.selectedProperty.images[this.selectedProperty.currentImageIndex];
                },

                // Touch event handlers for mobile swipe
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
                        this.nextImage(); // Swipe left
                    } else if (diff < -threshold) {
                        this.prevImage(); // Swipe right
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

                get viewNearbyGrouped() {
                    const groups = {};
                    const locations = this.selectedProperty.nearby_locations || [];
                    locations.forEach(loc => {
                        const cat = loc.category || 'custom';
                        if (!groups[cat]) groups[cat] = [];
                        groups[cat].push(loc);
                    });
                    return groups;
                },

                init() {
                    // Keyboard event listener
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

                    // Cleanup event listener when component is removed
                    this.$el.addEventListener('alpine:initialized', () => {
                        this.$el.addEventListener('alpine:destroying', () => {
                            document.removeEventListener('keydown', handleKeyDown);
                        });
                    });
                }
            }));
        });
    </script>
</x-app-layout>
