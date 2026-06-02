<!DOCTYPE html>
<!-- Tentang (About) page — supports light/dark theme, includes homepage styles for dark mode CSS -->
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Ulin Mahoni - Solusi Properti Terpercaya</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    {{-- Dark mode: apply only when localStorage explicitly says 'true' — matches the header
         and homepage convention so the page and header agree on the initial theme. --}}
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
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
        /* Overlay — soft white wash in light mode, dark gradient in dark mode. */
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

        /* Glass content card — opaque white in light mode keeps dark Tailwind text readable;
           dark mode override below drops it back to translucent dark glass. */
        .content-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.50);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }

        /* Dark mode overrides for content containers */
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
            <!-- About Section -->
            <section class="relative pt-16 pb-12 md:pt-20 md:pb-20 px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center pb-12 md:pb-16 content-card">
                        <h1 class="text-4xl md:text-5xl font-extrabold leading-tighter tracking-tighter mb-4 text-gray-900">
                            Tentang Ulin Mahoni
                        </h1>
                        <div class="max-w-3xl mx-auto">
                            <p class="text-xl text-gray-600 mb-8">
                                Membangun masa depan properti Indonesia yang lebih baik
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
                            <h2 class="text-3xl font-bold mb-6 text-gray-900">Visi Kami</h2>
                            <p class="text-gray-600 mb-8">
                                Menjadi platform properti terdepan yang menghubungkan dan memberikan solusi terbaik bagi seluruh kebutuhan properti di Indonesia.
                            </p>
                            <div class="bg-teal-50 p-6 rounded-lg">
                                <h3 class="font-bold mb-4 text-gray-900">Nilai-Nilai Kami</h3>
                                <ul class="space-y-3">
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">Integritas</span>
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">Inovasi</span>
                                    </li>
                                    <li class="flex items-center">
                                        <svg class="w-5 h-5 text-teal-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-gray-700">Kepuasan Pelanggan</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="content-card">
                            <h2 class="text-3xl font-bold mb-6 text-gray-900">Misi Kami</h2>
                            <ul class="space-y-6">
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">1</span>
                                    <div class="ml-4">
                                        <p class="text-gray-600">Menyediakan solusi properti yang inovatif dan terpercaya</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">2</span>
                                    <div class="ml-4">
                                        <p class="text-gray-600">Membangun ekosistem properti yang transparan dan efisien</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <span class="flex-shrink-0 w-8 h-8 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">3</span>
                                    <div class="ml-4">
                                        <p class="text-gray-600">Memberikan pengalaman terbaik bagi seluruh pengguna</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Team Section -->
                    <div class="text-center mb-16">
                        <div class="content-card mb-8">
                            <h2 class="text-3xl font-bold mb-2 text-gray-900">Tim Kami</h2>
                        </div>
                        <div class="grid md:grid-cols-3 gap-8">
                            <div class="content-card hover:scale-105 transition-transform duration-300">
                                <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                                <h3 class="font-bold text-xl mb-1 text-gray-900">John Doe</h3>
                                <p class="text-gray-600 mb-4">CEO & Founder</p>
                                <p class="text-gray-600 text-sm">10+ tahun pengalaman di industri properti</p>
                            </div>
                            <div class="content-card hover:scale-105 transition-transform duration-300">
                                <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                                <h3 class="font-bold text-xl mb-1 text-gray-900">Jane Smith</h3>
                                <p class="text-gray-600 mb-4">Head of Operations</p>
                                <p class="text-gray-600 text-sm">8+ tahun pengalaman manajemen operasional</p>
                            </div>
                            <div class="content-card hover:scale-105 transition-transform duration-300">
                                <div class="w-24 h-24 rounded-full bg-gray-200 mx-auto mb-4"></div>
                                <h3 class="font-bold text-xl mb-1 text-gray-900">Mike Johnson</h3>
                                <p class="text-gray-600 mb-4">Head of Technology</p>
                                <p class="text-gray-600 text-sm">12+ tahun pengalaman teknologi properti</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Section -->
                    <div class="text-center mt-16">
                        <div class="content-card">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Hubungi Kami</h3>
                            <p class="text-gray-700 mb-8 max-w-2xl mx-auto">Kami siap membantu Anda menemukan properti impian</p>
                            <a href="#" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold text-lg px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Kontak Sekarang
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                            <div class="grid md:grid-cols-2 gap-8 mt-8">
                                <div class="p-4">
                                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold mb-2 text-gray-900">Telepon</h3>
                                    <p class="text-gray-600">+62 123 4567 890</p>
                                </div>
                                <div class="p-4">
                                    <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-6 h-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-bold mb-2 text-gray-900">Alamat</h3>
                                    <p class="text-gray-600">Jakarta, Indonesia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div> <!-- /.min-h-screen -->
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html>
