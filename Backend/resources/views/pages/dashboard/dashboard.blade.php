<x-app-layout>
    <style>
        .dashboard-banner { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .dark .dashboard-banner { background: linear-gradient(135deg, rgb(30 41 59) 0%, rgb(30 27 75) 100%); }
        .dark .card-header-green { background: linear-gradient(to right, #052e16, #042f2e) !important; }
        .dark .card-header-blue { background: linear-gradient(to right, #0c1a3d, #0c2a3d) !important; }
        .dark .card-header-indigo { background: linear-gradient(to right, #1a0f3e, #1e0a3a) !important; }
        .dark .card-header-emerald { background: linear-gradient(to right, #052e1a, #052e16) !important; }
        .dark .card-header-blue-indigo { background: linear-gradient(to right, #0c1a3d, #1a0f3e) !important; }
        .dark .card-indigo { background-color: rgba(49, 46, 129, 0.4) !important; border-color: #4338ca !important; }
        .dark .card-indigo p.label { color: #a5b4fc !important; }
        .dark .card-indigo p.value { color: #c7d2fe !important; }
        .dark .card-purple { background-color: rgba(88, 28, 135, 0.4) !important; border-color: #7c3aed !important; }
        .dark .card-purple p.label { color: #c4b5fd !important; }
        .dark .card-purple p.value { color: #ddd6fe !important; }
    </style>
    @php
        // Helper function untuk check widget access
        $userRole = Auth::user()->role;
        $canViewWidget = function ($widgetSlug) use ($userRole) {
            // Jika user tidak punya role, tidak bisa lihat widget apapun
            if (!$userRole) {
                return false;
            }

            // Check akses widget selalu lewat role_dashboard_widgets, termasuk super admin
            return $userRole->hasWidgetAccess($widgetSlug);
        };
    @endphp

    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard Banner -->
        <div class="relative bg-cover bg-center p-4 sm:p-6 rounded-xl overflow-hidden mb-8 shadow-lg dashboard-banner">

            <!-- Content -->
            <div class="relative">
                <!-- Header Section (No Overlay) -->
                <div
                    class="relative bg-white/15 dark:bg-white/5 backdrop-blur-sm rounded-lg p-4 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                        <div>
                            <h1 class="text-2xl md:text-3xl text-white font-bold mb-1 drop-shadow-lg">
                                {{ __('ui.dashboard') }}
                                @if (Auth::user()->isSiteRole() && Auth::user()->property)
                                    - {{ Auth::user()->property->name ?? 'Unknown' }}
                                @else
                                    {{ Auth::user()->role->name ?? 'Ulin Mahoni' }}
                                @endif
                            </h1>
                            <p class="text-blue-100 font-medium drop-shadow">{{ __('ui.welcome_back') }}
                                {{ Auth::user()->first_name }}
                                {{ Auth::user()->last_name }}
                                @if (Auth::user()->role)
                                    <span class="text-yellow-300">• {{ Auth::user()->role->name }}</span>
                                @endif
                            </p>
                        </div>

                        <div class="mt-4 md:mt-0 flex items-center space-x-3">
                            @if (Auth::user()->role)
                                <span
                                    class="bg-white/20 backdrop-blur-sm rounded-lg px-3 py-2 text-white text-sm font-medium border border-white/20">
                                    {{ Auth::user()->role->name }}
                                </span>
                            @endif
                            <div
                                class="flex items-center bg-white/10 backdrop-blur-sm rounded-lg p-2 border border-white/20">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300 mr-2"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span class="text-white text-sm font-medium">{{ __('ui.last_updated') }}
                                    {{ now()->format('d M, Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    // Check if user has access to any booking stats widgets
                    $hasBookingStats =
                        $canViewWidget('booking_today') ||
                        $canViewWidget('booking_checkin') ||
                        $canViewWidget('booking_checkout');
                @endphp

                @if ($hasBookingStats)
                    <!-- Quick Stats Section with Overlay -->
                    <div
                        class="relative bg-white/10 dark:bg-white/5 backdrop-blur-md rounded-xl p-6 border border-white/10 shadow-2xl">

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @if ($canViewWidget('booking_today'))
                                <!-- Confirm Booking (Today) -->
                                <div
                                    class="group relative bg-gradient-to-br from-purple-500/20 to-purple-600/10 backdrop-blur-sm rounded-xl p-5 border border-purple-400/30 hover:border-purple-400/50 hover:shadow-lg hover:shadow-purple-500/20 transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col h-full">
                                        <div class="flex justify-between items-start mb-3">
                                            <div
                                                class="bg-purple-500/30 p-3 rounded-lg border border-purple-400/30 group-hover:bg-purple-500/40 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-purple-100"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-purple-100 text-xs font-semibold uppercase tracking-wide mb-2">
                                                {{ __('ui.booking_today') }}</p>
                                            <h3 class="text-white text-3xl font-bold mb-1 drop-shadow">
                                                {{ $stats['today'] ?? 0 }}</h3>
                                            <p class="text-purple-200 text-sm">{{ __('ui.arrivals') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($canViewWidget('booking_checkin'))
                                <!-- Check-In -->
                                <div
                                    class="group relative bg-gradient-to-br from-green-500/20 to-green-600/10 backdrop-blur-sm rounded-xl p-5 border border-green-400/30 hover:border-green-400/50 hover:shadow-lg hover:shadow-green-500/20 transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col h-full">
                                        <div class="flex justify-between items-start mb-3">
                                            <div
                                                class="bg-green-500/30 p-3 rounded-lg border border-green-400/30 group-hover:bg-green-500/40 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-100"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-green-100 text-xs font-semibold uppercase tracking-wide mb-2">
                                                {{ __('ui.check_in') }}</p>
                                            <h3 class="text-white text-3xl font-bold mb-1 drop-shadow">
                                                {{ $stats['checkin'] ?? 0 }}</h3>
                                            <p class="text-green-200 text-sm">
                                                @if ($isSite && $propertyName)
                                                    {{ __('ui.staying_at') }} {{ $propertyName }}
                                                @else
                                                    {{ __('ui.staying') }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($canViewWidget('booking_checkout'))
                                <!-- Check-Out -->
                                <div
                                    class="group relative bg-gradient-to-br from-yellow-500/20 to-yellow-600/10 backdrop-blur-sm rounded-xl p-5 border border-yellow-400/30 hover:border-yellow-400/50 hover:shadow-lg hover:shadow-yellow-500/20 transition-all duration-300 hover:-translate-y-1">
                                    <div class="flex flex-col h-full">
                                        <div class="flex justify-between items-start mb-3">
                                            <div
                                                class="bg-yellow-500/30 p-3 rounded-lg border border-yellow-400/30 group-hover:bg-yellow-500/40 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-100"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p
                                                class="text-yellow-100 text-xs font-semibold uppercase tracking-wide mb-2">
                                                {{ __('ui.check_out') }}</p>
                                            <h3 class="text-white text-3xl font-bold mb-1 drop-shadow">
                                                {{ $stats['checkout'] ?? 0 }}</h3>
                                            <p class="text-yellow-200 text-sm">{{ __('ui.checkout_reminder') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

            </div>
        </div>

        @php
            // Check if user has access to any finance widgets
            $hasFinanceWidgets =
                $canViewWidget('finance_today_revenue') ||
                $canViewWidget('finance_monthly_revenue') ||
                $canViewWidget('finance_pending_payments') ||
                $canViewWidget('finance_payment_success_rate') ||
                $canViewWidget('finance_payment_methods');
        @endphp

        @if ($hasFinanceWidgets)
            <!-- Finance Information Section -->
            <div class="mt-8">
                <div class="flex items-center space-x-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-emerald-600" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('ui.financial_info') }}</h2>
                </div>

                <!-- Financial Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @if ($canViewWidget('finance_today_revenue'))
                        <!-- Today's Revenue -->
                        <div
                            class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-white/20 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ __('ui.today') }}</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-medium text-emerald-100 mb-1">{{ __('ui.today_revenue') }}</h3>
                            <div class="text-3xl font-bold mb-2">
                                Rp {{ number_format($financeStats['today_revenue'] ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="flex items-center text-sm text-emerald-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <span>{{ $financeStats['today_transactions'] ?? 0 }}
                                    {{ __('ui.transactions') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($canViewWidget('finance_monthly_revenue'))
                        <!-- Monthly Revenue -->
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-white/20 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <span
                                        class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ __('ui.this_month') }}</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-medium text-blue-100 mb-1">{{ __('ui.monthly_revenue') }}</h3>
                            <div class="text-3xl font-bold mb-2">
                                Rp {{ number_format($financeStats['monthly_revenue'] ?? 0, 0, ',', '.') }}
                            </div>
                            @php $revenueChange = $financeStats['revenue_change'] ?? 0; @endphp
                            <div class="flex items-center gap-2 text-sm">
                                @php
                                    $isUp = $revenueChange > 0;
                                    $isDown = $revenueChange < 0;
                                @endphp

                                <span
                                    class="
                                            inline-flex items-center gap-1.5
                                            px-2.5 py-1 rounded-full font-medium
                                            transition-all duration-300
                                            {{ $isUp ? 'bg-emerald-500/15 text-emerald-400' : '' }}
                                            {{ $isDown ? 'bg-rose-500/15 text-rose-400' : '' }}
                                            {{ !$isUp && !$isDown ? 'bg-slate-500/15 text-slate-300' : '' }}
                                        ">
                                    @if ($isUp)
                                        <!-- Arrow Up -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 15l7-7 7 7" />
                                        </svg>
                                        +{{ $revenueChange }}%
                                    @elseif ($isDown)
                                        <!-- Arrow Down -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                        {{ $revenueChange }}%
                                    @else
                                        <!-- Minus -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 12h14" />
                                        </svg>
                                        0%
                                    @endif
                                </span>

                                <span class="text-xs text-blue-100">
                                    {{ __('ui.from_last_month') }}
                                </span>
                            </div>

                        </div>
                    @endif

                    @if ($canViewWidget('finance_pending_payments'))
                        <!-- Pending Payments -->
                        <div
                            class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-white/20 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full flex items-center">
                                        <span class="w-2 h-2 bg-white rounded-full mr-1 animate-pulse"></span>
                                        {{ __('ui.pending') }}
                                    </span>
                                </div>
                            </div>
                            <h3 class="text-sm font-medium text-amber-100 mb-1">{{ __('ui.pending_payments') }}</h3>
                            <div class="text-3xl font-bold mb-2">
                                Rp {{ number_format($financeStats['pending_payments'] ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="flex items-center text-sm text-amber-100">
                                <span>{{ $financeStats['pending_count'] ?? 0 }}
                                    {{ __('ui.invoice_awaiting_payment') }}</span>
                            </div>
                        </div>
                    @endif

                    @if ($canViewWidget('finance_payment_success_rate'))
                        <!-- Payment Success Rate -->
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-all duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="bg-white/20 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Status</span>
                                </div>
                            </div>
                            <h3 class="text-sm font-medium text-purple-100 mb-1">{{ __('ui.payment_rate') }}</h3>
                            <div class="text-3xl font-bold mb-2">
                                {{ $financeStats['payment_success_rate'] ?? 0 }}%
                            </div>
                            <div class="w-full bg-purple-700 rounded-full h-2 mt-3">
                                <div class="bg-white h-2 rounded-full transition-all"
                                    style="width: {{ $financeStats['payment_success_rate'] ?? 0 }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment Details Section -->
                <div class="flex justify-end mb-3">
                    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-lg p-1 shadow-sm border border-gray-200 dark:border-gray-700">
                        <button data-period="daily" class="shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('ui.daily') }}</button>
                        <button data-period="monthly" class="shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('ui.monthly') }}</button>
                        <button data-period="yearly" class="shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">{{ __('ui.yearly') }}</button>
                        <button data-period="all" class="shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 bg-indigo-600 text-white shadow-sm">{{ __('ui.all') }}</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8 items-start">
                    @if ($canViewWidget('finance_payment_methods'))
                        <!-- Payment Method Breakdown -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 card-header-blue">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                        </svg>
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">
                                            {{ __('ui.payment_methods_title') }}</h3>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400" id="pmPeriodLabel">{{ __('ui.monthly') }}</span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div id="paymentMethodsContent">
                                    <div class="text-center py-8">
                                        <div class="animate-pulse flex flex-col items-center">
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full mb-3"></div>
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full mb-3"></div>
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($canViewWidget('rooms_property_report'))
                        <!-- Revenue Per Property -->
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div
                                class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 card-header-indigo">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">
                                            {{ __('ui.revenue_per_property') }}</h3>
                                    </div>
                                    <select id="propertySelect"
                                        class="text-xs px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white dark:bg-gray-700 dark:text-gray-200">
                                        <option value="">{{ __('ui.all_properties') }}</option>
                                        @foreach ($roomReports ?? [] as $propertyId => $report)
                                            @if (isset($report['property']['name']))
                                                <option value="{{ $propertyId }}">{{ $report['property']['name'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="p-6">
                                <div id="propertyRevenueContent">
                                    <!-- Content will be loaded here -->
                                    <div class="text-center py-8">
                                        <div class="animate-pulse flex flex-col items-center">
                                            <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mb-4"></div>
                                            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
        @endif

        {{-- HIDDEN: Tren Pendapatan section
        @if ($canViewWidget('report_sales_chart'))
            <!-- Revenue Trend Chart - Full Width -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-emerald-50 to-green-50 card-header-emerald">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-emerald-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">Tren Pendapatan (<span
                                    id="revenueTrendPeriodLabel">7</span> Hari)</h3>
                        </div>
                        <div class="flex items-center space-x-3">
                            <select id="revenueTrendPeriod"
                                class="text-xs px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 w-36">
                                <option value="7" selected>7 Hari</option>
                                <option value="30">30 Hari</option>
                            </select>
                            <span class="text-xs text-gray-500">Cash In</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="revenueTrendChart" height="100"></canvas>
                </div>
            </div>
        @endif
        --}}
    </div>
    @endif

    @php
        // Check if user has access to any rooms or check-in/out list widgets
        $hasRoomsWidgets =
            $canViewWidget('rooms_availability') ||
            $canViewWidget('rooms_occupied_details') ||
            $canViewWidget('rooms_occupancy_history') ||
            $canViewWidget('rooms_type_breakdown') ||
            $canViewWidget('checkin_list') ||
            $canViewWidget('checkout_list');
    @endphp

    @if ($hasRoomsWidgets)
        @if ($canViewWidget('rooms_occupied_details'))
            <!-- Occupied Rooms & Analytics Section (Only for Super Admin and HO roles) -->
            @if (count($occupiedRooms) > 0)
                <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-indigo-50 to-purple-50 card-header-indigo">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">
                                    {{ __('ui.occupied_rooms_currently') }}</h2>
                                <span
                                    class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300 text-xs font-medium px-2.5 py-0.5 rounded-full">{{ count($occupiedRooms) }}
                                    {{ __('ui.active') }}</span>
                            </div>
                            @if (count($occupiedRooms) > 4)
                                <div class="flex items-center text-sm text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                                    </svg>
                                    {{ __('ui.scroll_to_see_more') }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="p-6">
                        @if (count($occupiedRooms) > 4)
                            <!-- Horizontal scroll untuk lebih dari 4 card -->
                            <div class="overflow-x-auto pb-4">
                                <div class="flex space-x-4 min-w-min"
                                    style="min-width: {{ count($occupiedRooms) > 4 ? 'min-content' : 'auto' }}">
                                    @foreach ($occupiedRooms as $occupied)
                                        <div class="w-80 flex-shrink-0">
                                            <div
                                                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow h-full {{ $occupied['is_overdue'] ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700' : ($occupied['is_checkout_today'] ? 'border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700' : '') }}">
                                                <!-- Header -->
                                                <div class="flex justify-between items-start mb-3">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100">
                                                            {{ $occupied['guest_name'] }}</h4>
                                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                                            {{ $occupied['room_name'] }}
                                                            •
                                                            {{ $occupied['room_number'] }}</p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $occupied['property_name'] }}</p>
                                                    </div>
                                                    @if ($occupied['is_overdue'])
                                                        <span
                                                            class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-3 w-3 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            {{ __('ui.overdue') }}
                                                        </span>
                                                    @elseif($occupied['is_checkout_today'])
                                                        <span
                                                            class="bg-yellow-500 text-white text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-3 w-3 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            {{ __('ui.checkout_today_label') }}
                                                        </span>
                                                    @else
                                                        <span
                                                            class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-medium px-2 py-1 rounded-full">
                                                            {{ __('ui.active') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                <!-- Stay Details -->
                                                <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                                                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 mr-1 text-green-500" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $occupied['check_in_date'] }}
                                                    </div>
                                                    <div class="flex items-center text-gray-600 dark:text-gray-300">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            class="h-4 w-4 mr-1 text-red-500" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $occupied['check_out_date'] }}
                                                    </div>
                                                </div>

                                                <!-- Progress Bar -->
                                                <div class="mb-3">
                                                    <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                                        <span>{{ __('ui.day_of_total', ['current' => $occupied['days_stayed'], 'total' => $occupied['total_days']]) }}</span>
                                                        <span>{{ __('ui.remaining_days', ['count' => $occupied['days_remaining']]) }}</span>
                                                    </div>
                                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                        <div class="bg-indigo-600 h-2 rounded-full transition-all"
                                                            style="width: {{ $occupied['progress_percentage'] }}%">
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Revenue Info -->
                                                <div
                                                    class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.daily_rate') }}</p>
                                                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rp
                                                            {{ number_format($occupied['daily_rate'], 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.total') }}</p>
                                                        <p class="text-sm font-semibold text-indigo-600">Rp
                                                            {{ number_format($occupied['total_price'], 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Grid layout untuk 4 card atau kurang -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @foreach ($occupiedRooms as $occupied)
                                    <div
                                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition-shadow {{ $occupied['is_overdue'] ? 'border-red-300 bg-red-50 dark:bg-red-900/20 dark:border-red-700' : ($occupied['is_checkout_today'] ? 'border-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-700' : '') }}">
                                        <!-- Header -->
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-semibold text-gray-800 dark:text-gray-100">
                                                    {{ $occupied['guest_name'] }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $occupied['room_name'] }} •
                                                    {{ $occupied['room_number'] }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $occupied['property_name'] }}
                                                </p>
                                            </div>
                                            @if ($occupied['is_overdue'])
                                                <span
                                                    class="bg-red-500 text-white text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    {{ __('ui.overdue') }}
                                                </span>
                                            @elseif($occupied['is_checkout_today'])
                                                <span
                                                    class="bg-yellow-500 text-white text-xs font-medium px-2 py-1 rounded-full flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    {{ __('ui.checkout_today_label') }}
                                                </span>
                                            @else
                                                <span
                                                    class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 text-xs font-medium px-2 py-1 rounded-full">
                                                    {{ __('ui.active') }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Stay Details -->
                                        <div class="grid grid-cols-2 gap-2 mb-3 text-sm">
                                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 mr-1 text-green-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $occupied['check_in_date'] }}
                                            </div>
                                            <div class="flex items-center text-gray-600 dark:text-gray-300">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-4 w-4 mr-1 text-red-500" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ $occupied['check_out_date'] }}
                                            </div>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="mb-3">
                                            <div class="flex justify-between text-xs text-gray-600 dark:text-gray-300 mb-1">
                                                <span>{{ __('ui.day_of_total', ['current' => $occupied['days_stayed'], 'total' => $occupied['total_days']]) }}</span>
                                                <span>{{ __('ui.remaining_days', ['count' => $occupied['days_remaining']]) }}</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all"
                                                    style="width: {{ $occupied['progress_percentage'] }}%"></div>
                                            </div>
                                        </div>

                                        <!-- Revenue Info -->
                                        <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-700">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.daily_rate') }}</p>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Rp
                                                    {{ number_format($occupied['daily_rate'], 0, ',', '.') }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.total') }}</p>
                                                <p class="text-sm font-semibold text-indigo-600">Rp
                                                    {{ number_format($occupied['total_price'], 0, ',', '.') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @endif

        {{-- HIDDEN: Tren Okupansi 30 Hari section
        @if ($canViewWidget('rooms_occupancy_history'))
            <!-- Occupancy History Chart (Only for Super Admin and HO roles) -->
            <div class="mt-8 bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-cyan-50 card-header-blue">
                    <div class="flex items-center space-x-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">Tren Okupansi 30 Hari</h2>
                    </div>
                </div>
                <div class="p-6">
                    <canvas id="occupancyChart" height="80"></canvas>
                </div>
            </div>
        @endif
        --}}

        <!-- Main Content -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-1 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                @if ($canViewWidget('checkin_list'))
                    <!-- Check-in Section -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden transition-all hover:shadow-lg">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-green-50 to-teal-50 card-header-green">
                            <div class="flex items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                                <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">{{ __('ui.todays_check_in') }}</h2>
                            </div>
                            <a href="{{ route('newReserv.index', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
                                class="text-sm font-medium text-green-600 hover:text-green-800 flex items-center">
                                {{ __('ui.view_all') }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            @include('pages.bookings.newreservations.partials.newreserve_table', [
                                'checkIns' => $checkIns,
                                'per_page' => request('per_page', 4),
                                'type' => 'check-in',
                                'showStatus' => false,
                                'showActions' => false,
                            ])
                        </div>
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                            {{ __('ui.showing_checkins', ['shown' => min(4, count($checkIns)), 'total' => count($checkIns)]) }}
                        </div>
                    </div>
                @endif

                @if ($canViewWidget('checkout_list'))
                    <!-- Check-out Section -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden transition-all hover:shadow-lg">
                        <div
                            class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50 card-header-blue-indigo">
                            <div class="flex items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                                </svg>
                                <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">{{ __('ui.checkout_reminder') }}</h2>
                            </div>
                            <a href="{{ route('checkin.index', ['start_date' => now()->format('Y-m-d'), 'end_date' => now()->addDays(3)->format('Y-m-d')]) }}"
                                class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                                {{ __('ui.view_all') }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="overflow-x-auto">
                            @include('pages.bookings.checkin.partials.checkin_table', [
                                'checkOuts' => $checkOuts,
                                'per_page' => request('per_page', 4),
                                'type' => 'check-out',
                                'showStatus' => false,
                                'showActions' => false,
                            ])
                        </div>
                        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700">
                            {{ __('ui.showing_checkouts', ['shown' => min(4, count($checkOuts)), 'total' => count($checkOuts)]) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if ($canViewWidget('rooms_availability'))
            <!-- Multi-Property Reports -->
            <div class="mt-8 grid grid-cols-1 gap-8">
                <!-- Room Availability Report -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-blue-50 to-indigo-50 card-header-blue-indigo">
                        <div class="flex justify-between items-center mb-6">
                            <!-- Left Section -->
                            <div class="flex items-center space-x-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">
                                    {{ __('ui.room_availability_report') }}</h2>
                            </div>

                            <!-- Search Input -->
                            <div class="relative w-full max-w-xs hidden sm:block">
                                <input id="searchKamar" type="text"
                                    placeholder="{{ __('ui.search_property_placeholder') }}"
                                    class="w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 dark:text-gray-200" />
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                            </div>
                        </div>

                    </div>
                    <div class="p-6">
                        @if (is_array($roomReports) && count($roomReports) > 0)
                            <div id="roomAvailabilityContainer">
                                @foreach ($roomReports as $propertyId => $report)
                                    @if (isset($report['property']) && isset($report['room_stats']))
                                        <div class="mb-6 last:mb-0 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 room-availability-item {{ $loop->first ? '' : 'hidden' }}"
                                            data-property-id="{{ $propertyId }}">
                                            <div class="flex justify-between items-start mb-4">
                                                <h3 class="font-semibold text-gray-700 dark:text-gray-200">
                                                    {{ $report['property']['name'] ?? 'N/A' }}
                                                </h3>
                                                <a href="{{ route('room-availability.index', ['search' => $report['property']['name'] ?? '']) }}"
                                                    class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">
                                                    {{ __('ui.view_all') }}
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>

                                            <!-- Room Stats -->
                                            <div class="grid grid-cols-4 gap-3 mb-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                                                        {{ $report['room_stats']['total_rooms'] }}</div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.total_rooms') }}
                                                    </div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-blue-600">
                                                        {{ $report['room_stats']['booked_rooms'] }}</div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.total_booked') }}</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-orange-600">
                                                        {{ $report['room_stats']['occupied_rooms'] }}</div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.total_occupied') }}</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-green-600">
                                                        {{ $report['room_stats']['available_rooms'] }}</div>
                                                    <div class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.available') }}</div>
                                                </div>
                                            </div>

                                            <!-- Occupancy Rate -->
                                            <div class="mb-4">
                                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-1">
                                                    <span>{{ __('ui.occupancy_rate') }}</span>
                                                    <span>{{ $report['room_stats']['occupancy_rate'] }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full"
                                                        style="width: {{ $report['room_stats']['occupancy_rate'] }}%">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Room Breakdown by Type -->
                                            @if (isset($report['room_types_breakdown']) &&
                                                    count($report['room_types_breakdown']) > 0 &&
                                                    $canViewWidget('rooms_type_breakdown'))
                                                <div class="mt-4">
                                                    <h4 class="font-medium text-gray-700 dark:text-gray-200 mb-2">
                                                        Breakdown Kamar
                                                    </h4>
                                                    <div class="space-y-2 max-h-96 overflow-y-auto">
                                                        @foreach ($report['room_types_breakdown'] as $index => $roomType)
                                                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                                                <!-- Room Type Header (Clickable) -->
                                                                <div class="flex justify-between items-center text-sm py-2 px-3 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer transition-colors"
                                                                    onclick="toggleRoomDetails('room-type-{{ $propertyId }}-{{ $index }}')">
                                                                    <div class="flex items-center space-x-3 flex-1">
                                                                        <span class="text-gray-800 dark:text-gray-100 font-semibold">{{ $roomType['name'] }}</span>
                                                                        <span class="text-gray-500 dark:text-gray-400 text-xs">({{ $roomType['total_rooms'] }} kamar)</span>
                                                                    </div>
                                                                    <div class="flex items-center space-x-2">
                                                                        <!-- Status Summary -->
                                                                        @if ($roomType['status_counts']['available'] > 0)
                                                                            <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs px-2 py-0.5 rounded font-medium">
                                                                                {{ $roomType['status_counts']['available'] }} Tersedia
                                                                            </span>
                                                                        @endif
                                                                        @if ($roomType['status_counts']['booked'] > 0)
                                                                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs px-2 py-0.5 rounded font-medium">
                                                                                {{ $roomType['status_counts']['booked'] }} Booking
                                                                            </span>
                                                                        @endif
                                                                        @if ($roomType['status_counts']['occupied'] > 0)
                                                                            <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 text-xs px-2 py-0.5 rounded font-medium">
                                                                                {{ $roomType['status_counts']['occupied'] }} Terisi
                                                                            </span>
                                                                        @endif
                                                                        @if ($roomType['status_counts']['unavailable'] > 0)
                                                                            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs px-2 py-0.5 rounded font-medium">
                                                                                {{ $roomType['status_counts']['unavailable'] }} Tidak Tersedia
                                                                            </span>
                                                                        @endif
                                                                        <!-- Toggle Icon -->
                                                                        <svg id="icon-room-type-{{ $propertyId }}-{{ $index }}"
                                                                            class="h-4 w-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                                        </svg>
                                                                    </div>
                                                                </div>

                                                                <!-- Room Details (Expandable) -->
                                                                <div id="room-type-{{ $propertyId }}-{{ $index }}" class="hidden bg-white dark:bg-gray-800">
                                                                    <div class="px-3 py-2 space-y-1">
                                                                        @foreach ($roomType['rooms'] as $room)
                                                                            <div class="flex justify-between items-center text-xs py-1 px-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                                                                                <span class="text-gray-600 dark:text-gray-300">
                                                                                    <span class="font-medium">No.</span> {{ $room['room_number'] }}
                                                                                </span>
                                                                                @if ($room['status'] === 'available')
                                                                                    <span class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-0.5 rounded text-xs">
                                                                                        Tersedia
                                                                                    </span>
                                                                                @elseif ($room['status'] === 'booked')
                                                                                    <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-0.5 rounded text-xs">
                                                                                        Terbooking
                                                                                    </span>
                                                                                @elseif ($room['status'] === 'occupied')
                                                                                    <span class="bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 px-2 py-0.5 rounded text-xs">
                                                                                        Terisi
                                                                                    </span>
                                                                                @else
                                                                                    <span class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-2 py-0.5 rounded text-xs">
                                                                                        Tidak Tersedia
                                                                                    </span>
                                                                                @endif
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            @if (count($roomReports) > 1)
                                <div class="mt-4 text-center">
                                    <button id="toggleRoomAvailability"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center justify-center mx-auto bg-blue-50 dark:bg-blue-900/30 px-4 py-2 rounded-lg transition-all hover:bg-blue-100 dark:hover:bg-blue-900/50">
                                        <span id="toggleRoomAvailabilityText">{{ __('ui.show_more') }}</span>
                                        <svg id="toggleRoomAvailabilityIcon" xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 ml-1 transition-transform" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="mt-2">{{ __('ui.no_room_report_data') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- HIDDEN: Detail Durasi Sewa & Penjualan section
                @if ($canViewWidget('report_rental_duration'))
                    <!-- Detailed Duration & Sales Report -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-r from-green-50 to-teal-50 card-header-green">
                            <div class="flex justify-between items-center mb-6">
                                <!-- Left Section -->
                                <div class="flex items-center space-x-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 text-lg">Detail Durasi Sewa & Penjualan</h2>
                                </div>

                                <!-- Search Input -->
                                <div class="relative w-full max-w-xs hidden sm:block">
                                    <input id="searchDurasi" type="text" placeholder="Cari Properti..."
                                        class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            @if (is_array($roomReports) && count($roomReports) > 0)
                                <div id="durationSalesContainer">
                                    @foreach ($roomReports as $propertyId => $report)
                                        @if (isset($report['property']) && isset($report['booking_durations']) && isset($report['monthly_sales']))
                                        <div class="mb-6 last:mb-0 duration-sales-item {{ $loop->first ? '' : 'hidden' }}"
                                            data-property-id="{{ $propertyId }}">
                                            <h3 class="font-semibold text-gray-700 mb-3 property-name-sales">
                                                {{ $report['property']['name'] ?? 'N/A' }}
                                            </h3>

                                            <!-- Durasi Sewa -->
                                            <div class="mb-4">
                                                <h4 class="font-medium text-gray-700 mb-2">Statistik Durasi Sewa</h4>
                                                <div class="grid grid-cols-3 gap-4 text-sm">
                                                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                                                        <div class="font-bold text-blue-600">
                                                            {{ $report['booking_durations']['average_duration'] }}
                                                            hari
                                                        </div>
                                                        <div class="text-blue-500 text-xs">Rata-rata</div>
                                                    </div>
                                                    <div class="text-center p-2 bg-green-50 rounded-lg">
                                                        <div class="font-bold text-green-600">
                                                            {{ $report['booking_durations']['min_duration'] }} hari
                                                        </div>
                                                        <div class="text-green-500 text-xs">Terpendek</div>
                                                    </div>
                                                    <div class="text-center p-2 bg-purple-50 rounded-lg">
                                                        <div class="font-bold text-purple-600">
                                                            {{ $report['booking_durations']['max_duration'] }} hari
                                                        </div>
                                                        <div class="text-purple-500 text-xs">Terlama</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Breakdown Durasi -->
                                            @if (isset($report['booking_durations']['duration_ranges']) && count($report['booking_durations']['duration_ranges']) > 0)
                                                <div class="mb-4">
                                                    <h4 class="font-medium text-gray-700 mb-2">Distribusi Durasi</h4>
                                                    <div class="space-y-2">
                                                        @foreach ($report['booking_durations']['duration_ranges'] as $duration)
                                                            <div
                                                                class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                                <span
                                                                    class="text-sm font-medium text-gray-700">{{ $duration->duration_range }}</span>
                                                                <div class="flex items-center space-x-2">
                                                                    <span
                                                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">
                                                                        {{ $duration->count }} booking
                                                                    </span>
                                                                    <span class="text-xs text-gray-500">
                                                                        {{ $report['booking_durations']['total_bookings'] > 0 ? round(($duration->count / $report['booking_durations']['total_bookings']) * 100, 1) : 0 }}%
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Monthly Sales -->
                                            <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                                <h4 class="font-medium text-yellow-800 mb-2">Penjualan Bulan Ini</h4>
                                                <div class="grid grid-cols-2 gap-4 text-sm">
                                                    <div class="text-center">
                                                        <div class="text-xl font-bold text-yellow-700">
                                                            {{ $report['monthly_sales']['total_bookings'] }}</div>
                                                        <div class="text-yellow-600 text-xs">Total Booking</div>
                                                    </div>
                                                    <div class="text-center">
                                                        <div class="text-xl font-bold text-yellow-700">Rp
                                                            {{ number_format($report['monthly_sales']['total_revenue'], 0, ',', '.') }}
                                                        </div>
                                                        <div class="text-yellow-600 text-xs">Total Pendapatan</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if (count($roomReports) > 1)
                                    <div class="mt-4 text-center">
                                        <button id="toggleDurationSales"
                                            class="text-sm font-medium text-green-600 hover:text-green-800 flex items-center justify-center mx-auto bg-green-50 px-4 py-2 rounded-lg transition-all hover:bg-green-100">
                                            <span id="toggleDurationSalesText">Lihat Selengkapnya</span>
                                            <svg id="toggleDurationSalesIcon" xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4 ml-1 transition-transform" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-8 text-gray-500">
                                    <p>Tidak ada data laporan</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                --}}
            </div>
        @endif
    @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Occupancy History Chart
        document.addEventListener('DOMContentLoaded', function() {
            @if ($canViewWidget('rooms_occupancy_history'))
                const ctx = document.getElementById('occupancyChart');
                if (ctx) {
                    const occupancyData = @json($occupancyHistory);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: occupancyData.map(d => d.date),
                            datasets: [{
                                    label: '{{ __('ui.chart_rooms_filled') }}',
                                    data: occupancyData.map(d => d.occupied),
                                    borderColor: 'rgb(59, 130, 246)',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: '{{ __('ui.chart_occupancy_rate') }}',
                                    data: occupancyData.map(d => d.occupancy_rate),
                                    borderColor: 'rgb(16, 185, 129)',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.datasetIndex === 1) {
                                                label += context.parsed.y + '%';
                                            } else {
                                                label += context.parsed.y +
                                                    ' {{ __('ui.chart_rooms_unit') }}';
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    title: {
                                        display: true,
                                        text: '{{ __('ui.chart_room_label') }}'
                                    }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    title: {
                                        display: true,
                                        text: '{{ __('ui.chart_occupancy_rate') }}'
                                    },
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    max: 100
                                }
                            }
                        }
                    });
                }

                // Search functionality for dashboard reports
                function setupSearch(searchInputId, containerSelector, noResultsMsgId) {
                    const searchInput = document.getElementById(searchInputId);

                    if (searchInput) {
                        searchInput.addEventListener('input', function(e) {
                            const searchTerm = e.target.value.toLowerCase().trim();
                            const reportContainers = document.querySelectorAll(containerSelector);

                            reportContainers.forEach(function(container) {
                                const propertyName = container.querySelector('h3');

                                if (propertyName) {
                                    const propertyText = propertyName.textContent.toLowerCase();

                                    if (searchTerm === '' || propertyText.includes(searchTerm)) {
                                        container.style.display = 'block';
                                        container.style.opacity = '0';
                                        setTimeout(() => {
                                            container.style.transition =
                                                'opacity 0.3s ease-in';
                                            container.style.opacity = '1';
                                        }, 10);
                                    } else {
                                        container.style.display = 'none';
                                    }
                                }
                            });

                            const visibleContainers = Array.from(reportContainers).filter(c => c.style
                                .display !== 'none');
                            const parentContainer = reportContainers[0]?.parentElement;

                            if (visibleContainers.length === 0 && parentContainer) {
                                let noResultsMsg = document.getElementById(noResultsMsgId);

                                if (!noResultsMsg) {
                                    noResultsMsg = document.createElement('div');
                                    noResultsMsg.id = noResultsMsgId;
                                    noResultsMsg.className = 'text-center py-8 text-gray-500 dark:text-gray-400';
                                    noResultsMsg.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2">{{ __('ui.property_not_found_keyword') }} "<span class="search-term">${searchTerm}</span>"</p>
                                `;
                                    parentContainer.appendChild(noResultsMsg);
                                } else {
                                    noResultsMsg.querySelector('.search-term').textContent = searchTerm;
                                    noResultsMsg.style.display = 'block';
                                }
                            } else {
                                const noResultsMsg = document.getElementById(noResultsMsgId);
                                if (noResultsMsg) {
                                    noResultsMsg.style.display = 'none';
                                }
                            }
                        });
                    }
                }

                setupSearch(
                    'searchKamar',
                    '.room-availability-item',
                    'no-results-message-kamar'
                );

                setupSearch(
                    'searchDurasi',
                    '.duration-sales-item',
                    'no-results-message-durasi'
                );

                // Toggle Room Availability Report
                const toggleRoomBtn = document.getElementById('toggleRoomAvailability');
                if (toggleRoomBtn) {
                    toggleRoomBtn.addEventListener('click', function() {
                        const items = document.querySelectorAll('.room-availability-item');
                        const icon = document.getElementById('toggleRoomAvailabilityIcon');
                        const text = document.getElementById('toggleRoomAvailabilityText');
                        let isExpanded = false;

                        items.forEach((item, index) => {
                            if (index > 0) {
                                if (item.classList.contains('hidden')) {
                                    item.classList.remove('hidden');
                                    isExpanded = true;
                                } else {
                                    item.classList.add('hidden');
                                    isExpanded = false;
                                }
                            }
                        });

                        if (isExpanded) {
                            text.textContent = '{{ __('ui.show_less') }}';
                            icon.style.transform = 'rotate(180deg)';
                        } else {
                            text.textContent = '{{ __('ui.show_more') }}';
                            icon.style.transform = 'rotate(0deg)';
                        }
                    });
                }

                // Toggle Room Type Details - Global function for room breakdown expand/collapse
                window.toggleRoomDetails = function(elementId) {
                    const detailsElement = document.getElementById(elementId);
                    const iconElement = document.getElementById('icon-' + elementId);

                    if (detailsElement && iconElement) {
                        if (detailsElement.classList.contains('hidden')) {
                            detailsElement.classList.remove('hidden');
                            iconElement.style.transform = 'rotate(180deg)';
                        } else {
                            detailsElement.classList.add('hidden');
                            iconElement.style.transform = 'rotate(0deg)';
                        }
                    }
                }

                // Toggle Duration & Sales Report
                const toggleDurationBtn = document.getElementById('toggleDurationSales');
                if (toggleDurationBtn) {
                    toggleDurationBtn.addEventListener('click', function() {
                        const items = document.querySelectorAll('.duration-sales-item');
                        const icon = document.getElementById('toggleDurationSalesIcon');
                        const text = document.getElementById('toggleDurationSalesText');
                        let isExpanded = false;

                        items.forEach((item, index) => {
                            if (index > 0) {
                                if (item.classList.contains('hidden')) {
                                    item.classList.remove('hidden');
                                    isExpanded = true;
                                } else {
                                    item.classList.add('hidden');
                                    isExpanded = false;
                                }
                            }
                        });

                        if (isExpanded) {
                            text.textContent = '{{ __('ui.show_less') }}';
                            icon.style.transform = 'rotate(180deg)';
                        } else {
                            text.textContent = '{{ __('ui.show_more') }}';
                            icon.style.transform = 'rotate(0deg)';
                        }
                    });
                }
            @endif

            // Financial Charts
            @if ($canViewWidget('report_sales_chart'))
                // Revenue Trend Chart - with dynamic period selection
                let revenueTrendChart = null;
                const revenueTrendCtx = document.getElementById('revenueTrendChart');

                function loadRevenueTrendChart(days) {
                    if (!revenueTrendCtx) return;

                    // Show loading state
                    const chartContainer = revenueTrendCtx.parentElement;
                    const originalHTML = chartContainer.innerHTML;

                    // Determine URL based on user property
                    const userPropertyId = '{{ Auth::user()->property_id ?? '' }}';
                    const url = userPropertyId ?
                        `/dashboard/revenue-trend/${userPropertyId}?days=${days}` :
                        `/dashboard/revenue-trend?days=${days}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                const cashFlowData = result.data;

                                // Destroy existing chart if exists
                                if (revenueTrendChart) {
                                    revenueTrendChart.destroy();
                                }

                                // Update period label
                                const periodLabel = document.getElementById('revenueTrendPeriodLabel');
                                if (periodLabel) {
                                    periodLabel.textContent = days;
                                }

                                // Create new chart
                                revenueTrendChart = new Chart(revenueTrendCtx, {
                                    type: 'line',
                                    data: {
                                        labels: cashFlowData.map(d => d.date),
                                        datasets: [{
                                            label: '{{ __('ui.chart_income_cash_in') }}',
                                            data: cashFlowData.map(d => d.cash_in),
                                            borderColor: 'rgb(16, 185, 129)',
                                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                            tension: 0.4,
                                            fill: true,
                                            pointRadius: 5,
                                            pointHoverRadius: 7,
                                            pointBackgroundColor: 'rgb(16, 185, 129)',
                                            pointBorderColor: '#fff',
                                            pointBorderWidth: 2,
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        interaction: {
                                            mode: 'index',
                                            intersect: false,
                                        },
                                        plugins: {
                                            legend: {
                                                display: true,
                                                position: 'top',
                                                labels: {
                                                    font: {
                                                        size: 12,
                                                        family: "'Inter', sans-serif"
                                                    },
                                                    padding: 15
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        let label = context.dataset.label || '';
                                                        if (label) {
                                                            label += ': ';
                                                        }
                                                        label += 'Rp ' + context.parsed.y
                                                            .toLocaleString('id-ID');
                                                        return label;
                                                    }
                                                },
                                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                                padding: 12,
                                                titleFont: {
                                                    size: 13
                                                },
                                                bodyFont: {
                                                    size: 12
                                                }
                                            }
                                        },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: function(value) {
                                                        return 'Rp ' + (value / 1000000).toFixed(
                                                                1) +
                                                            '{{ __('ui.chart_currency_millions') }}';
                                                    },
                                                    font: {
                                                        size: 11
                                                    }
                                                },
                                                grid: {
                                                    color: 'rgba(0, 0, 0, 0.05)'
                                                }
                                            },
                                            x: {
                                                grid: {
                                                    display: false
                                                },
                                                ticks: {
                                                    font: {
                                                        size: 11
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading revenue trend:', error);
                        });
                }

                // Initialize with default period (7 days)
                if (revenueTrendCtx) {
                    loadRevenueTrendChart(7);
                }

                // Period selector event listener
                const periodSelector = document.getElementById('revenueTrendPeriod');
                if (periodSelector) {
                    periodSelector.addEventListener('change', function() {
                        loadRevenueTrendChart(this.value);
                    });
                }

                // Payment Methods Chart (Doughnut)
                const paymentMethodsCtx = document.getElementById('paymentMethodsChart');
                if (paymentMethodsCtx) {
                    const paymentMethods = @json($financeStats['payment_methods'] ?? []);

                    const chartColors = [
                        'rgb(16, 185, 129)', // Green - Tunai
                        'rgb(59, 130, 246)', // Blue - Transfer
                        'rgb(168, 85, 247)', // Purple - Kartu Kredit
                        'rgb(249, 115, 22)', // Orange - E-Wallet
                        'rgb(99, 102, 241)', // Indigo - Kartu Debit
                        'rgb(236, 72, 153)', // Pink
                        'rgb(234, 179, 8)', // Yellow
                    ];

                    new Chart(paymentMethodsCtx, {
                        type: 'doughnut',
                        data: {
                            labels: paymentMethods.map(m => m.method),
                            datasets: [{
                                data: paymentMethods.map(m => m.amount),
                                backgroundColor: chartColors.slice(0, paymentMethods.length),
                                borderWidth: 2,
                                borderColor: '#fff',
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 12,
                                            family: "'Inter', sans-serif"
                                        },
                                        padding: 15,
                                        generateLabels: function(chart) {
                                            const data = chart.data;
                                            if (data.labels.length && data.datasets.length) {
                                                return data.labels.map((label, i) => {
                                                    const dataset = data.datasets[0];
                                                    const value = dataset.data[i];
                                                    const total = dataset.data.reduce((a, b) =>
                                                        a + b, 0);
                                                    const percentage = ((value / total) * 100)
                                                        .toFixed(1);

                                                    return {
                                                        text: `${label} (${percentage}%)`,
                                                        fillStyle: dataset.backgroundColor[i],
                                                        hidden: false,
                                                        index: i
                                                    };
                                                });
                                            }
                                            return [];
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.parsed || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b,
                                                0);
                                            const percentage = ((value / total) * 100).toFixed(1);

                                            return [
                                                label,
                                                '{{ __('ui.amount') }}: Rp ' + value
                                                .toLocaleString('id-ID'),
                                                '{{ __('ui.percentage') }}: ' + percentage + '%'
                                            ];
                                        }
                                    },
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    titleFont: {
                                        size: 13
                                    },
                                    bodyFont: {
                                        size: 12
                                    }
                                }
                            }
                        }
                    });
                }

                // ── Shared period filter ──────────────────────────────────────
                let sharedPeriod = 'all';
                const periodLabels = { daily: '{{ __('ui.daily') }}', monthly: '{{ __('ui.monthly') }}', yearly: '{{ __('ui.yearly') }}', all: '{{ __('ui.all') }}' };

                document.querySelectorAll('.shared-period-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        sharedPeriod = this.dataset.period;
                        document.querySelectorAll('.shared-period-btn').forEach(b => {
                            b.className = 'shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200';
                        });
                        this.className = 'shared-period-btn px-4 py-1.5 text-xs font-medium rounded-md transition-all duration-200 bg-indigo-600 text-white shadow-sm';
                        const pmLabel = document.getElementById('pmPeriodLabel');
                        if (pmLabel) pmLabel.textContent = periodLabels[sharedPeriod];
                        loadPaymentMethods(sharedPeriod);
                        const sel = document.getElementById('propertySelect');
                        loadPropertyRevenue(sel ? sel.value : '');
                    });
                });

                // ── Payment Methods ──────────────────────────────────────────
                function loadPaymentMethods(period) {
                    const contentDiv = document.getElementById('paymentMethodsContent');
                    if (!contentDiv) return;

                    contentDiv.innerHTML = `
                        <div class="animate-pulse space-y-3">
                            <div class="h-12 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                            <div class="h-12 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                            <div class="h-12 bg-gray-100 dark:bg-gray-700 rounded-lg"></div>
                        </div>`;

                    fetch(`/dashboard/payment-methods?period=${period}`)
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) renderPaymentMethods(data.data);
                            else contentDiv.innerHTML = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>{{ __('ui.failed_to_load_data') }}</p></div>`;
                        })
                        .catch(() => {
                            contentDiv.innerHTML = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>{{ __('ui.error_loading_data') }}</p></div>`;
                        });
                }

                function renderPaymentMethods(data) {
                    const contentDiv = document.getElementById('paymentMethodsContent');
                    if (!contentDiv) return;

                    const methodConfig = {
                        'Tunai':        { color: 'green',  icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z' },
                        'Transfer Bank':{ color: 'blue',   icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
                        'Kartu Kredit': { color: 'purple', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                        'E-Wallet':     { color: 'orange', icon: 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z' },
                        'Kartu Debit':  { color: 'indigo', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                    };
                    const defaultIcon = 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

                    const breakdown = data.breakdown ?? [];
                    const total     = data.total ?? 0;

                    if (breakdown.length === 0) {
                        contentDiv.innerHTML = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>{{ __('ui.no_payment_data_this_month') }}</p></div>`;
                        return;
                    }

                    let html = '<div class="space-y-4">';
                    breakdown.forEach(method => {
                        const cfg   = methodConfig[method.method] ?? { color: 'gray', icon: defaultIcon };
                        const c     = cfg.color;
                        html += `
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                <div class="flex items-center space-x-3">
                                    <div class="bg-${c}-100 dark:bg-${c}-900/30 p-2 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-${c}-600 dark:text-${c}-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${cfg.icon}" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 dark:text-gray-100">${method.method}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">${method.count} {{ __('ui.transactions') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800 dark:text-gray-100">Rp ${new Intl.NumberFormat('id-ID').format(method.amount)}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">${method.percentage}%</p>
                                </div>
                            </div>`;
                    });
                    html += '</div>';
                    html += `
                        <div class="mt-6 pt-4 border-t-2 border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-bold text-gray-800 dark:text-gray-100">{{ __('ui.total_income') }}</p>
                                <p class="text-2xl font-bold text-blue-600">Rp ${new Intl.NumberFormat('id-ID').format(total)}</p>
                            </div>
                        </div>`;
                    contentDiv.innerHTML = html;
                }

                // Load initial
                loadPaymentMethods(sharedPeriod);

                // ── Revenue Per Property ──────────────────────────────────────
                // Property Revenue Selector
                const propertySelect = document.getElementById('propertySelect');
                if (propertySelect) {
                    loadPropertyRevenue(propertySelect.value);
                    propertySelect.addEventListener('change', function() {
                        loadPropertyRevenue(this.value);
                    });
                } else {
                    const userPropertyId = '{{ Auth::user()->property_id ?? '' }}';
                    if (userPropertyId) loadPropertyRevenue(userPropertyId);
                }

                function loadPropertyRevenue(propertyId) {
                    const contentDiv = document.getElementById('propertyRevenueContent');
                    if (!contentDiv) return;

                    contentDiv.innerHTML = `
                        <div class="text-center py-8">
                            <div class="animate-pulse flex flex-col items-center">
                                <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-1/2 mb-4"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-1/3"></div>
                            </div>
                        </div>`;

                    const base = propertyId
                        ? `/dashboard/property-revenue/${propertyId}`
                        : '/dashboard/property-revenue';
                    const url = `${base}?period=${sharedPeriod}`;

                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                renderPropertyRevenue(data.data);
                            } else {
                                contentDiv.innerHTML = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>{{ __('ui.failed_to_load_data') }}</p></div>`;
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            contentDiv.innerHTML = `<div class="text-center py-8 text-gray-500 dark:text-gray-400"><p>{{ __('ui.error_loading_data') }}</p></div>`;
                        });
                }

                function renderPropertyRevenue(data) {
                    const contentDiv = document.getElementById('propertyRevenueContent');
                    if (!contentDiv) return;

                    let html = '';

                    if (Array.isArray(data) && data.length > 0) {
                        // Multiple properties - show first one and collapse the rest
                        html += renderPropertyCard(data[0]);

                        if (data.length > 1) {
                            // Add "Lihat Selengkapnya" dropdown button
                            html += `
                                        <div class="mt-6">
                                            <div class="relative flex items-center gap-3 mb-1">
                                                <div class="flex-1 h-px bg-gradient-to-r from-transparent via-indigo-200 dark:via-indigo-700 to-indigo-200 dark:to-indigo-700"></div>
                                                <button
                                                    onclick="togglePropertyDropdown()"
                                                    id="propertyDropdownBtn"
                                                    class="group flex items-center gap-2 px-5 py-2 rounded-full text-sm font-semibold text-indigo-600 dark:text-indigo-400 bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-700 shadow-sm hover:shadow-md hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all duration-200 whitespace-nowrap">
                                                    <span id="dropdownBtnText">${'{{ __('ui.show_more_properties') }}'.replace(':count', data.length - 1)}</span>
                                                    <svg id="dropdownIcon" class="w-4 h-4 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                                <div class="flex-1 h-px bg-gradient-to-l from-transparent via-indigo-200 dark:via-indigo-700 to-indigo-200 dark:to-indigo-700"></div>
                                            </div>
                                            <div id="propertyDropdownContent" style="max-height: 0; opacity: 0; overflow-y: auto; transition: max-height 0.35s ease, opacity 0.3s ease;">
                                                <div class="pt-4">
                                    `;

                            // Add remaining properties to dropdown
                            for (let i = 1; i < data.length; i++) {
                                html += renderPropertyCard(data[i]);
                            }

                            html += `
                                                </div>
                                            </div>
                                        </div>
                                    `;
                        }
                    } else if (typeof data === 'object') {
                        // Single property
                        html = renderPropertyCard(data);
                    } else {
                        html = `
                                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                        <p>{{ __('ui.no_revenue_data') }}</p>
                                    </div>
                                `;
                    }

                    contentDiv.innerHTML = html;
                }

                window.togglePropertyDropdown = function() {
                    const dropdown = document.getElementById('propertyDropdownContent');
                    const icon = document.getElementById('dropdownIcon');
                    const isCollapsed = !dropdown.style.maxHeight || dropdown.style.maxHeight === '0px';

                    if (isCollapsed) {
                        dropdown.style.maxHeight = '420px';
                        dropdown.style.opacity = '1';
                        icon.style.transform = 'rotate(180deg)';
                    } else {
                        dropdown.style.maxHeight = '0';
                        dropdown.style.opacity = '0';
                        icon.style.transform = 'rotate(0deg)';
                    }
                }

                function renderPropertyCard(property) {
                    const propertyName  = property.property_name || property.name || 'N/A';
                    const revenue       = property.revenue || 0;
                    const totalBookings = property.total_bookings || 0;

                    const periodLabel = periodLabels[sharedPeriod] || 'Bulanan';

                    return `
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <h4 class="font-semibold text-gray-800 dark:text-gray-100 mb-4">${propertyName}</h4>
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div class="text-center p-3 bg-indigo-50 rounded-lg border border-indigo-200 card-indigo">
                                            <p class="label text-xs text-indigo-600 font-medium mb-1">{{ __('ui.revenue') }} ${periodLabel}</p>
                                            <p class="value text-lg font-bold text-indigo-700">Rp ${formatNumber(revenue)}</p>
                                        </div>
                                        <div class="text-center p-3 bg-purple-50 rounded-lg border border-purple-200 card-purple">
                                            <p class="label text-xs text-purple-600 font-medium mb-1">{{ __('ui.total_bookings') }}</p>
                                            <p class="value text-lg font-bold text-purple-700">${totalBookings}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                }

                function formatNumber(num) {
                    return new Intl.NumberFormat('id-ID').format(num);
                }
            @endif
        });
    </script>
</x-app-layout>
