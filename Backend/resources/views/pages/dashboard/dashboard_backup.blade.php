<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Dashboard Banner -->
        <div class="relative bg-cover bg-center p-4 sm:p-6 rounded-xl overflow-hidden mb-8 shadow-lg"
            style="background-image: url('{{ asset('images/0fd3416c.jpeg') }}')">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-gray-900/70"></div>

            <!-- Content -->
            <div class="relative">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-2xl md:text-3xl text-white font-bold mb-1">
                            DASHBOARD FRONT DESK
                        </h1>
                        <p class="text-blue-100 font-medium">Selamat datang kembali, {{ Auth::user()->first_name }}
                            {{ Auth::user()->last_name }}</p>
                    </div>

                    <div class="mt-4 md:mt-0 flex items-center bg-white/10 backdrop-blur-sm rounded-lg p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-300 mr-2" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-white text-sm font-medium">Terakhir diperbarui:
                            {{ now()->format('d M, Y H:i') }}</span>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Confirm Booking (Upcoming) -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border-l-4 border-blue-300 hover:bg-white/15 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Konfirmasi Booking (Mendatang)</p>
                                <h3 class="text-white text-2xl font-bold mt-1">{{ $stats['upcoming'] }}</h3>
                                <p class="text-blue-200 text-xs mt-1">Segera check-in</p>
                            </div>
                            <div class="bg-blue-500/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-200" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Booking (Today) -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border-l-4 border-purple-300 hover:bg-white/15 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Konfirmasi Booking (Hari Ini)</p>
                                <h3 class="text-white text-2xl font-bold mt-1">{{ $stats['today'] }}</h3>
                                <p class="text-purple-200 text-xs mt-1">Kedatangan hari ini</p>
                            </div>
                            <div class="bg-purple-500/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-200" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Check-In -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border-l-4 border-green-300 hover:bg-white/15 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Check-In</p>
                                <h3 class="text-white text-2xl font-bold mt-1">{{ $stats['checkin'] }}</h3>
                                <p class="text-green-200 text-xs mt-1">Sedang menginap</p>
                            </div>
                            <div class="bg-green-500/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Check-Out -->
                    <div
                        class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border-l-4 border-yellow-300 hover:bg-white/15 transition-all duration-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-blue-100 text-sm font-medium">Check-Out</p>
                                <h3 class="text-white text-2xl font-bold mt-1">{{ $stats['checkout'] }}</h3>
                                <p class="text-yellow-200 text-xs mt-1">Jadwal Check-Out Hari Ini</p>
                            </div>
                            <div class="bg-yellow-500/20 p-2 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-200" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Check-out Section -->
                <div
                    class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden transition-all hover:shadow-lg">
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 17l-4 4m0 0l-4-4m4 4V3" />
                            </svg>
                            <h2 class="font-semibold text-gray-800 text-lg">Check-out Hari Ini</h2>
                        </div>
                        <a href="{{ route('checkin.index') }}"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                            Lihat Semua
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        @include('pages.bookings.checkin.partials.checkin_table', [
                            'checkOuts' => $checkOuts,
                            'per_page' => request('per_page', 4),
                            'type' => 'check-out',
                            'showStatus' => false,
                            'showActions' => false,
                        ])
                    </div>
                    <div class="px-6 py-3 bg-gray-50 text-sm text-gray-500 border-t border-gray-100">
                        Menampilkan {{ min(4, count($checkOuts)) }} dari {{ count($checkOuts) }} check-out mendatang
                    </div>
                </div>

                <!-- Check-in Section -->
                <div
                    class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden transition-all hover:shadow-lg">
                    <div
                        class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-green-50 to-teal-50">
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            <h2 class="font-semibold text-gray-800 text-lg">Check-in Hari Ini</h2>
                        </div>
                        <a href="{{ route('newReserv.index') }}"
                            class="text-sm font-medium text-green-600 hover:text-green-800 flex items-center">
                            Lihat Semua
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        @include('pages.bookings.newreservations.partials.newreserve_table', [
                            'checkIns' => $checkIns,
                            'per_page' => request('per_page', 4),
                            'type' => 'check-in',
                            'showStatus' => false,
                            'showActions' => false,
                        ])
                    </div>
                    <div class="px-6 py-3 bg-gray-50 text-sm text-gray-500 border-t border-gray-100">
                        Menampilkan {{ min(4, count($checkIns)) }} dari {{ count($checkIns) }} check-in mendatang
                    </div>
                </div>
            </div>
        </div>

        <!-- Room Reports Section -->
        <div class="mt-8 grid grid-cols-2 lg:grid-cols-2 gap-8">
            <!-- Room Availability Report -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex justify-between items-center mb-6">
                        <!-- Left Section -->
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <h2 class="font-semibold text-gray-800 text-lg">Laporan Ketersediaan Kamar</h2>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full max-w-xs hidden sm:block">
                            <input id="searchKamar" type="text" placeholder="Cari Properti..."
                                class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                            </svg>
                        </div>
                    </div>

                </div>
                <div class="p-6">
                    @if (is_array($roomReports) && count($roomReports) > 0)
                        @foreach ($roomReports as $propertyId => $report)
                            <div class="mb-6 last:mb-0 p-4 border border-gray-200 rounded-lg">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="font-semibold text-gray-700">{{ $report['property']['name'] }}</h3>
                                    <a href="{{ route('room-availability.index') }}"
                                        class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center bg-blue-50 px-2 py-1 rounded">
                                        Lihat Semua
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>

                                <!-- Room Stats -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-gray-800">
                                            {{ $report['room_stats']['total_rooms'] }}</div>
                                        <div class="text-sm text-gray-600">Total Kamar</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-green-600">
                                            {{ $report['room_stats']['available_rooms'] }}</div>
                                        <div class="text-sm text-gray-600">Tersedia</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-orange-600">
                                            {{ $report['room_stats']['booked_rooms'] }}</div>
                                        <div class="text-sm text-gray-600">Terisi</div>
                                    </div>
                                </div>

                                <!-- Occupancy Rate -->
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Occupancy Rate</span>
                                        <span>{{ $report['room_stats']['occupancy_rate'] }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                            style="width: {{ $report['room_stats']['occupancy_rate'] }}%"></div>
                                    </div>
                                </div>

                                <!-- Room Types Breakdown -->
                                @if (count($report['room_types_breakdown']) > 0)
                                    <div class="mt-4">
                                        <h4 class="font-medium text-gray-700 mb-2">Breakdown Tipe Kamar</h4>
                                        <div class="space-y-2">
                                            @foreach ($report['room_types_breakdown'] as $roomType)
                                                <div class="flex justify-between items-center text-sm">
                                                    <span class="text-gray-600">{{ $roomType->type }}</span>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="text-gray-500">{{ $roomType->available_rooms }}/{{ $roomType->total_rooms }}</span>
                                                        <span
                                                            class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                                            {{ $roomType->total_rooms > 0 ? round(($roomType->available_rooms / $roomType->total_rooms) * 100) : 0 }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2">Tidak ada data laporan kamar</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detailed Duration & Sales Report -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-teal-50">
                    <div class="flex justify-between items-center mb-6">
                        <!-- Left Section -->
                        <div class="flex items-center space-x-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h2 class="font-semibold text-gray-800 text-lg">Detail Durasi Sewa & Penjualan</h2>
                        </div>

                        <!-- Search Input -->
                        <div class="relative w-full max-w-xs hidden sm:block">
                            <input id="searchDurasi" type="text" placeholder="Cari Properti..."
                                class="w-full border border-gray-300 rounded-lg py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500" />
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if (is_array($roomReports) && count($roomReports) > 0)
                        @foreach ($roomReports as $propertyId => $report)
                            <div class="mb-6 last:mb-0 duration-sales-item">
                                <h3 class="font-semibold text-gray-700 mb-3 property-name-sales">{{ $report['property']['name'] }}
                                </h3>

                                <!-- Durasi Sewa -->
                                <div class="mb-4">
                                    <h4 class="font-medium text-gray-700 mb-2">Statistik Durasi Sewa</h4>
                                    <div class="grid grid-cols-3 gap-4 text-sm">
                                        <div class="text-center p-2 bg-blue-50 rounded-lg">
                                            <div class="font-bold text-blue-600">
                                                {{ $report['booking_durations']['average_duration'] }} hari</div>
                                            <div class="text-blue-500 text-xs">Rata-rata</div>
                                        </div>
                                        <div class="text-center p-2 bg-green-50 rounded-lg">
                                            <div class="font-bold text-green-600">
                                                {{ $report['booking_durations']['min_duration'] }} hari</div>
                                            <div class="text-green-500 text-xs">Terpendek</div>
                                        </div>
                                        <div class="text-center p-2 bg-purple-50 rounded-lg">
                                            <div class="font-bold text-purple-600">
                                                {{ $report['booking_durations']['max_duration'] }} hari</div>
                                            <div class="text-purple-500 text-xs">Terlama</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Breakdown Durasi -->
                                @if (count($report['booking_durations']['duration_ranges']) > 0)
                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-700 mb-2">Distribusi Durasi</h4>
                                        <div class="space-y-2">
                                            @foreach ($report['booking_durations']['duration_ranges'] as $duration)
                                                <div
                                                    class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                                    <span
                                                        class="text-sm font-medium text-gray-700">{{ $duration->duration_range }}</span>
                                                    <div class="flex items-center space-x-2">
                                                        <span
                                                            class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded">
                                                            {{ $duration->count }} booking
                                                        </span>
                                                        <span class="text-xs text-gray-500">
                                                            {{ $report['booking_durations']['total_bookings'] > 0 ? round(($duration->count / $report['booking_durations']['total_bookings']) * 100, 1) : 0 }}%
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Monthly Sales -->
                                <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                    <h4 class="font-medium text-yellow-800 mb-2">Penjualan Bulan Ini</h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-yellow-700">
                                                {{ $report['monthly_sales']['total_bookings'] }}</div>
                                            <div class="text-yellow-600 text-xs">Total Booking</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-xl font-bold text-yellow-700">Rp
                                                {{ number_format($report['monthly_sales']['total_revenue'], 0, ',', '.') }}
                                            </div>
                                            <div class="text-yellow-600 text-xs">Total Pendapatan</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <p>Tidak ada data laporan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    <script>
        // Search functionality for dashboard reports
        document.addEventListener('DOMContentLoaded', function() {
            // Helper function for search with fade animation
            function setupSearch(searchInputId, containerSelector, noResultsMsgId) {
                const searchInput = document.getElementById(searchInputId);

                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase().trim();
                        const reportContainers = document.querySelectorAll(containerSelector);

                        reportContainers.forEach(function(container) {
                            const propertyName = container.querySelector('h3');

                            if (propertyName) {
                                const propertyText = propertyName.textContent.toLowerCase();

                                if (searchTerm === '' || propertyText.includes(searchTerm)) {
                                    container.style.display = 'block';
                                    // Add fade-in animation
                                    container.style.opacity = '0';
                                    setTimeout(() => {
                                        container.style.transition = 'opacity 0.3s ease-in';
                                        container.style.opacity = '1';
                                    }, 10);
                                } else {
                                    container.style.display = 'none';
                                }
                            }
                        });

                        // Show "no results" message if all are hidden
                        const visibleContainers = Array.from(reportContainers).filter(c => c.style.display !==
                            'none');
                        const parentContainer = reportContainers[0]?.parentElement;

                        if (visibleContainers.length === 0 && parentContainer) {
                            let noResultsMsg = document.getElementById(noResultsMsgId);

                            if (!noResultsMsg) {
                                noResultsMsg = document.createElement('div');
                                noResultsMsg.id = noResultsMsgId;
                                noResultsMsg.className = 'text-center py-8 text-gray-500';
                                noResultsMsg.innerHTML = `
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <p class="mt-2">Tidak ditemukan properti dengan kata kunci "<span class="search-term">${searchTerm}</span>"</p>
                                `;
                                parentContainer.appendChild(noResultsMsg);
                            } else {
                                noResultsMsg.querySelector('.search-term').textContent = searchTerm;
                                noResultsMsg.style.display = 'block';
                            }
                        } else {
                            const noResultsMsg = document.getElementById(noResultsMsgId);
                            if (noResultsMsg) {
                                noResultsMsg.style.display = 'none';
                            }
                        }
                    });
                }
            }

            // Setup search for Room Availability Report
            setupSearch(
                'searchKamar',
                '.mb-6.last\\:mb-0.p-4.border.border-gray-200.rounded-lg',
                'no-results-message-kamar'
            );

            // Setup search for Duration & Sales Report
            setupSearch(
                'searchDurasi',
                '.duration-sales-item',
                'no-results-message-durasi'
            );
        });
    </script>
</x-app-layout>
