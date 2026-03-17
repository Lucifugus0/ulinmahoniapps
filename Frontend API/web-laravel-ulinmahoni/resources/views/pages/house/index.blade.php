<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni - Our Houses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'DengXian', 'Noto Sans SC', 'Microsoft YaHei', sans-serif;
        }
        
        .property-banner {
            position: relative;
            height: 80vh;
            background-size: cover;
            background-position: center;
            color: white;
        }
        
        .property-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.7), transparent);
            padding: 2rem;
        }
        
        .property-info-bar {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 0.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .property-info-item {
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }
        
        .cta-button {
            background-color: rgba(220, 38, 38, 0.9);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            background-color: rgba(185, 28, 28, 1);
        }
    </style>
</head>

<body class="bg-white text-gray-900">
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
                            <button type="button" class="inline-flex justify-center w-full border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-indigo-500" id="options-menu" aria-haspopup="true" aria-expanded="true" onclick="toggleDropdown()">
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
        <!-- Page Title -->
        <section class="bg-gray-100 py-12">
            <div class="max-w-7xl mx-auto px-4">
                <h1 class="text-4xl font-light text-center">
                    <span class="text-red-600">OUR</span> <span class="text-green-700">HOUSES</span>
                </h1>
                <p class="text-center text-gray-600 mt-4 max-w-3xl mx-auto">
                    Discover our exclusive selection of luxury accommodations designed for your comfort and relaxation.
                </p>
            </div>
        </section>

        <!-- Property Listings -->
        <section class="properties-section">
            <!-- LIU HOUSE WEST JAKARTA -->
            <div class="property-banner" 
            style="background-image: url('images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0012.jpg');">
                <div class="property-overlay">
                    <h2 class="text-3xl font-light mb-4">LIU HOUSE WEST JAKARTA</h2>
                    <a href="/houses/rooms" class="cta-button inline-block">See Our Rooms</a>
                </div>
                <div class="property-info-bar">
                    <div class="flex space-x-6">
                        <div class="property-info-item">
                            <span>Capacity:</span>
                            <span class="ml-2">7</span>
                        </div>
                        <div class="property-info-item">
                            <span>Size:</span>
                            <span class="ml-2">110m² / 1184ft²</span>
                        </div>
                        <div class="property-info-item">
                            <span>House</span>
                        </div>
                    </div>
                    <div class="property-info-item">
                        <span>Book Online From:</span>
                        <span class="ml-2 font-semibold">Rp.300,000</span>
                    </div>
                </div>
            </div>

            <!-- GRAND HOUSE SOUTH JAKARTA -->
            <div class="property-banner mt-8" style="background-image: url('images/assets/housing1.jpg'); background-position: bottom;">
                <div class="property-overlay">
                    <h2 class="text-3xl font-light mb-4">GRAND HOUSE SOUTH JAKARTA</h2>
                    <a href="/rooms/grand-house" class="cta-button inline-block">See Our Rooms</a>
                </div>
                <div class="property-info-bar">
                    <div class="flex space-x-6">
                        <div class="property-info-item">
                            <span>Capacity:</span>
                            <span class="ml-2">7</span>
                        </div>
                        <div class="property-info-item">
                            <span>Size:</span>
                            <span class="ml-2">110m² / 1184ft²</span>
                        </div>
                        <div class="property-info-item">
                            <span>House</span>
                        </div>
                    </div>
                    <div class="property-info-item">
                        <span>Book Online From:</span>
                        <span class="ml-2 font-semibold">Rp.300,000</span>
                    </div>
                </div>
            </div>

            <!-- LUXURY VILLA BALI -->
            <div class="property-banner mt-8" style="background-image: url('images/assets/ayana.jpg');">
                <div class="property-overlay">
                    <h2 class="text-3xl font-light mb-4">LUXURY VILLA BALI</h2>
                    <a href="/rooms/luxury-villa" class="cta-button inline-block">See Our Rooms</a>
                </div>
                <div class="property-info-bar">
                    <div class="flex space-x-6">
                        <div class="property-info-item">
                            <span>Capacity:</span>
                            <span class="ml-2">5</span>
                        </div>
                        <div class="property-info-item">
                            <span>Size:</span>
                            <span class="ml-2">95m² / 1022ft²</span>
                        </div>
                        <div class="property-info-item">
                            <span>Villa</span>
                        </div>
                    </div>
                    <div class="property-info-item">
                        <span>Book Online From:</span>
                        <span class="ml-2 font-semibold">Rp.450,000</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Section -->
        <section class="booking-form bg-gray-50 py-8 px-4 mt-8">
            <div class="max-w-6xl mx-auto flex flex-wrap justify-center items-center gap-4">
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="text-green-800 mr-2">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Check-Out</label>
                        <input type="date" class="border-0 focus:ring-0 p-0 text-sm">
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="text-green-800 mr-2">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Check-In</label>
                        <input type="date" class="border-0 focus:ring-0 p-0 text-sm">
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="text-green-800 mr-2">
                        <i class="fas fa-bed"></i>
                    </span>
                    <div>
                        <label class="block text-xs text-gray-500">Type</label>
                        <select class="border-0 focus:ring-0 p-0 text-sm">
                            <option>Any Type</option>
                            <option>House</option>
                            <option>Villa</option>
                            <option>Apartment</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center bg-white px-4 py-2 rounded shadow">
                    <span class="text-green-800 mr-2">
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
                        <li><a href="/" class="hover:text-white transition">Home</a></li>
                        <li><a href="/about" class="hover:text-white transition">About Us</a></li>
                        <li><a href="/rooms" class="hover:text-white transition">Rooms</a></li>
                        <li><a href="/contact" class="hover:text-white transition">Contact</a></li>
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

    <script>
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
</body>
</html>

