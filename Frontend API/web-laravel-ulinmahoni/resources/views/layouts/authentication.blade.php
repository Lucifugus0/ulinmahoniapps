<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $CRM_ISS->nilai }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <script>
        if (localStorage.getItem('dark-mode') === 'true') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <body class="font-inter antialiased bg-slate-100 dark:bg-gray-900 text-slate-600 dark:text-gray-300">

        <main class="bg-white dark:bg-gray-800">

            <div class="relative flex">

                <!-- Content -->
                <div class="w-full md:w-1/2">

                    <div class="min-h-screen h-full flex flex-col after:flex-1">

                        <!-- Header -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                                <!-- Logo -->
                                <a class="block" href="{{ route('dashboard') }}">
                                    <img src="/images/Logo.png" alt="" class='w-10 h-10'>
                                </a>
                            </div>
                        </div>

                        <div class="w-full max-w-sm mx-auto px-4 py-8">
                            {{ $slot }}
                        </div>

                    </div>

                </div>

                <!-- Image -->
                <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
                    {{-- <img class="object-cover object-center w-full h-full" src="{{ asset('images/auth-image.jpg') }}" width="760" height="1024" alt="Authentication image" /> --}}
                    <img class="object-cover object-center w-full h-full" src="{{ asset('images/200w.gif') }}" width="760" height="1024" alt="Authentication image" />
                    <img class="absolute top-1/4 left-0 -translate-x-1/2 ml-8 hidden lg:block" src="{{ asset('images/auth-decoration.png') }}" width="218" height="224" alt="Authentication decoration" />
                </div>
                {{-- <!-- Video -->
                <div class="hidden md:block absolute top-0 bottom-0 right-0 md:w-1/2" aria-hidden="true">
                    <video class="object-cover object-center w-full h-full" autoplay loop muted playsinline>
                        <source src="{{ asset('videos/auth-video.mp4') }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div> --}}

            </div>

        </main>        
    </body>
</html>
