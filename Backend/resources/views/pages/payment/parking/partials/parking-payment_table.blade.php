<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.payment_date') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.booking_id') ?? __('ui.order_id') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.guest_name') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.amount') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_period') }}
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($parkingTransactions as $trx)
            @php
                /* Resolve room number for the Property column. The booking row sits in
                   t_booking keyed on order_id; we pull the active row + its room name in
                   one inline query. N+1 is acceptable on a paginated 25-row admin list. */
                $bookingRow = \App\Models\Booking::where('order_id', $trx->order_id)
                    ->where('status', 1)
                    ->whereNull('check_out_at')
                    ->with('room:idrec,name,no')
                    ->orderByDesc('idrec')
                    ->first()
                    ?? \App\Models\Booking::where('order_id', $trx->order_id)
                        ->with('room:idrec,name,no')
                        ->orderByDesc('idrec')
                        ->first();
                $roomLabel = $bookingRow && $bookingRow->room
                    ? trim(($bookingRow->room->name ?? '') . ' ' . ($bookingRow->room->no ? '#' . $bookingRow->room->no : ''))
                    : '';
            @endphp
            <tr>
                {{-- 1. Entry Date — single line, no checkin date row underneath --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="text-sm text-gray-500">
                        {{ $trx->transaction_date->format('Y-m-d') }}
                    </div>
                </td>

                {{-- 2. Booking ID — order_id on row 1, stay period (check-in → check-out) on row 2 --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $trx->order_id }}</div>
                    @if($trx->transaction && $trx->transaction->check_in)
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ \Carbon\Carbon::parse($trx->transaction->check_in)->format('d M Y') }}
                            @if($trx->transaction->check_out)
                                → {{ \Carbon\Carbon::parse($trx->transaction->check_out)->format('d M Y') }}
                            @endif
                        </div>
                    @endif
                </td>

                {{-- 3. Guest Name (unchanged) --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $trx->user_name ?? '-' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $trx->user_phone ?? '' }}</div>
                            <div class="text-xs text-gray-400 font-mono">{{ $trx->vehicle_plate ?? '-' }}</div>
                        </div>
                    </div>
                </td>

                {{-- 4. Property — name on row 1, room on row 2 --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div>{{ $trx->property->name ?? '-' }}</div>
                    @if($roomLabel !== '')
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ $roomLabel }}</div>
                    @endif
                </td>

                {{-- 5. Amount (unchanged) --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp{{ number_format($trx->fee_amount, 0, ',', '.') }}
                    </div>
                </td>

                {{-- Parking Period — 3-row stack:
                     row1: "Entry Date: <paid_at>" on the left, parking-type badge on the right
                     row2: parking period date range
                     row3: duration --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                    @php
                        $typeColors = [
                            'car' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'motorcycle' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                        ];
                        $colorClass = $typeColors[$trx->parking_type] ?? 'bg-gray-100 text-gray-800';
                        $typeLabel = $trx->parking_type === 'car' ? __('ui.car') : __('ui.motorcycle');
                        // paid_at is the canonical "when was this row entered into the system" timestamp.
                        // Falls back to created_at for any legacy row that somehow lacks paid_at.
                        $entryStamp = $trx->paid_at ?? $trx->created_at;
                    @endphp
                    <div class="flex flex-col gap-1 min-w-[12rem]">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('ui.entry_date') }}: {{ $entryStamp ? $entryStamp->format('Y-m-d') : '—' }}
                            </span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                {{ $typeLabel }}
                            </span>
                        </div>
                        <div class="text-xs">{{ $trx->rent_period_label }}</div>
                        @if($trx->parking_duration)
                            <div class="text-xs text-gray-400">{{ $trx->parking_duration }} {{ $trx->parking_duration > 1 ? 'months' : 'month' }}</div>
                        @endif
                    </div>
                </td>

                {{-- 8. Status — now also carries the Verified badge + verifier name (moved from Action) --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'waiting' => 'bg-orange-100 text-orange-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-200 text-red-900',
                            'canceled' => 'bg-pink-100 text-pink-800',
                            'expired' => 'bg-gray-300 text-gray-800',
                            'failed' => 'bg-rose-100 text-rose-800',
                        ];
                        $statusColor = $statusColors[$trx->transaction_status] ?? 'bg-gray-100 text-gray-800';
                        $hasTooltip = $trx->transaction_status === 'rejected' && !empty($trx->notes);
                    @endphp
                    <div class="relative inline-block group">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }} {{ $hasTooltip ? 'cursor-help' : '' }}">
                            {{ ucfirst($trx->transaction_status) }}
                        </span>
                        @if($hasTooltip)
                            <div class="absolute z-10 w-64 p-2 mt-1 text-xs text-gray-600 bg-white border border-gray-300 rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform -translate-x-1/2 left-1/2">
                                {{ $trx->notes }}
                            </div>
                        @endif
                    </div>
                    {{-- Paid rows show Verified pill + verifier (moved here from the Action column) --}}
                    @if($trx->transaction_status === 'paid')
                        <div class="mt-1.5 flex flex-col items-center gap-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('ui.verified') }}
                            </span>
                            @if($trx->verifiedBy)
                                <div class="text-xs text-gray-500">
                                    {{ __('ui.by') }}: {{ $trx->verifiedBy->username ?? '-' }}
                                </div>
                            @endif
                        </div>
                    @endif
                </td>

                {{-- 9. Action — Konfirmasi for waiting; eye-icon-only View Proof for paid --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                    @if($trx->transaction_status === 'waiting')
                        <button type="button"
                            class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 px-4 py-2 rounded-lg transition-all duration-200 ease-in-out shadow-sm hover:shadow-md"
                            onclick="openParkingProofModal({{ $trx->idrec }}, '{{ $trx->order_id }}')"
                            title="{{ __('ui.confirm') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            <span class="text-sm font-semibold">{{ __('ui.confirm') }}</span>
                        </button>
                    @elseif($trx->transaction_status === 'pending')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('ui.pending') }}
                        </span>
                    @elseif($trx->transaction_status === 'paid' && $trx->images->count() > 0)
                        {{-- Eye-icon-only View Proof button --}}
                        <button onclick="openParkingProofModal({{ $trx->idrec }}, '{{ $trx->order_id }}', true)"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-lg text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/30 transition-colors"
                            title="{{ __('ui.view_proof') }}"
                            aria-label="{{ __('ui.view_proof') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
