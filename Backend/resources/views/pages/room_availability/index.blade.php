<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Bagian Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl md:text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                        {{ __('ui.room_availability') }}
                    </h1>
                </div>
                <p class="text-gray-500">{{ __('ui.room_availability_desc') }}</p>
            </div>

            <!-- Statistics Cards -->
            <div class="flex flex-wrap gap-3 w-full lg:w-auto">
                <!-- Total Kamar -->
                <div class="flex-1 lg:flex-none min-w-[140px] bg-white rounded-xl shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total</p>
                            <p class="text-xl font-bold text-gray-900" id="total-rooms">0</p>
                        </div>
                    </div>
                </div>

                <!-- Available -->
                <div class="flex-1 lg:flex-none min-w-[140px] bg-gradient-to-br from-emerald-50 to-green-50 rounded-xl shadow-sm border border-emerald-200 p-4 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-emerald-600 uppercase tracking-wide">Available</p>
                            <p class="text-xl font-bold text-emerald-700" id="available-rooms">0</p>
                        </div>
                    </div>
                </div>

                <!-- Booked -->
                <div class="flex-1 lg:flex-none min-w-[140px] bg-gradient-to-br from-rose-50 to-red-50 rounded-xl shadow-sm border border-rose-200 p-4 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-rose-100 rounded-lg">
                            <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-rose-600 uppercase tracking-wide">Booked</p>
                            <p class="text-xl font-bold text-rose-700" id="booked-rooms">0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('room-availability.index') }}"
                onsubmit="event.preventDefault(); fetchFilteredBookings();"
                class="flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200 rounded-lg overflow-visible">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <!-- Search Room -->
                    <div class="md:col-span-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.search_room_placeholder') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <!-- Status -->
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md">
                        <option value="all">{{ __('ui.all_status') }}</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('ui.available') }}</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>{{ __('ui.occupied') }}</option>
                    </select>

                    <div class="md:col-span-2 flex gap-2">
                        <div class="flex-1">
                            <div class="relative z-50">
                                <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}"
                                    data-input
                                    class="w-full min-w-[320px] px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <input type="hidden" id="start_date" name="start_date" value="{{ request('start_date') }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Show Per Page (aligned to the right) -->
                    <div class="md:col-span-1 flex justify-end items-end">
                        <div class="flex items-center gap-2">
                            <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                            <select name="per_page" id="per_page"
                                class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                onchange="fetchFilteredBookings()">
                                <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="hidden fixed inset-0 bg-white/60 backdrop-blur-sm z-40 flex items-center justify-center">
            <div class="bg-white rounded-2xl shadow-xl p-6 flex flex-col items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 border-4 border-blue-200 rounded-full animate-spin border-t-blue-600"></div>
                </div>
                <p class="text-gray-600 font-medium">Memuat data...</p>
            </div>
        </div>

        <!-- Tabel Ketersediaan Kamar -->
        <div class="relative" id="room-availability-wrapper">
            <div class="overflow-x-auto" id="room-availability-table">
                @include('pages.room_availability.partials.roomAvailability_table', ['rooms' => $rooms])
            </div>
        </div>

        <!-- Paginasi -->
        <div class="bg-white rounded-b-xl shadow-sm border border-t-0 border-gray-200 px-6 py-4" id="pagination-container">
            {{ $rooms->appends(request()->except('page'))->links() }}
        </div>
    </div>
    <style>
        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        .blink {
            animation: blink 1s infinite;
        }
    </style>
    <script>
        // Format date helper
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Inisialisasi date picker
        const datePicker = flatpickr("#date_picker", {
            mode: "range",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "j M Y",
            allowInput: true,
            static: true,
            monthSelectorType: 'static',
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    document.getElementById('start_date').value = formatDate(selectedDates[0]);
                    document.getElementById('end_date').value = formatDate(selectedDates[1] || selectedDates[0]);

                    Promise.all([
                        fetchFilteredBookings(1),
                        loadStatistics()
                    ]);
                }
            },
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 0) {
                    document.getElementById('start_date').value = '';
                    document.getElementById('end_date').value = '';
                    Promise.all([
                        fetchFilteredBookings(1),
                        loadStatistics()
                    ]);
                }
            }
        });

        // Define modalView as a global function so it's available when Alpine initializes new elements after AJAX
        window.modalView = function() {
            return {
                modalOpenDetail: false,
                loading: false,
                selectedProperty: {
                    roomName: '',
                    bookings: [],
                    totalBookings: 0
                },

                async openModal(roomId) {
                    this.loading = true;
                    this.modalOpenDetail = true;

                    try {
                        // Ambil parameter tanggal dari filter
                        const startDate = document.getElementById('start_date').value;
                        const endDate = document.getElementById('end_date').value;

                        let url = `/rooms/room-availability/${roomId}/bookings`;

                        // Tambahkan parameter tanggal jika ada
                        if (startDate && endDate) {
                            url += `?start_date=${startDate}&end_date=${endDate}`;
                        }

                        const response = await fetch(url);
                        const data = await response.json();

                        if (data.success) {
                            this.selectedProperty = {
                                roomName: data.room_name,
                                bookings: data.bookings,
                                totalBookings: data.total_bookings
                            };
                        } else {
                            console.error('Failed to load booking data');
                            this.selectedProperty.bookings = [];
                        }
                    } catch (error) {
                        console.error('Error loading booking data:', error);
                        this.selectedProperty.bookings = [];
                    } finally {
                        this.loading = false;
                    }
                },

                formatDate(dateString) {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID', {
                        weekday: 'short',
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                },

                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                }
            };
        };

        // Fungsi untuk update status kamar
        function updateRoomStatus(roomId, status) {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin mengubah status kamar?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Ubah Status!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/rooms/room-availability/${roomId}/status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                rental_status: status
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Toastify({
                                    text: data.message,
                                    duration: 3000,
                                    gravity: 'top',
                                    position: 'right',
                                    style: { background: '#22c55e' },
                                    stopOnFocus: true,
                                }).showToast();

                                // Pertahankan halaman saat ini dengan mengambil parameter page dari URL
                                const currentPage = getCurrentPage();

                                // Refresh data tabel dan statistik
                                Promise.all([
                                    fetchFilteredBookings(currentPage),
                                    loadStatistics()
                                ]).then(() => {
                                    console.log('Data tabel dan statistik berhasil diperbarui');
                                });
                            } else {
                                Toastify({
                                    text: 'Gagal mengupdate status kamar',
                                    duration: 3000,
                                    gravity: 'top',
                                    position: 'right',
                                    style: { background: '#ef4444' },
                                    stopOnFocus: true,
                                }).showToast();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Toastify({
                                text: 'Terjadi kesalahan saat mengupdate status kamar',
                                duration: 3000,
                                gravity: 'top',
                                position: 'right',
                                style: { background: '#ef4444' },
                                stopOnFocus: true,
                            }).showToast();
                        });
                }
            });
        }

        // Fungsi untuk memuat statistik
        function loadStatistics() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            return fetch(`/rooms/room-availability/data?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    // Update elemen statistik
                    document.getElementById('total-rooms').textContent = data.total_rooms;
                    document.getElementById('available-rooms').textContent = data.available_rooms;
                    document.getElementById('booked-rooms').textContent = data.booked_rooms;

                    // Optional: Tambahkan animasi update
                    animateCounter('total-rooms', data.total_rooms);
                    animateCounter('available-rooms', data.available_rooms);
                    animateCounter('booked-rooms', data.booked_rooms);

                    return data;
                })
                .catch(error => {
                    console.error('Error loading statistics:', error);
                });
        }

        // Fungsi animasi counter (optional)
        function animateCounter(elementId, targetValue) {
            const element = document.getElementById(elementId);
            const currentValue = parseInt(element.textContent);

            if (currentValue === targetValue) return;

            const duration = 500; // ms
            const steps = 20;
            const stepValue = (targetValue - currentValue) / steps;
            let currentStep = 0;

            const timer = setInterval(() => {
                currentStep++;
                const newValue = Math.round(currentValue + (stepValue * currentStep));
                element.textContent = newValue;

                if (currentStep >= steps) {
                    element.textContent = targetValue;
                    clearInterval(timer);
                }
            }, duration / steps);
        }

        // Show/hide loading overlay
        function showLoading() {
            document.getElementById('loading-overlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loading-overlay').classList.add('hidden');
        }

        // Fungsi untuk fetch data dengan filter dan halaman tertentu
        function fetchFilteredBookings(page = null) {
            showLoading();

            const search = document.getElementById('search').value;
            const status = document.getElementById('status').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const perPage = document.getElementById('per_page').value;

            // Gunakan halaman yang diberikan atau ambil dari URL
            const currentPage = page || getCurrentPage();

            const params = new URLSearchParams({
                search: search,
                status: status,
                start_date: startDate,
                end_date: endDate,
                per_page: perPage,
                page: currentPage
            });

            return fetch(`{{ route('room-availability.index') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('room-availability-table').innerHTML = data.html;
                    document.getElementById('pagination-container').innerHTML = data.pagination;

                    // Update URL tanpa reload page
                    updateUrl(params);

                    return data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    throw error;
                })
                .finally(() => {
                    hideLoading();
                });
        }

        // Fungsi untuk mendapatkan halaman saat ini dari URL
        function getCurrentPage() {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('page') || 1;
        }

        // Fungsi untuk update URL tanpa reload
        function updateUrl(params) {
            const newUrl = `${window.location.pathname}?${params.toString()}`;
            window.history.replaceState({}, '', newUrl);
        }

        // Event listeners untuk real-time filtering
        document.getElementById('search').addEventListener('input', debounce(function() {
            Promise.all([
                fetchFilteredBookings(1),
                loadStatistics()
            ]);
        }, 500));

        document.getElementById('status').addEventListener('change', function() {
            Promise.all([
                fetchFilteredBookings(1),
                loadStatistics()
            ]);
        });

        document.getElementById('per_page').addEventListener('change', function() {
            // Reset ke halaman 1 ketika mengubah items per page
            Promise.all([
                fetchFilteredBookings(1),
                loadStatistics()
            ]);
        });
        // Debounce function untuk search
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Load data statistik
        function loadStatistics() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`/rooms/room-availability/data?start_date=${startDate}&end_date=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('total-rooms').textContent = data.total_rooms;
                    document.getElementById('available-rooms').textContent = data.available_rooms;
                    document.getElementById('booked-rooms').textContent = data.booked_rooms;
                });
        }

        // Load statistik saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            // Set nilai input dari URL
            document.getElementById('search').value = urlParams.get('search') || '';
            document.getElementById('status').value = urlParams.get('status') || 'all';
            document.getElementById('start_date').value = urlParams.get('start_date') || '';
            document.getElementById('end_date').value = urlParams.get('end_date') || '';
            document.getElementById('per_page').value = urlParams.get('per_page') || '8';

            // Load statistik awal
            loadStatistics();
        });
    </script>
</x-app-layout>
