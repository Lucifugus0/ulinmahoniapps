<div x-data="{
    sortColumn: 'tanggal',
    sortDirection: 'desc',
    sortTable(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'desc';
        }
        this.performSort();
    },
    performSort() {
        const tbody = document.getElementById('transactionTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr[data-sortable]'));

        rows.sort((a, b) => {
            // Always place 'waiting' status at the top
            const aIsWaiting = a.dataset.status === 'waiting';
            const bIsWaiting = b.dataset.status === 'waiting';

            if (aIsWaiting && !bIsWaiting) return -1;
            if (!aIsWaiting && bIsWaiting) return 1;

            // If both have same waiting status, sort by column
            let aVal = a.dataset[this.sortColumn] || '';
            let bVal = b.dataset[this.sortColumn] || '';

            // Handle date sorting
            if (this.sortColumn === 'tanggal' || this.sortColumn === 'checkin') {
                aVal = aVal ? new Date(aVal).getTime() : 0;
                bVal = bVal ? new Date(bVal).getTime() : 0;
            } else {
                aVal = aVal.toLowerCase();
                bVal = bVal.toLowerCase();
            }

            if (this.sortDirection === 'asc') {
                return aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
            } else {
                return aVal < bVal ? 1 : aVal > bVal ? -1 : 0;
            }
        });

        rows.forEach(row => tbody.appendChild(row));
    },
    init() {
        this.$nextTick(() => this.performSort());
    }
}">
<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                @click="sortTable('tanggal')">
                <div class="flex items-center gap-1">
                    Tanggal
                    <span class="text-gray-400">
                        <template x-if="sortColumn === 'tanggal' && sortDirection === 'asc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </template>
                        <template x-if="sortColumn === 'tanggal' && sortDirection === 'desc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </template>
                        <template x-if="sortColumn !== 'tanggal'">
                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </template>
                    </span>
                </div>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                ID Pesanan</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                @click="sortTable('pelanggan')">
                <div class="flex items-center gap-1">
                    Pelanggan
                    <span class="text-gray-400">
                        <template x-if="sortColumn === 'pelanggan' && sortDirection === 'asc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </template>
                        <template x-if="sortColumn === 'pelanggan' && sortDirection === 'desc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </template>
                        <template x-if="sortColumn !== 'pelanggan'">
                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </template>
                    </span>
                </div>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                @click="sortTable('property')">
                <div class="flex items-center gap-1">
                    Property
                    <span class="text-gray-400">
                        <template x-if="sortColumn === 'property' && sortDirection === 'asc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </template>
                        <template x-if="sortColumn === 'property' && sortDirection === 'desc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </template>
                        <template x-if="sortColumn !== 'property'">
                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </template>
                    </span>
                </div>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Jumlah</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 select-none"
                @click="sortTable('metode')">
                <div class="flex items-center gap-1">
                    Metode
                    <span class="text-gray-400">
                        <template x-if="sortColumn === 'metode' && sortDirection === 'asc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                        </template>
                        <template x-if="sortColumn === 'metode' && sortDirection === 'desc'">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </template>
                        <template x-if="sortColumn !== 'metode'">
                            <svg class="w-4 h-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        </template>
                    </span>
                </div>
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tgl Pembayaran</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tgl Check in</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Notes</th>
            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                Aksi</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200" id="transactionTableBody">
        @forelse ($payments as $payment)
            @if (!$payment->transaction || $payment->transaction->transaction_status === 'expired')
                @continue
            @endif
            <tr data-sortable
                data-tanggal="{{ optional($payment->transaction?->created_at)->format('Y-m-d') }}"
                data-pelanggan="{{ $payment->transaction?->user?->username ?? '' }}"
                data-property="{{ $payment->transaction?->property?->name ?? '' }}"
                data-metode="{{ $payment->transaction?->transaction_type ?? '' }}"
                data-status="{{ $payment->transaction?->transaction_status ?? '' }}">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <div class="text-sm text-gray-500">
                        {{ optional($payment->transaction?->created_at)->format('Y-m-d') ?? '-' }}
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                    <div class="text-sm font-medium text-indigo-600">{{ $payment->order_id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $payment->transaction?->user?->username ?? '-' }}</div>
                            <div class="text-sm text-gray-500">{{ $payment->transaction?->user?->email ?? '-' }}
                            </div>
                            <div class="text-sm text-gray-500">{{ $payment->transaction?->user_phone_number ?? '-' }}
                            </div>
                        </div>
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div class="text-sm font-medium text-gray-900">
                        {{ $payment->transaction?->property?->name ?? 'N/A' }}</div>
                    <span class="text-xs text-gray-400">{{ $payment->transaction?->room?->name ?? '-' }}</span>
                    @if($payment->transaction?->room?->no ?? null)
                        <div class="text-xs text-gray-400">No. {{ $payment->transaction->room->no }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    Rp{{ number_format($payment->transaction?->grandtotal_price ?? 0, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $payment->transaction?->transaction_type ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    <div class="flex items-center gap-2">
                        <div>
                            @if ($payment?->transaction?->paid_at)
                                {{ $payment->transaction->paid_at->format('Y-m-d') }}
                            @elseif ($payment?->verified_at)
                                {{ $payment->verified_at->format('Y-m-d') }}
                            @else
                                -
                            @endif
                        </div>
                        @if ($payment?->transaction?->paid_at || $payment?->verified_at)
                            <button type="button"
                                onclick="showEditPaymentDateModal({{ $payment->idrec }}, '{{ ($payment->transaction?->paid_at ?? $payment->verified_at)->format('Y-m-d\TH:i') }}', '{{ $payment->transaction?->check_in ? $payment->transaction->check_in->format('Y-m-d\TH:i') : '' }}')"
                                class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                title="Edit Tanggal Pembayaran">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">
                    <div class="flex items-center gap-2">
                        <div>
                            @if ($payment->transaction?->check_in)
                                {{ $payment->transaction->check_in->format('Y-m-d') }}
                            @else
                                -
                            @endif
                        </div>
                        @if ($payment->transaction?->check_in)
                            <button type="button"
                                onclick="showEditCheckInOutModal({{ $payment->idrec }}, '{{ $payment->transaction->check_in->format('Y-m-d\TH:i') }}', '{{ $payment->transaction->check_out ? $payment->transaction->check_out->format('Y-m-d\TH:i') : '' }}')"
                                class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                title="Edit Tanggal Check-in/Check-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    @php
                        $status = $payment->transaction?->transaction_status ?? 'unknown';
                        $statusStyles = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'waiting' => 'bg-orange-100 text-orange-800',
                            'paid' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-200 text-red-900',
                            'canceled' => 'bg-pink-100 text-pink-800',
                            'failed' => 'bg-rose-100 text-rose-800',
                            'expired' => 'bg-gray-300 text-gray-800',
                        ];

                        $badgeStyle = $statusStyles[$status] ?? 'bg-gray-100 text-gray-800';
                        $hasTooltip = $status === 'rejected' && isset($payment->notes);
                    @endphp

                    <div class="relative inline-block group">
                        <span
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeStyle }} {{ $hasTooltip ? 'cursor-help' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>

                        @if ($hasTooltip)
                            <div
                                class="absolute z-10 w-64 p-2 mt-1 text-xs text-gray-600 bg-white border border-gray-300 rounded shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform -translate-x-1/2 left-1/2">
                                {{ $payment->notes }}
                            </div>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500" x-data="{
                    editing: false,
                    notes: '{{ addslashes($payment->notes ?? '') }}',
                    originalNotes: '{{ addslashes($payment->notes ?? '') }}',
                    saving: false,
                    saveNotes() {
                        this.saving = true;
                        fetch('{{ route('admin.payments.update-notes', $payment->idrec) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ notes: this.notes })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.originalNotes = this.notes;
                                this.editing = false;
                                // Success notification
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Notes berhasil diperbarui',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                throw new Error(data.message || 'Gagal menyimpan notes');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: 'Gagal!',
                                text: error.message || 'Gagal menyimpan notes',
                                icon: 'error',
                                confirmButtonColor: '#dc2626'
                            });
                        })
                        .finally(() => {
                            this.saving = false;
                        });
                    },
                    cancelEdit() {
                        this.notes = this.originalNotes;
                        this.editing = false;
                    }
                }">
                    <div class="max-w-xs">
                        <template x-if="!editing">
                            <div class="flex items-start gap-2 group">
                                <p class="text-sm text-gray-700 flex-1 break-words"
                                   x-text="notes || '-'"
                                   :class="notes ? '' : 'text-gray-400 italic'"></p>
                                <button @click="editing = true"
                                        class="opacity-0 group-hover:opacity-100 transition-opacity text-blue-600 hover:text-blue-800 flex-shrink-0"
                                        title="Edit Notes">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            </div>
                        </template>
                        <template x-if="editing">
                            <div class="space-y-2">
                                <textarea x-model="notes"
                                          rows="3"
                                          class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                                          placeholder="Masukkan catatan..."
                                          :disabled="saving"></textarea>
                                <div class="flex gap-2">
                                    <button @click="saveNotes()"
                                            :disabled="saving"
                                            class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-1">
                                        <template x-if="saving">
                                            <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </template>
                                        <span x-text="saving ? 'Menyimpan...' : 'Simpan'"></span>
                                    </button>
                                    <button @click="cancelEdit()"
                                            :disabled="saving"
                                            class="px-3 py-1 text-xs bg-gray-300 text-gray-700 rounded hover:bg-gray-400 disabled:opacity-50 disabled:cursor-not-allowed">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                    @if ($payment->transaction && in_array($payment->transaction->transaction_status, ['waiting']))
                        <div x-data="{
                            isOpen: false,
                            isLoading: true,
                            attachmentData: '',
                            attachmentType: 'unknown',
                            mimeType: 'image/jpeg',
                            orderId: '',
                            openModal(paymentId, orderId) {
                                this.isOpen = true;
                                this.isLoading = true;
                                this.orderId = orderId;
                                document.body.style.overflow = 'hidden';
                                fetch(`/api/v1/booking/${paymentId}`, {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success && data.attachment) {
                                        const raw = data.attachment;
                                        this.attachmentData = raw;
                                        if (raw.startsWith('/9j/')) {
                                            this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                        } else if (raw.startsWith('iVBORw0KGgo')) {
                                            this.attachmentType = 'image'; this.mimeType = 'image/png';
                                        } else if (raw.startsWith('R0lGOD')) {
                                            this.attachmentType = 'image'; this.mimeType = 'image/gif';
                                        } else if (raw.startsWith('JVBERi0')) {
                                            this.attachmentType = 'pdf'; this.mimeType = 'application/pdf';
                                        } else {
                                            this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                        }
                                    } else {
                                        this.attachmentType = 'unknown';
                                    }
                                    this.isLoading = false;
                                })
                                .catch(() => {
                                    this.attachmentType = 'unknown';
                                    this.isLoading = false;
                                });
                            },
                            closeModal() {
                                this.isOpen = false;
                                this.attachmentData = '';
                                this.attachmentType = 'unknown';
                                this.orderId = '';
                                document.body.style.overflow = '';
                            }
                        }" class="relative group">
                            <button type="button"
                                class="flex items-center gap-2 text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 px-4 py-2 rounded-lg transition-all duration-200 ease-in-out shadow-sm hover:shadow-md"
                                @click="openModal({{ $payment->idrec }}, '{{ $payment->order_id }}')"
                                title="Konfirmasi Pembayaran">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                                <span class="text-sm font-semibold">Konfirmasi</span>
                            </button>

                            <!-- Modal backdrop -->
                            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                x-show="isOpen" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                x-transition:leave="transition ease-out duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                aria-hidden="true" x-cloak>
                            </div>

                            <!-- Modal dialog -->
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
                                            {{ __('ui.payment_proof') }} — #<span x-text="orderId"></span>
                                        </h3>
                                        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Modal content -->
                                    <div class="overflow-y-auto flex-1 p-6">
                                        <template x-if="isLoading">
                                            <div class="flex justify-center items-center h-64">
                                                <svg class="animate-spin h-12 w-12 text-blue-500"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </template>

                                        <template x-if="!isLoading && attachmentType === 'image'">
                                            <img :src="'data:' + mimeType + ';base64,' + attachmentData"
                                                alt="{{ __('ui.payment_proof') }}"
                                                class="mx-auto max-h-[70vh] max-w-full object-contain">
                                        </template>

                                        <template x-if="!isLoading && attachmentType === 'pdf'">
                                            <div class="h-[70vh] w-full">
                                                <iframe :src="'data:application/pdf;base64,' + attachmentData"
                                                    class="w-full h-full border border-gray-200"
                                                    frameborder="0"></iframe>
                                            </div>
                                        </template>

                                        <template x-if="!isLoading && attachmentType === 'unknown'">
                                            <div class="text-center py-10">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('ui.unsupported_file') }}</h3>
                                                <p class="mt-2 text-sm text-gray-500">{{ __('ui.file_cannot_be_displayed') }}</p>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Modal footer dengan reject modal -->
                                    <div
                                        class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                                        <div class="text-sm text-gray-500">
                                            <span>Tekan ESC atau klik di luar untuk menutup</span>
                                        </div>
                                        <div class="flex space-x-3">
                                            <!-- Tombol Approve -->
                                            <form id="approve-form-{{ $payment->idrec }}"
                                                action="{{ route('admin.payments.approve', $payment->idrec) }}"
                                                method="POST">
                                                @csrf
                                                <button type="button"
                                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium"
                                                    onclick="confirmApprove({{ $payment->idrec }})">
                                                    Setujui
                                                </button>
                                            </form>

                                            <!-- Tombol Tolak -->
                                            <button type="button"
                                                class="flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium"
                                                onclick="showRejectModal({{ $payment->idrec }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                    stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                <span>Tolak</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif ($payment->transaction && $payment->transaction->transaction_status === 'pending')
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Menunggu
                        </span>
                        <!-- Tambahkan di bagian aksi untuk status terverifikasi -->
                    @elseif (
                        $payment->transaction &&
                            in_array($payment->transaction->transaction_status, ['paid', 'completed']) &&
                            empty($payment->booking?->check_out_at))
                        <div class="flex flex-col items-center text-center space-y-2">
                            <!-- Tombol Batalkan Booking -->
                            <button type="button" onclick="showCancelModal({{ $payment->idrec }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batalkan Booking
                            </button>

                            <!-- Status Terverifikasi -->
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Terverifikasi
                            </span>

                            <!-- Informasi Verifikasi -->
                            <div class="text-xs text-gray-500">
                                Oleh:
                                {{ $payment->verifiedBy->username ?? 'DOKU' }}
                            </div>

                            <!-- Lihat Bukti Pembayaran -->
                            @if ($payment->transaction && $payment->transaction->attachment)
                            <div x-data="{
                                isOpen: false,
                                isLoading: true,
                                attachmentData: '',
                                attachmentType: 'unknown',
                                mimeType: 'image/jpeg',
                                orderId: '',
                                openModal(paymentId, orderId) {
                                    this.isOpen = true;
                                    this.isLoading = true;
                                    this.orderId = orderId;
                                    document.body.style.overflow = 'hidden';
                                    fetch(`/api/v1/booking/${paymentId}`, {
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success && data.attachment) {
                                            const raw = data.attachment;
                                            this.attachmentData = raw;
                                            if (raw.startsWith('/9j/')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                            } else if (raw.startsWith('iVBORw0KGgo')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/png';
                                            } else if (raw.startsWith('R0lGOD')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/gif';
                                            } else if (raw.startsWith('JVBERi0')) {
                                                this.attachmentType = 'pdf'; this.mimeType = 'application/pdf';
                                            } else {
                                                this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                            }
                                        } else {
                                            this.attachmentType = 'unknown';
                                        }
                                        this.isLoading = false;
                                    })
                                    .catch(() => {
                                        this.attachmentType = 'unknown';
                                        this.isLoading = false;
                                    });
                                },
                                closeModal() {
                                    this.isOpen = false;
                                    this.attachmentData = '';
                                    this.attachmentType = 'unknown';
                                    this.orderId = '';
                                    document.body.style.overflow = '';
                                }
                            }" class="relative">
                                <button type="button"
                                    class="text-xs text-blue-600 underline hover:text-blue-800 cursor-pointer"
                                    @click="openModal({{ $payment->idrec }}, '{{ $payment->order_id }}')">
                                    {{ __('ui.view_proof') }}
                                </button>

                                <!-- Backdrop -->
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-out duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    aria-hidden="true" x-cloak>
                                </div>

                                <!-- Modal -->
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

                                        <!-- Header -->
                                        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                {{ __('ui.payment_proof') }} — #<span x-text="orderId"></span>
                                            </h3>
                                            <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="overflow-y-auto flex-1 p-6">
                                            <template x-if="isLoading">
                                                <div class="flex justify-center items-center h-64">
                                                    <svg class="animate-spin h-12 w-12 text-blue-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'image'">
                                                <img :src="'data:' + mimeType + ';base64,' + attachmentData"
                                                    alt="{{ __('ui.payment_proof') }}"
                                                    class="mx-auto max-h-[70vh] max-w-full object-contain">
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'pdf'">
                                                <div class="h-[70vh] w-full">
                                                    <iframe :src="'data:application/pdf;base64,' + attachmentData"
                                                        class="w-full h-full border border-gray-200"
                                                        frameborder="0"></iframe>
                                                </div>
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'unknown'">
                                                <div class="text-center py-10">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('ui.unsupported_file') }}</h3>
                                                    <p class="mt-2 text-sm text-gray-500">{{ __('ui.file_cannot_be_displayed') }}</p>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Footer -->
                                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Tekan ESC atau klik di luar untuk menutup</span>
                                            <button type="button" @click="closeModal"
                                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Modal Pembatalan Booking - Improved Design -->
                        <div id="cancelModal-{{ $payment->idrec }}"
                            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-[70]"
                            style="display: none;" onclick="hideCancelModal({{ $payment->idrec }})">
                            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                                <div class="relative mx-auto w-full max-w-2xl" onclick="event.stopPropagation()">
                                    <div class="relative bg-white rounded-lg shadow-2xl transform transition-all">
                                        <!-- Modal header -->
                                        <div
                                            class="px-6 py-4 border-b rounded-t bg-gradient-to-r from-orange-50 to-red-50">
                                            <h3 class="text-xl font-semibold text-gray-900">
                                                Batalkan Booking
                                            </h3>
                                            <button type="button" onclick="hideCancelModal({{ $payment->idrec }})"
                                                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors duration-200">
                                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg"
                                                    fill="none" viewBox="0 0 14 14">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                </svg>
                                                <span class="sr-only">Tutup modal</span>
                                            </button>
                                        </div>

                                        <!-- Modal body (dipertahankan dari versi sebelumnya) -->
                                        <form id="cancel-form-{{ $payment->idrec }}"
                                            action="{{ route('admin.bookings.cancel', $payment->idrec) }}"
                                            method="POST"
                                            onsubmit="return validateCancelForm(event, {{ $payment->idrec }})"
                                            class="max-w-full overflow-hidden">
                                            @csrf
                                            @method('PUT')

                                            <div class="p-6 space-y-4 break-words whitespace-normal">
                                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                                    <div class="flex">
                                                        <div class="flex-shrink-0">
                                                            <svg class="h-5 w-5 text-yellow-400"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h3 class="text-sm font-medium text-yellow-800">
                                                                Peringatan
                                                            </h3>
                                                            <div class="mt-2 text-sm text-yellow-700">
                                                                <p>Pembatalan booking akan mengembalikan dana kepada
                                                                    pelanggan dan membatalkan reservasi.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="border border-gray-200 rounded-lg p-4">
                                                    <h4 class="text-sm font-medium text-gray-900 mb-2">Detail Booking
                                                    </h4>
                                                    <div class="grid grid-cols-2 gap-2 text-sm text-gray-600">
                                                        <div>Order ID:</div>
                                                        <div class="font-medium">{{ $payment->order_id }}</div>

                                                        <div>Pelanggan:</div>
                                                        <div class="font-medium">
                                                            {{ $payment->transaction?->user?->username ?? '-' }}</div>

                                                        <div>Properti:</div>
                                                        <div class="font-medium">
                                                            {{ $payment->transaction?->property?->name ?? 'N/A' }}
                                                        </div>

                                                        <div>Total:</div>
                                                        <div class="font-medium">
                                                            Rp{{ number_format($payment->transaction?->grandtotal_price ?? 0, 0, ',', '.') }}
                                                        </div>

                                                        <div>Check-in:</div>
                                                        <div class="font-medium">
                                                            {{ $payment->transaction?->check_in?->format('d M Y') ?? '-' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="w-full">
                                                    <label for="cancelReason-{{ $payment->idrec }}"
                                                        class="block text-sm font-medium text-gray-700 mb-2">
                                                        Alasan Pembatalan <span class="text-red-500">*</span>
                                                    </label>
                                                    <select id="cancelReason-{{ $payment->idrec }}"
                                                        name="cancelReason"
                                                        class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200"
                                                        onchange="toggleCustomReason({{ $payment->idrec }})" required>
                                                        <option value="">Pilih alasan pembatalan</option>
                                                        <option value="pelanggan_request">Permintaan Pelanggan</option>
                                                        <option value="ketersediaan_properti">Ketersediaan Properti
                                                            Berubah
                                                        </option>
                                                        <option value="masalah_teknis">Masalah Teknis</option>
                                                        <option value="pelanggan_melanggar_kebijakan">Pelanggan
                                                            Melanggar
                                                            Kebijakan</option>
                                                        <option value="other">Lainnya</option>
                                                    </select>
                                                </div>

                                                <div id="customReasonContainer-{{ $payment->idrec }}"
                                                    class="hidden w-full">
                                                    <label for="customCancelReason-{{ $payment->idrec }}"
                                                        class="block text-sm font-medium text-gray-700 mb-2">
                                                        Jelaskan Alasan Lainnya <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea id="customCancelReason-{{ $payment->idrec }}" name="customCancelReason" rows="3"
                                                        class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200 resize-none break-words whitespace-normal"
                                                        placeholder="Jelaskan alasan pembatalan secara detail..."></textarea>
                                                </div>

                                                <div class="w-full">
                                                    <label for="refundAmount-{{ $payment->idrec }}"
                                                        class="block text-sm font-medium text-gray-700 mb-2">
                                                        Jumlah Pengembalian Dana
                                                    </label>
                                                    <div class="mt-1">
                                                        <div class="flex rounded-md shadow-sm">
                                                            <span
                                                                class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                                                Rp
                                                            </span>
                                                            <input type="text"
                                                                id="refundAmount-{{ $payment->idrec }}"
                                                                name="refundAmount"
                                                                value="{{ number_format($payment->transaction?->grandtotal_price ?? 0, 0, ',', '.') }}"
                                                                class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm">
                                                        </div>
                                                    </div>
                                                    <p class="mt-1 text-xs text-gray-500">Atur jumlah yang akan
                                                        dikembalikan
                                                        kepada pelanggan</p>
                                                </div>

                                                <div class="flex items-center">
                                                    <input id="sendNotification-{{ $payment->idrec }}"
                                                        name="sendNotification" type="checkbox" checked
                                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                                    <label for="sendNotification-{{ $payment->idrec }}"
                                                        class="ml-2 block text-sm text-gray-700">
                                                        Kirim notifikasi pembatalan ke pelanggan
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Modal footer -->
                                            <div
                                                class="flex items-center justify-end p-6 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                                                <button type="button"
                                                    onclick="hideCancelModal({{ $payment->idrec }})"
                                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors duration-200">
                                                    Batal
                                                </button>
                                                <button type="submit"
                                                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Konfirmasi Pembatalan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center text-center space-y-2">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Terverifikasi
                            </span>
                            <div class="text-xs text-gray-500">
                                Oleh:
                                {{ $payment->verifiedBy->name ?? 'DOKU' }}
                            </div>

                            <!-- Lihat Bukti Pembayaran -->
                            @if ($payment->transaction && $payment->transaction->attachment)
                            <div x-data="{
                                isOpen: false,
                                isLoading: true,
                                attachmentData: '',
                                attachmentType: 'unknown',
                                mimeType: 'image/jpeg',
                                orderId: '',
                                openModal(paymentId, orderId) {
                                    this.isOpen = true;
                                    this.isLoading = true;
                                    this.orderId = orderId;
                                    document.body.style.overflow = 'hidden';
                                    fetch(`/api/v1/booking/${paymentId}`, {
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success && data.attachment) {
                                            const raw = data.attachment;
                                            this.attachmentData = raw;
                                            if (raw.startsWith('/9j/')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                            } else if (raw.startsWith('iVBORw0KGgo')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/png';
                                            } else if (raw.startsWith('R0lGOD')) {
                                                this.attachmentType = 'image'; this.mimeType = 'image/gif';
                                            } else if (raw.startsWith('JVBERi0')) {
                                                this.attachmentType = 'pdf'; this.mimeType = 'application/pdf';
                                            } else {
                                                this.attachmentType = 'image'; this.mimeType = 'image/jpeg';
                                            }
                                        } else {
                                            this.attachmentType = 'unknown';
                                        }
                                        this.isLoading = false;
                                    })
                                    .catch(() => {
                                        this.attachmentType = 'unknown';
                                        this.isLoading = false;
                                    });
                                },
                                closeModal() {
                                    this.isOpen = false;
                                    this.attachmentData = '';
                                    this.attachmentType = 'unknown';
                                    this.orderId = '';
                                    document.body.style.overflow = '';
                                }
                            }" class="relative">
                                <button type="button"
                                    class="text-xs text-blue-600 underline hover:text-blue-800 cursor-pointer"
                                    @click="openModal({{ $payment->idrec }}, '{{ $payment->order_id }}')">
                                    {{ __('ui.view_proof') }}
                                </button>

                                <!-- Backdrop -->
                                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity"
                                    x-show="isOpen"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-out duration-200"
                                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                    aria-hidden="true" x-cloak>
                                </div>

                                <!-- Modal -->
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

                                        <!-- Header -->
                                        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-blue-50 to-indigo-50">
                                            <h3 class="text-lg font-semibold text-gray-800">
                                                {{ __('ui.payment_proof') }} — #<span x-text="orderId"></span>
                                            </h3>
                                            <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Content -->
                                        <div class="overflow-y-auto flex-1 p-6">
                                            <template x-if="isLoading">
                                                <div class="flex justify-center items-center h-64">
                                                    <svg class="animate-spin h-12 w-12 text-blue-500"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'image'">
                                                <img :src="'data:' + mimeType + ';base64,' + attachmentData"
                                                    alt="{{ __('ui.payment_proof') }}"
                                                    class="mx-auto max-h-[70vh] max-w-full object-contain">
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'pdf'">
                                                <div class="h-[70vh] w-full">
                                                    <iframe :src="'data:application/pdf;base64,' + attachmentData"
                                                        class="w-full h-full border border-gray-200"
                                                        frameborder="0"></iframe>
                                                </div>
                                            </template>
                                            <template x-if="!isLoading && attachmentType === 'unknown'">
                                                <div class="text-center py-10">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="h-16 w-16 mx-auto text-gray-400" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <h3 class="mt-4 text-lg font-medium text-gray-900">{{ __('ui.unsupported_file') }}</h3>
                                                    <p class="mt-2 text-sm text-gray-500">{{ __('ui.file_cannot_be_displayed') }}</p>
                                                </div>
                                            </template>
                                        </div>

                                        <!-- Footer -->
                                        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Tekan ESC atau klik di luar untuk menutup</span>
                                            <button type="button" @click="closeModal"
                                                class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500">
                    Belum ada pembayaran terbaru
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
</div>

<!-- Reject Modals - Outside table for proper z-index handling -->
@foreach ($payments as $payment)
    @if ($payment->transaction && in_array($payment->transaction->transaction_status, ['waiting']))
        <div id="rejectModal-{{ $payment->idrec }}"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-[70]"
            style="display: none;" onclick="hideRejectModal({{ $payment->idrec }})">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="relative mx-auto w-full max-w-md" onclick="event.stopPropagation()">
                    <div class="relative bg-white rounded-lg shadow-2xl transform transition-all">
                        <!-- Modal header -->
                        <div class="px-6 py-4 border-b rounded-t bg-gradient-to-r from-red-50 to-pink-50">
                            <h3 class="text-xl font-semibold text-gray-900">
                                Tolak Pembayaran
                            </h3>
                            <button type="button" onclick="hideRejectModal({{ $payment->idrec }})"
                                class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors duration-200">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                </svg>
                                <span class="sr-only">Tutup modal</span>
                            </button>
                        </div>

                        <!-- Modal body -->
                        <form id="reject-form-{{ $payment->idrec }}"
                            action="{{ route('admin.payments.reject', $payment->idrec) }}" method="POST"
                            onsubmit="return validateRejectForm(event, {{ $payment->idrec }})"
                            class="max-w-full overflow-hidden">
                            @csrf

                            <div class="p-6 space-y-4 break-words whitespace-normal">
                                <p class="text-gray-600">
                                    Apakah Anda yakin ingin menolak pembayaran untuk Order ID:
                                    <strong class="text-gray-900">{{ $payment->order_id }}</strong>?
                                </p>

                                <div class="w-full">
                                    <label for="rejectNote-{{ $payment->idrec }}"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Alasan Penolakan <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="rejectNote-{{ $payment->idrec }}" name="rejectNote" rows="4"
                                        class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-colors duration-200 resize-none break-words whitespace-normal"
                                        placeholder="Masukkan alasan penolakan secara detail..." required></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter</p>
                                </div>
                            </div>

                            <!-- Modal footer -->
                            <div
                                class="flex items-center justify-end p-6 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                                <button type="button" onclick="hideRejectModal({{ $payment->idrec }})"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors duration-200">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Konfirmasi Penolakan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Modal Edit Payment Date -->
@foreach ($payments as $payment)
    <div id="editPaymentDateModal-{{ $payment->idrec }}"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-[70]"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative mx-auto w-full max-w-md" onclick="event.stopPropagation()">
                <div class="relative bg-white rounded-lg shadow-2xl transform transition-all">
                    <!-- Modal header -->
                    <div class="px-6 py-4 border-b rounded-t bg-gradient-to-r from-blue-50 to-indigo-50">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Edit Tanggal Pembayaran
                        </h3>
                        <button type="button" onclick="hideEditPaymentDateModal({{ $payment->idrec }})"
                            class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors duration-200">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <form id="edit-payment-date-form-{{ $payment->idrec }}"
                        action="{{ route('admin.payments.update-payment-date', $payment->idrec) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-6 space-y-4">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 flex items-start gap-2">
                                <svg class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-yellow-800">Tanggal pembayaran harus sebelum atau sama dengan
                                    tanggal check-in</p>
                            </div>

                            <div>
                                <label for="payment_date-{{ $payment->idrec }}"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Pembayaran <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="payment_date-{{ $payment->idrec }}"
                                    name="payment_date"
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                @if ($payment->transaction?->check_in)
                                    <p class="mt-1 text-xs text-gray-500">Maksimal:
                                        {{ $payment->transaction->check_in->format('d M Y H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                            <button type="button" onclick="hideEditPaymentDateModal({{ $payment->idrec }})"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Check-in/Check-out -->
    <div id="editCheckInOutModal-{{ $payment->idrec }}"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-[70]"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="relative mx-auto w-full max-w-md" onclick="event.stopPropagation()">
                <div class="relative bg-white rounded-lg shadow-2xl transform transition-all">
                    <!-- Modal header -->
                    <div class="px-6 py-4 border-b rounded-t bg-gradient-to-r from-green-50 to-teal-50">
                        <h3 class="text-xl font-semibold text-gray-900">
                            Edit Tanggal Check-in & Check-out
                        </h3>
                        <button type="button" onclick="hideEditCheckInOutModal({{ $payment->idrec }})"
                            class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center transition-colors duration-200">
                            <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal body -->
                    <form id="edit-checkinout-form-{{ $payment->idrec }}"
                        action="{{ route('admin.payments.update-checkinout', $payment->idrec) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="p-6 space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5"
                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm text-blue-800">Tanggal check-in harus sebelum atau sama dengan
                                    tanggal check-in saat ini. Tanggal check-out dapat dipilih bebas.</p>
                            </div>

                            <div>
                                <label for="check_in-{{ $payment->idrec }}"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Check-in <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="check_in-{{ $payment->idrec }}" name="check_in"
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    required>
                                @if ($payment->transaction?->check_in)
                                    <p class="mt-1 text-xs text-gray-500">Maksimal:
                                        {{ $payment->transaction->check_in->format('d M Y H:i') }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="check_out-{{ $payment->idrec }}"
                                    class="block text-sm font-medium text-gray-700 mb-2">
                                    Tanggal Check-out
                                </label>
                                <input type="datetime-local" id="check_out-{{ $payment->idrec }}" name="check_out"
                                    class="w-full px-3 py-2 text-gray-700 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <p class="mt-1 text-xs text-gray-500">Dapat memilih tanggal kapan saja</p>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div
                            class="flex items-center justify-end p-6 space-x-3 border-t border-gray-200 rounded-b bg-gray-50">
                            <button type="button" onclick="hideEditCheckInOutModal({{ $payment->idrec }})"
                                class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
