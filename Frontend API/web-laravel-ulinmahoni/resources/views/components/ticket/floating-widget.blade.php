{{-- Floating ticket widget — appears on all public pages for authenticated users.
     Bottom-right chat bubble that expands into a compact ticket panel.
     Uses Alpine.js + fetch() to interact with the ticket API endpoints. --}}
@auth
<div x-data="ticketWidget()" x-cloak
     class="fixed bottom-5 right-5 z-[60]"
     style="pointer-events: none;">

    {{-- Expanded panel --}}
    <div x-show="isOpen" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-3 w-80 sm:w-96 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
         style="pointer-events: auto; max-height: 500px;">

        {{-- Panel header --}}
        <div class="bg-teal-600 dark:bg-teal-700 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2 text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="font-semibold text-sm">Customer Service</span>
            </div>
            <button @click="isOpen = false" class="text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Panel content --}}
        <div class="overflow-y-auto" style="max-height: 420px;">
            {{-- Loading state --}}
            <div x-show="loading" class="p-8 text-center">
                <svg class="animate-spin h-6 w-6 text-teal-500 mx-auto" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
            </div>

            {{-- Ticket list --}}
            <div x-show="!loading && !activeChat">
                {{-- New ticket + View all buttons --}}
                <div class="p-3 border-b border-gray-100 dark:border-gray-800 flex gap-2">
                    <a href="/tickets" class="flex-1 text-center text-xs py-2 px-3 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        View All Tickets
                    </a>
                    <a href="/tickets" class="flex-1 text-center text-xs py-2 px-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        + New Ticket
                    </a>
                </div>

                {{-- Ticket items --}}
                <template x-for="ticket in tickets" :key="ticket.id">
                    <a :href="'/tickets?open=' + ticket.id"
                       class="block px-4 py-3 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400" x-text="ticket.ticket_number"></span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full"
                                          :class="statusClass(ticket.ticket_status)"
                                          x-text="ticket.ticket_status.replace('_', ' ')"></span>
                                </div>
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="ticket.subject"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="ticket.category?.label_en || ''"></p>
                            </div>
                            <div class="flex flex-col items-end ml-2 shrink-0">
                                <span class="text-xs text-gray-400" x-text="formatTime(ticket.last_message_at || ticket.created_at)"></span>
                                <template x-if="ticket.unread_count > 0">
                                    <span class="mt-1 w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full flex items-center justify-center" x-text="ticket.unread_count"></span>
                                </template>
                            </div>
                        </div>
                    </a>
                </template>

                {{-- Empty state --}}
                <div x-show="tickets.length === 0" class="p-6 text-center text-gray-400 dark:text-gray-500">
                    <p class="text-sm">No tickets yet</p>
                    <a href="/tickets" class="text-xs text-teal-600 dark:text-teal-400 hover:underline mt-1 inline-block">Create your first ticket</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating action button --}}
    <button @click="toggleWidget()" style="pointer-events: auto;"
            class="relative w-14 h-14 bg-teal-600 hover:bg-teal-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center ml-auto">
        {{-- Chat icon (when closed) --}}
        <svg x-show="!isOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        {{-- Close icon (when open) --}}
        <svg x-show="isOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
        {{-- Unread badge --}}
        <span x-show="totalUnread > 0 && !isOpen"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center"
              x-text="totalUnread > 9 ? '9+' : totalUnread"></span>
    </button>
</div>

<script>
/** Alpine.js component for the floating ticket widget */
function ticketWidget() {
    return {
        isOpen: false,
        loading: false,
        tickets: [],
        totalUnread: 0,
        activeChat: null,
        userId: {{ auth()->id() ?? 'null' }},
        apiKey: '{{ config("app.api_key", "") }}',

        /** Initialize — fetch tickets on mount and poll every 30 seconds */
        init() {
            if (this.userId) {
                this.fetchTickets();
                setInterval(() => this.fetchTickets(), 30000);
            }
        },

        /** Toggle widget open/closed — fetch fresh data when opening */
        toggleWidget() {
            this.isOpen = !this.isOpen;
            if (this.isOpen && this.userId) {
                this.fetchTickets();
            }
        },

        /** Fetch user's tickets from the API */
        async fetchTickets() {
            if (!this.userId) return;
            try {
                const response = await fetch(`/api/v1/tickets?user_id=${this.userId}`, {
                    headers: {
                        'Accept': 'application/json',
                        'x-api-key': this.apiKey,
                    }
                });
                const data = await response.json();
                if (data.status === 'success') {
                    this.tickets = data.data || [];
                    this.totalUnread = this.tickets.reduce((sum, t) => sum + (t.unread_count || 0), 0);
                }
            } catch (e) {
                console.error('Widget fetch failed:', e);
            }
        },

        /** Get CSS class for ticket status badge */
        statusClass(status) {
            const classes = {
                'open': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                'closed': 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                'reopened': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            };
            return classes[status] || '';
        },

        /** Format timestamp to relative time */
        formatTime(dateStr) {
            if (!dateStr) return '';
            const date = new Date(dateStr);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            if (diff < 604800) return Math.floor(diff / 86400) + 'd ago';
            return date.toLocaleDateString();
        }
    };
}
</script>
@endauth
