<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerjasama - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    {{-- Match the header/homepage convention: dark mode only when localStorage explicitly says 'true'.
         Previously this page used `!== 'false'`, which defaulted the page to dark while the header
         defaulted to light — a mismatch when the key was unset. --}}
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
        /* Glass content card — light mode is mostly opaque white so dark text on cards stays
           legible over the bright hero image; dark mode override below drops it back to translucent. */
        .content-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.50);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        /* Dark mode overrides for glass content cards */
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
        /* Overlay — soft white wash in light mode (keeps the image visible without dimming text),
           dark gradient in dark mode for atmospheric contrast. */
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(255,255,255,0.10) 0%, rgba(255,255,255,0.30) 100%);
            z-index: 2;
        }
        html.dark .video-overlay {
            background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.5) 100%);
        }
        /* Push page content below the fixed header */
        main.relative {
            padding-top: 72px;
        }
    </style>
</head>
<body class="font-inter antialiased text-gray-900 tracking-tight video-page">
    <!-- Header -->
    @include('components.homepage.header')

    <main class="relative">
        <!-- Image Background — fixed behind content -->
        <div class="video-wrapper">
            <img src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" alt="Background" class="video-background">
            <div class="video-overlay"></div>
        </div>
        
        <div class="min-h-screen flex flex-col">
            <!-- Hero Section -->
            <section class="py-16 md:py-24 px-4">
                <!-- Hero glass card -->
                <div class="max-w-4xl mx-auto text-center content-card">
                    <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
                        Kerjasama Ulin Mahoni
                    </h1>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                        Kolaborasi yang menguntungkan untuk pengembangan properti bersama
                    </p>
                </div>
            </section>

        <!-- Content Sections -->
        <section class="relative pb-20 px-4 sm:px-6">
            <!-- Main content glass card -->
            <div class="max-w-6xl mx-auto content-card">
                <!-- Partnership Types -->
                <div class="grid md:grid-cols-3 gap-8 mb-16">
                    <!-- Property Owner — glass card styling -->
                    <div class="content-card hover:shadow-md transition-all duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Pemilik Properti</h3>
                        <p class="text-gray-600 mb-4">Daftarkan properti Anda dan dapatkan akses ke pasar yang lebih luas</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Manajemen properti profesional
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pemasaran yang optimal
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pembayaran terjamin
                            </li>
                        </ul>
                    </div>

                    <!-- Agent — glass card styling -->
                    <div class="content-card hover:shadow-md transition-all duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Agen Properti</h3>
                        <p class="text-gray-600 mb-4">Bergabunglah sebagai agen dan tingkatkan pendapatan Anda</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Komisi kompetitif
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Pelatihan profesional
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Akses ke properti eksklusif
                            </li>
                        </ul>
                    </div>

                    <!-- Investor — glass card styling -->
                    <div class="content-card hover:shadow-md transition-all duration-300">
                        <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-2">Investor</h3>
                        <p class="text-gray-600 mb-4">Investasikan dana Anda dalam properti potensial</p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Return investasi tinggi
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Portofolio terverifikasi
                            </li>
                            <li class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-teal-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                Manajemen risiko profesional
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-16">
                    <!-- CTA glass card -->
                    <div class="content-card max-w-4xl mx-auto">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Siap Bergabung?</h3>
                        <p class="text-gray-700 mb-8 max-w-2xl mx-auto">Mari berdiskusi lebih lanjut tentang peluang kerjasama dengan tim kami</p>
                        <a href="#" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold text-lg px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Hubungi Kami
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            <!-- U </div> -->
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 