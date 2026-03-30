<!DOCTYPE html>
<html class="" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $promo['title'] }} - {{ __('promo.description') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    @include('components.property.styles')
    <style>
        .promo-hero { --transition: all 0.3s ease; }
        .promo-hero img { transition: var(--transition); }
        .promo-hero:hover img { transform: scale(1.02); }
        .badge { transform: translateY(-5px); opacity: 0; transition: var(--transition); }
        .promo-hero:hover .badge { transform: translateY(0); opacity: 1; }
        .cta-button { transition: var(--transition); }
        .cta-button:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }

        /* Liquid glass — main promo content card */
        .bg-white.rounded-xl.shadow-lg { background: var(--glass-bg) !important; backdrop-filter: var(--glass-blur-strong); -webkit-backdrop-filter: var(--glass-blur-strong); border: 1px solid var(--glass-border); box-shadow: var(--glass-shadow); }
        .bg-gray-50.p-8.text-center { background: var(--glass-bg) !important; }

        /* Dark mode overrides */
        html.dark body { background-color: #111827 !important; color: #f3f4f6; }
        html.dark .bg-white.rounded-xl.shadow-lg { background: rgba(31, 41, 55, 0.8) !important; border-color: rgba(75, 85, 99, 0.5) !important; }
        html.dark .bg-gray-50.p-8.text-center { background: rgba(31, 41, 55, 0.6) !important; }
        html.dark .text-gray-900 { color: #f3f4f6 !important; }
        html.dark .text-gray-600 { color: #d1d5db !important; }
        html.dark .text-gray-500 { color: #9ca3af !important; }
        html.dark h2 { color: #f3f4f6 !important; }
        html.dark .text-gray-700 { color: #d1d5db !important; }
        html.dark nav .text-gray-500 { color: #9ca3af !important; }
        html.dark nav .text-gray-900 { color: #e5e7eb !important; }
    </style>
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
</head>

<body class="font-inter antialiased text-gray-900 tracking-tight" style="background-color: #f8f7f4;">
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main>
        <div class="container mx-auto px-4 py-8">
            <!-- Breadcrumb -->
            <nav class="mb-8">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="{{ route('homepage') }}" class="hover:text-gray-700">Home</a></li>
                    <li><span class="mx-2">/</span></li>
                    <li class="text-gray-900">{{ $promo['title'] }}</li>
                </ol>
            </nav>

            <!-- Promo Content -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Hero Section -->
                <div class="relative promo-hero overflow-hidden" style="aspect-ratio: 1911/372;">
                    @if($promo['image'])
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $promo['image'] }}"
                             alt="{{ $promo['title'] }}"
                             class="w-full h-full object-cover"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center\'><i class=\'fas fa-image text-6xl text-gray-400\'></i></div>';">
                    @else
                        <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <i class="fas fa-image text-6xl text-gray-400"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 text-white">
                        <span class="badge bg-yellow-400 text-xs px-3 py-1 rounded-full text-gray-800 font-medium mb-4 inline-block">
                            {{ $promo['badge'] }}
                        </span>
                        <h1 class="text-4xl font-bold mb-2">{{ $promo['title'] }}</h1>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid md:grid-cols-2 gap-8 p-8">
                    <!-- Left Column -->
                    <div>
                        <!-- Promo Code -->
                        @if(!empty($promo['promo_code']))
                        <div class="mb-6">
                            <h2 class="text-2xl font-semibold mb-2">{{ __('promo.promo_code') }}</h2>
                            <span class="text-lg font-bold text-gray-900">{{ $promo['promo_code'] }}</span>
                        </div>
                        @endif

                        <!-- Description -->
                        @if(!empty($promo['description']))
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold mb-4">{{ __('promo.description') }}</h2>
                            <div class="text-gray-600 leading-relaxed">
                                {!! nl2br(e($promo['description'])) !!}
                            </div>
                        </div>
                        @endif

                        <!-- How to Claim -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold mb-4">{{ __('promo.how_to_claim') }}</h2>
                            <ol class="list-decimal list-inside space-y-2 text-gray-600">
                                @foreach($promo['how_to_claim'] as $step)
                                    <li class="flex items-center">
                                        <span class="mr-2">{{ $loop->iteration }}.</span>
                                        <span>{{ $step }}</span>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <!-- Terms & Conditions -->
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold mb-4">{{ __('promo.terms_conditions') }}</h2>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                @foreach($promo['terms_conditions'] as $term)
                                    <li class="flex">
                                        <i class="fas fa-check text-teal-500 mt-1 mr-2"></i>
                                        <span>{{ $term }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Gallery -->
                        @if(!empty($promo['images']) && count($promo['images']) > 1)
                        <div class="mb-8">
                            <h2 class="text-2xl font-semibold mb-4">{{ __('promo.gallery') }}</h2>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($promo['images'] as $image)
                                    <div class="aspect-video rounded-lg overflow-hidden">
                                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $image['image'] }}"
                                             alt="{{ $image['caption'] ?? $promo['title'] }}"
                                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer"
                                             onerror="this.onerror=null; this.parentElement.style.display='none';">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="bg-gray-50 p-8 text-center">
                    <a href="{{ route('homepage') }}" class="cta-button inline-block bg-teal-600 hover:bg-teal-700 text-white font-semibold py-3 px-8 rounded-lg">
                        {{ __('promo.view_properties') }}
                    </a>
                    <p class="text-gray-500 mt-2">{{ __('promo.terms_apply') }}</p>
                </div>
            </div>
        </div>
    </main>

    @include('components.homepage.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @include('components.homepage.scripts')
        });
    </script>
</body>
</html>
