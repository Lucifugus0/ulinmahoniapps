<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ulin Mahoni</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    /* Custom styles */
    :root {
      --teal-600: #0d9488;
      --teal-700: #0f766e;
      --red-500: #ef4444;
      --red-700: #b91c1c;
    }
    
    body {
      background-color: #f5f2ea;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
    
    .bg-teal-600 {
      background-color: var(--teal-600);
    }
    
    .bg-teal-700 {
      background-color: var(--teal-700);
    }
    
    .text-teal-600 {
      color: var(--teal-600);
    }
    
    .hover\:bg-teal-700:hover {
      background-color: var(--teal-700);
    }
    
    .bg-red-500 {
      background-color: var(--red-500);
    }
    
    .bg-red-700 {
      background-color: var(--red-700);
    }
    
    .property-card {
      transition: transform 0.3s ease;
    }
    
    .property-card:hover .card-image {
      transform: scale(1.05);
    }
    
    .card-image {
      transition: transform 0.5s ease;
    }
    
    .tab-trigger {
      position: relative;
    }
    
    .tab-trigger.active {
      border-bottom: 2px solid var(--teal-600);
    }
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
    }
    
    .gradient-overlay {
      background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);
    }
    
    .feature-icon {
      width: 40px;
      height: 40px;
      background-color: #f3f4f6;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 0.25rem;
    }

    .hero-section {
        position: relative;
        width: 100%;
        height: 600px;
        background-color: #111827;
    }

    /* Add styles for header */
    .site-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 50;
        background-color: #f5f2ea;
        border-bottom: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(8px);
    }

    /* Add padding to main content to account for fixed header */
    main {
        padding-top: 72px; /* Height of the header */
    }

    /* Hide header on scroll down, show on scroll up */
    /* .site-header.header-hidden {
        transform: translateY(-100%);
    } */

    /* Responsive heights */
    @media (min-width: 768px) {
        .hero-section {
            height: 900px;
        }
    }

    @media (min-width: 1024px) {
        .hero-section {
            height: 800px;
        }
    }

    .hero-content {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .hero-media {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .search-section {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        transform: translateY(50%);
        z-index: 30;
    }

    .search-container {
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1rem;
    }

    .search-box {
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 1rem;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 1.5rem;
    }

    /* Section styling */
    .section-light {
        background-color: #f8f7f4;
        padding: 4rem 0;
    }

    .section-dark {
        background-color: #f5f2ea;
        padding: 4rem 0;
    }

    .section-container {
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .section-title {
        text-align: center;
        margin-bottom: 3rem;
    }

    .section-title h2 {
        font-size: 2.25rem;
        font-weight: 300;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .section-title .divider {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
    }

    .section-title .divider-line {
        width: 3rem;
        height: 1px;
        background-color: #d1d5db;
    }

    .section-title .divider-text {
        color: #0d9488;
        font-style: italic;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="site-header py-4 px-6 flex items-center justify-between">
    <div class="flex items-center space-x-8">
      <img src="images/assets/ulinmahoni-logo.svg" alt="Ulin Mahoni Logo" class="h-10 w-auto">
      <nav class="hidden md:flex">
        <ul class="flex space-x-6">
          <li>
            <a href="/sewa" class="text-sm text-gray-600 hover:text-gray-900">
              Sewa
            </a>
          </li>
          <li>
            <a href="/kerjasama" class="text-sm text-gray-600 hover:text-gray-900">
              Kerjasama Ulin Mahoni
            </a>
          </li>
          <li>
            <a href="/business" class="text-sm text-gray-600 hover:text-gray-900">
              Ulin Mahoni for Business
            </a>
          </li>
          <li>
            <a href="/tentang" class="text-sm text-gray-600 hover:text-gray-900">
              Tentang Ulin Mahoni
            </a>
          </li>
        </ul>
      </nav>
    </div>
    <div class="flex items-center space-x-4">
      <div class="flex items-center space-x-2">
        {{-- <img src="https://via.placeholder.com/24" alt="ID Flag" class="rounded-full"> --}}
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 64 64" class="mr-2">
            <image alt="ID Flag" class="rounded-full mr-2" xlink:href="data:image/png;base64,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" x="0" y="0" width="64" height="64"/>
          </svg>
        <span class="text-sm text-gray-600">ID</span>
      </div>
      <button class="border border-gray-300 rounded-md px-4 py-2 text-sm">
        Masuk / Daftar
      </button>
    </div>
  </header>

  <main>
    <!-- Hero Section with Video -->
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
      <div class="absolute inset-0 gradient-overlay flex flex-col justify-end p-8 md:p-12 lg:p-16 text-white">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-light mb-3">A safe and harmonious environment</h1>
        <p class="text-xl md:text-2xl font-light">#UlinMahoni</p>
      </div>

      <!-- Search Section - Positioned at bottom of hero -->
      <section class="search-section">
        <div class="search-container">
          <div class="search-box">
            <div class="flex flex-col md:flex-row gap-4">
              <div class="flex-1 relative">
                <i class="fas fa-map-marker-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Cari lokasi, nama gedung atau landmark..." class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
              </div>
              <div class="md:w-48 relative">
                <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="date" placeholder="Pilih tanggal" class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
              </div>
              <div class="md:w-48 relative">
                <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <select class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white">
                  <option value="">Semua tipe</option>
                  <option value="house">House & Room</option>
                  <option value="apartment">Apartment</option>
                  <option value="villa">Villa</option>
                  <option value="hotel">Hotel</option>
                </select>
              </div>
              <div class="md:w-48">
                <button class="w-full h-12 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-colors duration-200">
                  <i class="fas fa-search mr-2"></i>
                  Cari Hunian
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- Property Types Section -->
    <section class="section-light mt-4">
      <div class="section-container">
        <div class="section-title">
          <h2>Properties</h2>
          <div class="divider">
            <div class="divider-line"></div>
            <p class="divider-text">Exclusive & Cozy</p>
            <div class="divider-line"></div>
          </div>
        </div>

        <div class="w-full">
          <!-- Property Type Tabs -->
          <div class="flex border-b border-gray-200 mb-6">
            <button class="property-tab-trigger px-6 py-3 text-sm font-medium text-teal-600 border-b-2 border-teal-600" data-tab="house">
              House & Room
            </button>
            <button class="property-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="apartment">
              Apartment
            </button>
            <button class="property-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="villa">
              Villa
            </button>
            <button class="property-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="hotel">
              Hotel
            </button>
          </div>

          <!-- Property Type Content -->
          <div class="property-tab-contents">
            <!-- House & Room Content -->
            <div class="property-tab-content active" data-tab="house">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Property Card 1 -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative">
                    <div class="relative h-48 overflow-hidden">
                      <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0006.jpg" 
                           alt="Rexucia House & Room" 
                           class="card-image w-full h-full object-cover">
                      <div class="absolute top-2 right-2 z-10">
                      </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 gradient-overlay h-16"></div>
                    <div class="absolute bottom-2 left-2">
                      <span class="bg-teal-600 text-white text-xs px-2 py-1 rounded-full">Coliving</span>
                    </div>
                  </div>

                  <div class="p-4">
                    <h3 class="font-medium text-gray-800 mb-1">Rexucia House & Room</h3>
                    <p class="text-gray-500 text-sm mb-1">Petojo Selatan, Gambir</p>
                    <p class="text-gray-500 text-xs mb-3">2.4 km dari Stasiun MRT Bundaran HI</p>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-xs text-gray-500">
                          mulai dari <span class="line-through">Rp1.300.000</span>
                        </p>
                        <div class="flex items-center">
                          <p class="font-bold text-gray-800">
                            Rp975.000 <span class="text-xs font-normal">/bulan</span>
                          </p>
                        </div>
                      </div>

                      <div class="flex flex-col space-y-2 text-xs text-gray-500">
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">Diskon sewa 12 Bulan</span>
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">S+ Voucher s.d. 2%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Property Card 2 -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative">
                    <div class="relative h-48 overflow-hidden">
                      <img src="images/assets/apt.jpg" 
                           alt="Royal Mediteranian Apartment" 
                           class="card-image w-full h-full object-cover">
                      <div class="absolute top-2 right-2 z-10">
                      </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 gradient-overlay h-16"></div>
                    <div class="absolute bottom-2 left-2">
                      <span class="bg-teal-600 text-white text-xs px-2 py-1 rounded-full">Coliving</span>
                    </div>
                  </div>

                  <div class="p-4">
                    <h3 class="font-medium text-gray-800 mb-1">Royal Mediteranian Apartment</h3>
                    <p class="text-gray-500 text-sm mb-1">Cilandak Barat, Cilandak</p>
                    <p class="text-gray-500 text-xs mb-3">976 m dari Stasiun MRT Fatmawati</p>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-xs text-gray-500">
                          mulai dari <span class="line-through">Rp4.250.000</span>
                        </p>
                        <div class="flex items-center">
                          <p class="font-bold text-gray-800">
                            Rp4.025.000 <span class="text-xs font-normal">/bulan</span>
                          </p>
                        </div>
                      </div>

                      <div class="flex flex-col space-y-2 text-xs text-gray-500">
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">Diskon sewa 12 Bulan</span>
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">S+ Voucher s.d. 2%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Property Card 3 -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative">
                    <div class="relative h-48 overflow-hidden">
                      <img src="images/assets/villa.jpg" 
                           alt="Xilonen Villa" 
                           class="card-image w-full h-full object-cover">
                      <div class="absolute top-2 right-2 z-10">
                      </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 gradient-overlay h-16"></div>
                    <div class="absolute bottom-2 left-2">
                      <span class="bg-teal-600 text-white text-xs px-2 py-1 rounded-full">Coliving</span>
                    </div>
                  </div>

                  <div class="p-4">
                    <h3 class="font-medium text-gray-800 mb-1">Xilonen Villa</h3>
                    <p class="text-gray-500 text-sm mb-1">Kelurahan Fatmawati, Fatmawati</p>
                    <p class="text-gray-500 text-xs mb-3">3.3 km dari Stasiun MRT Bundaran Senayan</p>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-xs text-gray-500">
                          mulai dari <span class="line-through">Rp2.400.000</span>
                        </p>
                        <div class="flex items-center">
                          <p class="font-bold text-gray-800">
                            Rp2.275.000 <span class="text-xs font-normal">/bulan</span>
                          </p>
                        </div>
                      </div>

                      <div class="flex flex-col space-y-2 text-xs text-gray-500">
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">Diskon sewa 12 Bulan</span>
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">S+ Voucher s.d. 2%</span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Property Card 4 -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative">
                    <div class="relative h-48 overflow-hidden">
                      <img src="images/assets/hotel.jpg" 
                           alt="Kvlarya Hotel" 
                           class="card-image w-full h-full object-cover">
                      <div class="absolute top-2 right-2 z-10">
                      </div>
                    </div>
                    <div class="absolute bottom-0 left-0 right-0 gradient-overlay h-16"></div>
                    <div class="absolute bottom-2 left-2">
                      <span class="bg-pink-600 text-white text-xs px-2 py-1 rounded-full">Coliving Wanita</span>
                    </div>
                  </div>

                  <div class="p-4">
                    <h3 class="font-medium text-gray-800 mb-1">Kvlarya Hotel</h3>
                    <p class="text-gray-500 text-sm mb-1">Tegalrejo, Bogor Utara</p>
                    <p class="text-gray-500 text-xs mb-3">2.3 km dari Stasiun Bogor</p>

                    <div class="flex items-center justify-between">
                      <div>
                        <p class="text-xs text-gray-500">
                          mulai dari <span class="line-through">Rp2.000.000</span>
                        </p>
                        <div class="flex items-center">
                          <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full mr-2">HOT</span>
                          <p class="font-bold text-gray-800">
                            Rp1.891.999 <span class="text-xs font-normal">/bulan</span>
                          </p>
                        </div>
                      </div>

                      <div class="flex flex-col space-y-2 text-xs text-gray-500">
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">Diskon sewa 12 Bulan</span>
                        <span class="border border-gray-300 rounded-lg px-3 py-1.5 text-center">S+ Voucher s.d. 2%</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Apartment Content -->
            <div class="property-tab-content hidden" data-tab="apartment">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Property cards for Apartment -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <!-- Your existing apartment property card content -->
                </div>
                <!-- More apartment property cards -->
              </div>
            </div>

            <!-- Villa Content -->
            <div class="property-tab-content hidden" data-tab="villa">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Property cards for Villa -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <!-- Your existing villa property card content -->
                </div>
                <!-- More villa property cards -->
              </div>
            </div>

            <!-- Hotel Content -->
            <div class="property-tab-content hidden" data-tab="hotel">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <!-- Property cards for Hotel -->
                <div class="property-card bg-white rounded-lg shadow-md overflow-hidden">
                  <!-- Your existing hotel property card content -->
                </div>
                <!-- More hotel property cards -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Living Space Section -->
    {{-- <section class="py-12 px-4 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
          <h2 class="text-3xl font-light text-gray-800 mb-2">Living Space</h2>
          <div class="flex items-center justify-center">
            <div class="w-12 h-px bg-gray-300"></div>
            <p class="mx-4 text-teal-600 italic">Exclusive & Cozy</p>
            <div class="w-12 h-px bg-gray-300"></div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <!-- Feature Card 1 -->
          <div class="bg-white shadow-md overflow-hidden rounded-lg">
            <div class="relative">
              <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0006.jpg" 
                   alt="Rexucia" 
                   class="w-full h-64 object-cover">
              <div class="absolute bottom-0 left-0 right-0 bg-red-700 text-white p-3 text-center">
                <h3 class="text-xl font-light">Rexucia</h3>
              </div>
            </div>
            <div class="p-6 bg-gray-50">
              <h4 class="text-xl font-light text-center text-gray-700 mb-4">HOUSE & ROOM</h4>
              <p class="text-gray-600 text-center text-sm mb-6">Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.</p>
              <div class="text-center">
                <a href="#" class="text-teal-600 hover:text-teal-700">View</a>
              </div>
            </div>
          </div>

          <!-- Feature Card 2 -->
          <div class="bg-white shadow-md overflow-hidden rounded-lg">
            <div class="relative">
              <img src="images/assets/apt.jpg" 
                   alt="Royal Mediteranian" 
                   class="w-full h-64 object-cover">
              <div class="absolute bottom-0 left-0 right-0 bg-teal-700 text-white p-3 text-center">
                <h3 class="text-xl font-light">Royal Mediteranian</h3>
              </div>
            </div>
            <div class="p-6 bg-gray-50">
              <h4 class="text-xl font-light text-center text-gray-700 mb-4">APARTMENT</h4>
              <p class="text-gray-600 text-center text-sm mb-6">Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.</p>
              <div class="text-center">
                <a href="#" class="text-teal-600 hover:text-teal-700">View</a>
              </div>
            </div>
          </div>

          <!-- Feature Card 3 -->
          <div class="bg-white shadow-md overflow-hidden rounded-lg">
            <div class="relative">
              <img src="images/assets/villa.jpg" 
                   alt="Xilonen" 
                   class="w-full h-64 object-cover">
              <div class="absolute bottom-0 left-0 right-0 bg-teal-700 text-white p-3 text-center">
                <h3 class="text-xl font-light">Xilonen</h3>
              </div>
            </div>
            <div class="p-6 bg-gray-50">
              <h4 class="text-xl font-light text-center text-gray-700 mb-4">VILLA</h4>
              <p class="text-gray-600 text-center text-sm mb-6">Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.</p>
              <div class="text-center">
                <a href="#" class="text-teal-600 hover:text-teal-700">View</a>
              </div>
            </div>
          </div>

          <!-- Feature Card 4 -->
          <div class="bg-white shadow-md overflow-hidden rounded-lg">
            <div class="relative">
              <img src="images/assets/hotel.jpg" 
                   alt="Kvlarya" 
                   class="w-full h-64 object-cover">
              <div class="absolute bottom-0 left-0 right-0 bg-red-700 text-white p-3 text-center">
                <h3 class="text-xl font-light">Kvlarya</h3>
              </div>
            </div>
            <div class="p-6 bg-gray-50">
              <h4 class="text-xl font-light text-center text-gray-700 mb-4">HOTEL</h4>
              <p class="text-gray-600 text-center text-sm mb-6">Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.</p>
              <div class="text-center">
                <a href="#" class="text-teal-600 hover:text-teal-700">View</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section> --}}

    <!-- Promo berlangsung Section -->
    <section class="section-dark">
      <div class="section-container">
        <div class="section-title">
          <h2>Promo Berlangsung</h2>
          <div class="divider">
            <div class="divider-line"></div>
            <p class="divider-text">Special Offers</p>
            <div class="divider-line"></div>
          </div>
        </div>

        <div class="promo-slider relative">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Promo Card 1 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md">
              <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Screenshot%202025-04-11%20at%2001.38.16-ONQfSyRpYU314aVuFnhY5tCbMSbnlQ.png" alt="Rukita x Grab Promo" class="w-full h-48 object-cover">
              <div class="p-4">
                <div class="flex items-center mb-2">
                  <span class="bg-yellow-400 text-xs px-2 py-1 rounded-full text-gray-800 font-medium">Hemat 90%</span>
                </div>
                <h3 class="font-medium text-gray-800 mb-1">Hemat Waktu Hemat Ongkos</h3>
                <p class="text-gray-600 text-sm">Pakai Grab Hemat</p>
              </div>
            </div>

            <!-- Promo Card 2 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md">
              <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Screenshot%202025-04-11%20at%2001.38.16-ONQfSyRpYU314aVuFnhY5tCbMSbnlQ.png" alt="Rukita x Prabu Promo" class="w-full h-48 object-cover">
              <div class="p-4">
                <div class="flex items-center mb-2">
                  <span class="bg-yellow-400 text-xs px-2 py-1 rounded-full text-gray-800 font-medium">Diskon 100rb</span>
                </div>
                <h3 class="font-medium text-gray-800 mb-1">Dompet tetep anteng beli sepatu ganteng</h3>
                <p class="text-gray-600 text-sm">Nikmati diskon</p>
              </div>
            </div>

            <!-- Promo Card 3 -->
            <div class="bg-white rounded-lg overflow-hidden shadow-md">
              <img src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/Screenshot%202025-04-11%20at%2001.38.16-ONQfSyRpYU314aVuFnhY5tCbMSbnlQ.png" alt="Rukita x Primecare Promo" class="w-full h-48 object-cover">
              <div class="p-4">
                <div class="flex items-center mb-2">
                  <span class="bg-yellow-400 text-xs px-2 py-1 rounded-full text-gray-800 font-medium">499ribu</span>
                </div>
                <h3 class="font-medium text-gray-800 mb-1">Jadi gak gampang sakit pake promo vaksin</h3>
                <p class="text-gray-600 text-sm">Vaksin Flu 560ribu</p>
              </div>
            </div>
          </div>

          <!-- Slider Navigation Dots -->
          <div class="promo-nav-dots">
            <div class="nav-dot active"></div>
            <div class="nav-dot"></div>
            <div class="nav-dot"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Area Kost Coliving Terpopuler Section -->
    <section class="section-light">
      <div class="section-container">
        <div class="section-title">
          <h2>Area Kost & Coliving Terpopuler</h2>
          <div class="divider">
            <div class="divider-line"></div>
            <p class="divider-text">Popular Areas</p>
            <div class="divider-line"></div>
          </div>
        </div>

        <div class="mb-6">
          <!-- Area Tabs -->
          <div class="flex border-b border-gray-200">
            <button class="area-tab-trigger px-6 py-3 text-sm font-medium text-teal-600 border-b-2 border-teal-600" data-tab="jakarta">
              Jakarta
            </button>
            <button class="area-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="tangerang">
              Tangerang
            </button>
            <button class="area-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="bogor">
              Bogor
            </button>
            <button class="area-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="depok">
              Depok
            </button>
            <button class="area-tab-trigger px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="bekasi">
              Bekasi
            </button>
          </div>

          <!-- Area Content -->
          <div class="area-tab-contents mt-6">
            <!-- Jakarta Tab -->
            <div class="area-tab-content active" data-tab="jakarta">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Area Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative h-48">
                    <img src="images/assets/jakarta.jpg" alt="Jakarta Area" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                      <h3 class="text-xl font-medium">Jakarta Selatan</h3>
                      <p class="text-sm">50+ properties available</p>
                    </div>
                  </div>
                </div>
                <!-- More area cards for Jakarta -->
              </div>
            </div>

            <!-- Tangerang Tab -->
            <div class="area-tab-content hidden" data-tab="tangerang">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Area Card -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                  <div class="relative h-48">
                    <img src="images/assets/tangerang.jpg" alt="Tangerang Area" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
                    <div class="absolute bottom-4 left-4 text-white">
                      <h3 class="text-xl font-medium">BSD City</h3>
                      <p class="text-sm">30+ properties available</p>
                    </div>
                  </div>
                </div>
                <!-- More area cards for Tangerang -->
              </div>
            </div>

            <!-- Add similar blocks for Bogor, Depok, and Bekasi -->
          </div>
        </div>
      </div>
    </section>

    <!-- Featured Properties Section -->
    <section class="py-12 px-4 bg-[#f5f2ea]">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
          <h2 class="text-3xl font-light text-gray-800 mb-2">Featured Properties</h2>
          <div class="flex items-center justify-center">
            <div class="w-12 h-px bg-gray-300"></div>
            <p class="mx-4 text-teal-600 italic">Discover Our Collection</p>
            <div class="w-12 h-px bg-gray-300"></div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div class="relative rounded-lg overflow-hidden group">
            <img src="images/assets/0e127752-6073-4445-89cc-e9f47f7122f8.jpg"
                 alt="Japanese Style Interior"
                 class="w-full h-[400px] object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 gradient-overlay"></div>
            <div class="absolute bottom-4 left-4">
              <button class="bg-transparent border border-white text-white px-4 py-2 rounded hover:bg-white/20 transition flex items-center">
                BOOK NOW
                <i class="fas fa-arrow-right ml-2"></i>
              </button>
            </div>
          </div>

          <div class="relative rounded-lg overflow-hidden group">
            <img src="images/assets/Modern Japanese House_ Minimalist and Harmonious - Quiet Minimal.jpg"
                 alt="Townhouses"
                 class="w-full h-[400px] object-cover transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 gradient-overlay"></div>
            <div class="absolute bottom-4 left-4">
              <button class="bg-transparent border border-white text-white px-4 py-2 rounded hover:bg-white/20 transition flex items-center">
                BOOK NOW
                <i class="fas fa-arrow-right ml-2"></i>
              </button>
            </div>
          </div>
        </div>

        <div class="relative rounded-lg overflow-hidden group">
          <img src="images/assets/kos.jpg"
               alt="Mountain Cabin"
               class="w-full h-[500px] object-cover transition-transform duration-500 group-hover:scale-105">
          <div class="absolute inset-0 gradient-overlay"></div>
          <div class="absolute bottom-4 left-4">
            <button class="bg-transparent border border-white text-white px-4 py-2 rounded hover:bg-white/20 transition flex items-center">
              BOOK NOW
              <i class="fas fa-arrow-right ml-2"></i>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Special Offers Section -->
    <section class="py-12 px-4 bg-white">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-10">
          <h2 class="text-3xl font-light text-gray-800 mb-2">Special Offers</h2>
          <div class="flex items-center justify-center">
            <div class="w-12 h-px bg-gray-300"></div>
            <p class="mx-4 text-teal-600 italic">Trendy & Serene</p>
            <div class="w-12 h-px bg-gray-300"></div>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- The Oqua Spa -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 overflow-hidden">
              <img src="images/assets/adheesha-paranagama-kOYh8C_xLUQ-unsplash.jpg"
                   alt="The Oqua Spa"
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-800">THE OQUA SPA</h3>
              <p class="text-gray-500 text-sm mb-2">A journey of wellness and relaxation</p>
              <p class="text-gray-600 text-sm mb-10">
                The Oqua Spa at the newest luxury hotel in Mykonos is a 500 sqm heaven of wellness and relaxation.
              </p>
              <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">LEARN MORE</button>
            </div>
          </div>

          <!-- Lafs Restaurant -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 overflow-hidden">
              <img src="images/assets/Mykonos_Riviera_11-758x900.jpg.png"
                   alt="Lafs Restaurant"
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-800">LAFS RESTAURANT</h3>
              <p class="text-gray-500 text-sm mb-2">Waterfront dining under the stars</p>
              <p class="text-gray-600 text-sm mb-10">
                A new, sensational gastronomic concept in Mykonos with tradition inspired, seafood-based
                contemporary greek cuisine.
              </p>
              <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">LEARN MORE</button>
            </div>
          </div>

          <!-- Pool Club -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 overflow-hidden">
              <img src="images/assets/LAVEER-POOLSUITE-DINNER-DURING-SUNSET-758x900.jpg.png"
                   alt="Pool Club"
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-800">POOL CLUB</h3>
              <p class="text-gray-500 text-sm mb-2">An all day dining spot</p>
              <p class="text-gray-600 text-sm mb-10">
                Start with a healthy breakfast, served at your leisure until 11:30 am. Choose from an extensive
                casual all-day menu.
              </p>
              <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">LEARN MORE</button>
            </div>
          </div>

          <!-- Private BBQ -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 overflow-hidden">
              <img src="images/assets/LAFS-RESTAURANT-OUTDOOR-VERANDA-1-1-758x900.jpg.png"
                   alt="Private BBQ"
                   class="w-full h-full object-cover">
            </div>
            <div class="p-4">
              <h3 class="text-lg font-medium text-gray-800">PRIVATE BBQ</h3>
              <p class="text-gray-500 text-sm mb-2">Dine under Mykonos starlit sky</p>
              <p class="text-gray-600 text-sm mb-10">
                The Suites with their beautiful outdoor barbecue setup are ideal for personalized BBQ experience.
              </p>
              <button class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">LEARN MORE</button>
            </div>
          </div>
        </div>

        <!-- Pagination Dots -->
        <div class="flex justify-center mt-8 space-x-2">
          <div class="w-8 h-8 rounded-full bg-red-500 text-white flex items-center justify-center">1</div>
          <div class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center">2</div>
        </div>
      </div>
    </section>

    <!-- Liu House Section -->
    <section class="py-12 bg-[#f5f2ea]">
      <div class="max-w-7xl mx-auto">
        <div class="bg-white rounded-lg overflow-hidden shadow-md">
          <div class="flex flex-col lg:flex-row">
            <!-- Left Side - Property Image -->
            <div class="w-full lg:w-2/3">
              <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0012.jpg"
                   alt="Liu House West Jakarta"
                   class="w-full h-full object-cover">
            </div>

            <!-- Right Side - Property Details -->
            <div class="w-full lg:w-1/3 p-8">
              <h2 class="text-2xl font-light text-gray-800 mb-1">LIU HOUSE WEST JAKARTA</h2>
              <p class="text-gray-500 italic mb-4">Secret 3 Bedroom</p>

              <p class="text-gray-600 mb-6 text-sm">
                Gaze at the perfect sea-phony of blue and emerald green from the privacy of the stunning 125 square
                meter Secret Three Bedroom Pool Maisonette and listen to the wind audaciously, yet seductively
                humming in your ears.
              </p>

              <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Interior Image -->
                <div class="overflow-hidden rounded-lg">
                  <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0006.jpg"
                       alt="Liu House Interior"
                       class="w-full h-full object-cover">
                </div>

                <!-- Price Tag -->
                <div class="bg-teal-600 text-white p-4 rounded-lg flex flex-col justify-center items-center">
                  <p class="text-xs mb-1">Starting from</p>
                  <p class="text-xl font-light">Rp 1.500.000</p>
                  <p class="mt-1 text-xs">ULIN MAHONI</p>
                </div>
              </div>

              <button class="w-full bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded">BOOK NOW</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-12 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
      <div>
        <h4 class="text-xl font-medium mb-4">Ulin Mahoni</h4>
        <p class="text-gray-400 text-sm">
          Luxury living redefined for those who appreciate the finer things in life.
        </p>
      </div>

      <div>
        <h4 class="text-xl font-medium mb-4">Quick Links</h4>
        <ul class="space-y-2 text-gray-400 text-sm">
          <li>
            <a href="#" class="hover:text-white transition">
              Home
            </a>
          </li>
          <li>
            <a href="#" class="hover:text-white transition">
              About Us
            </a>
          </li>
          <li>
            <a href="#" class="hover:text-white transition">
              Rooms
            </a>
          </li>
          <li>
            <a href="#" class="hover:text-white transition">
              Facilities
            </a>
          </li>
        </ul>
      </div>

      <div>
        <h4 class="text-xl font-medium mb-4">Contact</h4>
        <ul class="space-y-2 text-gray-400 text-sm">
          <li>Jl. Luxury Boulevard No. 123</li>
          <li>+62 123 4567 890</li>
          <li>info@ulinmahoni.com</li>
        </ul>
      </div>

      <div>
        <h4 class="text-xl font-medium mb-4">Follow Us</h4>
        <div class="flex space-x-4">
          <a href="#" class="text-gray-400 hover:text-white transition">
            <div class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center">F</div>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition">
            <div class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center">I</div>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition">
            <div class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center">T</div>
          </a>
          <a href="#" class="text-gray-400 hover:text-white transition">
            <div class="w-8 h-8 rounded-full border border-gray-600 flex items-center justify-center">Y</div>
          </a>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
      <p>&copy; 2025 Ulin Mahoni. All rights reserved.</p>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Property tabs functionality
        const propertyTriggers = document.querySelectorAll('.property-tab-trigger');
        const propertyContents = document.querySelectorAll('.property-tab-content');

        propertyTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const tabName = trigger.getAttribute('data-tab');
                
                // Update trigger styles
                propertyTriggers.forEach(t => {
                    t.classList.remove('text-teal-600', 'border-b-2', 'border-teal-600');
                    t.classList.add('text-gray-500');
                });
                trigger.classList.remove('text-gray-500');
                trigger.classList.add('text-teal-600', 'border-b-2', 'border-teal-600');
                
                // Update content visibility
                propertyContents.forEach(content => {
                    if (content.getAttribute('data-tab') === tabName) {
                        content.classList.remove('hidden');
                        content.classList.add('active');
                    } else {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    }
                });
            });
        });

        // Area tabs functionality
        const areaTriggers = document.querySelectorAll('.area-tab-trigger');
        const areaContents = document.querySelectorAll('.area-tab-content');

        areaTriggers.forEach(trigger => {
            trigger.addEventListener('click', () => {
                const tabName = trigger.getAttribute('data-tab');
                
                // Update trigger styles
                areaTriggers.forEach(t => {
                    t.classList.remove('text-teal-600', 'border-b-2', 'border-teal-600');
                    t.classList.add('text-gray-500');
                });
                trigger.classList.remove('text-gray-500');
                trigger.classList.add('text-teal-600', 'border-b-2', 'border-teal-600');
                
                // Update content visibility
                areaContents.forEach(content => {
                    if (content.getAttribute('data-tab') === tabName) {
                        content.classList.remove('hidden');
                        content.classList.add('active');
                    } else {
                        content.classList.add('hidden');
                        content.classList.remove('active');
                    }
                });
            });
        });

        // Video functionality (keeping existing code)
        const video = document.getElementById('heroVideo');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const playPauseIcon = document.getElementById('playPauseIcon');
        
        if (video && playPauseBtn && playPauseIcon) {
            let isPlaying = true;

            video.play().catch(error => {
                console.error("Video autoplay failed:", error);
                isPlaying = false;
                playPauseIcon.classList.remove('fa-pause');
                playPauseIcon.classList.add('fa-play');
            });

            playPauseBtn.addEventListener('click', function() {
                if (isPlaying) {
                    video.pause();
                    playPauseIcon.classList.remove('fa-pause');
                    playPauseIcon.classList.add('fa-play');
                } else {
                    video.play();
                    playPauseIcon.classList.remove('fa-play');
                    playPauseIcon.classList.add('fa-pause');
                }
                isPlaying = !isPlaying;
            });
        }

        // Header scroll functionality (keeping existing code)
        let lastScrollY = window.scrollY;
        const header = document.querySelector('.site-header');

        window.addEventListener('scroll', () => {
            if (lastScrollY < window.scrollY) {
                header.classList.add('header-hidden');
            } else {
                header.classList.remove('header-hidden');
            }
            lastScrollY = window.scrollY;
        });
    });
  </script>
</body>
</html>