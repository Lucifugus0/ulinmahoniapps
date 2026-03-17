@props([
    'align' => 'right'
])

<div class="relative inline-flex" x-data="dropdownChat()" x-init="init()">
    {{-- Chat Button --}}
    <button
        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50 dark:lg:hover:bg-gray-800 rounded-full relative"
        :class="{ 'bg-gray-200 dark:bg-gray-800': open }"
        aria-haspopup="true"
        @click.prevent="toggleDropdown()"
        :aria-expanded="open"
    >
        <span class="sr-only">Messages</span>
        <svg class="fill-current text-gray-500/80 dark:text-gray-400/80" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 0C3.58 0 0 3.58 0 8c0 1.5.41 2.9 1.12 4.1L.05 15.05a.75.75 0 00.9.9l2.95-1.07A7.96 7.96 0 008 16c4.42 0 8-3.58 8-8s-3.58-8-8-8zm0 14c-1.28 0-2.48-.32-3.54-.88l-.25-.14-2.6.94.94-2.6-.14-.25A5.96 5.96 0 012 8c0-3.31 2.69-6 6-6s6 2.69 6 6-2.69 6-6 6z"/>
            <path d="M5 7h2v2H5V7zm4 0h2v2H9V7z"/>
        </svg>
        {{-- Unread Badge --}}
        <div x-show="totalUnread > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-50"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute top-0 right-0 w-2.5 h-2.5 bg-red-500 border-2 border-gray-100 dark:border-gray-900 rounded-full"></div>
    </button>

    {{-- Dropdown Panel --}}
    <div
        class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 min-w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-0' : 'left-0' }}"
        style="width: 380px;"
        @click.outside="closeDropdown()"
        @keydown.escape.window="closeDropdown()"
        x-show="open && !chatWindowOpen"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        {{-- Header --}}
        <div class="flex items-center justify-between pt-3 pb-2 px-4 border-b border-gray-200 dark:border-gray-700/60">
            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">Messages</div>
            <a href="{{ route('chat.index') }}" class="text-xs text-blue-500 hover:text-blue-600 font-medium">View All</a>
        </div>

        {{-- Search --}}
        <div class="p-3 border-b border-gray-200 dark:border-gray-700/60">
            <input type="text"
                   x-model="searchQuery"
                   @input="searchConversations()"
                   placeholder="Search conversations..."
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
        </div>

        {{-- Conversations List --}}
        <div class="overflow-y-auto" style="max-height: 400px;">
            <template x-if="loading">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loading && conversations.length === 0">
                <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">No conversations yet</p>
                </div>
            </template>

            <template x-if="!loading && conversations.length > 0">
                <ul>
                    <template x-for="conversation in conversations" :key="conversation.id">
                        <li class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
                            <a @click.prevent="openChat(conversation.id)"
                               class="block py-3 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/20 cursor-pointer"
                               @focus="open = true" @focusout="open = false">
                                <div class="flex items-start space-x-3">
                                    {{-- Avatar --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                                            <span x-text="getInitials(conversation.title || conversation.order_id)"></span>
                                        </div>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate" x-text="conversation.title || 'Chat ' + conversation.order_id"></span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 ml-2" x-text="formatTime(conversation.last_message_at)"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate" x-text="conversation.order_id"></p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-xs text-gray-500" x-text="conversation.messages_count + ' messages'"></span>
                                            <span x-show="conversation.unread_count > 0"
                                                  class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold bg-blue-600 text-white"
                                                  x-text="conversation.unread_count"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </template>
        </div>

        {{-- Footer --}}
        <div class="border-t border-gray-200 dark:border-gray-700/60 py-2 px-4">
            <button @click="openCheckedInModal()"
                    class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                + Start New Chat
            </button>
        </div>
    </div>

    {{-- Chat Window Panel --}}
    <div
        class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-0' : 'left-0' }}"
        style="width: 400px; height: 500px;"
        @click.outside="closeDropdown()"
        @keydown.escape.window="closeDropdown()"
        x-show="open && chatWindowOpen"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        {{-- Chat Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 flex items-center justify-between">
            <div class="flex items-center space-x-3 flex-1 min-w-0">
                <button @click="backToConversations()" class="hover:bg-white/20 rounded-full p-1 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-sm truncate" x-text="currentConversation?.title || 'Chat'"></h3>
                    <p class="text-xs opacity-90 truncate" x-text="currentConversation?.order_id"></p>
                </div>
            </div>
            <a :href="'/chat?open=' + currentConversation?.id" class="hover:bg-white/20 rounded-full p-1 transition" title="Open Full Chat">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
            </a>
        </div>

        {{-- Booking Info (if available) --}}
        <div x-show="currentBooking" class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border-b border-blue-200 dark:border-blue-800">
            <div class="flex items-center text-xs text-gray-600 dark:text-gray-300">
                <span class="font-medium" x-text="currentBooking?.user_name"></span>
                <span class="mx-2">|</span>
                <span x-text="currentBooking?.room_name"></span>
                <span class="mx-2">|</span>
                <span x-text="currentBooking?.check_in + ' - ' + currentBooking?.check_out"></span>
            </div>
        </div>

        {{-- Messages Container --}}
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900" x-ref="messagesContainer" style="height: 300px;">
            <template x-if="loadingMessages">
                <div class="flex items-center justify-center h-full">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loadingMessages && messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">No messages yet</p>
                </div>
            </template>

            <template x-if="!loadingMessages && messages.length > 0">
                <div class="space-y-3">
                    <template x-for="message in messages" :key="message.id">
                        <div :class="message.sender_id === {{ auth()->id() }} ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="message.sender_id === {{ auth()->id() }} ? 'flex flex-row-reverse items-end space-x-reverse space-x-2' : 'flex items-end space-x-2'">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    <div :class="message.sender_id === {{ auth()->id() }} ? 'w-7 h-7 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold' : 'w-7 h-7 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-semibold'">
                                        <span x-text="message.sender?.name?.charAt(0) || 'U'"></span>
                                    </div>
                                </div>

                                {{-- Message Bubble --}}
                                <div class="max-w-[200px]">
                                    <div :class="message.sender_id === {{ auth()->id() }} ? 'bg-blue-600 text-white rounded-2xl rounded-br-sm px-3 py-2' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-2xl rounded-bl-sm px-3 py-2 border border-gray-200 dark:border-gray-600'">
                                        <p x-show="message.message_text" class="text-sm whitespace-pre-wrap break-words" x-text="message.message_text"></p>
                                        {{-- Image Attachments --}}
                                        <template x-if="message.attachments && message.attachments.length > 0">
                                            <div class="mt-2 space-y-2">
                                                <template x-for="attachment in message.attachments" :key="attachment.id">
                                                    <template x-if="attachment.file_type && attachment.file_type.startsWith('image/')">
                                                        <a :href="(attachment.file_url || '/storage/' + attachment.file_path).replace(/^(?!\/)(?!http)/, '/')" target="_blank">
                                                            <img :src="(attachment.file_url || '/storage/' + attachment.file_path).replace(/^(?!\/)(?!http)/, '/')"
                                                                 :alt="attachment.file_name || 'Image'"
                                                                 class="max-w-full rounded-lg cursor-pointer hover:opacity-90 transition-opacity">
                                                        </a>
                                                    </template>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1 px-1" x-text="formatMessageTime(message.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Message Input --}}
        <div class="border-t border-gray-200 dark:border-gray-700 p-3 bg-white dark:bg-gray-800">
            <form @submit.prevent="sendMessage()" class="flex items-end space-x-2">
                <textarea x-ref="messageInput"
                          x-model="newMessage"
                          @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                          placeholder="Type a message..."
                          rows="1"
                          class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                          style="max-height: 80px;"></textarea>
                <button type="submit"
                        :disabled="!newMessage.trim() || sending"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg px-3 py-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Include Checked-In Users Modal --}}
@include('pages.chat.partials.checked-in-users-modal')

<script>
function dropdownChat() {
    return {
        open: false,
        chatWindowOpen: false,
        loading: false,
        loadingMessages: false,
        sending: false,
        conversations: [],
        messages: [],
        currentConversation: null,
        currentBooking: null,
        newMessage: '',
        searchQuery: '',
        totalUnread: 0,
        pollInterval: null,
        previousUnreadCounts: {},
        notifInitialized: false,

        init() {
            this.loadConversations();
            // Poll for new messages every 15 seconds
            this.pollInterval = setInterval(() => {
                this.loadConversations();
                if (this.open && this.chatWindowOpen && this.currentConversation) {
                    this.loadMessages(this.currentConversation.id);
                }
            }, 15000);

            // Request notification permission after a delay
            if ('Notification' in window && Notification.permission === 'default') {
                // Will be requested when user interacts with chat
            }

            // Make openChat globally available
            window.openChat = (id) => {
                this.open = true;
                this.openChat(id);
            };
        },

        toggleDropdown() {
            this.open = !this.open;
            if (this.open && !this.chatWindowOpen) {
                this.loadConversations();
            }
        },

        closeDropdown() {
            this.open = false;
            this.chatWindowOpen = false;
        },

        async loadConversations() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.searchQuery) params.append('search', this.searchQuery);

                const response = await fetch(`/chat/conversations-json?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    const newConversations = data.data.map(conv => ({
                        id: conv.id,
                        order_id: conv.order_id,
                        title: conv.title,
                        property: conv.property?.name || 'N/A',
                        messages_count: conv.messages_count || 0,
                        last_message_at: conv.last_message_at,
                        status: conv.status,
                        unread_count: conv.unread_count || 0,
                        user_name: conv.booking?.user_name || conv.transaction?.user?.name || '',
                        last_message_text: conv.messages?.[0]?.message_text || '',
                        last_message_sender: conv.messages?.[0]?.sender?.name || ''
                    }));

                    // Check for new messages and show browser notification
                    if (this.notifInitialized && !window.__chatPageActive) {
                        this.checkAndNotify(newConversations);
                    }

                    // Store unread state
                    newConversations.forEach(c => {
                        if (!this.notifInitialized) {
                            this.previousUnreadCounts[c.id] = c.unread_count;
                        }
                    });
                    this.notifInitialized = true;

                    this.conversations = newConversations;
                    this.totalUnread = data.total_unread || this.conversations.reduce((sum, c) => sum + c.unread_count, 0);
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            } finally {
                this.loading = false;
            }
        },

        async openChat(conversationId) {
            this.chatWindowOpen = true;
            this.currentConversation = this.conversations.find(c => c.id == conversationId) || { id: conversationId };
            await this.loadMessages(conversationId);
        },

        async loadMessages(conversationId) {
            this.loadingMessages = true;
            try {
                const response = await fetch(`/chat/${conversationId}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.messages = data.messages || [];
                    this.currentConversation = data.conversation || this.currentConversation;

                    // Set booking info if available
                    if (data.booking) {
                        this.currentBooking = {
                            user_name: data.booking.user_name || 'N/A',
                            room_name: data.booking.room?.name || 'N/A',
                            check_in: data.booking.transaction?.check_in ? new Date(data.booking.transaction.check_in).toLocaleDateString('id-ID') : 'N/A',
                            check_out: data.booking.transaction?.check_out ? new Date(data.booking.transaction.check_out).toLocaleDateString('id-ID') : 'N/A'
                        };
                    }
                }

                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.loadingMessages = false;
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;

            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            try {
                const response = await fetch(`/chat/${this.currentConversation.id}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        message_text: messageText
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.messages.push(data.message);
                    this.$nextTick(() => {
                        this.scrollToBottom();
                    });
                } else {
                    alert('Failed to send message');
                    this.newMessage = messageText;
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('An error occurred while sending message');
                this.newMessage = messageText;
            } finally {
                this.sending = false;
                this.$refs.messageInput?.focus();
            }
        },

        backToConversations() {
            this.chatWindowOpen = false;
            this.currentConversation = null;
            this.currentBooking = null;
            this.messages = [];
            this.loadConversations();
        },

        searchConversations() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.loadConversations();
            }, 500);
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },

        getInitials(text) {
            if (!text) return 'U';
            const words = text.split(' ');
            if (words.length >= 2) {
                return (words[0][0] + words[1][0]).toUpperCase();
            }
            return text.substring(0, 2).toUpperCase();
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

        formatMessageTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        },

        openCheckedInModal() {
            if (typeof window.openCheckedInModal === 'function') {
                window.openCheckedInModal();
            }
        },

        checkAndNotify(newConversations) {
            if (!('Notification' in window) || Notification.permission !== 'granted') return;

            newConversations.forEach(conv => {
                const prevCount = this.previousUnreadCounts[conv.id] || 0;
                if (conv.unread_count > prevCount) {
                    try {
                        const senderName = conv.last_message_sender || conv.user_name || 'New message';
                        const preview = conv.last_message_text?.substring(0, 80) || 'You have a new message';

                        const notification = new Notification(senderName, {
                            body: preview,
                            icon: '/images/frist_icon.png',
                            tag: `chat-${conv.id}`,
                        });

                        notification.onclick = function() {
                            window.focus();
                            window.location.href = `/chat?open=${conv.id}`;
                            notification.close();
                        };

                        setTimeout(() => notification.close(), 5000);
                    } catch (e) {
                        console.error('Notification error:', e);
                    }
                }
                this.previousUnreadCounts[conv.id] = conv.unread_count;
            });
        }
    }
}
</script>
