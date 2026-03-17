<!-- Today's Check-Out table: bookings due today or overdue -->
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_checkin') }}</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_checkout') }}</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_booking_id') }}</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_name') }}</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_property_room') }}</th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_status') }}</th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.action') }}</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($bookings as $booking)
            <tr>
                <!-- Check-in date (actual) -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->check_in_at)
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->check_in_at)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($booking->check_in_at)->format('H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">{{ __('ui.checkout_not_checked_in') }}</div>
                    @endif
                </td>
                <!-- Check-out date (scheduled, from transaction) -->
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->transaction && $booking->transaction->check_out)
                        @php
                            $checkoutDate = \Carbon\Carbon::parse($booking->transaction->check_out);
                            $isOverdue = $checkoutDate->lt(\Carbon\Carbon::today());
                        @endphp
                        <div class="text-sm font-medium {{ $isOverdue ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $checkoutDate->format('d M Y') }}
                        </div>
                        <div class="text-sm {{ $isOverdue ? 'text-red-400' : 'text-gray-500' }}">
                            {{ $checkoutDate->format('H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">-</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <!-- Booking ID uses same text color as user name -->
                    <div class="text-sm font-medium text-gray-900">{{ $booking->order_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <!-- Display first_name + last_name from user model instead of transaction user_name -->
                            <div class="text-sm font-medium text-gray-900">
                                {{ ($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '') ?: ($booking->transaction->user_name ?? 'N/A') }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>
                <!-- Status badge: overdue or due today -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @php
                        $checkoutDate = $booking->transaction ? \Carbon\Carbon::parse($booking->transaction->check_out) : null;
                        $isOverdue = $checkoutDate && $checkoutDate->lt(\Carbon\Carbon::today());
                    @endphp
                    @if ($isOverdue)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            {{ __('ui.overdue') }}
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            {{ __('ui.checkout_today_label') }}
                        </span>
                    @endif
                </td>
                <!-- Action: Check-Out Now button with modal -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    @include('pages.bookings.checkin.partials.checkout_modal_button', ['booking' => $booking])
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.checkout_no_bookings') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
