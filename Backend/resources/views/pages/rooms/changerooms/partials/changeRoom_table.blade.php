<table class="min-w-full divide-y divide-gray-200">
    <thead>
        <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.guest_name') }}</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.property') }}</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.room') }}</th>
            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('ui.status') }}</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($bookings as $booking)
            <tr class="cursor-pointer hover:bg-blue-50/50 transition-colors duration-150 booking-card"
                data-booking="{{ json_encode([
                    'booking_id' => $booking->idrec,
                    'room_number' => $booking->room->name ?? 'N/A',
                    'room_no' => $booking->room->no ?? 'N/A',
                    'check_in' => $booking->transaction?->check_in ?? $booking->check_in_at,
                    'check_out' => $booking->transaction?->check_out ?? $booking->check_out_at,
                    'rate' => $booking->room->price ?? 'N/A',
                    'guest_name' => $booking->transaction->user_name ?? 'N/A',
                    'guest_email' => $booking->transaction->user_email ?? '-',
                    'guest_phone' => $booking->transaction->user_phone_number ?? '-',
                    'order_id' => $booking->order_id,
                    'propertyName' => $booking->property->name ?? 'N/A',
                    'property_id' => $booking->property->idrec ?? 'N/A',
                    'room_id' => $booking->room->idrec ?? 'N/A',
                    'is_checked_in' => $booking->check_in_at ? true : false,
                    'booking_status' => $booking->check_in_at ? 'Checked In' : 'Pending',
                    'transfer_count' => $booking->transfer_count ?? 0,
                    'has_been_transferred' => $booking->previous_booking_id ? true : false,
                ]) }}"
                onclick="selectBooking(this)">
                {{-- Guest --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs flex-shrink-0">
                            {{ strtoupper(substr($booking->transaction->user_name ?? '?', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $booking->transaction->user_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 font-mono">{{ $booking->order_id }}</div>
                        </div>
                    </div>
                </td>
                {{-- Property --}}
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900">{{ $booking->property->name ?? '-' }}</div>
                </td>
                {{-- Room --}}
                <td class="px-4 py-3">
                    <div class="text-sm text-gray-900">{{ $booking->room->name ?? '-' }}</div>
                    <div class="text-xs text-gray-500">No. {{ $booking->room->no ?? '' }}</div>
                </td>
                {{-- Status --}}
                <td class="px-4 py-3 text-center">
                    @if ($booking->check_in_at)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1"></span>Checked In
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 mr-1"></span>Pending
                        </span>
                    @endif
                    @if ($booking->previous_booking_id)
                        <div class="mt-1">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                {{ $booking->transfer_count ?? 0 }}x Pindah
                            </span>
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="px-4 py-10 text-center text-gray-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">{{ __('ui.changeroom_no_bookings') }}</p>
                    @if(request('search'))
                        <p class="text-xs mt-1">{{ __('ui.changeroom_try_other_keyword') }}</p>
                    @endif
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
