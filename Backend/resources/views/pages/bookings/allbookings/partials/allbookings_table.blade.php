<!-- All Bookings table with server-side sorting -->
<!-- Sortable columns: Check-in, Check-out, Booking ID, Name, Property -->
<!-- Clicking a header triggers fetchFilteredBookings with sort params -->
<table class="min-w-full divide-y divide-gray-200" id="allbookings-sortable-table">
    <thead class="bg-gray-50">
        <tr>
            @php
                /* Get current sort state from request for icon display */
                $currentSort = request('sort_by', 'checkin');
                $currentDir = request('sort_dir', 'asc');
            @endphp
            <!-- Sortable Check-in header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('checkin')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_checkin') }}
                    @if($currentSort === 'checkin')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Check-out header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('checkout')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_checkout') }}
                    @if($currentSort === 'checkout')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Booking ID header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('orderid')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_booking_id') }}
                    @if($currentSort === 'orderid')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Name header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('name')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_name') }}
                    @if($currentSort === 'name')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Property header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('property')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_property_room') }}
                    @if($currentSort === 'property')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Status header (not sortable, translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_status') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($bookings as $booking)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->transaction && $booking->transaction->check_in)
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('Y M d') }}
                            </span>
                            <span class="text-xs text-gray-500 mt-0.5">
                                {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('H:i') }}
                            </span>
                        </div>
                    @else
                        <!-- "Not checked in" label translated via ui lang file -->
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ __('ui.allbookings_not_checked_in') }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    @if ($booking->transaction && $booking->transaction->check_out)
                        <div class="text-sm font-medium text-gray-900">
                            {{ $booking->transaction->check_out->format('Y M d') }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $booking->transaction->check_out->format('H:i') }}
                        </div>
                    @else
                        <!-- "Not checked out" label translated via ui lang file -->
                        <div class="text-sm text-gray-500 italic">{{ __('ui.allbookings_not_checked_out') }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <div class="text-sm font-medium text-indigo-600">{{ $booking->order_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $booking->transaction->user_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    @php
                        $statusClasses = [
                            'Waiting For Payment' => 'bg-yellow-100 text-yellow-800',
                            'Waiting For Confirmation Payment' => 'bg-orange-100 text-orange-800',
                            'Waiting For Check-In' => 'bg-cyan-100 text-cyan-800',
                            'Checked-In' => 'bg-green-100 text-green-800',
                            'Checked-Out' => 'bg-indigo-100 text-indigo-800',
                            'Canceled' => 'bg-red-100 text-red-800',
                            'Expired' => 'bg-pink-100 text-pink-800',
                            'Unknown' => 'bg-slate-100 text-slate-800',
                        ];

                        $statusLabels = [
                            'Waiting For Payment' => 'Pending Payment',
                            'Waiting For Confirmation Payment' => 'Confirming',
                            'Waiting For Check-In' => 'Waiting Check-In',
                            'Checked-In' => 'Checked-In',
                            'Checked-Out' => 'Checked-Out',
                            'Canceled' => 'Canceled',
                            'Expired' => 'Expired',
                            'Unknown' => 'Unknown',
                        ];
                    @endphp

                    <div class="flex flex-col items-center">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$booking->status] ?? $booking->status }}
                        </span>

                        @if ($booking->status === 'Checked-In' && $booking->check_in_at)
                            <!-- Check-in timestamp label translated via ui lang file -->
                            <div
                                class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses['Checked-In'] }}">
                                {{ __('ui.allbookings_checkin_at') }} {{ $booking->check_in_at->format('Y-m-d H:i') }}
                            </div>
                        @elseif ($booking->status === 'Checked-Out' && $booking->check_out_at)
                            <!-- Check-out timestamp label translated via ui lang file -->
                            <div
                                class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses['Checked-Out'] }}">
                                {{ __('ui.allbookings_checkout_at') }} {{ $booking->check_out_at->format('Y-m-d H:i') }}
                            </div>
                        @elseif ($booking->status === 'Canceled')
                            <div class="mt-2 w-full px-2 text-center">
                                @if ($booking->reason)
                                    <!-- Cancellation reason label translated via ui lang file -->
                                    <div class="text-xs text-gray-700 font-medium">
                                        <span class="font-semibold">{{ __('ui.allbookings_reason') }}</span>
                                        <span class="inline-block">{{ $booking->reason }}</span>
                                    </div>
                                @endif

                                @if ($booking->description)
                                    <!-- Cancellation description label translated via ui lang file -->
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">{{ __('ui.allbookings_description') }}</span>
                                        <span class="inline-block">{{ $booking->description }}</span>
                                    </div>
                                @endif

                                @if ($booking->refund)
                                    <!-- Refund status label translated via ui lang file -->
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">{{ __('ui.allbookings_refund_status') }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full inline-block
                {{ $booking->refund->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($booking->refund->status) }}
                                        </span>
                                    </div>

                                    @if ($booking->refund->amount)
                                        <!-- Refund amount label translated via ui lang file -->
                                        <div class="text-xs text-gray-600 mt-1">
                                            <span class="font-semibold">{{ __('ui.allbookings_refund_amount') }}</span>
                                            <span class="inline-block">Rp
                                                {{ number_format($booking->refund->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                    </div>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.allbookings_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
