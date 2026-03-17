<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ulin Mahoni - Coming Soon</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'DengXian', 'Noto Sans SC', 'Microsoft YaHei', sans-serif;
        }
        
        .coming-soon-overlay {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
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
                            <button type="button" class="inline-flex justify-center w-full border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none" id="options-menu" aria-haspopup="true" aria-expanded="true" onclick="toggleDropdown()">
                                EN
                                <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                        <div id="dropdownMenu" class="hidden z-10 mt-2 w-56 shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="?lang=en" class="text-gray-700 block px-4 py-2 text-sm">EN</a>
                                <a href="?lang=in" class="text-gray-700 block px-4 py-2 text-sm">IN</a>
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

    <main class="flex-1">
        <!-- Coming Soon Section -->
        <div class="relative h-screen">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="images/assets/foto_project_ulin_mahoni/RENDER HALUS PROJECT BAPAK LIU KOS BOGOR_pages-to-jpg-0012.jpg" 
                     class="w-full h-full object-cover" alt="Background">
                <div class="absolute inset-0 coming-soon-overlay"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 h-full flex flex-col items-center justify-center text-white px-4">
                <h1 class="text-6xl font-light mb-4">
                    <span class="text-red-400">COMING</span> 
                    <span class="text-green-400">SOON</span>
                </h1>
                <p class="text-xl font-light mb-8 text-center max-w-2xl">
                    We're working hard to bring you something amazing. Stay tuned for updates!
                </p>
                
                <!-- Countdown Timer -->
                {{-- <div class="grid grid-cols-4 gap-8 text-center mb-12">
                    <div class="bg-black bg-opacity-50 p-6 rounded-lg">
                        <span class="block text-4xl font-light" id="days">00</span>
                        <span class="text-sm">Days</span>
                    </div>
                    <div class="bg-black bg-opacity-50 p-6 rounded-lg">
                        <span class="block text-4xl font-light" id="hours">00</span>
                        <span class="text-sm">Hours</span>
                    </div>
                    <div class="bg-black bg-opacity-50 p-6 rounded-lg">
                        <span class="block text-4xl font-light" id="minutes">00</span>
                        <span class="text-sm">Minutes</span>
                    </div>
                    <div class="bg-black bg-opacity-50 p-6 rounded-lg">
                        <span class="block text-4xl font-light" id="seconds">00</span>
                        <span class="text-sm">Seconds</span>
                    </div>
                </div> --}}

                <!-- Notification Form -->
                <div class="w-full max-w-md">
                    <form class="flex gap-4">
                        <input type="email" 
                               placeholder="Enter your email for updates" 
                               class="flex-1 px-4 py-2 bg-white bg-opacity-20 text-white placeholder-gray-300 border border-white border-opacity-30 focus:outline-none focus:border-opacity-50">
                        <button type="submit" class="bg-green-800 text-white px-6 py-2 hover:bg-green-700 transition">
                            Notify Me
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Dropdown Toggle
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

        // Countdown Timer
        const launchDate = new Date('2024-12-31T00:00:00').getTime();

        function updateTimer() {
            const now = new Date().getTime();
            const distance = launchDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById('days').textContent = String(days).padStart(2, '0');
            document.getElementById('hours').textContent = String(hours).padStart(2, '0');
            document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
            document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
        }

        setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>
</html>
