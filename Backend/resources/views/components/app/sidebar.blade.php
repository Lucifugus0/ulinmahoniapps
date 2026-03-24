{{-- Liquid glass styling: sidebar uses semi-transparent backgrounds with backdrop-filter blur for a frosted glass effect --}}
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
                <!-- Dashboard -->
                <div class="mb-4">
                    <h3
                        class="text-xs uppercase text-indigo-400 dark:text-indigo-300 font-semibold pl-3 mb-2 overflow-hidden">
                        <span class="inline-block text-center transition-all duration-300"
                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                            :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'opacity-100 max-w-[1.5rem]' :
                                'opacity-0 max-w-0'"
                            aria-hidden="true">•••</span>
                        <span class="inline-block transition-all duration-300"
                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                'opacity-0 max-w-0'">{{ __('ui.sidebar_management') }}</span>
                    </h3>
                    <ul class="space-y-1">
                        <!-- Dashboard -->
                        @can('view_dashboard')
                            <li>
                                <a href="{{ route('dashboard') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('dashboard')) bg-indigo-900/60 @endif">
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

                        <!-- Bookings Menu Item -->
                        @can('view_bookings')
                            <li x-init="if (window.location.href.includes('checkin') ||
                                window.location.href.includes('checkout') ||
                                window.location.href.includes('bookings') ||
                                window.location.href.includes('pendings') ||
                                window.location.href.includes('newReserv') ||
                                window.location.href.includes('completed') ||
                                window.location.href.includes('change-room')) { activeMenu = 'bookings' }">

                                <!-- Main Menu Button -->
                                <a @click="activeMenu = activeMenu === 'bookings' ? '' : 'bookings'"
                                    class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors cursor-pointer group relative overflow-hidden @if (Route::is(
                                            'checkin.index',
                                            'checkout.index',
                                            'bookings.index',
                                            'pendings.index',
                                            'completed.index',
                                            'newReserv.index',
                                            'changerooom.index')) bg-indigo-900/60 @endif">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Calendar Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>

                                        <!-- Menu Text -->
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">
                                            {{ __('ui.sidebar_bookings') }}
                                        </span>
                                    </div>

                                    <!-- Chevron Icon -->
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

                                    <!-- Tooltip for Collapsed State -->
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
                                        <!-- All Bookings -->
                                        @can('view_all_bookings')
                                            <li>
                                                <a href="{{ route('bookings.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('bookings.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_all_bookings') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Pending -->
                                        @can('view_pending_bookings')
                                            <li>
                                                <a href="{{ route('pendings.index') }}"
                                                    class="flex items-center justify-between px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('pendings.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_pending') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Confirm Bookings -->
                                        @can('view_confirmed_bookings')
                                            <li>
                                                <a href="{{ route('newReserv.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('newReserv.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_confirmed_bookings') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Checked-ins -->
                                        @can('view_checkins')
                                            <li>
                                                <a href="{{ route('checkin.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('checkin.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_checked_ins') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Checked-outs -->
                                        @can('view_checkouts')
                                            <li>
                                                <a href="{{ route('checkout.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('checkout.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_checked_outs') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Completed -->
                                        @can('view_completed_bookings')
                                            <li>
                                                <a href="{{ route('completed.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('completed.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_completed') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Change Room -->
                                        @can('view_change_room')
                                            <li>
                                                <a href="{{ route('changerooom.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-colors @if (Route::is('changerooom.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_change_room') }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcan

                        <!-- Properties Menu Item -->
                        @canany(['view_properties', 'view_property_facilities', 'view_cities', 'view_deposit_fees'])
                            <li x-init="if (window.location.href.includes('m-properties') ||
                                window.location.href.includes('facilityProperty') ||
                                window.location.href.includes('cities') ||
                                window.location.href.includes('calendar') ||
                                window.location.href.includes('deposit-fees')) { activeMenu = 'properties' }">

                                <!-- Main Menu Button -->
                                <a @click="activeMenu = activeMenu === 'properties' ? '' : 'properties'"
                                    class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('properties.index', 'facilityProperty.index', 'cityProperty.index', 'calendar.index', 'deposit-fees.index')) bg-indigo-900/60 @endif">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Building Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>

                                        <!-- Menu Text -->
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">
                                            {{ __('ui.sidebar_properties') }}
                                        </span>
                                    </div>

                                    <!-- Chevron Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                        style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="[
                                            activeMenu === 'properties' ? 'rotate-180' : '',
                                            sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[1rem]' :
                                            'lg:opacity-0 lg:max-w-0'
                                        ]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>

                                    <!-- Tooltip for Collapsed State -->
                                    <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_properties') }}
                                    </div>
                                </a>

                                <!-- Submenu Items -->
                                <div x-show="activeMenu === 'properties' && (sidebarExpanded || window.innerWidth < 1024)"
                                    x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                    x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">

                                    <ul class="pl-8 mt-1 space-y-1">
                                        <!-- Master Properties -->
                                        @can('view_properties')
                                            <li>
                                                <a href="{{ route('properties.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('properties.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_properties') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Cities -->
                                        @can('view_cities')
                                            <li>
                                                <a href="{{ route('cityProperty.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('cityProperty.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_cities') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Facilities -->
                                        @can('view_property_facilities')
                                            <li>
                                                <a href="{{ route('facilityProperty.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('facilityProperty.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_facilities') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Calendar -->
                                        @can('view_properties')
                                            <li>
                                                <a href="{{ route('calendar.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('calendar.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_calendar') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Deposit Fee Management -->
                                        @can('view_deposit_fees')
                                            <li>
                                                <a href="{{ route('deposit-fees.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('deposit-fees.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_deposit_fees') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>
                            </li>
                        @endcanany

                        <!-- Parking Menu Item -->
                        @canany(['view_parking_fees', 'view_parking'])
                            <li x-init="if (window.location.href.includes('parking-fees') ||
                                window.location.href.includes('/parking')) { activeMenu = 'parking' }">

                                <!-- Main Menu Button -->
                                <a @click="activeMenu = activeMenu === 'parking' ? '' : 'parking'"
                                    class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('parking-fees.index', 'parking.index')) bg-indigo-900/60 @endif">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Parking Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <rect x="3" y="3" width="18" height="18" rx="4"
                                                stroke-width="2" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 16V8h4a3 3 0 010 6h-4" />
                                        </svg>
                                        <!-- Menu Text -->
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">
                                            {{ __('ui.sidebar_parking_menu') }}
                                        </span>
                                    </div>

                                    <!-- Chevron Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                        style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="[
                                            activeMenu === 'parking' ? 'rotate-180' : '',
                                            sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[1rem]' :
                                            'lg:opacity-0 lg:max-w-0'
                                        ]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>

                                    <!-- Tooltip for Collapsed State -->
                                    <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_parking_menu') }}
                                    </div>
                                </a>

                                <!-- Submenu Items -->
                                <div x-show="activeMenu === 'parking' && (sidebarExpanded || window.innerWidth < 1024)"
                                    x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                    x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">

                                    <ul class="pl-8 mt-1 space-y-1">
                                        <!-- Master Parking Fee -->
                                        @can('view_parking_fees')
                                            <li>
                                                <a href="{{ route('parking-fees.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('parking-fees.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking_fees') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Parking -->
                                        @can('view_parking')
                                            <li>
                                                <a href="{{ route('parking.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('parking.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking') }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>
                        @endcanany

                        <!-- Rooms/Units Menu Item -->
                        @can('rooms')
                            <li x-init="if (window.location.href.includes('m-rooms') ||
                                window.location.href.includes('room-name-types') ||
                                window.location.href.includes('facilityRooms')) { activeMenu = 'rooms' }">

                                <!-- Main Menu Button -->
                                <a @click="activeMenu = activeMenu === 'rooms' ? '' : 'rooms'"
                                    class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('rooms.index', 'facilityRooms.index')) bg-indigo-900/60 @endif">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Folder Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                        </svg>

                                        <!-- Menu Text -->
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">
                                            {{ __('ui.sidebar_rooms_units') }}
                                        </span>
                                    </div>

                                    <!-- Chevron Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                        style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="[
                                            activeMenu === 'rooms' ? 'rotate-180' : '',
                                            sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[1rem]' :
                                            'lg:opacity-0 lg:max-w-0'
                                        ]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>

                                    <!-- Tooltip for Collapsed State -->
                                    <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_rooms_units') }}
                                    </div>
                                </a>

                                <!-- Submenu Items -->
                                <div x-show="activeMenu === 'rooms' && (sidebarExpanded || window.innerWidth < 1024)"
                                    x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                    x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">

                                    <ul class="pl-8 mt-1 space-y-1">
                                        <!-- Master Rooms -->
                                        @can('view_rooms')
                                            <li>
                                                <a href="{{ route('rooms.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('rooms.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_rooms') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Room Types -->
                                        @can('view_rooms')
                                            <li>
                                                <a href="{{ route('roomNameTypes.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('roomNameTypes.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_room_types') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Master Facilities -->
                                        @can('view_room_facilities')
                                            <li>
                                                <a href="{{ route('facilityRooms.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('facilityRooms.index')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_master_facilities') }}</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Door Lock -->
                                        @can('view_door_locks')
                                            <li>
                                                <a href="{{ route('door-locks.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('door-locks.*')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">Door Lock</span>
                                                </a>
                                            </li>
                                        @endcan

                                    </ul>
                                </div>
                            </li>
                        @endcan


                        <!-- Customers -->
                        @can('view_customers')
                            <li>
                                <a href="{{ route('customers.index') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('customers.*')) bg-indigo-900/60 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                            'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_customers') }}</span>
                                    <!-- Tooltip for collapsed state -->
                                    <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_customers') }}
                                    </div>
                                </a>
                            </li>
                        @endcan

                        <!-- Room Availability -->
                        @can('view_room_availability')
                            <li>
                                <a href="{{ route('room-availability.index') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('room-availability.index')) bg-indigo-900/60 @endif">
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

                        <!-- Master Vouchers -->
                        @can('view_vouchers')
                            <li>
                                <a href="{{ route('vouchers.index') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('vouchers.*')) bg-indigo-900/60 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                            'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_vouchers') }}</span>

                                    <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_vouchers') }}
                                    </div>
                                </a>
                            </li>
                        @endcan

                        <!-- Promo Banners -->
                        @can('view_promo_banners')
                            <li>
                                <a href="{{ route('promo-banners.index') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('promo-banners.*')) bg-indigo-900/60 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                            'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_promo_banners') }}</span>

                                    <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_promo_banners') }}
                                    </div>
                                </a>
                            </li>
                        @endcan

                        <!-- Chat -->
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
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('chat.index')) bg-indigo-900/60 @endif">
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
                </div>

                <!-- Financial -->
                @can('financial')
                    <div class="mb-4">
                        <h3 class="text-xs uppercase text-indigo-400 dark:text-indigo-300 font-semibold pl-3 mb-2">
                            <span x-show="!sidebarExpanded && window.innerWidth >= 1024" class="text-center w-6"
                                aria-hidden="true">•••</span>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024"
                                class="transition-opacity duration-200">{{ __('ui.sidebar_financial') }}</span>
                        </h3>
                        <ul class="space-y-1">
                            <!-- Payment Menu Item -->
                            <li x-init="if (window.location.href.includes('admin/payments') ||
                                window.location.href.includes('admin/parking-payments') ||
                                window.location.href.includes('admin/deposit-payments')) { activeMenu = 'payments' }
                            @if (Route::is('admin.payments.*', 'admin.parking-payments.*', 'admin.deposit-payments.*')) activeMenu = 'payments' @endif">

                                <!-- Main Menu Button -->
                                <a @click="activeMenu = activeMenu === 'payments' ? '' : 'payments'"
                                    class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 cursor-pointer group relative @if (Route::is('admin.payments.*', 'admin.parking-payments.*', 'admin.deposit-payments.*')) bg-indigo-900/60 @endif">

                                    <div class="flex items-center gap-3 min-w-0">
                                        <!-- Payment Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>

                                        <!-- Menu Text -->
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ?
                                                'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                            Payment
                                        </span>
                                    </div>

                                    <!-- Chevron Icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 flex-shrink-0 transition-all duration-300"
                                        style="transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="[
                                            activeMenu === 'payments' ? 'rotate-180' : '',
                                            sidebarExpanded || window.innerWidth < 1024 ?
                                            'opacity-100 max-w-[1rem]' : 'lg:opacity-0 lg:max-w-0'
                                        ]"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>

                                    <!-- Tooltip for Collapsed State -->
                                    <div class="absolute left-full ml-2 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-all duration-200 pointer-events-none z-50 whitespace-nowrap shadow-lg"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        Payment
                                    </div>
                                </a>

                                <!-- Submenu Items -->
                                <div x-show="activeMenu === 'payments' && (sidebarExpanded || window.innerWidth < 1024)"
                                    x-collapse x-transition:enter="transition-[height] ease-out duration-300"
                                    x-transition:leave="transition-[height] ease-in duration-200" class="overflow-hidden">

                                    <ul class="pl-8 mt-1 space-y-1">
                                        <!-- Transaction -->
                                        @can('view_payments')
                                            <li>
                                                <a href="{{ route('admin.payments.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('admin.payments.*')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">Transaction</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Parking -->
                                        @can('view_parking_payments')
                                            <li>
                                                <a href="{{ route('admin.parking-payments.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('admin.parking-payments.*')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">Parking</span>
                                                </a>
                                            </li>
                                        @endcan

                                        <!-- Deposit -->
                                        @can('view_deposit_payments')
                                            <li>
                                                <a href="{{ route('admin.deposit-payments.index') }}"
                                                    class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('admin.deposit-payments.*')) bg-indigo-900/60 @endif">
                                                    <span
                                                        class="text-xs transition-all duration-300 hover:translate-x-1">Deposit</span>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </div>
                            </li>

                            <!-- Refunds -->
                            <li>
                                <a href="{{ route('admin.refunds.index') }}"
                                    class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('admin.refunds.index')) bg-indigo-900/60 @endif">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    <span class="whitespace-nowrap transition-all duration-300"
                                        style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                            'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_refunds') }}</span>
                                    <!-- Tooltip for collapsed state -->
                                    <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                        style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                        :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                        {{ __('ui.sidebar_refunds') }}
                                    </div>
                                </a>
                            </li>
                            @can('view_refunds')
                            @endcan

                            <!-- Reports Menu Item -->
                            @can('view_reports')
                                <li x-init="if (window.location.href.includes('reports/booking') ||
                                    window.location.href.includes('reports/payment') ||
                                    window.location.href.includes('reports/parking') ||
                                    window.location.href.includes('reports/deposit') ||
                                    window.location.href.includes('reports/rented-rooms')) { activeMenu = 'reports' }">

                                    <!-- Main Menu Button -->
                                    <a @click="activeMenu = activeMenu === 'reports' ? '' : 'reports'"
                                        class="flex items-center justify-between gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 cursor-pointer group relative @if (Route::is(
                                                'reports.booking.*',
                                                'reports.payment.*',
                                                'reports.parking.*',
                                                'reports.deposit.*',
                                                'reports.rented-rooms.*')) bg-indigo-900/60 @endif">

                                        <div class="flex items-center gap-3 min-w-0">
                                            <!-- Chart/Report Icon -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>

                                            <!-- Menu Text -->
                                            <span class="whitespace-nowrap transition-all duration-300"
                                                style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                                :class="sidebarExpanded || window.innerWidth < 1024 ?
                                                    'opacity-100 max-w-[200px]' : 'lg:opacity-0 lg:max-w-0'">
                                                {{ __('ui.sidebar_reports') }}
                                            </span>
                                        </div>

                                        <!-- Chevron Icon -->
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

                                        <!-- Tooltip for Collapsed State -->
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
                                            <!-- Booking Report -->
                                            @can('view_booking_report')
                                                <li>
                                                    <a href="{{ route('reports.booking.index') }}"
                                                        class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('reports.booking.*')) bg-indigo-900/60 @endif">
                                                        <span
                                                            class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_booking_report') }}</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- Payment Report -->
                                            @can('view_payment_report')
                                                <li>
                                                    <a href="{{ route('reports.payment.index') }}"
                                                        class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('reports.payment.*')) bg-indigo-900/60 @endif">
                                                        <span
                                                            class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_payment_report') }}</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- Parking Report -->
                                            @can('view_parking_report')
                                                <li>
                                                    <a href="{{ route('reports.parking.index') }}"
                                                        class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('reports.parking.*')) bg-indigo-900/60 @endif">
                                                        <span
                                                            class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_parking_report') }}</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- Deposit Report -->
                                            @can('view_deposit_report')
                                                <li>
                                                    <a href="{{ route('reports.deposit.index') }}"
                                                        class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('reports.deposit.*')) bg-indigo-900/60 @endif">
                                                        <span
                                                            class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_deposit_report') }}</span>
                                                    </a>
                                                </li>
                                            @endcan

                                            <!-- Rented Rooms Report -->
                                            @can('view_rented_rooms_report')
                                                <li>
                                                    <a href="{{ route('reports.rented-rooms.index') }}"
                                                        class="flex items-center gap-3 px-3 py-2 text-indigo-200 rounded-lg hover:bg-indigo-700/50 transition-all duration-300 @if (Route::is('reports.rented-rooms.*')) bg-indigo-900/60 @endif">
                                                        <span
                                                            class="text-xs transition-all duration-300 hover:translate-x-1">{{ __('ui.sidebar_rented_rooms_report') }}</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </li>
                            @endcan


                        </ul>
                    </div>
                @endcan

                <!-- Settings -->
                @can('Settings')
                    <div>
                        <h3 class="text-xs uppercase text-indigo-400 dark:text-indigo-300 font-semibold pl-3 mb-2">
                            <span x-show="!sidebarExpanded && window.innerWidth >= 1024" class="text-center w-6"
                                aria-hidden="true">•••</span>
                            <span x-show="sidebarExpanded || window.innerWidth < 1024"
                                class="transition-opacity duration-200">{{ __('ui.sidebar_settings') }}</span>
                        </h3>
                        <ul class="space-y-1">
                            <!-- Users -->
                            @can('view_users')
                                <li>
                                    <a href="{{ route('users-newManagement') }}"
                                        class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('users-newManagement')) bg-indigo-900/60 @endif">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_users') }}</span>
                                        <!-- Tooltip for collapsed state -->
                                        <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                            style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                            {{ __('ui.sidebar_users') }}
                                        </div>
                                    </a>
                                </li>
                            @endcan

                            <!-- Role & Permission -->
                            @can('manage_roles')
                                <li>
                                    <a href="{{ route('master-role-management') }}"
                                        class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-all duration-300 group relative @if (Route::is('master-role-management')) bg-indigo-900/60 @endif">
                                        <!-- Lock Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c-1.657 0-3 1.343-3 3v4h6v-4c0-1.657-1.343-3-3-3z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 11V7a5 5 0 10-10 0v4" />
                                            <rect x="6" y="11" width="12" height="10" rx="2" ry="2"
                                                stroke="currentColor" stroke-width="2" fill="none" />
                                        </svg>
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_role_permission') }}</span>
                                        <!-- Tooltip for collapsed state -->
                                        <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                            style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                            {{ __('ui.sidebar_role_permission') }}
                                        </div>
                                    </a>
                                </li>
                            @endcan
                            <!-- Account / Settings -->
                            @can('manage_settings')
                                <li>
                                    <a href="{{ route('users.show') }}"
                                        class="flex items-center gap-3 px-3 py-2 text-white rounded-lg hover:bg-indigo-700/50 transition-colors group relative overflow-hidden @if (Route::is('users.show')) bg-indigo-900/60 @endif">
                                        <!-- Gear Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="whitespace-nowrap transition-all duration-300"
                                            style="transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1), max-width 0.3s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="sidebarExpanded || window.innerWidth < 1024 ? 'opacity-100 max-w-[200px]' :
                                                'lg:opacity-0 lg:max-w-0'">{{ __('ui.sidebar_settings') }}</span>
                                        <!-- Tooltip for collapsed state -->
                                        <div class="absolute left-16 bg-gray-900 text-white px-2 py-1 rounded text-sm opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-50"
                                            style="transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1);"
                                            :class="!sidebarExpanded && window.innerWidth >= 1024 ? 'block' : 'hidden'">
                                            {{ __('ui.sidebar_settings') }}
                                        </div>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </div>
                @endcan
            </div>

            <!-- Expand / collapse button -->
            <div class="pt-3 hidden lg:inline-flex justify-end mt-auto">
                <div class="px-3 py-2">
                    <div class="flex flex-col gap-2">
                        <!-- Persistent Mode Toggle -->
                        <button
                            class="flex items-center justify-center gap-2 px-3 py-2 text-indigo-300 hover:text-white transition-colors rounded-lg border border-indigo-300/30 hover:border-indigo-200/50"
                            @click="sidebarPersistent = !sidebarPersistent; sidebarExpanded = sidebarPersistent; localStorage.setItem('sidebarPersistent', sidebarPersistent)"
                            :class="sidebarPersistent ? 'bg-indigo-900/30 text-white border-indigo-400' : ''"
                            title="Toggle persistent sidebar" :aria-pressed="sidebarPersistent.toString()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"
                                    x-show="!sidebarPersistent" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14M12 5v14" x-show="sidebarPersistent" />
                            </svg>
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
