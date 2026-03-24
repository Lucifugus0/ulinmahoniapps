<!DOCTYPE html>
<html lang="en" class="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulin Mahoni</title>
  <!-- Tailwind CSS — Play CDN generates utility CSS on-the-fly (avoids loading the full 2.9MB CSS bundle) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    /* Configure Tailwind Play CDN — class-based dark mode to match project settings */
    tailwind.config = { darkMode: 'class' }
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <!-- Swiper 11 — carousel/slider for promo banners and property cards -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11.2.6/swiper-bundle.min.css" />
  <!-- Swiper JS loaded before body scripts to ensure availability at DOMContentLoaded -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11.2.6/swiper-bundle.min.js"></script>
  @include('components.homepage.styles')
  <script>
    if (localStorage.getItem('dark-mode') === 'true') {
      document.documentElement.classList.add('dark');
    }
  </script>
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

      <!-- Overlay with text -->
      <div class="absolute inset-0 gradient-overlay flex flex-col justify-center md:justify-end p-8 md:p-12 lg:p-12  text-white">
        <h1 class="text-2xl md:text-5xl lg:text-4xl font-light mb-3 max-w-4xl">A safe and harmonious environment</h1>
        <p class="text-xl md:text-xl font-light mb-24">#UlinMahoni</p>
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
                            <option value="">All Properties</option>
                            <option value="kos" {{ request('type') == 'kos' ? 'selected' : '' }}>Boarding House</option>
                            <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                            <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="villa" {{ request('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                            <option value="hotel" {{ request('type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>

                    <!-- Rent Period -->
                    <div class="md:w-48 relative">
                        <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <select name="period" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                            <option value="">All Periods</option>
                            <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Daily</option>
                            <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
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
                                placeholder="Check-in Date"
                                class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div class="w-1/2 relative">
                            <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" 
                                name="check_out" 
                                value="{{ request('check_out') }}"
                                onfocus="(this.type='date')" 
                                onblur="if(!this.value) this.type='text'"
                                placeholder="Check-out Date"
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
                                });
                            }
                        });
                    </script>
                    
                    <div class="md:w-48">
                        <button type="submit" class="w-full h-12 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-search mr-2"></i>
                            <span>Search Property</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
    </div>

    @include('components.homepage.en.property-types')
    @include('components.homepage.en.promos')
    @include('components.homepage.en.areas')
    {{-- @include('components.homepage.en.featured') --}}
    {{-- @include('components.homepage.en.special-offers') --}}
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