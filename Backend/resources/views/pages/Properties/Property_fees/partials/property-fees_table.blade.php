{{-- Table partial for the unified deposit & parking fee page.
     One row per property showing deposit, motorcycle, and car columns. --}}
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.deposit_fee') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.motorcycle') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.car') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-400">
        @forelse($properties as $property)
            @php
                /* Extract motorcycle and car parking fee from eager-loaded collection */
                $motorcycle = $property->parkingFees->where('parking_type', 'motorcycle')->first();
                $car = $property->parkingFees->where('parking_type', 'car')->first();
                $deposit = $property->depositFee;
            @endphp
            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 cursor-pointer border-b border-gray-300 property-table-row">
                {{-- Property name + city --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $property->name }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $property->city ?? '' }}
                    </div>
                </td>

                {{-- Deposit Fee column --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($deposit)
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                            Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                        </div>
                        <div class="flex items-center space-x-1 mt-1">
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $deposit->status == 1 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                                {{ $deposit->status == 1 ? __('ui.active') : __('ui.inactive') }}
                            </span>
                        </div>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('ui.not_set') }}</span>
                    @endif
                </td>

                {{-- Motorcycle column --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($motorcycle)
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                            Rp {{ number_format($motorcycle->fee, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $motorcycle->quota_used }}/{{ $motorcycle->capacity }} {{ __('ui.parking_capacity') }}
                        </div>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium mt-1 {{ $motorcycle->status == 1 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                            {{ $motorcycle->status == 1 ? __('ui.active') : __('ui.inactive') }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('ui.not_set') }}</span>
                    @endif
                </td>

                {{-- Car column --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($car)
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                            Rp {{ number_format($car->fee, 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $car->quota_used }}/{{ $car->capacity }} {{ __('ui.parking_capacity') }}
                        </div>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium mt-1 {{ $car->status == 1 ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400' }}">
                            {{ $car->status == 1 ? __('ui.active') : __('ui.inactive') }}
                        </span>
                    @else
                        <span class="text-sm text-gray-400 dark:text-gray-500 italic">{{ __('ui.not_set') }}</span>
                    @endif
                </td>

                {{-- Actions --}}
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <button type="button"
                        onclick="openEditPropertyFeesModal({{ $property->idrec }}, '{{ addslashes($property->name) }}', {{ $deposit ? $deposit->amount : 'null' }}, {{ $motorcycle ? $motorcycle->fee : 'null' }}, {{ $motorcycle ? $motorcycle->capacity : 'null' }}, {{ $car ? $car->fee : 'null' }}, {{ $car ? $car->capacity : 'null' }})"
                        class="text-yellow-500 hover:text-yellow-700"
                        title="{{ __('ui.edit') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
