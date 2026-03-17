<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
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
        .login-box {
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
        }
        @media (max-width: 640px) {
            .login-box {
                margin: 1rem;
                padding: 1.5rem;
            }
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
                Your browser does not support the video tag.
            </video>
            <div class="video-overlay"></div>
        </div>
        
        <div class="login-box">
            <div class="text-center mb-6">
                <div class="flex justify-center mb-4">
                    <img src="{{ asset('images/assets/ulinmahoni-logo.svg') }}" alt="Ulin Mahoni Logo" class="h-16 w-auto">
                </div>
                <h2 class="text-4xl font-light text-gray-900 mb-2">{{ __('auth.create_account') }}</h2>
                <p class="text-gray-600 text-lg">Ulin Mahoni</p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" id="name" name="name" value="">
                <div class="rounded-md shadow-sm space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="sr-only">{{ __('First Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    required
                                    class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="{{ __('auth.first_name') }}"
                                    value="{{ old('first_name') }}" />
                            </div>
                        </div>
                        <div>
                            <label for="last_name" class="sr-only">{{ __('Last Name') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                    placeholder="{{ __('auth.last_name') }}"
                                    value="{{ old('last_name') }}" />
                            </div>
                        </div>
                    </div>

                    <!-- Hidden username field - auto-populated from email -->
                    <input id="username" name="username" type="hidden" value="{{ old('username') }}" />

                    <div>
                        <label for="email" class="sr-only">{{ __('Email Address') }}</label>
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
                        <label for="phone" class="sr-only">{{ __('Phone Number') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input id="phone_number" name="phone_number" type="tel" class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="{{ __('auth.phone_number') }}" value="{{ old('phone_number') }}" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="sr-only">{{ __('Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Password" />
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="sr-only">{{ __('Confirm Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-lg block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="{{ __('auth.confirm_password_placeholder') }}" />
                        </div>
                    </div>
                </div>

                <!-- <div class="flex items-center">
                    <input id="newsletter" name="newsletter" type="checkbox" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                    <label for="newsletter" class="ml-2 block text-sm text-gray-900">
                        Email me about product news
                    </label>
                </div> -->

                <!-- Cloudflare Turnstile -->
                <div class="flex justify-center">
                    <div class="cf-turnstile" data-sitekey="{{ env('CLOUDFLARE_TURNSTILE_SITE_KEY') }}" data-callback="onTurnstileSuccess"></div>
                </div>

                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded" required>
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        {{ __('auth.agree_to') }}
                        <a href="{{ route('terms-of-services') }}" target="_blank" class="text-teal-600 hover:text-teal-500 underline">{{ __('auth.terms_of_service') }}</a>
                        {{ __('auth.and') }}
                        <a href="{{ route('privacy-policy') }}" target="_blank" class="text-teal-600 hover:text-teal-500 underline">{{ __('auth.privacy_policy') }}</a>
                    </label>
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-all duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-user-plus text-teal-500 group-hover:text-teal-400"></i>
                        </span>
                        {{ __('auth.register_button') }}
                    </button>
                </div>
            </form>

            <x-jet-validation-errors class="mt-4"/>

            <div class="text-center mt-4">
                <p class="text-sm text-gray-600">
                    {{ __('auth.has_account') }}
                    <a href="{{ route('login') }}" class="font-medium text-teal-600 hover:text-teal-500">
                        {{ __('auth.login_link') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    @include('components.homepage.footer')
    
    <script>
        // Cloudflare Turnstile validation
        let turnstileToken = null;

        // Callback when Turnstile is successfully completed
        function onTurnstileSuccess(token) {
            turnstileToken = token;
        }

        // Video play/pause functionality
        document.addEventListener('DOMContentLoaded', function() {
            const video = document.querySelector('.video-background');

            // Ensure video plays when page loads
            video.play().catch(error => {
                console.log('Autoplay prevented:', error);
                // Show play button if autoplay is prevented
                video.controls = true;
            });

            // Auto-populate username from email
            const emailInput = document.getElementById('email');
            const usernameInput = document.getElementById('username');

            // Function to sanitize username to match Laravel's alpha_dash validation
            // Only keeps letters, numbers, dashes, and underscores (removes dots, spaces, commas, etc.)
            function sanitizeUsername(str) {
                return str.replace(/[^a-zA-Z0-9_-]/g, '');
            }

            if (emailInput && usernameInput) {
                emailInput.addEventListener('input', function(e) {
                    const email = e.target.value;
                    // Extract username from email (part before @)
                    const username = email.split('@')[0];
                    // Sanitize username to remove invalid characters
                    usernameInput.value = sanitizeUsername(username);
                });

                // Set initial value if email already has a value (e.g., from old input)
                if (emailInput.value) {
                    const username = emailInput.value.split('@')[0];
                    usernameInput.value = sanitizeUsername(username);
                }
            }

            // Handle form submission
            const form = document.querySelector('form[action="{{ route('register') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const firstName = document.getElementById('first_name').value.trim();
                    const lastNameInput = document.getElementById('last_name');

                    // Check Cloudflare Turnstile validation
                    if (!turnstileToken) {
                        e.preventDefault();
                        alert('{{ __('auth.turnstile_required') }}');
                        return false;
                    }

                    // Check if terms checkbox is checked
                    const termsCheckbox = document.getElementById('terms');
                    if (!termsCheckbox.checked) {
                        e.preventDefault();
                        alert('{{ __('auth.terms_required') }}');
                        return false;
                    }

                    // If last name is empty, copy first name to last name
                    if (lastNameInput.value.trim() === '') {
                        lastNameInput.value = firstName;
                    }

                    // Update the hidden name field with full name (first_name + last_name)
                    // This matches the User model's getFullNameAttribute() logic
                    const lastName = lastNameInput.value.trim();
                    const fullName = (firstName + ' ' + lastName).trim();
                    document.getElementById('name').value = fullName;

                    // Ensure username is populated from email with sanitization
                    const email = document.getElementById('email').value;
                    const username = email.split('@')[0];
                    usernameInput.value = sanitizeUsername(username);
                });
            }
        });
    </script>
</body>
</html>
