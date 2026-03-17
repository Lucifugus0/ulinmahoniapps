<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.room_number') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.lock_id') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.alias_model') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.battery') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.passcode') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.date_added') }}
            </th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($doorLocks as $lock)
            <tr>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $lock->room->no ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $lock->room->name ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $lock->room->property_name ?? '-' }}
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="text-sm font-mono font-medium text-gray-900">{{ $lock->lock_id }}</span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $lock->lock_alias ?? '-' }}</div>
                    <div class="text-xs text-gray-500">{{ $lock->model_num ?? '-' }}</div>
                    @if($lock->lock_mac)
                        <div class="text-xs text-gray-400 font-mono">{{ $lock->lock_mac }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($lock->battery_level !== null)
                        @php
                            $batteryColor = $lock->battery_level > 50 ? 'text-green-600' : ($lock->battery_level > 20 ? 'text-yellow-500' : 'text-red-500');
                        @endphp
                        <span class="text-sm font-medium {{ $batteryColor }}">
                            {{ $lock->battery_level }}%
                        </span>
                    @else
                        <span class="text-sm text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($lock->passcode)
                        <div class="text-sm font-mono font-bold text-indigo-700">{{ $lock->passcode }}</div>
                        @if($lock->passcode_name)
                            <div class="text-xs text-gray-500">{{ $lock->passcode_name }}</div>
                        @endif
                        @if($lock->passcode_end)
                            <div class="text-xs text-gray-400">
                                s/d {{ \Carbon\Carbon::createFromTimestampMs($lock->passcode_end)->format('d M Y') }}
                            </div>
                        @endif
                    @else
                        <span class="text-xs text-gray-400 italic">{{ __('ui.no_passcode') }}</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $lock->created_at->format('d M Y') }}</div>
                    <div class="text-xs text-gray-400">{{ $lock->created_at->format('H:i') }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <div class="flex items-center gap-2">
                        <!-- Add Passcode -->
                        <button type="button"
                            onclick="openAddPasscodeModal(@js(['id' => $lock->idrec, 'lock_id' => $lock->lock_id, 'lock_alias' => $lock->lock_alias]))"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs rounded-md shadow transition"
                            title="{{ __('ui.add_passcode') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                            </svg>
                            {{ __('ui.passcode') }}
                        </button>

                        <!-- Delete -->
                        <button type="button"
                            onclick="deleteDoorLock({{ $lock->idrec }})"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs rounded-md border border-red-200 transition"
                            title="{{ __('ui.delete') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            {{ __('ui.delete') }}
                        </button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                    {{ __('ui.no_door_lock_found') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
