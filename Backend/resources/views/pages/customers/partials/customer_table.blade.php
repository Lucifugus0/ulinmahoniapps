<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.customer') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.contact_info') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.registration') }}
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.current_status') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.total_bookings') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.parking_info') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.last_booking') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.total_spent') }}
                </th>
                <th scope="col"
                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                    {{ __('ui.action') }}
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($customers as $customer)
                @php
                    $colors = [
                        'bg-red-500/35',
                        'bg-blue-500/35',
                        'bg-green-500/35',
                        'bg-purple-500/35',
                        'bg-pink-500/35',
                        'bg-yellow-500/35',
                        'bg-indigo-500/35',
                        'bg-teal-500/35',
                        'bg-orange-500/35',
                    ];
                    $bgColor = $colors[array_rand($colors)];
                    $initials = strtoupper(substr($customer->username ?? 'U', 0, 1));
                    if ($customer->username && strpos($customer->username, ' ') !== false) {
                        $nameParts = explode(' ', $customer->username);
                        $initials = strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <!-- Customer Name with Avatar -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full {{ $bgColor }} text-white font-semibold">
                                {{ $initials }}
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $customer->username ?? __('ui.unknown') }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Contact Info -->
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $customer->email ?? '-' }}</div>
                        <div class="text-sm text-gray-500">{{ $customer->phone ?? '-' }}</div>
                        <div class="text-sm text-gray-400">{{ __('ui.nik') }}: {{ $customer->nik ?? '-' }}</div>
                    </td>

                    <!-- Registration Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if ($customer->registration_status === 'registered')
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('ui.registered') }}
                            </span>
                        @else
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('ui.guest') }}
                            </span>
                        @endif
                    </td>

                    <!-- Current Status -->
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $now = now();
                            $checkIn = $customer->last_check_in ? \Carbon\Carbon::parse($customer->last_check_in) : null;
                            $checkOut = $customer->last_check_out ? \Carbon\Carbon::parse($customer->last_check_out) : null;
                            $bookingStatus = $customer->current_booking_status;
                        @endphp

                        @if ($bookingStatus === 'checked-in' || ($checkIn && $checkOut && $now->between($checkIn, $checkOut)))
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                {{ __('ui.currently_checking_in') }}
                            </span>
                        @elseif ($bookingStatus === 'checked-out' || $bookingStatus === 'completed')
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('ui.completed') }}
                            </span>
                        @elseif ($bookingStatus === 'confirmed' || $bookingStatus === 'pending')
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('ui.upcoming') }}
                            </span>
                        @elseif ($bookingStatus === 'cancelled')
                            <span
                                class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                {{ __('ui.cancelled') }}
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">{{ __('ui.no_booking_yet') }}</span>
                        @endif
                    </td>

                    <!-- Total Bookings -->
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span
                            class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold {{ $customer->total_bookings > 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-500' }}">
                            {{ $customer->total_bookings ?? 0 }}
                        </span>
                    </td>

                    <!-- Parking Info -->
                    <td class="px-6 py-4">
                        @if ($customer->parking_info)
                            <div class="text-sm text-gray-900">
                                @php
                                    $parkings = explode(', ', $customer->parking_info);
                                @endphp
                                @foreach ($parkings as $parking)
                                    <div class="flex items-center mb-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-indigo-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                        <span class="text-xs">{{ $parking }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-sm">{{ __('ui.no_parking') }}</span>
                        @endif
                    </td>

                    <!-- Last Booking Date -->
                    <td class="px-6 py-4 text-sm text-gray-900">
                        @if ($customer->last_booking_date)
                            <div class="space-y-1">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ \Carbon\Carbon::parse($customer->last_booking_date)->format('M d, Y') }}
                                </div>
                                @if ($customer->last_property_name)
                                    <div class="text-xs text-gray-600">
                                        <span class="font-medium">{{ $customer->last_property_name }}</span>
                                        @if ($customer->last_room_name)
                                            <span class="text-gray-400">-</span>
                                            <span>{{ $customer->last_room_name }}</span>
                                            @if ($customer->last_room_number)
                                                <span class="text-gray-400 mx-1">•</span>
                                                <span class="font-semibold text-indigo-600">{{ __('ui.no') }}. {{ $customer->last_room_number }}</span>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>

                    <!-- Total Spent -->
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                        <span class="font-semibold text-green-600">
                            Rp {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <div class="flex items-center justify-center gap-2">
                            <button
                                onclick="window.dispatchEvent(new CustomEvent('open-customer-modal', { detail: { identifier: '{{ $customer->registration_status === 'registered' ? $customer->id : $customer->email }}', type: '{{ $customer->registration_status }}', name: '{{ $customer->username }}' } }))"
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                {{ __('ui.detail') }}
                            </button>

                            @if ($customer->registration_status === 'registered' && $customer->id)
                                <button
                                    onclick="openEditCustomerModal({{ json_encode([
                                        'id'           => $customer->id,
                                        'first_name'   => $customer->first_name ?? '',
                                        'last_name'    => $customer->last_name ?? '',
                                        'username'     => $customer->username ?? '',
                                        'email'        => $customer->email ?? '',
                                        'phone_number' => $customer->phone ?? '',
                                        'nik'          => $customer->nik ?? '',
                                    ]) }})"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    {{ __('ui.edit') }}
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mx-auto mb-4"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-gray-600 font-medium text-lg">{{ __('ui.no_customers_found') }}</p>
                        <p class="text-gray-500 text-sm mt-1">{{ __('ui.no_customers_found_desc') }}</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if ($customers->hasPages())
    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
        {{ $customers->links() }}
    </div>
@endif
