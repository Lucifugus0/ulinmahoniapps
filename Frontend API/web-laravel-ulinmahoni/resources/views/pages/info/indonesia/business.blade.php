<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni for Business - Solusi Properti untuk Bisnis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <!-- Styles -->
    @include('components.property.styles')
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
        html.dark .content-card {
            background: rgba(255, 255, 255, 0.06) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-color: rgba(255, 255, 255, 0.10) !important;
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
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            z-index: 1;
        }
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
            z-index: 2;
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
                        Ulin Mahoni for Business
                    </h1>
                    <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                        Solusi properti lengkap untuk kebutuhan bisnis Anda
                    </p>
                </div>
            </section>

        <!-- Content Sections -->
        <section class="relative pb-20 px-4 sm:px-6">
            <div class="max-w-6xl mx-auto">
                <!-- Business Solutions -->
                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <!-- Corporate Housing — glass card styling -->
                    <div class="content-card hover:shadow-lg transition-all duration-300">
                        <h3 class="text-2xl font-bold mb-4">Corporate Housing</h3>
                        <p class="text-gray-600 mb-6">Solusi akomodasi untuk karyawan dan tamu perusahaan Anda</p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Fleksibilitas Tinggi</h4>
                                    <p class="text-gray-600">Pilihan periode sewa yang dapat disesuaikan</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Lokasi Strategis</h4>
                                    <p class="text-gray-600">Dekat dengan pusat bisnis dan fasilitas penting</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Layanan Lengkap</h4>
                                    <p class="text-gray-600">Termasuk perawatan dan manajemen properti</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- Office Space — glass card styling -->
                    <div class="content-card hover:shadow-lg transition-all duration-300">
                        <h3 class="text-2xl font-bold mb-4">Ruang Kantor</h3>
                        <p class="text-gray-600 mb-6">Ruang kerja yang dapat disesuaikan dengan kebutuhan bisnis</p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Ruang Fleksibel</h4>
                                    <p class="text-gray-600">Dapat disesuaikan dengan ukuran tim</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Fasilitas Modern</h4>
                                    <p class="text-gray-600">Internet cepat dan ruang meeting</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="font-semibold mb-1">Lokasi Premium</h4>
                                    <p class="text-gray-600">Di area bisnis strategis</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Benefits Section -->
                <div class="text-center mb-16">
                    <div class="grid md:grid-cols-4 gap-6">
                        <div class="content-card">
                            <div class="w-16 h-16 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Efisiensi Biaya</h3>
                            <p class="text-gray-600 text-sm">Optimalisasi pengeluaran properti</p>
                        </div>
                        <div class="content-card">
                            <div class="w-16 h-16 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Proses Cepat</h3>
                            <p class="text-gray-600 text-sm">Administrasi yang efisien</p>
                        </div>
                        <div class="content-card">
                            <div class="w-16 h-16 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Keamanan</h3>
                            <p class="text-gray-600 text-sm">Jaminan keamanan properti</p>
                        </div>
                        <div class="content-card">
                            <div class="w-16 h-16 bg-teal-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold mb-2">Dukungan 24/7</h3>
                            <p class="text-gray-600 text-sm">Tim support selalu siap</p>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-16">
                    <!-- CTA glass card -->
                    <div class="content-card">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Mulai Sekarang</h3>
                        <p class="text-gray-700 mb-8 max-w-2xl mx-auto">Hubungi tim bisnis kami untuk solusi yang sesuai dengan kebutuhan perusahaan Anda</p>
                        <a href="#" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold text-lg px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Konsultasi Gratis
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 