<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Icon
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nama Fasilitas
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Dibuat Oleh
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tanggal Perubahan
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($facilities as $facility)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-100 rounded-lg">
                        @if($facility->icon)
                            <span class="iconify text-2xl text-gray-700" data-icon="{{ $facility->icon }}"></span>
                        @else
                            <span class="text-gray-400 text-xs">-</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $facility->facility }}
                        </div>
                        <div class="text-sm text-gray-500 break-words whitespace-normal">
                            <!-- Display facility description, fallback to translation if null -->
                            {{ $facility->description ?? __('ui.facility_no_description') }}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $facility->createdBy->username ?? 'System' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $facility->created_at->format('d M Y') }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($facility->updated_by)
                        <div class="text-sm font-medium text-gray-900">
                            {{ $facility->updatedBy->username ?? 'System' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $facility->updated_at->format('d M Y') }}
                        </div>
                    @else
                        <div class="text-sm font-medium text-gray-400 italic">
                            {{ __('ui.facility_not_updated') }}
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                class="sr-only peer facility-room-status-toggle"
                                data-id="{{ $facility->idrec }}"
                                {{ $facility->status == 1 ? 'checked' : '' }}
                                onchange="toggleFacilityRoomStatus(this)">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                        </label>
                        <span class="text-sm font-medium status-label {{ $facility->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $facility->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button type="button"
                        onclick="openEditRoomFacilityModal(@js($facility))"
                        class="text-yellow-500 hover:text-yellow-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path
                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.facility_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
