<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Long-term Rental - Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { darkMode: 'class' }</script>
    @include('components.homepage.styles')
    <script>if (localStorage.getItem('dark-mode') !== 'false') document.documentElement.classList.add('dark');</script>
</head>
<body class="font-inter antialiased bg-white text-gray-900 tracking-tight">
    <!-- Header -->
    @include('components.homepage.header')
    <div class="header-spacer"></div>

    <main class="flex-grow">
        <!-- Hero Section -->
        <section class="relative pt-32 pb-12 md:pt-40 md:pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="text-center pb-12 md:pb-16">
                    <h1 class="text-4xl md:text-5xl font-extrabold leading-tighter tracking-tighter mb-4">
                        Long-term Rental
                    </h1>
                    <div class="max-w-3xl mx-auto">
                        <p class="text-xl text-gray-600 mb-8">
                            Flexible and reliable long-term property rental solutions
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Sections -->
        <section class="relative pb-20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="grid md:grid-cols-2 gap-12 md:gap-20">
                    <!-- Features -->
                    <div>
                        <h3 class="h3 mb-6">Benefits of Long-term Rental</h3>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="text-xl font-bold mb-1">Better Value</h4>
                                    <p class="text-gray-600">Get special offers for long-term rentals</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="text-xl font-bold mb-1">Flexibility</h4>
                                    <p class="text-gray-600">Choose rental periods that suit your needs</p>
                                </div>
                            </li>
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-teal-500 mr-4 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <div>
                                    <h4 class="text-xl font-bold mb-1">Reliability</h4>
                                    <p class="text-gray-600">Verified properties with quality assurance</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <!-- How it Works -->
                    <div>
                        <h3 class="h3 mb-6">How It Works</h3>
                        <div class="space-y-8">
                            <div class="flex items-center">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">1</span>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold mb-1">Choose Property</h4>
                                    <p class="text-gray-600">Find a property that meets your needs</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">2</span>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold mb-1">Select Period</h4>
                                    <p class="text-gray-600">Choose your desired rental period</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <span class="flex-shrink-0 w-10 h-10 rounded-full bg-teal-100 text-teal-500 flex items-center justify-center font-bold text-lg">3</span>
                                <div class="ml-4">
                                    <h4 class="text-xl font-bold mb-1">Make Payment</h4>
                                    <p class="text-gray-600">Secure and easy payment process</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Section -->
                <div class="text-center mt-16">
                    <a href="{{ route('properties.index') }}" class="btn text-white bg-teal-600 hover:bg-teal-700 rounded-lg px-8 py-4 text-lg font-semibold">
                        Start Property Search
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    @include('components.homepage.footer')
</body>
</html> 