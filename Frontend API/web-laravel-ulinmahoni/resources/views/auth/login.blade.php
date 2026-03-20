<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.homepage.styles')
    <!-- Dark mode: apply 'dark' class before render to prevent flash of light mode -->
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
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
        /* Text colors handled by central .login-container .login-box rules in homepage/styles.blade.php */
    </style>
</head>
<body class="bg-gray-50">
    @include('components.homepage.header')

    <div class="login-container">
        <!-- Video Background -->
        <div class="video-wrapper">
            <video class="video-background" autoplay loop muted playsinline>
                <source src="{{ asset('images/assets/My_Movie.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="video-overlay"></div>
        </div>
        
        <div class="login-box">

            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/assets/ulinmahoni-logo.svg') }}" alt="Ulin Mahoni Logo" class="h-16 w-auto">
                </div>
                <h2 class="text-4xl font-light text-gray-900 mb-2">{{ __('auth.welcome') }}</h2>
                <p class="text-gray-600 text-lg">Ulin Mahoni</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <x-jet-validation-errors class="mt-4"/>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">{{ __('Your Email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Email" value="{{ old('email') }}" />
                        </div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">{{ __('Your Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Password" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-teal-600 hover:text-teal-500">
                                {{ __('auth.forgot_password_link') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-teal-500 group-hover:text-teal-400"></i>
                        </span>
                        {{ __('auth.login_button') }}
                    </button>
                </div>
            </form>

            <!-- Divider with "or" text -->
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-500">{{ __('auth.or') }}</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            <!-- Google Login Button -->
            <div class="mt-6 space-y-4">
                <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                    <img class="h-5 w-5 mr-2" src="https://www.google.com/favicon.ico" alt="Google">
                    {{ __('auth.login_with_google') }}
                </a>
                <!-- <a href="{{ route('login.apple') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                    <img class="h-5 w-5 mr-2" src="https://img.icons8.com/?size=100&id=30840&format=png&color=000000" alt="Apple">
                    Masuk dengan Apple
                </a> -->
                {{-- <a href="#" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                    <img class="h-5 w-5 mr-2" src="https://img.icons8.com/?size=100&id=30840&format=png&color=000000" alt="Apple">
                    Masuk dengan Apple
                </a> --}}
            </div>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    {{ __('auth.no_account') }}
                    <a href="{{ route('register') }}" class="font-medium text-teal-600 hover:text-teal-500">
                        {{ __('auth.register_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    @include('components.homepage.footer')
    
    <script>
        // Video play/pause functionality
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('.video-background');
            
            // Ensure video plays when page loads
            video.play().catch(error => {
                console.log('Autoplay prevented:', error);
                // Show play button if autoplay is prevented
                video.controls = true;
            });
        });
    </script>
</body>
</html>
