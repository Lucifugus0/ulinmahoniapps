{{-- Customer Service Tickets Page — full-page ticket management for web customers.
     Split layout: ticket list (left) + chat/detail panel (right).
     Uses Alpine.js + fetch() to interact with /api/v1/tickets endpoints.
     Includes Tailwind CDN for styling consistency with public pages. --}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Customer Service - Ulin Mahoni</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen">

    {{-- Top navigation bar --}}
    <nav class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <a href="/" class="text-teal-600 dark:text-teal-400 font-bold text-lg">Ulin Mahoni</a>
            <span class="text-gray-300 dark:text-gray-600">|</span>
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">
                <i class="fas fa-headset mr-1"></i>Customer Service
            </span>
        </div>
        <div class="flex items-center gap-3">
            <a href="/bookings" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <i class="fas fa-calendar mr-1"></i>My Bookings
            </a>
            <a href="/" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <i class="fas fa-home mr-1"></i>Home
            </a>
        </div>
    </nav>

    {{-- Main content --}}
    <div x-data="ticketsPage()" class="flex h-[calc(100vh-52px)]">

        {{-- LEFT PANEL: Ticket List --}}
        <div class="w-full md:w-2/5 lg:w-1/3 border-r border-gray-200 dark:border-gray-800 flex flex-col bg-white dark:bg-gray-900"
             :class="{ 'hidden md:flex': activeTicketId }">

            {{-- Header --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-800">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold"><i class="fas fa-ticket-alt mr-2 text-teal-500"></i>My Tickets</h2>
                    <button @click="showCreateModal = true"
                            class="text-xs px-3 py-1.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i>New Ticket
                    </button>
                </div>

                {{-- Status filter tabs --}}
                <div class="flex gap-1 text-xs">
                    <button @click="statusFilter = ''; fetchTickets()"
                            :class="statusFilter === '' ? 'bg-teal-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-lg transition-colors">All</button>
                    <button @click="statusFilter = 'open'; fetchTickets()"
                            :class="statusFilter === 'open' ? 'bg-green-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-lg transition-colors">Open</button>
                    <button @click="statusFilter = 'in_progress'; fetchTickets()"
                            :class="statusFilter === 'in_progress' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-lg transition-colors">In Progress</button>
                    <button @click="statusFilter = 'closed'; fetchTickets()"
                            :class="statusFilter === 'closed' ? 'bg-gray-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                            class="px-3 py-1.5 rounded-lg transition-colors">Closed</button>
                </div>
            </div>

            {{-- Ticket list --}}
            <div class="flex-1 overflow-y-auto">
                <div x-show="loadingList" class="p-8 text-center"><i class="fas fa-spinner fa-spin text-teal-500 text-xl"></i></div>
                <template x-for="ticket in tickets" :key="ticket.id">
                    <div @click="selectTicket(ticket.id)"
                         class="p-4 border-b border-gray-100 dark:border-gray-800 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors"
                         :class="{ 'bg-teal-50 dark:bg-teal-900/20 border-l-4 border-l-teal-500': activeTicketId === ticket.id }">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="text-xs font-mono font-bold text-teal-600 dark:text-teal-400" x-text="ticket.ticket_number"></span>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full" :class="statusClass(ticket.ticket_status)" x-text="formatStatus(ticket.ticket_status)"></span>
                                </div>
                                <p class="text-sm font-medium truncate" x-text="ticket.subject"></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="(ticket.category?.label_en || '') + (ticket.property?.name ? ' • ' + ticket.property.name : '')"></p>
                            </div>
                            <div class="flex flex-col items-end ml-2 shrink-0">
                                <span class="text-xs text-gray-400" x-text="formatTime(ticket.last_message_at || ticket.created_at)"></span>
                                <span x-show="ticket.unread_count > 0" class="mt-1 w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full flex items-center justify-center" x-text="ticket.unread_count"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="!loadingList && tickets.length === 0" class="p-8 text-center text-gray-400">
                    <i class="fas fa-ticket-alt text-4xl mb-3"></i>
                    <p class="text-sm">No tickets yet</p>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL: Chat --}}
        <div class="flex-1 flex flex-col bg-gray-50 dark:bg-gray-950"
             :class="{ 'hidden md:flex': !activeTicketId }">

            {{-- Empty state --}}
            <div x-show="!activeTicketId" class="flex-1 flex items-center justify-center">
                <div class="text-center text-gray-400"><i class="fas fa-comments text-5xl mb-3"></i><p>Select a ticket to start chatting</p></div>
            </div>

            {{-- Active chat --}}
            <div x-show="activeTicketId" x-cloak class="flex flex-col h-full">
                {{-- Chat header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 flex items-center justify-between">
                    <div>
                        <button @click="activeTicketId = null" class="md:hidden mr-2 text-gray-500"><i class="fas fa-arrow-left"></i></button>
                        <span class="font-mono font-bold text-teal-600 dark:text-teal-400" x-text="currentTicket?.ticket_number"></span>
                        <span class="text-xs px-2 py-0.5 rounded-full ml-1" :class="statusClass(currentTicket?.ticket_status)" x-text="formatStatus(currentTicket?.ticket_status)"></span>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-0.5" x-text="currentTicket?.subject"></p>
                    </div>
                    <div class="flex gap-2">
                        <button x-show="currentTicket?.ticket_status !== 'closed'" @click="closeCurrentTicket()"
                                class="text-xs px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900 transition-colors">Close</button>
                        <button x-show="currentTicket?.ticket_status === 'closed' && canReopen" @click="reopenCurrentTicket()"
                                class="text-xs px-3 py-1.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-300 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-900 transition-colors">Reopen</button>
                    </div>
                </div>

                {{-- Messages --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-2" id="webMessagesContainer">
                    <div x-show="loadingMessages" class="text-center py-8"><i class="fas fa-spinner fa-spin text-teal-500 text-xl"></i></div>
                    <template x-for="msg in messages" :key="msg.id">
                        <div>
                            {{-- System message --}}
                            <div x-show="msg.message_type === 'system'" class="flex justify-center my-2">
                                <span class="text-xs text-gray-500 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full" x-text="msg.message_text"></span>
                            </div>
                            {{-- User/staff message --}}
                            <div x-show="msg.message_type !== 'system'" class="flex" :class="msg.sender_id == userId ? 'justify-end' : 'justify-start'">
                                <div class="max-w-[70%]">
                                    <p class="text-xs mb-0.5" :class="msg.sender_id == userId ? 'text-right text-teal-400' : 'text-gray-400'" x-text="msg.sender?.first_name || msg.sender?.name || 'User'"></p>
                                    <div class="rounded-xl px-3 py-2 shadow-sm" :class="msg.sender_id == userId ? 'bg-teal-600 text-white' : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100'">
                                        <template x-if="msg.attachments && msg.attachments.length > 0">
                                            <template x-for="att in msg.attachments" :key="att.id">
                                                <a :href="att.file_url" target="_blank"><img :src="att.thumbnail_url || att.file_url" class="max-w-[250px] rounded-lg mb-1" loading="lazy"></a>
                                            </template>
                                        </template>
                                        <p x-show="msg.message_text" class="text-sm whitespace-pre-wrap" x-text="msg.message_text"></p>
                                        <p class="text-xs mt-1 opacity-60" x-text="formatMsgTime(msg.created_at)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Input area --}}
                <div x-show="currentTicket?.ticket_status !== 'closed'" class="p-4 border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="flex items-end gap-2">
                        <input type="text" x-model="newMessage" @keydown.enter="sendMessage()"
                               placeholder="Type a message..." class="flex-1 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 focus:ring-teal-500 focus:border-teal-500">
                        <label class="cursor-pointer text-gray-400 hover:text-teal-500 p-2">
                            <i class="fas fa-image text-lg"></i>
                            <input type="file" class="hidden" accept="image/*" @change="sendImageFile($event)">
                        </label>
                        <button @click="sendMessage()" :disabled="sending || !newMessage.trim()"
                                class="px-4 py-2.5 bg-teal-600 text-white rounded-lg text-sm hover:bg-teal-700 disabled:opacity-50 transition-colors">
                            <i class="fas fa-paper-plane" :class="{'fa-spinner fa-spin': sending}"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Create Ticket Modal --}}
        <div x-show="showCreateModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
             @click.self="showCreateModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md mx-4 p-6"
                 @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-plus-circle text-teal-500 mr-2"></i>New Ticket
                    </h3>
                    <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form @submit.prevent="submitTicket()">
                    {{-- Category --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category *</label>
                        <select x-model="newTicket.category_id" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            <option value="">Select category...</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option :value="cat.id" x-text="cat.label_en"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Subject --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject *</label>
                        <input type="text" x-model="newTicket.subject" required maxlength="255"
                               placeholder="Brief description of your issue"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>

                    {{-- Message --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
                        <textarea x-model="newTicket.initial_message" rows="3" maxlength="5000"
                                  placeholder="Describe your issue in detail..."
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm bg-white dark:bg-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-teal-500 focus:border-teal-500 resize-none"></textarea>
                    </div>

                    {{-- Error --}}
                    <p x-show="createError" x-text="createError" class="text-red-500 text-xs mb-3"></p>

                    {{-- Submit --}}
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="showCreateModal = false"
                                class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                            Cancel
                        </button>
                        <button type="submit" :disabled="creatingTicket"
                                class="px-4 py-2 text-sm bg-teal-600 text-white rounded-lg hover:bg-teal-700 disabled:opacity-50 transition-colors">
                            <span x-show="!creatingTicket">Create Ticket</span>
                            <span x-show="creatingTicket"><i class="fas fa-spinner fa-spin mr-1"></i>Creating...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    /** Alpine.js component for the full-page ticket management interface */
    function ticketsPage() {
        return {
            userId: {{ auth()->id() ?? 'null' }},
            apiKey: '{{ env("API_KEY", "") }}',
            tickets: [],
            messages: [],
            currentTicket: null,
            canReopen: false,
            activeTicketId: null,
            newMessage: '',
            statusFilter: '',
            loadingList: false,
            loadingMessages: false,
            sending: false,
            showCreateModal: false,
            categories: [],
            newTicket: { category_id: '', subject: '', initial_message: '' },
            creatingTicket: false,
            createError: '',
            pollingInterval: null,

            init() {
                this.fetchTickets();
                this.fetchCategories();
                /** Check URL for ?open= param */
                const params = new URLSearchParams(window.location.search);
                const openId = params.get('open');
                if (openId) this.selectTicket(parseInt(openId));
                /** Poll every 10 seconds */
                this.pollingInterval = setInterval(() => {
                    if (this.activeTicketId) this.refreshMessages();
                }, 10000);
            },

            /** Fetch ticket categories for the create modal dropdown */
            async fetchCategories() {
                try {
                    const r = await fetch('/api/v1/tickets/categories', { headers: { 'Accept': 'application/json', 'x-api-key': this.apiKey } });
                    const d = await r.json();
                    if (d.status === 'success') this.categories = d.data || [];
                } catch (e) { console.error('Failed to load categories:', e); }
            },

            /** Submit a new ticket via the API */
            async submitTicket() {
                this.createError = '';
                this.creatingTicket = true;
                try {
                    const r = await fetch('/api/v1/tickets', {
                        method: 'POST',
                        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'x-api-key': this.apiKey },
                        body: JSON.stringify({ user_id: this.userId, ...this.newTicket })
                    });
                    const d = await r.json();
                    if (d.status === 'success') {
                        this.showCreateModal = false;
                        this.newTicket = { category_id: '', subject: '', initial_message: '' };
                        await this.fetchTickets();
                        if (d.data?.id) this.selectTicket(d.data.id);
                    } else {
                        this.createError = d.message || 'Failed to create ticket';
                    }
                } catch (e) {
                    this.createError = 'Network error. Please try again.';
                    console.error(e);
                }
                this.creatingTicket = false;
            },

            async fetchTickets() {
                this.loadingList = true;
                try {
                    const params = new URLSearchParams({ user_id: this.userId });
                    if (this.statusFilter) params.set('status', this.statusFilter);
                    const r = await fetch(`/api/v1/tickets?${params}`, { headers: { 'Accept': 'application/json', 'x-api-key': this.apiKey } });
                    const d = await r.json();
                    if (d.status === 'success') this.tickets = d.data || [];
                } catch (e) { console.error(e); }
                this.loadingList = false;
            },

            async selectTicket(ticketId) {
                this.activeTicketId = ticketId;
                this.loadingMessages = true;
                try {
                    const r = await fetch(`/api/v1/tickets/${ticketId}?user_id=${this.userId}`, { headers: { 'Accept': 'application/json', 'x-api-key': this.apiKey } });
                    const d = await r.json();
                    if (d.status === 'success') {
                        this.currentTicket = d.data.ticket;
                        this.messages = d.data.messages || [];
                        this.canReopen = d.data.can_reopen;
                        this.$nextTick(() => { const c = document.getElementById('webMessagesContainer'); if (c) c.scrollTop = c.scrollHeight; });
                    }
                } catch (e) { console.error(e); }
                this.loadingMessages = false;
            },

            async sendMessage() {
                if (this.sending || !this.newMessage.trim()) return;
                this.sending = true;
                try {
                    const r = await fetch(`/api/v1/tickets/${this.activeTicketId}/messages`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'x-api-key': this.apiKey },
                        body: JSON.stringify({ user_id: this.userId, message_text: this.newMessage.trim() }),
                    });
                    const d = await r.json();
                    if (d.status === 'success') { this.messages.push(d.data); this.newMessage = ''; this.scrollToBottom(); }
                } catch (e) { console.error(e); }
                this.sending = false;
            },

            async sendImageFile(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.sending = true;
                try {
                    const fd = new FormData();
                    fd.append('user_id', this.userId);
                    fd.append('image', file);
                    const r = await fetch(`/api/v1/tickets/${this.activeTicketId}/messages`, {
                        method: 'POST', headers: { 'Accept': 'application/json', 'x-api-key': this.apiKey }, body: fd,
                    });
                    const d = await r.json();
                    if (d.status === 'success') { this.messages.push(d.data); this.scrollToBottom(); }
                } catch (e) { console.error(e); }
                this.sending = false;
                event.target.value = '';
            },

            async closeCurrentTicket() {
                if (!confirm('Close this ticket?')) return;
                try {
                    const r = await fetch(`/api/v1/tickets/${this.activeTicketId}/close`, {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'x-api-key': this.apiKey },
                        body: JSON.stringify({ user_id: this.userId }),
                    });
                    const d = await r.json();
                    if (d.status === 'success') { this.selectTicket(this.activeTicketId); this.fetchTickets(); }
                } catch (e) { console.error(e); }
            },

            async reopenCurrentTicket() {
                try {
                    const r = await fetch(`/api/v1/tickets/${this.activeTicketId}/reopen`, {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'x-api-key': this.apiKey },
                        body: JSON.stringify({ user_id: this.userId }),
                    });
                    const d = await r.json();
                    if (d.status === 'success') { this.selectTicket(this.activeTicketId); this.fetchTickets(); }
                } catch (e) { console.error(e); }
            },

            async refreshMessages() {
                if (!this.activeTicketId) return;
                try {
                    const r = await fetch(`/api/v1/tickets/${this.activeTicketId}?user_id=${this.userId}`, { headers: { 'Accept': 'application/json', 'x-api-key': this.apiKey } });
                    const d = await r.json();
                    if (d.status === 'success' && d.data.messages.length !== this.messages.length) {
                        this.messages = d.data.messages; this.currentTicket = d.data.ticket; this.canReopen = d.data.can_reopen; this.scrollToBottom();
                    }
                } catch (e) {}
            },

            scrollToBottom() { this.$nextTick(() => { const c = document.getElementById('webMessagesContainer'); if (c) c.scrollTop = c.scrollHeight; }); },
            statusClass(s) { return { open: 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300', in_progress: 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300', closed: 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400', reopened: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' }[s] || ''; },
            formatStatus(s) { return s ? s.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : ''; },
            formatTime(d) { if (!d) return ''; const s = Math.floor((Date.now() - new Date(d)) / 1000); if (s < 60) return 'now'; if (s < 3600) return Math.floor(s/60) + 'm'; if (s < 86400) return Math.floor(s/3600) + 'h'; return Math.floor(s/86400) + 'd'; },
            formatMsgTime(d) { if (!d) return ''; const dt = new Date(d); return dt.toLocaleString('en-GB', { day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit' }); },
        };
    }
    </script>
</body>
</html>
