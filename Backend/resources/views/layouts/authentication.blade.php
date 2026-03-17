<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400..700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    @livewireStyles

    <style>
        body {
            background-image: url('/images/workspace.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
        }

        /* Optional: Add a dark overlay to improve text readability */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        /* Ensure content stays above the overlay */
        main {
            position: relative;
            z-index: 1;
        }
    </style>

    <script>
        if (localStorage.getItem('dark-mode') === 'false' || !('dark-mode' in localStorage)) {
            document.querySelector('html').classList.remove('dark');
            document.querySelector('html').style.colorScheme = 'light';
        } else {
            document.querySelector('html').classList.add('dark');
            document.querySelector('html').style.colorScheme = 'dark';
        }
    </script>
</head>

<body class="font-inter antialiased text-white">

    <!-- Language Switcher -->
    <div class="fixed top-4 right-4 z-50">
        <x-language-switcher />
    </div>

    <main class="min-h-screen flex items-center justify-center p-4">

        <div class="w-full max-w-md transform transition-all">
            <!-- Logo Container -->
            <div class="flex justify-center mb-8">
                <a class="block" href="{{ route('dashboard') }}">
                    <div class="bg-white p-4 rounded-full shadow-lg">
                        <img src="/images/apple-touch-icon.png" alt="Logo" class="w-20 h-20">
                    </div>

                </a>
            </div>

            <!-- Content Card -->
            <div>
                {{ $slot }}
            </div>
        </div>

    </main>

    @livewireScriptConfig
</body>

</html>
