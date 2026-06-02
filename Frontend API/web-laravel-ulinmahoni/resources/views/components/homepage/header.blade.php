<!-- Header Component
     - Supports 3 locales: ID, EN, ZH (Simplified Chinese)
     - Dark mode toggle (sun/moon icon) persisted in localStorage('dark-mode')
     - Language switcher dropdown with flag icons for each locale
-->
{{-- Header text override — consistent across ALL pages and scroll positions.
     The header is now always frosted (see inline --glass-bg / --glass-blur-strong below),
     so text is ALWAYS dark in light mode and white in dark mode. No scroll-driven swap.
     Beats the `.site-header a/span/button { color: #1f2937/#f3f4f6 !important; }` rules in
     styles.blade.php by matching selector + !important and appearing later in cascade.
     Dropdown menu items are excluded so their own light/dark text colors keep working. --}}
<style>
/* Light mode — dark text for legibility over the frosted-glass header (every page). */
.site-header a,
.site-header span,
.site-header button {
    color: #1f2937 !important;
    font-weight: 700 !important;
}
.site-header a:hover,
.site-header button:hover {
    color: #000000 !important;
    opacity: 1;
}
/* Dark mode — white text on every page, regardless of scroll position. */
html.dark .site-header a,
html.dark .site-header span,
html.dark .site-header button {
    color: #ffffff !important;
}
html.dark .site-header a:hover,
html.dark .site-header button:hover {
    color: #ffffff !important;
    opacity: 0.85;
}
/* Dropdown panel items keep their own weight + text colors so menus don't look chunky. */
.site-header .header-dropdown a,
.site-header .header-dropdown span,
.site-header .header-dropdown button,
.site-header .header-dropdown p {
    font-weight: 400 !important;
    color: inherit !important;
}
</style>
<!-- Header — floating liquid glass bar, fixed over content with a constant frosted backdrop.
     The frosted look is now FIXED (no scroll ramp): a light translucent white tint + 20px blur
     in light mode, applied via the inline --glass-bg / --glass-blur-strong CSS variables which
     the .site-header rule in styles.blade.php reads from. Dark mode background is supplied by
     the `html.dark .site-header` rule (it sets `background` directly, ignoring --glass-bg). -->
<header class="site-header py-4 px-6 flex items-center justify-between fixed top-0 left-0 right-0 z-50 transition-all duration-500" style="--glass-bg: rgba(255, 255, 255, 0.6); --glass-blur-strong: blur(20px);">
    <div class="flex items-center space-x-8">
        <!-- Mobile Menu Button (Hidden on desktop) -->
        <div x-data="{ mobileMenuOpen: false }" class="md:hidden">
            <button @click="mobileMenuOpen = true" class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

            <!-- Mobile Sidebar -->
            <div x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 w-64 bg-white dark:bg-gray-800 shadow-lg z-50"
                 style="display: none;"
                 @click.away="mobileMenuOpen = false">
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    @php
                        $mobileLocale = app()->getLocale();
                    @endphp
                    <a href="/{{ $mobileLocale }}/homepage" class="flex items-center">
                        <img src="{{ asset('images/assets/new-ullinmahoni.PNG') }}" alt="Ulin Mahoni Logo" class="h-8 w-auto">
                    </a>
                    <button @click="mobileMenuOpen = false" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                {{-- Mobile nav — same active detection as desktop --}}
                @php
                    $mPath = request()->path();
                    $mIsHome = str_contains($mPath, 'homepage');
                    $mIsPartnership = str_contains($mPath, 'kerjasama') || str_contains($mPath, 'partnership');
                    $mIsBusiness = str_contains($mPath, 'business');
                    $mIsAbout = str_contains($mPath, 'tentang') || str_contains($mPath, 'about');
                    $mIsBookings = str_starts_with($mPath, 'bookings');
                    $mobileNavClass = 'block px-4 py-2 text-base text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors';
                @endphp
                <nav class="p-4">
                    <ul class="space-y-3">
                        <li>
                            <a href="/{{ $mobileLocale }}/homepage" class="{{ $mobileNavClass }} {{ $mIsHome ? 'font-bold' : '' }}">
                                {{ __('common.navigation.home') }}
                            </a>
                        </li>
                        <li>
                            @php
                                $partnershipUrl = match($mobileLocale) {
                                    'en' => 'partnership',
                                    'zh' => 'partnership',
                                    default => 'kerjasama',
                                };
                                $partnershipLabel = match($mobileLocale) {
                                    'en' => 'Partnership',
                                    'zh' => '合作',
                                    default => 'Kerjasama',
                                };
                            @endphp
                            <a href="/{{ $mobileLocale }}/{{ $partnershipUrl }}" class="{{ $mobileNavClass }} {{ $mIsPartnership ? 'font-bold' : '' }}">
                                {{ $partnershipLabel }}
                            </a>
                        </li>
                        <li>
                            @php
                                $businessLabel = match($mobileLocale) {
                                    'en' => 'Corporate',
                                    'zh' => '企业',
                                    default => 'Korporasi',
                                };
                            @endphp
                            <a href="/{{ $mobileLocale }}/business" class="{{ $mobileNavClass }} {{ $mIsBusiness ? 'font-bold' : '' }}">
                                {{ $businessLabel }}
                            </a>
                        </li>
                        <li>
                            @php
                                $aboutUrl = match($mobileLocale) {
                                    'en' => 'about',
                                    'zh' => 'about',
                                    default => 'tentang',
                                };
                                $aboutLabel = match($mobileLocale) {
                                    'en' => 'About Us',
                                    'zh' => '关于我们',
                                    default => 'Tentang Kami',
                                };
                            @endphp
                            <a href="/{{ $mobileLocale }}/{{ $aboutUrl }}" class="{{ $mobileNavClass }} {{ $mIsAbout ? 'font-bold' : '' }}">
                                {{ $aboutLabel }}
                            </a>
                        </li>
                        {{-- "My Bookings" link in mobile nav — only visible when logged in --}}
                        @auth
                        <li>
                            @php
                                $mobileBookingsLabel = match($mobileLocale) {
                                    'en' => 'My Bookings',
                                    'zh' => '我的预订',
                                    default => 'Pemesanan Saya',
                                };
                            @endphp
                            <a href="/bookings" class="{{ $mobileNavClass }} {{ $mIsBookings ? 'font-bold' : '' }}">
                                {{ $mobileBookingsLabel }}
                            </a>
                        </li>
                        @endauth
                    </ul>
                </nav>
            </div>
            <!-- Overlay -->
            <div x-show="mobileMenuOpen"
                 x-transition.opacity
                 class="fixed inset-0 bg-black bg-opacity-50 z-40"
                 style="display: none;"
                 @click="mobileMenuOpen = false">
            </div>
        </div>

        <!-- Logo for Desktop -->
        @php
            $logoLocale = app()->getLocale();
        @endphp
        <a href="/{{ $logoLocale }}/homepage" class="hidden md:flex items-center">
            <img src="{{ asset('images/assets/new-ullinmahoni.PNG') }}" alt="Ulin Mahoni Logo" class="h-10 w-auto">
        </a>

        <!-- Navigation — consistent text-sm, black in light / white in dark, bold when active -->
        <nav class="hidden md:flex">
            <ul class="flex space-x-6">
                @php
                    $locale = app()->getLocale();
                    $currentPath = request()->path();

                    // Detect active page from URL path
                    $isHome = str_contains($currentPath, 'homepage');
                    $isPartnership = str_contains($currentPath, 'kerjasama') || str_contains($currentPath, 'partnership');
                    $isBusiness = str_contains($currentPath, 'business');
                    $isAbout = str_contains($currentPath, 'tentang') || str_contains($currentPath, 'about');
                    $isBookings = str_starts_with($currentPath, 'bookings');

                    // Shared nav link classes: black in light, white in dark
                    $navClass = 'text-base text-gray-900 dark:text-white hover:opacity-75 transition-colors duration-200';
                @endphp
                <li>
                    <a href="/{{ $locale }}/homepage" class="{{ $navClass }} {{ $isHome ? 'font-bold' : '' }}">
                        {{ __('common.navigation.home') }}
                    </a>
                </li>
                <li>
                    @php
                        $navPartnershipUrl = match($locale) {
                            'en' => 'partnership',
                            'zh' => 'partnership',
                            default => 'kerjasama',
                        };
                        $navPartnershipLabel = match($locale) {
                            'en' => 'Partnership',
                            'zh' => '合作',
                            default => 'Kerjasama',
                        };
                    @endphp
                    <a href="/{{ $locale }}/{{ $navPartnershipUrl }}" class="{{ $navClass }} {{ $isPartnership ? 'font-bold' : '' }}">
                        {{ $navPartnershipLabel }}
                    </a>
                </li>
                <li>
                    @php
                        $navBusinessLabel = match($locale) {
                            'en' => 'Corporate',
                            'zh' => '企业',
                            default => 'Korporasi',
                        };
                    @endphp
                    <a href="/{{ $locale }}/business" class="{{ $navClass }} {{ $isBusiness ? 'font-bold' : '' }}">
                        {{ $navBusinessLabel }}
                    </a>
                </li>
                <li>
                    @php
                        $navAboutUrl = match($locale) {
                            'en' => 'about',
                            'zh' => 'about',
                            default => 'tentang',
                        };
                        $navAboutLabel = match($locale) {
                            'en' => 'About Us',
                            'zh' => '关于我们',
                            default => 'Tentang Kami',
                        };
                    @endphp
                    <a href="/{{ $locale }}/{{ $navAboutUrl }}" class="{{ $navClass }} {{ $isAbout ? 'font-bold' : '' }}">
                        {{ $navAboutLabel }}
                    </a>
                </li>
                {{-- "My Bookings" link in header — only visible when logged in --}}
                @auth
                <li>
                    @php
                        $navBookingsLabel = match($locale) {
                            'en' => 'My Bookings',
                            'zh' => '我的预订',
                            default => 'Pemesanan Saya',
                        };
                    @endphp
                    <a href="/bookings" class="{{ $navClass }} {{ $isBookings ? 'font-bold' : '' }}">
                        {{ $navBookingsLabel }}
                    </a>
                </li>
                @endauth
            </ul>
        </nav>
    </div>

    <!-- Right Side: Theme Toggle + Language Switcher + Auth -->
    <div class="flex items-center space-x-4">
        <!-- Dark Mode Toggle: toggles 'dark' class on <html>, persists in localStorage('dark-mode') -->
        <button
            x-data="{ dark: localStorage.getItem('dark-mode') === 'true' }"
            x-init="$watch('dark', val => { localStorage.setItem('dark-mode', val); document.documentElement.classList.toggle('dark', val) }); document.documentElement.classList.toggle('dark', dark)"
            @click="dark = !dark"
            type="button"
            class="p-2 rounded-lg text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200"
            :title="dark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
            <!-- Moon icon (shown in dark mode — click to switch to light) -->
            <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <!-- Sun icon (shown in light mode — click to switch to dark) -->
            <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </button>

        @php
            // Get current locale from app (set by SetLocale middleware)
            $currentLocale = app()->getLocale();
            $isEn = $currentLocale === 'en';
            $isZh = $currentLocale === 'zh';

            // Get current path and remove locale prefix
            $currentPath = request()->path();
            $pathWithoutLocale = preg_replace('/^(id|en|zh)\/?/', '', $currentPath);

            // Define flag SVGs
            $idFlag = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32"><path d="M31,8c0-2.209-1.791-4-4-4H5c-2.209,0-4,1.791-4,4v9H31V8Z" fill="#ea3323"></path><path d="M5,28H27c2.209,0,4-1.791,4-4v-8H1v8c0,2.209,1.791,4,4,4Z" fill="#fff"></path><path d="M5,28H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4ZM2,8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8Z" opacity=".15"></path><path d="M27,5H5c-1.657,0-3,1.343-3,3v1c0-1.657,1.343-3,3-3H27c1.657,0,3,1.343,3,3v-1c0-1.657-1.343-3-3-3Z" fill="#fff" opacity=".2"></path></svg>';
            $enFlag = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 60 30"><clipPath id="a"><path d="M0 0v30h60V0z"/></clipPath><clipPath id="b"><path d="M30 15h30v15zv15H0zH0V0z"/></clipPath><g clip-path="url(#a)"><path d="M0 0v30h60V0z" fill="#012169"/><path d="M0 0l60 30m0-30L0 30" stroke="#fff" stroke-width="6"/><path d="M0 0l60 30m0-30L0 30" clip-path="url(#b)" stroke="#C8102E" stroke-width="4"/><path d="M30 0v30M0 15h60" stroke="#fff" stroke-width="10"/><path d="M0 15h60M30 0v30" stroke="#C8102E" stroke-width="6"/></g></svg>';
            $zhFlag = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32"><rect x="1" y="4" width="30" height="24" rx="4" ry="4" fill="#ee1c25"></rect><path d="M7.5,7l.93,2.87H11.4l-2.4,1.74.92,2.83L7.5,12.7,5.08,14.44,6,11.61,3.6,9.87H6.57Z" fill="#ff0"></path><path d="M13,7.33l-.64,1.27-1.38.27,1,.97-.18,1.4L13,10.5l1.2.74-.18-1.4,1-.97-1.38-.27Z" fill="#ff0"></path><path d="M15.17,9.83l-1.1.77.02-1.35-1.04-.86,1.33-.24.58-1.22.58,1.22,1.33.24-1.04.86.02,1.35Z" fill="#ff0"></path><path d="M15.5,13.33l-.93-.97-1.37.33.65-1.22-.65-1.22,1.37.33.93-.97v1.36l1.17.62-1.17.62Z" fill="#ff0"></path><path d="M13.5,15.67l.33-1.37-.97-.93,1.36,0,.62-1.17.62,1.17h1.36l-.97.93.33,1.37-1.18-.62Z" fill="#ff0"></path><path d="M5,28H27c2.209,0,4-1.791,4-4V8c0-2.209-1.791-4-4-4H5c-2.209,0-4,1.791-4,4V24c0,2.209,1.791,4,4,4ZM2,8c0-1.654,1.346-3,3-3H27c1.654,0,3,1.346,3,3V24c0,1.654-1.346,3-3,3H5c-1.654,0-3-1.346-3-3V8Z" opacity=".15"></path></svg>';

            // Determine current flag and label
            $currentFlag = $isEn ? $enFlag : ($isZh ? $zhFlag : $idFlag);
            $currentLabel = $isEn ? 'EN' : ($isZh ? 'ZH' : 'ID');
        @endphp

        <!-- Language Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" type="button" class="flex items-center space-x-1 px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                <span class="flex items-center">
                    {!! $currentFlag !!}
                    <span class="ml-1 text-base text-gray-900 dark:text-white">{{ $currentLabel }}</span>
                </span>
                <svg class="w-4 h-4 text-gray-900 dark:text-white" :class="{ 'transform rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown menu -->
            <div x-show="open"
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="header-dropdown absolute right-0 mt-2 w-36 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 focus:outline-none z-50"
                 style="display: none;">
                <div class="py-1">
                    <!-- Indonesian Language Form -->
                    <form method="POST" action="{{ route('language.switch') }}" class="block">
                        @csrf
                        <input type="hidden" name="locale" value="id">
                        <input type="hidden" name="redirect_url" value="{{ request()->fullUrl() }}">
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $currentLocale === 'id' ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                            <span class="flex items-center">
                                {!! $idFlag !!}
                                <span class="ml-2">Indonesia</span>
                            </span>
                        </button>
                    </form>

                    <!-- English Language Form -->
                    <form method="POST" action="{{ route('language.switch') }}" class="block">
                        @csrf
                        <input type="hidden" name="locale" value="en">
                        <input type="hidden" name="redirect_url" value="{{ request()->fullUrl() }}">
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isEn ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                            <span class="flex items-center">
                                {!! $enFlag !!}
                                <span class="ml-2">English</span>
                            </span>
                        </button>
                    </form>

                    <!-- Chinese Language Form -->
                    <form method="POST" action="{{ route('language.switch') }}" class="block">
                        @csrf
                        <input type="hidden" name="locale" value="zh">
                        <input type="hidden" name="redirect_url" value="{{ request()->fullUrl() }}">
                        <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isZh ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                            <span class="flex items-center">
                                {!! $zhFlag !!}
                                <span class="ml-2">简体中文</span>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @guest
            @php
                $authLocale = app()->getLocale();
                $signInLabel = match($authLocale) {
                    'en' => 'Sign In',
                    'zh' => '登录',
                    default => 'Masuk',
                };
                $signUpLabel = match($authLocale) {
                    'en' => 'Sign Up',
                    'zh' => '注册',
                    default => 'Daftar',
                };
            @endphp
            <a href="{{ route('login') }}" class="text-base font-medium text-gray-900 dark:text-white hover:opacity-75 px-4 py-2 rounded-lg transition-colors duration-200">
                {{ $signInLabel }}
            </a>
            {{-- Daftar (Sign Up) — uses var(--accent) so it follows the theme:
                 light = UM Green, dark = UM Maroon. !important on color overrides
                 the .site-header a !important rule in styles.blade.php:376. --}}
            <a href="{{ route('register') }}"
               class="text-base font-medium px-4 py-2 rounded-lg transition-colors duration-200 btn-um-themed"
               style="color: #ffffff !important;">
                {{ $signUpLabel }}
            </a>
        @else
            <!-- Profile dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="flex items-center space-x-2 rounded-lg p-1 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                    <span class="text-base font-medium text-gray-900 dark:text-white">{{ Auth::user()->username }}</span>
                    <svg class="w-4 h-4 text-gray-900 dark:text-white" :class="{ 'transform rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>

                </button>

                <!-- Dropdown menu — uses fixed positioning to escape header's backdrop-filter context -->
                <div x-show="open"
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="header-dropdown fixed w-56 rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700 focus:outline-none"
                     style="display: none; top: 64px; right: 16px; z-index: 60;">
                    <div class="py-1">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->username }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="{{ route('profile.show') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Profil
                        </a>
                        @if(Auth::user()->is_admin)
                        <!-- Link to Backend admin dashboard using ADMIN_URL env variable -->
                        <a href="{{ config('app.admin_url', env('ADMIN_URL', '/dashboard')) }}/dashboard" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                            <svg class="mr-3 h-5 w-5 text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                            </svg>
                            Admin Dashboard
                        </a>
                        @endif
                        <div class="border-t border-gray-100 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <svg class="mr-3 h-5 w-5 text-red-500 dark:text-red-400 group-hover:text-red-600 dark:group-hover:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endguest
    </div>
</header>

{{-- Floating ticket widget — appears for authenticated users on all public pages --}}
@include('components.ticket.floating-widget')

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Scroll-driven header transparency was removed on 2026-05-18.
     The header is now permanently frosted with fixed --glass-bg / --glass-blur-strong
     values set inline on the .site-header element, and text colors no longer depend on
     a .is-scrolled class — they are constant (dark in light mode, white in dark mode).
     This makes the header look identical on every page (homepage, property/house
     detail, listings) and at every scroll position. --}}
