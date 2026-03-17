<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni - Our Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'DengXian', 'Noto Sans SC', 'Microsoft YaHei', sans-serif;
        }
        
        .room-card {
            display: flex;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .room-image {
            width: 60%;
            height: 450px;
            object-fit: cover;
        }
        
        .room-details {
            width: 40%;
            padding: 2rem;
            background-color: #f9f9f9;
            position: relative;
        }
        
        .room-title {
            font-size: 1.75rem;
            font-weight: 300;
            color: #333;
            letter-spacing: 1px;
        }
        
        .room-subtitle {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .room-description {
            font-size: 0.9rem;
            line-height: 1.6;
            color: #666;
            margin-bottom: 2rem;
        }
        
        .room-price {
            font-size: 1.5rem;
            font-weight: 300;
            color: rgb(54, 53, 53);
            background-color: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1rem;
            position: absolute;
            top: 50%;
            left: 0;
            transform: translateY(-50%);
        }
        
        .room-thumbnail {
            width: 100%;
            height: 150px;
            object-fit: cover;
            position: absolute;
            bottom: 2.5rem;
            right: 0;
        }
        
        .room-type-label {
            position: absolute;
            bottom: 0;
            right: 0;
            background-color: #166534;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-button {
            border: 1px solid #e2e8f0;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            font-size: 0.875rem;
        }
        
        .book-button {
            background-color: #166534;
            color: white;
            padding: 0.5rem 1.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>

<body class="bg-white text-gray-900">
    <!-- Header -->
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

    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Room Listings -->
        <div class="room-listings">
            <!-- Alpha Room -->
            <div class="room-card">
                <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0012.jpg" alt="Alpha Room" class="room-image">
                
                <div class="room-details">
                    <h2 class="room-title">ALPHA ROOMS</h2>
                    <p class="room-subtitle">Secret 3 Bedroom</p>
                    
                    <p class="room-description">
                        Gaze at the perfect sea-phony of blue and emerald green from the 
                        privacy of the stunning 125 square meter Secret Three Bedroom Pool 
                        Maisonette and listen to the wind audaciously, yet seductively humming 
                        in your ears.
                    </p>
                    
                    <div class="room-price">
                        Rp.1.500.000
                    </div>
                    
                    <img src="images/assets/pics/WhatsApp Image 2025-02-20 at 14.30.46 (5).jpeg" alt="Alpha Room Dining Area" class="room-thumbnail">
                    
                    <div class="room-type-label">
                        ALPHA ROOMS
                    </div>
                </div>
            </div>
            
            <!-- Beta Room -->
            <div class="room-card">
                <img src="images/assets/foto_project_ulin_mahoni/bedroom-with-brick-wall.jpg" alt="Beta Room" class="room-image">
                
                <div class="room-details">
                    <h2 class="room-title">BETA ROOMS</h2>
                    <p class="room-subtitle">Secret 3 Bedroom</p>
                    
                    <p class="room-description">
                        Gaze at the perfect sea-phony of blue and emerald green from the 
                        privacy of the stunning 125 square meter Secret Three Bedroom Pool 
                        Maisonette and listen to the wind audaciously, yet seductively humming 
                        in your ears.
                    </p>
                    
                    <div class="room-price">
                        Rp.1.500.000
                    </div>
                    
                    <img src="images/assets/bathroom.jpg" alt="Beta Room Bathroom" class="room-thumbnail">
                    
                    <div class="room-type-label">
                        BETA ROOMS
                    </div>
                </div>
            </div>
            
            <!-- Gamma Room -->
            <div class="room-card">
                <img src="images/assets/housing1.jpg" alt="Gamma Room" class="room-image">
                
                <div class="room-details">
                    <h2 class="room-title">GAMMA ROOMS</h2>
                    <p class="room-subtitle">Secret 2 Bedroom</p>
                    
                    <p class="room-description">
                        Experience luxury in our intimate Secret Two Bedroom suite, featuring 
                        90 square meters of elegant design and modern amenities. Enjoy panoramic 
                        views and premium comfort in this exclusive accommodation option.
                    </p>
                    
                    <div class="room-price">
                        Rp.1.200.000
                    </div>
                    
                    <img src="images/assets/living-room.jpg" alt="Gamma Room Living Area" class="room-thumbnail">
                    
                    <div class="room-type-label">
                        GAMMA ROOMS
                    </div>
                </div>
            </div>
            
            <!-- Delta Room -->
            <div class="room-card">
                <img src="images/assets/ayana.jpg" alt="Delta Room" class="room-image">
                
                <div class="room-details">
                    <h2 class="room-title">DELTA ROOMS</h2>
                    <p class="room-subtitle">Secret 1 Bedroom</p>
                    
                    <p class="room-description">
                        Our Secret One Bedroom offers an intimate 65 square meter sanctuary 
                        with premium furnishings and a private balcony. Perfect for couples 
                        or solo travelers seeking a luxurious retreat with all modern comforts.
                    </p>
                    
                    <div class="room-price">
                        Rp.950.000
                    </div>
                    
                    <img src="images/assets/balcony.jpg" alt="Delta Room Balcony" class="room-thumbnail">
                    
                    <div class="room-type-label">
                        DELTA ROOMS
                    </div>
                </div>
            </div>
        </div>
    </main>

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

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownMenu');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('dropdownMenu');
            const button = document.getElementById('options-menu');
            
            if (!button?.contains(event.target) && !dropdown?.contains(event.target)) {
                dropdown?.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

