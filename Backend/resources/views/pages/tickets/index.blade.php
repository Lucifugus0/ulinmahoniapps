{{-- Ticket Management Index — two-panel layout: ticket list (left) + chat detail (right).
     Uses Alpine.js for interactive state management, AJAX for filtering and messaging. --}}
<x-app-layout>
    <div class="flex h-[calc(100vh-64px)]" x-data="ticketManager()">

        {{-- LEFT PANEL: Ticket List --}}
        <div class="w-full md:w-2/5 lg:w-1/3 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-white dark:bg-gray-900">
            {{-- Header with title and broadcast link --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-headset mr-2 text-indigo-500"></i>{{ __('ui.tickets') ?? 'Tickets' }}
                    </h2>
                    <a href="{{ route('broadcasts.index') }}"
                       class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                        <i class="fas fa-bullhorn mr-1"></i>{{ __('ui.broadcasts') ?? 'Broadcasts' }}
                    </a>
                </div>

                {{-- Search --}}
                <div class="relative mb-2">
                    <input type="text"
                           x-model="searchQuery"
                           @input.debounce.300ms="filterTickets()"
                           placeholder="{{ __('ui.ticket_search_placeholder') ?? 'Search tickets...' }}"
                           class="w-full text-sm pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                </div>

                {{-- Filters row --}}
                <div class="flex gap-2">
                    <select x-model="statusFilter" @change="filterTickets()"
                            class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-1.5 px-2">
                        <option value="">{{ __('ui.all_status') ?? 'All Status' }}</option>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="reopened">Reopened</option>
                        <option value="closed">Closed</option>
                    </select>
                    <select x-model="typeFilter" @change="filterTickets()"
                            class="flex-1 text-xs border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 py-1.5 px-2">
                        <option value="">{{ __('ui.all_types') ?? 'All Types' }}</option>
                        <option value="booking">Booking</option>
                        <option value="complaint">Complaint</option>
                        <option value="suggestion">Suggestion</option>
                    </select>
                </div>
            </div>

            {{-- Scrollable ticket list --}}
            <div class="flex-1 overflow-y-auto" id="ticketListContainer">
                <div x-show="loadingList" class="p-8 text-center">
                    <i class="fas fa-spinner fa-spin text-indigo-500 text-2xl"></i>
                </div>
                <div x-show="!loadingList" id="ticketListContent">
                    @include('pages.tickets.partials.ticket-list', ['tickets' => $tickets])
                </div>
            </div>

            {{-- Pagination --}}
            <div class="p-2 border-t border-gray-200 dark:border-gray-700 text-xs" id="ticketPagination">
                {{ $tickets->links() }}
            </div>
        </div>

        {{-- RIGHT PANEL: Ticket Detail / Chat --}}
        <div class="hidden md:flex flex-1 flex-col bg-gray-50 dark:bg-gray-800">
            {{-- Empty state when no ticket selected --}}
            <div x-show="!activeTicketId" class="flex-1 flex items-center justify-center">
                <div class="text-center text-gray-400 dark:text-gray-500">
                    <i class="fas fa-ticket-alt text-5xl mb-4"></i>
                    <p class="text-lg">{{ __('ui.ticket_select_prompt') ?? 'Select a ticket to view' }}</p>
                </div>
            </div>

            {{-- Active ticket view --}}
            <div x-show="activeTicketId" x-cloak class="flex flex-col h-full">
                {{-- Ticket header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400" x-text="currentTicket?.ticket_number"></span>
                                <span class="text-xs px-2 py-0.5 rounded-full"
                                      :class="getStatusClass(currentTicket?.ticket_status)"
                                      x-text="formatStatus(currentTicket?.ticket_status)"></span>
                                <span class="text-xs px-1.5 py-0.5 rounded"
                                      :class="getTypeClass(currentTicket?.category?.ticket_type)"
                                      x-text="currentTicket?.category?.ticket_type"></span>
                            </div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1" x-text="currentTicket?.subject"></p>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500">
                                <span x-text="currentTicket?.user?.first_name || currentTicket?.user?.name || 'User'"></span>
                                <template x-if="currentTicket?.property">
                                    <span>&bull; <span x-text="currentTicket?.property?.name"></span></span>
                                </template>
                                <template x-if="currentTicket?.order_id">
                                    <span>&bull; <span class="font-mono" x-text="currentTicket?.order_id"></span></span>
                                </template>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- Close/Reopen buttons --}}
                            <template x-if="currentTicket?.ticket_status !== 'closed'">
                                <button @click="closeCurrentTicket()"
                                        class="text-xs px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-800 transition-colors">
                                    <i class="fas fa-times-circle mr-1"></i>{{ __('ui.close_ticket') ?? 'Close' }}
                                </button>
                            </template>
                            <template x-if="currentTicket?.ticket_status === 'closed' && currentTicket?.can_reopen">
                                <button @click="reopenCurrentTicket()"
                                        class="text-xs px-3 py-1.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg hover:bg-yellow-200 dark:hover:bg-yellow-800 transition-colors">
                                    <i class="fas fa-redo mr-1"></i>{{ __('ui.reopen_ticket') ?? 'Reopen' }}
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Messages area --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3" id="messagesContainer">
                    <div x-show="loadingMessages" class="flex justify-center py-8">
                        <i class="fas fa-spinner fa-spin text-indigo-500 text-2xl"></i>
                    </div>
                    <div x-show="!loadingMessages" id="messagesList"></div>
                </div>

                {{-- Message input (hidden when ticket is closed) --}}
                <template x-if="currentTicket?.ticket_status !== 'closed'">
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
                        {{-- Image preview --}}
                        <div x-show="selectedImagePreview" class="mb-2 relative inline-block">
                            <img :src="selectedImagePreview" class="h-20 rounded-lg border">
                            <button @click="clearImage()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">
                                &times;
                            </button>
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <textarea x-model="newMessage"
                                          @keydown.enter.prevent="if(!$event.shiftKey) sendCurrentMessage()"
                                          placeholder="{{ __('ui.ticket_type_message') ?? 'Type a message...' }}"
                                          rows="1"
                                          class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 resize-none focus:ring-indigo-500 focus:border-indigo-500"
                                          style="min-height: 40px; max-height: 120px;"></textarea>
                            </div>
                            {{-- Image upload button --}}
                            <label class="cursor-pointer text-gray-400 hover:text-indigo-500 transition-colors p-2">
                                <i class="fas fa-image text-lg"></i>
                                <input type="file" class="hidden" accept="image/jpeg,image/png,image/heic,image/heif"
                                       @change="handleImageSelect($event)">
                            </label>
                            {{-- Send button --}}
                            <button @click="sendCurrentMessage()"
                                    :disabled="sending || (!newMessage.trim() && !selectedImage)"
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fas fa-paper-plane" :class="{ 'fa-spinner fa-spin': sending }"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    /** Alpine.js component for the ticket management two-panel interface */
    function ticketManager() {
        return {
            // State
            activeTicketId: {{ $openTicketId ?? 'null' }},
            currentTicket: null,
            messages: [],
            newMessage: '',
            searchQuery: '',
            statusFilter: '',
            typeFilter: '',
            loadingList: false,
            loadingMessages: false,
            sending: false,
            selectedImage: null,
            selectedImagePreview: null,
            pollingInterval: null,
            currentUserId: {{ Auth::id() }},

            /** Initialize component — load ticket if URL has ?open= param */
            init() {
                if (this.activeTicketId) {
                    this.selectTicket(this.activeTicketId);
                }
                // Poll for new messages every 10 seconds
                this.pollingInterval = setInterval(() => {
                    if (this.activeTicketId) this.refreshMessages();
                }, 10000);
            },

            /** Select a ticket and load its messages */
            async selectTicket(ticketId) {
                this.activeTicketId = ticketId;
                this.loadingMessages = true;

                try {
                    const response = await fetch(`/tickets/${ticketId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.currentTicket = data.ticket;
                        this.currentTicket.can_reopen = this.currentTicket.reopen_deadline
                            ? new Date(this.currentTicket.reopen_deadline) > new Date()
                            : false;
                        this.messages = data.messages;
                        this.renderMessages();
                    }
                } catch (e) {
                    console.error('Failed to load ticket:', e);
                } finally {
                    this.loadingMessages = false;
                }
            },

            /** Render messages as HTML in the messages container */
            renderMessages() {
                const container = document.getElementById('messagesList');
                if (!container) return;
                container.innerHTML = this.buildMessagesHtml();
                this.$nextTick(() => {
                    const mc = document.getElementById('messagesContainer');
                    if (mc) mc.scrollTop = mc.scrollHeight;
                });
            },

            /** Build HTML for all messages */
            buildMessagesHtml() {
                if (!this.messages.length) {
                    return '<div class="text-center text-gray-400 dark:text-gray-500 py-8"><p class="text-sm">No messages yet</p></div>';
                }

                return this.messages.map(msg => {
                    const isMe = msg.sender_id === this.currentUserId;
                    const isSystem = msg.message_type === 'system';
                    const time = new Date(msg.created_at).toLocaleString('en-GB', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' });
                    const senderName = msg.sender?.first_name || msg.sender?.name || 'Unknown';

                    if (isSystem) {
                        return `<div class="flex justify-center my-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">${this.escapeHtml(msg.message_text)} &bull; ${time}</span>
                        </div>`;
                    }

                    // Build attachment HTML
                    let attachmentHtml = '';
                    if (msg.attachments && msg.attachments.length > 0) {
                        msg.attachments.forEach(att => {
                            if (att.file_type && att.file_type.startsWith('image/')) {
                                attachmentHtml += `<a href="${att.file_url}" target="_blank"><img src="${att.thumbnail_url || att.file_url}" class="max-w-xs rounded-lg mt-1 cursor-pointer hover:opacity-90" loading="lazy"></a>`;
                            }
                        });
                    }

                    const align = isMe ? 'justify-end' : 'justify-start';
                    const bubbleBg = isMe ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100';
                    const metaColor = isMe ? 'text-indigo-200' : 'text-gray-400 dark:text-gray-500';

                    return `<div class="flex ${align} mb-2">
                        <div class="max-w-[70%]">
                            <div class="text-xs ${metaColor} mb-0.5 ${isMe ? 'text-right' : ''}">${this.escapeHtml(senderName)}</div>
                            <div class="${bubbleBg} rounded-xl px-3 py-2 shadow-sm">
                                ${attachmentHtml}
                                ${msg.message_text ? `<p class="text-sm whitespace-pre-wrap">${this.escapeHtml(msg.message_text)}</p>` : ''}
                                <p class="text-xs ${metaColor} mt-1 ${isMe ? 'text-right' : ''}">${time}</p>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            },

            /** Send a text or image message */
            async sendCurrentMessage() {
                if (this.sending) return;
                if (!this.newMessage.trim() && !this.selectedImage) return;

                this.sending = true;
                try {
                    let response;
                    if (this.selectedImage) {
                        // Image upload
                        const formData = new FormData();
                        formData.append('image', this.selectedImage);
                        if (this.newMessage.trim()) formData.append('caption', this.newMessage.trim());
                        response = await fetch(`/tickets/${this.activeTicketId}/upload-image`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: formData,
                        });
                    } else {
                        // Text message
                        response = await fetch(`/tickets/${this.activeTicketId}/send`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ message_text: this.newMessage.trim() }),
                        });
                    }

                    const data = await response.json();
                    if (data.success) {
                        this.messages.push(data.message);
                        this.newMessage = '';
                        this.clearImage();
                        this.renderMessages();
                    }
                } catch (e) {
                    console.error('Send failed:', e);
                } finally {
                    this.sending = false;
                }
            },

            /** Close the currently active ticket */
            async closeCurrentTicket() {
                if (!confirm('{{ __("ui.ticket_close_confirm") ?? "Close this ticket?" }}')) return;
                try {
                    const response = await fetch(`/tickets/${this.activeTicketId}/close`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.selectTicket(this.activeTicketId);
                        this.filterTickets();
                    }
                } catch (e) {
                    console.error('Close failed:', e);
                }
            },

            /** Reopen the currently active ticket */
            async reopenCurrentTicket() {
                try {
                    const response = await fetch(`/tickets/${this.activeTicketId}/reopen`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.selectTicket(this.activeTicketId);
                        this.filterTickets();
                    }
                } catch (e) {
                    console.error('Reopen failed:', e);
                }
            },

            /** Filter ticket list via AJAX */
            async filterTickets() {
                this.loadingList = true;
                try {
                    const params = new URLSearchParams();
                    if (this.searchQuery) params.set('search', this.searchQuery);
                    if (this.statusFilter) params.set('status', this.statusFilter);
                    if (this.typeFilter) params.set('type', this.typeFilter);

                    const response = await fetch(`/tickets/filter?${params}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    document.getElementById('ticketListContent').innerHTML = data.html;
                    document.getElementById('ticketPagination').innerHTML = data.pagination;
                } catch (e) {
                    console.error('Filter failed:', e);
                } finally {
                    this.loadingList = false;
                }
            },

            /** Poll for new messages in the active ticket */
            async refreshMessages() {
                if (!this.activeTicketId) return;
                try {
                    const response = await fetch(`/tickets/${this.activeTicketId}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await response.json();
                    if (data.success && data.messages.length !== this.messages.length) {
                        this.messages = data.messages;
                        this.currentTicket = data.ticket;
                        this.currentTicket.can_reopen = this.currentTicket.reopen_deadline
                            ? new Date(this.currentTicket.reopen_deadline) > new Date()
                            : false;
                        this.renderMessages();
                    }
                } catch (e) { /* silent fail for polling */ }
            },

            /** Handle image file selection */
            handleImageSelect(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.selectedImage = file;
                this.selectedImagePreview = URL.createObjectURL(file);
            },

            /** Clear selected image */
            clearImage() {
                this.selectedImage = null;
                this.selectedImagePreview = null;
            },

            /** Helper: get CSS class for ticket status badge */
            getStatusClass(status) {
                const classes = {
                    'open': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'in_progress': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                    'closed': 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                    'reopened': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
                };
                return classes[status] || '';
            },

            /** Helper: get CSS class for ticket type badge */
            getTypeClass(type) {
                const classes = {
                    'booking': 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                    'complaint': 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                    'suggestion': 'bg-teal-100 text-teal-700 dark:bg-teal-900 dark:text-teal-300',
                };
                return classes[type] || 'bg-gray-100 text-gray-600';
            },

            /** Helper: format status string for display */
            formatStatus(status) {
                return status ? status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : '';
            },

            /** Helper: escape HTML for safe rendering */
            escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            },

            /** Cleanup on destroy */
            destroy() {
                if (this.pollingInterval) clearInterval(this.pollingInterval);
            }
        };
    }
    </script>
    @endpush
</x-app-layout>
