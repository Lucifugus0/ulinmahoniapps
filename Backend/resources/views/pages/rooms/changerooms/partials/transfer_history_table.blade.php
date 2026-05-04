{{-- <!-- Transfer History table body partial — loaded inline and via AJAX search --> --}}
<table class="w-full text-sm text-left">
    <thead>
        <tr class="border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500">
            <th class="px-4 py-3 font-semibold">#</th>
            <th class="px-4 py-3 font-semibold">Order ID</th>
            <th class="px-4 py-3 font-semibold">{{ __('ui.guest_name') }}</th>
            <th class="px-4 py-3 font-semibold">{{ __('ui.property') }}</th>
            <th class="px-4 py-3 font-semibold">{{ __('ui.room') }}</th>
            <th class="px-4 py-3 font-semibold text-center">Transfers</th>
            <th class="px-4 py-3 font-semibold">{{ __('ui.last_transfer') }}</th>
            <th class="px-4 py-3 font-semibold text-center">{{ __('ui.action') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transferHistory as $index => $history)
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                {{-- Row number --}}
                <td class="px-4 py-3 text-gray-500">
                    {{ ($transferHistory->currentPage() - 1) * $transferHistory->perPage() + $index + 1 }}
                </td>

                {{-- Order ID --}}
                <td class="px-4 py-3">
                    <span class="font-mono text-sm font-medium">{{ $history['order_id'] }}</span>
                </td>

                {{-- Guest Name + email under it --}}
                <td class="px-4 py-3">
                    <div class="font-medium">{{ $history['guest_name'] }}</div>
                    @if (!empty($history['guest_email']))
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history['guest_email'] }}</div>
                    @endif
                </td>

                {{-- Property --}}
                <td class="px-4 py-3">{{ $history['property']->name ?? '-' }}</td>

                {{-- Room from → to --}}
                <td class="px-4 py-3">
                    <div class="flex items-center gap-1.5">
                        <span class="transfer-badge-origin inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border"
                            style="background: rgba(147, 51, 234, 0.15); color: #a855f7; border-color: rgba(147, 51, 234, 0.3);">
                            {{ $history['first_room']->name ?? '' }} #{{ $history['first_room']->no ?? '' }}
                        </span>
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                        <span class="transfer-badge-current inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border"
                            style="background: rgba(34, 197, 94, 0.15); color: #22c55e; border-color: rgba(34, 197, 94, 0.3);">
                            {{ $history['last_room']->name ?? '' }} #{{ $history['last_room']->no ?? '' }}
                        </span>
                    </div>
                </td>

                {{-- Transfer count badge --}}
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                        style="background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                        {{ $history['transfer_count'] }}x
                    </span>
                </td>

                {{-- Last transfer: who + when --}}
                <td class="px-4 py-3 text-xs">
                    @if($history['last_transfer_by'])
                        <div class="font-medium text-gray-300">{{ $history['last_transfer_by'] }}</div>
                    @endif
                    @if($history['last_transfer_at'])
                        <div class="text-gray-500">{{ \Carbon\Carbon::parse($history['last_transfer_at'])->format('d M Y H:i') }}</div>
                    @else
                        -
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        {{-- View Detail button --}}
                        <button type="button"
                            onclick="viewChainDetail('{{ $history['order_id'] }}')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors border"
                            style="background: rgba(99, 102, 241, 0.15); color: #818cf8; border-color: rgba(99, 102, 241, 0.3);">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Detail
                        </button>

                        {{-- Rollback button --}}
                        @if($history['active_booking'] && $history['active_booking']->previous_booking_id)
                            <button type="button"
                                onclick="openRollbackModal({{ $history['active_booking']->idrec }}, '{{ $history['order_id'] }}')"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors border"
                                style="background: rgba(249, 115, 22, 0.15); color: #fb923c; border-color: rgba(249, 115, 22, 0.3);">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                </svg>
                                Rollback
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    {{ __('ui.no_transfer_history') ?? 'No room transfer history found' }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
