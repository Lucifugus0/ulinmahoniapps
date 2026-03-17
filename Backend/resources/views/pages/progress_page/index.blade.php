<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 px-4">
        <div class="max-w-md w-full text-center">
            <!-- Animated Gear System -->
            <div class="relative mx-auto w-64 h-64 mb-8 flex items-center justify-center">
                <!-- Large Main Gear -->
                <svg class="absolute w-40 h-40 animate-spin-slow" viewBox="0 0 100 100" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 15L54.5 25H45.5L50 15Z" fill="#3B82F6" />
                    <path d="M65 20L72.5 27.5L65 35L57.5 27.5L65 20Z" fill="#3B82F6" />
                    <path d="M85 50L75 54.5V45.5L85 50Z" fill="#3B82F6" />
                    <path d="M80 65L72.5 72.5L65 65L72.5 57.5L80 65Z" fill="#3B82F6" />
                    <path d="M50 85L54.5 75H45.5L50 85Z" fill="#3B82F6" />
                    <path d="M20 65L27.5 72.5L35 65L27.5 57.5L20 65Z" fill="#3B82F6" />
                    <path d="M15 50L25 54.5V45.5L15 50Z" fill="#3B82F6" />
                    <path d="M35 20L27.5 27.5L20 20L27.5 12.5L35 20Z" fill="#3B82F6" />
                    <circle cx="50" cy="50" r="20" fill="#EFF6FF" stroke="#3B82F6" stroke-width="2" />
                </svg>

                <!-- Medium Gear (Top Right) -->
                <svg class="absolute top-8 right-8 w-24 h-24 animate-spin-reverse-slow" viewBox="0 0 100 100"
                    fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 20L55 32.5H45L50 20Z" fill="#60A5FA" />
                    <path d="M70 30L77.5 37.5L70 45L62.5 37.5L70 30Z" fill="#60A5FA" />
                    <path d="M80 50L70 55V45L80 50Z" fill="#60A5FA" />
                    <path d="M70 70L62.5 77.5L55 70L62.5 62.5L70 70Z" fill="#60A5FA" />
                    <path d="M50 80L55 70H45L50 80Z" fill="#60A5FA" />
                    <path d="M30 70L37.5 77.5L45 70L37.5 62.5L30 70Z" fill="#60A5FA" />
                    <path d="M20 50L30 55V45L20 50Z" fill="#60A5FA" />
                    <path d="M30 30L37.5 37.5L45 30L37.5 22.5L30 30Z" fill="#60A5FA" />
                    <circle cx="50" cy="50" r="15" fill="#EFF6FF" stroke="#60A5FA" stroke-width="2" />
                </svg>

                <!-- Small Gear (Bottom Left) -->
                <svg class="absolute bottom-8 left-8 w-16 h-16 animate-spin-slow" viewBox="0 0 100 100" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 25L53 32.5H47L50 25Z" fill="#93C5FD" />
                    <path d="M65 30L70 35L65 40L60 35L65 30Z" fill="#93C5FD" />
                    <path d="M75 50L70 52.5V47.5L75 50Z" fill="#93C5FD" />
                    <path d="M70 65L65 70L60 65L65 60L70 65Z" fill="#93C5FD" />
                    <path d="M50 75L53 70H47L50 75Z" fill="#93C5FD" />
                    <path d="M30 65L35 70L40 65L35 60L30 65Z" fill="#93C5FD" />
                    <path d="M25 50L30 52.5V47.5L25 50Z" fill="#93C5FD" />
                    <path d="M30 30L35 35L40 30L35 25L30 30Z" fill="#93C5FD" />
                    <circle cx="50" cy="50" r="10" fill="#EFF6FF" stroke="#93C5FD" stroke-width="2" />
                </svg>
            </div>

            <!-- Text Content -->
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Sedang Dalam Pengerjaan</h1>
            <p class="text-lg text-gray-600 mb-8">Tim kami sedang menyempurnakan sistem ini. Fitur akan segera hadir
                dengan performa optimal!</p>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-3 mb-8 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full rounded-full animate-progress"
                    style="width: 0%"></div>
            </div>

            <!-- Action Button -->
            <a href="{{ route('dashboard') }}" class="inline-block">
                <button
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 shadow-lg">
                    Kembali ke Dashboard
                </button>
            </a>
        </div>
    </div>

    <style>
        /* Custom animations */
        .animate-spin-slow {
            animation: spin 8s linear infinite;
        }

        .animate-spin-reverse-slow {
            animation: spin-reverse 6s linear infinite;
        }

        .animate-progress {
            animation: progress-load 2.5s ease-in-out forwards;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes spin-reverse {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(-360deg);
            }
        }

        @keyframes progress-load {
            0% {
                width: 0%;
            }

            100% {
                width: 72%;
            }
        }
    </style>
</x-app-layout>
