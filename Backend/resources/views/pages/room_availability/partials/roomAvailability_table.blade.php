<!-- Room availability table with client-side sorting and dark mode via html.dark CSS overrides -->
<!-- Sorting: Alpine.js sorts rows by data-* attributes. Default: Property asc, Room asc -->
<!-- Action column: only visible to admin_tsno@gmail.com -->
@php
    /* New layout: Room | Property | Status | Booking Reference */
    $colCount = 4;
@endphp
<div class="room-avail-table bg-white rounded-xl shadow-sm border border-gray-200"
    x-data="{
        sortColumn: 'property',
        sortDirection: 'asc',
        /* Sort table rows by column. Toggles direction if same column clicked again */
        sortTable(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            this.performSort();
        },
        /* Re-order DOM rows based on sortColumn and sortDirection */
        performSort() {
            const tbody = this.$el.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr[data-sortable]'));
            const dir = this.sortDirection === 'asc' ? 1 : -1;
            const col = this.sortColumn;
            rows.sort((a, b) => {
                let aVal = (a.dataset[col] || '').toLowerCase();
                let bVal = (b.dataset[col] || '').toLowerCase();
                /* For status, sort numerically (0=available first when asc) */
                if (col === 'status') {
                    return (parseInt(aVal) - parseInt(bVal)) * dir;
                }
                let cmp = aVal.localeCompare(bVal) * dir;
                /* Secondary sort: property->room or room->property */
                if (cmp === 0) {
                    let secKey = col === 'property' ? 'room' : 'property';
                    let aS = (a.dataset[secKey] || '').toLowerCase();
                    let bS = (b.dataset[secKey] || '').toLowerCase();
                    cmp = aS.localeCompare(bS);
                }
                return cmp;
            });
            rows.forEach(row => tbody.appendChild(row));
        },
        /* Apply default sort on init */
        init() { this.performSort(); }
    }">
    {{-- Legend for the Booking Reference badge colours, shown above the table.
         Lives inside the partial so it survives AJAX refreshes. --}}
    <div class="booking-ref-legend px-6 py-3 border-b border-gray-100 flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs">
        <span class="font-semibold text-gray-500 uppercase tracking-wider">{{ __('ui.booking_reference') }}:</span>
        <span class="booking-ref-badge booking-ref-badge--current px-2 py-0.5 rounded ring-1 ring-inset">{{ __('ui.booking_ref_legend_active') }}</span>
        <span class="booking-ref-badge booking-ref-badge--soon px-2 py-0.5 rounded ring-1 ring-inset">{{ __('ui.booking_ref_legend_soon') }}</span>
        <span class="booking-ref-badge booking-ref-badge--overdue px-2 py-0.5 rounded ring-1 ring-inset">{{ __('ui.booking_ref_legend_overdue') }}</span>
        <span class="booking-ref-badge booking-ref-badge--future px-2 py-0.5 rounded ring-1 ring-inset">{{ __('ui.booking_ref_legend_upcoming') }}</span>
    </div>
    <div class="overflow-x-auto" style="overflow-y: visible;">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-gray-50 to-slate-100">
                <tr>
                    <!-- Sortable Room column header -->
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                        @click="sortTable('room')">
                        <div class="flex items-center gap-1">
                            {{ __('ui.room') }}
                            <!-- Sort direction indicator -->
                            <template x-if="sortColumn === 'room' && sortDirection === 'asc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg></template>
                            <template x-if="sortColumn === 'room' && sortDirection === 'desc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg></template>
                            <template x-if="sortColumn !== 'room'"><svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg></template>
                        </div>
                    </th>
                    <!-- Sortable Property column header -->
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                        @click="sortTable('property')">
                        <div class="flex items-center gap-1">
                            {{ __('ui.property') }}
                            <template x-if="sortColumn === 'property' && sortDirection === 'asc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg></template>
                            <template x-if="sortColumn === 'property' && sortDirection === 'desc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg></template>
                            <template x-if="sortColumn !== 'property'"><svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg></template>
                        </div>
                    </th>
                    <!-- Sortable Status column header -->
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100 transition-colors"
                        @click="sortTable('status')">
                        <div class="flex items-center gap-1">
                            {{ __('ui.status') }}
                            <template x-if="sortColumn === 'status' && sortDirection === 'asc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5.293 9.707l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 7.414l-3.293 3.293a1 1 0 01-1.414-1.414z"/></svg></template>
                            <template x-if="sortColumn === 'status' && sortDirection === 'desc'"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M14.707 10.293l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 12.586l3.293-3.293a1 1 0 111.414 1.414z"/></svg></template>
                            <template x-if="sortColumn !== 'status'"><svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M7 8l3-3 3 3m0 4l-3 3-3-3"/></svg></template>
                        </div>
                    </th>
                    <!-- Booking Reference (not sortable): inline badges, blue=current, green=future -->
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('ui.booking_reference') }}
                    </th>
                    {{-- Action column removed — room availability is system-managed --}}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100 no-backdrop-filter">
                @forelse($rooms as $index => $room)
                    <!-- Each row has data-* attributes for client-side sorting -->
                    <tr class="hover:bg-blue-50/30 transition-all duration-200 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}"
                        data-sortable
                        data-property="{{ strtolower($room->property->name ?? '') }}"
                        data-room="{{ strtolower($room->no ?? '') }}"
                        data-status="{{ $room->rental_status }}">
                        <!-- Room column: Room Number (plain text), Room Type (badge) | Capacity (badge) -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-4">
                                <div class="flex-shrink-0 h-12 w-12 relative group">
                                    @if ($room->thumbnail)
                                        <img class="h-12 w-12 rounded-xl object-cover shadow-sm ring-2 ring-gray-100 group-hover:ring-blue-200 transition-all duration-200"
                                            src="{{ asset('storage/' . $room->thumbnail->image) }}"
                                            alt="{{ $room->name }}">
                                    @else
                                        <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-sm">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <!-- Room number as plain text -->
                                    <div class="text-sm font-semibold text-gray-900">No. {{ $room->no }}</div>
                                    <!-- Room Type badge | Capacity badge -->
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium capitalize">{{ $room->name }}</span>
                                        <span class="text-gray-300">|</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium">
                                            <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ $room->capacity }} {{ __('ui.person') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Property -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $room->property->name ?? '-' }}</div>
                            <div class="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $room->property->province ?? '-' }}
                            </div>
                        </td>

                        <!-- Status + Rent Type -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($room->rental_status == 1)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300 ring-1 ring-inset ring-rose-200 dark:ring-rose-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                                    {{ __('ui.occupied') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 ring-1 ring-inset ring-emerald-200 dark:ring-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    {{ __('ui.available') }}
                                </span>
                            @endif
                            {{-- Rent type from master room --}}
                            <div class="mt-1">
                                @if($room->periode_daily && $room->periode_monthly)
                                    <span class="text-xs text-gray-500">Daily & Monthly</span>
                                @elseif($room->periode_daily)
                                    <span class="text-xs text-gray-500">Daily</span>
                                @elseif($room->periode_monthly)
                                    <span class="text-xs text-gray-500">Monthly</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </div>
                        </td>

                        <!-- Booking Reference: one inline badge per paid booking
                             - blue badge = currently checked in (check_in_at NOT NULL, check_out_at NULL)
                             - green badge = future paid booking (check_in_at NULL, scheduled check_in >= today)
                             Each badge: line 1 customer first+last name, line 2 stay period -->
                        <td class="px-6 py-4 align-top">
                            @php
                                $today = \Carbon\Carbon::today();

                                // Eager-load already filters: status=1 + transaction.transaction_status=paid + renewal_status=0
                                // so we only need to split by physical-state here.
                                $currentBookings = $room->bookings->filter(function ($b) {
                                    return $b->transaction
                                        && $b->getRawOriginal('status') == 1
                                        && $b->check_in_at && !$b->check_out_at;
                                });

                                $futureBookings = $room->bookings->filter(function ($b) use ($today) {
                                    if (!$b->transaction) return false;
                                    if ($b->getRawOriginal('status') != 1) return false;
                                    if ($b->check_in_at) return false; // not yet physically checked in
                                    return \Carbon\Carbon::parse($b->transaction->check_in)->startOfDay()->gte($today);
                                })->sortBy(fn ($b) => $b->transaction->check_in);

                                /* Resolve customer display name in priority:
                                   1. account name — users.first_name + last_name (when the booking
                                      has a linked user account, this is the authoritative full name)
                                   2. check-in name — t_booking.user_name (entered on the booking row,
                                      which is what the admin sees during check-in)
                                   3. transaction name — t_transactions.user_name (last-resort fallback)
                                   Returns empty string when all three are blank — no "Unknown" placeholder. */
                                $resolveName = function ($b) {
                                    $u = $b->transaction->user ?? null;
                                    $name = $u ? trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) : '';
                                    if ($name === '') $name = trim($b->user_name ?? '');
                                    if ($name === '') $name = trim($b->transaction->user_name ?? '');
                                    return $name;
                                };

                                /* Decide which colour modifier to use for a current (checked-in) badge,
                                   based on how close the SCHEDULED check_out date is to today:
                                   - --overdue (red):   check_out is today or in the past
                                   - --soon    (yellow): check_out is in the next 1–3 days (D-1 / D-2 / D-3)
                                   - --current (blue):  check_out is more than 3 days away */
                                $currentBadgeClass = function ($b) use ($today) {
                                    $checkOut = \Carbon\Carbon::parse($b->transaction->check_out)->startOfDay();
                                    if ($checkOut->lte($today)) return 'booking-ref-badge--overdue';
                                    if ($checkOut->lte($today->copy()->addDays(3))) return 'booking-ref-badge--soon';
                                    return 'booking-ref-badge--current';
                                };
                            @endphp

                            @if ($currentBookings->isEmpty() && $futureBookings->isEmpty())
                                <span class="inline-flex items-center gap-1.5 text-sm text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                    </svg>
                                    {{ __('ui.no_bookings') }}
                                </span>
                            @else
                                <div class="flex flex-col gap-1.5">
                                    @foreach ($currentBookings as $b)
                                        <div class="booking-ref-badge {{ $currentBadgeClass($b) }} inline-flex flex-col px-3 py-1.5 rounded-lg ring-1 ring-inset">
                                            <span class="text-sm font-semibold">{{ $resolveName($b) }}</span>
                                            <span class="text-xs opacity-90">
                                                {{ \Carbon\Carbon::parse($b->transaction->check_in)->format('d M Y') }}
                                                →
                                                {{ \Carbon\Carbon::parse($b->transaction->check_out)->format('d M Y') }}
                                            </span>
                                            {{-- Row 3: actual admin-recorded check-in datetime (only set when guest is currently in the room) --}}
                                            @if ($b->check_in_at)
                                                <span class="text-xs opacity-80">
                                                    {{ __('ui.allbookings_checkin_at') }} {{ \Carbon\Carbon::parse($b->check_in_at)->format('d M Y H:i') }}
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                    @foreach ($futureBookings as $b)
                                        <div class="booking-ref-badge booking-ref-badge--future inline-flex flex-col px-3 py-1.5 rounded-lg ring-1 ring-inset">
                                            <span class="text-sm font-semibold">{{ $resolveName($b) }}</span>
                                            <span class="text-xs opacity-90">
                                                {{ \Carbon\Carbon::parse($b->transaction->check_in)->format('d M Y') }}
                                                →
                                                {{ \Carbon\Carbon::parse($b->transaction->check_out)->format('d M Y') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        {{-- Action column removed — room availability is system-managed --}}
                    </tr>
                @empty
                    <tr>
                        <!-- Dynamic colspan based on whether Action column is shown -->
                        <td colspan="{{ $colCount }}" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ __('ui.no_room_data') }}</h3>
                                <p class="text-gray-500 text-sm">{{ __('ui.no_room_found_filter') }}</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Dark mode overrides for room availability table and modal -->
<style>
    /* Legend strip above the table */
    .booking-ref-legend { background-color: #f8fafc; }
    html.dark .booking-ref-legend { background-color: #0f172a !important; border-color: #334155 !important; }
    html.dark .booking-ref-legend > span:first-child { color: #94a3b8 !important; }

    /* Booking Reference badges — explicit colors so dark mode contrast is reliable
       (avoids partial remapping by the dark .bg-blue-50 override below) */
    .booking-ref-badge--current {
        background-color: #dbeafe;          /* blue-100 */
        color: #1e3a8a;                      /* blue-900 — high contrast on light bg */
        --tw-ring-color: #bfdbfe;           /* ring-blue-200 */
    }
    .booking-ref-badge--future {
        background-color: #d1fae5;          /* emerald-100 */
        color: #065f46;                      /* emerald-800 */
        --tw-ring-color: #a7f3d0;           /* ring-emerald-200 */
    }
    .booking-ref-badge--soon {
        background-color: #fef3c7;          /* amber-100 — D-3 to D-1 warning */
        color: #78350f;                      /* amber-900 */
        --tw-ring-color: #fde68a;           /* ring-amber-200 */
    }
    .booking-ref-badge--overdue {
        background-color: #fee2e2;          /* red-100 — today or overdue */
        color: #7f1d1d;                      /* red-900 */
        --tw-ring-color: #fecaca;           /* ring-red-200 */
    }
    html.dark .booking-ref-badge--current {
        background-color: #1e3a8a !important; /* blue-900 */
        color: #dbeafe !important;            /* blue-100 — light text on dark bg */
        --tw-ring-color: #2563eb !important;  /* ring-blue-600 */
    }
    html.dark .booking-ref-badge--future {
        background-color: #065f46 !important; /* emerald-800 */
        color: #d1fae5 !important;            /* emerald-100 */
        --tw-ring-color: #059669 !important;  /* ring-emerald-600 */
    }
    html.dark .booking-ref-badge--soon {
        background-color: #78350f !important; /* amber-900 */
        color: #fef3c7 !important;            /* amber-100 */
        --tw-ring-color: #d97706 !important;  /* ring-amber-600 */
    }
    html.dark .booking-ref-badge--overdue {
        background-color: #7f1d1d !important; /* red-900 */
        color: #fee2e2 !important;            /* red-100 */
        --tw-ring-color: #dc2626 !important;  /* ring-red-600 */
    }

    /* Table dark mode */
    html.dark .room-avail-table { background-color: #1e293b !important; border-color: #334155 !important; }
    html.dark .room-avail-table table { border-color: #334155 !important; }
    html.dark .room-avail-table thead { background: #334155 !important; }
    html.dark .room-avail-table thead th { color: #cbd5e1 !important; }
    html.dark .room-avail-table thead th:hover { background-color: #475569 !important; }
    html.dark .room-avail-table tbody { background-color: #1e293b !important; }
    html.dark .room-avail-table tbody tr { background-color: #1e293b !important; border-color: #334155 !important; }
    html.dark .room-avail-table tbody tr:nth-child(even) { background-color: #1e293b !important; }
    html.dark .room-avail-table tbody tr:hover { background-color: #334155 !important; }
    html.dark .room-avail-table .text-gray-900 { color: #f1f5f9 !important; }
    html.dark .room-avail-table .text-gray-500 { color: #94a3b8 !important; }
    html.dark .room-avail-table .bg-indigo-50 { background-color: #312e81 !important; }
    html.dark .room-avail-table .text-indigo-700 { color: #a5b4fc !important; }
    html.dark .room-avail-table .bg-blue-50 { background-color: #1e3a5f !important; }
    html.dark .room-avail-table .text-blue-700 { color: #93c5fd !important; }
    html.dark .room-avail-table .divide-y > :not([hidden]) ~ :not([hidden]) { border-color: #334155 !important; }

    /* Modal dark mode: dialog container, header, content, footer, and booking cards */
    html.dark #property-detail-modal .bg-white { background-color: #1e293b !important; }
    html.dark #property-detail-modal .bg-gradient-to-r { background: #1e293b !important; }
    html.dark #property-detail-modal .border-gray-200,
    html.dark #property-detail-modal .border-gray-100 { border-color: #334155 !important; }
    html.dark #property-detail-modal .text-gray-900 { color: #f1f5f9 !important; }
    html.dark #property-detail-modal .text-gray-700 { color: #cbd5e1 !important; }
    html.dark #property-detail-modal .text-gray-500 { color: #94a3b8 !important; }
    html.dark #property-detail-modal .text-gray-400 { color: #64748b !important; }
    html.dark #property-detail-modal .text-gray-600 { color: #94a3b8 !important; }
    html.dark #property-detail-modal .bg-gray-50,
    html.dark #property-detail-modal .bg-gray-50\/50 { background-color: #0f172a !important; }
    html.dark #property-detail-modal .bg-gray-100 { background-color: #334155 !important; }
    html.dark #property-detail-modal .bg-blue-50 { background-color: #1e3a5f !important; }
    html.dark #property-detail-modal .bg-emerald-50 { background-color: #064e3b !important; }
    html.dark #property-detail-modal .bg-white.rounded-lg.shadow-sm { background-color: #334155 !important; }
    /* Invoice number badge: make visible in dark mode */
    html.dark #property-detail-modal .text-\[10px\].font-mono.text-gray-400 { color: #94a3b8 !important; background-color: #334155 !important; }
</style>
