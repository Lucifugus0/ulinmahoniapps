@props([
    'align' => 'right'
])

{{-- Unified notifications dropdown — chat, broadcast, and other push messages aggregated.
     Replaces the legacy chat-only dropdown. Polls /notifications/feed every 30s. --}}
<div class="relative inline-flex" x-data="notificationCenter()" x-init="init()">
    <button
        class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 lg:hover:bg-gray-200 dark:hover:bg-gray-700/50 dark:lg:hover:bg-gray-800 rounded-full relative"
        :class="{ 'bg-gray-200 dark:bg-gray-800': open }"
        aria-haspopup="true"
        @click.prevent="toggleDropdown()"
        :aria-expanded="open"
    >
        <span class="sr-only">Notifications</span>
        <svg class="fill-current text-gray-500/80 dark:text-gray-400/80" width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
            <path d="M6.5 0C2.91 0 0 2.91 0 6.5 0 8.32.74 9.97 1.93 11.16L.15 14.41a.75.75 0 0 0 .9 1.06l3.7-1.23A6.5 6.5 0 1 0 6.5 0Zm0 11.5a5 5 0 1 1 0-10 5 5 0 0 1 0 10Z"/>
            <circle cx="4" cy="6.5" r="1"/>
            <circle cx="6.5" cy="6.5" r="1"/>
            <circle cx="9" cy="6.5" r="1"/>
        </svg>
        {{-- Unread count badge — shown when totalUnread > 0 --}}
        <span x-show="totalUnread > 0"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-50"
              x-transition:enter-end="opacity-100 scale-100"
              x-text="totalUnread > 99 ? '99+' : totalUnread"
              class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold border-2 border-white dark:border-gray-900"></span>
    </button>

    {{-- Dropdown panel --}}
    <div
        class="origin-top-right z-10 absolute top-full -mr-48 sm:mr-0 min-w-80 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg overflow-hidden mt-1 {{ $align === 'right' ? 'right-0' : 'left-0' }}"
        style="width: 380px; background-color: white; isolation: isolate;"
        x-bind:style="document.documentElement.classList.contains('dark') ? 'width: 380px; background-color: rgb(31, 41, 55); isolation: isolate;' : 'width: 380px; background-color: white; isolation: isolate;'"
        @click.outside="closeDropdown()"
        @keydown.escape.window="closeDropdown()"
        x-show="open"
        x-transition:enter="transition ease-out duration-200 transform"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
    >
        {{-- Header with title + Mark all as read --}}
        <div class="flex items-center justify-between pt-3 pb-2 px-4 border-b border-gray-200 dark:border-gray-700/60">
            <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase">{{ __('ui.notifications') ?? 'Notifications' }}</div>
            <button @click.prevent="markAllRead()"
                    :disabled="totalUnread === 0"
                    class="text-xs text-blue-500 hover:text-blue-600 font-medium disabled:opacity-40 disabled:cursor-not-allowed">
                {{ __('ui.mark_all_read') ?? 'Mark all read' }}
            </button>
        </div>

        {{-- Items list --}}
        <div class="overflow-y-auto" style="max-height: 440px;">
            <template x-if="loading">
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                </div>
            </template>

            <template x-if="!loading && items.length === 0">
                <div class="flex flex-col items-center justify-center py-10 text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <p class="text-sm">{{ __('ui.no_notifications') ?? 'No new notifications' }}</p>
                </div>
            </template>

            <template x-if="!loading && items.length > 0">
                <ul>
                    <template x-for="item in items" :key="item.id">
                        <li class="border-b border-gray-200 dark:border-gray-700/60 last:border-0">
                            <a :href="item.link"
                               class="block py-3 px-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 cursor-pointer">
                                <div class="flex items-start space-x-3">
                                    {{-- Type icon --}}
                                    <div class="flex-shrink-0">
                                        <div :class="item.type === 'chat'
                                                ? 'w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center'
                                                : 'w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center'">
                                            <template x-if="item.type === 'chat'">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M8 0C3.58 0 0 3.58 0 8c0 1.5.41 2.9 1.12 4.1L.05 15.05a.75.75 0 00.9.9l2.95-1.07A7.96 7.96 0 008 16c4.42 0 8-3.58 8-8s-3.58-8-8-8z"/>
                                                </svg>
                                            </template>
                                            <template x-if="item.type === 'broadcast'">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M18 3a1 1 0 00-1.196-.98l-10 2A1 1 0 006 5v9.114A4.369 4.369 0 005 14c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V7.82l8-1.6v5.894A4.37 4.37 0 0015 12c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2V3z"/>
                                                </svg>
                                            </template>
                                        </div>
                                    </div>
                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate" x-text="item.title"></span>
                                            <span class="text-xs text-gray-400 dark:text-gray-500 ml-2 flex-shrink-0" x-text="formatTime(item.timestamp)"></span>
                                        </div>
                                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate" x-text="item.body"></p>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase"
                                                  :class="item.type === 'chat'
                                                          ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                                                          : 'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'"
                                                  x-text="item.type"></span>
                                            <span x-show="item.unread_count > 1"
                                                  class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-600 text-white"
                                                  x-text="item.unread_count"></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </li>
                    </template>
                </ul>
            </template>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        /* Alpine component for the unified notification center dropdown */
        function notificationCenter() {
            return {
                open: false,
                loading: false,
                items: [],
                totalUnread: 0,
                pollInterval: null,

                init() {
                    /* Initial fetch + 30s polling for badge updates */
                    this.fetchFeed();
                    this.pollInterval = setInterval(() => this.fetchFeed(), 30000);
                },

                async fetchFeed() {
                    try {
                        const res = await fetch('{{ route('notifications.feed') }}', {
                            headers: { 'Accept': 'application/json' },
                            credentials: 'same-origin',
                        });
                        if (!res.ok) return;
                        const data = await res.json();
                        if (data.success) {
                            this.items = data.items || [];
                            this.totalUnread = data.total_unread || 0;
                        }
                    } catch (e) {
                        console.error('Notification feed fetch failed:', e);
                    }
                },

                toggleDropdown() {
                    this.open = !this.open;
                    if (this.open) {
                        this.loading = true;
                        this.fetchFeed().finally(() => { this.loading = false; });
                    }
                },

                closeDropdown() {
                    this.open = false;
                },

                async markAllRead() {
                    if (this.totalUnread === 0) return;
                    try {
                        const res = await fetch('{{ route('notifications.mark-all-read') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            credentials: 'same-origin',
                        });
                        if (res.ok) {
                            this.totalUnread = 0;
                            /* Refresh feed to clear unread items */
                            await this.fetchFeed();
                        }
                    } catch (e) {
                        console.error('Mark all read failed:', e);
                    }
                },

                /* Format ISO timestamp as relative time (e.g. "5m ago", "2h ago", "3d ago") */
                formatTime(iso) {
                    if (!iso) return '';
                    const now = new Date();
                    const then = new Date(iso);
                    const diffMs = now - then;
                    const diffSec = Math.floor(diffMs / 1000);
                    if (diffSec < 60) return 'now';
                    const diffMin = Math.floor(diffSec / 60);
                    if (diffMin < 60) return diffMin + 'm';
                    const diffHr = Math.floor(diffMin / 60);
                    if (diffHr < 24) return diffHr + 'h';
                    const diffDay = Math.floor(diffHr / 24);
                    if (diffDay < 7) return diffDay + 'd';
                    return then.toLocaleDateString();
                },
            };
        }
    </script>
    @endpush
@endonce
