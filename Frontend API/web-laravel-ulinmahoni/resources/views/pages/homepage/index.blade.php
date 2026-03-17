<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('homepage.hero.title') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
  @include('components.homepage.styles')
  <script>
    if (localStorage.getItem('dark-mode') === 'true') {
      document.documentElement.classList.add('dark');
    }
  </script>
  <style>
    /* Override placeholder color for date inputs — dark-mode-aware */
    input[name="check_in"]::placeholder,
    input[name="check_out"]::placeholder {
      color: #000000 !important;
      opacity: 1 !important;
    }
    html.dark input[name="check_in"]::placeholder,
    html.dark input[name="check_out"]::placeholder {
      color: #e5e7eb !important;
      opacity: 1 !important;
    }
  </style>
</head>
<body>
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

      <!-- Overlay with text -->
      <div class="absolute inset-0 gradient-overlay flex flex-col justify-center md:justify-end p-8 md:p-12 lg:p-12  text-white">
        <h1 class="text-2xl md:text-5xl lg:text-4xl font-light mb-3 max-w-4xl">{{ __('homepage.hero.subtitle') }}</h1>
        <p class="text-xl md:text-xl font-light mb-24">{{ __('homepage.hero.subtitle') }}</p>
    </div>

      <!-- Search Section -->
      <section class="search-section">
        <div class="search-container">
            <form action="{{ route('properties.index') }}" method="GET" class="search-box">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- Property Types -->
                    <div class="md:w-48 relative">
                        <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="type" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                            <option value="">{{ __('homepage.search.all_properties') }}</option>
                            <option value="Kos" {{ request('type') == 'Kos' ? 'selected' : '' }}>{{ __('homepage.property_types.Kos') }}</option>
                            <option value="House" {{ request('type') == 'House' ? 'selected' : '' }}>{{ __('homepage.property_types.House') }}</option>
                            <option value="Apartment" {{ request('type') == 'Apartment' ? 'selected' : '' }}>{{ __('homepage.property_types.Apartment') }}</option>
                            <option value="Villa" {{ request('type') == 'Villa' ? 'selected' : '' }}>{{ __('homepage.property_types.Villa') }}</option>
                            <option value="Hotel" {{ request('type') == 'Hotel' ? 'selected' : '' }}>{{ __('homepage.property_types.Hotel') }}</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Rent Period -->
                    <div class="md:w-48 relative">
                        <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="period" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                            <option value="">{{ __('homepage.search.all_periods') }}</option>
                            <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>{{ __('homepage.period.daily') }}</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>{{ __('homepage.period.monthly') }}</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Check-in Check-out Dates -->
                    <div class="flex-1 flex gap-4">
                        <div class="w-1/2 relative">
                            <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                name="check_in"
                                value="{{ request('check_in') }}"
                                onfocus="(this.type='date')"
                                onblur="if(!this.value) this.type='text'"
                                placeholder="{{ __('homepage.search.check_in') }}"
                                class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div class="w-1/2 relative">
                            <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text"
                                name="check_out"
                                value="{{ request('check_out') }}"
                                onfocus="(this.type='date')"
                                onblur="if(!this.value) this.type='text'"
                                placeholder="{{ __('homepage.search.check_out') }}"
                                class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
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
                        <button type="submit" class="w-full h-12 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>
                            <span>{{ __('homepage.search.submit') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    </div>

    @include('components.homepage.property-types')
    @include('components.homepage.promos')
    @include('components.homepage.areas')
    {{-- @include('components.homepage.featured') --}}
    {{-- @include('components.homepage.special-offers') --}}
    {{-- @include('components.homepage.liu-house') --}}
  </main>

  @include('components.homepage.footer')

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      @include('components.homepage.scripts')
    });
  </script>
</body>
</html>