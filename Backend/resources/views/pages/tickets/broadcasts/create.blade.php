{{-- Broadcast Creation Form — admin creates and sends one-way announcements.
     Supports Front Desk (property-scoped) and HQ (all users) broadcasts. --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                <i class="fas fa-bullhorn mr-2 text-indigo-500"></i>{{ __('ui.new_broadcast') ?? 'New Broadcast' }}
            </h1>
            <a href="{{ route('broadcasts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <i class="fas fa-arrow-left mr-1"></i>{{ __('ui.back') ?? 'Back' }}
            </a>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
             x-data="broadcastForm()">
            {{-- novalidate: suppress browser's native HTML5 "Please fill out this field" tooltip; validation is handled by isValid() --}}
            <form @submit.prevent="submitBroadcast()" novalidate>
                {{-- Sender Type --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.sender_type') ?? 'Sender Type' }}</label>
                    <select x-model="senderType" @change="onSenderTypeChange()"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 text-sm">
                        <option value="front_desk">{{ __('ui.front_desk') ?? 'Front Desk (Property)' }}</option>
                        <option value="hq">{{ __('ui.hq_customer_service') ?? 'HQ Customer Service' }}</option>
                    </select>
                </div>

                {{-- Property (shown only for Front Desk) --}}
                <div class="mb-4" x-show="senderType === 'front_desk'">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.property') ?? 'Property' }}</label>
                    <select x-model="propertyId"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 text-sm">
                        <option value="">{{ __('ui.select_property') ?? 'Select Property' }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->idrec }}">{{ $property->name }} ({{ $property->initial }})</option>
                        @endforeach
                    </select>
                </div>

                {{-- Audience --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.audience') ?? 'Target Audience' }}</label>
                    <select x-model="audience"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 text-sm">
                        <template x-if="senderType === 'hq'">
                            <option value="all_users">{{ __('ui.audience_all_users') ?? 'All Users' }}</option>
                        </template>
                        <option value="active_bookings">{{ __('ui.audience_active_bookings') ?? 'Users with Active Bookings' }}</option>
                        <option value="active_and_future_bookings">{{ __('ui.audience_active_future') ?? 'Users with Active & Future Bookings' }}</option>
                    </select>
                </div>

                {{-- Title --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.title') ?? 'Title' }}</label>
                    <input type="text" x-model="title" maxlength="255" required
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 text-sm"
                           placeholder="{{ __('ui.broadcast_title_placeholder') ?? 'e.g., Scheduled Maintenance Notice' }}">
                </div>

                {{-- Message --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('ui.message') ?? 'Message' }}</label>
                    <textarea x-model="messageText" rows="5" maxlength="5000" required
                              class="w-full border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 text-sm resize-none"
                              placeholder="{{ __('ui.broadcast_message_placeholder') ?? 'Type your announcement...' }}"></textarea>
                    <p class="text-xs text-gray-400 mt-1" x-text="messageText.length + '/5000'"></p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('broadcasts.index') }}" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        {{ __('ui.cancel') ?? 'Cancel' }}
                    </a>
                    <button type="submit" :disabled="submitting || !isValid()"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                        <i class="fas fa-paper-plane mr-1" :class="{ 'fa-spinner fa-spin': submitting }"></i>
                        {{ __('ui.send_broadcast') ?? 'Send Broadcast' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    /** Alpine.js component for broadcast creation form */
    function broadcastForm() {
        return {
            senderType: '{{ Auth::user()->isSite() ? "front_desk" : "hq" }}',
            propertyId: '{{ Auth::user()->property_id ?? "" }}',
            audience: 'active_bookings',
            title: '',
            messageText: '',
            submitting: false,

            /** Reset audience options when sender type changes */
            onSenderTypeChange() {
                if (this.senderType === 'front_desk') {
                    this.audience = 'active_bookings';
                }
            },

            /** Validate form before submission */
            isValid() {
                if (!this.title.trim() || !this.messageText.trim()) return false;
                if (this.senderType === 'front_desk' && !this.propertyId) return false;
                return true;
            },

            /** Submit the broadcast via AJAX */
            async submitBroadcast() {
                if (this.submitting || !this.isValid()) return;

                /** Confirmation dialog */
                const confirmed = confirm('{{ __("ui.broadcast_confirm") ?? "Send this broadcast? This action cannot be undone." }}');
                if (!confirmed) return;

                this.submitting = true;
                try {
                    const response = await fetch('{{ route("broadcasts.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            sender_type: this.senderType,
                            property_id: this.senderType === 'front_desk' ? this.propertyId : null,
                            audience: this.audience,
                            title: this.title.trim(),
                            message_text: this.messageText.trim(),
                        }),
                    });

                    const data = await response.json();
                    if (data.success) {
                        Toastify({ text: `Broadcast sent to ${data.recipient_count} users!`, duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#10B981' }).showToast();
                        window.location.href = '{{ route("broadcasts.index") }}';
                    } else {
                        Toastify({ text: data.message || 'Failed to send broadcast', duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#EF4444' }).showToast();
                    }
                } catch (e) {
                    console.error('Broadcast failed:', e);
                    Toastify({ text: 'An error occurred', duration: 3000, gravity: 'top', position: 'right', backgroundColor: '#EF4444' }).showToast();
                } finally {
                    this.submitting = false;
                }
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
