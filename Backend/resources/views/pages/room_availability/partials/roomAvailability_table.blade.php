<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-slate-100">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.room') }}
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.property') }}
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.type_capacity') }}
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.status') }}
                    </th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.related_bookings') }}
                    </th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.action') }}
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rooms as $index => $room)
                    <tr class="hover:bg-blue-50/30 transition-all duration-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                        <!-- Kamar -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 h-12 w-12 relative group">
                                    @if ($room->thumbnail)
                                        <img class="h-12 w-12 rounded-xl object-cover shadow-sm ring-2 ring-gray-100 group-hover:ring-blue-200 transition-all duration-200"
                                            src="{{ asset('storage/' . $room->thumbnail->image) }}"
                                            alt="{{ $room->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $room->name }}</div>
                                    <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        No. {{ $room->no }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Property -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $room->property->name ?? '-' }}</div>
                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $room->property->province ?? '-' }}
                            </div>
                        </td>

                        <!-- Tipe & Kapasitas -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-sm font-medium capitalize">
                                {{ $room->name }}
                            </div>
                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                {{ $room->capacity }} {{ __('ui.person') }}
                            </div>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($room->rental_status == 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 ring-1 ring-inset ring-rose-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    {{ __('ui.occupied') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('ui.available') }}
                                </span>
                            @endif
                        </td>

                        <!-- Booking Terkait -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @php
                                $startDate = request('start_date');
                                $endDate = request('end_date');

                                $currentBookings = $room->bookings->filter(function ($booking) use ($startDate, $endDate) {
                                    if (!$booking->transaction) {
                                        return false;
                                    }
                                    if (!$startDate || !$endDate) {
                                        return true;
                                    }
                                    $checkIn = $booking->transaction->check_in;
                                    $checkOut = $booking->transaction->check_out;
                                    return !($checkOut < $startDate || $checkIn > $endDate);
                                });

                                // Hitung penyewa unik: user_id sama = perpanjangan = 1 penyewa
                                // Hanya booking dengan is_renewal=0 yang dihitung sebagai booking baru
                                $uniqueTenantCount = $currentBookings
                                    ->groupBy(function ($booking) {
                                        return $booking->transaction->user_id
                                            ?? $booking->user_id
                                            ?? $booking->order_id;
                                    })
                                    ->count();
                            @endphp

                            @if ($currentBookings->count() > 0)
                                <div x-data="modalView()" class="relative">
                                    <button
                                        class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition-all duration-200 font-medium text-sm ring-1 ring-inset ring-blue-200 hover:ring-blue-300"
                                        type="button" @click.prevent="openModal({{ $room->idrec }})"
                                        title="{{ __('ui.view_bookings') }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>{{ $uniqueTenantCount }} {{ __('ui.booking') }}</span>
                                    </button>

                                    <!-- Modal Backdrop -->
                                    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                        x-show="modalOpenDetail" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-out duration-200"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        aria-hidden="true" x-cloak>
                                    </div>

                                    <!-- Modal Dialog -->
                                    <div id="property-detail-modal"
                                        class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
                                        role="dialog" aria-modal="true" x-show="modalOpenDetail"
                                        x-transition:enter="transition ease-in-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in-out duration-200"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95" x-cloak>

                                        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[90vh] flex flex-col"
                                            @click.outside="modalOpenDetail = false"
                                            @keydown.escape.window="modalOpenDetail = false">

                                            <!-- Modal Header -->
                                            <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50">
                                                <div class="text-left">
                                                    <div class="flex items-center gap-3 mb-1">
                                                        <div class="p-2 bg-blue-100 rounded-lg">
                                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-xl font-bold text-gray-900" x-text="selectedProperty.roomName"></h3>
                                                    </div>
                                                    <p class="text-gray-500 text-sm ml-11">{{ __('ui.active_users_list') }}</p>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-right">
                                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            <span class="text-sm font-semibold text-gray-700" x-text="`${selectedProperty.totalBookings || 0} booking`"></span>
                                                        </div>
                                                        <div class="flex items-center justify-end mt-2" x-show="loading">
                                                            <svg class="animate-spin h-4 w-4 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24">
                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                            </svg>
                                                            <span class="text-xs text-gray-500">{{ __('ui.loading') }}</span>
                                                        </div>
                                                    </div>
                                                    <button type="button"
                                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-all duration-200"
                                                        @click="modalOpenDetail = false">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Modal Content -->
                                            <div class="overflow-y-auto flex-1 p-6 bg-gray-50/50">
                                                <!-- Empty State -->
                                                <template x-if="!loading && (!selectedProperty.bookings || selectedProperty.bookings.length === 0)">
                                                    <div class="text-center py-16">
                                                        <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </div>
                                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('ui.no_active_bookings') }}</h3>
                                                        <p class="text-gray-500 max-w-sm mx-auto text-sm">
                                                            {{ __('ui.no_active_bookings_desc') }}
                                                        </p>
                                                    </div>
                                                </template>

                                                <!-- Loading State -->
                                                <template x-if="loading">
                                                    <div class="flex flex-col justify-center items-center py-16">
                                                        <div class="relative">
                                                            <div class="w-14 h-14 border-4 border-blue-200 rounded-full animate-spin border-t-blue-600"></div>
                                                        </div>
                                                        <p class="text-gray-500 mt-4 text-sm">{{ __('ui.loading_booking_data') }}</p>
                                                    </div>
                                                </template>

                                                <!-- Bookings List -->
                                                <template x-if="!loading && selectedProperty.bookings && selectedProperty.bookings.length > 0">
                                                    <div class="grid gap-4 md:grid-cols-2">
                                                        <template x-for="booking in selectedProperty.bookings" :key="booking.id">
                                                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden">
                                                                <!-- Card Header -->
                                                                <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50/50">
                                                                    <div class="flex justify-between items-start">
                                                                        <div class="flex-1 min-w-0">
                                                                            <div class="flex items-center gap-2">
                                                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs" x-text="booking.user_name ? booking.user_name.charAt(0).toUpperCase() : 'U'"></div>
                                                                                <div class="min-w-0">
                                                                                    <h4 class="font-semibold text-gray-900 truncate" x-text="booking.user_name"></h4>
                                                                                    <p class="text-xs text-gray-500 truncate" x-text="booking.user_email"></p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="flex flex-col items-end space-y-1.5 ml-3">
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="booking.status_badge" x-text="booking.status"></span>
                                                                            <span x-show="booking.is_renewal" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                                                Perpanjangan
                                                                            </span>
                                                                            <span x-show="booking.is_room_changed" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-700 ring-1 ring-purple-200">
                                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                                                Pindah Kamar
                                                                            </span>
                                                                            <span class="text-[10px] font-mono text-gray-400 bg-gray-100 px-1.5 py-0.5 rounded" x-text="booking.booking_code"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Card Body -->
                                                                <div class="p-5">
                                                                    <div class="grid grid-cols-2 gap-3 mb-4">
                                                                        <div class="text-center p-3 bg-blue-50 rounded-xl">
                                                                            <p class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider mb-1">{{ __('ui.check_in') }}</p>
                                                                            <p class="text-sm font-bold text-gray-900" x-text="formatDate(booking.check_in)"></p>
                                                                        </div>
                                                                        <div class="text-center p-3 bg-emerald-50 rounded-xl">
                                                                            <p class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wider mb-1">{{ __('ui.check_out') }}</p>
                                                                            <p class="text-sm font-bold text-gray-900" x-text="formatDate(booking.check_out)"></p>
                                                                        </div>
                                                                    </div>

                                                                    <div class="space-y-2.5">
                                                                        <div class="flex justify-between items-center text-sm">
                                                                            <span class="text-gray-500 flex items-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                                {{ __('ui.duration') }}
                                                                            </span>
                                                                            <span class="font-semibold text-gray-900" x-text="booking.duration"></span>
                                                                        </div>
                                                                        <div class="flex justify-between items-center text-sm">
                                                                            <span class="text-gray-500 flex items-center gap-1.5">
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                                {{ __('ui.total') }}
                                                                            </span>
                                                                            <span class="font-bold text-emerald-600" x-text="formatCurrency(booking.total_amount)"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Card Footer -->
                                                                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                                                                    <div class="flex justify-between items-center text-xs">
                                                                        <div class="flex items-center gap-1.5">
                                                                            <span class="w-2 h-2 rounded-full"
                                                                                :class="{
                                                                                    'bg-amber-400 animate-pulse': booking.payment_status === 'unpaid',
                                                                                    'bg-emerald-400': booking.payment_status === 'paid',
                                                                                    'bg-yellow-400 animate-pulse': booking.payment_status === 'pending',
                                                                                    'bg-red-400': booking.payment_status === 'failed'
                                                                                }">
                                                                            </span>
                                                                            <span class="text-gray-600 capitalize font-medium" x-text="booking.payment_status"></span>
                                                                        </div>
                                                                        <span class="text-gray-400" x-text="booking.created_at"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Modal Footer -->
                                            <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-between items-center">
                                                <div class="text-sm text-gray-500">
                                                    <span x-text="'{{ __('ui.showing_bookings', ['shown' => '\' + (selectedProperty.bookings ? selectedProperty.bookings.length : 0) + \'', 'total' => '\' + (selectedProperty.totalBookings || 0) + \'']) }}'.replace(':shown', selectedProperty.bookings ? selectedProperty.bookings.length : 0).replace(':total', selectedProperty.totalBookings || 0)"></span>
                                                </div>
                                                <button @click="modalOpenDetail = false"
                                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200 font-medium text-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    {{ __('ui.close') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                    {{ __('ui.no_bookings') }}
                                </span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex justify-center items-center">
                                @if ($room->rental_status == 1)
                                    <button onclick="updateRoomStatus({{ $room->idrec }}, 0)"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('ui.set_available') }}
                                    </button>
                                @else
                                    <button onclick="updateRoomStatus({{ $room->idrec }}, 1)"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ __('ui.set_booked') }}
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('ui.no_room_data') }}</h3>
                                <p class="text-gray-500 text-sm">{{ __('ui.no_room_found_filter') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
