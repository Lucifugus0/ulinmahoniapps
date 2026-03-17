<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.name') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.province') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.addition_date') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.change_date') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.added_by') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="propertyTableBody">
        @forelse ($properties as $property)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if ($property->thumbnail)
                                <img src="{{ Storage::url($property->thumbnail->image) }}" alt="Property Image"
                                    class="w-full h-full object-cover rounded"
                                    onerror="this.src='{{ asset('images/picture.png') }}'" />
                            @elseif ($property->images->isNotEmpty())
                                <img src="{{ Storage::url($property->images->first()->image) }}" alt="Property Image"
                                    class="w-full h-full object-cover rounded"
                                    onerror="this.src='{{ asset('images/picture.png') }}'" />
                            @else
                                <img src="{{ asset('images/picture.png') }}" alt="Default Property Image"
                                    class="w-full h-full object-cover rounded" />
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $property->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-left">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $property->province }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $property->city }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-left">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::parse($property->created_at)->format('Y M d') }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($property->created_at)->format('H:i') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-left">
                    @if ($property->updated_at)
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($property->updated_at)->format('Y M d') }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">
                            {{ \Carbon\Carbon::parse($property->updated_at)->format('H:i') }}</div>
                    @else
                        <div class="text-gray-500 dark:text-gray-400">-</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $property->creator->username ?? 'Unknown' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <div class="flex items-center justify-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer property-status-toggle"
                                data-id="{{ $property->idrec }}" {{ $property->status ? 'checked' : '' }}
                                onchange="togglePropertyStatus(this)">
                            <div
                                class="w-11 h-6 bg-gray-300 dark:bg-gray-600 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300">
                            </div>
                            <div
                                class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5">
                            </div>
                        </label>
                        <span
                            class="text-sm font-medium status-label {{ $property->status ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $property->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <div class="flex justify-center items-left space-x-3">
                        <!-- View -->
                        <div x-data="modalView()" class="relative group">
                            @php
                                $images = [];

                                foreach ($property->images as $image) {
                                    if (!empty($image->image)) {
                                        $images[] = asset('storage/' . $image->image);
                                    }
                                }

                                $features = [
                                    'general' => $facilities->get('general', []),
                                    'security' => $facilities->get('security', []),
                                    'amenities' => $facilities->get('amenities', []),
                                ];
                            @endphp
                            <button
                                class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200"
                                type="button"
                                @click.prevent='openModal({
                                                name: @json($property->name),
                                                city: @json($property->city),
                                                province: @json($property->province),
                                                description: @json($property->description),
                                                created_at: "{{ \Carbon\Carbon::parse($property->created_at)->format('Y-m-d H:i') }}",
                                                updated_at: "{{ $property->updated_at ? \Carbon\Carbon::parse($property->updated_at)->format('Y-m-d H:i') : '-' }}",
                                                creator: "{{ $property->creator->username ?? 'Unknown' }}",
                                                status: "{{ $property->status ? 'Active' : 'Inactive' }}",
                                                images: {!! json_encode($images) !!},
                                                location: @json($property->location),
                                                distance: @json($property->distance),
                                                general: @json($property->general),
                                                security: @json($property->security),
                                                amenities: @json($property->amenities),
                                                nearby_locations: @json($property->nearby_locations ?? []),
                                                facilities: {!! json_encode($features) !!},
                                                gender: @json($property->gender),
                                                tags: @json($property->tags)
                                            })'
                                aria-controls="property-detail-modal" title="View Details">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <!-- Modal backdrop -->
                            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                x-show="modalOpenDetail" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-out duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                aria-hidden="true" x-cloak>
                            </div>

                            <!-- Modal dialog -->
                            <div id="property-detail-modal"
                                class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
                                role="dialog" aria-modal="true" x-show="modalOpenDetail"
                                x-transition:enter="transition ease-in-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in-out duration-200"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95" x-cloak>

                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[95vh] flex flex-col"
                                    @click.outside="modalOpenDetail = false"
                                    @keydown.escape.window="modalOpenDetail = false">

                                    <!-- Modal header -->
                                    <div
                                        class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="flex-shrink-0 h-14 w-14 rounded-full bg-indigo-500 flex items-center justify-center shadow-lg">
                                                <span class="text-white font-bold text-xl"
                                                    x-text="selectedProperty.name ? selectedProperty.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '?'"></span>
                                            </div>
                                            <div class="text-left">
                                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1"
                                                    x-text="selectedProperty.name"></h3>
                                                <p class="text-gray-600 dark:text-gray-300 flex items-center">
                                                    <svg class="w-4 h-4 mr-1 text-gray-400 dark:text-gray-500"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span
                                                        x-text="selectedProperty.city + ', ' + selectedProperty.province"></span>
                                                </p>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 p-2 hover:bg-white dark:hover:bg-gray-700 rounded-full"
                                            @click="modalOpenDetail = false">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal content -->
                                    <div class="overflow-y-auto flex-1">
                                        <!-- Property image slider -->
                                        <div class="relative h-72 overflow-hidden bg-gray-200 dark:bg-gray-700">
                                            <!-- Images -->
                                            <div class="flex h-full transition-transform duration-300 ease-in-out"
                                                :style="'transform: translateX(-' + (selectedProperty
                                                    .currentImageIndex * 100) + '%)'">
                                                <template x-for="(image, index) in selectedProperty.images"
                                                    :key="index">
                                                    <img :src="image" alt="Property Image"
                                                        class="w-full h-full object-cover object-center flex-shrink-0">
                                                </template>
                                            </div>

                                            <!-- Navigation arrows -->
                                            <button x-show="selectedProperty.images.length > 1"
                                                @click="selectedProperty.currentImageIndex = (selectedProperty.currentImageIndex - 1 + selectedProperty.images.length) % selectedProperty.images.length"
                                                class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 rounded-full p-2 shadow-md">
                                                <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button x-show="selectedProperty.images.length > 1"
                                                @click="selectedProperty.currentImageIndex = (selectedProperty.currentImageIndex + 1) % selectedProperty.images.length"
                                                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 rounded-full p-2 shadow-md">
                                                <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>

                                            <!-- Status badge -->
                                            <div
                                                class="absolute top-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg">
                                                <span
                                                    :class="selectedProperty && selectedProperty
                                                        .status === 'Active' ?
                                                        'text-green-600 dark:text-green-400 font-semibold' :
                                                        'text-red-600 dark:text-red-400 font-semibold'"
                                                    class="text-sm flex items-center">
                                                    <span class="w-2.5 h-2.5 rounded-full mr-2 block"
                                                        :class="selectedProperty && selectedProperty
                                                            .status === 'Active' ? 'bg-green-500' :
                                                            'bg-red-500'"></span>
                                                    <span
                                                        x-text="selectedProperty && selectedProperty.status ? selectedProperty.status : ''"></span>
                                                </span>
                                            </div>

                                            <!-- Image indicators -->
                                            <div x-show="selectedProperty.images.length > 1"
                                                class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
                                                <template x-for="(image, index) in selectedProperty.images"
                                                    :key="index">
                                                    <button @click="selectedProperty.currentImageIndex = index"
                                                        class="w-3 h-3 rounded-full transition-all"
                                                        :class="selectedProperty.currentImageIndex === index ?
                                                            'bg-white dark:bg-gray-300 w-6' :
                                                            'bg-white/50 dark:bg-gray-500/50'"></button>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="p-6 space-y-8">
                                            <!-- Description -->
                                            <div class="text-center">
                                                <p class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed whitespace-pre-line"
                                                    x-text="selectedProperty.description"></p>
                                            </div>

                                            <!-- Main Content Area - Side by Side -->
                                            <div class="flex flex-col lg:flex-row gap-8">
                                                <!-- Left Column - Property Info Grid -->
                                                <div class="lg:w-1/3">
                                                    <div
                                                        class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl space-y-6 text-center">

                                                        <div class="flex flex-col items-center space-y-1">
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <svg class="w-5 h-5 text-indigo-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                                <p
                                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Added By</p>
                                                            </div>
                                                            <p class="text-gray-800 dark:text-white font-medium"
                                                                x-text="selectedProperty.creator"></p>
                                                        </div>

                                                        <!-- Gender (only for Kos) -->
                                                        <div x-show="selectedProperty.tags === 'Kos'" class="flex flex-col items-center space-y-1">
                                                            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                {{ __('ui.gender_tenant') }}
                                                            </p>
                                                            <div x-show="selectedProperty.gender === 'male'" class="flex items-center gap-1.5">
                                                                <span class="text-2xl font-bold text-green-500">♂</span>
                                                                <span class="text-gray-800 dark:text-white font-medium">{{ __('ui.gender_male') }}</span>
                                                            </div>
                                                            <div x-show="selectedProperty.gender === 'female'" class="flex items-center gap-1.5">
                                                                <span class="text-2xl font-bold text-pink-500">♀</span>
                                                                <span class="text-gray-800 dark:text-white font-medium">{{ __('ui.gender_female') }}</span>
                                                            </div>
                                                            <div x-show="selectedProperty.gender === 'mixed'" class="flex items-center gap-0.5">
                                                                <span class="text-xl font-bold text-green-500">♂</span>
                                                                <span class="text-xl font-bold text-pink-500">♀</span>
                                                                <span class="text-gray-800 dark:text-white font-medium ml-1">{{ __('ui.gender_mixed') }}</span>
                                                            </div>
                                                            <div x-show="!selectedProperty.gender" class="flex items-center gap-1.5">
                                                                <span class="text-gray-400 dark:text-gray-500 text-sm italic">-</span>
                                                            </div>
                                                        </div>

                                                        <div class="flex flex-col items-center space-y-1">
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <svg class="w-5 h-5 text-red-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                </svg>
                                                                <p
                                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Location</p>
                                                            </div>
                                                            <a class="text-gray-800 dark:text-white font-medium underline hover:text-blue-600 dark:hover:text-blue-400"
                                                                :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(selectedProperty.location ? selectedProperty.location : selectedProperty.city + ', ' + selectedProperty.province)}`"
                                                                target="_blank">
                                                                Click here for Maps
                                                            </a>
                                                        </div>

                                                        <div class="flex flex-col items-center space-y-1">
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <svg class="w-5 h-5 text-blue-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <p
                                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Added</p>
                                                            </div>
                                                            <p class="text-gray-800 dark:text-white font-medium"
                                                                x-text="selectedProperty.created_at"></p>
                                                        </div>

                                                        <div class="flex flex-col items-center space-y-1">
                                                            <div class="flex items-center justify-center space-x-2">
                                                                <svg class="w-5 h-5 text-green-500" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                </svg>
                                                                <p
                                                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                                    Last Updated</p>
                                                            </div>
                                                            <p class="text-gray-800 dark:text-white font-medium"
                                                                x-text="selectedProperty.updated_at"></p>
                                                        </div>

                                                    </div>
                                                </div>

                                                <!-- Right Column - Features Section -->
                                                <div class="lg:w-2/3">
                                                    <div x-show="selectedProperty.general.length > 0 || selectedProperty.security.length > 0 || selectedProperty.amenities.length > 0"
                                                        class="space-y-8 text-right">

                                                        <!-- General Facilities -->
                                                        <div x-show="selectedProperty.general.length > 0"
                                                            class="space-y-4">
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <h4
                                                                    class="text-lg font-bold text-gray-900 dark:text-white">
                                                                    General
                                                                    Facilities</h4>
                                                            </div>
                                                            <div class="flex flex-wrap gap-2 justify-end">
                                                                <template
                                                                    x-for="facilityId in selectedProperty.general"
                                                                    :key="facilityId">
                                                                    <span
                                                                        class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                                                        <span x-show="getFacilityIcon(facilityId, 'general')" class="iconify text-sm" :data-icon="getFacilityIcon(facilityId, 'general')"></span>
                                                                        <span x-text="getFacilityName(facilityId, 'general')"></span>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Security Facilities -->
                                                        <div x-show="selectedProperty.security.length > 0"
                                                            class="space-y-4">
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <h4
                                                                    class="text-lg font-bold text-gray-900 dark:text-white">
                                                                    Security
                                                                    Facilities</h4>
                                                            </div>
                                                            <div class="flex flex-wrap gap-2 justify-end">
                                                                <template
                                                                    x-for="facilityId in selectedProperty.security"
                                                                    :key="facilityId">
                                                                    <span
                                                                        class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800">
                                                                        <span x-show="getFacilityIcon(facilityId, 'security')" class="iconify text-sm" :data-icon="getFacilityIcon(facilityId, 'security')"></span>
                                                                        <span x-text="getFacilityName(facilityId, 'security')"></span>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Amenities Facilities -->
                                                        <div x-show="selectedProperty.amenities.length > 0"
                                                            class="space-y-4">
                                                            <div class="flex items-center justify-end space-x-2">
                                                                <h4
                                                                    class="text-lg font-bold text-gray-900 dark:text-white">
                                                                    Amenities
                                                                </h4>
                                                            </div>
                                                            <div class="flex flex-wrap gap-2 justify-end">
                                                                <template
                                                                    x-for="facilityId in selectedProperty.amenities"
                                                                    :key="facilityId">
                                                                    <span
                                                                        class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                                                        <span x-show="getFacilityIcon(facilityId, 'amenities')" class="iconify text-sm" :data-icon="getFacilityIcon(facilityId, 'amenities')"></span>
                                                                        <span x-text="getFacilityName(facilityId, 'amenities')"></span>
                                                                    </span>
                                                                </template>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>

                                            </div>

                                            <!-- Nearby Locations -->
                                            <div x-show="selectedProperty.nearby_locations && selectedProperty.nearby_locations.length > 0">
                                                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ __('ui.nearby_locations') }}
                                                </h4>
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <template x-for="(locations, catKey) in viewNearbyGrouped" :key="catKey">
                                                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden">
                                                            <div class="bg-gray-50 dark:bg-gray-700 px-3 py-2 flex items-center">
                                                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="viewNearbyCategoryIcons[catKey] || viewNearbyCategoryIcons['custom']"></path>
                                                                </svg>
                                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300" x-text="viewNearbyCategories[catKey] || catKey"></span>
                                                            </div>
                                                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                                                                <template x-for="(loc, locIdx) in locations" :key="locIdx">
                                                                    <div class="px-3 py-2 flex items-center justify-between">
                                                                        <span class="text-sm text-gray-800 dark:text-gray-200 truncate" x-text="loc.name"></span>
                                                                        <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-600 px-2 py-0.5 rounded-full ml-2 flex-shrink-0" x-text="loc.distance_text"></span>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal footer -->
                                    <div
                                        class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <span>Press ESC or click outside to close</span>
                                        </div>
                                        <div class="flex space-x-3">
                                            <button @click="modalOpenDetail = false"
                                                class="px-6 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-all duration-200 font-medium hover:shadow-md">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Edit -->
                        @php
                            $propertyEditData = [
                                'idrec' => $property->idrec,
                                'name' => $property->name ?? '',
                                'initial' => $property->initial ?? '',
                                'tags' => $property->tags ?? 'Kos',
                                'gender' => $property->gender ?? null,
                                'description' => $property->description ?? '',
                                'address' => $property->address ?? '',
                                'latitude' => $property->latitude,
                                'longitude' => $property->longitude,
                                'province' => $property->province ?? '',
                                'city' => $property->city ?? '',
                                'subdistrict' => $property->subdistrict ?? '',
                                'village' => $property->village ?? '',
                                'postal_code' => $property->postal_code ?? '',
                                'general' => is_array($property->general) ? $property->general : [],
                                'security' => is_array($property->security) ? $property->security : [],
                                'amenities' => is_array($property->amenities) ? $property->amenities : [],
                                'nearby_locations' => is_array($property->nearby_locations) ? $property->nearby_locations : [],
                                'existingImages' => $property->images
                                    ->map(function ($image) {
                                        return [
                                            'id' => $image->idrec,
                                            'url' => asset('storage/' . $image->image),
                                            'caption' => $image->caption,
                                            'name' => 'Image_' . $image->idrec . '.jpg',
                                            'is_thumbnail' => $image->thumbnail == 1,
                                        ];
                                    })
                                    ->toArray(),
                            ];
                        @endphp
                        <div x-data="modalPropertyEdit(@js($propertyEditData))" class="relative group">

                            <button
                                class="text-amber-600 hover:text-amber-900 dark:text-amber-500 dark:hover:text-amber-400"
                                type="button" @click.prevent="openModal()"
                                aria-controls="property-edit-modal-{{ $property->idrec }}" title="Edit Property">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>

                            <!-- Modal backdrop -->
                            <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity"
                                x-show="editModalOpen" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-out duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                aria-hidden="true" x-cloak></div>

                            <!-- Modal dialog -->
                            <div id="property-edit-modal-{{ $property->idrec }}"
                                class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                role="dialog" aria-modal="true" x-show="editModalOpen"
                                x-transition:enter="transition ease-in-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in-out duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

                                <div class="bg-white dark:bg-gray-800 rounded shadow-lg overflow-auto w-3/4 max-h-full flex flex-col text-left"
                                    @click.outside="editModalOpen = false"
                                    @keydown.escape.window="editModalOpen = false">

                                    <!-- Modal header with step indicator -->
                                    <div
                                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
                                        <div class="flex justify-between items-center mb-4">
                                            <div class="font-bold text-xl text-gray-800 dark:text-white">{{ __('ui.edit_property') }}</div>
                                            <button type="button"
                                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200"
                                                @click="editModalOpen = false">
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
                                                    :class="editStep >= 1 ?
                                                        'bg-blue-600 border-blue-600 text-white' :
                                                        'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                                    <span class="text-sm font-semibold" x-show="editStep < 1">1</span>
                                                    <svg x-show="editStep >= 1" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <p class="font-medium transition-colors duration-300"
                                                        :class="editStep >= 1 ? 'text-blue-600 dark:text-blue-400' :
                                                            'text-gray-500 dark:text-gray-400'">
                                                        {{ __('ui.basic_information') }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Connector -->
                                            <div class="w-16 h-0.5 transition-colors duration-300"
                                                :class="editStep >= 2 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                                            </div>

                                            <!-- Step 2 -->
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                                    :class="editStep >= 2 ?
                                                        'bg-blue-600 border-blue-600 text-white' :
                                                        'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                                    <span class="text-sm font-semibold" x-show="editStep < 2">2</span>
                                                    <svg x-show="editStep >= 2" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <p class="font-medium transition-colors duration-300"
                                                        :class="editStep >= 2 ? 'text-blue-600 dark:text-blue-400' :
                                                            'text-gray-500 dark:text-gray-400'">
                                                        {{ __('ui.location_details') }}</p>
                                                </div>
                                            </div>

                                            <!-- Connector -->
                                            <div class="w-16 h-0.5 transition-colors duration-300"
                                                :class="editStep >= 3 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                                            </div>

                                            <!-- Step 3 (Fasilitas) -->
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                                    :class="editStep >= 3 ?
                                                        'bg-blue-600 border-blue-600 text-white' :
                                                        'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                                    <span class="text-sm font-semibold" x-show="editStep < 3">3</span>
                                                    <svg x-show="editStep >= 3" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <p class="font-medium transition-colors duration-300"
                                                        :class="editStep >= 3 ? 'text-blue-600 dark:text-blue-400' :
                                                            'text-gray-500 dark:text-gray-400'">
                                                        {{ __('ui.facilities') }}</p>
                                                </div>
                                            </div>

                                            <!-- Connector -->
                                            <div class="w-16 h-0.5 transition-colors duration-300"
                                                :class="editStep >= 4 ? 'bg-blue-600' : 'bg-gray-300 dark:bg-gray-600'">
                                            </div>

                                            <!-- Step 4 (Foto) -->
                                            <div class="flex items-center">
                                                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-300"
                                                    :class="editStep >= 4 ?
                                                        'bg-blue-600 border-blue-600 text-white' :
                                                        'border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'">
                                                    <span class="text-sm font-semibold" x-show="editStep < 4">4</span>
                                                    <svg x-show="editStep >= 4" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <p class="font-medium transition-colors duration-300"
                                                        :class="editStep >= 4 ? 'text-blue-600 dark:text-blue-400' :
                                                            'text-gray-500 dark:text-gray-400'">
                                                        {{ __('ui.photos') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal content -->
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <form id="propertyFormEdit-{{ $property->idrec }}" method="POST"
                                            action="{{ route('properties.update', $property->idrec) }}"
                                            enctype="multipart/form-data" @submit.prevent="submitEditForm">
                                            @csrf
                                            @method('PUT')

                                            <!-- Step 1 - Basic Information -->
                                            <div x-show="editStep === 1"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-x-4"
                                                x-transition:enter-end="opacity-100 translate-x-0">
                                                <div class="space-y-6">
                                                    <!-- Nama Properti & Initial -->
                                                    <div class="grid grid-cols-12 gap-4">
                                                        <!-- Nama Properti -->
                                                        <div class="col-span-10">
                                                            <label for="property_name_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.property_name') }} <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                id="property_name_edit_{{ $property->idrec }}"
                                                                name="property_name" required
                                                                x-model="propertyData.name"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                placeholder="{{ __('ui.enter_property_name') }}">
                                                        </div>

                                                        <!-- Initial -->
                                                        <div class="col-span-2">
                                                            <label for="initial_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                Initial <span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="flex items-center">
                                                                <input type="text"
                                                                    id="initial_edit_{{ $property->idrec }}"
                                                                    name="initial" required maxlength="3"
                                                                    x-model="propertyData.initial"
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
                                                            @php
                                                                $propertyTypes = [
                                                                    ['label' => 'Kos', 'value' => 'Kos'],
                                                                    ['label' => 'Apartment', 'value' => 'Apartment'],
                                                                    ['label' => 'Villa', 'value' => 'Villa'],
                                                                    ['label' => 'Hotel', 'value' => 'Hotel'],
                                                                ];
                                                            @endphp
                                                            @foreach ($propertyTypes as $type)
                                                                <div class="relative">
                                                                    <input
                                                                        id="type-edit-{{ $property->idrec }}-{{ $type['value'] }}"
                                                                        name="property_type" type="radio"
                                                                        value="{{ $type['value'] }}"
                                                                        class="sr-only peer" required
                                                                        x-model="propertyData.tags">
                                                                    <label
                                                                        for="type-edit-{{ $property->idrec }}-{{ $type['value'] }}"
                                                                        class="flex items-center justify-center p-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all duration-200">
                                                                        {{ $type['label'] }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <!-- Gender Penghuni (khusus Kos) -->
                                                    <div x-show="propertyData.tags === 'Kos'"
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
                                                                @click="propertyData.gender = (propertyData.gender === 'male' ? null : 'male')"
                                                                :class="propertyData.gender === 'male'
                                                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 shadow-sm'
                                                                    : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-green-300 hover:bg-green-50/50'"
                                                                class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                                <span class="text-xl font-bold leading-none" :class="propertyData.gender === 'male' ? 'text-green-500' : 'text-gray-400'">♂</span>
                                                                <span>{{ __('ui.gender_male') }}</span>
                                                            </button>

                                                            <!-- Perempuan -->
                                                            <button type="button"
                                                                @click="propertyData.gender = (propertyData.gender === 'female' ? null : 'female')"
                                                                :class="propertyData.gender === 'female'
                                                                    ? 'border-pink-500 bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400 shadow-sm'
                                                                    : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-pink-300 hover:bg-pink-50/50'"
                                                                class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                                <span class="text-xl font-bold leading-none" :class="propertyData.gender === 'female' ? 'text-pink-500' : 'text-gray-400'">♀</span>
                                                                <span>{{ __('ui.gender_female') }}</span>
                                                            </button>

                                                            <!-- Campur -->
                                                            <button type="button"
                                                                @click="propertyData.gender = (propertyData.gender === 'mixed' ? null : 'mixed')"
                                                                :class="propertyData.gender === 'mixed'
                                                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 shadow-sm'
                                                                    : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:border-purple-300 hover:bg-purple-50/50'"
                                                                class="flex items-center gap-2 px-5 py-3 border-2 rounded-lg font-medium text-sm transition-all duration-200 focus:outline-none">
                                                                <span class="text-base font-bold leading-none" :class="propertyData.gender === 'mixed' ? 'text-green-500' : 'text-gray-400'">♂</span><span class="text-base font-bold leading-none -ml-1" :class="propertyData.gender === 'mixed' ? 'text-pink-500' : 'text-gray-400'">♀</span>
                                                                <span>{{ __('ui.gender_mixed') }}</span>
                                                            </button>
                                                        </div>
                                                        <p class="mt-2 text-xs text-gray-400 dark:text-gray-500">{{ __('ui.gender_hint') }}</p>
                                                    </div>

                                                    <!-- Deskripsi -->
                                                    <div>
                                                        <label for="description_edit_{{ $property->idrec }}"
                                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                            {{ __('ui.description') }} <span class="text-red-500">*</span>
                                                        </label>
                                                        <textarea id="description_edit_{{ $property->idrec }}" name="description" rows="4" required
                                                            x-model="propertyData.description"
                                                            class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                            placeholder="{{ __('ui.describe_your_property') }}..."></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 2 - Location Details -->
                                            <div x-show="editStep === 2"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-x-4"
                                                x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                                <div class="space-y-6">
                                                    <div class="relative">
                                                        <label for="full_address_edit_{{ $property->idrec }}"
                                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                            {{ __('ui.full_address') }} <span class="text-red-500">*</span>
                                                            <span class="text-xs font-normal text-gray-500 ml-2">({{ __('ui.type_to_search_address') }})</span>
                                                        </label>
                                                        <div class="relative">
                                                            <textarea id="full_address_edit_{{ $property->idrec }}" name="full_address" rows="3" required
                                                                x-model="propertyData.address" @input="searchAddress($event.target.value)"
                                                                @focus="showAddressSuggestions = addressSuggestions.length > 0" @click.outside="showAddressSuggestions = false"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                placeholder="{{ __('ui.enter_full_address') }}"></textarea>
                                                            <!-- Loading indicator -->
                                                            <div x-show="isAddressSearching"
                                                                class="absolute right-3 top-3">
                                                                <svg class="animate-spin h-5 w-5 text-blue-500"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12"
                                                                        cy="12" r="10" stroke="currentColor"
                                                                        stroke-width="4"></circle>
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
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                                            </path>
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                                            </path>
                                                                        </svg>
                                                                        <span
                                                                            class="text-sm text-gray-700 dark:text-gray-200"
                                                                            x-text="suggestion.display_name"></span>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label
                                                            class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                            {{ __('ui.pinpoint_location') }} <span class="text-red-500 ml-1">*</span>
                                                            <span class="text-gray-500 dark:text-gray-400 text-sm font-normal ml-2">({{ __('ui.click_to_mark_on_map') }})</span>
                                                        </label>
                                                        <div id="map_edit_{{ $property->idrec }}"
                                                            class="h-64 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-gray-200 dark:border-gray-600 flex items-center justify-center">
                                                            <div class="text-gray-500 dark:text-gray-400 text-center"
                                                                x-show="!propertyData.latitude || !propertyData.longitude">
                                                                <svg class="w-12 h-12 mx-auto mb-2" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                                                    </path>
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z">
                                                                    </path>
                                                                </svg>
                                                                <p>{{ __('ui.click_to_set_location') }}</p>
                                                            </div>
                                                        </div>
                                                        <div id="coordinates_edit_{{ $property->idrec }}"
                                                            class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                            <span
                                                                x-show="propertyData.latitude && propertyData.longitude">
                                                                {{ __('ui.coordinates') }}: <span
                                                                    x-text="propertyData.latitude"></span>,
                                                                <span x-text="propertyData.longitude"></span>
                                                            </span>
                                                        </div>
                                                        <input type="hidden"
                                                            id="latitude_edit_{{ $property->idrec }}" name="latitude"
                                                            x-model="propertyData.latitude">
                                                        <input type="hidden"
                                                            id="longitude_edit_{{ $property->idrec }}"
                                                            name="longitude" x-model="propertyData.longitude">
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="province_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.province') }} <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                id="province_edit_{{ $property->idrec }}"
                                                                name="province" required
                                                                x-model="propertyData.province"
                                                                placeholder="{{ __('ui.enter_province') }}"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                        </div>

                                                        <div>
                                                            <label for="city_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.city_regency') }} <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                id="city_edit_{{ $property->idrec }}" name="city"
                                                                required x-model="propertyData.city"
                                                                placeholder="{{ __('ui.enter_city_regency') }}"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" />
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="district_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.district') }} <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                id="district_edit_{{ $property->idrec }}"
                                                                name="district" required
                                                                x-model="propertyData.subdistrict"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                placeholder="{{ __('ui.enter_district') }}">
                                                        </div>

                                                        <div>
                                                            <label for="village_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.village') }} <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="text"
                                                                id="village_edit_{{ $property->idrec }}"
                                                                name="village" required x-model="propertyData.village"
                                                                class="w-full border-2 border-gray-200 dark:border-gray-600 rounded-lg shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                                                placeholder="{{ __('ui.enter_village') }}">
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label for="postal_code_edit_{{ $property->idrec }}"
                                                                class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.postal_code') }}
                                                            </label>
                                                            <input type="text"
                                                                id="postal_code_edit_{{ $property->idrec }}"
                                                                name="postal_code" x-model="propertyData.postal_code"
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
                                                                    @click="if(propertyData.latitude && propertyData.longitude) { fetchNearbyLocations(parseFloat(propertyData.latitude), parseFloat(propertyData.longitude)); }"
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
                                            <div x-show="editStep === 3"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-x-4"
                                                x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                                <div class="space-y-8">
                                                    <!-- General Facilities -->
                                                    <div>
                                                        <h3
                                                            class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-blue-600 dark:text-blue-400"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            {{ __('ui.general_facilities') }}
                                                        </h3>
                                                        <div
                                                            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto p-2">
                                                            @foreach ($generalFacilities as $facility)
                                                                <div class="relative">
                                                                    <input
                                                                        id="general-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        name="general[]" type="checkbox"
                                                                        value="{{ $facility->idrec }}"
                                                                        class="sr-only peer"
                                                                        x-model="propertyData.general"
                                                                        :checked="propertyData.general.includes(
                                                                            {{ $facility->idrec }})">
                                                                    <label
                                                                        for="general-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-blue-600 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/30 peer-checked:text-blue-600 dark:peer-checked:text-blue-400 transition-all duration-200">
                                                                        <div class="flex-1 overflow-hidden">
                                                                            <span
                                                                                class="block break-words">{{ $facility->facility }}</span>
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
                                                                <svg class="w-10 h-10 mx-auto text-gray-400"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <p
                                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ __('ui.no_general_facilities') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Security Facilities -->
                                                    <div>
                                                        <h3
                                                            class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-green-600 dark:text-green-400"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
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
                                                                    <input
                                                                        id="security-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        name="security[]" type="checkbox"
                                                                        value="{{ $facility->idrec }}"
                                                                        class="sr-only peer"
                                                                        x-model="propertyData.security"
                                                                        :checked="propertyData.security.includes(
                                                                            {{ $facility->idrec }})">
                                                                    <label
                                                                        for="security-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-green-600 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/30 peer-checked:text-green-600 dark:peer-checked:text-green-400 transition-all duration-200">
                                                                        <div class="flex-1 overflow-hidden">
                                                                            <span
                                                                                class="block break-words">{{ $facility->facility }}</span>
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
                                                                <svg class="w-10 h-10 mx-auto text-gray-400"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <p
                                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ __('ui.no_security_facilities') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <!-- Amenities -->
                                                    <div>
                                                        <h3
                                                            class="font-semibold text-lg text-gray-800 dark:text-white mb-4 flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-purple-600 dark:text-purple-400"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
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
                                                                    <input
                                                                        id="amenities-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        name="amenities[]" type="checkbox"
                                                                        value="{{ $facility->idrec }}"
                                                                        class="sr-only peer"
                                                                        x-model="propertyData.amenities"
                                                                        :checked="propertyData.amenities.includes(
                                                                            {{ $facility->idrec }})">
                                                                    <label
                                                                        for="amenities-edit-{{ $facility->idrec }}-{{ $property->idrec }}"
                                                                        class="flex items-start p-4 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 peer-checked:border-purple-600 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/30 peer-checked:text-purple-600 dark:peer-checked:text-purple-400 transition-all duration-200">
                                                                        <div class="flex-1 overflow-hidden">
                                                                            <span
                                                                                class="block break-words">{{ $facility->facility }}</span>
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
                                                                <svg class="w-10 h-10 mx-auto text-gray-400"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <p
                                                                    class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                                    {{ __('ui.no_additional_services') }}</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Step 4 - Photos -->
                                            <div x-show="editStep === 4"
                                                x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0 translate-x-4"
                                                x-transition:enter-end="opacity-100 translate-x-0" x-cloak>
                                                <div class="space-y-6">
                                                    <div>
                                                        <!-- Hidden field to store thumbnail index -->
                                                        <input type="hidden" name="thumbnail_index"
                                                            x-model="thumbnailIndex">

                                                        <label
                                                            class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                                            {{ __('ui.property_photos') }} <span class="text-red-500">*</span>
                                                            <span
                                                                class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                                                (Minimal <span x-text="editMinImages"></span> foto,
                                                                maksimal <span x-text="editMaxImages"></span> foto)
                                                            </span>
                                                        </label>

                                                        <!-- Thumbnail Preview Section -->
                                                        <div class="mb-6">
                                                            <h4
                                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.current_thumbnail') }} <span class="text-red-500">*</span>
                                                                <span class="text-xs font-normal text-gray-500 dark:text-gray-400">({{ __('ui.main_photo_property') }})</span>
                                                            </h4>

                                                            <div class="flex items-center space-x-4">
                                                                <!-- Thumbnail Preview -->
                                                                <div
                                                                    class="w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-gray-300 dark:border-gray-600 overflow-hidden relative flex items-center justify-center">
                                                                    <template x-if="getCurrentThumbnail()">
                                                                        <img :src="getCurrentThumbnail()?.url"
                                                                            class="w-full h-full object-cover"
                                                                            alt="Current Thumbnail">
                                                                    </template>
                                                                    <div class="absolute inset-0 flex items-center justify-center text-gray-400 dark:text-gray-500"
                                                                        x-show="!getCurrentThumbnail()">
                                                                        <svg class="w-10 h-10" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                        </svg>
                                                                    </div>
                                                                </div>

                                                                <!-- Thumbnail Selection Instructions -->
                                                                <div class="flex-1">
                                                                    <p
                                                                        class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                                        <span x-show="thumbnailIndex === null"
                                                                            class="font-medium text-red-500">
                                                                            {{ __('ui.no_thumbnail_selected') }}!
                                                                        </span>
                                                                        <span x-show="thumbnailIndex !== null"
                                                                            class="font-medium text-green-600 dark:text-green-400">
                                                                            {{ __('ui.thumbnail_selected') }}.
                                                                        </span>
                                                                        {{ __('ui.click_photo_for_thumbnail') }}.
                                                                    </p>
                                                                    <p
                                                                        class="text-xs text-gray-500 dark:text-gray-400">
                                                                        {{ __('ui.choose_best_thumbnail_property') }}.
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Upload Area -->
                                                        <div x-show="editCanUploadMore" @drop="handleEditDrop($event)"
                                                            @dragover.prevent @dragenter.prevent
                                                            class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors duration-200 cursor-pointer"
                                                            :class="{ 'border-blue-400 dark:border-blue-500 bg-blue-50 dark:bg-blue-900/20': editCanUploadMore }">
                                                            <div class="space-y-2">
                                                                <svg class="w-12 h-12 mx-auto text-gray-400 dark:text-gray-500"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                <div
                                                                    class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                                                    <label
                                                                        for="edit_property_images_{{ $property->idrec }}"
                                                                        class="relative cursor-pointer bg-white dark:bg-gray-800 rounded-md font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                        <span>{{ __('ui.upload_photo') }}</span>
                                                                        <input
                                                                            id="edit_property_images_{{ $property->idrec }}"
                                                                            name="edit_property_images[]"
                                                                            type="file" multiple accept="image/*"
                                                                            @change="handleEditFileSelect($event)"
                                                                            class="sr-only">
                                                                    </label>
                                                                    <p class="pl-1">{{ __('ui.or') }} {{ __('ui.drag_and_drop') }}</p>
                                                                </div>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ __('ui.photo_upload_hint') }}</p>
                                                                <p class="text-xs text-blue-600 dark:text-blue-400"
                                                                    x-text="`Dapat upload ${editRemainingSlots} foto lagi`">
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <!-- Full Upload Message -->
                                                        <div x-show="!editCanUploadMore"
                                                            class="border-2 border-green-300 dark:border-green-600 rounded-lg p-8 text-center bg-green-50 dark:bg-green-900/20">
                                                            <div class="space-y-2">
                                                                <svg class="w-12 h-12 mx-auto text-green-500"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                <p
                                                                    class="text-sm text-green-600 dark:text-green-400 font-medium">
                                                                    <span x-text="editMaxImages"></span> foto telah
                                                                    diupload!
                                                                </p>
                                                                <p class="text-xs text-green-500 dark:text-green-400">
                                                                    {{ __('ui.max_photos_reached') }}</p>
                                                            </div>
                                                        </div>

                                                        <!-- Image Preview Grid -->
                                                        <div x-show="getAllImages().length > 0" class="mt-4">
                                                            <h4
                                                                class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                                                {{ __('ui.uploaded_photos') }}
                                                            </h4>

                                                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3"
                                                                x-transition:enter="transition ease-out duration-300"
                                                                x-transition:enter-start="opacity-0 scale-95"
                                                                x-transition:enter-end="opacity-100 scale-100">

                                                                <!-- Existing Images -->
                                                                <template
                                                                    x-for="(image, index) in propertyData.existingImages"
                                                                    :key="'existing-' + index">
                                                                    <div class="relative group"
                                                                        x-show="!image.markedForDeletion"
                                                                        @click="setThumbnail(getImageIndex('existing', index))">
                                                                        <!-- Image Container -->
                                                                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                                                            :class="thumbnailIndex === getImageIndex('existing',
                                                                                    index) ?
                                                                                'border-blue-600 ring-2 ring-blue-400' :
                                                                                'border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500'">
                                                                            <img :src="image.url"
                                                                                :alt="'Existing Image ' + (getDisplayIndex(
                                                                                    'existing', index) + 1)"
                                                                                class="w-full h-full object-cover">

                                                                            <!-- Thumbnail Badge -->
                                                                            <div x-show="thumbnailIndex === getImageIndex('existing', index)"
                                                                                class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                                Thumbnail
                                                                            </div>

                                                                            <!-- Image Number -->
                                                                            <div
                                                                                class="absolute bottom-1 left-1 bg-gray-800 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                                <span
                                                                                    x-text="getDisplayIndex('existing', index) + 1"></span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Remove Button -->
                                                                        <button type="button"
                                                                            @click.stop="removeEditExistingImage(index)"
                                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                                            <svg class="w-3 h-3" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                        </button>

                                                                        <!-- Hidden Image ID -->
                                                                        <input type="hidden" name="existing_images[]"
                                                                            :value="image.id">
                                                                    </div>
                                                                </template>

                                                                <!-- New Images -->
                                                                <template x-for="(image, index) in editImages"
                                                                    :key="'new-' + index">
                                                                    <div class="relative group"
                                                                        @click="setThumbnail(getImageIndex('new', index))">
                                                                        <!-- Image Container -->
                                                                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden border-2 transition-all duration-200"
                                                                            :class="thumbnailIndex === getImageIndex('new',
                                                                                    index) ?
                                                                                'border-blue-600 ring-2 ring-blue-400' :
                                                                                'border-gray-200 dark:border-gray-600 hover:border-blue-400 dark:hover:border-blue-500'">
                                                                            <img :src="image.url"
                                                                                :alt="'New Image ' + (getDisplayIndex('new',
                                                                                    index) + 1)"
                                                                                class="w-full h-full object-cover">

                                                                            <!-- Thumbnail Badge -->
                                                                            <div x-show="thumbnailIndex === getImageIndex('new', index)"
                                                                                class="absolute top-1 right-1 bg-blue-600 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                                Thumbnail
                                                                            </div>

                                                                            <!-- Image Number -->
                                                                            <div
                                                                                class="absolute bottom-1 left-1 bg-gray-800 text-white text-xs px-1.5 py-0.5 rounded-full font-medium">
                                                                                <span
                                                                                    x-text="getDisplayIndex('new', index) + 1"></span>
                                                                            </div>
                                                                        </div>

                                                                        <!-- Remove Button -->
                                                                        <button @click.stop="removeEditImage(index)"
                                                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-[10px] hover:bg-red-600 transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                                            <svg class="w-3 h-3" fill="none"
                                                                                stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </template>
                                                            </div>
                                                        </div>

                                                        <!-- Progress Indicator -->
                                                        <div class="mt-4">
                                                            <div
                                                                class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                                <span>Progress Upload</span>
                                                                <span
                                                                    x-text="`${getAllImages().length}/${editMaxImages} foto`"></span>
                                                            </div>
                                                            <div
                                                                class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                                <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                                    :style="`width: ${(getAllImages().length / editMaxImages) * 100}%`">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Validation Messages -->
                                                        <div class="mt-3 space-y-2">
                                                            <div x-show="getAllImages().length < editMinImages"
                                                                class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                                                <p class="text-sm text-red-600 dark:text-red-400">
                                                                    <span class="font-medium">{{ __('ui.warning') }}:</span>
                                                                    {{ __('ui.validation_required_photos') }}
                                                                </p>
                                                            </div>

                                                            <div x-show="thumbnailIndex === null && getAllImages().length > 0"
                                                                class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                                                <p class="text-sm text-yellow-600 dark:text-yellow-400">
                                                                    <span class="font-medium">{{ __('ui.warning') }}:</span>
                                                                    {{ __('ui.validation_required_thumbnail') }}.
                                                                </p>
                                                            </div>

                                                            <div x-show="getAllImages().length >= editMinImages && thumbnailIndex !== null"
                                                                class="p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                                                                <p class="text-sm text-green-600 dark:text-green-400">
                                                                    {{ __('ui.photos_valid_thumbnail_selected') }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Form Actions -->
                                            <div class="mt-6 flex justify-end">
                                                <div>
                                                    <button type="button" x-show="editStep > 1" @click="editStep--"
                                                        class="px-6 py-2 border-2 border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 19l-7-7 7-7">
                                                            </path>
                                                        </svg>
                                                        {{ __('ui.previous') }}
                                                    </button>
                                                    <button type="button" x-show="editStep < 4"
                                                        @click="validateEditStep(editStep) && editStep++"
                                                        class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                        {{ __('ui.next') }}
                                                        <svg class="w-4 h-4 inline ml-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button type="submit" x-show="editStep === 4"
                                                        class="px-6 py-2 border-2 border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                                        <svg class="w-4 h-4 inline mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7">
                                                            </path>
                                                        </svg>
                                                        {{ __('ui.update') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_properties_found') }}.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
