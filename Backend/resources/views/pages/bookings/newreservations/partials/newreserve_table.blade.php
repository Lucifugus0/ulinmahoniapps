{{-- Confirmed Bookings page table — column structure matches All Bookings (Booking ID |
     Booking Period | Name | Property/Room | Status | Action). The big inline check-in modal
     in the Action cell is preserved as-is below; only the columns leading up to it changed. --}}
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_booking_id') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_dates_col') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_name') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_property_room') }}
            </th>
            @if ($showStatus ?? true)
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ __('ui.allbookings_col_status') }}
                </th>
            @endif
            @if ($showActions ?? true)
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ __('ui.actions') }}
                </th>
            @endif
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($checkIns as $booking)
            <tr>
                {{-- Booking ID cell with at-a-glance badges (Deposit / Renewed / Checked Out). --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium align-top">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->order_id }}</div>
                    @if ($booking->created_at)
                        <div class="text-xs text-gray-400">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                    @endif
                    @php
                        $depositFee = (int) ($booking->transaction->deposit_fee ?? 0);
                        $isRenewed = ($booking->transaction?->renewal_status ?? 0) == 1;
                        $isCheckedOut = $booking->check_out_at && !$isRenewed;
                    @endphp
                    @if ($depositFee > 0 || $isRenewed || $isCheckedOut)
                        <div class="flex flex-wrap items-center gap-1 mt-1">
                            @if ($depositFee > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                    {{ __('ui.allbookings_badge_deposit') }}
                                </span>
                            @endif
                            @if ($isRenewed)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                    {{ __('ui.allbookings_badge_renewed') }}
                                </span>
                            @endif
                            @if ($isCheckedOut)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                    {{ __('ui.allbookings_badge_checked_out') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </td>
                {{-- Booking Period cell — same 3-row stacked format as All Bookings. --}}
                <td class="px-6 py-4 text-sm text-gray-500 text-left align-top">
                    @php
                        $checkedInBy = $booking->checkedInByUser;
                        $checkedInByName = $checkedInBy
                            ? (trim(($checkedInBy->first_name ?? '') . ' ' . ($checkedInBy->last_name ?? '')) ?: ($checkedInBy->username ?? null))
                            : null;
                        $checkedOutBy = $booking->checkedOutByUser;
                        $checkedOutByName = $checkedOutBy
                            ? (trim(($checkedOutBy->first_name ?? '') . ' ' . ($checkedOutBy->last_name ?? '')) ?: ($checkedOutBy->username ?? null))
                            : null;
                    @endphp
                    @if ($booking->transaction && $booking->transaction->check_in)
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <div class="whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('Y M d') }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('H:i') }}</span>
                            </div>
                            <span class="text-gray-400">|</span>
                            @if ($booking->transaction->check_out)
                                <div class="whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $booking->transaction->check_out->format('Y M d') }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $booking->transaction->check_out->format('H:i') }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">{{ __('ui.allbookings_not_checked_out') }}</span>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">
                            {{ __('ui.allbookings_not_checked_in') }}
                        </span>
                    @endif

                    @if ($booking->check_in_at)
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 mt-1.5">
                            {{ __('ui.allbookings_checkin_at') }} {{ $booking->check_in_at->format('Y-m-d H:i') }}
                            @if ($checkedInByName)
                                <span class="text-gray-500 dark:text-gray-400 font-normal">{{ __('ui.allbookings_by') }}</span>
                                <span>{{ $checkedInByName }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($booking->check_out_at)
                        @if ($booking->transaction?->renewal_status == 1)
                            <div class="text-xs font-medium text-amber-600 dark:text-amber-400 mt-1">
                                {{ __('ui.allbookings_renewed_at') }} {{ $booking->check_out_at->format('Y-m-d H:i') }}
                            </div>
                        @else
                            <div class="text-xs font-medium text-indigo-600 dark:text-indigo-400 mt-1">
                                {{ __('ui.allbookings_checkout_at') }} {{ $booking->check_out_at->format('Y-m-d H:i') }}
                                @if ($checkedOutByName)
                                    <span class="text-gray-500 dark:text-gray-400 font-normal">{{ __('ui.allbookings_by') }}</span>
                                    <span>{{ $checkedOutByName }}</span>
                                @endif
                            </div>
                        @endif
                    @endif
                </td>
                {{-- Name cell with full fallback chain. --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ trim(($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '')) ?: ($booking->transaction->user_name ?? 'N/A') }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? $booking->user_email ?? $booking->user->email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? $booking->user_phone_number ?? $booking->user->phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                {{-- Property/Room. --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>
                {{-- Status — page semantics: paid bookings awaiting check-in. Use the same Renewed
                     override as All Bookings so renewed parents read as Renewed. --}}
                @if ($showStatus ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @php
                            $statusClassesCB = [
                                'Waiting For Check-In' => 'bg-cyan-100 text-cyan-800',
                                'Checked-In' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                                'Checked-Out' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                                'Renewed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                                'Unknown' => 'bg-gray-100 text-gray-800',
                            ];
                            $displayStatus = $booking->status;
                            if ($displayStatus === 'Checked-Out' && ($booking->transaction?->renewal_status ?? 0) == 1) {
                                $displayStatus = 'Renewed';
                            }
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClassesCB[$displayStatus] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $displayStatus }}
                        </span>
                    </td>
                @endif
                @if ($showActions ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        @if (is_null($booking->check_in_at))
                            <div class="flex flex-col items-center space-y-2">
                                @if (!is_null($booking->doc_path) && $booking->is_printed != 1)
                                    {{-- Document exists and not yet printed - Show Print Registration Form button --}}
                                    <a href="{{ route('newReserv.checkin.regist', $booking->order_id) }}"
                                        onclick="event.preventDefault();
                                                 window.open(this.href, 'RegistrationForm', 'width=800,height=600');
                                                 // Refresh tabel setelah 2 detik untuk update status is_printed
                                                 setTimeout(() => {
                                                     if (typeof fetchFilteredBookings === 'function') {
                                                         fetchFilteredBookings();
                                                     } else {
                                                         window.location.reload();
                                                     }
                                                 }, 2000);"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-amber-600 rounded hover:bg-amber-700 focus:outline-none">
                                        {{ __('ui.print_regist_form') }}
                                    </a>
                                @endif
                                <div x-data="checkInModal('{{ $booking->order_id }}', {{ is_null($booking->doc_path) ? 'true' : 'false' }})"
                                     class="{{ (!is_null($booking->doc_path) && $booking->is_printed != 1) ? 'hidden' : '' }}"
                                     id="checkin-btn-{{ $booking->order_id }}">
                                    <!-- Tombol Trigger -->
                                    <button type="button"
                                        @click="openModal('{{ $booking->idrec }}', '{{ $booking->order_id }}')"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 21V3a1 1 0 011-1h5.5a1 1 0 011 1v2m0 0v14m0-14l7 2v14l-7-2">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h1"></path>
                                        </svg>
                                        {{ __('ui.check_in') }}
                                    </button>

                                    <!-- Backdrop Modal -->
                                    <div class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 transition-opacity"
                                        x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-out duration-200"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        aria-hidden="true" x-cloak></div>

                                    <!-- Dialog Modal -->
                                    <div class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                                        role="dialog" aria-modal="true" x-show="isOpen"
                                        x-transition:enter="transition ease-in-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in-out duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 scale-95" x-cloak>

                                        {{-- Check-in modal body: added dark:bg-gray-800 for dark mode --}}
                                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-auto w-full overflow-auto max-h-full flex flex-col text-left max-w-7xl"
                                            @click.outside="closeModal" @keydown.escape.window="closeModal">

                                            <!-- Header Modal - dark mode uses darker bg for readability -->
                                            <div
                                                class="checkin-modal-header px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-green-100">
                                                <div class="flex justify-between items-center">
                                                    <div class="font-bold text-xl text-gray-800 dark:text-white">{{ __('ui.process_checkin') }}</div>
                                                    <button type="button"
                                                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                                        @click="closeModal">
                                                        <div class="sr-only">{{ __('ui.close') }}</div>
                                                        <svg class="w-6 h-6 fill-current">
                                                            <path
                                                                d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <p class="text-sm text-gray-600 dark:text-gray-200 mt-1">{{ __('ui.review_complete_checkin') }}</p>
                                                <p class="text-lg font-bold text-gray-800 dark:text-green-300 mt-1"
                                                    x-text="currentDateTime">
                                                </p>
                                            </div>

                                            <!-- Konten Modal -->
                                            <div class="flex-1 overflow-y-auto px-6 py-6">
                                                <!-- Konten Check-in - Diatur dalam alur logis -->
                                                <div class="space-y-8">
                                                    <!-- Bagian 1: Detail Pemesanan -->
                                                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
                                                        <h3
                                                            class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                                                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                            </svg>
                                                            {{ __('ui.booking_details') }}
                                                        </h3>

                                                        {{-- 3-column layout grouped by relevancy: Booking | Stay | Payment --}}
                                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                            {{-- Column 1: Booking Info --}}
                                                            <div class="space-y-3">
                                                                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Booking</h4>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.order_id') }}</span>
                                                                    <p class="text-sm font-mono font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.order_id"></p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.guest_name') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.guest_name"></p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.property') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.property_name"></p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.room') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.room_name"></p>
                                                                </div>
                                                            </div>

                                                            {{-- Column 2: Stay Details --}}
                                                            <div class="space-y-3">
                                                                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Stay</h4>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.check_in_date') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.check_in"></p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.check_out_date') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.check_out"></p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.duration') }}</span>
                                                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="bookingDetails.duration"></p>
                                                                </div>
                                                            </div>

                                                            {{-- Column 3: Payment --}}
                                                            <div class="space-y-3">
                                                                <h4 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Payment</h4>
                                                                <div class="flex justify-between">
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Total Booking</span>
                                                                    <span class="text-sm text-gray-800 dark:text-gray-200" x-text="bookingDetails.total_booking"></span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Deposit</span>
                                                                    <span class="text-sm text-gray-800 dark:text-gray-200" x-text="bookingDetails.deposit"></span>
                                                                </div>
                                                                <div class="flex justify-between">
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Service Fee</span>
                                                                    <span class="text-sm text-gray-800 dark:text-gray-200" x-text="bookingDetails.service_fee"></span>
                                                                </div>
                                                                <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-600">
                                                                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('ui.total_payment') }}</span>
                                                                    <span class="text-sm font-bold text-green-600 dark:text-green-400" x-text="bookingDetails.total_payment"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Bagian 2: Profil Tamu dan Unggah Identifikasi Berdampingan -->
                                                    <div class="grid grid-cols-1 gap-8" :class="docRequired ? 'lg:grid-cols-2' : 'lg:grid-cols-1'">
                                                        <!-- Profil Tamu -->
                                                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600">
                                                            <h3
                                                                class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                                                                <svg class="w-5 h-5 mr-2 text-purple-600"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                </svg>
                                                                {{ __('ui.guest_profile') }}
                                                            </h3>

                                                            <div class="space-y-4">
                                                                <!-- Foto Profil -->
                                                                <div
                                                                    class="border-2 border-dashed border-gray-300 rounded-lg p-4 bg-gray-50">
                                                                    <template x-if="profilePhotoUrl">
                                                                        <div class="w-full">
                                                                            <img :src="profilePhotoUrl"
                                                                                alt="{{ __('ui.profile_photo') }}"
                                                                                class="w-full h-48 object-cover rounded-lg">
                                                                            <span
                                                                                class="mt-2 text-sm text-gray-600 block text-center"
                                                                                x-text="bookingDetails.guest_name"></span>
                                                                        </div>
                                                                    </template>
                                                                    <template x-if="!profilePhotoUrl">
                                                                        <div class="flex flex-col items-center">
                                                                            <div
                                                                                class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="h-12 w-12 text-gray-400"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke="currentColor">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                                                </svg>
                                                                            </div>
                                                                            <span class="mt-2 text-sm text-gray-600"
                                                                                x-text="bookingDetails.guest_name"></span>
                                                                            <span
                                                                                class="text-xs text-red-500 mt-1">{{ __('ui.account_not_verified') }}</span>
                                                                        </div>
                                                                    </template>
                                                                </div>

                                                                <!-- Informasi Kontak -->
                                                                <div>
                                                                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                                                                        {{ __('ui.contact_information') }}</h4>
                                                                    <div class="space-y-3">
                                                                        <!-- Input Nama -->
                                                                        <div>
                                                                            <label for="guestName"
                                                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.full_name') }}</label>
                                                                            <input type="text" id="guestName"
                                                                                x-model="guestContact.name"
                                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                                                placeholder="{{ __('ui.enter_full_name') }}"
                                                                                required>
                                                                        </div>

                                                                        <!-- Input Email -->
                                                                        <div>
                                                                            <label for="guestEmail"
                                                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.email') }}</label>
                                                                            <input type="email" id="guestEmail"
                                                                                x-model="guestContact.email"
                                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                                                placeholder="{{ __('ui.enter_email') }}"
                                                                                required>
                                                                        </div>

                                                                        <!-- Input Telepon -->
                                                                        <div>
                                                                            <label for="guestPhone"
                                                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.phone_number') }}</label>
                                                                            <input type="tel" id="guestPhone"
                                                                                x-model="guestContact.phone"
                                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                                                placeholder="{{ __('ui.enter_phone_number') }}"
                                                                                required>
                                                                        </div>

                                                                        <!-- Input NIK -->
                                                                        <div>
                                                                            <label for="guestNik"
                                                                                class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.nik') }}</label>
                                                                            <input type="text" id="guestNik"
                                                                                x-model="guestContact.nik"
                                                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                                                placeholder="{{ __('ui.nik') }}"
                                                                                required>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Unggah Identifikasi -->
                                                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600" x-show="docRequired">
                                                            <h3
                                                                class="font-semibold text-lg text-gray-800 mb-4 flex items-center">
                                                                <svg class="w-5 h-5 mr-2 text-green-600"
                                                                    fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                </svg>
                                                                {{ __('ui.upload_identification') }}
                                                            </h3>

                                                            <div class="space-y-4">
                                                                <!-- Pemilihan Jenis Dokumen -->
                                                                <div>
                                                                    <label for="documentType"
                                                                        class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.document_type') }}</label>
                                                                    <select id="documentType"
                                                                        x-model="selectedDocType"
                                                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md">
                                                                        <option value="ktp">KTP</option>
                                                                        <option value="passport">{{ __('ui.passport') }}</option>
                                                                        <option value="sim">SIM</option>
                                                                        <option value="other">{{ __('ui.other_id') }}</option>
                                                                    </select>
                                                                </div>

                                                                <!-- Tab untuk memilih metode unggah -->
                                                                <div>
                                                                    <div class="flex border-b border-gray-200">
                                                                        <button type="button"
                                                                            @click="handleUploadMethodChange('file')"
                                                                            :class="uploadMethod === 'file' ?
                                                                                'border-green-500 text-green-600' :
                                                                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                                                            class="flex-1 py-2 px-4 text-center border-b-2 font-medium text-sm">
                                                                            {{ __('ui.upload_file') }}
                                                                        </button>
                                                                        <button type="button"
                                                                            @click="handleUploadMethodChange('camera')"
                                                                            :class="uploadMethod === 'camera' ?
                                                                                'border-green-500 text-green-600' :
                                                                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                                                            class="flex-1 py-2 px-4 text-center border-b-2 font-medium text-sm">
                                                                            {{ __('ui.webcam') }}
                                                                        </button>
                                                                    </div>

                                                                    <!-- Area Unggah File -->
                                                                    <div x-show="uploadMethod === 'file'"
                                                                        class="mt-4">
                                                                        <div x-show="!docPreview"
                                                                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors duration-200 cursor-pointer"
                                                                            @click="$refs.docInput.click()"
                                                                            @drop.prevent="handleDocDrop($event)"
                                                                            @dragover.prevent @dragenter.prevent
                                                                            :class="{ 'border-green-400 bg-green-50': isDragging }"
                                                                            role="button" tabindex="0">
                                                                            <input type="file" id="document"
                                                                                name="document" accept="image/*"
                                                                                class="hidden" x-ref="docInput"
                                                                                @change="handleDocUpload($event)">

                                                                            <div class="space-y-2">
                                                                                <svg class="w-12 h-12 mx-auto text-gray-400"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                                    </path>
                                                                                </svg>
                                                                                <p class="text-sm text-gray-600">
                                                                                    <span
                                                                                        class="font-medium text-green-600">{{ __('ui.click_to_upload') }}</span> {{ __('ui.or_drag_and_drop') }}
                                                                                </p>
                                                                                <p class="text-xs text-gray-500">{{ __('ui.file_format_limit') }}</p>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                    <!-- Area Webcam -->
                                                                    <div x-show="uploadMethod === 'camera'"
                                                                        class="mt-4">
                                                                        <div
                                                                            class="border-2 border-dashed border-gray-300 rounded-lg p-4">
                                                                            <!-- Preview Webcam -->
                                                                            <div x-show="!isCapturing && !webcamPhoto"
                                                                                class="text-center p-8 bg-gray-50 rounded-lg">
                                                                                <svg class="w-12 h-12 mx-auto text-gray-400"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                                </svg>
                                                                                <p class="text-sm text-gray-600 mt-2">
                                                                                    {{ __('ui.click_to_take_photo') }}
                                                                                </p>
                                                                            </div>

                                                                            <!-- Video Webcam -->
                                                                            <div x-show="isCapturing"
                                                                                class="relative">
                                                                                <video x-ref="webcamVideo"
                                                                                    class="w-full h-auto rounded-lg"
                                                                                    autoplay playsinline>
                                                                                </video>
                                                                                <div
                                                                                    class="absolute bottom-4 left-0 right-0 flex justify-center">
                                                                                    <button type="button"
                                                                                        @click="capturePhoto"
                                                                                        class="bg-white rounded-full p-3 shadow-lg hover:bg-gray-100 transition-colors">
                                                                                        <svg class="w-6 h-6 text-gray-700"
                                                                                            fill="none"
                                                                                            stroke="currentColor"
                                                                                            viewBox="0 0 24 24">
                                                                                            <path
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                                                            <path
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Hasil Foto -->
                                                                            <div x-show="webcamPhoto && !isCapturing"
                                                                                class="relative">
                                                                                <img :src="webcamPhoto"
                                                                                    alt="{{ __('ui.webcam_photo') }}"
                                                                                    class="w-full h-auto rounded-lg">
                                                                                <div
                                                                                    class="absolute top-2 right-2 flex space-x-2">
                                                                                    <button type="button"
                                                                                        @click="retakePhoto"
                                                                                        class="bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors">
                                                                                        <svg class="w-4 h-4 text-gray-700"
                                                                                            fill="none"
                                                                                            stroke="currentColor"
                                                                                            viewBox="0 0 24 24">
                                                                                            <path
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                                        </svg>
                                                                                    </button>
                                                                                    <button type="button"
                                                                                        @click="useWebcamPhoto"
                                                                                        class="bg-green-500 rounded-full p-2 shadow-lg hover:bg-green-600 transition-colors">
                                                                                        <svg class="w-4 h-4 text-white"
                                                                                            fill="none"
                                                                                            stroke="currentColor"
                                                                                            viewBox="0 0 24 24">
                                                                                            <path
                                                                                                stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M5 13l4 4L19 7" />
                                                                                        </svg>
                                                                                    </button>
                                                                                </div>
                                                                            </div>

                                                                            <!-- Tombol Mulai Webcam -->
                                                                            <div x-show="!isCapturing && !webcamPhoto"
                                                                                class="mt-4">
                                                                                <button type="button"
                                                                                    @click="startWebcam"
                                                                                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                                                                    <svg class="w-5 h-5 inline mr-2"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                                                    </svg>
                                                                                    {{ __('ui.open_camera') }}
                                                                                </button>
                                                                            </div>

                                                                            <!-- Tombol Tutup Webcam -->
                                                                            <div x-show="isCapturing" class="mt-4">
                                                                                <button type="button"
                                                                                    @click="stopWebcam"
                                                                                    class="w-full bg-red-600 text-white py-2 px-4 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                                                                    <svg class="w-5 h-5 inline mr-2"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M6 18L18 6M6 6l12 12" />
                                                                                    </svg>
                                                                                    {{ __('ui.close_camera') }}
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Pratinjau Dokumen -->
                                                                <div class="mt-4" x-show="docPreview" x-transition>
                                                                    <h4 class="text-sm font-medium text-gray-700 mb-2">
                                                                        {{ __('ui.document_preview') }} (<span
                                                                            x-text="selectedDocType.toUpperCase()"></span>):
                                                                    </h4>
                                                                    <div
                                                                        class="border border-gray-200 rounded-lg p-2 bg-white">
                                                                        <template x-if="docPreviewType === 'image'">
                                                                            <img :src="docPreview"
                                                                                alt="{{ __('ui.document_preview') }}"
                                                                                class="w-full h-auto max-h-48 object-contain">
                                                                        </template>
                                                                        <template x-if="docPreviewType === 'pdf'">
                                                                            <div class="bg-gray-100 p-4 text-center">
                                                                                <svg class="w-12 h-12 mx-auto text-red-500"
                                                                                    fill="none"
                                                                                    stroke="currentColor"
                                                                                    viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2"
                                                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                                    </path>
                                                                                </svg>
                                                                                <p class="text-sm text-gray-600 mt-2">
                                                                                    {{ __('ui.pdf_document') }}</p>
                                                                            </div>
                                                                        </template>
                                                                        <div
                                                                            class="mt-2 flex justify-between items-center">
                                                                            <span class="text-xs text-gray-500">{{ __('ui.document_uploaded') }}</span>
                                                                            <button type="button" @click="removeDoc"
                                                                                class="text-red-500 hover:text-red-700 text-xs font-medium">
                                                                                {{ __('ui.remove') }}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                                <!-- Pesan Validasi -->
                                                                <div class="mt-3" x-show="!docPreview">
                                                                    <p class="text-sm text-red-600">
                                                                        <span class="font-medium">{{ __('ui.note') }}:</span>
                                                                        {{ __('ui.id_required_for_checkin') }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Informasi Dokumen Sudah Tersimpan -->
                                                        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg border border-gray-200 dark:border-gray-600" x-show="!docRequired">
                                                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                                                                <div class="flex items-start">
                                                                    <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                    <div>
                                                                        <p class="text-base font-semibold text-blue-800 mb-1">
                                                                            {{ __('ui.id_document_saved') }}
                                                                        </p>
                                                                        <p class="text-sm text-blue-700">
                                                                            {{ __('ui.continue_checkin_without_doc') }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Footer Modal -->
                                                <div
                                                    class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between">
                                                    <button type="button" @click="closeModal"
                                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        {{ __('ui.cancel') }}
                                                    </button>
                                                    <button type="button" @click="submitCheckIn"
                                                        :disabled="(docRequired && !docPreview && !profilePhotoUrl) || !guestContact.name || !guestContact.email || !guestContact.phone || !guestContact.nik"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                        {{ __('ui.complete_checkin') }}
                                                    </button>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif (!is_null($booking->check_in_at) && is_null($booking->check_out_at))
                            {{-- Sudah check-in, belum check-out --}}
                            <div class="flex flex-col items-center space-y-2">
                                <span class="text-yellow-600 font-semibold">{{ __('ui.currently_staying_label') }}</span>

                                <div class="flex flex-col items-center space-y-2">
                                    <a href="{{ route('newReserv.checkin.invoice', $booking->order_id) }}"
                                        target="_blank"
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none">
                                        {{ __('ui.view_invoice') }}
                                    </a>

                                    @if ($booking->is_printed < 2)
                                        <a href="{{ route('newReserv.checkin.regist', $booking->order_id) }}"
                                            target="_blank"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-amber-600 rounded hover:bg-amber-700 focus:outline-none">
                                            {{ __('ui.print_regist_form') }}
                                            <span class="ml-1 px-1 py-0.5 text-[10px] bg-amber-800 rounded">{{ $booking->is_printed }}/2</span>
                                        </a>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-gray-400 bg-gray-200 rounded cursor-not-allowed"
                                            title="{{ __('ui.print_limit_reached') }}">
                                            {{ __('ui.print_regist_form') }}
                                            <span class="ml-1 px-1 py-0.5 text-[10px] bg-gray-300 text-gray-500 rounded">2/2</span>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @elseif (!is_null($booking->check_in_at) && !is_null($booking->check_out_at))
                            {{-- Sudah check-in dan check-out --}}
                            <span class="text-green-600">{{ __('ui.checked_out') }}</span>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.no_new_reservations') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
