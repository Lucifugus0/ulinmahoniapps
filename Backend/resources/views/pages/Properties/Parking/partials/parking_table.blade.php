{{-- Parking Management table.
     Column order: Invoice # | Parking Period | Booking ID | Property (with Room) | Parking Type (with Plate) | Status | Action

     Invoice # falls back through: latest paid t_parking_fee_transaction.invoice_id →
     t_transactions.invoice_number for the linked booking → '-' (no paid txn yet, e.g. pending or
     pre-2026-03-06 legacy rows). The model accessor `invoice_display` does the lookup.
     Booking ID cell stacks 3 rows: order_id (+ Perpanjangan badge), customer name + phone,
     and the booking's stay period (from t_transactions.check_in / check_out).
     Parking Type cell stacks 2 rows: the type pill, then the vehicle plate with type icon.
     New entries are no longer created here — Finance > Parking Entry is the only entry path. --}}
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.invoice_no_short') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_period') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.booking_id') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.property') }}
            </th>
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ __('ui.parking_type') }}
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
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-300 dark:divide-gray-400">
        @forelse($parkings as $parking)
            @php
                /* Customer display: prefer User.first_name+last_name + User.phone_number;
                   fall back to the booking transaction snapshot, then to the parking row's own snapshot.
                   Matches the precedence used on the Bookings tables. */
                $txn = $parking->bookingTransaction;
                $usr = $txn?->user;
                $custName = trim(($usr?->first_name ?? '') . ' ' . ($usr?->last_name ?? ''));
                if ($custName === '') {
                    $custName = $txn?->user_name ?: $parking->owner_name ?: '-';
                }
                $custPhone = $usr?->phone_number ?: ($txn?->user_phone_number ?: $parking->owner_phone);
                /* Stay period from the parking's linked booking transaction (NOT the parking's own period). */
                $stayIn = $txn?->check_in ? \Carbon\Carbon::parse($txn->check_in)->format('d M Y') : null;
                $stayOut = $txn?->check_out ? \Carbon\Carbon::parse($txn->check_out)->format('d M Y') : null;
            @endphp
            <tr class="{{ $parking->trashed() ? 'bg-red-50 dark:bg-red-900/20' : 'hover:bg-gray-200 dark:hover:bg-gray-700' }} transition-colors duration-200 border-b border-gray-300 property-table-row">
                {{-- Invoice # — number on row 1, source label ("Booking + Parking" vs "Add on Parking") on row 2 --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($parking->invoice_display)
                        <div class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $parking->invoice_display }}</div>
                        @if($parking->invoice_source === 'bundled')
                            <div class="mt-0.5">
                                <span class="px-1.5 py-0.5 inline-flex text-[10px] leading-3 font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ __('ui.parking_invoice_bundled') }}
                                </span>
                            </div>
                        @elseif($parking->invoice_source === 'addon')
                            <div class="mt-0.5">
                                <span class="px-1.5 py-0.5 inline-flex text-[10px] leading-3 font-semibold rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                                    {{ __('ui.parking_invoice_addon') }}
                                </span>
                            </div>
                        @endif
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>

                {{-- Parking Period (from start_rent / end_rent via the model's accessor) --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="text-xs text-gray-700 dark:text-gray-300">{{ $parking->rent_period_label }}</div>
                    @if($parking->parking_duration)
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $parking->parking_duration }} {{ __('ui.parking_duration_months') }}
                        </div>
                    @endif
                </td>

                {{-- Booking ID stack: order_id + Perpanjangan, then customer name+phone, then booking stay period --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($parking->order_id)
                        @php
                            $isParkingRenewal = $txn && $txn->is_renewal == 1;
                        @endphp
                        <div class="flex items-center gap-1">
                            <span class="text-xs font-mono text-gray-700 dark:text-gray-300">{{ $parking->order_id }}</span>
                            @if ($isParkingRenewal)
                                <span class="px-1.5 py-0.5 inline-flex text-xs leading-4 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300">
                                    Perpanjangan
                                </span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">
                            {{ $custName }}@if($custPhone) - {{ $custPhone }}@endif
                        </div>
                        @if($stayIn || $stayOut)
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                {{ $stayIn ?? '-' }} → {{ $stayOut ?? '-' }}
                            </div>
                        @endif
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </td>

                {{-- Property + Room (room comes from t_booking row whose status=1, joined to m_rooms) --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $parking->property->name ?? '-' }}
                    </div>
                    @if($parking->activeBooking && $parking->activeBooking->room)
                        <div class="text-xs text-gray-600 dark:text-gray-300 mt-0.5">
                            {{ __('ui.room') }} {{ $parking->activeBooking->room->no }}
                        </div>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $parking->property->city ?? '' }}
                    </div>
                </td>

                {{-- Parking Type pill + Vehicle Plate (with type icon) stacked --}}
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
                    <div class="flex items-center gap-2 mt-1">
                        @if ($parking->parking_type === 'car')
                            <svg class="h-4 w-4 text-blue-600 dark:text-blue-400" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 13l2-5a2 2 0 0 1 2-1h10a2 2 0 0 1 2 1l2 5"></path>
                                <rect x="3" y="13" width="18" height="5" rx="2"></rect>
                                <circle cx="7" cy="18" r="2"></circle>
                                <circle cx="17" cy="18" r="2"></circle>
                            </svg>
                        @else
                            <svg class="h-4 w-4 text-orange-600 dark:text-orange-400" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="6" cy="18" r="2.5"></circle>
                                <circle cx="18" cy="18" r="2.5"></circle>
                                <path d="M8.5 18h7.5"></path>
                                <path d="M8.5 18v-3a2 2 0 0 1 2-2h4"></path>
                                <path d="M18 18v-6l2-2"></path>
                                <path d="M18 10h3"></path>
                                <path d="M10 13h4"></path>
                                <ellipse cx="12" cy="13" rx="3" ry="1"></ellipse>
                                <path d="M16 15c1-1 2-2 2-3"></path>
                            </svg>
                        @endif
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100 tracking-wider">{{ $parking->vehicle_plate }}</span>
                    </div>
                </td>

                {{-- Status (read-only badge — toggle removed) --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($parking->trashed())
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                            {{ __('ui.deleted') }}
                        </span>
                    @elseif ($parking->status == 1)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                            {{ __('ui.active') }}
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                            {{ __('ui.inactive') }}
                        </span>
                    @endif
                </td>

                {{-- Action (Edit/Delete removed; Restore stays for soft-deleted rows surfaced via Show Deleted toggle) --}}
                <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
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
                        <span class="text-xs text-gray-400">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    {{ __('ui.no_parking_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
