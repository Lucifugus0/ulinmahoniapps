<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_type') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_fee_amount') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_capacity') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.added_by') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($parkingFees as $parking)
            <tr class="{{ $parking->trashed() ? 'bg-red-50 dark:bg-red-900/10 opacity-70' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }} transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium {{ $parking->trashed() ? 'text-gray-400 dark:text-gray-500 line-through' : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $parking->property->name ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $parking->property->city ?? '' }}
                    </div>
                    @if($parking->trashed())
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400 mt-1">
                            Dihapus {{ $parking->deleted_at->diffForHumans() }}
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $typeColors = [
                            'car' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                            'motorcycle' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
                        ];
                        $colorClass = $typeColors[$parking->parking_type] ?? 'bg-gray-100 text-gray-800';
                        $typeLabel = $parking->parking_type === 'car' ? __('ui.car') : __('ui.motorcycle');
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                        {{ $typeLabel }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($parking->fee, 0, ',', '.') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($parking->capacity > 0)
                        <!-- Quota Information -->
                        <div class="text-sm text-gray-900 dark:text-gray-100 mb-1">
                            <span class="font-semibold {{ $parking->available_quota > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $parking->available_quota }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400">/ {{ $parking->capacity }}</span>
                            <span class="text-xs text-gray-400 ml-1">({{ __('ui.available') }})</span>
                        </div>

                        <!-- Used Quota -->
                        <div class="flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400">
                            <span class="font-medium">{{ __('ui.in_use') }}:</span>
                            @if(!$parking->trashed())
                                <button type="button"
                                    onclick="adjustQuota({{ $parking->idrec }}, 'decrement')"
                                    {{ $parking->quota_used <= 0 ? 'disabled' : '' }}
                                    class="w-5 h-5 flex items-center justify-center rounded bg-red-100 text-red-600 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-800/50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                </button>
                            @endif
                            <span class="font-semibold text-blue-600 dark:text-blue-400 min-w-[1.5rem] text-center">{{ $parking->quota_used }}</span>
                            @if(!$parking->trashed())
                                <button type="button"
                                    onclick="adjustQuota({{ $parking->idrec }}, 'increment')"
                                    {{ ($parking->capacity > 0 && $parking->quota_used >= $parking->capacity) ? 'disabled' : '' }}
                                    class="w-5 h-5 flex items-center justify-center rounded bg-green-100 text-green-600 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400 dark:hover:bg-green-800/50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                </button>
                            @endif
                        </div>

                        <!-- Quota Progress Bar -->
                        @if($parking->quota_usage_percentage > 0)
                            <div class="mt-2">
                                <div class="w-full bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                    <div class="h-1.5 rounded-full transition-all duration-300
                                        {{ $parking->quota_usage_percentage >= 90 ? 'bg-red-600' : ($parking->quota_usage_percentage >= 70 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                        style="width: {{ min($parking->quota_usage_percentage, 100) }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ number_format($parking->quota_usage_percentage, 0) }}% {{ __('ui.used') }}
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('ui.no_quota_limit') }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $parking->createdBy->username ?? 'System' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $parking->created_at->format('d M Y') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if(!$parking->trashed())
                        <div class="flex items-center space-x-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox"
                                    class="sr-only peer parking-status-toggle"
                                    data-id="{{ $parking->idrec }}"
                                    {{ $parking->status == 1 ? 'checked' : '' }}
                                    onchange="toggleParkingStatus(this)">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                            </label>
                            <span class="text-sm font-medium status-label {{ $parking->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $parking->status == 1 ? __('ui.active') : __('ui.inactive') }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs text-gray-400 dark:text-gray-500 italic">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    @if($parking->trashed())
                        <button type="button"
                            onclick="restoreParkingFee({{ $parking->idrec }})"
                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200"
                            title="{{ __('ui.restore') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <button type="button"
                            onclick="openEditParkingModal(@js($parking))"
                            class="text-yellow-500 hover:text-yellow-700"
                            title="{{ __('ui.edit') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                        </button>
                        <button type="button"
                            onclick="deleteParkingFee({{ $parking->idrec }})"
                            class="text-red-500 hover:text-red-700 ml-2"
                            title="{{ __('ui.delete') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
