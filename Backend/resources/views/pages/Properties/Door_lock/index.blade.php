<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.door_lock_management') }}
            </h1>
            <div class="mt-4 md:mt-0">
                <button type="button" id="btnTambahDoorLock"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ __('ui.add_door_lock') }}
                </button>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Search and Filter -->
            <div class="p-4 border-b border-gray-200">
                <form id="searchForm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="w-full md:w-1/3">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                    placeholder="{{ __('ui.search_door_lock_placeholder') }}">
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <label for="perPageSelect" class="text-sm text-gray-600">{{ __('ui.show') }}:</label>
                            <select name="per_page" id="perPageSelect"
                                class="border-gray-200 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="8" {{ request('per_page', 8) == 8 ? 'selected' : '' }}>8</option>
                                <option value="25" {{ request('per_page', 8) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ request('per_page', 8) == 50 ? 'selected' : '' }}>50</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto" id="tableContainer">
                @include('pages.Properties.Door_lock.partials.door-lock_table', ['doorLocks' => $doorLocks])
            </div>

            <!-- Pagination -->
            <div class="bg-gray-50 rounded p-4" id="paginationContainer">
                {{ $doorLocks->appends(request()->input())->links() }}
            </div>
        </div>
    </div>

    <!-- ==================== MODAL: TAMBAH DOOR LOCK ==================== -->
    <div id="modalAddDoorLock" class="fixed inset-0 z-50 hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" id="backdropAddDoorLock"></div>

        <!-- Modal Content -->
        <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-lg border border-gray-100 relative z-10">
                <!-- Header -->
                <div class="px-5 py-4 bg-gradient-to-r from-blue-100 to-indigo-100 border-b flex items-center justify-between rounded-t-xl">
                    <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.add_door_lock') }}</h3>
                    <button type="button" id="closeModalAddDoorLock" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-5 py-4 space-y-4">
                    <!-- Step 1: Pilih Kamar & Input Lock ID -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.select_room') }} <span class="text-red-500">*</span></label>
                        <select id="addDoorLockRoom"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">{{ __('ui.select_room_placeholder') }}</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->idrec }}">
                                    [{{ $room->no }}] {{ $room->name }} - {{ $room->property_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.lock_id') }} <span class="text-red-500">*</span></label>
                        <div class="flex gap-2">
                            <input type="number" id="addDoorLockId" placeholder="{{ __('ui.lock_id_placeholder') }}"
                                class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 font-mono" />
                            <button type="button" id="btnGetLockData"
                                class="px-4 py-2 text-sm bg-gray-700 hover:bg-gray-800 text-white rounded-md shadow transition whitespace-nowrap">
                                <span id="btnGetLockDataText">{{ __('ui.get_data') }}</span>
                                <span id="btnGetLockDataLoading" class="hidden">
                                    <svg class="animate-spin h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Loading...
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Hasil dari API (hidden until fetched) -->
                    <div id="lockDetailsSection" class="hidden space-y-3 border border-blue-100 bg-blue-50 rounded-lg p-4">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">{{ __('ui.lock_detail_from_api') }}</p>

                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.lock_alias') }}</span>
                                <p id="detailLockAlias" class="font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.model') }}</span>
                                <p id="detailModelNum" class="font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.mac_address') }}</span>
                                <p id="detailLockMac" class="font-mono text-xs text-gray-800">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.firmware') }}</span>
                                <p id="detailFirmware" class="font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.battery') }}</span>
                                <p id="detailBattery" class="font-medium text-gray-800">-</p>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500">{{ __('ui.gateway') }}</span>
                                <p id="detailGateway" class="font-medium text-gray-800">-</p>
                            </div>
                        </div>

                        <!-- Lock Alias (editable) -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">{{ __('ui.lock_alias_label') }}</label>
                            <input type="text" id="addLockAlias"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="{{ __('ui.lock_alias_placeholder') }}" />
                        </div>
                    </div>

                    <!-- Hidden inputs for storing API result -->
                    <input type="hidden" id="hiddenLockId" />
                    <input type="hidden" id="hiddenLockMac" />
                    <input type="hidden" id="hiddenModelNum" />
                    <input type="hidden" id="hiddenFirmwareRevision" />
                    <input type="hidden" id="hiddenBatteryLevel" />
                    <input type="hidden" id="hiddenHasGateway" />
                    <input type="hidden" id="hiddenLockSound" />
                    <input type="hidden" id="hiddenPrivacyLock" />
                    <input type="hidden" id="hiddenIsFrozen" />
                    <input type="hidden" id="hiddenPassageMode" />
                    <input type="hidden" id="hiddenLastSyncAt" />
                </div>

                <!-- Footer -->
                <div class="px-5 py-4 border-t flex justify-end gap-2">
                    <button type="button" id="cancelAddDoorLock"
                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                        {{ __('ui.cancel') }}
                    </button>
                    <button type="button" id="btnSaveDoorLock" disabled
                        class="px-4 py-1.5 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow disabled:opacity-40 disabled:cursor-not-allowed transition">
                        {{ __('ui.save_door_lock') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL: ADD PASSCODE ==================== -->
    <div id="modalAddPasscode" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm" id="backdropAddPasscode"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4 sm:p-6">
            <div class="bg-white w-full max-w-md rounded-xl shadow-lg border border-gray-100 relative z-10">
                <!-- Header -->
                <div class="px-5 py-4 bg-gradient-to-r from-indigo-100 to-purple-100 border-b flex items-center justify-between rounded-t-xl">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800">{{ __('ui.add_passcode') }}</h3>
                        <p id="passcodeModalSubtitle" class="text-xs text-gray-500 mt-0.5"></p>
                    </div>
                    <button type="button" id="closeModalAddPasscode" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 8.586l4.95-4.95a1 1 0 011.414 1.414L11.414 10l4.95 4.95a1 1 0 01-1.414 1.414L10 11.414l-4.95 4.95a1 1 0 01-1.414-1.414L8.586 10l-4.95-4.95a1 1 0 011.414-1.414L10 8.586z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div class="px-5 py-4 space-y-4">
                    <input type="hidden" id="passcodeLockIdrec" />

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.passcode_name') }} <span class="text-red-500">*</span></label>
                        <input type="text" id="passcodeName" placeholder="{{ __('ui.passcode_name_placeholder') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.valid_from') }} <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="passcodeStartDate"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.valid_until') }} <span class="text-red-500">*</span></label>
                            <input type="datetime-local" id="passcodeEndDate"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>
                    </div>

                    <!-- Result display -->
                    <div id="passcodeResultSection" class="hidden border border-green-100 bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-xs text-green-700 font-semibold uppercase tracking-wide mb-1">{{ __('ui.passcode_created_success') }}</p>
                        <p id="passcodeResultValue" class="text-3xl font-bold font-mono text-green-700 tracking-widest"></p>
                    </div>
                </div>

                <div class="px-5 py-4 border-t flex justify-end gap-2">
                    <button type="button" id="cancelAddPasscode"
                        class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-md bg-white hover:bg-gray-50">
                        {{ __('ui.close') }}
                    </button>
                    <button type="button" id="btnGeneratePasscode"
                        class="inline-flex items-center gap-2 px-4 py-1.5 text-sm text-white bg-indigo-600 hover:bg-indigo-700 rounded-md shadow transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd" />
                        </svg>
                        <span id="btnGeneratePasscodeText">{{ __('ui.generate_passcode') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // ===================== UTILS =====================
        function showToast(icon, title) {
            Swal.fire({ toast: true, position: 'top-end', icon, title, showConfirmButton: false, timer: 3000 });
        }

        // ===================== SEARCH / FILTER =====================
        function applyDoorLockFilters() {
            const search = document.getElementById('searchInput').value;
            const perPage = document.getElementById('perPageSelect').value;
            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (perPage) params.append('per_page', perPage);

            fetch(`{{ route('door-locks.index') }}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const newTable = doc.querySelector('#tableContainer table');
                const curTable = document.querySelector('#tableContainer table');
                if (newTable && curTable) curTable.innerHTML = newTable.innerHTML;

                const newPage = doc.getElementById('paginationContainer');
                const curPage = document.getElementById('paginationContainer');
                if (newPage && curPage) curPage.innerHTML = newPage.innerHTML;

                window.history.pushState({}, '', `${window.location.pathname}?${params.toString()}`);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            let searchTimeout;
            document.getElementById('searchInput').addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(applyDoorLockFilters, 500);
            });
            document.getElementById('perPageSelect').addEventListener('change', applyDoorLockFilters);

            // ===================== ADD DOOR LOCK MODAL =====================
            const modalAdd = document.getElementById('modalAddDoorLock');
            const lockDetailsSection = document.getElementById('lockDetailsSection');
            const btnSave = document.getElementById('btnSaveDoorLock');
            let fetchedLockData = null;

            function openAddModal() {
                modalAdd.classList.remove('hidden');
                fetchedLockData = null;
                lockDetailsSection.classList.add('hidden');
                btnSave.disabled = true;
                document.getElementById('addDoorLockRoom').value = '';
                document.getElementById('addDoorLockId').value = '';
                document.getElementById('addLockAlias').value = '';
            }

            function closeAddModal() {
                modalAdd.classList.add('hidden');
            }

            document.getElementById('btnTambahDoorLock').addEventListener('click', openAddModal);
            document.getElementById('closeModalAddDoorLock').addEventListener('click', closeAddModal);
            document.getElementById('cancelAddDoorLock').addEventListener('click', closeAddModal);
            document.getElementById('backdropAddDoorLock').addEventListener('click', closeAddModal);

            // Get Data from API
            document.getElementById('btnGetLockData').addEventListener('click', async function () {
                const lockId = document.getElementById('addDoorLockId').value.trim();
                if (!lockId) { showToast('warning', '{{ __('ui.enter_lock_id_first') }}'); return; }

                document.getElementById('btnGetLockDataText').classList.add('hidden');
                document.getElementById('btnGetLockDataLoading').classList.remove('hidden');
                this.disabled = true;

                try {
                    const res = await fetch('{{ route('door-locks.get-details') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ lock_id: lockId })
                    });

                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        showToast('error', json.message || '{{ __('ui.failed_fetch_lock') }}');
                        return;
                    }

                    fetchedLockData = json.data;

                    // Populate display
                    document.getElementById('detailLockAlias').textContent = fetchedLockData.lock_alias || '-';
                    document.getElementById('detailModelNum').textContent = fetchedLockData.model_num || '-';
                    document.getElementById('detailLockMac').textContent = fetchedLockData.lock_mac || '-';
                    document.getElementById('detailFirmware').textContent = fetchedLockData.firmware_revision || '-';
                    document.getElementById('detailBattery').textContent = fetchedLockData.battery_level != null ? fetchedLockData.battery_level + '%' : '-';
                    document.getElementById('detailGateway').textContent = fetchedLockData.has_gateway ? '{{ __('ui.gateway_yes') }}' : '{{ __('ui.gateway_no') }}';
                    document.getElementById('addLockAlias').value = fetchedLockData.lock_alias || '';

                    // Set hidden fields
                    document.getElementById('hiddenLockId').value = fetchedLockData.lock_id;
                    document.getElementById('hiddenLockMac').value = fetchedLockData.lock_mac || '';
                    document.getElementById('hiddenModelNum').value = fetchedLockData.model_num || '';
                    document.getElementById('hiddenFirmwareRevision').value = fetchedLockData.firmware_revision || '';
                    document.getElementById('hiddenBatteryLevel').value = fetchedLockData.battery_level ?? '';
                    document.getElementById('hiddenHasGateway').value = fetchedLockData.has_gateway ? '1' : '0';
                    document.getElementById('hiddenLockSound').value = fetchedLockData.lock_sound ?? '';
                    document.getElementById('hiddenPrivacyLock').value = fetchedLockData.privacy_lock ?? '';
                    document.getElementById('hiddenIsFrozen').value = fetchedLockData.is_frozen ?? '';
                    document.getElementById('hiddenPassageMode').value = fetchedLockData.passage_mode ?? '';
                    document.getElementById('hiddenLastSyncAt').value = fetchedLockData.last_sync_at ?? '';

                    lockDetailsSection.classList.remove('hidden');
                    btnSave.disabled = false;
                    showToast('success', '{{ __('ui.lock_data_fetched_success') }}');

                } catch (err) {
                    console.error(err);
                    showToast('error', '{{ __('ui.door_lock_fetch_error') }}');
                } finally {
                    document.getElementById('btnGetLockDataText').classList.remove('hidden');
                    document.getElementById('btnGetLockDataLoading').classList.add('hidden');
                    this.disabled = false;
                }
            });

            // Save Door Lock
            document.getElementById('btnSaveDoorLock').addEventListener('click', async function () {
                const roomIdrec = document.getElementById('addDoorLockRoom').value;
                if (!roomIdrec) { showToast('warning', '{{ __('ui.select_room_first') }}'); return; }
                if (!fetchedLockData) { showToast('warning', '{{ __('ui.fetch_lock_data_first') }}'); return; }

                this.disabled = true;
                this.textContent = '{{ __('ui.saving') }}';

                try {
                    const payload = {
                        room_idrec:        roomIdrec,
                        lock_id:           document.getElementById('hiddenLockId').value,
                        lock_alias:        document.getElementById('addLockAlias').value,
                        lock_mac:          document.getElementById('hiddenLockMac').value,
                        model_num:         document.getElementById('hiddenModelNum').value,
                        firmware_revision: document.getElementById('hiddenFirmwareRevision').value,
                        battery_level:     document.getElementById('hiddenBatteryLevel').value || null,
                        has_gateway:       document.getElementById('hiddenHasGateway').value,
                        lock_sound:        document.getElementById('hiddenLockSound').value || null,
                        privacy_lock:      document.getElementById('hiddenPrivacyLock').value || null,
                        is_frozen:         document.getElementById('hiddenIsFrozen').value || null,
                        passage_mode:      document.getElementById('hiddenPassageMode').value || null,
                        last_sync_at:      document.getElementById('hiddenLastSyncAt').value || null,
                    };

                    const res = await fetch('{{ route('door-locks.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });

                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        let msg = json.message || '{{ __('ui.door_lock_save_failed') }}';
                        if (json.errors) msg = Object.values(json.errors).flat().join('\n');
                        showToast('error', msg);
                        return;
                    }

                    closeAddModal();
                    showToast('success', '{{ __('ui.door_lock_added') }}');
                    setTimeout(() => window.location.reload(), 1200);

                } catch (err) {
                    console.error(err);
                    showToast('error', '{{ __('ui.door_lock_save_error') }}');
                } finally {
                    this.disabled = false;
                    this.textContent = '{{ __('ui.save_door_lock') }}';
                }
            });

            // ===================== ADD PASSCODE MODAL =====================
            const modalPasscode = document.getElementById('modalAddPasscode');

            function closePasscodeModal() {
                modalPasscode.classList.add('hidden');
                document.getElementById('passcodeName').value = '';
                document.getElementById('passcodeStartDate').value = '';
                document.getElementById('passcodeEndDate').value = '';
                document.getElementById('passcodeResultSection').classList.add('hidden');
                document.getElementById('passcodeResultValue').textContent = '';
                document.getElementById('btnGeneratePasscodeText').textContent = '{{ __('ui.generate_passcode') }}';
                document.getElementById('btnGeneratePasscode').disabled = false;
            }

            document.getElementById('closeModalAddPasscode').addEventListener('click', closePasscodeModal);
            document.getElementById('cancelAddPasscode').addEventListener('click', closePasscodeModal);
            document.getElementById('backdropAddPasscode').addEventListener('click', closePasscodeModal);

            document.getElementById('btnGeneratePasscode').addEventListener('click', async function () {
                const idrec = document.getElementById('passcodeLockIdrec').value;
                const name = document.getElementById('passcodeName').value.trim();
                const startRaw = document.getElementById('passcodeStartDate').value;
                const endRaw = document.getElementById('passcodeEndDate').value;

                if (!name) { showToast('warning', '{{ __('ui.passcode_name_required') }}'); return; }
                if (!startRaw || !endRaw) { showToast('warning', '{{ __('ui.passcode_period_required') }}'); return; }

                const startMs = new Date(startRaw).getTime();
                const endMs = new Date(endRaw).getTime();

                if (endMs <= startMs) { showToast('warning', '{{ __('ui.passcode_end_after_start') }}'); return; }

                this.disabled = true;
                document.getElementById('btnGeneratePasscodeText').textContent = '{{ __('ui.generating') }}';

                try {
                    const res = await fetch(`/properties/rooms/door-locks/${idrec}/passcode`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ name, start_periode: startMs, end_periode: endMs })
                    });

                    const json = await res.json();

                    if (!res.ok || !json.success) {
                        showToast('error', json.message || '{{ __('ui.passcode_failed') }}');
                        return;
                    }

                    document.getElementById('passcodeResultValue').textContent = json.passcode;
                    document.getElementById('passcodeResultSection').classList.remove('hidden');
                    document.getElementById('btnGeneratePasscodeText').textContent = '{{ __('ui.regenerate') }}';
                    showToast('success', '{{ __('ui.passcode_created_success') }}');
                    setTimeout(() => window.location.reload(), 2000);

                } catch (err) {
                    console.error(err);
                    showToast('error', '{{ __('ui.passcode_error') }}');
                } finally {
                    this.disabled = false;
                    if (!document.getElementById('passcodeResultSection').classList.contains('hidden') === false) {
                        document.getElementById('btnGeneratePasscodeText').textContent = '{{ __('ui.generate_passcode') }}';
                    }
                }
            });
        });

        // ===================== GLOBAL FUNCTIONS (called from table) =====================
        function openAddPasscodeModal(lock) {
            document.getElementById('passcodeLockIdrec').value = lock.id;
            document.getElementById('passcodeModalSubtitle').textContent =
                `{{ __('ui.lock_id') }}: ${lock.lock_id}${lock.lock_alias ? ' — ' + lock.lock_alias : ''}`;
            document.getElementById('passcodeResultSection').classList.add('hidden');
            document.getElementById('passcodeName').value = '';
            document.getElementById('passcodeStartDate').value = '';
            document.getElementById('passcodeEndDate').value = '';
            document.getElementById('btnGeneratePasscodeText').textContent = '{{ __('ui.generate_passcode') }}';
            document.getElementById('btnGeneratePasscode').disabled = false;
            document.getElementById('modalAddPasscode').classList.remove('hidden');
        }

        function deleteDoorLock(id) {
            Swal.fire({
                title: '{{ __('ui.delete_door_lock') }}',
                text: '{{ __('ui.delete_door_lock_confirm') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __('ui.yes') }}, {{ __('ui.delete') }}',
                cancelButtonText: '{{ __('ui.cancel') }}'
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch(`/properties/rooms/door-locks/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ __('ui.door_lock_deleted') }}', showConfirmButton: false, timer: 2000 });
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: json.message || '{{ __('ui.door_lock_delete_failed') }}', showConfirmButton: false, timer: 3000 });
                    }
                });
            });
        }
    </script>
</x-app-layout>
