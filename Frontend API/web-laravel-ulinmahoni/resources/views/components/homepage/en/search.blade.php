<!-- Search Component -->
<section class="search-section">
    <div class="search-container">
        <div class="search-box">
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Property Types -->
                <div class="md:w-48 relative">
                    <i class="fas fa-building absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <select class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                        <option value="">Property Types</option>
                        <option value="house">House & Room</option>
                        <option value="apartment">Apartment</option>
                        <option value="villa">Villa</option>
                        <option value="hotel">Hotel</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>

                <!-- Rent Period -->
                <div class="md:w-48 relative">
                    <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <select class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent appearance-none bg-white transition-all duration-200">
                        <option value="">Rent Period</option>
                        <option value="daily">Daily</option>
                        <option value="monthly">Monthly</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                </div>

                <!-- Check-in Check-out Dates -->
                <div class="flex-1 flex gap-4">
                    <div class="w-1/2 relative">
                        <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" 
                            placeholder="Check-in" 
                            class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                    </div>
                    <div class="w-1/2 relative">
                        <i class="far fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="date" 
                            placeholder="Check-out" 
                            class="w-full pl-10 h-12 border border-gray-200 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all duration-200">
                    </div>
                </div>
                
                <div class="md:w-48">
                    <button class="w-full h-12 bg-teal-600 hover:bg-teal-700 text-white rounded-lg transition-all duration-200 flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        <span>Search Properties</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>