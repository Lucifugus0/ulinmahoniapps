<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sewa Berjangka - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <!-- Styles -->
    @include('components.property.styles')
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') === 'true') document.documentElement.classList.add('dark');</script>
    <style>
        /* Base styles */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            scroll-behavior: smooth;
        }
        
        /* Video background container */
        .video-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        /* Video element */
        .video-background {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translate(-50%, -50%);
            object-fit: cover;
        }
        
        /* Overlay for better text readability */
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.6) 100%);
            z-index: 1;
        }
        
        /* Main content container */
        main {
            position: relative;
            z-index: 2;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Section styling */
        section {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            width: calc(100% - 4rem);
            max-width: 1200px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            color: #1a202c;
            font-weight: 700;
            line-height: 1.2;
        }
        
        p {
            color: #4a5568;
            line-height: 1.6;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            section {
                width: calc(100% - 2rem);
                margin: 1rem auto;
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 480px) {
            section {
                width: calc(100% - 1rem);
                margin: 0.5rem auto;
                padding: 1.25rem;
                border-radius: 0.75rem;
            }
        }
        
        /* Animation for content */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        
       
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')

    <main class="relative">
        <!-- Video Background -->
        <div class="video-wrapper">
            <video class="video-background" autoplay loop muted playsinline>
                <source src="{{ asset('images/assets/My_Movie.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-overlay"></div>
        </div>
        
        <div class="min-h-screen flex flex-col">
            <!-- Hero Section -->
            <section class="py-16 md:py-24 px-4 max-w-4xl mx-auto text-center bg-white p-8 rounded-2xl shadow-lg my-8">
                <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-6">
                    Sewa Berjangka
                </h1>
                <p class="text-xl text-gray-700 max-w-3xl mx-auto">
                    Solusi penyewaan properti jangka panjang yang fleksibel dan terpercaya
                </p>
            </section>

            <!-- Content Sections -->
            <section class="w-full py-12 px-4">
                <div class="max-w-6xl mx-auto">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Features -->
                        <div class="space-y-6">
                            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-md border border-white/20">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Keuntungan Sewa Berjangka</h3>
                                <ul class="space-y-4">
                                    <li class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 flex items-center justify-center mr-4 mt-1">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Harga Lebih Hemat</h4>
                                            <p class="text-gray-700 text-sm">Dapatkan penawaran khusus untuk penyewaan jangka panjang</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 flex items-center justify-center mr-4 mt-1">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Fleksibel</h4>
                                            <p class="text-gray-700 text-sm">Pilih periode sewa yang sesuai dengan kebutuhan Anda</p>
                                        </div>
                                    </li>
                                    <li class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 flex items-center justify-center mr-4 mt-1">
                                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Terpercaya</h4>
                                            <p class="text-gray-700 text-sm">Properti terverifikasi dengan jaminan kualitas terbaik</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- How it Works -->
                        <div class="space-y-6">
                            <div class="bg-white/80 backdrop-blur-sm p-6 rounded-2xl shadow-md border border-white/20">
                                <h3 class="text-2xl font-bold text-gray-900 mb-6">Cara Kerja</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold text-lg mr-4">1</span>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Pilih Properti</h4>
                                            <p class="text-gray-700 text-sm">Temukan properti yang sesuai dengan kebutuhan Anda</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold text-lg mr-4">2</span>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Tentukan Periode</h4>
                                            <p class="text-gray-700 text-sm">Pilih periode sewa yang Anda inginkan</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start p-4 bg-white/60 rounded-xl hover:bg-white/90 transition-all duration-300 border border-white/30">
                                        <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-500/10 text-teal-600 flex items-center justify-center font-bold text-lg mr-4">3</span>
                                        <div>
                                            <h4 class="text-lg font-bold text-gray-900 mb-1">Lakukan Pembayaran</h4>
                                            <p class="text-gray-700 text-sm">Proses pembayaran yang aman dan mudah</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Section -->
                    <div class="text-center mt-16">
                        <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-md border border-white/20">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Siap Memulai?</h3>
                            <p class="text-gray-700 mb-8 max-w-2xl mx-auto">Temukan properti impian Anda sekarang dan nikmati kemudahan sewa berjangka</p>
                            <a href="{{ route('homepage') }}" class="inline-flex items-center justify-center bg-green-500 hover:bg-green-600 text-white font-semibold text-lg px-8 py-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                Mulai Mencari Properti
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
            
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 