<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Bagian Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.refunds') }}
                </h1>
                <p class="text-gray-600 mt-2">{{ __('ui.manage_refunds_desc') }}</p>
            </div>
        </div>

        <!-- Bagian Pencarian dan Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible mb-6">
            <form method="GET" action="{{ route('admin.payments.filter') }}"
                onsubmit="event.preventDefault(); fetchFilteredBookings();"
                class="flex flex-col gap-4 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">

                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <!-- Pencarian Refund -->
                    <div class="md:col-span-1 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="search" name="search" placeholder="{{ __('ui.order_id_or_guest_payment') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            value="{{ request('search') }}">
                    </div>

                    <!-- Status -->
                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">{{ __('ui.all_status') }}</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('ui.pending') }}</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>{{ __('ui.refunded') }}</option>
                    </select>

                    <div class="md:col-span-2 flex gap-2">
                        <div class="flex-1">
                            <div class="relative z-10">
                                <input type="text" id="date_picker"
                                    placeholder="{{ __('ui.select_date_range') }}" data-input
                                    class="w-full min-w-[280px] px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                                <input type="hidden" id="start_date" name="start_date"
                                    value="{{ request('start_date') }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ request('end_date') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Tampilkan Per Halaman (rata kanan) -->
                    <div class="md:col-span-1 flex justify-end items-end">
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

        <!-- Tabel Refund Pembayaran -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.order_id') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.guest_name') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.property') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.refund_amount') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.refund_date') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.status') }}</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('ui.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($refunds as $refund)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-indigo-600">{{ $refund->id_booking }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $refund->transaction->transaction_code ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $refund->transaction->user_name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $refund->transaction->user_phone_number ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $refund->transaction->property_name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $refund->transaction->room_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900">Rp
                                        {{ number_format($refund->transaction->grandtotal_price ?? 0, 0, ',', '.') }}
                                    </div>
                                    <div class="text-xs text-gray-500">{{ $refund->transaction->booking_days ?? 0 }}
                                        {{ __('ui.days') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ \Carbon\Carbon::parse($refund->refund_date)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'paid' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'processed' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-purple-100 text-purple-800',
                                            'refunded' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $colorClass = $statusColors[$refund->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $colorClass }}">
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex justify-center space-x-2">
                                        @if($refund->status === 'pending')
                                        <!-- Tombol Refund -->
                                        <div x-data="refundModal()" class="relative group">
                                            <button type="button"
                                                class="inline-flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-all duration-200 ease-in-out shadow-sm hover:shadow-md"
                                                @click="openModal('{{ $refund->id_booking }}')"
                                                title="{{ __('ui.confirm_refund') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <polyline points="9 11 12 14 22 4"></polyline>
                                                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                                                </svg>
                                                <span class="text-sm font-medium">{{ __('ui.confirm_refund') }}</span>
                                            </button>

                                            <!-- Modal -->
                                            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                                x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                                                x-transition:enter-start="opacity-0"
                                                x-transition:enter-end="opacity-100"
                                                x-transition:leave="transition ease-out duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0" aria-hidden="true" x-cloak>
                                            </div>

                                            <div class="fixed inset-0 z-50 overflow-hidden flex items-center justify-center p-4"
                                                role="dialog" aria-modal="true" x-show="isOpen"
                                                x-transition:enter="transition ease-in-out duration-300"
                                                x-transition:enter-start="opacity-0 scale-95"
                                                x-transition:enter-end="opacity-100 scale-100"
                                                x-transition:leave="transition ease-in-out duration-200"
                                                x-transition:leave-start="opacity-100 scale-100"
                                                x-transition:leave-end="opacity-0 scale-95" x-cloak>

                                                <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl max-h-[95vh] flex flex-col"
                                                    @click.outside="closeModal" @keydown.escape.window="closeModal">

                                                    <!-- Modal header -->
                                                    <div
                                                        class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50">
                                                        <h3 class="text-lg font-semibold text-gray-800">
                                                            {{ __('ui.refund_proof') }} #{{ $refund->id_booking }}<span x-text="orderId"></span>
                                                        </h3>
                                                        <button @click="closeModal"
                                                            class="text-gray-500 hover:text-gray-700">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                                                                fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>

                                                    <!-- Modal content -->
                                                    <div class="overflow-y-auto flex-1 p-6">
                                                        <form :id="'refund-form-' + orderId" method="POST"
                                                            action="{{ route('admin.refunds.store') }}"
                                                            enctype="multipart/form-data">
                                                            @csrf

                                                            <!-- Input hidden untuk order_id -->
                                                            <input type="hidden" name="order_id"
                                                                x-bind:value="orderId">

                                                            <div class="mb-6">
                                                                <label
                                                                    class="block text-sm font-medium text-gray-700 mb-2">
                                                                    {{ __('ui.upload_refund_proof') }}
                                                                </label>

                                                                <!-- Upload Area -->
                                                                <div x-show="!selectedFile" @drop="handleDrop($event)"
                                                                    @dragover.prevent @dragenter.prevent
                                                                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 cursor-pointer"
                                                                    :class="{ 'border-blue-400 bg-blue-50': isDragOver }">
                                                                    <div class="space-y-2">
                                                                        <svg class="w-12 h-12 mx-auto text-gray-400"
                                                                            fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                            </path>
                                                                        </svg>
                                                                        <div
                                                                            class="flex text-sm text-gray-600 justify-center">
                                                                            <label :for="'refund_image-' + orderId"
                                                                                class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                                <span>{{ __('ui.upload_photo') }}</span>
                                                                                <input :id="'refund_image-' + orderId"
                                                                                    name="refund_image" type="file"
                                                                                    accept="image/*"
                                                                                    @change="handleFileSelect($event)"
                                                                                    class="sr-only">
                                                                            </label>
                                                                            <p class="pl-1">{{ __('ui.or_drag_drop') }}</p>
                                                                        </div>
                                                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG
                                                                            up to 5MB</p>
                                                                    </div>
                                                                </div>

                                                                <!-- Preview Area -->
                                                                <div x-show="selectedFile" class="mt-4 space-y-4">
                                                                    <!-- Info File -->
                                                                    <div
                                                                        class="flex items-center justify-between bg-gray-50 p-3 rounded-md">
                                                                        <div class="flex items-center space-x-3">
                                                                            <svg class="h-8 w-8 text-green-500"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke="currentColor">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                            </svg>
                                                                            <div>
                                                                                <p class="text-sm font-medium text-gray-900"
                                                                                    x-text="selectedFile ? selectedFile.name : ''">
                                                                                </p>
                                                                                <p class="text-sm text-gray-500"
                                                                                    x-text="selectedFile ? formatFileSize(selectedFile.size) : ''">
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                        <button type="button" @click="removeFile"
                                                                            class="text-red-500 hover:text-red-700">
                                                                            <svg class="h-5 w-5" fill="none"
                                                                                viewBox="0 0 24 24"
                                                                                stroke="currentColor">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M6 18L18 6M6 6l12 12" />
                                                                            </svg>
                                                                        </button>
                                                                    </div>

                                                                    <!-- Preview -->
                                                                    <div
                                                                        class="border border-gray-200 rounded-lg overflow-hidden">
                                                                        <div
                                                                            class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                                                            <h4
                                                                                class="text-sm font-medium text-gray-700">
                                                                                {{ __('ui.preview') }}</h4>
                                                                        </div>
                                                                        <div class="p-4 bg-white">
                                                                            <div class="flex justify-center">
                                                                                <img x-bind:src="imagePreviewUrl"
                                                                                    alt="Preview bukti refund"
                                                                                    class="max-w-full max-h-64 object-contain rounded-lg shadow-sm border border-gray-200"
                                                                                    x-show="imagePreviewUrl">
                                                                                <div x-show="!imagePreviewUrl"
                                                                                    class="text-center py-8 text-gray-500">
                                                                                    <svg class="w-12 h-12 mx-auto text-gray-400"
                                                                                        fill="none"
                                                                                        stroke="currentColor"
                                                                                        viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round"
                                                                                            stroke-linejoin="round"
                                                                                            stroke-width="2"
                                                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                                    </svg>
                                                                                    <p class="mt-2 text-sm">{{ __('ui.failed_load_preview') }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <!-- Modal footer -->
                                                    <div
                                                        class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                                                        <div class="text-sm text-gray-500">
                                                            <span>{{ __('ui.press_esc_to_close') }}</span>
                                                        </div>
                                                        <div class="flex space-x-3">
                                                            <button type="button"
                                                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium transition-colors duration-200"
                                                                @click="closeModal">
                                                                {{ __('ui.cancel') }}
                                                            </button>
                                                            <button type="button"
                                                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors duration-200"
                                                                @click="submitRefund" :disabled="!selectedFile || isLoading">
                                                                <span x-show="!isLoading">{{ __('ui.confirm_refund') }}</span>
                                                                <span x-show="isLoading" class="flex items-center">
                                                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                                        xmlns="http://www.w3.org/2000/svg"
                                                                        fill="none" viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12"
                                                                            cy="12" r="10"
                                                                            stroke="currentColor" stroke-width="4">
                                                                        </circle>
                                                                        <path class="opacity-75" fill="currentColor"
                                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                        </path>
                                                                    </svg>
                                                                    {{ __('ui.processing') }}
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <!-- Status sudah refunded - tampilkan badge -->
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-100 text-green-700 text-sm font-medium">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                            </svg>
                                            {{ __('ui.refunded') }}
                                        </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-lg font-medium text-gray-600">{{ __('ui.no_refund_requests') }}</p>
                                        <p class="text-gray-500 mt-1">{{ __('ui.refund_requests_appear_here') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($refunds->hasPages())
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    {{ $refunds->appends(request()->except('page'))->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('refundModal', (modalId) => ({
                isOpen: false,
                idBooking: '',
                orderIdDisplay: '',
                selectedFile: null,
                imagePreviewUrl: null,
                isDragOver: false,
                isLoading: false,
                modalId: modalId, // ID unik untuk modal

                openModal(idBooking, orderId) {
                    this.isOpen = true;
                    this.idBooking = idBooking;
                    this.orderIdDisplay = orderId;
                    this.selectedFile = null;
                    this.imagePreviewUrl = null;
                    this.isDragOver = false;
                    this.isLoading = false;
                    document.body.style.overflow = 'hidden';
                },

                closeModal() {
                    this.isOpen = false;
                    this.idBooking = '';
                    this.orderIdDisplay = '';
                    this.selectedFile = null;
                    this.imagePreviewUrl = null;
                    this.isDragOver = false;
                    this.isLoading = false;
                    document.body.style.overflow = '';
                },

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.validateAndSetFile(file);
                    } else {
                        this.selectedFile = null;
                        this.imagePreviewUrl = null;
                    }
                },

                handleDrop(event) {
                    event.preventDefault();
                    this.isDragOver = false;

                    const files = event.dataTransfer.files;
                    if (files.length > 0) {
                        this.validateAndSetFile(files[0]);
                    } else {
                        this.selectedFile = null;
                        this.imagePreviewUrl = null;
                    }
                },

                validateAndSetFile(file) {
                    // Validasi tipe file
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                    if (!allowedTypes.includes(file.type)) {
                        this.showAlert('error',
                            '{{ __('ui.unsupported_file_type') }}');
                        this.selectedFile = null;
                        this.imagePreviewUrl = null;
                        return;
                    }

                    // Validasi ukuran file (max 5MB)
                    const maxSize = 5 * 1024 * 1024;
                    if (file.size > maxSize) {
                        this.showAlert('error', '{{ __('ui.file_too_large') }}');
                        this.selectedFile = null;
                        this.imagePreviewUrl = null;
                        return;
                    }

                    this.selectedFile = file;

                    // Generate preview URL
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreviewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removeFile() {
                    this.selectedFile = null;
                    this.imagePreviewUrl = null;
                    const fileInput = document.getElementById('refund_image-' + this.modalId);
                    if (fileInput) {
                        fileInput.value = '';
                    }
                },

                formatFileSize(bytes) {
                    if (!bytes || bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },

                async submitRefund() {
                    if (!this.selectedFile) {
                        this.showAlert('error', '{{ __('ui.select_refund_file') }}');
                        return;
                    }

                    if (!this.idBooking) {
                        this.showAlert('error', '{{ __('ui.booking_id_not_found') }}');
                        return;
                    }

                    this.isLoading = true;

                    const form = document.getElementById('refund-form-' + this.modalId);
                    const formData = new FormData();

                    // Tambahkan data ke FormData
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                        .content);
                    formData.append('order_id', this.idBooking);
                    formData.append('refund_image', this.selectedFile);

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (response.ok) {
                            this.showAlert('success', data.message ||
                                '{{ __('ui.refund_confirmed_success') }}');
                            this.closeModal();

                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message ||
                                '{{ __('ui.error_processing_refund') }}');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        this.showAlert('error', error.message ||
                            '{{ __('ui.error_processing_refund') }}');
                    } finally {
                        this.isLoading = false;
                    }
                },

                showAlert(type, message) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: type,
                            title: message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    } else {
                        // Fallback alert
                        alert(`${type.toUpperCase()}: ${message}`);
                    }
                }
            }));
        });
    </script>
</x-app-layout>
