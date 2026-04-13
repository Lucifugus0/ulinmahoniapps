
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.property.styles')
    @include('components.homepage.styles')
    <!-- Dark mode: apply saved preference before paint -->
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
    <style>
        /* Shared policy page styling for rich-text CMS content */
        .policy-section {
            margin-bottom: 2.5rem;
        }
        .policy-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1rem;
        }
        .policy-section h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin: 1.5rem 0 0.75rem 0;
        }
        .policy-content {
            color: #4b5563;
            line-height: 1.75;
            margin-bottom: 1rem;
        }
        .policy-list {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin: 1rem 0;
        }
        .policy-list li {
            margin-bottom: 0.5rem;
        }
        .policy-sub-list {
            list-style-type: lower-latin;
            padding-left: 1.5rem;
            margin: 0.5rem 0;
        }
        /* Rich text content styling for CMS output */
        .legal-rich-content h1 { font-size: 1.75rem; font-weight: 700; margin-bottom: 1rem; }
        .legal-rich-content h2 { font-size: 1.5rem; font-weight: 600; margin-bottom: 0.75rem; }
        .legal-rich-content h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
        .legal-rich-content p { margin-bottom: 1rem; line-height: 1.75; }
        .legal-rich-content ul { list-style-type: disc; padding-left: 1.5rem; margin: 1rem 0; }
        .legal-rich-content ol { list-style-type: decimal; padding-left: 1.5rem; margin: 1rem 0; }
        .legal-rich-content li { margin-bottom: 0.5rem; }
        .legal-rich-content a { color: #2563eb; text-decoration: underline; }
        .legal-rich-content table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .legal-rich-content th, .legal-rich-content td { border: 1px solid #e5e7eb; padding: 0.5rem 1rem; }

        /* Dark mode overrides for legal pages */
        html.dark body { background-color: #111827 !important; color: #e5e7eb !important; }
        html.dark .policy-section h2 { color: #f3f4f6 !important; }
        html.dark .policy-section h3 { color: #e5e7eb !important; }
        html.dark .policy-content { color: #d1d5db !important; }
        html.dark .legal-rich-content h1,
        html.dark .legal-rich-content h2,
        html.dark .legal-rich-content h3 { color: #f3f4f6 !important; }
        html.dark .legal-rich-content p,
        html.dark .legal-rich-content li { color: #d1d5db !important; }
        html.dark .legal-rich-content a { color: #60a5fa !important; }
        html.dark .legal-rich-content th,
        html.dark .legal-rich-content td { border-color: #374151 !important; color: #d1d5db !important; }
        html.dark .legal-card { background-color: #1f2937 !important; border-color: #374151 !important; }
        html.dark .breadcrumb-text { color: #9ca3af !important; }
        html.dark .breadcrumb-active { color: #e5e7eb !important; }
    </style>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight" style="background-color: white;">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb navigation -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="breadcrumb-text hover:text-gray-700">Beranda</a></li>
                    <li><span class="mx-2 breadcrumb-text">/</span></li>
                    <li class="breadcrumb-active">{{ $title }}</li>
                </ol>
            </nav>

            <!-- Main Content: renders CMS rich-text content from database -->
            <!-- Main content card with dark mode support via .legal-card class -->
            <div class="legal-card bg-white rounded-lg shadow-sm border border-gray-100 p-6 md:p-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-8" style="color: inherit;">{{ $title }}</h1>

                <div class="prose max-w-none legal-rich-content">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html>
