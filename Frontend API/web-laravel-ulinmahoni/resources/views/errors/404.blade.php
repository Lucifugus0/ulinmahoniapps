<!DOCTYPE html>
<html class="" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - 404 Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="flex items-center justify-center min-h-screen bg-gray-50">
        <div class="max-w-2xl mx-auto px-4 py-8 text-center">
            <div class="mb-8">
                <h1 class="text-6xl font-bold text-teal-600 mb-4">404</h1>
                <p class="text-xl text-gray-600 mb-6">Oops! Page not found</p>
                <p class="text-gray-500 mb-8">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>
                <a href="{{ route('homepage') }}" class="inline-block bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors">
                    Back to Homepage
                </a>
            </div>
            
            <div class="border-t border-gray-200 pt-8">
                <p class="text-gray-500">Looking for something specific?</p>
                <div class="flex justify-center gap-4 mt-4">
                    <a href="/" class="text-teal-600 hover:text-teal-700">Home</a>
                    {{-- <a href="{{ route('properties') }}" class="text-teal-600 hover:text-teal-700">Properties</a>
                    <a href="{{ route('houses') }}" class="text-teal-600 hover:text-teal-700">Houses</a>
                    <a href="{{ route('apartments') }}" class="text-teal-600 hover:text-teal-700">Apartments</a>
                    <a href="{{ route('villas') }}" class="text-teal-600 hover:text-teal-700">Villas</a>
                    <a href="{{ route('hotels') }}" class="text-teal-600 hover:text-teal-700">Hotels</a> --}}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 