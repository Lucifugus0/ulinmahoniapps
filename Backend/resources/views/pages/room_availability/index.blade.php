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
                <div class="flex-1 lg:flex-none min-w-[140px] bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/30 dark:to-green-900/30 rounded-xl shadow-sm border border-emerald-200 dark:border-emerald-800 p-4 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 uppercase tracking-wide">Available</p>
                            <p class="text-xl font-bold text-emerald-700 dark:text-emerald-300" id="available-rooms">0</p>
                        </div>
                    </div>
                </div>

                <!-- Booked -->
                <div class="flex-1 lg:flex-none min-w-[140px] bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/30 dark:to-red-900/30 rounded-xl shadow-sm border border-rose-200 dark:border-rose-800 p-4 hover:shadow-md transition-shadow duration-300">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-rose-100 dark:bg-rose-900/50 rounded-lg">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-rose-600 dark:text-rose-400 uppercase tracking-wide">Booked</p>
                            <p class="text-xl font-bold text-rose-700 dark:text-rose-300" id="booked-rooms">0</p>
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

                <!-- Compact single-row filter layout -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Room -->
                    <div class="relative flex-1 min-w-[160px]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.search_room_placeholder') }}"
                            class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <!-- Property filter dropdown -->
                    <select id="property_id" name="property_id" class="px-3 py-2 text-sm border border-gray-300 rounded-md min-w-[140px]"
                        onchange="fetchFilteredBookings()">
                        <option value="">{{ __('ui.all_properties') }}</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->idrec }}" {{ request('property_id') == $property->idrec ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- Status -->
                    <select id="status" name="status" class="px-3 py-2 text-sm border border-gray-300 rounded-md min-w-[120px]">
                        <option value="all">{{ __('ui.all_status') }}</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('ui.available') }}</option>
                        <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>{{ __('ui.occupied') }}</option>
                    </select>

                    <!-- Date range -->
                    <div class="relative z-50">
                        <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}"
                            data-input
                            class="w-[220px] px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <input type="hidden" id="start_date" name="start_date" value="{{ request('start_date') }}">
                        <input type="hidden" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>

                    <!-- Show Per Page -->
                    <div class="flex items-center gap-1 ml-auto">
                        <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                        <select name="per_page" id="per_page"
                            class="border-gray-200 rounded-md text-sm py-2 px-2"
                            onchange="fetchFilteredBookings()">
                            <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
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
            <div class="overflow-x-auto" style="overflow-y: visible;" id="room-availability-table">
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
            const propertyId = document.getElementById('property_id').value; /* Property filter */
            const status = document.getElementById('status').value;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            const perPage = document.getElementById('per_page').value;

            // Gunakan halaman yang diberikan atau ambil dari URL
            const currentPage = page || getCurrentPage();

            const params = new URLSearchParams({
                search: search,
                property_id: propertyId,
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

    {{-- Room Booking Modal — placed at page level to avoid table overflow/backdrop-filter clipping --}}
    <div x-data="{
        modalOpen: false,
        loading: false,
        selectedProperty: { roomName: '', bookings: [], totalBookings: 0 },

        async openModal(roomId) {
            this.loading = true;
            this.modalOpen = true;
            try {
                const startDate = document.getElementById('start_date').value;
                const endDate = document.getElementById('end_date').value;
                let url = `/rooms/room-availability/${roomId}/bookings`;
                if (startDate && endDate) {
                    url += `?start_date=${startDate}&end_date=${endDate}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                if (data.success) {
                    this.selectedProperty = { roomName: data.room_name, bookings: data.bookings, totalBookings: data.total_bookings };
                } else {
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
            return date.toLocaleDateString('id-ID', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' });
        },
        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }
    }" @open-room-booking-modal.window="openModal($event.detail.roomId)" x-cloak>
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
            x-show="modalOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-out duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            aria-hidden="true"></div>

        <!-- Dialog -->
        <div id="property-detail-modal"
            class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
            role="dialog" aria-modal="true" x-show="modalOpen"
            x-transition:enter="transition ease-in-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in-out duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[90vh] flex flex-col"
                @click.outside="modalOpen = false"
                @keydown.escape.window="modalOpen = false">

                <!-- Header -->
                <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50">
                    <div class="text-left">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900" x-text="selectedProperty.roomName"></h3>
                        </div>
                        <p class="text-gray-500 text-sm ml-11">{{ __('ui.active_users_list') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg shadow-sm">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-sm font-semibold text-gray-700" x-text="`${selectedProperty.totalBookings || 0} booking`"></span>
                        </div>
                        <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-all duration-200" @click="modalOpen = false">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="overflow-y-auto flex-1 p-6 bg-gray-50/50">
                    <template x-if="loading">
                        <div class="flex flex-col justify-center items-center py-16">
                            <div class="w-14 h-14 border-4 border-blue-200 rounded-full animate-spin border-t-blue-600"></div>
                            <p class="text-gray-500 mt-4 text-sm">{{ __('ui.loading_booking_data') }}</p>
                        </div>
                    </template>
                    <template x-if="!loading && (!selectedProperty.bookings || selectedProperty.bookings.length === 0)">
                        <div class="text-center py-16">
                            <div class="w-20 h-20 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('ui.no_active_bookings') }}</h3>
                            <p class="text-gray-500 max-w-sm mx-auto text-sm">{{ __('ui.no_active_bookings_desc') }}</p>
                        </div>
                    </template>
                    <template x-if="!loading && selectedProperty.bookings && selectedProperty.bookings.length > 0">
                        <div class="grid gap-4 md:grid-cols-2">
                            <template x-for="booking in selectedProperty.bookings" :key="booking.id">
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-300 overflow-hidden">
                                    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-blue-50/50">
                                        <p class="font-semibold text-gray-900 mb-2" x-text="booking.booking_code"></p>
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-xs" x-text="booking.user_name ? booking.user_name.charAt(0).toUpperCase() : 'U'"></div>
                                                    <div class="min-w-0">
                                                        <h4 class="font-semibold text-gray-900 truncate" x-text="booking.user_name"></h4>
                                                        <p class="text-xs text-gray-500 truncate" x-text="booking.user_email"></p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end space-y-1.5 ml-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium" :class="booking.status_badge" x-text="booking.status"></span>
                                                <span x-show="booking.is_renewal" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-yellow-100 text-yellow-700 ring-1 ring-yellow-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    {{ __('ui.renewal') }}
                                                </span>
                                                <span x-show="booking.is_room_changed" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-100 text-purple-700 ring-1 ring-purple-200">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                    {{ __('ui.room_change') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <div class="grid grid-cols-2 gap-3 mb-4">
                                            <div class="text-center p-3 bg-blue-50 rounded-xl">
                                                <p class="text-[10px] font-semibold text-blue-600 uppercase tracking-wider mb-1">{{ __('ui.check_in') }}</p>
                                                <p class="text-sm font-bold text-gray-900" x-text="formatDate(booking.check_in)"></p>
                                            </div>
                                            <div class="text-center p-3 bg-emerald-50 rounded-xl">
                                                <p class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wider mb-1">{{ __('ui.check_out') }}</p>
                                                <p class="text-sm font-bold text-gray-900" x-text="formatDate(booking.check_out)"></p>
                                            </div>
                                        </div>
                                        <div class="space-y-2.5">
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-500 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    {{ __('ui.duration') }}
                                                </span>
                                                <span class="font-semibold text-gray-900" x-text="booking.duration"></span>
                                            </div>
                                            <div class="flex justify-between items-center text-sm">
                                                <span class="text-gray-500 flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    {{ __('ui.total') }}
                                                </span>
                                                <span class="font-bold text-emerald-600" x-text="formatCurrency(booking.total_amount)"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                                        <div class="flex justify-between items-center text-xs">
                                            <div class="flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full" :class="{ 'bg-amber-400 animate-pulse': booking.payment_status === 'unpaid', 'bg-emerald-400': booking.payment_status === 'paid', 'bg-yellow-400 animate-pulse': booking.payment_status === 'pending', 'bg-red-400': booking.payment_status === 'failed' }"></span>
                                                <span class="text-gray-600 capitalize font-medium" x-text="booking.payment_status"></span>
                                            </div>
                                            <span class="text-gray-400" x-text="booking.created_at"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <span x-text="(selectedProperty.bookings ? selectedProperty.bookings.length : 0) + ' / ' + (selectedProperty.totalBookings || 0) + ' bookings'"></span>
                    </div>
                    <button @click="modalOpen = false" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all duration-200 font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        {{ __('ui.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
