<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni for Business - Property Solutions for Business</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
        .login-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        /* Video background fixed below header (header ~72px tall) */
        .video-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        .video-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 100%);
            z-index: 2;
        }
        /* Push page content below the fixed header */
        main.flex-grow {
            padding-top: 72px;
        }
        /* Glass login panel — highly transparent with strong blur */
        .login-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(48px);
            -webkit-backdrop-filter: blur(48px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2.5rem;
            max-width: 28rem;
            width: 100%;
            margin: 2rem;
            position: relative;
            z-index: 10;
        }
        @media (max-width: 640px) {
            .login-box {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
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
        /* Dark mode overrides for glass content cards — remove white outline */
        html.dark .content-card {
            background: rgba(255, 255, 255, 0.06) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: none !important;
            color: #e5e7eb;
        }
        html.dark .content-card h1,
        html.dark .content-card h2,
        html.dark .content-card h3,
        html.dark .content-card h4 {
            color: #f3f4f6 !important;
        }
        html.dark .content-card p,
        html.dark .content-card span,
        html.dark .content-card li {
            color: #d1d5db !important;
        }
    </style>
</head>
<body class="font-inter antialiased text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')

    <main class="flex-grow relative">
        <!-- Image Background — fixed behind content -->
        <div class="video-wrapper">
            <img src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" alt="Background" class="video-background">
            <div class="video-overlay"></div>
        </div>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-12 md:pt-40 md:pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center pb-12 md:pb-16">
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tighter tracking-tighter mb-4">
                        Ulin Mahoni for Business
                    </h1>
                    <div class="max-w-3xl mx-auto">
                        <p class="text-xl text-gray-600 mb-8">
                            Complete property solutions for your business needs
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="relative pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <!-- Business Solutions -->
                <div class="grid md:grid-cols-2 gap-12 mb-16">
                    <!-- Corporate Housing — glass card styling -->
                    <div class="content-card">
                        <h3 class="text-2xl font-bold mb-4">Corporate Housing</h3>
                        <p class="text-gray-600 mb-6">Accommodation solutions for your employees and corporate guests</p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">High Flexibility</h4>
                                    <p class="text-gray-600">Customizable rental periods</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Strategic Locations</h4>
                                    <p class="text-gray-600">Close to business centers and essential facilities</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Full Service</h4>
                                    <p class="text-gray-600">Includes maintenance and property management</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Office Space — glass card styling -->
                    <div class="content-card">
                        <h3 class="text-2xl font-bold mb-4">Office Space</h3>
                        <p class="text-gray-600 mb-6">Workspace solutions tailored to your business needs</p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Flexible Space</h4>
                                    <p class="text-gray-600">Adaptable to your team size</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Modern Facilities</h4>
                                    <p class="text-gray-600">High-speed internet and meeting rooms</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Premium Locations</h4>
                                    <p class="text-gray-600">In strategic business areas</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Benefits Section -->
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold mb-8">Benefits for Your Business</h2>
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="p-4">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Cost Efficiency</h3>
                            <p class="text-gray-600 text-sm">Optimize property expenses</p>
                        </div>
                        <div class="p-4">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Fast Process</h3>
                            <p class="text-gray-600 text-sm">Efficient administration</p>
                        </div>
                        <div class="p-4">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Security</h3>
                            <p class="text-gray-600 text-sm">Property security guarantee</p>
                        </div>
                        <div class="p-4">
                            <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">24/7 Support</h3>
                            <p class="text-gray-600 text-sm">Support team always ready</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center">
                    <h2 class="text-3xl font-bold mb-4">Start Now</h2>
                    <p class="text-gray-600 mb-8">Contact our business team for solutions tailored to your company's needs</p>
                    <a href="#" class="btn text-white bg-teal-600 hover:bg-teal-700 rounded-lg px-8 py-4 text-lg font-semibold">
                        Free Consultation
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 