<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Check-in
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Check-out
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Order ID
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Name
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Property/Room
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
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
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            Not checked in
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
                        <div class="text-sm text-gray-500 italic">Not checked out</div>
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
                            <div
                                class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses['Checked-In'] }}">
                                Check-in at: {{ $booking->check_in_at->format('Y-m-d H:i') }}
                            </div>
                        @elseif ($booking->status === 'Checked-Out' && $booking->check_out_at)
                            <div
                                class="inline-flex items-center mt-2 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses['Checked-Out'] }}">
                                Check-out at: {{ $booking->check_out_at->format('Y-m-d H:i') }}
                            </div>
                        @elseif ($booking->status === 'Canceled')
                            <div class="mt-2 w-full px-2 text-center">
                                @if ($booking->reason)
                                    <div class="text-xs text-gray-700 font-medium">
                                        <span class="font-semibold">Reason:</span>
                                        <span class="inline-block">{{ $booking->reason }}</span>
                                    </div>
                                @endif

                                @if ($booking->description)
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">Description:</span>
                                        <span class="inline-block">{{ $booking->description }}</span>
                                    </div>
                                @endif

                                @if ($booking->refund)
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">Refund Status:</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full inline-block 
                {{ $booking->refund->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($booking->refund->status) }}
                                        </span>
                                    </div>

                                    @if ($booking->refund->amount)
                                        <div class="text-xs text-gray-600 mt-1">
                                            <span class="font-semibold">Refund Amount:</span>
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
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.allbookings_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
