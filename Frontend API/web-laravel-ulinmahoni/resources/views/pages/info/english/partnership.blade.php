<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partnership - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
        /* Glass content card — translucent panel */
        .content-card {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.20);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        /* Dark mode overrides for glass content cards */
        html.dark body {
            background-color: #111827 !important;
        }
        html.dark .content-card {
            background: rgba(255, 255, 255, 0.06) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-color: rgba(255, 255, 255, 0.10) !important;
            color: #e5e7eb;
        }
        html.dark .content-card h1,
        html.dark .content-card h2,
        html.dark .content-card h3 {
            color: #f3f4f6 !important;
        }
        html.dark .content-card p,
        html.dark .content-card span,
        html.dark .content-card li {
            color: #d1d5db !important;
        }
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative pt-32 pb-12 md:pt-40 md:pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center pb-12 md:pb-16">
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tighter tracking-tighter mb-4">
                        Ulin Mahoni Partnership
                    </h1>
                    <div class="max-w-3xl mx-auto">
                        <p class="text-xl text-gray-600 mb-8">
                            Join us and grow your property business together
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="relative pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <!-- Partnership Types -->
                <div class="grid md:grid-cols-3 gap-8 mb-16">
                    <!-- Property Owner — glass card styling -->
                    <div class="content-card">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Property Owner</h3>
                        <p class="text-gray-600 mb-4">List your property and gain access to a wider market</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Professional property management
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Optimal marketing
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Guaranteed payments
                            </li>
                        </ul>
                    </div>

                    <!-- Agent — glass card styling -->
                    <div class="content-card">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Property Agent</h3>
                        <p class="text-gray-600 mb-4">Join as an agent and increase your income</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Competitive commission
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Professional training
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Access to exclusive properties
                            </li>
                        </ul>
                    </div>

                    <!-- Investor — glass card styling -->
                    <div class="content-card">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Investor</h3>
                        <p class="text-gray-600 mb-4">Invest your funds in potential properties</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                High investment returns
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Verified portfolio
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Professional risk management
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold mb-4">Ready to Join?</h2>
                    <p class="text-gray-600 mb-8">Contact our team for more information</p>
                    <a href="#" class="btn text-white bg-teal-600 hover:bg-teal-700 rounded-lg px-8 py-4 text-lg font-semibold">
                        Start Partnership
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 