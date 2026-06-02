<!-- All Bookings table with server-side sorting -->
<!-- Sortable columns: Booking ID, Booking Period (sorts by check_in), Name, Property -->
<!-- Clicking a header triggers fetchFilteredBookings with sort params -->
<table class="min-w-full divide-y divide-gray-200" id="allbookings-sortable-table">
    <thead class="bg-gray-50">
        <tr>
            @php
                /* Get current sort state from request for icon display.
                   Defaults must match AllBookingController: Booking ID descending. */
                $currentSort = request('sort_by', 'orderid');
                $currentDir = request('sort_dir', 'desc');
            @endphp
            <!-- Sortable Booking ID header (translated via ui lang file) — first column now -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('orderid')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_booking_id') }}
                    @if($currentSort === 'orderid')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            {{-- Booking Period header. Single sortable header — sorts by t_transactions.check_in
                 (the check_out sort sub-button was dropped; sort by check_in covers the common
                 case and the merged cell still surfaces both check_out + actual check-in/out
                 timestamps for visual scanning). --}}
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('checkin')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_dates_col') }}
                    @if($currentSort === 'checkin')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Name header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('name')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_name') }}
                    @if($currentSort === 'name')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Sortable Property header (translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                onclick="sortBookingsBy('property')">
                <div class="flex items-center gap-1">
                    {{ __('ui.allbookings_col_property_room') }}
                    @if($currentSort === 'property')
                        @if($currentDir === 'asc')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg>
                        @else
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg>
                        @endif
                    @else
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg>
                    @endif
                </div>
            </th>
            <!-- Status header (not sortable, translated via ui lang file) -->
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ __('ui.allbookings_col_status') }}
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($bookings as $booking)
            <tr>
                {{-- Booking ID cell — first column now (swapped with the Booking Period cell so the
                     order_id is the leftmost data point on each row, matching the Booking-ID-default sort). --}}
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 align-top">
                    <div class="text-sm font-medium text-gray-900">{{ $booking->order_id }}</div>
                    @if ($booking->created_at)
                        <div class="text-xs text-gray-400">{{ $booking->created_at->format('Y-m-d H:i') }}</div>
                    @endif
                    {{-- At-a-glance badges:
                         Deposit (green): transaction.deposit_fee > 0 — initial booking that carried a deposit.
                         Renewed (purple): transaction.renewal_status = 1 — superseded by a later renewal.
                         Checked Out (red): t_booking.check_out_at set AND renewal_status != 1 — distinguishes
                         a genuine admin-driven physical checkout from a renewal close-out (which also writes
                         check_out_at on the parent but is not the same thing). --}}
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
                {{-- Booking Period cell. Three stacked rows:
                       Row 1: scheduled check-in date/time | scheduled check-out date/time (from t_transactions).
                       Row 2: actual check-in datetime + admin who performed it (from t_booking.check_in_at + checkedInByUser).
                       Row 3: actual check-out datetime + admin who performed it (from t_booking.check_out_at + checkedOutByUser).
                              When the parent transaction's renewal_status = 1, row 3 reads "Extended at:" with no
                              "by" attribution — renewal is a guest-driven action that closes the parent without
                              an admin checkout, so checked_out_by is NULL and showing "by —" would be misleading. --}}
                <td class="px-6 py-4 text-sm text-gray-500 text-left align-top">
                    @php
                        /* Pretty admin name for the "by" attribution. Mirrors the customer-name fallback used
                           elsewhere on this page: prefer first+last, then username, then '-'. */
                        $checkedInBy = $booking->checkedInByUser;
                        $checkedInByName = $checkedInBy
                            ? (trim(($checkedInBy->first_name ?? '') . ' ' . ($checkedInBy->last_name ?? '')) ?: ($checkedInBy->username ?? null))
                            : null;
                        $checkedOutBy = $booking->checkedOutByUser;
                        $checkedOutByName = $checkedOutBy
                            ? (trim(($checkedOutBy->first_name ?? '') . ' ' . ($checkedOutBy->last_name ?? '')) ?: ($checkedOutBy->username ?? null))
                            : null;
                    @endphp

                    {{-- Row 1: scheduled period (always shown when transaction has dates) --}}
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
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            {{ __('ui.allbookings_not_checked_in') }}
                        </span>
                    @endif

                    {{-- Row 2: actual check-in (only when admin has checked the guest in) --}}
                    @if ($booking->check_in_at)
                        <div class="text-xs font-medium text-green-600 dark:text-green-400 mt-1.5">
                            {{ __('ui.allbookings_checkin_at') }} {{ $booking->check_in_at->format('Y-m-d H:i') }}
                            @if ($checkedInByName)
                                <span class="text-gray-500 dark:text-gray-400 font-normal">{{ __('ui.allbookings_by') }}</span>
                                <span>{{ $checkedInByName }}</span>
                            @endif
                        </div>
                    @endif

                    {{-- Row 3: actual check-out OR renewal close-out --}}
                    @if ($booking->check_out_at)
                        @if ($booking->transaction?->renewal_status == 1)
                            {{-- Renewal: no admin "by" — guest renewed via app/web, not an admin action. --}}
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
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <!-- Display first_name + last_name from user model instead of transaction user_name -->
                            <div class="text-sm font-medium text-gray-900">
                                {{ ($booking->user->first_name ?? '') . ' ' . ($booking->user->last_name ?? '') ?: ($booking->transaction->user_name ?? 'N/A') }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_email ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->transaction->user_phone_number ?? '-' }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $booking->property->name ?? 'N/A' }}</div>
                    <div class="text-sm text-gray-500">{{ $booking->room->name ?? 'N/A' }}</div>
                    @if($booking->room->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $booking->room->no }}</div>
                    @endif
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    @php
                        /* Checked-Out is red here per design (matches the red "Checked Out" badge under Booking ID).
                           A "Renewed" pseudo-status is added so a renewal-closed parent doesn't render as Checked-Out
                           — the parent never physically left the room, the next renewal just superseded it. */
                        /* Dark-mode variants are present on Checked-In / Checked-Out / Renewed because those are
                           the three states whose colored pills get scanned constantly. The other (less common)
                           lifecycle states keep just the light-mode classes — Tailwind v4 still renders the
                           light bg/text on the dark page background, just with less contrast. */
                        $statusClasses = [
                            'Waiting For Payment' => 'bg-yellow-100 text-yellow-800',
                            'Waiting For Confirmation Payment' => 'bg-orange-100 text-orange-800',
                            'Waiting For Check-In' => 'bg-cyan-100 text-cyan-800',
                            'Checked-In' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                            'Checked-Out' => 'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300',
                            'Renewed' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
                            'Canceled' => 'bg-red-100 text-red-800',
                            'Expired' => 'bg-pink-100 text-pink-800',
                            'Rejected' => 'bg-red-100 text-red-800',
                            'Unknown' => 'bg-slate-100 text-slate-800',
                        ];

                        $statusLabels = [
                            'Waiting For Payment' => 'Pending Payment',
                            'Waiting For Confirmation Payment' => 'Confirming',
                            'Waiting For Check-In' => 'Waiting Check-In',
                            'Checked-In' => 'Checked-In',
                            'Checked-Out' => 'Checked-Out',
                            'Renewed' => 'Renewed',
                            'Canceled' => 'Canceled',
                            'Expired' => 'Expired',
                            'Rejected' => 'Rejected',
                            'Unknown' => 'Unknown',
                        ];

                        /* Override Checked-Out → Renewed when this booking's transaction was superseded
                           by a later renewal (renewal_status = 1). The Booking model accessor returns
                           Checked-Out whenever check_out_at is set, but for renewed parents the timestamp
                           reflects the renewal close-out, not a real departure — Renewed is the more
                           accurate label and aligns with the Renewed badge under the Booking ID. */
                        $displayStatus = $booking->status;
                        if ($displayStatus === 'Checked-Out' && ($booking->transaction?->renewal_status ?? 0) == 1) {
                            $displayStatus = 'Renewed';
                        }
                    @endphp

                    <div class="flex flex-col items-center">
                        {{-- Standard status badge (no date/time). The actual check-in / check-out timestamps
                             live in the merged Dates column; the per-booking state badges (Deposit / Renewed /
                             Checked Out) live under the Booking ID. So the Status column only carries the
                             coarse lifecycle label here, keeping the column compact. --}}
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$displayStatus] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $statusLabels[$displayStatus] ?? $displayStatus }}
                        </span>

                        {{-- Resolve the chosen payment method, with shared logic across paid/expired branches.
                             Prefer t_transactions.transaction_type (universal — set on every booking incl. DOKU
                             channels) and fall back to legacy t_transactions.payment_bank (only set on the
                             admin-confirmed BRI Manual flow). Channel values are inconsistent across history
                             ('bri_manual' / 'BRI Manual' / 'qris' / 'QRIS' / 'credit_card' / 'mandiri' / etc.),
                             so normalize via a small label map; everything else falls through to uppercase. --}}
                        @php
                            $rawType = $booking->transaction->transaction_type ?? null;
                            $paymentLabels = [
                                'bri_manual'  => 'BRI Manual',
                                'BRI Manual'  => 'BRI Manual',
                                'qris'        => 'QRIS',
                                'QRIS'        => 'QRIS',
                                'credit_card' => 'Credit Card',
                                'Credit Card' => 'Credit Card',
                                'Transfer VA' => 'Transfer VA',
                                'mandiri'     => 'Mandiri VA',
                                'bri'         => 'BRI VA',
                                'bsi'         => 'BSI VA',
                                'cimb'        => 'CIMB VA',
                                'btn'         => 'BTN VA',
                                'danamon'     => 'Danamon VA',
                            ];
                            $paidMethod = $rawType
                                ? ($paymentLabels[$rawType] ?? strtoupper(str_replace('_', ' ', $rawType)))
                                : ($booking->transaction->payment_bank ?? null);
                        @endphp

                        {{-- Show payment info (method, confirmer, paid datetime) under Checked-In / Checked-Out badge --}}
                        @if (in_array($booking->status, ['Checked-In', 'Checked-Out']))
                            @php
                                /* verified_by/_at from t_payment. DOKU webhook callbacks (QRIS / VA / Credit Card)
                                   do NOT write verified_by — when the transaction is genuinely paid through DOKU,
                                   show "DOKU" so the cell is never an unhelpful em-dash. Admin-approved manual
                                   payments still show the admin's name. */
                                $confirmer = $booking->payment->verifiedBy ?? null;
                                $confirmerName = $confirmer
                                    ? (trim(($confirmer->first_name ?? '') . ' ' . ($confirmer->last_name ?? '')) ?: ($confirmer->username ?? null))
                                    : null;
                                $isDokuPaid = !$confirmerName
                                    && in_array(strtolower($rawType ?? ''), ['qris', 'credit_card', 'transfer va', 'mandiri', 'bri', 'bsi', 'cimb', 'btn', 'danamon'], true);
                                $confirmerDisplay = $confirmerName ?: ($isDokuPaid ? 'DOKU' : null);
                                $paidAt = $booking->transaction->paid_at ?? null;
                            @endphp
                            <div class="mt-2 w-full px-2 text-center">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">{{ __('ui.allbookings_paid_method') }}</span>
                                    <span>{{ $paidMethod ?: '—' }}</span>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">{{ __('ui.allbookings_payment_confirmed_by') }}</span>
                                    <span>{{ $confirmerDisplay ?: '—' }}</span>
                                </div>
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">{{ __('ui.allbookings_payment_at') }}</span>
                                    <span>{{ $paidAt ? \Carbon\Carbon::parse($paidAt)->format('Y-m-d H:i') : '—' }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- Expired bookings never reached the paid state, so only the chosen payment method
                             is meaningful (no confirmer, no paid_at). Render a single line so admins can still
                             tell at a glance which channel the guest was attempting to use. --}}
                        @if ($booking->status === 'Expired' && $paidMethod)
                            <div class="mt-2 w-full px-2 text-center">
                                <div class="text-xs text-gray-600">
                                    <span class="font-semibold">{{ __('ui.allbookings_paid_method') }}</span>
                                    <span>{{ $paidMethod }}</span>
                                </div>
                            </div>
                        @endif

                        @if ($booking->status === 'Rejected')
                            {{-- Show when + who rejected (from t_payment.verified_at / verified_by) --}}
                            <div class="mt-2 w-full px-2 text-center">
                                @if ($booking->payment && $booking->payment->verified_at)
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking->payment->verified_at)->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                                @if ($booking->payment && $booking->payment->verifiedBy)
                                    <div class="text-xs text-gray-700 font-medium">
                                        {{ trim(($booking->payment->verifiedBy->first_name ?? '') . ' ' . ($booking->payment->verifiedBy->last_name ?? '')) ?: $booking->payment->verifiedBy->username }}
                                    </div>
                                @endif
                                @if ($booking->payment && $booking->payment->notes)
                                    <div class="text-xs text-gray-600 mt-1 italic">{{ $booking->payment->notes }}</div>
                                @endif
                            </div>
                        @endif

                        @if ($booking->status === 'Canceled')
                            <div class="mt-2 w-full px-2 text-center">
                                {{-- Show when + who cancelled (cancel_at from transaction, requested_by from refund) --}}
                                @if ($booking->transaction && $booking->transaction->cancel_at)
                                    <div class="text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($booking->transaction->cancel_at)->format('Y-m-d H:i') }}
                                    </div>
                                @endif
                                @if ($booking->refund && $booking->refund->requestedBy)
                                    <div class="text-xs text-gray-700 font-medium">
                                        {{ trim(($booking->refund->requestedBy->first_name ?? '') . ' ' . ($booking->refund->requestedBy->last_name ?? '')) ?: $booking->refund->requestedBy->username }}
                                    </div>
                                @endif

                                @if ($booking->reason)
                                    <!-- Cancellation reason label translated via ui lang file -->
                                    <div class="text-xs text-gray-700 font-medium mt-1">
                                        <span class="font-semibold">{{ __('ui.allbookings_reason') }}</span>
                                        <span class="inline-block">{{ $booking->reason }}</span>
                                    </div>
                                @endif

                                @if ($booking->description)
                                    <!-- Cancellation description label translated via ui lang file -->
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">{{ __('ui.allbookings_description') }}</span>
                                        <span class="inline-block">{{ $booking->description }}</span>
                                    </div>
                                @endif

                                @if ($booking->refund)
                                    <!-- Refund status label translated via ui lang file -->
                                    <div class="text-xs text-gray-600 mt-1">
                                        <span class="font-semibold">{{ __('ui.allbookings_refund_status') }}</span>
                                        <span
                                            class="px-2 py-0.5 rounded-full inline-block
                {{ $booking->refund->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($booking->refund->status) }}
                                        </span>
                                    </div>

                                    @if ($booking->refund->amount)
                                        <!-- Refund amount label translated via ui lang file -->
                                        <div class="text-xs text-gray-600 mt-1">
                                            <span class="font-semibold">{{ __('ui.allbookings_refund_amount') }}</span>
                                            <span class="inline-block">Rp
                                                {{ number_format($booking->refund->amount, 0, ',', '.') }}</span>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endif

                    </div>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    {{ __('ui.allbookings_no_data') }}
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
