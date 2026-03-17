{{-- <!-- Multi-Tier Pricing: Modal for managing room pricing rules (weekday, weekend, holiday, seasons) -->
     <!-- Uses Alpine.js for interactivity, fetches/saves rules via AJAX to RoomPricingRulesController -->
     <!-- Sections: Base Prices (weekday/weekend), Seasons (high/low), Holidays, Calendar Preview --> --}}

<div x-data="pricingRulesModal({{ $room->idrec }})" x-cloak>
    {{-- Trigger Button --}}
    <button @click="openModal()"
        class="p-2 text-blue-600 hover:text-blue-900 transition-colors duration-200 rounded-full hover:bg-blue-50"
        title="Atur Harga Multi-Tier">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>

    {{-- Modal --}}
    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape="closeModal()">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isOpen" @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-5xl transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl">

                {{-- Header --}}
                <div class="flex items-center justify-between p-6 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Multi-Tier Pricing — <span x-text="roomName"></span>
                    </h3>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Content --}}
                <div class="p-6 max-h-[75vh] overflow-y-auto space-y-8">

                    {{-- Loading state --}}
                    <div x-show="loading" class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Memuat data...</p>
                    </div>

                    <div x-show="!loading">
                        {{-- ===== SECTION 1: Base Prices (Weekday / Weekend) ===== --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                <i class="fas fa-calendar-week mr-2 text-teal-600"></i>Harga Dasar
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Weekday --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Weekday (Sen-Jum)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="weekdayPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>
                                {{-- Weekend --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Weekend (Sab-Min)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="weekendPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>
                            </div>
                            <button @click="saveBasePrice()" class="mt-3 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-save mr-1"></i> Simpan Harga Dasar
                            </button>
                        </div>

                        {{-- ===== SECTION 2: Seasons (High / Low) ===== --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    <i class="fas fa-sun mr-2 text-orange-500"></i>Musim (High / Low Season)
                                </h4>
                                <button @click="showSeasonForm = !showSeasonForm" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                            </div>

                            {{-- Add season form --}}
                            <div x-show="showSeasonForm" x-transition class="bg-white dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-600 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tipe</label>
                                        <select x-model="newSeason.rule_type" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                            <option value="high_season">High Season</option>
                                            <option value="low_season">Low Season</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Label</label>
                                        <input type="text" x-model="newSeason.label" placeholder="e.g. Lebaran 2026" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                                        <input type="date" x-model="newSeason.date_start" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                                        <input type="date" x-model="newSeason.date_end" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Harga/Malam</label>
                                        <input type="number" x-model="newSeason.price" placeholder="Rp" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <button @click="addRule(newSeason)" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg transition">Simpan</button>
                                    <button @click="showSeasonForm = false; resetSeasonForm()" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">Batal</button>
                                </div>
                            </div>

                            {{-- Season rules table --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm" x-show="seasonRules.length > 0">
                                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-600">
                                        <tr>
                                            <th class="pb-2">Tipe</th>
                                            <th class="pb-2">Label</th>
                                            <th class="pb-2">Mulai</th>
                                            <th class="pb-2">Selesai</th>
                                            <th class="pb-2">Harga</th>
                                            <th class="pb-2 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="rule in seasonRules" :key="rule.idrec">
                                            <tr class="border-b dark:border-gray-600">
                                                <td class="py-2">
                                                    <span x-text="rule.rule_type === 'high_season' ? 'High' : 'Low'"
                                                        :class="rule.rule_type === 'high_season' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'"
                                                        class="px-2 py-0.5 rounded-full text-xs font-medium"></span>
                                                </td>
                                                <td class="py-2 text-gray-700 dark:text-gray-300" x-text="rule.label || '-'"></td>
                                                <td class="py-2 text-gray-600 dark:text-gray-400" x-text="rule.date_start"></td>
                                                <td class="py-2 text-gray-600 dark:text-gray-400" x-text="rule.date_end"></td>
                                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200" x-text="'Rp ' + Number(rule.price).toLocaleString('id-ID')"></td>
                                                <td class="py-2 text-right">
                                                    <button @click="deleteRule(rule.idrec)" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <p x-show="seasonRules.length === 0" class="text-gray-400 text-sm text-center py-4">Belum ada aturan musim</p>
                            </div>
                        </div>

                        {{-- ===== SECTION 3: Holidays ===== --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                                    <i class="fas fa-star mr-2 text-yellow-500"></i>Hari Libur Nasional
                                </h4>
                                <button @click="showHolidayForm = !showHolidayForm" class="px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded-lg transition">
                                    <i class="fas fa-plus mr-1"></i> Tambah
                                </button>
                            </div>

                            {{-- Add holiday form --}}
                            <div x-show="showHolidayForm" x-transition class="bg-white dark:bg-gray-800 p-4 rounded-lg border dark:border-gray-600 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Nama Hari Libur</label>
                                        <input type="text" x-model="newHoliday.label" placeholder="e.g. Idul Fitri" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Mulai</label>
                                        <input type="date" x-model="newHoliday.date_start" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Tanggal Selesai</label>
                                        <input type="date" x-model="newHoliday.date_end" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Harga/Malam</label>
                                        <input type="number" x-model="newHoliday.price" placeholder="Rp" class="w-full py-2 px-3 border dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                                    </div>
                                </div>
                                <div class="flex gap-2 mt-3">
                                    <button @click="addRule({...newHoliday, rule_type: 'holiday'})" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-lg transition">Simpan</button>
                                    <button @click="showHolidayForm = false; resetHolidayForm()" class="px-3 py-1.5 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded-lg transition">Batal</button>
                                </div>
                            </div>

                            {{-- Holiday rules table --}}
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm" x-show="holidayRules.length > 0">
                                    <thead class="text-left text-gray-500 dark:text-gray-400 border-b dark:border-gray-600">
                                        <tr>
                                            <th class="pb-2">Nama</th>
                                            <th class="pb-2">Mulai</th>
                                            <th class="pb-2">Selesai</th>
                                            <th class="pb-2">Harga</th>
                                            <th class="pb-2 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="rule in holidayRules" :key="rule.idrec">
                                            <tr class="border-b dark:border-gray-600">
                                                <td class="py-2 text-gray-700 dark:text-gray-300" x-text="rule.label || '-'"></td>
                                                <td class="py-2 text-gray-600 dark:text-gray-400" x-text="rule.date_start"></td>
                                                <td class="py-2 text-gray-600 dark:text-gray-400" x-text="rule.date_end"></td>
                                                <td class="py-2 font-medium text-gray-800 dark:text-gray-200" x-text="'Rp ' + Number(rule.price).toLocaleString('id-ID')"></td>
                                                <td class="py-2 text-right">
                                                    <button @click="deleteRule(rule.idrec)" class="text-red-500 hover:text-red-700 text-xs"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                                <p x-show="holidayRules.length === 0" class="text-gray-400 text-sm text-center py-4">Belum ada hari libur</p>
                            </div>
                        </div>

                        {{-- ===== SECTION 4: Color Legend ===== --}}
                        <div class="flex flex-wrap gap-3 text-xs">
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-400"></span> Weekday</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-purple-400"></span> Weekend</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-400"></span> High Season</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-400"></span> Low Season</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-orange-400"></span> Holiday</span>
                            <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-400"></span> Manual</span>
                        </div>

                    </div>
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between p-6 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                    <button @click="regeneratePrices()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        <i class="fas fa-sync-alt mr-1"></i> Regenerate Semua Harga
                    </button>
                    <button @click="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- <!-- Alpine.js component for pricing rules modal --> --}}
<script>
function pricingRulesModal(roomId) {
    return {
        roomId: roomId,
        roomName: '',
        isOpen: false,
        loading: false,
        weekdayPrice: 0,
        weekendPrice: 0,
        allRules: [],
        showSeasonForm: false,
        showHolidayForm: false,
        newSeason: { rule_type: 'high_season', label: '', date_start: '', date_end: '', price: '' },
        newHoliday: { label: '', date_start: '', date_end: '', price: '' },

        /* Computed: filter season rules from all rules */
        get seasonRules() {
            return this.allRules.filter(r => r.rule_type === 'high_season' || r.rule_type === 'low_season');
        },
        get holidayRules() {
            return this.allRules.filter(r => r.rule_type === 'holiday');
        },

        async openModal() {
            this.isOpen = true;
            this.loading = true;
            await this.loadRules();
            this.loading = false;
        },
        closeModal() { this.isOpen = false; },

        /* Fetch all pricing rules from server */
        async loadRules() {
            try {
                const res = await fetch(`/rooms/${this.roomId}/pricing-rules`);
                const json = await res.json();
                if (json.status === 'success') {
                    this.roomName = json.data.room_name;
                    this.weekdayPrice = json.data.price_weekday || 0;
                    this.weekendPrice = json.data.price_weekend || 0;
                    this.allRules = json.data.rules || [];
                }
            } catch (e) { console.error('Failed to load rules:', e); }
        },

        /* Save weekday + weekend base prices */
        async saveBasePrice() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            /* Save weekday */
            await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ rule_type: 'weekday', price: this.weekdayPrice })
            });
            /* Save weekend */
            await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ rule_type: 'weekend', price: this.weekendPrice })
            });
            alert('Harga dasar berhasil disimpan & harga per-tanggal di-regenerate.');
            await this.loadRules();
        },

        /* Add a new rule (season or holiday) */
        async addRule(ruleData) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            try {
                const res = await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify(ruleData)
                });
                const json = await res.json();
                if (json.status === 'success') {
                    this.showSeasonForm = false;
                    this.showHolidayForm = false;
                    this.resetSeasonForm();
                    this.resetHolidayForm();
                    await this.loadRules();
                } else {
                    alert('Error: ' + (json.message || 'Gagal menyimpan'));
                }
            } catch (e) { alert('Network error'); }
        },

        /* Delete a rule */
        async deleteRule(ruleId) {
            if (!confirm('Hapus aturan harga ini?')) return;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/rooms/${this.roomId}/pricing-rules/${ruleId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            await this.loadRules();
        },

        /* Regenerate all per-date prices */
        async regeneratePrices() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`/rooms/${this.roomId}/regenerate-prices`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const json = await res.json();
            alert(json.message || 'Selesai');
        },

        resetSeasonForm() { this.newSeason = { rule_type: 'high_season', label: '', date_start: '', date_end: '', price: '' }; },
        resetHolidayForm() { this.newHoliday = { label: '', date_start: '', date_end: '', price: '' }; },
    };
}
</script>
