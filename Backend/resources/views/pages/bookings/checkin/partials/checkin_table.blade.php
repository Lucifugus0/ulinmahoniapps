<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Check-in</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Check-out</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Booking ID</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Name</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Property/Room</th>
            @if ($showStatus ?? true)
                <th scope="col"
                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Status</th>
            @endif
            @if ($showActions ?? true)
                <th scope="col"
                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Aksi</th>
            @endif
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($checkOuts as $booking)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->transaction && $booking->transaction->check_in)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('Y M d') }}
                            </span>
                            <span class="text-xs text-gray-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('H:i') }}
                            </span>
                        </div>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">
                            {{ __('ui.checkin_not_checked_in') }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    @if ($booking->transaction->check_out)
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $booking->transaction->check_out->format('Y M d') }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $booking->transaction->check_out->format('H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">{{ __('ui.not_checked_out') }}</div>
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
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ ($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '') ?: ($booking->transaction->user_name ?? 'N/A') }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>
                @if ($showStatus ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if ($booking->check_out_at)
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:text-gray-100">
                                {{ __('ui.checkout_already_checked_out') }}
                            </span>
                        @else
                            <span
                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('ui.occupied') }}
                            </span>
                        @endif
                    </td>
                @endif
                @if ($showActions ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center" x-data="{ open: false }">
                        @if (is_null($booking->check_out_at))
                            @include('pages.bookings.checkin.partials.checkout_modal_button', ['booking' => $booking])
                        @elseif (!is_null($booking->check_in_at) && !is_null($booking->check_out_at))
                            <div class="flex flex-col items-center space-y-2">
                                <span class="text-green-600">{{ __('ui.checkout_already_checked_out') }}</span>

                                <a href="{{ route('newReserv.checkin.invoice', $booking->order_id) }}"
                                    target="_blank"
                                    class="iinline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none">
                                    {{ __('ui.checkout_view_invoice') }}
                                </a>
                            </div>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                    Tidak ada pemesanan baru.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
