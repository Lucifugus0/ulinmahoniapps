<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $CRM_ISS->nilai ?? 'Ulin Mahoni' }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        @livewireStyles

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <script>
        // <!-- Default to dark mode -->
        if (localStorage.getItem('dark-mode') !== 'false') {
            document.documentElement.classList.add('dark');
        }
    </script>
    <body class="font-inter antialiased bg-slate-100 dark:bg-gray-900 text-slate-600 dark:text-gray-300">

        {{ $slot }}

        @livewireScripts
    </body>
</html>
