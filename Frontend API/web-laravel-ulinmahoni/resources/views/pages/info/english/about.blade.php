<!DOCTYPE html>
<!-- About page (EN) — supports light/dark theme -->
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Ulin Mahoni - Trusted Property Solutions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @include('components.homepage.styles')
    <!-- Dark mode: apply 'dark' class before render to prevent flash of light mode -->
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
        /* Dark mode overrides for content containers */
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
        html.dark .content-card span {
            color: #d1d5db !important;
        }
        html.dark .content-card .bg-teal-50 {
            background-color: rgba(13, 148, 136, 0.15) !important;
        }
        html.dark .content-card .bg-teal-100 {
            background-color: rgba(13, 148, 136, 0.2) !important;
        }
        html.dark .content-card .bg-gray-200 {
            background-color: #374151 !important;
        }
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative pt-32 pb-12 md:pt-40 md:pb-20 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="text-center pb-12 md:pb-16 content-card">
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tighter tracking-tighter mb-4 text-gray-900">
                        About Ulin Mahoni
                    </h1>
                    <div class="max-w-3xl mx-auto">
                        <p class="text-xl text-gray-600 mb-8">
                            Building a better future for Indonesian property
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="relative pb-20 px-4">
            <div class="max-w-6xl mx-auto">
                <!-- Vision & Mission -->
                <div class="grid md:grid-cols-2 gap-12 mb-16">
                    <div class="content-card">
                        <h2 class="text-3xl font-bold mb-6 text-gray-900">Our Vision</h2>
                        <p class="text-gray-600 mb-8">
                            To become the leading property platform that connects and provides the best solutions for all property needs in Indonesia.
                        </p>
                        <div class="bg-teal-50 p-6 rounded-lg">
                            <h3 class="font-bold mb-4 text-gray-900">Our Values</h3>
                            <ul class="space-y-3">
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Integrity</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Innovation</span>
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span class="text-gray-700">Customer Satisfaction</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="content-card">
                        <h2 class="text-3xl font-bold mb-6 text-gray-900">Our Mission</h2>
                        <ul class="space-y-6">
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">1</span>
                                <div class="ml-4">
                                    <p class="text-gray-600">Provide innovative and trusted property solutions</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">2</span>
                                <div class="ml-4">
                                    <p class="text-gray-600">Build a transparent and efficient property ecosystem</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">3</span>
                                <div class="ml-4">
                                    <p class="text-gray-600">Deliver the best experience for all users</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Team Section -->
                <div class="text-center mb-16">
                    <div class="content-card mb-8">
                        <h2 class="text-3xl font-bold mb-2 text-gray-900">Our Team</h2>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="content-card">
                            <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                            <h3 class="font-bold text-xl mb-1 text-gray-900">John Doe</h3>
                            <p class="text-gray-600 mb-4">CEO & Founder</p>
                            <p class="text-gray-600 text-sm">10+ years of property industry experience</p>
                        </div>
                        <div class="content-card">
                            <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                            <h3 class="font-bold text-xl mb-1 text-gray-900">Jane Smith</h3>
                            <p class="text-gray-600 mb-4">Head of Operations</p>
                            <p class="text-gray-600 text-sm">8+ years of operational management experience</p>
                        </div>
                        <div class="content-card">
                            <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                            <h3 class="font-bold text-xl mb-1 text-gray-900">Mike Johnson</h3>
                            <p class="text-gray-600 mb-4">Head of Technology</p>
                            <p class="text-gray-600 text-sm">12+ years of property technology experience</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="text-center content-card">
                    <h2 class="text-3xl font-bold mb-4 text-gray-900">Contact Us</h2>
                    <p class="text-gray-600 mb-8">Have questions? Our team is ready to help</p>
                    <div class="grid md:grid-cols-3 gap-6 max-w-3xl mx-auto">
                        <div class="p-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2 text-gray-900">Email</h3>
                            <p class="text-gray-600">info@ulinmahoni.com</p>
                        </div>
                        <div class="p-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2 text-gray-900">Phone</h3>
                            <p class="text-gray-600">+62 123 4567 890</p>
                        </div>
                        <div class="p-4">
                            <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2 text-gray-900">Address</h3>
                            <p class="text-gray-600">Jakarta, Indonesia</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html>
