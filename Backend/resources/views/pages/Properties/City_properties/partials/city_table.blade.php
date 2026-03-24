{{-- Partial table for city management — displays city records with status toggle and edit action --}}
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_name') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_province') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_created_by') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_change_date') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_status') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.city_col_action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse($cities as $city)
            <tr>
                {{-- City name with slug displayed below --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex flex-col">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $city->city_name }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $city->slug }}
                        </div>
                    </div>
                </td>

                {{-- Province column --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $city->province }}</div>
                </td>

                {{-- Created by column with date --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $city->createdBy->username ?? 'System' }}
                    </div>
                    <div class="text-xs text-gray-400">
                        {{ $city->created_at->format('d M Y') }}
                    </div>
                </td>

                {{-- Last update column — shows updater or "not updated yet" --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($city->updated_by)
                        <div class="text-sm font-medium text-gray-900">
                            {{ $city->updatedBy->username ?? 'System' }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ $city->updated_at->format('d M Y') }}
                        </div>
                    @else
                        <div class="text-sm font-medium text-gray-400 italic">
                            {{ __('ui.city_not_updated') }}
                        </div>
                    @endif
                </td>

                {{-- Status toggle switch --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox"
                                class="sr-only peer city-status-toggle"
                                data-id="{{ $city->idrec }}"
                                {{ $city->status == 1 ? 'checked' : '' }}
                                onchange="toggleCityStatus(this)">
                            <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300"></div>
                            <div class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5"></div>
                        </label>
                        <span class="text-sm font-medium status-label {{ $city->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $city->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </td>

                {{-- Edit action button --}}
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button type="button"
                        onclick="openEditCityModal(@js($city))"
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
                    {{ __('ui.city_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
