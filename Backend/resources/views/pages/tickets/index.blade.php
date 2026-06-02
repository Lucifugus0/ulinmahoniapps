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
                    <div class="flex items-center gap-2">
                        <!-- New Ticket button — admin-initiated notice -->
                        <button @click="showNewTicketModal = true; loadEligibleBookings()"
                            class="text-xs px-2.5 py-1.5 bg-green-600 text-white rounded-sm hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i>{{ __('ui.new_ticket') ?? 'New Ticket' }}
                        </button>
                        <a href="{{ route('broadcasts.index') }}"
                           class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                            <i class="fas fa-bullhorn mr-1"></i>{{ __('ui.broadcasts') ?? 'Broadcasts' }}
                        </a>
                    </div>
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
                        <option value="notice">Notice</option>
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
                                <span x-text="[currentTicket?.user?.first_name, currentTicket?.user?.last_name].filter(Boolean).join(' ') || currentTicket?.user?.name || 'User'"></span>
                                <template x-if="currentTicket?.property">
                                    <span>&bull; <span x-text="currentTicket?.property?.name"></span>
                                        <template x-if="currentTicket?.transaction?.room?.no">
                                            <span> No.<span x-text="currentTicket.transaction.room.no"></span></span>
                                        </template>
                                    </span>
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

                        <div class="flex items-center gap-2">
                            <div class="flex-1">
                                <textarea x-model="newMessage"
                                          @keydown.enter.prevent="if(!$event.shiftKey) sendCurrentMessage()"
                                          placeholder="{{ __('ui.ticket_type_message') ?? 'Type a message...' }}"
                                          rows="1"
                                          class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 p-2.5 resize-none focus:ring-indigo-500 focus:border-indigo-500"
                                          style="min-height: 40px; max-height: 120px;"></textarea>
                            </div>
                            {{-- Image upload button --}}
                            <label class="cursor-pointer text-gray-400 hover:text-indigo-500 transition-colors flex items-center justify-center w-10 h-10">
                                <i class="fas fa-image text-lg"></i>
                                <input type="file" class="hidden" accept="image/jpeg,image/png,image/heic,image/heif"
                                       @change="handleImageSelect($event)">
                            </label>
                            {{-- Send button --}}
                            <button @click="sendCurrentMessage()"
                                    :disabled="sending || (!newMessage.trim() && !selectedImage)"
                                    class="flex items-center justify-center w-10 h-10 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <i class="fas fa-paper-plane" :class="{ 'fa-spinner fa-spin': sending }"></i>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- NEW CHAT MODAL — admin-initiated chat with searchable booking table and category selector --}}
        <div x-show="showNewTicketModal" x-cloak
             class="fixed inset-0 bg-black/70 z-50 flex items-center justify-center"
             x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-3xl mx-4 max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                 style="box-shadow: 0 25px 50px -12px rgba(0,0,0,0.4);"
                 @click.outside="showNewTicketModal = false">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        <i class="fas fa-plus-circle mr-2 text-green-500"></i>{{ __('ui.new_ticket') }}
                    </h3>
                    <button @click="showNewTicketModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="px-6 py-5 space-y-4">
                    <!-- Booking search + selectable table -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('ui.select_booking') }} <span class="text-red-500">*</span>
                        </label>
                        <!-- Search box -->
                        <input type="text" x-model="bookingSearch" @input.debounce.300ms="searchBookings()"
                               placeholder="{{ __('ui.search_booking') }}"
                               class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 mb-2">
                        <!-- Selected booking display -->
                        <div x-show="newTicketForm.order_id" class="text-xs bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-lg px-3 py-2 mb-2 flex justify-between items-center">
                            <span><i class="fas fa-check-circle mr-1"></i> Selected: <strong x-text="newTicketForm.order_id"></strong></span>
                            <button @click="newTicketForm.order_id = ''" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-times"></i></button>
                        </div>
                        <!-- Booking results table -->
                        <div x-show="loadingBookings" class="text-sm text-gray-500 py-3 text-center">
                            <i class="fas fa-spinner fa-spin mr-1"></i> Loading...
                        </div>
                        <div x-show="!loadingBookings && eligibleBookings.length > 0" class="max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg relative">
                            <table class="w-full text-xs">
                                <!-- Sticky header with glassmorphism backdrop blur -->
                                <thead class="sticky top-0 z-10" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                                    <tr class="bg-gray-100/90 dark:bg-gray-700/90">
                                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase font-semibold">{{ __('ui.property') }}</th>
                                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase font-semibold">Room</th>
                                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase font-semibold">{{ __('ui.name') }}</th>
                                        <th class="px-3 py-2 text-left text-gray-500 dark:text-gray-400 uppercase font-semibold">Check-in — Check-out</th>
                                        <th class="px-3 py-2 text-center text-gray-500 dark:text-gray-400 uppercase font-semibold">{{ __('ui.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    <template x-for="b in eligibleBookings" :key="b.order_id">
                                        <tr @click="newTicketForm.order_id = b.order_id"
                                            class="cursor-pointer transition-colors"
                                            :class="newTicketForm.order_id === b.order_id ? 'bg-green-50 dark:bg-green-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700'">
                                            <td class="px-3 py-2 text-gray-900 dark:text-white" x-text="b.property_name"></td>
                                            <td class="px-3 py-2">
                                                <!-- Room number + room type badge -->
                                                <div class="text-gray-900 dark:text-white font-medium" x-text="'No. ' + b.room_no"></div>
                                                <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-xs bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300" x-text="b.room_name"></span>
                                            </td>
                                            <td class="px-3 py-2 text-gray-900 dark:text-white" x-text="b.user_name"></td>
                                            <td class="px-3 py-2 text-gray-600 dark:text-gray-400" x-text="b.check_in + ' — ' + b.check_out"></td>
                                            <td class="px-3 py-2 text-center">
                                                <span class="px-1.5 py-0.5 rounded text-xs font-medium"
                                                    :class="{
                                                        'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300': b.status === 'checked_in',
                                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300': b.status === 'upcoming' || b.status === 'active',
                                                        'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-300': b.status === 'checked_out'
                                                    }"
                                                    x-text="b.status === 'checked_in' ? 'Checked In' : b.status === 'upcoming' ? 'Upcoming' : b.status === 'active' ? 'Active' : 'Checked Out'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <p x-show="!loadingBookings && eligibleBookings.length === 0 && bookingSearch" class="text-xs text-gray-500 mt-1">No bookings found.</p>
                    </div>
                    <!-- Category: admin can only select Notice -->
                    @php $noticeCategory = $categories->firstWhere('category', 'notice'); @endphp
                    @if($noticeCategory)
                        <input type="hidden" x-model="newTicketForm.category_id" value="{{ $noticeCategory->id }}">
                    @endif
                    <!-- Subject -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('ui.subject') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" x-model="newTicketForm.subject" maxlength="255"
                               placeholder="{{ __('ui.ticket_subject_placeholder') }}"
                               class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500">
                    </div>
                    <!-- Message -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('ui.message') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea x-model="newTicketForm.message" rows="3" maxlength="2000"
                                  placeholder="{{ __('ui.ticket_message_placeholder') }}"
                                  class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500"></textarea>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 bg-green-50 dark:bg-green-900/20 rounded-lg px-3 py-2">
                        <i class="fas fa-info-circle mr-1 text-green-500"></i>
                        {{ __('ui.ticket_notice_info') }}
                    </div>
                </div>
                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                    <button @click="showNewTicketModal = false" class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800">
                        {{ __('ui.cancel') }}
                    </button>
                    <button @click="submitNewTicket()"
                            :disabled="!newTicketForm.order_id || !newTicketForm.subject || !newTicketForm.message || creatingTicket"
                            class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50">
                        <i x-show="creatingTicket" class="fas fa-spinner fa-spin mr-1"></i>
                        {{ __('ui.create_ticket') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Image preview modal — 80% screen with close button --}}
    <div id="chatImageModal" class="fixed inset-0 bg-black/80 z-[60] hidden items-center justify-center" style="display: none;" onclick="if(event.target===this)closeImageModal()">
        <div class="relative" style="width: 80vw; height: 80vh;">
            <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl font-bold z-10">&times;</button>
            <img id="chatImageModalImg" src="" alt="Preview" class="w-full h-full object-contain">
        </div>
    </div>

    @push('scripts')
    <script>
    /** Open image preview modal */
    function openImageModal(url) {
        document.getElementById('chatImageModalImg').src = url;
        document.getElementById('chatImageModal').style.display = 'flex';
    }
    function closeImageModal() {
        document.getElementById('chatImageModal').style.display = 'none';
        document.getElementById('chatImageModalImg').src = '';
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeImageModal(); });

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

            // New Chat modal state
            showNewTicketModal: false,
            eligibleBookings: [],
            loadingBookings: false,
            bookingSearch: '',
            newTicketForm: { order_id: '', category_id: '{{ $categories->firstWhere("category", "notice")?->id ?? "" }}', subject: '', message: '' },
            creatingTicket: false,

            /** Initialize component — load ticket if URL has ?open= param */
            init() {
                // Expose selectTicket globally so server-rendered onclick can call it
                window._selectTicket = (id) => this.selectTicket(id);

                if (this.activeTicketId) {
                    this.selectTicket(this.activeTicketId);
                }
                // Poll for new messages every 10 seconds
                this.pollingInterval = setInterval(() => {
                    if (this.activeTicketId) this.refreshMessages();
                }, 10000);
            },

            /** Highlight the active ticket in the list — works after AJAX reload */
            highlightActiveTicket() {
                document.querySelectorAll('.ticket-item').forEach(el => {
                    const id = parseInt(el.dataset.ticketId);
                    if (id === this.activeTicketId) {
                        el.classList.add('bg-indigo-50', 'dark:bg-indigo-900/30', 'border-l-4', 'border-l-indigo-500');
                        el.classList.remove('hover:bg-gray-50', 'dark:hover:bg-gray-800');
                    } else {
                        el.classList.remove('bg-indigo-50', 'dark:bg-indigo-900/30', 'border-l-4', 'border-l-indigo-500');
                    }
                });
            },

            /** Select a ticket and load its messages */
            async selectTicket(ticketId) {
                this.activeTicketId = ticketId;
                this.highlightActiveTicket();
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
                    const senderName = [msg.sender?.first_name, msg.sender?.last_name].filter(Boolean).join(' ') || msg.sender?.name || 'Unknown';

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
                                attachmentHtml += `<img src="${att.thumbnail_url || att.file_url}" data-full="${att.file_url}" class="max-w-xs mt-1 cursor-pointer hover:opacity-90" loading="lazy" onclick="openImageModal(this.dataset.full)">`;
                            }
                        });
                    }

                    const align = isMe ? 'justify-end' : 'justify-start';
                    const bubbleBg = isMe ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100';
                    const metaColor = isMe ? 'text-indigo-200' : 'text-gray-400 dark:text-gray-500';

                    return `<div class="flex ${align} mb-2">
                        <div class="max-w-[70%]">
                            <div class="text-xs ${metaColor} mb-0.5 ${isMe ? 'text-right' : ''}">${this.escapeHtml(senderName)}</div>
                            <div class="${bubbleBg} rounded-md px-3 py-2 shadow-sm">
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
                    // Re-apply active highlight after DOM update
                    this.$nextTick(() => this.highlightActiveTicket());
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
                    'notice': 'bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300',
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

            /** Load eligible bookings for the new chat modal */
            async loadEligibleBookings() {
                this.loadingBookings = true;
                this.eligibleBookings = [];
                this.bookingSearch = '';
                try {
                    const res = await fetch('/tickets/eligible-bookings', {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (data.success) this.eligibleBookings = data.data;
                } catch (e) {
                    console.error('Failed to load bookings:', e);
                } finally {
                    this.loadingBookings = false;
                }
            },

            /** Search bookings with debounced input */
            async searchBookings() {
                this.loadingBookings = true;
                try {
                    const params = this.bookingSearch ? `?search=${encodeURIComponent(this.bookingSearch)}` : '';
                    const res = await fetch(`/tickets/eligible-bookings${params}`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const data = await res.json();
                    if (data.success) this.eligibleBookings = data.data;
                } catch (e) {
                    console.error('Failed to search bookings:', e);
                } finally {
                    this.loadingBookings = false;
                }
            },

            /** Submit admin-initiated chat with selected category */
            async submitNewTicket() {
                if (!this.newTicketForm.order_id || !this.newTicketForm.category_id || !this.newTicketForm.subject || !this.newTicketForm.message) return;
                this.creatingTicket = true;
                try {
                    const res = await fetch('/tickets/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            order_id: this.newTicketForm.order_id,
                            category_id: this.newTicketForm.category_id,
                            subject: this.newTicketForm.subject,
                            message: this.newTicketForm.message,
                        }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.showNewTicketModal = false;
                        this.newTicketForm = { order_id: '', category_id: '{{ $categories->firstWhere("category", "notice")?->id ?? "" }}', subject: '', message: '' };
                        this.filterTickets();
                        if (data.ticket_id) {
                            this.selectTicket(data.ticket_id);
                        }
                    } else {
                        alert(data.message || 'Failed to create chat');
                    }
                } catch (e) {
                    console.error('Create chat failed:', e);
                    alert('Failed to create chat');
                } finally {
                    this.creatingTicket = false;
                }
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
