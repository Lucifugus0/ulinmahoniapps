<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Terverifikasi - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
    <style>
        .login-container {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
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
        .confirmation-box {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2.5rem;
            max-width: 28rem;
            width: 100%;
            margin: 2rem;
            text-align: center;
        }
        @media (max-width: 640px) {
            .confirmation-box {
                margin: 1rem;
                padding: 1.5rem;
            }
        }
        .checkmark-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #d1fae5;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        .countdown-bar {
            height: 4px;
            background-color: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
            margin-top: 1.5rem;
        }
        .countdown-fill {
            height: 100%;
            background-color: #0d9488;
            border-radius: 9999px;
            width: 100%;
            transition: width linear;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('components.homepage.header')

    <div class="login-container">
        <!-- Video Background -->
        <div class="video-wrapper">
            <video class="video-background" autoplay loop muted playsinline>
                <source src="{{ asset('images/assets/My_Movie.mp4') }}" type="video/mp4">
            </video>
            <div class="video-overlay"></div>
        </div>

        <div class="confirmation-box">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/assets/ulinmahoni-logo.svg') }}" alt="Ulin Mahoni Logo" class="h-12 w-auto">
            </div>

            <!-- Checkmark Icon -->
            <div class="checkmark-circle">
                <i class="fas fa-check text-3xl text-green-600"></i>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl font-bold text-gray-900 mb-3">Email Berhasil Diverifikasi!</h1>

            <!-- Message -->
            <p class="text-gray-600 mb-2">
                Selamat! Alamat email Anda telah berhasil diverifikasi.
            </p>
            @auth
            <p class="text-sm text-gray-500 mb-6">
                Akun <span class="font-medium text-teal-600">{{ auth()->user()->email }}</span> sudah aktif dan siap digunakan.
            </p>
            @else
            <p class="text-sm text-gray-500 mb-6">Akun Anda sudah aktif dan siap digunakan.</p>
            @endauth

            <!-- CTA Buttons -->
            <div class="space-y-3">
                <a href="{{ route('homepage') }}" class="flex items-center justify-center w-full py-3 px-4 bg-teal-600 hover:bg-teal-700 text-white font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
                <a href="{{ route('login') }}" class="flex items-center justify-center w-full py-3 px-4 border border-teal-600 text-teal-600 hover:bg-teal-50 font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Masuk ke Akun
                </a>
            </div>

            <!-- Auto-redirect countdown -->
            <div class="mt-6">
                <p class="text-xs text-gray-400">Anda akan diarahkan ke beranda dalam <span id="countdown" class="font-semibold text-teal-600">10</span> detik</p>
                <div class="countdown-bar mt-2">
                    <div class="countdown-fill" id="countdownBar"></div>
                </div>
            </div>
        </div>
    </div>

    @include('components.homepage.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const video = document.querySelector('.video-background');
            if (video) {
                video.play().catch(() => { video.controls = true; });
            }

            // Countdown redirect
            let seconds = 10;
            const countdownEl = document.getElementById('countdown');
            const barEl = document.getElementById('countdownBar');

            barEl.style.transition = `width ${seconds}s linear`;
            // Trigger reflow then animate
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    barEl.style.width = '0%';
                });
            });

            const interval = setInterval(function () {
                seconds--;
                countdownEl.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '{{ route('homepage') }}';
                }
            }, 1000);
        });
    </script>
</body>
</html>
