<!-- Room Table — no wrapper needed, parent container already provides bg/shadow -->
<table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_property') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_name') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_addition_date') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_change_date') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_added_by') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_status') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_period') }}</th>
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_action') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-400" id="rooms-table-body">
                @forelse ($rooms as $room)
                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer border-b border-gray-300 property-table-row">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $room->property->name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $room->property->subdistrict ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            <div class="text-sm font-medium text-gray-900">{{ $room->name }}</div>
                            <div class="text-sm text-gray-400">Number:{{ $room->no }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            @if ($room->created_at)
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($room->created_at)->format('Y M d') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($room->created_at)->format('H:i') }}</div>
                            @else
                                <div>-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            @if ($room->updated_at)
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($room->updated_at)->format('Y M d') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($room->updated_at)->format('H:i') }}</div>
                            @else
                                <div>-</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $room->creator->username }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" class="sr-only peer room-status-toggle" data-id="{{ $room->idrec }}"
                                        {{ $room->status ? 'checked' : '' }} onchange="toggleRoomStatus(this)">
                                    <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                                    <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                                </label>
                                <span class="text-sm font-medium status-label {{ $room->status ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $room->status ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $periode = is_array($room->periode)
                                    ? $room->periode
                                    : json_decode($room->periode, true);

                                $isDaily = $periode['daily'] ?? false;
                                $isMonthly = $periode['monthly'] ?? false;
                            @endphp

                            <div class="flex flex-col items-center space-y-2">
                                @if ($isDaily && $isMonthly)
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Daily
                                        </span>
                                        <span
                                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Monthly
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ __('ui.room_both_options') }}</span>
                                @elseif ($isDaily)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_daily_only') }}
                                    </span>
                                @elseif ($isMonthly)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_monthly_only') }}
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_not_set') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex space-x-2 justify-center">
                                <!-- View Room Button -->
                                @include('pages.Properties.m-Rooms.components.room-detail-modal', [
                                    'room' => $room,
                                ])

                                <!-- Edit Room Modal -->
                                @include('pages.Properties.m-Rooms.components.room-edit-modal', [
                                    'room' => $room,
                                ])

                                @if ($isDaily)
                                    <!-- Edit Price (Calendar) -->
                                    @include('pages.Properties.m-Rooms.components.edit-price-daily-modal', [
                                        'room' => $room,
                                    ])

                                    <!-- Multi-Tier Pricing: Pricing Rules Modal -->
                                    @include('pages.Properties.m-Rooms.components.pricing-rules-modal', [
                                        'room' => $room,
                                    ])
                                @endif

                                <!-- Delete (Bin) -->
                                <button title="Delete"
                                    class="p-2 flex items-center justify-center text-red-600 hover:text-red-900 transition-colors duration-200 rounded-full hover:bg-red-50"
                                    onclick="deleteRoom({{ $room->idrec }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('ui.room_no_rooms') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
