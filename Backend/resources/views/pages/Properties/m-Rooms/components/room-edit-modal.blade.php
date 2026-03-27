<!-- Edit Room Modal -->
<div x-data="modalRoomEdit()">
    <!-- Trigger Button -->
    <button class="p-2 flex items-center justify-center text-amber-600 hover:text-amber-900 transition-colors duration-200 rounded-full hover:bg-amber-50"
        type="button"
        @click.prevent="openModal(@js($room->load(['property', 'roomImages'])))"
        aria-controls="room-edit-modal" title="Edit Room">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
        </svg>
    </button>

    <!-- Modal -->
    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity" x-show="editModalOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak></div>

    <div class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
        role="dialog" aria-modal="true" x-show="editModalOpen"
        x-transition:enter="transition ease-in-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in-out duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

        <div class="bg-white rounded-2xl shadow-lg overflow-auto w-3/4 max-h-full flex flex-col text-left"
            @click.outside="editModalOpen = true" @keydown.escape.window="editModalOpen = false">

            <!-- Modal header with step indicator — room-edit-modal-header class for dark mode override in app.css -->
            <div class="room-edit-modal-header px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex justify-between items-center mb-4">
                    <div class="font-bold text-xl text-gray-800">{{ __('ui.room_edit_title') }}</div>
                    <button type="button"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        @click="editModalOpen = false">
                        <div class="sr-only">Close</div>
                        <svg class="w-6 h-6 fill-current">
                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                        </svg>
                    </button>
                </div>

                <!-- Step Indicator -->
                <div class="flex items-center justify-center space-x-4">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                            :class="editStep > 1 ? 'bg-blue-600 border-blue-600 text-white' : editStep === 1 ?
                                'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                            <span class="text-sm font-semibold" x-show="editStep === 1">1</span>
                            <span class="text-sm font-semibold" x-show="editStep < 1">1</span>
                            <svg x-show="editStep > 1" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3 text-sm">
                            <p class="font-medium transition-colors duration-300"
                                :class="editStep >= 1 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_basic_info') }}</p>
                        </div>
                    </div>

                    <!-- Connector -->
                    <div class="w-16 h-0.5 transition-colors duration-300"
                        :class="editStep >= 2 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                            :class="editStep > 2 ? 'bg-blue-600 border-blue-600 text-white' : editStep === 2 ?
                                'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                            <span class="text-sm font-semibold" x-show="editStep === 2 || editStep < 2">2</span>
                            <svg x-show="editStep > 2" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3 text-sm">
                            <p class="font-medium transition-colors duration-300"
                                :class="editStep >= 2 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_pricing') }}</p>
                        </div>
                    </div>

                    <!-- Connector -->
                    <div class="w-16 h-0.5 transition-colors duration-300"
                        :class="editStep >= 3 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                            :class="editStep > 3 ? 'bg-blue-600 border-blue-600 text-white' : editStep === 3 ?
                                'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500'">
                            <span class="text-sm font-semibold" x-show="editStep === 3 || editStep < 3">3</span>
                            <svg x-show="editStep > 3" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div class="ml-3 text-sm">
                            <p class="font-medium transition-colors duration-300"
                                :class="editStep >= 3 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_facilities') }}</p>
                        </div>
                    </div>

                    <!-- Connector -->
                    <div class="w-16 h-0.5 transition-colors duration-300"
                        :class="editStep >= 4 ? 'bg-blue-600' : 'bg-gray-300'"></div>

                    <!-- Step 4 -->
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                            :class="editStep === 4 ? 'bg-blue-600 border-blue-600 text-white' :
                                'border-gray-300 text-gray-500'">
                            <span class="text-sm font-semibold">4</span>
                        </div>
                        <div class="ml-3 text-sm">
                            <p class="font-medium transition-colors duration-300"
                                :class="editStep >= 4 ? 'text-blue-600' : 'text-gray-500'">{{ __('ui.room_step_photos') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal content -->
            <div class="flex-1 overflow-y-auto px-6 py-6">
                <form x-ref="editForm" method="POST" :action="editFormAction"
                    enctype="multipart/form-data" @submit.prevent="submitEditForm">
                    @csrf
                    @method('PUT')

                    <!-- Step 1 - Basic Information -->
                    <div x-show="editStep === 1" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-6">
                            <!-- Property Selector -->
                            <div>
                                <label for="edit_property_id"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('ui.room_property_label') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="edit_property_id" name="property_name"
                                    :value="roomData.property_name" readonly
                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 bg-gray-100 text-gray-700">
                                <input type="hidden" name="property_id" :value="roomData.property_id">
                            </div>

                            <div class="grid grid-cols-4 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="edit_room_no"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('ui.room_number_label') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="edit_room_no" name="room_no" required
                                        class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="{{ __('ui.room_number_placeholder') }}" x-model="roomData.room_no">
                                </div>

                                <div>
                                    <label for="edit_room_name"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('ui.room_name_type_label') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit_room_name" name="room_name" required
                                        class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        x-model="roomData.room_name">
                                        <option value="">{{ __('ui.room_select_type') }}</option>
                                        <option value="Standar">Standar</option>
                                        <option value="Superior">Superior</option>
                                        <option value="Deluxe">Deluxe</option>
                                        <option value="Suite">Suite</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_room_bed"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('ui.room_bed_type_label') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="edit_room_bed" name="room_bed" required
                                        class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        x-model="roomData.room_bed">
                                        <option value="">{{ __('ui.room_select_bed_type') }}</option>
                                        <option value="Single">Single</option>
                                        <option value="Twin">Twin</option>
                                        <option value="Double">Double</option>
                                        <option value="Queen">Queen</option>
                                        <option value="King">King</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="edit_room_capacity"
                                        class="block text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('ui.room_capacity_label') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="edit_room_capacity" name="room_capacity"
                                        required
                                        class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="{{ __('ui.room_capacity_placeholder') }}" x-model="roomData.room_capacity">
                                </div>
                            </div>

                            <div>
                                <label for="edit_description"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('ui.room_description_label') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea id="edit_description" name="description" rows="4" required
                                    class="w-full border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                    placeholder="{{ __('ui.room_description_placeholder') }}" x-model="roomData.description"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 - Pricing -->
                    <div x-show="editStep === 2" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                        <div class="space-y-6">
                            <div class="mb-4">
                                <h3 class="text-md font-semibold text-gray-700 mb-2">{{ __('ui.room_price_type_label') }} <span
                                        class="text-red-500">*</span></h3>
                                <p class="text-xs text-gray-500 mb-3">{{ __('ui.room_price_type_hint') }}</p>
                                <div class="flex space-x-6">
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="edit_price_type" value="daily"
                                            x-model="selectedPriceType"
                                            @change="onPriceTypeChange('daily')"
                                            class="form-radio text-blue-600 h-4 w-4"
                                            required>
                                        <span class="ml-2 text-sm font-medium text-gray-700">{{ __('ui.room_daily_label') }}</span>
                                    </label>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="radio" name="edit_price_type" value="monthly"
                                            x-model="selectedPriceType"
                                            @change="onPriceTypeChange('monthly')"
                                            class="form-radio text-blue-600 h-4 w-4"
                                            required>
                                        <span class="ml-2 text-sm font-medium text-gray-700">{{ __('ui.room_monthly_label') }}</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Daily type: hide price input, show message to use daily price management -->
                            <div x-show="selectedPriceType === 'daily'" x-transition>
                                <p class="text-sm text-blue-600 font-medium bg-blue-50 border border-blue-200 rounded-lg px-4 py-3">
                                    {{ __('ui.room_daily_use_management') }}
                                </p>
                                <!-- Hidden inputs to preserve daily price data -->
                                <input type="hidden" name="daily_price" x-model="dailyPrice">
                            </div>

                            <div x-show="selectedPriceType === 'monthly'" x-transition class="mt-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    {{ __('ui.room_monthly_price') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">Rp</span>
                                    </div>
                                    <input type="text" x-ref="monthlyPriceInput"
                                        class="w-full pl-10 border-2 border-gray-200 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                        placeholder="{{ __('ui.room_monthly_price_placeholder') }}" x-model="monthlyPriceFormatted"
                                        @input="updateMonthlyPrice($event.target.value)">
                                    <!-- Hidden input untuk backend -->
                                    <input type="hidden" name="monthly_price" x-model="monthlyPrice">
                                </div>
                                <p x-show="monthlyPriceError" class="text-red-500 text-xs mt-1"
                                    x-text="monthlyPriceError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 - Facilities -->
                    <div x-show="editStep === 3" x-transition:enter="transition ease-out duration-300"
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
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2">
                                    @foreach ($facilities as $facility)
                                        <div class="relative">
                                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors duration-200"
                                                :class="selectedFacilities.includes('{{ $facility->idrec }}') ? 'border-blue-500 bg-blue-50' : ''">
                                                <input type="checkbox" name="general_facilities[]"
                                                    value="{{ $facility->idrec }}"
                                                    id="edit_facility_{{ $facility->idrec }}"
                                                    class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                    x-model="selectedFacilities">
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

                                @if ($facilities->isEmpty())
                                    <div class="text-center py-8">
                                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">{{ __('ui.room_no_facilities') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Step 4 - Photos -->
                    <div x-show="editStep === 4" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    {{ __('ui.room_photos_label') }} <span class="text-red-500">*</span>
                                    <span class="text-sm font-normal text-gray-500">
                                        (Minimal 3 foto, maksimal 5 foto - <span x-text="editRemainingSlots"></span> slot tersisa)
                                    </span>
                                </label>

                                <!-- Thumbnail Selection Area -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                        {{ __('ui.room_select_thumbnail') }} <span class="text-red-500">*</span>
                                        <span class="text-xs font-normal text-gray-500">{{ __('ui.room_thumbnail_hint') }}</span>
                                    </h4>

                                    <div class="flex items-center space-x-4">
                                        <!-- Thumbnail Preview -->
                                        <div class="w-32 h-32 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 overflow-hidden relative"
                                            x-show="editAllImages.length > 0">
                                            <template x-if="editThumbnailIndex !== null && editAllImages[editThumbnailIndex]">
                                                <img :src="editAllImages[editThumbnailIndex].url"
                                                    alt="Selected Thumbnail"
                                                    class="w-full h-full object-cover">
                                            </template>
                                            <div class="absolute inset-0 flex items-center justify-center text-gray-400"
                                                x-show="editThumbnailIndex === null">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Thumbnail Selection Instructions -->
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600 mb-2">
                                                <span x-show="editThumbnailIndex === null" class="font-medium text-red-500">{{ __('ui.room_no_thumbnail') }}</span>
                                                <span x-show="editThumbnailIndex !== null" class="font-medium text-green-600">{{ __('ui.room_thumbnail_selected') }}</span>
                                                {{ __('ui.room_click_photo_thumbnail') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Pastikan memilih foto terbaik sebagai thumbnail karena ini akan menjadi gambar utama kamar Anda.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Area -->
                                <div x-show="editCanUploadMore" @drop="handleEditDrop($event)" @dragover.prevent @dragenter.prevent
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                    :class="{ 'border-blue-400 bg-blue-50': editCanUploadMore }">
                                    <div class="space-y-2">
                                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="edit_room_images"
                                                class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>{{ __('ui.room_upload_new_photos') }}</span>
                                                <input id="edit_room_images" name="room_images[]" type="file" multiple accept="image/*"
                                                    @change="handleEditFileSelect($event)" class="sr-only">
                                            </label>
                                            <p class="pl-1">atau drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG up to 5MB</p>
                                        <p class="text-xs text-blue-600" x-text="`Dapat upload ${editRemainingSlots} foto lagi`"></p>
                                    </div>
                                </div>

                                <!-- Full Upload Message -->
                                <div x-show="!editCanUploadMore" class="border-2 border-green-300 rounded-lg p-8 text-center bg-green-50">
                                    <div class="space-y-2">
                                        <svg class="w-12 h-12 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <p class="text-sm text-green-600 font-medium">5 foto telah diupload!</p>
                                        <p class="text-xs text-green-500">Maksimal foto telah tercapai</p>
                                    </div>
                                </div>

                                <!-- Image Preview Grid -->
                                <div x-show="editAllImages.length > 0" class="mt-4">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ __('ui.room_photos_label') }}</h4>
                                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100">
                                        <template x-for="(image, index) in editAllImages" :key="image.id || index">
                                            <div class="relative group" @click="setEditThumbnail(index)">
                                                <!-- Image Container -->
                                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                                    :class="editThumbnailIndex === index ?
                                                        'border-blue-600 ring-2 ring-blue-400' :
                                                        'border-gray-200 hover:border-blue-400'">
                                                    <img :src="image.url" :alt="`Preview ${index + 1}`"
                                                        class="w-full h-full object-cover">

                                                    <!-- Thumbnail badge -->
                                                    <div x-show="editThumbnailIndex === index"
                                                        class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                        Thumbnail
                                                    </div>

                                                    <!-- Existing image badge -->
                                                    <div x-show="image.isExisting"
                                                        class="absolute top-1 left-1 bg-gray-600 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                        Existing
                                                    </div>
                                                </div>

                                                <!-- Remove Button -->
                                                <button type="button" @click.stop="removeEditImage(index, $event)"
                                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>

                                                <!-- Image Number Badge -->
                                                <div class="absolute bottom-1 left-1 bg-gray-800 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                    <span x-text="index + 1"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Progress Indicator -->
                                <div class="mt-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span>{{ __('ui.room_total_photos') }}</span>
                                        <span x-text="`${editAllImages.length}/${editMaxImages} foto`"></span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                            :style="`width: ${(editAllImages.length / editMaxImages) * 100}%`">
                                        </div>
                                    </div>
                                </div>

                                <!-- Validation Messages -->
                                <div class="mt-3 space-y-2">
                                    <p class="text-sm text-red-600" x-show="editAllImages.length < 3">
                                        <span class="font-medium">{{ __('ui.room_attention') }}</span>
                                        {{ __('ui.room_must_upload_3') }}
                                    </p>

                                    <p class="text-sm text-red-600" x-show="editAllImages.length >= 3 && editThumbnailIndex === null">
                                        <span class="font-medium">{{ __('ui.room_attention') }}</span>
                                        {{ __('ui.room_must_select_thumbnail') }}
                                    </p>

                                    <p class="text-sm text-green-600" x-show="editAllImages.length >= 3 && editThumbnailIndex !== null">
                                        <span class="font-medium">{{ __('ui.room_perfect') }}</span>
                                        {{ __('ui.room_photos_complete') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" x-show="editStep > 1" @click="editStep--"
                            class="px-6 py-2 border-2 border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Sebelumnya
                        </button>
                        <button type="button" x-show="editStep < 4"
                            @click="validateEditStep(editStep) && editStep++"
                            class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            Selanjutnya
                            <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <button type="submit" x-show="editStep === 4" x-ref="submitBtn"
                            class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>