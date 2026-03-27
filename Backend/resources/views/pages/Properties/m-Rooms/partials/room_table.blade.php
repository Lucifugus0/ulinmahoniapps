<!-- Room Table — restructured columns: removed "Ditambahkan Oleh", removed delete icon,
     swapped "Edit Harga" to use Multi-Tier icon, removed separate Multi-Tier button,
     relocated date columns to end as "Dibuat Oleh" / "Dirubah Oleh" with username + date badge,
     fixed badge dark mode styling -->
<table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <!-- Properti -->
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_property') }}</th>
                    <!-- Nama Kamar -->
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_name') }}</th>
                    <!-- Status -->
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_status') }}</th>
                    <!-- Periode -->
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_period') }}</th>
                    <!-- Aksi -->
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_action') }}</th>
                    <!-- Dibuat Oleh (relocated from "Tgl Penambahan" + "Ditambahkan Oleh") -->
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_created_by') }}</th>
                    <!-- Dirubah Oleh (relocated from "Tgl Perubahan") -->
                    <th scope="col"
                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ __('ui.room_col_updated_by') }}</th>
                </tr>
            </thead>
            <!-- no-backdrop-filter prevents dark mode backdrop-filter from trapping fixed-position modals inside table rows -->
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-400 no-backdrop-filter" id="rooms-table-body">
                @forelse ($rooms as $room)
                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer border-b border-gray-300 property-table-row">
                        <!-- Properti: property name + subdistrict -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $room->property->name ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $room->property->subdistrict ?? '-' }}
                            </div>
                        </td>
                        <!-- Nama Kamar: room name + number -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                            <div class="text-sm font-medium text-gray-900">{{ $room->name }}</div>
                            <div class="text-sm text-gray-400">Number:{{ $room->no }}</div>
                        </td>
                        <!-- Status: toggle switch -->
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
                        <!-- Periode: daily/monthly badges with dark mode support -->
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
                                    <!-- Both daily and monthly badges -->
                                    <div class="flex items-center justify-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400 dark:text-blue-300" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Daily
                                        </span>
                                        <span
                                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400 dark:text-green-300" fill="currentColor"
                                                viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3" />
                                            </svg>
                                            Monthly
                                        </span>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.room_both_options') }}</span>
                                @elseif ($isDaily)
                                    <!-- Daily only badge -->
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-blue-400 dark:text-blue-300" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_daily_only') }}
                                    </span>
                                @elseif ($isMonthly)
                                    <!-- Monthly only badge -->
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400 dark:text-green-300" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_monthly_only') }}
                                    </span>
                                @else
                                    <!-- Not set badge -->
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400 dark:text-gray-500" fill="currentColor"
                                            viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ __('ui.room_not_set') }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Aksi: View, Edit, Multi-Tier Pricing (for daily rooms only) -->
                        <!-- Removed: delete icon, separate calendar "Edit Harga" button -->
                        <!-- Changed: "Edit Harga" calendar icon replaced with Multi-Tier pricing icon -->
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
                                    <!-- Edit Harga: calendar-based daily price editor -->
                                    @include('pages.Properties.m-Rooms.components.edit-price-daily-modal', [
                                        'room' => $room,
                                    ])
                                @endif

                                {{-- Delete button removed per requirement --}}
                            </div>
                        </td>

                        <!-- Dibuat Oleh: creator username (row 1) + creation date badge (row 2) -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $room->creator->username ?? '-' }}
                            </div>
                            @if ($room->created_at)
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($room->created_at)->format('Y M d, H:i') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>

                        <!-- Dirubah Oleh: updater username (row 1) + last edit date badge (row 2) -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $room->updater->username ?? '-' }}
                            </div>
                            @if ($room->updated_at)
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($room->updated_at)->format('Y M d, H:i') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <!-- Updated colspan to 7 (was 8) since we removed "Ditambahkan Oleh" column -->
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                            {{ __('ui.room_no_rooms') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
