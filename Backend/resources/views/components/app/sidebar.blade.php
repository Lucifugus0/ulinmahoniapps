{{-- Liquid glass styling: sidebar uses semi-transparent backgrounds with backdrop-filter blur for a frosted glass effect --}}
{{-- Restructured sidebar: removed 3 section headers (Management, Financial, Settings), replaced with flat structure of standalone items + collapsible groups --}}
<div x-data="{
    sidebarOpen: false,
    sidebarExpanded: localStorage.getItem('sidebarPersistent') === 'true',
    sidebarPersistent: localStorage.getItem('sidebarPersistent') === 'true',
    activeMenu: ''
}" class="flex">
    <div class="min-w-fit">
        <!-- Mobile Menu Button -->
        <button @click.stop="sidebarOpen = true" x-show="!sidebarOpen"
            class="fixed top-4 left-4 z-[60] lg:hidden p-2 rounded-lg bg-gray-800 text-white hover:bg-gray-700 transition-all shadow-lg"
            aria-label="Open sidebar" type="button">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <!-- Sidebar backdrop (mobile only) -->
        <div class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" @click="sidebarOpen = false"
            aria-hidden="true" x-cloak></div>

        <!-- Sidebar -->
        <div id="sidebar"
            class="flex lg:flex flex-col fixed lg:sticky z-50 lg:z-40 left-0 top-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar shrink-0 bg-gray-800 dark:bg-gray-900/60 p-4 border-r border-gray-200 dark:border-gray-700/60 shadow-2xl lg:shadow-none"
            style="transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1), transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); will-change: width, transform; backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);"
            :class="[
                sidebarExpanded || window.innerWidth < 1024 ? 'w-64' : 'w-20',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
            ]"
            x-init="$el.classList.toggle('sidebar-expanded', sidebarExpanded)" x-effect="$el.classList.toggle('sidebar-expanded', sidebarExpanded)"
            @mouseenter="if (!sidebarPersistent && window.innerWidth >= 1024) sidebarExpanded = true"
            @mouseleave="if (!sidebarPersistent && window.innerWidth >= 1024) sidebarExpanded = false"
            @click.away="if (window.innerWidth < 1024) sidebarOpen = false">

            <!-- Sidebar header -->
            <div class="flex justify-between mb-8 pr-3 sm:px-2">
                <!-- Close button -->
                <button class="lg:hidden text-gray-300 hover:text-white" @click.stop="sidebarOpen = !sidebarOpen"
                    aria-controls="sidebar" :aria-expanded="sidebarOpen">
                    <span class="sr-only">Close sidebar</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.7 18.7l1.4-1.4L7.8 13H20v-2H7.8l4.3-4.3-1.4-1.4L4 12z" />
                    </svg>
                </button>
                <!-- Logo -->
                <div class="flex flex-row gap-3 items-center" href="{{ route('dashboard') }}">
                    <img src="/images/frist_icon.png" alt="Booking Logo" class='w-10 h-10 rounded-lg bg-white p-0.5'>
                    <p class="text-white text-center text-lg font-bold transition-opacity duration-200"
                        :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0'"
                        x-show="sidebarExpanded || window.innerWidth < 1024">
                        {{ $globalTitle }}
                    </p>
                </div>
            </div>

            <!-- Links -->
            <div class="space-y-1"
                @click="if (window.innerWidth < 1024 && $event.target.tagName === 'A') sidebarOpen = false">

                {{-- ==================== --}}
                {{-- Standalone Items --}}
                {{-- ==================== --}}
                <ul class="space-y-1">
                    <!-- Dashboard -->
                    @can('view_dashboard')
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-colors group relative overflow-hidden @if (Route::is('dashboard')) bg-indigo-600 @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span class="whitespace-nowrap transition-all duration-300"
                                    style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                        'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_dashboard') }}</span>
                                <!-- Tooltip for collapsed state -->
                                <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_dashboard') }}
                                </div>
                            </a>
                        </li>
                    @endcan

                    <!-- Room Availability -->
                    @can('view_room_availability')
                        <li>
                            <a href="{{ route('room-availability.index') }}"
                                class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-colors group relative overflow-hidden @if (Route::is('room-availability.index')) bg-indigo-600 @endif">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 15l2 2 4-4" />
                                </svg>
                                <span class="whitespace-nowrap transition-all duration-300"
                                    style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                        'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_room_availability') }}</span>
                                <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_room_availability') }}
                                </div>
                            </a>
                        </li>
                    @endcan

                    <!-- Chat (with unread badge) -->
                    @can('manage_chat')
                        <li x-data="{ unreadCount: 0 }" x-init="// Fetch unread count on init
                        fetch('/chat/unread-count', {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    unreadCount = data.unread_count;
                                }
                            })
                            .catch(error => console.error('Error fetching unread count:', error));

                        // Refresh every 30 seconds
                        setInterval(() => {
                            fetch('/chat/unread-count', {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        unreadCount = data.unread_count;
                                    }
                                })
                                .catch(error => console.error('Error fetching unread count:', error));
                        }, 30000);">
                            <a href="{{ route('chat.index') }}"
                                class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-colors group relative overflow-hidden @if (Route::is('chat.index')) bg-indigo-600 @endif">
                                <!-- Chat Icon with Badge -->
                                <div class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <!-- Unread Badge (collapsed state) -->
                                    <span x-show="unreadCount > 0 && (!sidebarExpanded && window.innerWidth >= 1024)"
                                        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full min-w-[18px]"
                                        x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </div>

                                <span class="whitespace-nowrap transition-all duration-300 flex items-center gap-2"
                                    style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                        'lg:opacity-0 lg:max-w-0'">
                                    {{ __('ui.sidebar_chat') }}
                                    <!-- Unread Badge (expanded state) -->
                                    <span x-show="unreadCount > 0"
                                        class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full min-w-[20px]"
                                        x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </span>

                                <!-- Tooltip for collapsed state -->
                                <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50 flex items-center gap-2"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_chat') }}
                                    <span x-show="unreadCount > 0"
                                        class="inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full min-w-[18px]"
                                        x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                                </div>
                            </a>
                        </li>
                    @endcan
                </ul>

                {{-- ==================== --}}
                {{-- Bookings Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: bookings, check-in/out, change booking, door lock, parking management --}}
                @can('view_bookings')
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('checkin') ||
                            window.location.href.includes('checkout') ||
                            window.location.href.includes('bookings') ||
                            window.location.href.includes('pendings') ||
                            window.location.href.includes('newReserv') ||
                            window.location.href.includes('completed') ||
                            window.location.href.includes('change-room') ||
                            window.location.href.includes('door-locks') ||
                            window.location.href.includes('/parking')) { activeMenu = 'bookings' }">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'bookings' ? '' : 'bookings'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-colors cursor-pointer group relative overflow-hidden @if (Route::is(
                                        'checkin.index',
                                        'checkout.index',
                                        'bookings.index',
                                        'pendings.index',
                                        'completed.index',
                                        'newReserv.index',
                                        'changerooom.index',
                                        'door-locks.*',
                                        'parking.index')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                            'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_bookings') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'bookings' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[1rem]' :
                                        'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_bookings') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'bookings' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    @can('view_all_bookings')
                                        <li>
                                            <a href="{{ route('bookings.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('bookings.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_all_bookings') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_pending_bookings')
                                        <li>
                                            <a href="{{ route('pendings.index') }}"
                                                class="flex items-center justify-between px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('pendings.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_pending') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_confirmed_bookings')
                                        <li>
                                            <a href="{{ route('newReserv.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('newReserv.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_confirmed_bookings') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_checkins')
                                        <li>
                                            <a href="{{ route('checkin.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('checkin.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_checked_ins') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_checkouts')
                                        <li>
                                            <a href="{{ route('checkout.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('checkout.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_checked_outs') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_completed_bookings')
                                        <li>
                                            <a href="{{ route('completed.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('completed.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_completed') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Renamed from "Change Room" to "Change Booking" --}}
                                    @can('view_change_room')
                                        <li>
                                            <a href="{{ route('changerooom.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('changerooom.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_change_room') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Door Lock: moved from Rooms/Units group to Bookings --}}
                                    @can('view_door_locks')
                                        <li>
                                            <a href="{{ route('door-locks.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('door-locks.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_door_lock') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Parking Management: moved from standalone to Bookings sub-item --}}
                                    @can('view_parking')
                                        <li>
                                            <a href="{{ route('parking.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-colors @if (Route::is('parking.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcan

                {{-- ==================== --}}
                {{-- Finance Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: parking entry, deposit entry, booking payment, refunds (replaces Financial section + Payments sub-group) --}}
                @canany(['view_payments', 'view_parking_payments', 'view_deposit_payments', 'view_refunds'])
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('/payment/')) { activeMenu = 'finance' }
                        @if (Route::is('admin.payments.*', 'admin.parking-payments.*', 'admin.deposit-payments.*', 'admin.refunds.*')) activeMenu = 'finance' @endif">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'finance' ? '' : 'finance'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('admin.payments.*', 'admin.parking-payments.*', 'admin.deposit-payments.*', 'admin.refunds.*')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_finance') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'finance' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ?
                                        'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_finance') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'finance' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    {{-- Renamed from "Parking" to "Parking Entry" --}}
                                    @can('view_parking_payments')
                                        <li>
                                            <a href="{{ route('admin.parking-payments.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('admin.parking-payments.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking_entry') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Renamed from "Deposit" to "Deposit Entry" --}}
                                    @can('view_deposit_payments')
                                        <li>
                                            <a href="{{ route('admin.deposit-payments.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('admin.deposit-payments.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_deposit_entry') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Renamed from "Transaction" to "Booking Payment" --}}
                                    @can('view_payments')
                                        <li>
                                            <a href="{{ route('admin.payments.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('admin.payments.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_booking_payment') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_refunds')
                                        <li>
                                            <a href="{{ route('admin.refunds.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('admin.refunds.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_refunds') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcanany

                {{-- ==================== --}}
                {{-- Promo Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: voucher management, banner management --}}
                @canany(['view_vouchers', 'view_promo_banners'])
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('vouchers') ||
                            window.location.href.includes('promo-banners')) { activeMenu = 'promo' }">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'promo' ? '' : 'promo'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('vouchers.*', 'promo-banners.*')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Tag/Promo Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_promo') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'promo' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ?
                                        'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_promo') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'promo' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    {{-- Renamed from "Vouchers" to "Voucher Management" --}}
                                    @can('view_vouchers')
                                        <li>
                                            <a href="{{ route('vouchers.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('vouchers.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_vouchers') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Renamed from "Promo Banners" to "Banner Management" --}}
                                    @can('view_promo_banners')
                                        <li>
                                            <a href="{{ route('promo-banners.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('promo-banners.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_promo_banners') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcanany

                {{-- ==================== --}}
                {{-- Reports Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: moved from Financial section to top-level --}}
                @can('view_reports')
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('reports/booking') ||
                            window.location.href.includes('reports/payment') ||
                            window.location.href.includes('reports/parking') ||
                            window.location.href.includes('reports/deposit') ||
                            window.location.href.includes('reports/rented-rooms')) { activeMenu = 'reports' }">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'reports' ? '' : 'reports'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-all duration-300 cursor-pointer group relative @if (Route::is(
                                        'reports.booking.*',
                                        'reports.payment.*',
                                        'reports.parking.*',
                                        'reports.deposit.*',
                                        'reports.rented-rooms.*')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_reports') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'reports' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ?
                                        'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_reports') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'reports' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    @can('view_booking_report')
                                        <li>
                                            <a href="{{ route('reports.booking.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('reports.booking.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_booking_report') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_payment_report')
                                        <li>
                                            <a href="{{ route('reports.payment.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('reports.payment.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_payment_report') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_parking_report')
                                        <li>
                                            <a href="{{ route('reports.parking.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('reports.parking.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking_report') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_deposit_report')
                                        <li>
                                            <a href="{{ route('reports.deposit.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('reports.deposit.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_deposit_report') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    @can('view_rented_rooms_report')
                                        <li>
                                            <a href="{{ route('reports.rented-rooms.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('reports.rented-rooms.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_rented_rooms_report') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcan

                {{-- ==================== --}}
                {{-- Masters Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: consolidates Properties, Rooms/Units, Customers, Users into one master data section --}}
                @canany(['view_cities', 'view_properties', 'view_property_facilities', 'view_deposit_fees', 'view_parking_fees', 'view_rooms', 'view_room_facilities', 'view_customers', 'view_users'])
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('m-properties') ||
                            window.location.href.includes('facilityProperty') ||
                            window.location.href.includes('cities') ||
                            window.location.href.includes('calendar') ||
                            window.location.href.includes('deposit-fees') ||
                            window.location.href.includes('property-fees') ||
                            window.location.href.includes('m-rooms') ||
                            window.location.href.includes('room-name-types') ||
                            window.location.href.includes('facilityRooms') ||
                            window.location.href.includes('customers') ||
                            window.location.href.includes('users-newManagement')) { activeMenu = 'masters' }">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'masters' ? '' : 'masters'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-all duration-300 cursor-pointer group relative @if (Route::is(
                                        'properties.index',
                                        'facilityProperty.index',
                                        'cityProperty.index',
                                        'calendar.index',
                                        'deposit-fees.index',
                                        'property-fees.index',
                                        'rooms.index',
                                        'roomNameTypes.index',
                                        'facilityRooms.index',
                                        'customers.*')) bg-indigo-600 @elseif(Route::is('users-newManagement')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Database/Master Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_masters') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'masters' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ?
                                        'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_masters') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'masters' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    {{-- Cities: was "Master Cities" --}}
                                    @can('view_cities')
                                        <li>
                                            <a href="{{ route('cityProperty.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('cityProperty.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_cities') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Properties: renamed from "Master Properties" --}}
                                    @can('view_properties')
                                        <li>
                                            <a href="{{ route('properties.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('properties.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_properties') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Property's Facilities: renamed from "Master Facilities" (property) --}}
                                    @can('view_property_facilities')
                                        <li>
                                            <a href="{{ route('facilityProperty.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('facilityProperty.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_property_facilities') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Property's Deposit & Parking: renamed from "Master Deposit & Parking" --}}
                                    @canany(['view_deposit_fees', 'view_parking_fees'])
                                        <li>
                                            <a href="{{ route('property-fees.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('property-fees.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_property_fees') }}</span>
                                            </a>
                                        </li>
                                    @endcanany
                                    {{-- Property's Rooms: renamed from "Master Rooms" --}}
                                    @can('view_rooms')
                                        <li>
                                            <a href="{{ route('rooms.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('rooms.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_rooms') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Room Types: renamed from "Master Room Types" --}}
                                    @can('view_rooms')
                                        <li>
                                            <a href="{{ route('roomNameTypes.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('roomNameTypes.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_room_types') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Room's Facilities: renamed from "Master Facilities" (room) --}}
                                    @can('view_room_facilities')
                                        <li>
                                            <a href="{{ route('facilityRooms.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('facilityRooms.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_room_facilities') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Daily Pricing Management: renamed from "Master Calendar" --}}
                                    @can('view_properties')
                                        <li>
                                            <a href="{{ route('calendar.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('calendar.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_calendar') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Customers: moved from standalone to Masters --}}
                                    @can('view_customers')
                                        <li>
                                            <a href="{{ route('customers.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('customers.*')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_customers') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Users: moved from Settings section to Masters --}}
                                    @can('view_users')
                                        <li>
                                            <a href="{{ route('users-newManagement') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('users-newManagement')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_users') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcanany

                {{-- ==================== --}}
                {{-- App Management Group --}}
                {{-- ==================== --}}
                {{-- Collapsible group: access management (role & permission), settings (replaces Settings section) --}}
                @canany(['manage_roles', 'manage_settings'])
                    <ul class="space-y-1 mt-2">
                        <li x-init="if (window.location.href.includes('master-role-management') ||
                            window.location.href.includes('user-access') ||
                            window.location.href.includes('users/show') ||
                            window.location.href.includes('maintenance')) { activeMenu = 'appManagement' }
                        @if (Route::is('master-role-management', 'user-access.*', 'users.show', 'maintenance.index')) activeMenu = 'appManagement' @endif">

                            <!-- Main Menu Button -->
                            <a @click="activeMenu = activeMenu === 'appManagement' ? '' : 'appManagement'"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-600/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('master-role-management', 'user-access.*', 'users.show', 'maintenance.index')) bg-indigo-600 @endif">
                                <div class="flex items-center gap-3 min-w-0">
                                    <!-- Gear/Cog Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                        {{ __('ui.sidebar_app_management') }}
                                    </span>
                                </div>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                    style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="[
                                        activeMenu === 'appManagement' ? 'rotate-180' : '',
                                        sidebarExpanded || window.innerWidth < 1024 ?
                                        'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                    ]"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                    style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                    :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                    {{ __('ui.sidebar_app_management') }}
                                </div>
                            </a>

                            <!-- Submenu Items -->
                            <div x-show="activeMenu === 'appManagement' && (sidebarExpanded || window.innerWidth < 1024)"
                                x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">
                                <ul class="pl-8 mt-1 space-y-1">
                                    {{-- Access Management: renamed from "Role & Permission" --}}
                                    @can('manage_roles')
                                        <li>
                                            <a href="{{ route('master-role-management') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('master-role-management')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_role_permission') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Settings --}}
                                    @can('manage_settings')
                                        <li>
                                            <a href="{{ route('users.show') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('users.show')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_settings') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                    {{-- Maintenance Mode: only visible to super admins --}}
                                    @can('manage_settings')
                                        <li>
                                            <a href="{{ route('maintenance.index') }}"
                                                class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-600/50 transition-all duration-300 @if (Route::is('maintenance.index')) bg-indigo-600 @endif">
                                                <span class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_maintenance_mode') }}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    </ul>
                @endcanany

            </div>

        </div>

            <!-- Purple circular expand/collapse button — vertically centered on the sidebar right edge.
                 Placed outside the scrollable sidebar div so overflow doesn't clip it. -->
            <button
                class="hidden lg:flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg fixed top-1/2 -translate-y-1/2 z-50 transition-all duration-300"
                :style="'left: ' + (sidebarExpanded ? 'calc(16rem - 16px)' : 'calc(5rem - 16px)') + ';'"
                @click="sidebarPersistent = !sidebarPersistent; sidebarExpanded = sidebarPersistent; localStorage.setItem('sidebarPersistent', sidebarPersistent)"
                title="Toggle sidebar"
                :aria-pressed="sidebarPersistent.toString()">
                {{-- Show ">" chevron when collapsed, "<" chevron when expanded --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"
                    :class="sidebarPersistent ? 'rotate-180' : ''"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </button>
    </div>
</div>
