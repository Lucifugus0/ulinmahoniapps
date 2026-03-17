<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Check-in
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.vehicle_plate') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_type') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.owner_name') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_fee_amount') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.status') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.action') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($parkings as $parking)
            <tr class="{{ $parking->trashed() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $parking->property->name ?? '-' }}
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $parking->property->city ?? '' }}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($parking->bookingTransaction && $parking->bookingTransaction->check_in)
                        <div class="text-gray-900 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($parking->bookingTransaction->check_in)->format('Y-m-d') }}
                        </div>
                        @if($parking->bookingTransaction->check_out)
                            <div class="text-xs text-gray-400 dark:text-gray-500">
                                s/d {{ \Carbon\Carbon::parse($parking->bookingTransaction->check_out)->format('Y-m-d') }}
                            </div>
                        @endif
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        @if ($parking->parking_type === 'car')
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M3 13l2-5a2 2 0 0 1 2-1h10a2 2 0 0 1 2 1l2 5"></path>
                                    <rect x="3" y="13" width="18" height="5" rx="2"></rect>
                                    <circle cx="7" cy="18" r="2"></circle>
                                    <circle cx="17" cy="18" r="2"></circle>

                                </svg>

                            </div>
                        @else
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                                <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24"
     fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
     stroke-linejoin="round">
    <!-- Roda belakang -->
    <circle cx="6" cy="18" r="2.5"></circle>
    <!-- Roda depan -->
    <circle cx="18" cy="18" r="2.5"></circle>
    <!-- Body/deck motor matic -->
    <path d="M8.5 18h7.5"></path>
    <path d="M8.5 18v-3a2 2 0 0 1 2-2h4"></path>
    <!-- Stang/handlebar -->
    <path d="M18 18v-6l2-2"></path>
    <path d="M18 10h3"></path>
    <!-- Jok/seat -->
    <path d="M10 13h4"></path>
    <ellipse cx="12" cy="13" rx="3" ry="1"></ellipse>
    <!-- Spatbor depan -->
    <path d="M16 15c1-1 2-2 2-3"></path>
</svg>
                            </div>
                        @endif
                        <span
                            class="text-sm font-bold text-gray-900 dark:text-gray-100 tracking-wider">{{ $parking->vehicle_plate }}</span>
                    </div>
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
                    <div class="text-sm text-gray-900 dark:text-gray-100">
                        {{ $parking->owner_name ?? '-' }}
                    </div>
                    @if ($parking->owner_phone)
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $parking->owner_phone }}
                        </div>
                    @endif
                    @if ($parking->order_id)
                        @php
                            $parkingTxn = \App\Models\Transaction::where('order_id', $parking->order_id)
                                ->select('is_renewal')
                                ->first();
                            $isParkingRenewal = $parkingTxn && $parkingTxn->is_renewal == 1;
                        @endphp
                        <div class="flex items-center gap-1 mt-1">
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $parking->order_id }}</span>
                            @if ($isParkingRenewal)
                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                    Perpanjangan
                                </span>
                            @endif
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @php
                        $fee = \App\Models\ParkingFee::where('property_id', $parking->property_id)
                            ->where('parking_type', $parking->parking_type)
                            ->where('status', 1)
                            ->first();
                    @endphp
                    @if ($fee)
                        <div class="text-sm font-semibold text-green-600 dark:text-green-400">
                            Rp {{ number_format($fee->fee, 0, ',', '.') }}
                        </div>
                    @else
                        <span class="text-xs text-red-500">{{ __('ui.parking_fee_not_configured') }}</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($parking->trashed())
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            {{ __('ui.deleted') }}
                        </span>
                    @else
                        <div class="flex items-center space-x-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer parking-status-toggle"
                                    data-id="{{ $parking->idrec }}" {{ $parking->status == 1 ? 'checked' : '' }}
                                    onchange="toggleParkingStatus(this)">
                                <div
                                    class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer-checked:bg-blue-600 transition-all duration-300">
                                </div>
                                <div
                                    class="absolute left-0.5 top-0.5 w-5 h-5 bg-white rounded-full shadow transform transition-transform duration-300 peer-checked:translate-x-5">
                                </div>
                            </label>
                            <span
                                class="text-sm font-medium status-label {{ $parking->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $parking->status == 1 ? __('ui.active') : __('ui.inactive') }}
                            </span>
                        </div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                    <div class="flex items-center gap-2">
                        @if ($parking->trashed())
                            <button type="button" onclick="restoreParking({{ $parking->idrec }})"
                                class="text-green-500 hover:text-green-700" title="{{ __('ui.restore') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @else
                            <button type="button" onclick="openEditParkingModal(@js($parking))"
                                class="text-yellow-500 hover:text-yellow-700" title="{{ __('ui.edit') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path
                                        d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                            <button type="button" onclick="deleteParking({{ $parking->idrec }})"
                                class="text-red-500 hover:text-red-700" title="{{ __('ui.delete') }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_parking_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
