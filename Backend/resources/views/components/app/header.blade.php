<!-- Header — liquid glass bar with backdrop blur, matching frontend glassmorphism style.
     Blur styles are inline (not in app.css) because the Vite/Lightning CSS build strips
     the unprefixed backdrop-filter property, which Firefox requires. -->
<style>
    /* Header ::before pseudo-element provides the glassmorphism blur layer.
       Both prefixed and unprefixed backdrop-filter are needed for cross-browser support
       (Firefox uses unprefixed, Safari/Chrome use -webkit- prefix). */
    .admin-header::before {
        content: '';
        position: absolute;
        inset: 0;
        z-index: -1;
        background: rgba(255, 255, 255, 0.35);
        -webkit-backdrop-filter: blur(48px);
        backdrop-filter: blur(48px);
        border-bottom: 1px solid rgba(200, 200, 200, 0.30);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
    }
    html.dark .admin-header::before {
        background: rgba(10, 10, 25, 0.35);
        -webkit-backdrop-filter: blur(48px);
        backdrop-filter: blur(48px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
    }
</style>
<header class="admin-header sticky top-0 z-30">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 {{ $variant === 'v2' || $variant === 'v3' ? '' : 'lg:border-b border-gray-200 dark:border-gray-700/60' }}">

            <!-- Header: Left side -->
            <div class="flex">
                
                <!-- Hamburger button -->
                <button
                    class="text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 lg:hidden"
                    @click.stop="sidebarOpen = !sidebarOpen"
                    aria-controls="sidebar"
                    :aria-expanded="sidebarOpen"
                >
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <rect x="4" y="5" width="16" height="2" />
                        <rect x="4" y="11" width="16" height="2" />
                        <rect x="4" y="17" width="16" height="2" />
                    </svg>
                </button>

            </div>

            <!-- Header: Right side -->
            <div class="flex items-center space-x-3">

                <!-- Search Button with Modal -->
                {{-- <x-modal-search /> --}}

                {{-- Unified notifications dropdown — replaces the legacy chat-only dropdown.
                     Aggregates chat unread + broadcast unread + future push types. --}}
                @auth
                    <x-dropdown-notifications align="right" />
                @endauth

                <!-- Info button -->
                {{-- <x-dropdown-help align="right" /> --}}

                <!-- Dark mode toggle -->
                <x-theme-toggle />

                <!-- Language switcher -->
                <x-language-switcher />

                <!-- Divider -->
                <hr class="w-px h-6 bg-gray-200 dark:bg-gray-700/60 border-none" />

                <!-- User button -->
                <x-dropdown-profile align="right" />

            </div>

        </div>
    </div>
</header>