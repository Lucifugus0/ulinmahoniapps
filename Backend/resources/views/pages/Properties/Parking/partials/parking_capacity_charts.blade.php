{{-- Parking Capacity charts — one card per accessible property, two stacked progress bars
     (car + motorcycle) showing used / capacity. "Used" comes live from t_parking.status=1
     count (not the drift-prone m_parking_fee.quota_used). Capacity comes from m_parking_fee.

     Site users see only their property card; HQ/HO users see all properties in a 4-col grid.
     Properties whose types are all unconfigured are hidden entirely; within a card, only
     configured types render — keeps the dashboard focused on actionable utilization. --}}
@php
    /* Pre-filter: drop entire cards where neither type is configured. */
    $visibleStats = collect($parkingStats ?? [])->filter(function ($row) {
        foreach ($row['types'] as $stats) {
            if (!empty($stats['configured'])) return true;
        }
        return false;
    })->values();
@endphp
@if($visibleStats->isNotEmpty())
    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($visibleStats as $row)
            @php $prop = $row['property']; @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-baseline justify-between mb-3">
                    <div class="font-semibold text-gray-900 dark:text-gray-100">{{ $prop->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $prop->city }}</div>
                </div>

                @foreach(['car', 'motorcycle'] as $type)
                    @php $stats = $row['types'][$type]; @endphp
                    @continue(empty($stats['configured']))
                    @php
                        $capacity = $stats['capacity'];
                        $used = $stats['used'];
                        $pct = $capacity > 0 ? min(100, (int) round(($used / $capacity) * 100)) : 0;
                        $isFull = $capacity > 0 && $used >= $capacity;
                        if ($isFull) {
                            $barColor = 'bg-red-500';
                        } else {
                            $barColor = $type === 'car' ? 'bg-blue-500' : 'bg-orange-500';
                        }
                        $iconColor = $type === 'car' ? 'text-blue-500 dark:text-blue-400' : 'text-orange-500 dark:text-orange-400';
                        $typeLabel = $type === 'car' ? __('ui.car') : __('ui.motorcycle');
                    @endphp
                    <div class="mb-3 last:mb-0">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <div class="flex items-center gap-1.5">
                                @if($type === 'car')
                                    <svg class="h-4 w-4 {{ $iconColor }}" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 13l2-5a2 2 0 0 1 2-1h10a2 2 0 0 1 2 1l2 5"></path>
                                        <rect x="3" y="13" width="18" height="5" rx="2"></rect>
                                        <circle cx="7" cy="18" r="2"></circle>
                                        <circle cx="17" cy="18" r="2"></circle>
                                    </svg>
                                @else
                                    <svg class="h-4 w-4 {{ $iconColor }}" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="6" cy="18" r="2.5"></circle>
                                        <circle cx="18" cy="18" r="2.5"></circle>
                                        <path d="M8.5 18h7.5"></path>
                                        <path d="M8.5 18v-3a2 2 0 0 1 2-2h4"></path>
                                        <path d="M18 18v-6l2-2"></path>
                                        <path d="M18 10h3"></path>
                                    </svg>
                                @endif
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $typeLabel }}</span>
                                @if($isFull)
                                    <span class="px-1.5 py-0.5 text-[10px] leading-3 font-semibold rounded-full bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300">FULL</span>
                                @endif
                            </div>
                            <div class="font-mono text-gray-700 dark:text-gray-300">
                                {{ $used }}/{{ $capacity }} <span class="text-gray-400 dark:text-gray-500">({{ $pct }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="{{ $barColor }} h-2 rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
@endif
