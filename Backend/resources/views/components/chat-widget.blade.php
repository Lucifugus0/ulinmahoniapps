{{-- Floating Chat Widget --}}
<div id="chatWidget" x-data="chatWidget()" x-init="init()" class="fixed bottom-4 right-4 z-50">
    {{-- Chat Button (Always Visible) --}}
    <button @click="toggleConversations()"
            class="bg-blue-600 hover:bg-blue-700 text-white rounded-full w-14 h-14 flex items-center justify-center shadow-lg transition-all duration-300 hover:scale-110 hover:shadow-2xl active:scale-95"
            :class="{ 'hidden': conversationsOpen || chatWindowOpen }">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span x-show="totalUnread > 0"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-50"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-50"
              class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"
              x-text="totalUnread > 99 ? '99+' : totalUnread"></span>
    </button>

    {{-- Conversations List Popup --}}
    <div x-show="conversationsOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
         class="bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden"
         style="width: 360px; height: 500px; display: none;"
         @click.away="conversationsOpen = false">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 flex items-center justify-between">
            <h3 class="font-semibold text-lg">Messages</h3>
            <button @click="conversationsOpen = false" class="hover:bg-white/20 rounded-full p-1 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Search --}}
        <div class="p-3 border-b border-gray-200">
            <input type="text"
                   x-model="searchQuery"
                   @input="searchConversations()"
                   placeholder="Search conversations..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Conversations List --}}
        <div class="overflow-y-auto" style="height: calc(500px - 120px);">
            <template x-if="loading">
                <div class="flex items-center justify-center h-full">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loading && conversations.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">No conversations yet</p>
                </div>
            </template>

            <template x-if="!loading && conversations.length > 0">
                <div>
                    <template x-for="conversation in conversations" :key="conversation.id">
                        <div @click="openChat(conversation.id)"
                             class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 transition-all duration-200 hover:shadow-sm active:bg-gray-100">
                            <div class="flex items-start space-x-3">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold">
                                        <span x-text="getInitials(conversation.title || conversation.order_id)"></span>
                                    </div>
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4 class="text-sm font-semibold text-gray-900 truncate" x-text="conversation.title || 'Chat ' + conversation.order_id"></h4>
                                        <span class="text-xs text-gray-500" x-text="formatTime(conversation.last_message_at)"></span>
                                    </div>
                                    <p class="text-xs text-gray-600 truncate" x-text="conversation.order_id"></p>
                                    <div class="flex items-center justify-between mt-1">
                                        <p class="text-xs text-gray-500 truncate" x-text="conversation.messages_count + ' messages'"></p>
                                        <span x-show="conversation.unread_count > 0"
                                              x-transition:enter="transition ease-out duration-200"
                                              x-transition:enter-start="opacity-0 scale-50"
                                              x-transition:enter-end="opacity-100 scale-100"
                                              class="bg-blue-600 text-white text-xs rounded-full px-2 py-0.5 font-semibold"
                                              x-text="conversation.unread_count"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

    {{-- Chat Window Popup --}}
    <div x-show="chatWindowOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 transform translate-y-4 scale-95"
         class="bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden flex flex-col"
         style="width: 380px; height: 600px; display: none;"
         @click.away="minimizeChat()">

        {{-- Chat Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 flex items-center justify-between flex-shrink-0">
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
            <button @click="minimizeChat()" class="hover:bg-white/20 rounded-full p-1 transition ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                </svg>
            </button>
        </div>

        {{-- Messages Container --}}
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50" x-ref="messagesContainer" style="height: 0;">
            <template x-if="loadingMessages">
                <div class="flex items-center justify-center h-full">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loadingMessages && messages.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <p class="text-sm">No messages yet</p>
                    <p class="text-xs text-gray-400 mt-1">Start the conversation!</p>
                </div>
            </template>

            <template x-if="!loadingMessages && messages.length > 0">
                <div class="space-y-4">
                    <template x-for="message in messages" :key="message.id">
                        <div :class="message.sender_id === {{ auth()->id() }} ? 'flex justify-end' : 'flex justify-start'">
                            <div :class="message.sender_id === {{ auth()->id() }} ? 'flex flex-row-reverse items-end space-x-reverse space-x-2' : 'flex items-end space-x-2'">
                                {{-- Avatar --}}
                                <div class="flex-shrink-0">
                                    <div :class="message.sender_id === {{ auth()->id() }} ? 'w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-semibold' : 'w-8 h-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-semibold'">
                                        <span x-text="message.sender?.name?.charAt(0) || 'U'"></span>
                                    </div>
                                </div>

                                {{-- Message Bubble --}}
                                <div :class="message.sender_id === {{ auth()->id() }} ? 'max-w-xs' : 'max-w-xs'">
                                    <div :class="message.sender_id === {{ auth()->id() }} ? 'bg-blue-600 text-white rounded-2xl rounded-br-sm px-4 py-2' : 'bg-white text-gray-900 rounded-2xl rounded-bl-sm px-4 py-2 border border-gray-200'">
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
                                    <p class="text-xs text-gray-500 mt-1 px-2" x-text="formatMessageTime(message.created_at)"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

        {{-- Message Input --}}
        <div class="border-t border-gray-200 p-3 bg-white flex-shrink-0">
            <form @submit.prevent="sendMessage()" class="space-y-2">
                {{-- Image Preview --}}
                <div x-show="selectedImage"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform -translate-y-2"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="px-2">
                    <div class="relative inline-block">
                        <img x-ref="previewImg" src="" alt="Preview" class="max-h-24 rounded-lg border border-gray-300">
                        <button type="button" @click="clearImagePreview()"
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-end space-x-2">
                    <textarea x-ref="messageInput"
                              x-model="newMessage"
                              @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                              placeholder="Type a message..."
                              rows="1"
                              class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                              style="max-height: 100px;"></textarea>

                    {{-- Image Upload Button --}}
                    <input type="file" x-ref="imageInput" accept="image/jpeg,image/png,image/jpg"
                           class="hidden" @change="handleImageSelect($event)">
                    <button type="button" @click="$refs.imageInput.click()"
                            class="bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg px-3 py-2 transition-all duration-200 hover:shadow-md active:scale-95">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </button>

                    <button type="submit"
                            :disabled="(!newMessage.trim() && !selectedImage) || sending"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg px-4 py-2 transition-all duration-200 hover:shadow-md active:scale-95 disabled:hover:shadow-none disabled:active:scale-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function chatWidget() {
    return {
        conversationsOpen: false,
        chatWindowOpen: false,
        loading: false,
        loadingMessages: false,
        sending: false,
        conversations: [],
        messages: [],
        currentConversation: null,
        newMessage: '',
        searchQuery: '',
        totalUnread: 0,
        pollInterval: null,
        selectedImage: null,

        init() {
            this.loadConversations();
            // Poll for new messages every 10 seconds
            this.pollInterval = setInterval(() => {
                if (this.conversationsOpen || this.chatWindowOpen) {
                    this.loadConversations();
                    if (this.chatWindowOpen && this.currentConversation) {
                        this.loadMessages(this.currentConversation.id);
                    }
                }
            }, 10000);
        },

        toggleConversations() {
            this.conversationsOpen = !this.conversationsOpen;
            if (this.conversationsOpen) {
                this.chatWindowOpen = false;
                this.loadConversations();
            }
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
                    this.conversations = data.data.map(conv => ({
                        id: conv.id,
                        order_id: conv.order_id,
                        title: conv.title,
                        property: conv.property?.name || 'N/A',
                        messages_count: conv.messages_count || 0,
                        last_message_at: conv.last_message_at,
                        status: conv.status,
                        unread_count: conv.unread_count || 0
                    }));

                    // Calculate total unread
                    this.totalUnread = data.total_unread || this.conversations.reduce((sum, c) => sum + c.unread_count, 0);
                }
            } catch (error) {
                console.error('Error loading conversations:', error);
            } finally {
                this.loading = false;
            }
        },

        async openChat(conversationId) {
            this.conversationsOpen = false;
            this.chatWindowOpen = true;
            this.currentConversation = this.conversations.find(c => c.id == conversationId);
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
            if ((!this.newMessage.trim() && !this.selectedImage) || this.sending) return;

            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';

            try {
                let endpoint = `/chat/${this.currentConversation.id}/send`;
                let body;
                let headers = {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                };

                // If image is selected, use FormData and upload-image endpoint
                if (this.selectedImage) {
                    endpoint = `/chat/${this.currentConversation.id}/upload-image`;
                    body = new FormData();
                    if (messageText) {
                        body.append('message_text', messageText);
                    }
                    body.append('image', this.selectedImage);
                } else {
                    headers['Content-Type'] = 'application/json';
                    body = JSON.stringify({
                        message_text: messageText
                    });
                }

                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: headers,
                    body: body
                });

                const data = await response.json();

                if (data.success) {
                    // Add message to list
                    this.messages.push(data.message);
                    this.clearImagePreview();
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

        handleImageSelect(event) {
            const file = event.target.files[0];
            if (!file) return;

            // Validate file type
            if (!['image/jpeg', 'image/png', 'image/jpg'].includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, JPG)');
                event.target.value = '';
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must not exceed 5MB');
                event.target.value = '';
                return;
            }

            this.selectedImage = file;

            // Show preview
            const reader = new FileReader();
            reader.onload = (e) => {
                this.$refs.previewImg.src = e.target.result;
            };
            reader.readAsDataURL(file);
        },

        clearImagePreview() {
            this.selectedImage = null;
            if (this.$refs.imageInput) {
                this.$refs.imageInput.value = '';
            }
        },

        backToConversations() {
            this.chatWindowOpen = false;
            this.conversationsOpen = true;
            this.currentConversation = null;
            this.messages = [];
            this.clearImagePreview();
            this.loadConversations();
        },

        minimizeChat() {
            this.chatWindowOpen = false;
            this.conversationsOpen = false;
            this.clearImagePreview();
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
        }
    }
}
</script>
