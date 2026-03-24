{{-- Partial table for room name type management --}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.room_name_type_col_name') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.facility_col_created_by') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.facility_col_change_date') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.facility_col_status') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.facility_col_action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($roomNameTypes as $type)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $type->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $type->createdBy->username ?? 'System' }}</div>
                    <div class="text-xs text-gray-400">{{ $type->created_at ? $type->created_at->format('d M Y') : '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($type->updated_by)
                        <div class="text-sm font-medium text-gray-900">{{ $type->updatedBy->username ?? 'System' }}</div>
                        <div class="text-xs text-gray-400">{{ $type->updated_at->format('d M Y') }}</div>
                    @else
                        <div class="text-sm font-medium text-gray-400 italic">{{ __('ui.facility_not_updated') }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" data-id="{{ $type->idrec }}"
                                {{ $type->status == 1 ? 'checked' : '' }}
                                onchange="toggleRoomNameTypeStatus(this)">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                        </label>
                        <span class="text-sm font-medium status-label {{ $type->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $type->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button type="button" onclick="openEditRoomNameTypeModal(@js($type))" class="text-yellow-500 hover:text-yellow-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.room_name_type_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
