<!-- View -->
<div x-data="modalViewRoom()" class="relative group">
    @php
        $roomImages = [];
        foreach ($room->roomImages as $image) {
            if (!empty($image->image)) {
                // Ubah dari base64 ke path storage
                $roomImages[] = asset('storage/' . $image->image);
            }
        }
        $facilities = json_encode($room->facility, JSON_HEX_APOS | JSON_HEX_QUOT);
    @endphp
    <button class="p-2 text-blue-500 hover:text-blue-700 transition-colors duration-200 rounded-full hover:bg-blue-50"
        type="button"
        @click.prevent='openModal({
                            name: @json($room->name),
                            number: @json($room->no),
                            bed: @json($room->bed_type),
                            capacity: @json($room->capacity),
                            description: @json($room->descriptions),
                            created_at: "{{ \Carbon\Carbon::parse($room->created_at)->format('Y-m-d H:i') }}",
                            updated_at: "{{ $room->updated_at ? \Carbon\Carbon::parse($room->updated_at)->format('Y-m-d H:i') : '-' }}",
                            creator: "{{ $room->creator->username ?? 'Unknown' }}",
                            status: "{{ $room->status ? 'Active' : 'Inactive' }}",
                            images: {!! json_encode($roomImages) !!},
                            daily_price: "{{ number_format($room->price_original_daily, 0, ',', '.') }}",
                            monthly_price: "{{ number_format($room->price_original_monthly, 0, ',', '.') }}",
                            facilities: {!! $facilities !!},
                            property_name: @json($room->property_name ?? 'Unknown Property')
                        })'
        aria-controls="room-detail-modal" title="View Details">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
        </svg>
    </button>
    <!-- Modal backdrop -->
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity" x-show="modalOpenDetail"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak>
    </div>

    <!-- Modal dialog -->
    <div class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
        role="dialog" aria-modal="true" x-show="modalOpenDetail"
        x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in-out duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" x-cloak>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[95vh] flex flex-col"
            @click.outside="modalOpenDetail = false" @keydown.escape.window="modalOpenDetail = false">

            <!-- Modal header -->
            <div
                class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="text-left">
                    <h3 class="text-2xl font-bold text-gray-900 mb-1" x-text="selectedRoom.name"></h3>
                    <p class="text-gray-600">
                        <span x-text="'Room No: ' + selectedRoom.number"></span>
                        •
                        <span x-text="selectedRoom.property_name"></span>
                    </p>
                </div>
                <button type="button"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200 p-2 hover:bg-white rounded-full"
                    @click="modalOpenDetail = false">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal content -->
            <div class="overflow-y-auto flex-1">
                <!-- Room image slider -->
                <div class="relative h-72 overflow-hidden bg-gray-200">
                    <!-- Images -->
                    <div class="flex h-full transition-transform duration-300 ease-in-out"
                        :style="'transform: translateX(-' + (selectedRoom
                            .currentImageIndex * 100) + '%)'">
                        <template x-for="(image, index) in selectedRoom.images" :key="index">
                            <img :src="image" alt="Room Image"
                                class="w-full h-full object-cover object-center flex-shrink-0">
                        </template>
                    </div>

                    <!-- Navigation arrows -->
                    <button x-show="selectedRoom.images.length > 1"
                        @click="selectedRoom.currentImageIndex = (selectedRoom.currentImageIndex - 1 + selectedRoom.images.length) % selectedRoom.images.length"
                        class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-md">
                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button x-show="selectedRoom.images.length > 1"
                        @click="selectedRoom.currentImageIndex = (selectedRoom.currentImageIndex + 1) % selectedRoom.images.length"
                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white rounded-full p-2 shadow-md">
                        <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Status badge -->
                    <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg">
                        <span
                            :class="selectedRoom && selectedRoom
                                .status === 'Active' ?
                                'text-green-600 font-semibold' :
                                'text-red-600 font-semibold'"
                            class="text-sm flex items-center">
                            <span class="w-2.5 h-2.5 rounded-full mr-2 block"
                                :class="selectedRoom && selectedRoom
                                    .status === 'Active' ?
                                    'bg-green-500' : 'bg-red-500'"></span>
                            <span x-text="selectedRoom && selectedRoom.status ? selectedRoom.status : ''"></span>
                        </span>
                    </div>

                    <!-- Image indicators -->
                    <div x-show="selectedRoom.images.length > 1"
                        class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                        <template x-for="(image, index) in selectedRoom.images" :key="index">
                            <button @click="selectedRoom.currentImageIndex = index"
                                class="w-3 h-3 rounded-full transition-all"
                                :class="selectedRoom.currentImageIndex ===
                                    index ? 'bg-white w-6' :
                                    'bg-white/50'"></button>
                        </template>
                    </div>
                </div>

                <div class="p-6 space-y-8">
                    <!-- Room Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 mb-3 text-left">
                                    {{ __('ui.room_specifications') }}</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        <span class="text-gray-700">
                                            <span class="font-medium">{{ __('ui.room_detail_property') }}</span>
                                            <span x-text="selectedRoom.property_name"></span>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        <span class="text-gray-700">
                                            <span class="font-medium">{{ __('ui.room_detail_number') }}</span>
                                            <span x-text="selectedRoom.number"></span>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                        </svg>
                                        <span class="text-gray-700">
                                            <span class="font-medium">{{ __('ui.room_detail_bed') }}</span>
                                            <span x-text="selectedRoom.bed"></span>
                                        </span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span class="text-gray-700">
                                            <span class="font-medium">{{ __('ui.room_detail_capacity') }}</span>
                                            <span x-text="selectedRoom.capacity + ' {{ __('ui.room_detail_persons') }}'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div>
                                <h4 class="text-lg font-bold text-gray-900 mb-3 text-left">
                                    Pricing</h4>
                                <div class="space-y-3">
                                    <template x-if="selectedRoom.daily_price && selectedRoom.daily_price !== '0'">
                                        <div class="flex items-center justify-between bg-blue-50 p-3 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-gray-700 font-medium">{{ __('ui.room_detail_daily_rate') }}</span>
                                            </div>
                                            <span class="text-blue-600 font-bold"
                                                x-text="'Rp ' + selectedRoom.daily_price"></span>
                                        </div>
                                    </template>
                                    <template x-if="selectedRoom.monthly_price && selectedRoom.monthly_price !== '0'">
                                        <div class="flex items-center justify-between bg-green-50 p-3 rounded-lg">
                                            <div class="flex items-center">
                                                <svg class="w-5 h-5 text-green-600 mr-3" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-gray-700 font-medium">{{ __('ui.room_detail_monthly_rate') }}</span>
                                            </div>
                                            <span class="text-green-600 font-bold"
                                                x-text="'Rp ' + selectedRoom.monthly_price"></span>
                                        </div>
                                    </template>
                                    <template
                                        x-if="!selectedRoom.daily_price && !selectedRoom.monthly_price || (selectedRoom.daily_price === '0' && selectedRoom.monthly_price === '0')">
                                        <div class="text-center py-3 text-gray-500">
                                            {{ __('ui.room_detail_no_pricing') }}
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-900 mb-3">
                                Description</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="text-gray-700 leading-relaxed prose max-w-none"
                                    x-html="selectedRoom.description">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Facilities room Section -->
                    <div x-show="selectedRoom.facilities && selectedRoom.facilities.length > 0" class="space-y-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h4 class="text-lg font-bold text-gray-900">{{ __('ui.room_detail_facilities') }}
                            </h4>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <template x-for="facilityId in selectedRoom.facilities" :key="facilityId">
                                <div
                                    class="flex items-center space-x-3 bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <template x-if="getFacilityIcon(facilityId)">
                                        <span class="iconify text-xl text-blue-600" :data-icon="getFacilityIcon(facilityId)"></span>
                                    </template>
                                    <template x-if="!getFacilityIcon(facilityId)">
                                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </template>
                                    <span x-text="getFacilityName(facilityId, 'room')"
                                        class="text-gray-800 font-medium text-sm"></span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Meta Information -->
                    <div class="grid grid-cols-3 grid-rows-1 gap-4 bg-gray-50 p-6 rounded-xl">
                        <!-- Added By -->
                        <div class="flex flex-col items-center justify-center text-center space-y-2">
                            <div
                                class="flex items-center space-x-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <p>{{ __('ui.room_detail_added_by') }}</p>
                            </div>
                            <p class="text-gray-800 font-medium" x-text="selectedRoom.creator"></p>
                        </div>

                        <!-- Added -->
                        <div class="flex flex-col items-center justify-center text-center space-y-2">
                            <div
                                class="flex items-center space-x-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p>{{ __('ui.room_detail_added') }}</p>
                            </div>
                            <p class="text-gray-800 font-medium" x-text="selectedRoom.created_at"></p>
                        </div>

                        <!-- Last Updated -->
                        <div class="flex flex-col items-center justify-center text-center space-y-2">
                            <div
                                class="flex items-center space-x-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <p>{{ __('ui.room_detail_last_updated') }}</p>
                            </div>
                            <p class="text-gray-800 font-medium" x-text="selectedRoom.updated_at"></p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Modal footer -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <span>{{ __('ui.room_detail_press_esc') }}</span>
                </div>
                <div class="flex space-x-3">
                    <button @click="modalOpenDetail = false"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-all duration-200 font-medium hover:shadow-md">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
