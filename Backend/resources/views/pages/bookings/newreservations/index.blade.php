<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.confirm_reservations') }}
                </h1>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('newReserv.filter') }}"
                onsubmit="event.preventDefault(); fetchFilteredBookings();"
                class="flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <!-- Search Booking -->
                    <div class="md:col-span-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.order_id_or_guest') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <div class="md:col-span-2 flex gap-2">
                        <div class="flex-1">
                            <div class="relative z-10">
                                <input type="text" id="date_picker" placeholder="{{ __('ui.select_date_range') }}"
                                    data-input
                                    class="w-full min-w-[320px] px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <input type="hidden" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Show Per Page (aligned to the right) -->
                    <div class="md:col-span-1 md:col-start-5 flex justify-end items-end">
                        <div class="flex items-center gap-2">
                            <label for="per_page" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                            <select name="per_page" id="per_page"
                                class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="8" {{ request('per_page') == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bookings Table -->
        <div class="overflow-x-auto" id="bookingTableContainer">
            @include('pages.bookings.newreservations.partials.newreserve_table', [
                'checkIns' => $checkIns,
                'per_page' => request('per_page', 8),
                'showActions' => $showActions,
                'checkIns' => $checkIns,
                'per_page' => request('per_page', 8),
                'showActions' => $showActions,
            ])
        </div>
        <!-- Pagination -->
        <div class="bg-gray-50 rounded p-4" id="paginationContainer">
            {{ $checkIns->appends(request()->input())->links() }}
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkInModal', (initialOrderId, docRequired = true) => ({
                isOpen: false,
                isDragging: false,
                docPreview: null,
                docPreviewType: null,
                docFile: null, // Variabel baru untuk menyimpan file
                profilePhotoUrl: null,
                selectedDocType: 'ktp',
                bookingId: initialOrderId,
                docRequired: docRequired, // Add docRequired property
                guestContact: {
                    name: '',
                    email: '',
                    phone: '',
                    nik: ''
                },
                currentDateTime: new Date().toLocaleString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit',
                    hour12: false
                }),
                bookingDetails: {
                    order_id: '',
                    guest_name: '',
                    property_name: '',
                    room_name: '',
                    duration: '',
                    total_payment: ''
                },

                // Variabel untuk webcam
                uploadMethod: 'file',
                isCapturing: false,
                webcamPhoto: null,
                webcamStream: null,
                webcamInitialized: false,

                init() {
                    // Update time every second
                    setInterval(() => {
                        this.currentDateTime = new Date().toLocaleString('en-US', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        });
                    }, 1000);
                },

                async openModal(idrec, orderId) {
                    this.bookingId = orderId;
                    this.isOpen = true;
                    this.currentDateTime = new Date().toLocaleString('en-US', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit',
                        hour12: false
                    });

                    await this.fetchBookingDetails();

                    // Set nilai default untuk form kontak
                    this.guestContact.name = this.bookingDetails.guest_name || '';
                    this.guestContact.email = this.bookingDetails.guest_email || '';
                    this.guestContact.phone = this.bookingDetails.guest_phone || '';
                    this.guestContact.nik = this.bookingDetails.guest_nik || '';
                },

                // Method untuk webcam
                async startWebcam() {
                    try {
                        // Hentikan webcam jika sedang berjalan
                        if (this.webcamStream) {
                            this.stopWebcam();
                        }

                        // Dapatkan akses ke kamera
                        this.webcamStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: 'environment',
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            },
                            audio: false
                        });

                        // Tunggu hingga video element siap
                        await this.$nextTick();

                        const video = this.$refs.webcamVideo;
                        if (video) {
                            video.srcObject = this.webcamStream;

                            // Tunggu video siap diputar
                            video.onloadedmetadata = () => {
                                video.play().catch(error => {
                                    console.error('Error playing video:', error);
                                });
                            };
                        }

                        this.isCapturing = true;
                        this.webcamPhoto = null;
                        this.webcamInitialized = true;

                    } catch (error) {
                        console.error('Error accessing webcam:', error);
                        this.showErrorToast(
                            '{{ __('ui.camera_access_error') }}'
                        );
                    }
                },

                stopWebcam() {
                    if (this.webcamStream) {
                        this.webcamStream.getTracks().forEach(track => {
                            track.stop();
                        });
                        this.webcamStream = null;
                    }
                    this.isCapturing = false;
                    this.webcamInitialized = false;

                    // Hapus srcObject dari video element
                    if (this.$refs.webcamVideo) {
                        this.$refs.webcamVideo.srcObject = null;
                    }
                },

                capturePhoto() {
                    if (!this.webcamStream || !this.$refs.webcamVideo) {
                        this.showErrorToast('{{ __('ui.webcam_unavailable') }}');
                        return;
                    }

                    const video = this.$refs.webcamVideo;
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    // Set canvas size sama dengan video
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;

                    // Gambar frame video ke canvas
                    context.drawImage(video, 0, 0, canvas.width, canvas.height);

                    // Konversi ke data URL
                    try {
                        this.webcamPhoto = canvas.toDataURL('image/jpeg', 0.8);
                        this.isCapturing = false;

                        // Stop webcam setelah mengambil foto
                        this.stopWebcam();

                        this.showSuccessToast('{{ __('ui.photo_captured') }}');
                    } catch (error) {
                        console.error('Error capturing photo:', error);
                        this.showErrorToast('{{ __('ui.photo_capture_failed') }}');
                    }
                },

                retakePhoto() {
                    this.webcamPhoto = null;
                    this.startWebcam();
                },

                useWebcamPhoto() {
                    if (this.webcamPhoto) {
                        this.docPreview = this.webcamPhoto;
                        this.docPreviewType = 'image';

                        // Konversi data URL ke File object
                        const fileName = `webcam-capture-${Date.now()}.jpg`;
                        this.docFile = this.dataURLtoFile(this.webcamPhoto, fileName);

                        this.webcamPhoto = null;
                        this.showSuccessToast(
                            '{{ __('ui.photo_used_as_id') }}'
                        );
                    }
                },

                handleUploadMethodChange(method) {
                    this.uploadMethod = method;

                    // Jika beralih dari camera ke file, pastikan webcam dimatikan
                    if (method === 'file' && this.isCapturing) {
                        this.stopWebcam();
                    }
                },

                // Method untuk mengkonversi data URL ke File object
                dataURLtoFile(dataurl, filename) {
                    const arr = dataurl.split(',');
                    const mime = arr[0].match(/:(.*?);/)[1];
                    const bstr = atob(arr[1]);
                    let n = bstr.length;
                    const u8arr = new Uint8Array(n);

                    while (n--) {
                        u8arr[n] = bstr.charCodeAt(n);
                    }

                    return new File([u8arr], filename, {
                        type: mime
                    });
                },

                async fetchBookingDetails() {
                    try {
                        const response = await fetch(
                            `/bookings/newReserv-in/${this.bookingId}/details`);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }

                        const contentType = response.headers.get('content-type');
                        if (!contentType || !contentType.includes('application/json')) {
                            throw new Error('Server returned non-JSON response');
                        }

                        const data = await response.json();

                        const formatDateTime = (dateString, timeString) => {
                            if (!dateString) return {
                                date: 'N/A',
                                time: 'N/A'
                            };

                            const date = new Date(dateString);
                            const formattedDate = date.toLocaleString('en-US', {
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });

                            let formattedTime = timeString;
                            if (!timeString && dateString) {
                                formattedTime = date.toLocaleString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                });
                            }

                            return {
                                date: formattedDate,
                                time: formattedTime || 'N/A'
                            };
                        };

                        const checkIn = formatDateTime(data.transaction?.check_in, data.transaction
                            ?.check_in_time);
                        const checkOut = formatDateTime(data.transaction?.check_out, data
                            .transaction?.check_out_time);

                        // PERBAIKAN: Hitung durasi berdasarkan booking_type
                        const duration = this.calculateDuration(
                            data.transaction?.check_in,
                            data.transaction?.check_out,
                            data.transaction?.booking_type,
                            data.transaction?.booking_days,
                            data.transaction?.booking_months
                        );

                        this.bookingDetails = {
                            order_id: data.order_id,
                            check_in: data.transaction?.check_in ?
                                new Date(data.transaction.check_in).toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                }) : 'N/A',
                            check_out: data.transaction?.check_out ?
                                new Date(data.transaction.check_out).toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false
                                }) : 'N/A',
                            check_in_date: checkIn.date,
                            check_in_time: checkIn.time,
                            check_out_date: checkOut.date,
                            check_out_time: checkOut.time,
                            guest_name: data.user?.first_name || 'N/A',
                            guest_email: data.user?.email || 'N/A',
                            guest_phone: data.transaction?.user_phone_number || 'N/A',
                            guest_nik: data.user?.nik || '',
                            property_name: data.property?.name || 'N/A',
                            property_address: data.property?.address || 'N/A',
                            room_name: data.room?.name || 'N/A',
                            room_number: data.room?.no || 'N/A',
                            update_by: data.transaction?.update_by || 'N/A',
                            duration: duration, // Gunakan durasi yang sudah dihitung
                            total_payment: data.transaction?.grandtotal_price ?
                                this.formatRupiah(data.transaction.grandtotal_price) : 'N/A',
                            transaction_type: data.transaction?.transaction_type || 'N/A',
                            doc_path: data.doc_path
                        };

                        this.profilePhotoUrl = data.user_profile_photo_url || null;

                    } catch (error) {
                        console.error('Error fetching booking details:', error);
                        this.showErrorToast('{{ __('ui.failed_load_booking_details') }} ' + error.message);
                    }
                },

                // PERBAIKAN: Method calculateDuration yang diperbarui
                calculateDuration(checkIn, checkOut, bookingType, bookingDays, bookingMonths) {
                    if (!checkIn || !checkOut) return 'N/A';

                    // Jika booking_type tersedia, gunakan logika berdasarkan jenis booking
                    if (bookingType) {
                        switch (bookingType.toLowerCase()) {
                            case 'daily':
                                if (bookingDays && !isNaN(bookingDays)) {
                                    return `${bookingDays} {{ __('ui.days') }}`;
                                }
                                // Fallback ke perhitungan normal jika bookingDays tidak tersedia
                                break;

                            case 'monthly':
                                if (bookingMonths && !isNaN(bookingMonths)) {
                                    return `${bookingMonths} {{ __('ui.months') }}`;
                                }
                                // Fallback ke perhitungan normal jika bookingMonths tidak tersedia
                                break;

                            default:
                                // Untuk jenis booking lain, gunakan perhitungan normal
                                break;
                        }
                    }

                    // Perhitungan durasi normal berdasarkan tanggal check-in dan check-out
                    const start = new Date(checkIn);
                    const end = new Date(checkOut);
                    const diffTime = Math.abs(end - start);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    return `${diffDays} {{ __('ui.nights') }}`;
                },

                getDocumentImageUrl() {
                    if (this.bookingDetails.doc_path) {
                        if (this.bookingDetails.doc_path.startsWith('http')) {
                            return this.bookingDetails.doc_path;
                        }
                        return window.location.origin + '/' + this.bookingDetails.doc_path;
                    }

                    if (this.docPreview) {
                        return this.docPreview;
                    }

                    if (this.profilePhotoUrl) {
                        return this.profilePhotoUrl;
                    }

                    return null;
                },

                handleDocDrop(e) {
                    this.isDragging = false;
                    const files = e.dataTransfer.files;
                    if (files.length > 0 && files[0].type.match('image.*')) {
                        this.previewDoc(files[0]);
                    }
                },

                handleDocUpload(e) {
                    const file = e.target.files[0];
                    if (file && file.type.match('image.*')) {
                        this.previewDoc(file);
                    }
                },

                previewDoc(file) {
                    if (file.size > 5 * 1024 * 1024) {
                        this.showErrorToast('{{ __('ui.file_size_limit') }}');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.docPreview = e.target.result;
                        this.docPreviewType = 'image';
                        this.docFile = file; // Simpan file object
                    };
                    reader.readAsDataURL(file);
                },

                removeDoc() {
                    this.docPreview = null;
                    this.docPreviewType = null;
                    this.docFile = null; // Reset file juga
                    if (this.$refs.docInput) {
                        this.$refs.docInput.value = '';
                    }
                },

                async submitCheckIn() {
                    // Validasi informasi kontak
                    if (!this.guestContact.name || !this.guestContact.email || !this.guestContact
                        .phone || !this.guestContact.nik) {
                        this.showErrorToast('{{ __('ui.complete_contact_info') }}');
                        return;
                    }

                    // Validasi dokumen identifikasi (hanya jika docRequired = true)
                    if (this.docRequired && !this.profilePhotoUrl && !this.docFile) {
                        this.showErrorToast('{{ __('ui.upload_id_document') }}');
                        return;
                    }

                    try {
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content');

                        let formData = new FormData();
                        formData.append('doc_type', this.selectedDocType);
                        formData.append('has_profile_photo', this.profilePhotoUrl ? 1 : 0);
                        formData.append('guest_name', this.guestContact.name);
                        formData.append('guest_email', this.guestContact.email);
                        formData.append('guest_phone', this.guestContact.phone);
                        formData.append('guest_nik', this.guestContact.nik);

                        // Upload dokumen jika ada
                        if (this.docFile) {
                            formData.append('doc_image', this.docFile);
                        }

                        const response = await fetch(`/bookings/newReserv/${this.bookingId}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || `HTTP error: ${response.status}`);
                        }

                        if (data.success) {
                            this.showSuccessToast('{{ __('ui.checkin_success') }}');

                            // Jika perlu print agreement, buka di tab baru
                            if (data.need_print_agreement && data.print_url) {
                                setTimeout(() => {
                                    window.open(data.print_url, '_blank');
                                }, 500);
                            }

                            // Refresh tabel setelah check-in berhasil
                            this.refreshBookingTable();

                            // Close modal
                            setTimeout(() => {
                                this.closeModal();
                            }, 1500);
                        } else {
                            this.showErrorToast(data.message || '{{ __('ui.checkin_failed') }}');
                        }

                    } catch (error) {
                        console.error("Check-in error:", error);
                        this.showErrorToast(error.message || '{{ __('ui.checkin_error') }}');
                    }
                },

                // Tambahkan method untuk refresh tabel
                refreshBookingTable() {
                    // Method 1: Reload bagian tabel saja
                    fetch('/bookings/newReserv?per_page=' + this.getCurrentPerPage())
                        .then(response => response.text())
                        .then(html => {
                            // Cari container tabel dan update isinya
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTable = doc.querySelector('#bookingTableContainer');

                            if (newTable) {
                                const container = document.querySelector('#bookingTableContainer');
                                container.innerHTML = newTable.innerHTML;

                                // Reinitialize Alpine components on the newly added elements
                                if (typeof Alpine !== 'undefined') {
                                    Alpine.initTree(container);
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error refreshing table:', error);
                            // Fallback: reload page jika metode di atas gagal
                            window.location.reload();
                        });
                },

                // Method untuk mendapatkan nilai per_page saat ini
                getCurrentPerPage() {
                    const urlParams = new URLSearchParams(window.location.search);
                    return urlParams.get('per_page') || 8;
                },

                openRegistrationForm(orderId) {
                    const url = `/bookings/newReserv-in/${orderId}/regist`;

                    // Buka di tab baru
                    const newWindow = window.open(url, '_blank');

                    // Focus ke window baru jika berhasil dibuka
                    if (newWindow) {
                        newWindow.focus();
                    } else {
                        // Fallback: jika popup diblokir, redirect di tab saat ini
                        this.showErrorToast('{{ __('ui.popup_blocked') }}');
                        window.location.href = url;
                    }
                },

                showSuccessToast(message) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: message,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                },

                showErrorToast(message) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: message,
                        showConfirmButton: false,
                        timer: 1500,
                        timerProgressBar: true
                    });
                },

                formatRupiah(value) {
                    const numericValue = parseFloat(value);

                    if (isNaN(numericValue)) {
                        return 'Rp 0';
                    }

                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(numericValue);
                },

                closeModal() {
                    this.isOpen = false;
                    this.stopWebcam(); // Pastikan webcam dihentikan saat modal ditutup
                    this.resetForm();
                },

                resetForm() {
                    this.docPreview = null;
                    this.docPreviewType = null;
                    this.docFile = null; // Reset file
                    this.selectedDocType = 'ktp';
                    this.uploadMethod = 'file';
                    this.isCapturing = false;
                    this.webcamPhoto = null;
                    this.guestContact = {
                        name: '',
                        email: '',
                        phone: '',
                        nik: ''
                    };
                    if (this.$refs.docInput) {
                        this.$refs.docInput.value = '';
                    }
                    this.stopWebcam();
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function() {
            function formatDate(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            const urlParams = new URLSearchParams(window.location.search);
            const urlStartDate = urlParams.get('start_date');
            const urlEndDate = urlParams.get('end_date');

            const datePicker = flatpickr("#date_picker", {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "j M Y",
                allowInput: true,
                static: true,
                monthSelectorType: 'static',
                defaultDate: (urlStartDate && urlEndDate) ? [urlStartDate, urlEndDate] : undefined,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        document.getElementById('start_date').value = formatDate(selectedDates[0]);
                        document.getElementById('end_date').value = formatDate(selectedDates[1] || selectedDates[0]);
                        fetchFilteredBookings();
                    }
                },
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 0) {
                        document.getElementById('start_date').value = '';
                        document.getElementById('end_date').value = '';
                        fetchFilteredBookings();
                    }
                }
            });

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

            // Event listeners
            searchInput.addEventListener('input', debounce(fetchFilteredBookings, 300));
            perPageSelect.addEventListener('change', fetchFilteredBookings);

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

                    // Get date range values
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    if (startDate) params.append('start_date', startDate);
                    if (endDate) params.append('end_date', endDate);

                    // Get per page value
                    const perPage = document.getElementById('per_page').value;
                    params.append('per_page', perPage);
                }

                // Show loading state
                const tableContainer = document.querySelector('.overflow-x-auto');
                tableContainer.innerHTML = `
                <div class="flex justify-center items-center h-64">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            `;

                // Make AJAX request to the filter endpoint
                fetch(`{{ route('newReserv.filter') }}?${params.toString()}`, {
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

                        // Reinitialize Alpine components on the newly added elements
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
</x-app-layout>
