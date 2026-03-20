<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.homepage.styles')
    <!-- Liquid glass — translucent forgot-password panel with strong blur -->
    <style>
        .forgot-password-box {
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(48px);
            -webkit-backdrop-filter: blur(48px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            padding: 2rem;
            max-width: 28rem;
            width: 100%;
        }
        /* Darker text for glass on light gradient background */
        .forgot-password-box input::placeholder {
          color: #3d3d55 !important;
          opacity: 1 !important;
        }
        .forgot-password-box .text-gray-400 {
          color: #3d3d55 !important;
        }
        .forgot-password-box input {
          color: #0f0f1a !important;
          background: rgba(255, 255, 255, 0.25) !important;
          border-color: rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('components.homepage.header')

    <main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 forgot-password-box">
            <div>
                <h2 class="text-4xl font-light text-center text-gray-900 mb-2">Forgot Password?</h2>
                <p class="text-center text-gray-600 text-lg">Enter your email address and we'll send you a link to reset your password</p>
            </div>

            @if (session('status'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('status') }}</span>
                </div>
            @endif

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">{{ __('Your Email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Email address" :value="old('email')" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        {{ __('Send Reset Link') }}
                    </button>
                </div>
            </form>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </main>
</body>
</html>
