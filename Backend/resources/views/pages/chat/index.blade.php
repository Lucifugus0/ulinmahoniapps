<x-app-layout>
    <div class="flex" style="height: calc(100vh - 64px);" x-data="chatMessenger()" x-init="init()">

        {{-- LEFT PANEL: Conversation List --}}
        <div class="w-full md:w-[360px] flex-shrink-0 border-r border-gray-200 bg-white flex flex-col"
             :class="activeConversationId ? 'hidden md:flex' : 'flex'">

            {{-- Header --}}
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Chat</h2>
                <button onclick="openCheckedInModal()"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Chat
                </button>
            </div>

            {{-- Search & Filter --}}
            <div class="px-4 py-3 border-b border-gray-200 space-y-2 flex-shrink-0">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="searchQuery" @input="debouncedSearch()"
                        placeholder="{{ __('ui.chat_search_order_name') }}"
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <select x-model="statusFilter" @change="filterConversations()"
                    class="w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="archived">Archived</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            {{-- Notification Permission Banner --}}
            <div x-show="showNotifBanner" x-transition
                 class="px-4 py-2 bg-yellow-50 border-b border-yellow-200 flex items-center justify-between flex-shrink-0">
                <span class="text-xs text-yellow-800">Enable notifications for new messages?</span>
                <div class="flex gap-2">
                    <button @click="requestNotificationPermission()" class="text-xs px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Enable</button>
                    <button @click="showNotifBanner = false" class="text-xs px-2 py-1 text-gray-500 hover:text-gray-700">Dismiss</button>
                </div>
            </div>

            {{-- Conversation Cards --}}
            <div class="flex-1 overflow-y-auto">
                {{-- Loading --}}
                <template x-if="loadingConversations">
                    <div class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </template>

                {{-- Empty --}}
                <template x-if="!loadingConversations && conversations.length === 0">
                    <div class="flex flex-col items-center justify-center py-12 text-gray-400 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm font-medium">No conversations found</p>
                        <p class="text-xs mt-1 text-center">Start a new chat with a checked-in user</p>
                    </div>
                </template>

                {{-- Conversation List --}}
                <template x-if="!loadingConversations && conversations.length > 0">
                    <div>
                        <template x-for="conv in conversations" :key="conv.id">
                            <div @click="selectConversation(conv.id)"
                                :class="activeConversationId == conv.id ? 'bg-blue-50 border-l-4 border-l-blue-600' : 'border-l-4 border-l-transparent hover:bg-gray-50'"
                                class="px-4 py-3 cursor-pointer border-b border-gray-100 transition-colors">
                                <div class="flex items-start gap-3">
                                    {{-- Avatar --}}
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                        <span x-text="getInitials(conv.user_name || conv.title || conv.order_id)"></span>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm font-semibold text-gray-900 truncate" x-text="conv.user_name || conv.title || conv.order_id"></span>
                                            <span class="text-xs text-gray-400 ml-2 flex-shrink-0" x-text="formatTime(conv.last_message_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-500 truncate mt-0.5" x-text="conv.order_id + ' - ' + conv.property_name"></p>
                                        <div class="flex items-center justify-between mt-1">
                                            <p class="text-xs text-gray-400 truncate" x-text="conv.last_message_preview || (conv.messages_count + ' messages')"></p>
                                            <span x-show="conv.unread_count > 0"
                                                class="bg-blue-600 text-white text-xs rounded-full px-2 py-0.5 font-bold flex-shrink-0 ml-2"
                                                x-text="conv.unread_count"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>

        {{-- RIGHT PANEL: Active Chat --}}
        <div class="flex-1 flex flex-col bg-gray-50"
             :class="!activeConversationId ? 'hidden md:flex' : 'flex'">

            {{-- Empty State --}}
            <template x-if="!activeConversationId">
                <div class="flex-1 flex items-center justify-center">
                    <div class="text-center text-gray-400 px-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <h3 class="text-xl font-medium text-gray-500">Select a conversation</h3>
                        <p class="text-sm mt-2">Choose a conversation from the list to start chatting</p>
                    </div>
                </div>
            </template>

            {{-- Active Chat Content --}}
            <template x-if="activeConversationId">
                <div class="flex flex-col h-full">

                    {{-- Chat Header --}}
                    <div class="flex-shrink-0">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 md:px-6 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    {{-- Back button (mobile) --}}
                                    <button @click="activeConversationId = null; currentConversation = null; currentBooking = null; messages = [];"
                                        class="md:hidden flex-shrink-0 hover:bg-white/20 rounded-full p-1 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <div class="flex-1 min-w-0">
                                        <h2 class="text-lg font-semibold truncate" x-text="currentConversation?.title || 'Chat untuk Booking ' + (currentConversation?.order_id || '')"></h2>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-sm mt-1 opacity-90">
                                            <span x-text="'Order: ' + (currentConversation?.order_id || '')"></span>
                                            <span x-text="'Property: ' + (currentConversation?.property?.name || 'N/A')"></span>
                                            <template x-if="currentBooking && currentBooking.room">
                                                <span x-text="'Kamar: No.' + (currentBooking.room?.no || 'N/A') + ' - ' + (currentBooking.room?.name || 'N/A')"></span>
                                            </template>
                                            <span x-text="'Participants: ' + (currentConversation?.participants?.length || 0)"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0 ml-3" x-html="getStatusBadge(currentConversation?.status)"></div>
                            </div>
                        </div>

                        {{-- User Info Box --}}
                        <template x-if="currentBooking">
                            <div class="px-4 md:px-6 py-3 bg-blue-50 border-b border-blue-200">
                                <div class="flex items-center flex-wrap gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                                        <span x-text="(currentBooking.user_name || 'U').charAt(0).toUpperCase()"></span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 text-sm" x-text="currentBooking.user_name || 'N/A'"></h3>
                                        <p class="text-xs text-gray-600" x-text="currentBooking.user_email || ''"></p>
                                    </div>
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Check-in:</span>
                                            <span class="font-medium text-gray-900" x-text="formatBookingDate(currentBooking.transaction?.check_in)"></span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span>Check-out:</span>
                                            <span class="font-medium text-gray-900" x-text="formatBookingDate(currentBooking.transaction?.check_out)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Messages Container --}}
                    <div class="flex-1 overflow-y-auto p-4 md:p-6" x-ref="messagesContainer">
                        {{-- Loading Messages --}}
                        <template x-if="loadingMessages">
                            <div class="flex items-center justify-center h-full">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                            </div>
                        </template>

                        {{-- Empty Messages --}}
                        <template x-if="!loadingMessages && messages.length === 0">
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No messages yet</h3>
                                    <p class="text-gray-500">Start the conversation by sending a message below</p>
                                </div>
                            </div>
                        </template>

                        {{-- Messages List --}}
                        <template x-if="!loadingMessages && messages.length > 0">
                            <div id="messagesList"></div>
                        </template>
                    </div>

                    {{-- Message Input Area --}}
                    <div class="flex-shrink-0 border-t border-gray-200 bg-white p-4">
                        {{-- Image Preview --}}
                        <div x-show="selectedImagePreview" class="mb-3" x-transition>
                            <div class="relative inline-block">
                                <img x-ref="previewImg" src="" alt="Preview" class="max-h-32 rounded-lg border border-gray-300">
                                <button type="button" @click="clearImagePreview()"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-end space-x-3">
                            <div class="flex-1">
                                <textarea x-ref="messageInput" x-model="newMessage"
                                    @keydown.enter.prevent="if(!$event.shiftKey) sendCurrentMessage()"
                                    rows="2" placeholder="Type your message..."
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none min-h-[46px]"></textarea>
                            </div>

                            {{-- Image Upload --}}
                            <input type="file" x-ref="imageInput" accept="image/jpeg,image/png,image/jpg" class="hidden" @change="handleImageSelect($event)">
                            <button type="button" @click="$refs.imageInput.click()"
                                class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors h-[46px]"
                                title="Upload Image">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </button>

                            {{-- Send --}}
                            <button @click="sendCurrentMessage()"
                                :disabled="(!newMessage.trim() && !selectedImage) || sending"
                                class="inline-flex items-center px-5 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors h-[46px]">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Send
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Include Checked-In Users Modal --}}
    @include('pages.chat.partials.checked-in-users-modal')

    <script>
        // Flag to prevent duplicate notifications from dropdown-chat
        window.__chatPageActive = true;

        const AUTH_USER_ID = {{ auth()->id() }};
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

        function chatMessenger() {
            return {
                // Data
                conversations: [],
                messages: [],
                currentConversation: null,
                currentBooking: null,
                activeConversationId: null,

                // UI
                loadingConversations: false,
                loadingMessages: false,
                sending: false,
                newMessage: '',
                selectedImage: null,
                selectedImagePreview: false,
                searchQuery: '',
                statusFilter: '',
                searchTimeout: null,
                showNotifBanner: false,

                // Polling & Notifications
                pollInterval: null,
                previousUnreadState: {},

                init() {
                    // Initialize conversations from server data
                    const serverConversations = @json($conversations->items());
                    this.conversations = serverConversations.map(c => this.mapConversation(c));

                    // Store initial unread state
                    this.conversations.forEach(c => {
                        this.previousUnreadState[c.id] = c.unread_count;
                    });

                    // Auto-open conversation if ?open= param present
                    const openId = @json($openConversationId ?? null);
                    if (openId) {
                        this.$nextTick(() => this.selectConversation(parseInt(openId)));
                    }

                    // Polling every 15 seconds
                    this.pollInterval = setInterval(() => {
                        this.refreshConversations();
                        if (this.activeConversationId) {
                            this.refreshMessages();
                        }
                    }, 15000);

                    // Notification permission
                    if ('Notification' in window && Notification.permission === 'default') {
                        this.showNotifBanner = true;
                    }

                    // Global openChat for checked-in-users-modal
                    window.openChat = (id) => this.selectConversation(id);
                },

                mapConversation(conv) {
                    const lastMsg = conv.messages && conv.messages.length > 0
                        ? conv.messages[conv.messages.length - 1] || conv.messages[0]
                        : null;

                    return {
                        id: conv.id,
                        order_id: conv.order_id,
                        title: conv.title,
                        status: conv.status,
                        messages_count: conv.messages_count || 0,
                        last_message_at: conv.last_message_at,
                        unread_count: conv.unread_count || 0,
                        user_name: conv.booking?.user_name || conv.transaction?.user?.name || 'N/A',
                        user_email: conv.booking?.user_email || conv.transaction?.user?.email || '',
                        room_name: conv.booking?.room?.name || 'N/A',
                        property_name: conv.property?.name || 'N/A',
                        last_message_preview: lastMsg?.message_text?.substring(0, 60) || '',
                        last_message_sender: lastMsg?.sender?.name || ''
                    };
                },

                async selectConversation(conversationId) {
                    this.activeConversationId = conversationId;
                    this.loadingMessages = true;
                    this.messages = [];

                    try {
                        const response = await fetch(`/chat/${conversationId}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.currentConversation = data.conversation;
                            this.currentBooking = data.booking;
                            this.messages = data.messages || [];

                            // Mark as read in local state
                            const conv = this.conversations.find(c => c.id == conversationId);
                            if (conv) {
                                conv.unread_count = 0;
                                this.previousUnreadState[conv.id] = 0;
                            }

                            this.$nextTick(() => {
                                this.renderMessages();
                                this.$nextTick(() => this.scrollToBottom());
                            });
                        }
                    } catch (error) {
                        console.error('Error loading chat:', error);
                    } finally {
                        this.loadingMessages = false;
                    }
                },

                renderMessages() {
                    const container = document.getElementById('messagesList');
                    if (!container) return;
                    container.innerHTML = this.buildMessagesHtml(this.messages);
                },

                buildMessagesHtml(messages) {
                    if (!messages || messages.length === 0) return '';

                    return messages.map(message => {
                        const isOwn = message.sender_id === AUTH_USER_ID;
                        const senderName = this.getSenderName(message.sender);

                        // Attachments
                        let attachmentHtml = '';
                        if (message.attachments && message.attachments.length > 0) {
                            attachmentHtml = message.attachments.map(att => {
                                if (att.file_type && att.file_type.startsWith('image/')) {
                                    let fileUrl = att.file_url || '/storage/' + att.file_path;
                                    if (fileUrl && !fileUrl.startsWith('/') && !fileUrl.startsWith('http')) {
                                        fileUrl = '/' + fileUrl;
                                    }
                                    return `<div class="mt-2">
                                        <a href="${this.escapeHtml(fileUrl)}" target="_blank">
                                            <img src="${this.escapeHtml(fileUrl)}" alt="${this.escapeHtml(att.file_name || 'Image')}"
                                                class="max-w-xs rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                                        </a>
                                    </div>`;
                                }
                                return '';
                            }).join('');
                        }

                        // Edit button
                        const editButton = isOwn && message.message_type === 'text' ? `
                            <button onclick="chatMessengerEditMessage(${message.id}, \`${this.escapeHtml(message.message_text || '').replace(/`/g, '\\`')}\`, ${message.conversation_id})"
                                class="ml-2 text-xs ${isOwn ? 'text-blue-200 hover:text-white' : 'text-gray-500 hover:text-gray-700'} opacity-0 group-hover:opacity-100 transition-opacity">
                                Edit
                            </button>
                        ` : '';

                        // Edited indicator
                        const editedIndicator = message.is_edited ? `
                            <span class="text-xs ${isOwn ? 'text-blue-200' : 'text-gray-500'} ml-2">(edited)</span>
                        ` : '';

                        return `
                            <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4 group">
                                <div class="flex items-start max-w-xl ${isOwn ? 'flex-row-reverse' : ''}">
                                    <div class="flex-shrink-0 ${isOwn ? 'ml-3' : 'mr-3'}">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br ${isOwn ? 'from-blue-500 to-indigo-600' : 'from-gray-400 to-gray-600'} flex items-center justify-center text-white font-semibold">
                                            ${senderName.charAt(0).toUpperCase()}
                                        </div>
                                    </div>
                                    <div class="flex flex-col ${isOwn ? 'items-end' : 'items-start'}">
                                        <div class="flex items-center mb-1">
                                            <span class="text-sm font-medium text-gray-900">${this.escapeHtml(senderName)}</span>
                                            <span class="text-xs text-gray-500 ml-2">${this.formatDate(message.created_at)}</span>
                                            ${editedIndicator}
                                            ${editButton}
                                        </div>
                                        <div class="rounded-lg px-4 py-2 ${isOwn ? 'bg-blue-600 text-white rounded-br-sm' : 'bg-white text-gray-900 border border-gray-200 rounded-bl-sm'}" id="message_${message.id}">
                                            ${message.message_text ? `<p class="text-sm whitespace-pre-wrap">${this.escapeHtml(message.message_text)}</p>` : ''}
                                            ${attachmentHtml}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                },

                async sendCurrentMessage() {
                    if ((!this.newMessage.trim() && !this.selectedImage) || this.sending) return;

                    this.sending = true;
                    const messageText = this.newMessage.trim();
                    this.newMessage = '';

                    try {
                        let endpoint, options;

                        if (this.selectedImage) {
                            endpoint = `/chat/${this.activeConversationId}/upload-image`;
                            const formData = new FormData();
                            formData.append('image', this.selectedImage);
                            if (messageText) formData.append('message_text', messageText);
                            options = {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': CSRF_TOKEN,
                                    'Accept': 'application/json',
                                },
                                body: formData
                            };
                        } else {
                            endpoint = `/chat/${this.activeConversationId}/send`;
                            options = {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': CSRF_TOKEN,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({ message_text: messageText })
                            };
                        }

                        const response = await fetch(endpoint, options);
                        const data = await response.json();

                        if (data.success) {
                            this.messages.push(data.message);
                            this.clearImagePreview();
                            this.renderMessages();
                            this.$nextTick(() => this.scrollToBottom());
                        } else {
                            alert('Failed to send message. Please try again.');
                            this.newMessage = messageText;
                        }
                    } catch (error) {
                        console.error('Error sending:', error);
                        alert('An error occurred. Please try again.');
                        this.newMessage = messageText;
                    } finally {
                        this.sending = false;
                        this.$refs.messageInput?.focus();
                    }
                },

                async refreshConversations() {
                    try {
                        const params = new URLSearchParams();
                        if (this.searchQuery) params.append('search', this.searchQuery);
                        if (this.statusFilter) params.append('status', this.statusFilter);

                        const response = await fetch(`/chat/conversations-json?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const data = await response.json();

                        if (data.success) {
                            const newConversations = data.data.map(c => this.mapConversation(c));

                            // Check for new messages and notify
                            this.checkForNewMessages(newConversations);

                            this.conversations = newConversations;
                        }
                    } catch (error) {
                        console.error('Error refreshing conversations:', error);
                    }
                },

                async refreshMessages() {
                    try {
                        const response = await fetch(`/chat/${this.activeConversationId}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        const data = await response.json();
                        if (data.success && data.messages) {
                            const hadCount = this.messages.length;
                            this.messages = data.messages;
                            if (data.messages.length > hadCount) {
                                this.renderMessages();
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        }
                    } catch (error) {
                        console.error('Error refreshing messages:', error);
                    }
                },

                debouncedSearch() {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.filterConversations(), 500);
                },

                async filterConversations() {
                    this.loadingConversations = true;
                    await this.refreshConversations();
                    this.loadingConversations = false;
                },

                // Image handling
                handleImageSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                        alert('Please select a valid image file (JPEG, PNG, JPG)');
                        event.target.value = '';
                        return;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image size must not exceed 5MB');
                        event.target.value = '';
                        return;
                    }

                    this.selectedImage = file;
                    this.selectedImagePreview = true;

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (this.$refs.previewImg) {
                            this.$refs.previewImg.src = e.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                },

                clearImagePreview() {
                    this.selectedImage = null;
                    this.selectedImagePreview = false;
                    if (this.$refs.imageInput) this.$refs.imageInput.value = '';
                },

                // Notification methods
                requestNotificationPermission() {
                    if ('Notification' in window) {
                        Notification.requestPermission().then(permission => {
                            this.showNotifBanner = false;
                        });
                    }
                },

                checkForNewMessages(newConversations) {
                    if (!('Notification' in window) || Notification.permission !== 'granted') return;

                    newConversations.forEach(conv => {
                        // Skip notification for currently active conversation
                        if (conv.id == this.activeConversationId) return;

                        const prevCount = this.previousUnreadState[conv.id] || 0;
                        if (conv.unread_count > prevCount) {
                            this.showBrowserNotification(conv);
                        }
                        this.previousUnreadState[conv.id] = conv.unread_count;
                    });
                },

                showBrowserNotification(conv) {
                    try {
                        const senderName = conv.last_message_sender || conv.user_name || 'New message';
                        const preview = conv.last_message_preview || 'You have a new message';

                        const notification = new Notification(senderName, {
                            body: preview,
                            icon: '/images/frist_icon.png',
                            tag: `chat-${conv.id}`,
                            data: { conversationId: conv.id }
                        });

                        notification.onclick = (event) => {
                            event.preventDefault();
                            window.focus();
                            this.selectConversation(conv.id);
                            notification.close();
                        };

                        setTimeout(() => notification.close(), 5000);
                    } catch (e) {
                        console.error('Notification error:', e);
                    }
                },

                // Helpers
                scrollToBottom() {
                    const container = this.$refs.messagesContainer;
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },

                getStatusBadge(status) {
                    const badges = {
                        'active': '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-400/30 text-white">Active</span>',
                        'archived': '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-400/30 text-white">Archived</span>',
                        'closed': '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-400/30 text-white">Closed</span>'
                    };
                    return badges[status] || '';
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleString('en-US', {
                        month: 'short', day: 'numeric', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    const date = new Date(timestamp);
                    const now = new Date();
                    const diff = now - date;
                    const hours = Math.floor(diff / 3600000);
                    const days = Math.floor(diff / 86400000);
                    if (hours < 1) return 'Just now';
                    if (hours < 24) return `${hours}h ago`;
                    if (days < 7) return `${days}d ago`;
                    return date.toLocaleDateString();
                },

                formatBookingDate(dateString) {
                    if (!dateString) return 'N/A';
                    try {
                        const date = new Date(dateString);
                        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
                    } catch (e) {
                        return dateString;
                    }
                },

                getSenderName(sender) {
                    if (!sender) return this.currentBooking?.user_name || 'Unknown';
                    if (sender.name && sender.name.trim()) return sender.name;
                    const firstName = sender.first_name || '';
                    const lastName = sender.last_name || '';
                    const fullName = `${firstName} ${lastName}`.trim();
                    if (fullName) return fullName;
                    return this.currentBooking?.user_name || 'Unknown';
                },

                escapeHtml(text) {
                    if (!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                },

                getInitials(text) {
                    if (!text) return 'U';
                    const words = text.split(' ');
                    if (words.length >= 2) {
                        return (words[0][0] + words[1][0]).toUpperCase();
                    }
                    return text.substring(0, 2).toUpperCase();
                }
            };
        }

        // Global function for message editing (called from innerHTML)
        function chatMessengerEditMessage(messageId, currentText, conversationId) {
            Swal.fire({
                title: 'Edit Message',
                input: 'textarea',
                inputValue: currentText,
                inputAttributes: { 'aria-label': 'Edit your message', 'rows': 4 },
                showCancelButton: true,
                confirmButtonText: 'Save',
                confirmButtonColor: '#3b82f6',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value || value.trim() === '') return 'Message cannot be empty';
                    if (value.length > 5000) return 'Message is too long (max 5000 characters)';
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    chatMessengerUpdateMessage(messageId, result.value, conversationId);
                }
            });
        }

        function chatMessengerUpdateMessage(messageId, newText, conversationId) {
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(`/chat/messages/${messageId}/edit`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message_text: newText })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Message updated successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Refresh current conversation
                    if (window.openChat) window.openChat(conversationId);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to update message',
                        icon: 'error',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while updating the message',
                    icon: 'error',
                    confirmButtonColor: '#3b82f6'
                });
            });
        }
    </script>
</x-app-layout>
