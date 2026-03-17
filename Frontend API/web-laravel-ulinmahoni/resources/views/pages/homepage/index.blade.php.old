<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-white text-gray-900">
    <!-- Add this near the top of your blade template for debugging -->
    @if(isset($banners))
        <script>
            console.log('Banner Data:', @json($banners));
        </script>
    @endif

    @if(isset($error))
        <script>
            console.error('Error:', @json($error));
        </script>
    @endif

    <!-- Add this near the top of your blade template, after the <body> tag -->
    @if(isset($debug))
        <script>
            console.group('API Debug Information');
            console.log('Type:', @json($debug['type']));
            console.log('Data:', @json($debug['data']));
            console.groupEnd();

            @if($debug['type'] === 'error' || $debug['type'] === 'exception')
                console.error('Error Details:', @json($debug['data']));
            @endif
        </script>
    @endif

    <!-- Topbar -->
    <header class="bg-white text-gray-900 p-4 fixed w-full top-0 left-0 shadow-md z-10 flex justify-between items-center h-16">
        <div class="flex items-center">
            <button class="border border-gray-300 p-2 mr-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class="ml-2 text-gray-600">MENU</span>
            </button>
        </div>
        
        <div class="flex items-center justify-center">
            <a href="/homepage">
                <img src="images/assets/ulinmahoni-logo.svg" alt="Ulin Mahoni Logo" width="45">
            </a>
        </div>
        
        <nav>
            <ul class="flex space-x-4">
                <li>
                    <div class="relative inline-block text-left">
                        <div>
                            <button type="button" 
                                    class="inline-flex justify-center w-full border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" id="options-menu" aria-haspopup="true" aria-expanded="true" onclick="toggleDropdown()">
                                EN
                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div id="dropdownMenu" class="hidden z-10 mt-2 w-56 shadow-lg bg-white ring-1 ring-black ring-opacity-5" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            <div class="py-1" role="none">
                                <a href="?lang=en" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">EN</a>
                                <a href="?lang=in" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem">IN</a>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <button class="bg-green-800 text-white px-4 py-2">
                        BOOK ONLINE
                    </button>
                </li>
            </ul>
        </nav>
    </header>

    <main class="flex-1 mt-16">
        <!-- Hero Section -->
        <div class="relative w-full" style="position: relative; width: 100%; padding-top: calc(9 / 32 * 100%);">
            <video id="heroVideo" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                <source src="videos/assets/hero-video.mp4" type="video/mp4">
                Browser Anda tidak mendukung video.
            </video>

            <!-- Overlay with text -->
            <div class="absolute inset-0 flex flex-col justify-end items-start p-8 text-white hero-overlay" 
                 style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; justify-content: flex-end; align-items: flex-start; padding: 2rem; color: white;">
                <h1 class="text-2xl font-light mb-2" style="font-size: 1.5rem; font-weight: 300; margin-bottom: 0.5rem;">
                    A safe and harmonious environment
                </h1>
                <p class="text-base font-light" style="font-size: 1rem; font-weight: 300;">#UlinMahoni</p>
            </div>

            <!-- Play/Pause Button -->
            <button id="playPauseBtn" 
                    class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white bg-opacity-50 rounded-full w-12 h-12 flex items-center justify-center" 
                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: rgba(255,255,255,0.5); border-radius: 50%; width: 3rem; height: 3rem; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-pause text-white text-lg" style="color: white; font-size: 1.125rem;"></i>
            </button>
        </div>

       <!-- Luxoria Living Section -->
        <section class="flex flex-col md:flex-row">
            <!-- Left Image -->
            <div class="w-full md:w-1/2 h-64 md:h-96 overflow-hidden">
                <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0008.jpg" 
                    alt="Luxury Bedroom" 
                    class="w-full h-full object-cover">
                </div>

            <!-- Right Content -->
            <div class="w-full md:w-1/2 flex flex-col justify-center items-center p-12 bg-white">
                <h2 class="text-5xl font-light mb-2">
                    <span class="text-red-600">ULIN</span>
                    <span class="text-green-700">MAHONI</span>
                    </h2>
                <h3 class="text-xl text-gray-500 font-light tracking-wider mb-8">LUXÓRIA LIVING</h3>
                
                <p class="text-gray-600 font-light text-center max-w-xl leading-relaxed mb-8">
                        Ulin Mahoni adalah hunian eksklusif yang menghadirkan kemewahan, kenyamanan, dan ketenangan
                        dalam satu tempat. Dengan desain interior yang elegan dan fasilitas premium, setiap sudutnya
                        dirancang untuk memberikan pengalaman tinggal yang istimewa.
                </p>
                
                <button class="border border-gray-300 text-gray-600 px-8 py-2 hover:bg-gray-50 transition">
                    View More
                </button>
            </div>
        </section>

        <!-- Booking Section -->
        <section class="booking-form bg-gray-50 py-8 px-4">
            <div class="max-w-6xl mx-auto flex flex-wrap justify-center items-center gap-4">
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="icon mr-2">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Check-Out</label>
                        <input type="date" class="border-0 focus:ring-0 p-0 text-sm">
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="icon mr-2">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Check-In</label>
                        <input type="date" class="border-0 focus:ring-0 p-0 text-sm">
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="icon mr-2">
                        <i class="fas fa-bed"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Type</label>
                        <select class="border-0 focus:ring-0 p-0 text-sm">
                            <option>Standard Room</option>
                            <option>Deluxe Room</option>
                            <option>Suite</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="icon mr-2">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Location</label>
                        <input type="text" placeholder="Any location" class="border-0 focus:ring-0 p-0 text-sm">
                    </div>
                </div>

                <button class="bg-green-800 text-white px-6 py-3 rounded shadow hover:bg-green-900 transition">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </section>

        <!-- Living Space Section -->
        <section class="living-space py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-light text-gray-700 mb-2">Living Space</h2>
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-px bg-gray-300"></div>
                        <p class="mx-4 text-red-700 italic">Exclusive & Cozy</p>
                        <div class="w-12 h-px bg-gray-300"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Rexucia Card -->
                    <div class="bg-white shadow-md overflow-hidden">
                        <div class="relative">
                            <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0006.jpg" alt="Rexucia Room" class="w-full h-80 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-red-800 text-white p-3 text-center">
                                <h3 class="text-xl font-light">Rexucia</h3>
                            </div>
                        </div>
                        <div class="p-8 bg-gray-50">
                            <h4 class="text-2xl font-light text-center text-gray-600 mb-4">HOUSE & ROOM</h4>
                            <p class="text-gray-600 text-center mb-6">
                                Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.
                            </p>
                            <div class="text-center">
                                <a href="/houses" class="text-green-700 hover:text-green-800 transition">View</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Royal Mediteranian Card -->
                    <div class="bg-white shadow-md overflow-hidden">
                        <div class="relative">
                            <img src="images/assets/apt.jpg" alt="Royal Mediteranian Building" class="w-full h-80 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-green-800 text-white p-3 text-center">
                                <h3 class="text-xl font-light">Royal Mediteranian</h3>
                            </div>
                        </div>
                        <div class="p-8 bg-gray-50">
                            <h4 class="text-2xl font-light text-center text-gray-600 mb-4">APARTMENT</h4>
                            <p class="text-gray-600 text-center mb-6">
                                Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.
                            </p>
                            <div class="text-center">
                                {{-- <a href="#" class="text-green-700 hover:text-green-800 transition">View</a> --}}
                                <a href="/coming-soon" class="text-green-700 hover:text-green-800 transition">View</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Xilonen Card -->
                    <div class="bg-white shadow-md overflow-hidden">
                        <div class="relative">
                            <img src="images/assets/villa.jpg" alt="Xilonen Villa" class="w-full h-80 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-green-800 text-white p-3 text-center">
                                <h3 class="text-xl font-light">Xilonen</h3>
                            </div>
                        </div>
                        <div class="p-8 bg-gray-50">
                            <h4 class="text-2xl font-light text-center text-gray-600 mb-4">VILLA</h4>
                            <p class="text-gray-600 text-center mb-6">
                                Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.
                            </p>
                            <div class="text-center">
                                {{-- <a href="#" class="text-green-700 hover:text-green-800 transition">View</a> --}}
                                <a href="/coming-soon" class="text-green-700 hover:text-green-800 transition">View</a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kvlarya Card -->
                    <div class="bg-white shadow-md overflow-hidden">
                        <div class="relative">
                            <img src="images/assets/hotel.jpg" alt="Kvlarya Hotel Lobby" class="w-full h-80 object-cover">
                            <div class="absolute bottom-0 left-0 right-0 bg-red-800 text-white p-3 text-center">
                                <h3 class="text-xl font-light">Kvlarya</h3>
                            </div>
                        </div>
                        <div class="p-8 bg-gray-50">
                            <h4 class="text-2xl font-light text-center text-gray-600 mb-4">HOTEL</h4>
                            <p class="text-gray-600 text-center mb-6">
                                Mykonos Riviera Hotel & Spa offers a large selection of suites, most with heated pools, scintillating sunset views, and utmost privacy.
                            </p>
                            <div class="text-center">
                                {{-- <a href="#" class="text-green-700 hover:text-green-800 transition">View</a> --}}
                                <a href="/coming-soon" class="text-green-700 hover:text-green-800 transition">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Properties Section -->
        <section class="featured-properties py-16 px-4 bg-gray-50">
            <div class="max-w-full mx-auto" style="max-width: 1912px;">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-light text-gray-700 mb-2">Featured Properties</h2>
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-px bg-gray-300"></div>
                        <p class="mx-4 text-green-700 italic">Discover Our Collection</p>
                        <div class="w-12 h-px bg-gray-300"></div>
                    </div>
                </div>
                
                <div class="flex flex-col items-start justify-center gap-6" style="height: 1900px;">
                    <!-- Side by Side Properties (Top Row) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
                        <!-- Left Property -->
                        <div class="featured-property h-64 md:h-128 overflow-hidden shadow-lg relative" style="height: 42rem;">
                            <img src="images/assets/0e127752-6073-4445-89cc-e9f47f7122f8.jpg" 
                                alt="Japanese Style Interior" 
                                class="w-full h-full object-cover">
                            <button class="absolute bottom-4 left-4 bg-transparent border border-white px-6 py-2 text-sm font-bold text-white hover:bg-white hover:bg-opacity-20 transition flex items-center">
                            BOOK NOW
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                        </div>
                        
                        <!-- Right Property -->
                        <div class="featured-property h-64 md:h-128 overflow-hidden shadow-lg relative" style="height: 42rem;">
                            <img src="images/assets/Modern Japanese House_ Minimalist and Harmonious - Quiet Minimal.jpg" 
                                alt="Townhouses" 
                                class="w-full h-full object-cover">
                            <button class="absolute bottom-4 left-4 bg-transparent border border-white px-6 py-2 text-sm font-bold text-white hover:bg-white hover:bg-opacity-20 transition flex items-center">
                            BOOK NOW
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                        </div>
                    </div>
                    
                    <!-- Large Feature Property (Bottom) -->
                    <div class="featured-property w-full overflow-hidden shadow-lg relative" style="height: calc(1900px - 384px - 1.5rem);">
                        <img src="images/assets/kos.jpg" 
                            alt="Mountain Cabin" 
                            class="w-full h-full object-cover">
                        <button class="absolute bottom-4 left-4 bg-transparent border border-white px-6 py-2 text-sm font-bold text-white hover:bg-white hover:bg-opacity-20 transition flex items-center">
                            BOOK NOW
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Special Offers Section -->
        <section class="special-offers py-16 px-4">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-light text-gray-700 mb-2">Special Offers</h2>
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-px bg-gray-300"></div>
                        <p class="mx-4 text-green-700 italic">Trendy & Serene</p>
                        <div class="w-12 h-px bg-gray-300"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- The Oqua Spa -->
                    <div class="bg-white shadow-md overflow-hidden relative">
                        <div class="h-64 overflow-hidden">
                            <img src="images/assets/adheesha-paranagama-kOYh8C_xLUQ-unsplash.jpg" alt="The Oqua Spa"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-medium text-gray-800 mb-1">THE OQUA SPA</h3>
                            <p class="text-gray-600 text-sm mb-4">A journey of wellness and relaxation</p>
                            <p class="text-gray-600 mb-14">
                                The Oqua Spa at the newest luxury hotel in Mykonos is a 500 sqm heaven of wellness and
                                relaxation.
                            </p>
                        </div>
                        <button class="absolute bottom-0 left-0 m-6 bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded">
                            LEARN MORE
                        </button>
                    </div>

                    <!-- Lafs Restaurant -->
                    <div class="bg-white shadow-md overflow-hidden relative">
                        <div class="h-64 overflow-hidden">
                            <img src="images/assets/Mykonos_Riviera_11-758x900.jpg.png" alt="Lafs Restaurant"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-medium text-gray-800 mb-1">LAFS RESTAURANT</h3>
                            <p class="text-gray-600 text-sm mb-4">Waterfront dining under the stars</p>
                            <p class="text-gray-600 mb-14">
                                A new, sensational gastronomic concept in Mykonos with tradition inspired, seafood-based
                                contemporary greek cuisine.
                            </p>
                        </div>
                        <button class="absolute bottom-0 left-0 m-6 bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded">
                            LEARN MORE
                        </button>
                    </div>

                    <!-- Pool Club -->
                    <div class="bg-white shadow-md overflow-hidden relative">
                        <div class="h-64 overflow-hidden">
                            <img src="images/assets/LAVEER-POOLSUITE-DINNER-DURING-SUNSET-758x900.jpg.png" alt="Pool Club"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-medium text-gray-800 mb-1">POOL CLUB</h3>
                            <p class="text-gray-600 text-sm mb-4">An all day dining spot</p>
                            <p class="text-gray-600 mb-14"> <!-- Increased margin here -->
                                Start with a healthy breakfast, served at your leisure until 11:30 am. Choose from an extensive casual all-day menu filled with Greek-traditional inspired bites to tempt all.
                            </p>
                        </div>

                        <button class="absolute bottom-0 left-0 m-6 bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded">
                            LEARN MORE
                        </button>
                    </div>


                    <!-- Private BBQ -->
                    <div class="bg-white shadow-md overflow-hidden relative">
                        <div class="h-64 overflow-hidden">
                            <img src="images/assets/LAFS-RESTAURANT-OUTDOOR-VERANDA-1-1-758x900.jpg.png" alt="Private BBQ"
                                class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-medium text-gray-800 mb-1">PRIVATE BBQ</h3>
                            <p class="text-gray-600 text-sm mb-4">Dine under Mykonos starlit sky</p>
                            <p class="text-gray-600 mb-14">
                                The Suites with their beautiful outdoor barbecue setup are ideal for personalized BBQ
                                experience.
                            </p>
                        </div>
                        <button class="absolute bottom-0 left-0 m-6 bg-green-700 hover:bg-green-800 text-white py-2 px-4 rounded">
                            LEARN MORE
                        </button>
                    </div>
                </div>

                <!-- Pagination Dots -->
                <div style="display: flex; justify-content: center; margin-top: 2rem;">
                    <div style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 0.25rem; cursor: pointer; transition: all 0.3s ease; background-color: #f87171; color: white; border-color: #f87171;">
                        1
                    </div>
                    <div style="width: 30px; height: 30px; border-radius: 50%; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; margin: 0 0.25rem; cursor: pointer; transition: all 0.3s ease;">
                        2
                    </div>
                </div>

            </div>
        </section>

        
        <!-- Liu House West Jakarta Section -->
        <section class="liu-house py-16 bg-white">
            <div class="max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch">
                <!-- Left Side - Property Image -->
                <div class="w-full lg:w-2/3 h-64 lg:h-auto overflow-hidden"> <!-- Adjusted height -->
                    <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0012.jpg" 
                    alt="Liu House West Jakarta" class="w-full h-full object-cover" style="height: 30rem;">
                </div>
                
                <!-- Right Side - Property Details -->
                <div class="w-full lg:w-1/3 p-8 flex flex-col justify-center">
                    <h2 class="text-3xl font-light text-gray-700 mb-2">LIU HOUSE WEST JAKARTA</h2>
                    <p class="text-gray-500 italic mb-6">Secret 3 Bedroom</p>
                    
                    <p class="text-gray-600 mb-8">
                        Gaze at the perfect sea-phony of blue and emerald green from the privacy of the stunning 125 square meter Secret Three Bedroom Pool Maisonette and listen to the wind audaciously, yet seductively humming in your ears.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <!-- Interior Image -->
                        <div class="h-48 overflow-hidden">
                            <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0006.jpg" alt="Liu House Interior" class="w-full h-full object-cover">
                        </div>
                        
                        <!-- Price Tag -->
                        <div class="bg-green-800 text-white p-4 flex flex-col justify-center items-center">
                            <p class="text-sm mb-2">Starting from</p>
                            <p class="text-3xl font-light">Rp 1.500.000</p>
                            <p class="mt-2 text-sm">ULIN MAHONI</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        <!-- Footer -->
        <footer class="bg-gray-800 text-white p-12 mt-8">
            <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h4 class="text-xl font-medium mb-4">Ulin Mahoni</h4>
                    <p class="text-gray-400">Luxury living redefined for those who appreciate the finer things in life.</p>
                </div>
                
                <div>
                    <h4 class="text-xl font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Home</a></li>
                        <li><a href="#" class="hover:text-white transition">About Us</a></li>
                        <li><a href="#" class="hover:text-white transition">Rooms</a></li>
                        <li><a href="#" class="hover:text-white transition">Facilities</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-xl font-medium mb-4">Contact</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><i class="fas fa-map-marker-alt mr-2"></i> Jl. Luxury Boulevard No. 123</li>
                        <li><i class="fas fa-phone mr-2"></i> +62 123 4567 890</li>
                        <li><i class="fas fa-envelope mr-2"></i> info@ulinmahoni.com</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-xl font-medium mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Ulin Mahoni. All rights reserved.</p>
            </div>
        </footer>
    </main>

</body>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const video = document.getElementById("heroVideo");
        const playPauseBtn = document.getElementById("playPauseBtn");

        // Atur teks tombol saat pertama kali dimuat
        playPauseBtn.textContent = "Play";

        // Event listener untuk tombol Play/Pause
        playPauseBtn.addEventListener("click", function() {
            if (video.paused) {
                video.play();
                playPauseBtn.textContent = "Pause";
            } else {
                video.pause();
                playPauseBtn.textContent = "Play";
            }
        });
    });

    function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const button = document.getElementById('options-menu');
            
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
    });
</script>

</html>
