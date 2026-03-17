<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.check_out') }}
                </h1>
                <!-- Description text explaining what this page shows -->
                <p class="text-sm text-gray-500 mt-1">{{ __('ui.checked_out_desc') }}</p>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <!-- Search and filter container - lighter bg in dark mode to differentiate from page background -->
        <div class="search-filter-container bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('completed.filter') }}"
                onsubmit="event.preventDefault(); fetchFilteredBookings();"
                class="search-filter-form flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">

                <!-- Single row: search, checkbox, per page -->
                <div class="flex flex-wrap items-center gap-4">
                    <!-- Search Booking -->
                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.order_id_or_guest') }}"
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <!-- Checkbox to show all checked-in bookings -->
                    <label class="flex items-center gap-2 cursor-pointer whitespace-nowrap">
                        <input type="checkbox" id="show_all_checkin" name="show_all_checkin"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-600">{{ __('ui.show_all_checked_in') }}</span>
                    </label>

                    <!-- Show Per Page (pushed to the right) -->
                    <div class="ml-auto flex items-center gap-2">
                        <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                        <select name="per_page" id="per_page"
                            class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                            <option value="8" {{ request('per_page', 25) == 8 ? 'selected' : '' }}>8</option>
                            <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>


        <!-- Table Container -->
        <div class="overflow-x-auto" id="bookingsTable">
            @include('pages.bookings.checkout.partials.checkout_table', [
                'bookings' => $bookings,
                'per_page' => request('per_page', 8),
            ])
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="bg-gray-50 rounded p-4">
            {{ $bookings->appends(request()->input())->links() }}
        </div>
    </div>

    <script>
        /* Alpine.js checkOutModal component for checkout modal on this page */
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkOutModal', (orderId) => ({
                isOpen: false,
                currentDateTime: '',
                isLateCheckout: false,
                bookingDetails: { order_id: '', guest_name: '', property_name: '', room_name: '', check_in: '', check_out: '', duration: '', total_payment: '' },
                roomInventory: [
                    { name: 'TV', condition: 'good' }, { name: 'Air Conditioner', condition: 'good' },
                    { name: 'Bed', condition: 'good' }, { name: 'Wardrobe', condition: 'good' },
                    { name: 'Desk', condition: 'good' }, { name: 'Chair', condition: 'good' },
                    { name: 'Lamp', condition: 'good' }, { name: 'Bathroom Mirror', condition: 'good' },
                    { name: 'Shower', condition: 'good' }, { name: 'Toilet', condition: 'good' },
                ],
                lainLainSelected: false,
                lainLainItem: { name: 'Lain-lain', condition: 'damaged', customText: '' },
                additionalNotes: '',
                damageCharges: 0,
                scheduledCheckoutTime: null,
                init() {
                    this.updateCurrentTime();
                    this.timeInterval = setInterval(() => { this.updateCurrentTime(); this.checkLateCheckout(); }, 1000);
                },
                setAllCondition(condition) { this.roomInventory.forEach(item => { item.condition = condition; }); },
                updateCurrentTime() {
                    const now = new Date();
                    this.currentDateTime = now.toLocaleString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
                },
                checkLateCheckout() { if (this.scheduledCheckoutTime) { this.isLateCheckout = new Date() > this.scheduledCheckoutTime; } },
                openModal(idrec, orderId) { this.isOpen = true; this.fetchBookingDetails(orderId); },
                closeModal() {
                    this.isOpen = false;
                    this.roomInventory.forEach(item => { item.condition = 'good'; });
                    this.lainLainSelected = false; this.lainLainItem.condition = 'damaged'; this.lainLainItem.customText = '';
                    this.additionalNotes = ''; this.damageCharges = 0;
                },
                async fetchBookingDetails(orderId) {
                    try {
                        const response = await fetch(`/bookings/check-out/${orderId}/details`);
                        const data = await response.json();
                        const formatDate = (dateString) => { if (!dateString) return '-'; return new Date(dateString).toLocaleString(); };
                        if (data.check_out) { this.scheduledCheckoutTime = new Date(data.check_out); }
                        this.bookingDetails = {
                            order_id: data.order_id, guest_name: data.user_name, property_name: data.property_name, room_name: data.room_name,
                            check_in: formatDate(data.actual_check_in || data.check_in), check_out: formatDate(data.check_out),
                            duration: this.calculateDuration(data.actual_check_in || data.check_in, data.check_out),
                            total_payment: this.formatRupiah(data.grandtotal_price)
                        };
                        this.checkLateCheckout();
                    } catch (error) { console.error('Error fetching booking details:', error); }
                },
                calculateDuration(checkIn, checkOut) {
                    if (!checkIn) return '-';
                    const diffDays = Math.ceil(Math.abs(new Date(checkOut || new Date()) - new Date(checkIn)) / (1000 * 60 * 60 * 24));
                    return `${diffDays} ${diffDays > 1 ? 'days' : 'day'}`;
                },
                formatRupiah(value) {
                    const num = parseFloat(value);
                    if (isNaN(num)) return 'Rp 0';
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(num);
                },
                get hasDamagedItems() {
                    return this.roomInventory.some(i => i.condition === 'damaged' || i.condition === 'missing') || (this.lainLainSelected && this.lainLainItem.customText.length > 0);
                },
                submitCheckOut() {
                    Swal.fire({ title: 'Confirm Check-Out', text: 'Are you sure you want to complete the check-out process?', icon: 'question', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, check-out', cancelButtonText: 'Cancel' }).then((result) => {
                        if (result.isConfirmed) {
                            const itemConditions = this.roomInventory.map(i => ({ name: i.name, condition: i.condition, customText: '' }));
                            if (this.lainLainSelected && this.lainLainItem.customText.trim().length > 0) { itemConditions.push({ name: 'Lain-lain', condition: this.lainLainItem.condition, customText: this.lainLainItem.customText.trim() }); }
                            fetch(`/bookings/check-out/${this.bookingDetails.order_id}`, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: JSON.stringify({ check_out_time: new Date().toISOString(), item_conditions: itemConditions, additional_notes: this.additionalNotes, damage_charges: this.damageCharges, is_late_checkout: this.isLateCheckout }) })
                            .then(r => r.ok ? r.json() : r.json().then(e => { throw e; }))
                            .then(data => { if (data.success) { Swal.fire({ title: 'Success!', text: data.message || 'Guest successfully checked out.', icon: 'success' }).then(() => { this.closeModal(); window.location.reload(); }); } else { throw new Error(data.message || 'Unknown error'); } })
                            .catch(error => { Swal.fire({ title: 'Error!', text: error.message || 'An error occurred during check-out', icon: 'error' }); });
                        }
                    });
                },
                destroy() { if (this.timeInterval) clearInterval(this.timeInterval); }
            }));
        });

        // document.addEventListener('alpine:init', () => {
        //     Alpine.data('checkOutModal', (orderId) => ({
        //         isOpen: false,
        //         currentDateTime: '',
        //         isLateCheckout: false,
        //         bookingDetails: {
        //             order_id: '',
        //             guest_name: '',
        //             property_name: '',
        //             room_name: '',
        //             check_in: '',
        //             check_out: '',
        //             duration: '',
        //             total_payment: ''
        //         },
        //         roomInventory: [{
        //                 name: 'TV',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Air Conditioner',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Bed',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Wardrobe',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Desk',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Chair',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Lamp',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Bathroom Mirror',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Shower',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             },
        //             {
        //                 name: 'Toilet',
        //                 missingOrDamaged: false,
        //                 condition: ''
        //             }
        //         ],
        //         additionalNotes: '',
        //         damageCharges: 0,
        //         scheduledCheckoutTime: null,

        //         init() {
        //             // Initialize with current time
        //             this.updateCurrentTime();

        //             // Update time every second for real-time clock
        //             this.timeInterval = setInterval(() => {
        //                 this.updateCurrentTime();
        //                 this.checkLateCheckout();
        //             }, 1000);
        //         },

        //         toggleSelectAll(checked) {
        //             this.roomInventory.forEach(item => {
        //                 item.missingOrDamaged = checked;
        //                 // You might want to set a default condition when selecting all
        //                 if (checked && !item.condition) {
        //                     item.condition =
        //                     'damaged'; // or 'missing' depending on your preference
        //                 }
        //             });
        //         },

        //         updateCurrentTime() {
        //             const now = new Date();
        //             this.currentDateTime = now.toLocaleString('en-US', {
        //                 weekday: 'long',
        //                 year: 'numeric',
        //                 month: 'long',
        //                 day: 'numeric',
        //                 hour: '2-digit',
        //                 minute: '2-digit',
        //                 second: '2-digit',
        //                 hour12: true
        //             });
        //         },

        //         checkLateCheckout() {
        //             if (this.scheduledCheckoutTime) {
        //                 const now = new Date();
        //                 this.isLateCheckout = now > this.scheduledCheckoutTime;
        //             }
        //         },

        //         openModal(idrec, orderId) {
        //             this.isOpen = true;
        //             this.fetchBookingDetails(orderId);
        //         },

        //         closeModal() {
        //             this.isOpen = false;
        //             // Reset form when closing
        //             this.roomInventory.forEach(item => {
        //                 item.missingOrDamaged = false;
        //                 item.condition = '';
        //             });
        //             this.additionalNotes = '';
        //             this.damageCharges = 0;
        //         },

        //         async fetchBookingDetails(orderId) {
        //             try {
        //                 const response = await fetch(`/bookings/check-out/${orderId}/details`);
        //                 const data = await response.json();

        //                 // Format dates properly
        //                 const formatDate = (dateString) => {
        //                     if (!dateString) return 'Not checked in yet';
        //                     const date = new Date(dateString);
        //                     return date.toLocaleString();
        //                 };

        //                 // Store scheduled checkout time for comparison
        //                 if (data.check_out) {
        //                     this.scheduledCheckoutTime = new Date(data.check_out);
        //                 }

        //                 this.bookingDetails = {
        //                     order_id: data.order_id,
        //                     guest_name: data.user_name,
        //                     property_name: data.property_name,
        //                     room_name: data.room_name,
        //                     check_in: formatDate(data.actual_check_in || data.check_in),
        //                     check_out: formatDate(data.check_out), // Scheduled check-out
        //                     duration: this.calculateDuration(data.actual_check_in || data
        //                         .check_in, data.check_out),
        //                     total_payment: this.formatRupiah(data.grandtotal_price)
        //                 };

        //                 // Immediately check if checkout is late
        //                 this.checkLateCheckout();
        //             } catch (error) {
        //                 console.error('Error fetching booking details:', error);
        //                 // You might want to show an error message to the user here
        //             }
        //         },

        //         calculateDuration(checkIn, checkOut) {
        //             if (!checkIn) return 'Not checked in';

        //             const start = new Date(checkIn);
        //             const end = checkOut ? new Date(checkOut) : new Date();
        //             const diffTime = Math.abs(end - start);
        //             const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        //             return `${diffDays} ${diffDays > 1 ? 'days' : 'day'}`;
        //         },

        //         formatRupiah(value) {
        //             const numericValue = parseFloat(value);

        //             if (isNaN(numericValue)) {
        //                 return 'Rp 0';
        //             }

        //             return new Intl.NumberFormat('id-ID', {
        //                 style: 'currency',
        //                 currency: 'IDR',
        //                 minimumFractionDigits: 0,
        //                 maximumFractionDigits: 0
        //             }).format(numericValue);
        //         },

        //         get hasDamagedItems() {
        //             return this.roomInventory.some(item => item.missingOrDamaged);
        //         },

        //         submitCheckOut() {
        //             // Show confirmation dialog
        //             Swal.fire({
        //                 title: 'Confirm Check-Out',
        //                 text: 'Are you sure you want to complete the check-out process?',
        //                 icon: 'question',
        //                 showCancelButton: true,
        //                 confirmButtonColor: '#d33',
        //                 cancelButtonColor: '#3085d6',
        //                 confirmButtonText: 'Yes, check-out',
        //                 cancelButtonText: 'Cancel'
        //             }).then((result) => {
        //                 if (result.isConfirmed) {
        //                     // Prepare damaged items data
        //                     const damagedItems = this.roomInventory
        //                         .filter(item => item.missingOrDamaged)
        //                         .map(item => ({
        //                             name: item.name,
        //                             condition: item.condition
        //                         }));

        //                     // Prepare payload
        //                     const payload = {
        //                         check_out_time: new Date().toISOString(),
        //                         // damaged_items: damagedItems,
        //                         // additional_notes: this.additionalNotes,
        //                         // damage_charges: this.damageCharges,
        //                         // is_late_checkout: this.isLateCheckout
        //                     };

        //                     // Show loading state
        //                     this.isSubmitting = true;

        //                     // Send data to server
        //                     fetch(`/bookings/check-out/${this.bookingDetails.order_id}`, {
        //                             method: 'POST',
        //                             headers: {
        //                                 'Content-Type': 'application/json',
        //                                 'X-CSRF-TOKEN': document.querySelector(
        //                                         'meta[name="csrf-token"]')
        //                                     .content,
        //                                 'Accept': 'application/json'
        //                             },
        //                             body: JSON.stringify(payload)
        //                         })
        //                         .then(response => {
        //                             if (!response.ok) {
        //                                 return response.json().then(err => {
        //                                     throw err;
        //                                 });
        //                             }
        //                             return response.json();
        //                         })
        //                         .then(data => {
        //                             if (data.success) {
        //                                 // Show success notification
        //                                 Swal.fire({
        //                                     title: 'Success!',
        //                                     text: data.message ||
        //                                         'Guest successfully checked out.',
        //                                     icon: 'success',
        //                                     confirmButtonText: 'OK'
        //                                 }).then(() => {
        //                                     // Close modal and refresh
        //                                     this.closeModal();
        //                                     window.location.reload();
        //                                 });
        //                             } else {
        //                                 throw new Error(data.message ||
        //                                     'Unknown error occurred');
        //                             }
        //                         })
        //                         .catch(error => {
        //                             console.error('Error:', error);
        //                             // Show error notification
        //                             Swal.fire({
        //                                 title: 'Error!',
        //                                 text: error.message ||
        //                                     'An error occurred during check-out',
        //                                 icon: 'error',
        //                                 confirmButtonText: 'OK'
        //                             });
        //                         })
        //                         .finally(() => {
        //                             this.isSubmitting = false;
        //                         });
        //                 }
        //             });
        //         },

        //         // Clean up interval when component is destroyed
        //         destroy() {
        //             if (this.timeInterval) {
        //                 clearInterval(this.timeInterval);
        //             }
        //         }
        //     }));
        // });

        document.addEventListener('DOMContentLoaded', function() {
            // Get all filter elements
            const searchInput = document.getElementById('search');
            const perPageSelect = document.getElementById('per_page');

            // Debounce function for search
            const debounce = (func, delay) => {
                let timeout;
                return function() {
                    const context = this;
                    const args = arguments;
                    clearTimeout(timeout);
                    timeout = setTimeout(() => func.apply(context, args), delay);
                };
            };

            // Show all checked-in checkbox
            const showAllCheckinCheckbox = document.getElementById('show_all_checkin');

            // Event listeners
            searchInput.addEventListener('input', debounce(fetchFilteredBookings, 300));
            perPageSelect.addEventListener('change', fetchFilteredBookings);
            showAllCheckinCheckbox.addEventListener('change', fetchFilteredBookings);

            // Function to fetch filtered bookings
            function fetchFilteredBookings(url = null) {
                // Collect all filter values
                const params = new URLSearchParams();

                // If URL is provided (from pagination) and it's a valid string, extract params from it
                if (url && typeof url === 'string' && url.startsWith('http')) {
                    const urlObj = new URL(url);
                    urlObj.searchParams.forEach((value, key) => {
                        params.append(key, value);
                    });
                } else {
                    // Get search value
                    const search = document.getElementById('search').value;
                    if (search) params.append('search', search);

                    // Get per page value
                    const perPage = document.getElementById('per_page').value;
                    params.append('per_page', perPage);

                    /* Pass show_all_checkin checkbox state */
                    if (document.getElementById('show_all_checkin').checked) {
                        params.append('show_all_checkin', '1');
                    }
                }

                // Show loading state
                const tableContainer = document.querySelector('.overflow-x-auto');
                tableContainer.innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            `;

                // Make AJAX request to the filter endpoint
                fetch(`{{ route('checkout.filter') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        document.querySelector('.overflow-x-auto').innerHTML = data.table;
                        document.getElementById('paginationContainer').innerHTML = data.pagination;

                        // Re-initialize Alpine.js components for new DOM elements
                        if (typeof Alpine !== 'undefined') {
                            Alpine.initTree(document.querySelector('.overflow-x-auto'));
                        }

                        // Re-attach event listeners for new pagination links
                        attachPaginationListeners();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.querySelector('.overflow-x-auto').innerHTML = `
                        <div class="text-center py-8 text-red-500">
                            {{ __('ui.error_loading') }}
                        </div>
                    `;
                    });
            }

            // Function to attach event listeners to pagination links
            function attachPaginationListeners() {
                const paginationContainer = document.getElementById('paginationContainer');
                if (!paginationContainer) return;

                // Find all pagination links
                const paginationLinks = paginationContainer.querySelectorAll('a[href]');

                paginationLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href');
                        if (url) {
                            fetchFilteredBookings(url);
                        }
                    });
                });
            }

            // Initial attach of pagination listeners
            attachPaginationListeners();
        });
    </script>
    @include('pages.bookings.partials.dark-badge-styles')
</x-app-layout>
