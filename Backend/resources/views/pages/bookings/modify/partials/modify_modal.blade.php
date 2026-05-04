{{-- Single shared Modify Booking modal — populated via window.openModifyModal(orderId).
     The Alpine x-data tracks the form state, the original-from-server snapshot, and the
     fetched modification history. Saving POSTs back to modifyBooking.update; on success
     the page reloads (cheaper than re-fetching just this row). --}}
<div id="modifyBookingModal"
     x-data="modifyBookingModal()"
     x-show="open"
     x-cloak
     class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-[70]"
     @click.self="close()"
     @keydown.escape.window="close()">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="relative mx-auto w-full max-w-2xl">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-2xl">
                {{-- Header --}}
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 rounded-t bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('ui.modify_booking_modal_title') }}
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="snapshot.order_id ?? ''"></p>
                    <button type="button" @click="close()"
                            class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 dark:hover:bg-gray-600 hover:text-gray-900 dark:hover:text-white rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" /></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                    {{-- Read-only booking summary. --}}
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 py-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('ui.customer') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="snapshot.guest_name ?? '-'"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 py-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('ui.property') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="(snapshot.property_name ?? '-') + ' · ' + (snapshot.room_name ?? '-') + ' #' + (snapshot.room_no ?? '-')"></span>
                        </div>
                        {{-- Booking duration (contracted) — separate from the editable check_in/check_out
                             dates. Shows "X month(s)" for monthly rentals or "X day(s)" for daily. --}}
                        <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 py-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('ui.payment_details_duration') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="formatDuration(snapshot)"></span>
                        </div>
                        <div class="flex justify-between border-b border-gray-100 dark:border-gray-700 py-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('ui.payment_method') }}</span>
                            <span class="font-medium text-gray-900 dark:text-white" x-text="(snapshot.transaction_type ?? '—').toUpperCase()"></span>
                        </div>
                    </div>

                    {{-- Editable fields. --}}
                    <form @submit.prevent="save()" class="space-y-4">
                        {{-- Date-only inputs. Time portion is fixed server-side (14:00 check-in,
                             12:00 check-out) so admins can't accidentally shift the property's
                             standard arrival / departure times. --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('ui.modify_booking_field_check_in') }} <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal">(14:00)</span>
                            </label>
                            <input type="date" x-model="form.check_in" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('ui.modify_booking_field_check_out') }} <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal">(12:00)</span>
                            </label>
                            <input type="date" x-model="form.check_out" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('ui.modify_booking_field_paid_at') }}
                            </label>
                            <input type="datetime-local" x-model="form.paid_at"
                                   :disabled="!snapshot.paid_at_editable"
                                   :class="snapshot.paid_at_editable ? '' : 'opacity-60 cursor-not-allowed'"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1" x-show="!snapshot.paid_at_editable">
                                {{ __('ui.modify_booking_paid_at_locked_hint') }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('ui.modify_booking_field_notes') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea x-model="form.modification_notes" rows="3" minlength="20" maxlength="500" required
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                      placeholder="{{ __('ui.modify_booking_notes_placeholder') }}"></textarea>
                            <p class="mt-1 text-xs"
                               :class="(form.modification_notes ?? '').length < 20 ? 'text-red-500' : 'text-gray-500'">
                                <span x-text="(form.modification_notes ?? '').length"></span> / 20
                                {{ __('ui.modify_booking_notes_min_hint') }}
                            </p>
                        </div>
                    </form>

                    {{-- Modification history panel. --}}
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2 uppercase tracking-wide">
                            {{ __('ui.modify_booking_history') }}
                        </h4>
                        <template x-if="snapshot.history && snapshot.history.length > 0">
                            <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                                <template x-for="(entry, i) in snapshot.history" :key="i">
                                    <div class="text-xs bg-gray-50 dark:bg-gray-700 rounded p-2 border border-gray-200 dark:border-gray-600">
                                        <div class="flex justify-between items-baseline">
                                            <span class="font-medium text-gray-900 dark:text-white" x-text="entry.modified_at"></span>
                                            <span class="text-blue-600 dark:text-blue-300" x-text="entry.modification_type"></span>
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-400 mt-0.5">
                                            <span class="font-semibold">{{ __('ui.allbookings_by') }}</span>
                                            <span x-text="entry.modified_by"></span>
                                        </div>
                                        <template x-if="entry.modification_notes">
                                            <div class="text-gray-500 italic mt-1" x-text="entry.modification_notes"></div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!snapshot.history || snapshot.history.length === 0">
                            <p class="text-xs text-gray-500 italic">{{ __('ui.modify_booking_history_empty') }}</p>
                        </template>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end p-4 space-x-2 border-t border-gray-200 dark:border-gray-700 rounded-b bg-gray-50 dark:bg-gray-700">
                    <button type="button" @click="close()"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        {{ __('ui.modify_booking_cancel') }}
                    </button>
                    <button type="button" @click="save()"
                            :disabled="saving || (form.modification_notes ?? '').trim().length < 20"
                            :title="((form.modification_notes ?? '').trim().length < 20) ? '{{ __('ui.modify_booking_notes_min_hint') }}' : ''"
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center">
                        <svg x-show="saving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        {{ __('ui.modify_booking_save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    /* Alpine component for the Modify Booking modal. window.openModifyModal(orderId) is the
       global entry point — the table row's button calls it; this script picks it up via
       a custom event so the global helper doesn't need a direct reference to the Alpine
       instance. */
    function modifyBookingModal() {
        return {
            open: false,
            saving: false,
            snapshot: { order_id: null, history: [] },
            form: { check_in: '', check_out: '', paid_at: '', modification_notes: '' },
            init() {
                window.addEventListener('modify-booking:open', (e) => this.load(e.detail.orderId));
            },
            /* Render booking duration as "X month(s)" or "X day(s)" based on booking_type.
               Falls back to '—' if duration data is missing. */
            formatDuration(s) {
                if (!s || !s.booking_type) return '—';
                if (s.booking_type === 'monthly' && s.booking_months > 0) {
                    return s.booking_months + ' ' + (s.booking_months === 1 ? 'month' : 'months');
                }
                if (s.booking_type === 'daily' && s.booking_days > 0) {
                    return s.booking_days + ' ' + (s.booking_days === 1 ? 'day' : 'days');
                }
                return '—';
            },
            async load(orderId) {
                this.snapshot = { order_id: orderId, history: [] };
                this.form = { check_in: '', check_out: '', paid_at: '', modification_notes: '' };
                this.open = true;
                try {
                    const res = await fetch(`/bookings/modify-booking/${encodeURIComponent(orderId)}/details`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!res.ok) throw new Error('Failed to load booking');
                    const data = await res.json();
                    this.snapshot = data;
                    this.form.check_in = data.check_in ?? '';
                    this.form.check_out = data.check_out ?? '';
                    this.form.paid_at = data.paid_at ?? '';
                } catch (err) {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load booking details.' });
                    this.open = false;
                }
            },
            close() { this.open = false; },
            async save() {
                if (this.saving) return;
                /* Notes are mandatory + min 20 chars per audit-trail policy. The save button is
                   already disabled when this fails, but defend in depth in case someone bypasses
                   the disabled state via devtools. */
                const notes = (this.form.modification_notes ?? '').trim();
                if (notes.length < 20) {
                    Swal.fire({ icon: 'warning', title: 'Notes required', text: '{{ __('ui.modify_booking_notes_min_hint') }}' });
                    return;
                }
                this.saving = true;
                try {
                    const res = await fetch(`/bookings/modify-booking/${encodeURIComponent(this.snapshot.order_id)}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify(this.form),
                    });
                    const data = await res.json();
                    if (!res.ok || !data.success) {
                        Swal.fire({ icon: 'warning', title: 'Cannot save', text: data.message || '{{ __('ui.modify_booking_save_failed') }}' });
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('ui.modify_booking_save_success') }}',
                        timer: 1500,
                        showConfirmButton: false,
                    }).then(() => window.location.reload());
                } catch (err) {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: '{{ __('ui.modify_booking_save_failed') }}' });
                } finally {
                    this.saving = false;
                }
            },
        };
    }

    // Global entry point used by the row buttons. Decoupled from the Alpine instance via
    // a custom DOM event so this works regardless of when Alpine initializes.
    window.openModifyModal = function (orderId) {
        window.dispatchEvent(new CustomEvent('modify-booking:open', { detail: { orderId } }));
    };
</script>
@endpush
