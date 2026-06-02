{{-- Checked-In page table — column structure matches All Bookings (Booking ID | Booking Period
     | Name | Property/Room | Status). Status logic preserved from the prior version: shows
     "Already Checked Out" when check_out_at is set, otherwise "Occupied" green pill. The
     $showActions slot stays optional so this partial keeps working for callers that pass an
     action column (e.g. when reused in flows that need check-in / check-out buttons). --}}
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_booking_id') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_dates_col') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_name') }}
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                {{ __('ui.allbookings_col_property_room') }}
            </th>
            @if ($showStatus ?? true)
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ __('ui.allbookings_col_status') }}
                </th>
            @endif
            @if ($showActions ?? true)
                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ __('ui.actions') }}
                </th>
            @endif
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
        @forelse ($checkOuts as $booking)
            <tr>
                {{-- Booking ID cell — order_id, transaction created_at, and at-a-glance badges
                     (Deposit / Renewed / Checked Out). Mirrors the All Bookings layout exactly. --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium align-top">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->order_id }}</div>
                    @if ($booking->created_at)
                        <div class="text-xs text-gray-400">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                    @endif
                    @php
                        $depositFee = (int) ($booking->transaction->deposit_fee ?? 0);
                        $isRenewed = ($booking->transaction?->renewal_status ?? 0) == 1;
                        $isCheckedOut = $booking->check_out_at && !$isRenewed;
                    @endphp
                    @if ($depositFee > 0 || $isRenewed || $isCheckedOut)
                        <div class="flex flex-wrap items-center gap-1 mt-1">
                            @if ($depositFee > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                    {{ __('ui.allbookings_badge_deposit') }}
                                </span>
                            @endif
                            @if ($isRenewed)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                    {{ __('ui.allbookings_badge_renewed') }}
                                </span>
                            @endif
                            @if ($isCheckedOut)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                    {{ __('ui.allbookings_badge_checked_out') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </td>
                {{-- Booking Period cell — three stacked rows, identical to All Bookings:
                     Row 1: scheduled check-in | scheduled check-out
                     Row 2: actual check-in datetime + admin (if set)
                     Row 3: actual check-out datetime + admin OR "Extended at:" if renewal_status=1 --}}
                <td class="px-6 py-4 text-sm text-gray-500 text-left align-top">
                    @php
                        $checkedInBy = $booking->checkedInByUser;
                        $checkedInByName = $checkedInBy
                            ? (trim(($checkedInBy->first_name ?? '') . ' ' . ($checkedInBy->last_name ?? '')) ?: ($checkedInBy->username ?? null))
                            : null;
                        $checkedOutBy = $booking->checkedOutByUser;
                        $checkedOutByName = $checkedOutBy
                            ? (trim(($checkedOutBy->first_name ?? '') . ' ' . ($checkedOutBy->last_name ?? '')) ?: ($checkedOutBy->username ?? null))
                            : null;
                    @endphp
                    @if ($booking->transaction && $booking->transaction->check_in)
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <div class="whitespace-nowrap">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('Y M d') }}</span>
                                <span class="text-xs text-gray-500 ml-1">{{ \Carbon\Carbon::parse($booking->transaction->check_in)->format('H:i') }}</span>
                            </div>
                            <span class="text-gray-400">|</span>
                            @if ($booking->transaction->check_out)
                                <div class="whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $booking->transaction->check_out->format('Y M d') }}</span>
                                    <span class="text-xs text-gray-400 ml-1">{{ $booking->transaction->check_out->format('H:i') }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">{{ __('ui.allbookings_not_checked_out') }}</span>
                            @endif
                        </div>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">
                            {{ __('ui.allbookings_not_checked_in') }}
                        </span>
                    @endif

                    @if ($booking->check_in_at)
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 mt-1.5">
                            {{ __('ui.allbookings_checkin_at') }} {{ $booking->check_in_at->format('Y-m-d H:i') }}
                            @if ($checkedInByName)
                                <span class="text-gray-500 dark:text-gray-400 font-normal">{{ __('ui.allbookings_by') }}</span>
                                <span>{{ $checkedInByName }}</span>
                            @endif
                        </div>
                    @endif

                    @if ($booking->check_out_at)
                        @if ($booking->transaction?->renewal_status == 1)
                            <div class="text-xs font-medium text-amber-600 dark:text-amber-400 mt-1">
                                {{ __('ui.allbookings_renewed_at') }} {{ $booking->check_out_at->format('Y-m-d H:i') }}
                            </div>
                        @else
                            <div class="text-xs font-medium text-indigo-600 dark:text-indigo-400 mt-1">
                                {{ __('ui.allbookings_checkout_at') }} {{ $booking->check_out_at->format('Y-m-d H:i') }}
                                @if ($checkedOutByName)
                                    <span class="text-gray-500 dark:text-gray-400 font-normal">{{ __('ui.allbookings_by') }}</span>
                                    <span>{{ $checkedOutByName }}</span>
                                @endif
                            </div>
                        @endif
                    @endif
                </td>
                {{-- Name cell with email/phone fallback chain (transaction → booking → user → '-'). --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ trim(($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '')) ?: ($booking->transaction->user_name ?? 'N/A') }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? $booking->user_email ?? $booking->user->email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? $booking->user_phone_number ?? $booking->user->phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                {{-- Property/Room cell. --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>
                {{-- Status cell. Page semantics: rows shown here are checked-in (occupied) bookings.
                     Use the same Renewed override as All Bookings so a renewed parent reads as Renewed
                     instead of Checked-Out. --}}
                @if ($showStatus ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if ($booking->check_out_at && ($booking->transaction?->renewal_status ?? 0) != 1)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                Checked-Out
                            </span>
                        @elseif (($booking->transaction?->renewal_status ?? 0) == 1)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300">
                                Renewed
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                Checked-In
                            </span>
                        @endif
                    </td>
                @endif
                {{-- Action cell — preserves the prior partial's behavior: checkout modal trigger
                     when not yet checked out, "Already checked out + view invoice" when both
                     check_in_at and check_out_at are set. Hidden when $showActions is false (the
                     pure Checked-In list page passes false; reuse from other contexts may keep it). --}}
                @if ($showActions ?? true)
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center" x-data="{ open: false }">
                        @if (is_null($booking->check_out_at))
                            @include('pages.bookings.checkin.partials.checkout_modal_button', ['booking' => $booking])
                        @elseif (!is_null($booking->check_in_at) && !is_null($booking->check_out_at))
                            <div class="flex flex-col items-center space-y-2">
                                <span class="text-green-600">{{ __('ui.checkout_already_checked_out') }}</span>
                                <a href="{{ route('newReserv.checkin.invoice', $booking->order_id) }}"
                                    target="_blank"
                                    class="inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:outline-none">
                                    {{ __('ui.checkout_view_invoice') }}
                                </a>
                            </div>
                        @endif
                    </td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.checkout_no_bookings') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
