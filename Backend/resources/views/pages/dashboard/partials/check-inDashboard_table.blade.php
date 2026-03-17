<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Check-In</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Check-Out</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Booking ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Guest</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Room</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status</th>
            {{-- <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Action</th> --}}
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($bookings as $booking)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->transaction->check_in)
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">Not Checked-In Yet</div>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($booking->transaction->check_out)
                        <div class="text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($booking->transaction->check_out)->format('d M Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($booking->transaction->check_out)->format('H:i') }}
                        </div>
                    @else
                        <div class="text-sm text-gray-500 italic">Not Checked-Out Yet</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <div class="text-sm font-medium text-indigo-600">{{ $booking->order_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $booking->transaction->user_name ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $booking->property->name ?? 'N/A' }}</div>
                    ({{ $booking->room->name ?? '-' }})
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
                    <span
                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$booking->status] ?? $booking->status }}
                    </span>
                </td>

                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <a href="" class="text-blue-600 hover:text-blue-900">Details</a>
                </td> --}}
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No
                    bookings found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
