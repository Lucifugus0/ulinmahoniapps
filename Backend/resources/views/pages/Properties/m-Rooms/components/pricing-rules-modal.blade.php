{{-- <!-- Multi-Tier Pricing: Modal for managing room pricing rules -->
     <!-- Prices are per-room; date classifications (holiday, high_season, low_season) are global via Master Calendar -->
     <!-- Uses Alpine.js for interactivity, fetches/saves rules via AJAX to RoomPricingRulesController --> --}}

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
    <template x-teleport="body">
    <div x-show="isOpen" class="fixed inset-0 z-50 overflow-y-auto" @keydown.escape="closeModal()">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal()"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isOpen" @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative w-full max-w-3xl transform rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl">

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
                <div class="p-6 max-h-[75vh] overflow-y-auto space-y-6">

                    {{-- Loading state --}}
                    <div x-show="loading" class="text-center py-8">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal-600 mx-auto"></div>
                        <p class="mt-2 text-gray-500">Memuat data...</p>
                    </div>

                    <div x-show="!loading">
                        {{-- ===== SECTION 1: Base Prices (Weekday / Weekend) ===== --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                                <i class="fas fa-calendar-week mr-2 text-teal-600"></i>{{ __('ui.room_price_base') ?? 'Harga Dasar' }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Weekday price --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Weekday (Min-Kam)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="weekdayPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>
                                {{-- Weekend price --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Weekend (Jum-Sab)</label>
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

                        {{-- ===== SECTION 2: Season & Holiday Prices (price only — dates managed in Master Calendar) ===== --}}
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl">
                            <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                                <i class="fas fa-sun mr-2 text-orange-500"></i>Harga Musim & Hari Libur
                            </h4>
                            {{-- Info banner linking to Master Calendar --}}
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg text-sm text-blue-700 dark:text-blue-300">
                                <i class="fas fa-info-circle mr-1"></i>
                                Klasifikasi tanggal (hari libur, musim ramai/sepi) dikelola di
                                <a href="{{ route('calendar.index') }}" class="font-semibold underline hover:text-blue-900 dark:hover:text-blue-100">Master Calendar</a>.
                                Di sini Anda hanya mengatur <strong>harga</strong> per kamar untuk setiap tipe.
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                {{-- High Season price --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span class="inline-block w-3 h-3 rounded bg-red-400 mr-1"></span>High Season
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="highSeasonPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="Kosongkan = pakai harga dasar">
                                    </div>
                                </div>
                                {{-- Low Season price --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span class="inline-block w-3 h-3 rounded bg-green-400 mr-1"></span>Low Season
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="lowSeasonPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="Kosongkan = pakai harga dasar">
                                    </div>
                                </div>
                                {{-- Holiday price --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        <span class="inline-block w-3 h-3 rounded bg-orange-400 mr-1"></span>Hari Libur
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                                        <input type="number" x-model="holidayPrice"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="Kosongkan = pakai harga dasar">
                                    </div>
                                </div>
                            </div>
                            <button @click="saveSeasonPrices()" class="mt-3 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition">
                                <i class="fas fa-save mr-1"></i> Simpan Harga Musim & Libur
                            </button>
                        </div>

                        {{-- ===== SECTION 3: Color Legend ===== --}}
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
    </template>
</div>

{{-- <!-- Alpine.js component for pricing rules modal — prices only, no date management --> --}}
<script>
function pricingRulesModal(roomId) {
    return {
        roomId: roomId,
        roomName: '',
        isOpen: false,
        loading: false,
        weekdayPrice: 0,
        weekendPrice: 0,
        highSeasonPrice: '',
        lowSeasonPrice: '',
        holidayPrice: '',

        async openModal() {
            this.isOpen = true;
            this.loading = true;
            await this.loadRules();
            this.loading = false;
        },
        closeModal() { this.isOpen = false; },

        /* Fetch pricing rules from server — extract prices per type */
        async loadRules() {
            try {
                const res = await fetch(`/rooms/${this.roomId}/pricing-rules`);
                const json = await res.json();
                if (json.status === 'success') {
                    this.roomName = json.data.room_name;
                    this.weekdayPrice = json.data.price_weekday || 0;
                    this.weekendPrice = json.data.price_weekend || 0;

                    /* Extract per-type prices from rules array */
                    const rules = json.data.rules || [];
                    const highSeason = rules.find(r => r.rule_type === 'high_season');
                    const lowSeason = rules.find(r => r.rule_type === 'low_season');
                    const holiday = rules.find(r => r.rule_type === 'holiday');
                    this.highSeasonPrice = highSeason ? highSeason.price : '';
                    this.lowSeasonPrice = lowSeason ? lowSeason.price : '';
                    this.holidayPrice = holiday ? holiday.price : '';
                }
            } catch (e) { console.error('Failed to load rules:', e); }
        },

        /* Save weekday + weekend base prices */
        async saveBasePrice() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ rule_type: 'weekday', price: this.weekdayPrice })
            });
            await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ rule_type: 'weekend', price: this.weekendPrice })
            });
            alert('Harga dasar berhasil disimpan & harga per-tanggal di-regenerate.');
            await this.loadRules();
        },

        /* Save high season, low season, and holiday prices */
        async saveSeasonPrices() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            const types = [
                { rule_type: 'high_season', price: this.highSeasonPrice },
                { rule_type: 'low_season', price: this.lowSeasonPrice },
                { rule_type: 'holiday', price: this.holidayPrice },
            ];
            for (const t of types) {
                if (t.price !== '' && t.price !== null) {
                    await fetch(`/rooms/${this.roomId}/pricing-rules`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify(t)
                    });
                }
            }
            alert('Harga musim & libur berhasil disimpan & harga per-tanggal di-regenerate.');
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
    };
}
</script>
