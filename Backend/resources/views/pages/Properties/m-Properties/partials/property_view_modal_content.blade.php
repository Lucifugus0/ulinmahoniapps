{{-- Property View Modal Content — rendered at page level to avoid clipping --}}

<!-- Modal header -->
<div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800">
    <div class="flex items-center gap-4">
        <div class="flex-shrink-0 h-14 w-14 rounded-full bg-indigo-500 flex items-center justify-center shadow-lg">
            <span class="text-white font-bold text-xl" x-text="selectedProperty.name ? selectedProperty.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : '?'"></span>
        </div>
        <div class="text-left">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1" x-text="selectedProperty.name"></h3>
            <p class="text-gray-600 dark:text-gray-300 flex items-center">
                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span x-text="selectedProperty.city + ', ' + selectedProperty.province"></span>
            </p>
        </div>
    </div>
    <button type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200 p-2 hover:bg-white dark:hover:bg-gray-700 rounded-full" @click="modalOpenDetail = false">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<!-- Modal content -->
<div class="overflow-y-auto flex-1">
    <!-- Property image slider -->
    <div class="relative h-72 overflow-hidden bg-gray-200 dark:bg-gray-700">
        <div class="flex h-full transition-transform duration-300 ease-in-out" :style="'transform: translateX(-' + (selectedProperty.currentImageIndex * 100) + '%)'">
            <template x-for="(image, index) in selectedProperty.images" :key="index">
                <img :src="image" alt="Property Image" class="w-full h-full object-cover object-center flex-shrink-0">
            </template>
        </div>
        <button x-show="selectedProperty.images.length > 1" @click="selectedProperty.currentImageIndex = (selectedProperty.currentImageIndex - 1 + selectedProperty.images.length) % selectedProperty.images.length" class="absolute left-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 rounded-full p-2 shadow-md">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
        </button>
        <button x-show="selectedProperty.images.length > 1" @click="selectedProperty.currentImageIndex = (selectedProperty.currentImageIndex + 1) % selectedProperty.images.length" class="absolute right-2 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-gray-800/80 hover:bg-white dark:hover:bg-gray-700 rounded-full p-2 shadow-md">
            <svg class="w-6 h-6 text-gray-800 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
        </button>
        <div class="absolute top-4 right-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm px-4 py-2 rounded-full shadow-lg">
            <span :class="selectedProperty.status === 'Active' ? 'text-green-600 dark:text-green-400 font-semibold' : 'text-red-600 dark:text-red-400 font-semibold'" class="text-sm flex items-center">
                <span class="w-2.5 h-2.5 rounded-full mr-2 block" :class="selectedProperty.status === 'Active' ? 'bg-green-500' : 'bg-red-500'"></span>
                <span x-text="selectedProperty.status || ''"></span>
            </span>
        </div>
        <div x-show="selectedProperty.images.length > 1" class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2">
            <template x-for="(image, index) in selectedProperty.images" :key="index">
                <button @click="selectedProperty.currentImageIndex = index" class="w-3 h-3 rounded-full transition-all" :class="selectedProperty.currentImageIndex === index ? 'bg-white dark:bg-gray-300 w-6' : 'bg-white/50 dark:bg-gray-500/50'"></button>
            </template>
        </div>
    </div>

    <div class="p-6 space-y-8">
        <!-- Description -->
        <div class="text-center">
            <div class="text-gray-700 dark:text-gray-300 text-lg leading-relaxed prose dark:prose-invert max-w-none" x-html="selectedProperty.description"></div>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Column - Property Info -->
            <div class="lg:w-1/3">
                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-xl space-y-6 text-center">
                    <div class="flex flex-col items-center space-y-1">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Added By</p>
                        <p class="text-gray-800 dark:text-white font-medium" x-text="selectedProperty.creator"></p>
                    </div>
                    <div x-show="selectedProperty.tags === 'Kos'" class="flex flex-col items-center space-y-1">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('ui.gender_tenant') }}</p>
                        <div x-show="selectedProperty.gender === 'male'" class="flex items-center gap-1.5"><span class="text-2xl font-bold text-green-500">♂</span><span class="text-gray-800 dark:text-white font-medium">{{ __('ui.gender_male') }}</span></div>
                        <div x-show="selectedProperty.gender === 'female'" class="flex items-center gap-1.5"><span class="text-2xl font-bold text-pink-500">♀</span><span class="text-gray-800 dark:text-white font-medium">{{ __('ui.gender_female') }}</span></div>
                        <div x-show="selectedProperty.gender === 'mixed'" class="flex items-center gap-0.5"><span class="text-xl font-bold text-green-500">♂</span><span class="text-xl font-bold text-pink-500">♀</span><span class="text-gray-800 dark:text-white font-medium ml-1">{{ __('ui.gender_mixed') }}</span></div>
                        <div x-show="!selectedProperty.gender"><span class="text-gray-400 dark:text-gray-500 text-sm italic">-</span></div>
                    </div>
                    <div class="flex flex-col items-center space-y-1">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Location</p>
                        <a class="text-gray-800 dark:text-white font-medium underline hover:text-blue-600" :href="`https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(selectedProperty.location || (selectedProperty.city + ', ' + selectedProperty.province))}`" target="_blank">Click here for Maps</a>
                    </div>
                    <div class="flex flex-col items-center space-y-1">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Added</p>
                        <p class="text-gray-800 dark:text-white font-medium" x-text="selectedProperty.created_at"></p>
                    </div>
                    <div class="flex flex-col items-center space-y-1">
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Updated</p>
                        <p class="text-gray-800 dark:text-white font-medium" x-text="selectedProperty.updated_at"></p>
                    </div>
                </div>
            </div>

            <!-- Right Column - Features -->
            <div class="lg:w-2/3">
                <div x-show="selectedProperty.general.length > 0 || selectedProperty.security.length > 0 || selectedProperty.amenities.length > 0" class="space-y-8 text-right">
                    <div x-show="selectedProperty.general.length > 0" class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white text-right">General Facilities</h4>
                        <div class="flex flex-wrap gap-2 justify-end">
                            <template x-for="facilityId in selectedProperty.general" :key="facilityId">
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                                    <span x-show="getFacilityIcon(facilityId, 'general')" class="iconify text-sm" :data-icon="getFacilityIcon(facilityId, 'general')"></span>
                                    <span x-text="getFacilityName(facilityId, 'general')"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div x-show="selectedProperty.security.length > 0" class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white text-right">Security Facilities</h4>
                        <div class="flex flex-wrap gap-2 justify-end">
                            <template x-for="facilityId in selectedProperty.security" :key="facilityId">
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800">
                                    <span x-show="getFacilityIcon(facilityId, 'security')" class="iconify text-sm" :data-icon="getFacilityIcon(facilityId, 'security')"></span>
                                    <span x-text="getFacilityName(facilityId, 'security')"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    <div x-show="selectedProperty.amenities.length > 0" class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white text-right">Amenities</h4>
                        <div class="flex flex-wrap gap-2 justify-end">
                            <template x-for="facilityId in selectedProperty.amenities" :key="facilityId">
                                <span class="px-2.5 py-1 inline-flex items-center gap-1 text-xs leading-5 font-medium rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
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
<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-between items-center">
    <div class="text-sm text-gray-500 dark:text-gray-400">Press ESC or click outside to close</div>
    <button @click="modalOpenDetail = false" class="px-6 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-all duration-200 font-medium hover:shadow-md">Close</button>
</div>
