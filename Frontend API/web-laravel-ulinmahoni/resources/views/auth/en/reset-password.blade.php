<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Ulin Mahoni</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    @include('components.homepage.styles')
</head>
<body class="bg-gray-50">
    @include('components.homepage.header')

    <main class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-lg">
            <div>
                <h2 class="text-4xl font-light text-center text-gray-900 mb-2">Reset Password</h2>
                <p class="text-center text-gray-600 text-lg">Enter your new password below</p>
            </div>

            <form class="mt-8 space-y-6" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="email" class="sr-only">{{ __('Your Email') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input id="email" name="email" type="email" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Email address" :value="old('email', $request->email)" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="sr-only">{{ __('Your Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="New Password" autocomplete="new-password" />
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="sr-only">{{ __('Confirm Password') }}</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="appearance-none rounded-lg relative block w-full px-3 py-3 pl-10 border border-gray-200 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Confirm Password" autocomplete="new-password" />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                        {{ __('Reset Password') }}
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
