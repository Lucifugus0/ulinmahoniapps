<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('homepage.hero.title') }}</title>
  <!-- Tailwind CSS — Play CDN generates utility CSS on-the-fly (avoids loading the full 2.9MB CSS bundle) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    /* Configure Tailwind Play CDN — class-based dark mode to match project settings */
    tailwind.config = { darkMode: 'class' }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <!-- Swiper 11 — carousel/slider for promo banners and property cards -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.2.6/swiper-bundle.min.css" />
  <!-- Inter font — Apple-like clean sans-serif for the liquid glass UI -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
  <!-- Swiper JS loaded before body scripts to ensure availability at DOMContentLoaded -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11.2.6/swiper-bundle.min.js"></script>
  @include('components.homepage.styles')
  <script>
    /* Initialize dark mode from localStorage before paint — prevents flash */
    if (localStorage.getItem('dark-mode') === 'true') {
      document.documentElement.classList.add('dark');
    }
  </script>
  <style>
    /* Inter font override — cleaner type for glass surfaces */
    body, button, input, select, textarea {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'SF Pro Display', 'Segoe UI', Roboto, sans-serif;
    }

    /* Placeholder colors for date inputs */
    input[name="check_in"]::placeholder,
    input[name="check_out"]::placeholder {
      color: var(--text-secondary, #4a4a68) !important;
      opacity: 1 !important;
    }
    html.dark input[name="check_in"]::placeholder,
    html.dark input[name="check_out"]::placeholder {
      color: var(--text-tertiary, #7878a0) !important;
      opacity: 1 !important;
    }
  </style>
</head>
<body class="liquid-glass-page">
  @include('components.homepage.header')

  <main>
    <!-- Hero Section with Search -->
    <div class="hero-section">
      @if($heroMedia['type'] == 'video')
      <div class="hero-content">
        <video id="heroVideo" class="hero-media" autoplay loop muted playsinline>
          <source src="{{ asset($heroMedia['sources']['video']) }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
        <div class="absolute bottom-4 right-4 p-4">
          <button id="playPauseBtn" class="bg-black/50 hover:bg-black/70 text-white p-3 rounded-full">
            <i id="playPauseIcon" class="fas fa-pause"></i>
          </button>
        </div>
      </div> 
      @else
      <div class="hero-content">
          <img id="heroImage" 
              src="{{ asset('images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.45.jpeg') }}" 
              alt="Hero Image"
          class="hero-media">
        </div>
      @endif

      <!-- Overlay with text — frosted text area over hero image -->
      <div class="absolute inset-0 gradient-overlay flex flex-col justify-center md:justify-end p-8 md:p-12 lg:p-16 text-white">
        <!-- Tagline — will be replaced with dynamic tagline from database -->
        <h1 class="text-2xl md:text-5xl lg:text-5xl font-light mb-40 max-w-4xl tracking-tight" style="letter-spacing: -0.03em;">{{ $heroTagline ?? __('homepage.hero.subtitle') }}</h1>
    </div>

      <!-- Search Section -->
      <section class="search-section">
        <div class="search-container">
            <form action="{{ route('properties.index') }}" method="GET" class="search-box">
                <div class="flex flex-col md:flex-row gap-4 md:items-end">
                    <!-- Property Types -->
                    {{-- Field label sits above the input; inner .relative wrapper preserves absolute icon positioning --}}
                    <div class="md:w-48">
                        <label for="search-type" class="search-label">{{ __('homepage.search.label_property_type') }}</label>
                        <div class="relative">
                            <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="search-type" name="type" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                                <option value="">{{ __('homepage.search.all_properties') }}</option>
                                <option value="Kos" {{ request('type') == 'Kos' ? 'selected' : '' }}>{{ __('homepage.property_types.Kos') }}</option>
                                <option value="House" {{ request('type') == 'House' ? 'selected' : '' }}>{{ __('homepage.property_types.House') }}</option>
                                <option value="Apartment" {{ request('type') == 'Apartment' ? 'selected' : '' }}>{{ __('homepage.property_types.Apartment') }}</option>
                                <option value="Villa" {{ request('type') == 'Villa' ? 'selected' : '' }}>{{ __('homepage.property_types.Villa') }}</option>
                                <option value="Hotel" {{ request('type') == 'Hotel' ? 'selected' : '' }}>{{ __('homepage.property_types.Hotel') }}</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Rent Period -->
                    <div class="md:w-48">
                        <label for="search-period" class="search-label">{{ __('homepage.search.label_period') }}</label>
                        <div class="relative">
                            <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <select id="search-period" name="period" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                                <option value="">{{ __('homepage.search.all_periods') }}</option>
                                <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>{{ __('homepage.period.daily') }}</option>
                                <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>{{ __('homepage.period.monthly') }}</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>

                    <!-- Check-in Check-out Dates -->
                    <div class="flex-1 flex gap-4">
                        <div class="w-1/2">
                            <label for="search-check-in" class="search-label">{{ __('homepage.search.label_check_in') }}</label>
                            <div class="relative">
                                <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text"
                                    id="search-check-in"
                                    name="check_in"
                                    value="{{ request('check_in') }}"
                                    onfocus="(this.type='date')"
                                    onblur="if(!this.value) this.type='text'"
                                    placeholder="{{ __('homepage.search.check_in') }}"
                                    class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                        <div class="w-1/2">
                            <label for="search-check-out" class="search-label">{{ __('homepage.search.label_check_out') }}</label>
                            <div class="relative">
                                <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text"
                                    id="search-check-out"
                                    name="check_out"
                                    value="{{ request('check_out') }}"
                                    onfocus="(this.type='date')"
                                    onblur="if(!this.value) this.type='text'"
                                    placeholder="{{ __('homepage.search.check_out') }}"
                                    class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>
                    </div>
                    
                    <script>
                        // Ensure date inputs work correctly with the placeholder solution
                        document.addEventListener('DOMContentLoaded', function() {
                            const checkInInput = document.querySelector('input[name="check_in"]');
                            const checkOutInput = document.querySelector('input[name="check_out"]');
                            const searchForm = document.querySelector('form.search-box');
                            
                            // Initialize input types based on value
                            if (checkInInput && !checkInInput.value) checkInInput.type = 'text';
                            if (checkOutInput && !checkOutInput.value) checkOutInput.type = 'text';
                            
                            // Add min attribute to check-out date when check-in is selected
                            if (checkInInput && checkOutInput) {
                                checkInInput.addEventListener('change', function() {
                                    if (this.value) {
                                        checkOutInput.min = this.value;
                                        if (checkOutInput.value && checkOutInput.value < this.value) {
                                            checkOutInput.value = '';
                                        }
                                    }
                                });
                            }

                            // Save search state when form is submitted
                            if (searchForm) {
                                searchForm.addEventListener('submit', function(e) {
                                    // Save search state to localStorage
                                    const searchState = {
                                        type: this.elements.type.value,
                                        period: this.elements.period.value,
                                        check_in: this.elements.check_in.type === 'date' ? this.elements.check_in.value : '',
                                        check_out: this.elements.check_out.type === 'date' ? this.elements.check_out.value : ''
                                    };
                                    localStorage.setItem('propertySearch', JSON.stringify(searchState));

                                    // Also save to roomBookingDates for room detail page
                                    if (searchState.check_in || searchState.check_out) {
                                        const bookingDates = {
                                            check_in: searchState.check_in,
                                            check_out: searchState.check_out,
                                            period: searchState.period
                                        };
                                        localStorage.setItem('roomBookingDates', JSON.stringify(bookingDates));
                                    }
                                });
                            }
                        });
                    </script>
                    
                    <div class="md:w-48">
                        {{-- Cari Property / Search — themed via var(--accent): green light / maroon dark. --}}
                        <button type="submit"
                            class="w-full h-12 rounded-lg transition-all duration-200 flex items-center justify-center btn-um-themed"
                            style="color: #ffffff;">
                            <i class="fas fa-search mr-2"></i>
                            <span>{{ __('homepage.search.submit') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    </div>

    <!-- Sticky search bar: move to body when scrolled past hero to avoid overflow:hidden clipping -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchSection = document.querySelector('.search-section');
        const heroSection = document.querySelector('.hero-section');
        if (!searchSection || !heroSection) return;

        const originalParent = searchSection.parentElement;
        let isSticky = false;

        const observer = new IntersectionObserver(([entry]) => {
            if (!entry.isIntersecting && !isSticky) {
                document.body.appendChild(searchSection);
                searchSection.classList.add('is-sticky');
                isSticky = true;
            } else if (entry.isIntersecting && isSticky) {
                searchSection.classList.remove('is-sticky');
                originalParent.appendChild(searchSection);
                isSticky = false;
            }
        }, { threshold: 0, rootMargin: '-72px 0px 0px 0px' });

        observer.observe(heroSection);
    });
    </script>

    @include('components.homepage.property-types')
    @include('components.homepage.promos')
    @include('components.homepage.areas')
    {{-- @include('components.homepage.featured') --}}
    {{-- @include('components.homepage.special-offers') --}}
    {{-- @include('components.homepage.liu-house') --}}
  </main>

  @include('components.homepage.footer')
  @include('components.homepage.email-verification-popup')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      @include('components.homepage.scripts')
    });
  </script>
</body>
</html>