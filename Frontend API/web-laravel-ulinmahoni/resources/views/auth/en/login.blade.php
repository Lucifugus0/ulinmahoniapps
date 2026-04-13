<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ulin Mahoni</title>
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
        /* Override browser autofill yellow background */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
            -webkit-text-fill-color: #111827 !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        /* Liquid glass — translucent login panel with strong blur */
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
                <h2 class="text-4xl font-light text-gray-900 mb-2">Welcome</h2>
                <p class="text-gray-600 text-lg">UlinMahoni</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">{{ __('Your Email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="z-index:1;">
                                <i class="fas fa-envelope" style="color: #111827;"></i>
                            </div>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Email" :value="old('email')" />
                        </div>
                    </div>
                    <div>
                        <label for="password" class="sr-only">{{ __('Your Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none" style="z-index:1;">
                                <i class="fas fa-lock" style="color: #111827;"></i>
                            </div>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 pr-10 border border-gray-200 placeholder-gray-500 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Password" />
                            <button type="button" onclick="const p=document.getElementById('password');const h=p.type==='password';p.type=h?'text':'password';this.querySelector('.eye-open').style.display=h?'none':'block';this.querySelector('.eye-closed').style.display=h?'block':'none';" class="absolute inset-y-0 right-0 pr-3 flex items-center" style="z-index:1; color: #111827;">
                                <i class="fas fa-eye eye-open"></i>
                                <i class="fas fa-eye-slash eye-closed" style="display:none"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}" class="font-medium text-teal-600 hover:text-teal-500">
                                {{ __('Forgot Password?') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-teal-500 group-hover:text-teal-400"></i>
                        </span>
                        {{ __('Login') }}
                    </button>
                </div>
            </form>

            <!-- Divider with "or" text -->
            <div class="flex items-center my-6">
                <div class="flex-1 border-t border-gray-200"></div>
                <span class="px-4 text-sm text-gray-500">or</span>
                <div class="flex-1 border-t border-gray-200"></div>
            </div>

            <!-- Google Login Button -->
            <div class="mt-6 space-y-4">
                <a href="{{ route('login.google') }}" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                    <img class="h-5 w-5 mr-2" src="https://www.google.com/favicon.ico" alt="Google">
                    Sign in with Google
                </a>
                {{-- <a href="#" class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                    <img class="h-5 w-5 mr-2" src="https://img.icons8.com/?size=100&id=30840&format=png&color=000000" alt="Apple">
                    Sign in with Apple
                </a> --}}
            </div>

            <x-jet-validation-errors class="mt-4"/>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    {{ __("Don't have an account?") }}
                    <a href="{{ route('register') }}" class="font-medium text-teal-600 hover:text-teal-500">
                        {{ __('Sign Up') }}
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
