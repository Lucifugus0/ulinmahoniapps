<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.date') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.order_id') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.guest_name') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}/{{ __('ui.room') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.amount') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.notes') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($depositTransactions as $trx)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="text-sm text-gray-500">
                        {{ $trx->transaction_date->format('Y-m-d') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-indigo-600 dark:text-indigo-400">{{ $trx->order_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div
                            class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $trx->transaction->user_name ?? '-' }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $trx->transaction->user_phone_number ?? '' }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $trx->transaction->user_email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $trx->transaction->property->name ?? '-' }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $trx->transaction->room->name ?? '' }}
                        @if($trx->transaction->room?->no)
                            ({{ $trx->transaction->room->no }})
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp{{ number_format($trx->fee_amount, 0, ',', '.') }}
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                    @if (!empty($trx->notes))
                        <div class="max-w-xs truncate" title="{{ $trx->notes }}">
                            {{ $trx->notes }}
                        </div>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
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
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }} {{ $hasTooltip ? 'cursor-help' : '' }}">
                            {{ ucfirst($trx->transaction_status) }}
                        </span>
                        @if ($hasTooltip)
                            <div
                                class="absolute z-10 w-64 p-2 mt-1 text-xs text-gray-600 bg-white border border-gray-300 rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform -translate-x-1/2 left-1/2">
                                {{ $trx->notes }}
                            </div>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                    @if ($trx->transaction_status === 'waiting')
                        {{-- Konfirmasi Button --}}
                        <button type="button"
                            class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 px-4 py-2 rounded-lg transition-all duration-200 ease-in-out shadow-sm hover:shadow-md"
                            onclick="openDepositProofModal({{ $trx->idrec }}, '{{ $trx->order_id }}')"
                            title="{{ __('ui.confirm') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M20 6L9 17l-5-5" />
                                <circle cx="12" cy="12" r="10" />
                            </svg>
                            <span class="text-sm font-semibold">{{ __('ui.confirm') }}</span>
                        </button>
                    @elseif($trx->transaction_status === 'pending')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('ui.pending') }}
                        </span>
                    @elseif($trx->transaction_status === 'paid')
                        <div class="text-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ __('ui.verified') }}
                            </span>
                            @if ($trx->verifiedBy)
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ __('ui.by') }}: {{ $trx->verifiedBy->username ?? '-' }}
                                </div>
                            @endif
                            @if ($trx->images->count() > 0)
                                <button
                                    onclick="openDepositProofModal({{ $trx->idrec }}, '{{ $trx->order_id }}', true)"
                                    class="text-xs text-blue-600 hover:text-blue-800 mt-1 underline">
                                    {{ __('ui.view_proof') }}
                                </button>
                            @endif
                        </div>
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
