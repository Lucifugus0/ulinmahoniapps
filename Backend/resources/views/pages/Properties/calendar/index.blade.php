{{-- <!-- Master Calendar: Global date classification management page -->
     <!-- Manages holidays, high seasons, low seasons that apply to ALL rooms -->
     <!-- Uses Alpine.js for calendar interactivity, AJAX for CRUD operations --> --}}
<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="calendarManager()">
        <style>
            /* Calendar cell colors — light mode */
            .cal-regular { background-color: rgba(59, 130, 246, 0.15) !important; }
            .cal-weekend { background-color: rgba(139, 92, 246, 0.20) !important; }
            .cal-high-season { background-color: rgba(239, 68, 68, 0.20) !important; }
            .cal-low-season { background-color: rgba(34, 197, 94, 0.20) !important; }
            .cal-holiday { background-color: rgba(249, 115, 22, 0.20) !important; }
            /* Calendar cell colors — dark mode: brighter and more vivid */
            html.dark .cal-regular { background-color: rgba(96, 165, 250, 0.35) !important; }
            html.dark .cal-weekend { background-color: rgba(167, 139, 250, 0.40) !important; }
            html.dark .cal-high-season { background-color: rgba(248, 113, 113, 0.45) !important; }
            html.dark .cal-low-season { background-color: rgba(74, 222, 128, 0.45) !important; }
            html.dark .cal-holiday { background-color: rgba(251, 146, 60, 0.50) !important; }
        </style>

        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <h1 class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                {{ __('ui.calendar_title') }}
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Calendar Grid -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <!-- Month Navigation -->
                <div class="flex items-center justify-between mb-4">
                    <button @click="prevMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <h2 class="text-lg font-semibold text-gray-800" x-text="monthNames[currentMonth] + ' ' + currentYear"></h2>
                    <button @click="nextMonth()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>

                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    <template x-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']">
                        <div class="text-center text-xs font-medium text-gray-500 py-1" x-text="day"></div>
                    </template>
                </div>

                <!-- Calendar Days -->
                <div class="grid grid-cols-7 gap-1">
                    <template x-for="(cell, index) in calendarCells" :key="index">
                        <div
                            @click="cell.day && selectDate(cell)"
                            :class="{
                                'cursor-pointer hover:ring-2 hover:ring-blue-400': cell.day,
                                'cal-high-season': cell.type === 'high_season',
                                'cal-low-season': cell.type === 'low_season',
                                'cal-holiday': cell.type === 'holiday',
                                'cal-weekend': !cell.type && cell.isWeekend,
                                'cal-regular': !cell.type && !cell.isWeekend && cell.day,
                                'ring-2 ring-blue-500': cell.dateStr === selectedDate,
                            }"
                            class="relative p-2 rounded-lg text-center min-h-[3.5rem] transition-all">
                            <span class="text-sm font-medium" x-text="cell.day || ''"
                                :class="cell.day ? 'text-gray-800' : 'text-transparent'"></span>
                            <div x-show="cell.type" class="text-[10px] leading-tight mt-0.5 truncate font-semibold text-white"
                                x-text="cell.label || (cell.type === 'high_season' ? 'High' : cell.type === 'low_season' ? 'Low' : 'Holiday')">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap gap-3 mt-4 text-xs">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded cal-regular"></span> {{ __('ui.calendar_regular') }}</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded cal-weekend"></span> {{ __('ui.calendar_weekend') }}</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded cal-high-season"></span> {{ __('ui.calendar_high_season') }}</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded cal-low-season"></span> {{ __('ui.calendar_low_season') }}</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded cal-holiday"></span> {{ __('ui.calendar_holiday') }}</span>
                </div>
            </div>

            <!-- Active Entries — separate card below calendar -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2 text-indigo-500"></i>{{ __('ui.calendar_active_entries') }}
                    </h3>
                    <!-- Show all entries checkbox -->
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" x-model="showAllEntries" @change="fetchListEntries()" class="rounded border-gray-300 text-teal-600 focus:ring-teal-500">
                        {{ __('ui.calendar_show_all') }}
                    </label>
                </div>
                <div class="space-y-2">
                    <template x-for="entry in groupedListEntries" :key="entry.key">
                        <div class="p-3 rounded-lg text-sm"
                            :class="{
                                'cal-high-season': entry.isActive && entry.date_type === 'high_season',
                                'cal-low-season': entry.isActive && entry.date_type === 'low_season',
                                'cal-holiday': entry.isActive && entry.date_type === 'holiday',
                                'bg-gray-600/40': !entry.isActive,
                            }">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-white" x-text="entry.label || entry.date_type.replace('_', ' ')"></span>
                                    <span class="text-xs text-white/80" x-text="entry.dateRange"></span>
                                    <span x-show="!entry.isActive" class="text-xs bg-red-500/80 text-white px-1.5 py-0.5 rounded">({{ __('ui.calendar_inactive') }})</span>
                                </div>
                                {{-- Toggle active/inactive button for each entry --}}
                                <button @click.stop="toggleEntryStatus(entry)"
                                    class="text-xs px-2.5 py-1 rounded-md font-medium transition-colors"
                                    :class="entry.isActive
                                        ? 'bg-red-500/30 text-red-200 hover:bg-red-500/50'
                                        : 'bg-green-500/30 text-green-200 hover:bg-green-500/50'"
                                    x-text="entry.isActive ? '{{ __('ui.deactivate') ?? 'Nonaktifkan' }}' : '{{ __('ui.activate') ?? 'Aktifkan' }}'">
                                </button>
                            </div>
                            {{-- Created by + date --}}
                            <div class="text-xs text-white/70 mt-1">
                                <span x-text="'{{ __('ui.calendar_created_by') }}: ' + entry.createdBy + (entry.createdDate ? ' — ' + entry.createdDate : '')"></span>
                            </div>
                            {{-- Deactivated by + date (only for inactive entries) --}}
                            <div x-show="!entry.isActive && entry.updatedBy" class="text-xs text-red-300 mt-0.5">
                                <span x-text="'{{ __('ui.calendar_deactivated_by') }}: ' + entry.updatedBy + (entry.updatedDate ? ' — ' + entry.updatedDate : '')"></span>
                            </div>
                        </div>
                    </template>
                    <p x-show="groupedListEntries.length === 0" class="text-gray-400 text-sm text-center py-4">{{ __('ui.calendar_no_entries') }}</p>
                </div>
            </div>

            <!-- Right Panel: Add Range + Entry List -->
            <div class="space-y-6">
                <!-- Add Date Range Form -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-plus-circle mr-2 text-teal-600"></i>{{ __('ui.calendar_add_range') }}
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.calendar_date_type') }}</label>
                            <select x-model="newEntry.date_type" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="holiday">{{ __('ui.calendar_holiday') }}</option>
                                <option value="high_season">{{ __('ui.calendar_high_season') }}</option>
                                <option value="low_season">{{ __('ui.calendar_low_season') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.calendar_label') }}</label>
                            <input type="text" x-model="newEntry.label" minlength="5" placeholder="e.g. Idul Fitri, Natal, Lebaran" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.calendar_date_start') }}</label>
                                <input type="date" x-model="newEntry.date_start" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.calendar_date_end') }}</label>
                                <input type="date" x-model="newEntry.date_end" class="w-full py-2 px-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500">
                            </div>
                        </div>
                        <button @click="storeRange()" :disabled="saving"
                            class="w-full px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition disabled:opacity-50">
                            <span x-show="!saving"><i class="fas fa-save mr-1"></i> {{ __('ui.save') }}</span>
                            <span x-show="saving">{{ __('ui.facility_saving') }}</span>
                        </button>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <script>
    function calendarManager() {
        return {
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            entries: [],
            listEntries: [],
            showAllEntries: false,
            selectedDate: null,
            saving: false,
            regenerating: false,
            newEntry: { date_type: 'holiday', label: '', date_start: '', date_end: '' },

            /* Initialize: fetch calendar and list entries */
            init() { this.fetchEntries(); this.fetchListEntries(); },

            /* Navigate months */
            prevMonth() {
                if (this.currentMonth === 0) { this.currentMonth = 11; this.currentYear--; }
                else { this.currentMonth--; }
                this.fetchEntries();
            },
            nextMonth() {
                if (this.currentMonth === 11) { this.currentMonth = 0; this.currentYear++; }
                else { this.currentMonth++; }
                this.fetchEntries();
            },

            /* Fetch entries for current month (calendar grid — active only) */
            async fetchEntries() {
                try {
                    const res = await fetch(`/properties/calendar/entries?year=${this.currentYear}&month=${this.currentMonth + 1}&mode=calendar`);
                    const json = await res.json();
                    this.entries = json.data || [];
                } catch (e) { console.error('Failed to fetch calendar entries:', e); }
            },

            /* Fetch entries for the list view (from today onwards, or all) */
            async fetchListEntries() {
                try {
                    const res = await fetch(`/properties/calendar/entries?mode=list&show_all=${this.showAllEntries ? '1' : '0'}`);
                    const json = await res.json();
                    this.listEntries = json.data || [];
                } catch (e) { console.error('Failed to fetch list entries:', e); }
            },

            /* Build calendar grid cells for the current month */
            get calendarCells() {
                const firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                const cells = [];

                // Empty cells before first day
                for (let i = 0; i < firstDay; i++) {
                    cells.push({ day: null, type: null, label: null, isWeekend: false, dateStr: null });
                }

                // Day cells
                for (let d = 1; d <= daysInMonth; d++) {
                    const dateObj = new Date(this.currentYear, this.currentMonth, d);
                    const dateStr = `${this.currentYear}-${String(this.currentMonth + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    // Weekend = Friday (5) + Saturday (6)
                    const isWeekend = dateObj.getDay() === 5 || dateObj.getDay() === 6;

                    // Find matching entry — compare date portion only (API may return ISO datetime)
                    const entry = this.entries.find(e => e.date.substring(0, 10) === dateStr);
                    cells.push({
                        day: d,
                        type: entry ? entry.date_type : null,
                        label: entry ? entry.label : null,
                        isWeekend,
                        dateStr,
                    });
                }

                return cells;
            },

            /* Format date string to readable format (e.g. "24 Mar 2026") */
            formatDate(dateStr) {
                const d = new Date(dateStr + 'T00:00:00');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
            },

            /* Group consecutive list entries by type+label+status for the active entries card */
            get groupedListEntries() {
                if (!this.listEntries.length) return [];
                const sorted = [...this.listEntries].sort((a, b) => a.date.localeCompare(b.date));
                const groups = [];
                let current = null;

                for (const e of sorted) {
                    const dateOnly = e.date.substring(0, 10);
                    const createdBy = e.creator_name || 'System';
                    const updatedBy = e.updater_name || null;
                    const createdDate = e.created_date || null;
                    const updatedDate = e.updated_date || null;
                    const isActive = e.status === 1;

                    if (current && current.date_type === e.date_type && current.label === e.label && current.isActive === isActive) {
                        current.endDate = dateOnly;
                    } else {
                        if (current) groups.push(current);
                        current = { date_type: e.date_type, label: e.label, startDate: dateOnly, endDate: dateOnly, key: e.idrec, createdBy, updatedBy, createdDate, updatedDate, isActive };
                    }
                }
                if (current) groups.push(current);

                return groups.map(g => ({
                    ...g,
                    dateRange: g.startDate === g.endDate
                        ? this.formatDate(g.startDate)
                        : `${this.formatDate(g.startDate)} — ${this.formatDate(g.endDate)}`,
                }));
            },

            selectDate(cell) {
                this.selectedDate = cell.dateStr;
                // Pre-fill the form with clicked date
                if (!this.newEntry.date_start) this.newEntry.date_start = cell.dateStr;
                if (!this.newEntry.date_end) this.newEntry.date_end = cell.dateStr;
            },

            /* Save a new date range */
            async storeRange() {
                if (!this.newEntry.date_start || !this.newEntry.date_end || !this.newEntry.date_type) {
                    alert('Harap isi tanggal mulai, selesai, dan tipe.');
                    return;
                }
                if (!this.newEntry.label || this.newEntry.label.length < 5) {
                    alert('Label harus minimal 5 karakter.');
                    return;
                }
                this.saving = true;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const res = await fetch('/properties/calendar/store', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify(this.newEntry)
                    });
                    const json = await res.json();
                    if (json.status === 'success') {
                        this.newEntry = { date_type: 'holiday', label: '', date_start: '', date_end: '' };
                        await this.fetchEntries();
                        await this.fetchListEntries();
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Error', text: json.message || 'Gagal menyimpan' });
                    }
                } catch (e) { alert('Network error'); }
                this.saving = false;
            },

            /* Toggle active/inactive status for an entry group */
            async toggleEntryStatus(entry) {
                const newStatus = !entry.isActive;
                const action = newStatus ? 'mengaktifkan' : 'menonaktifkan';
                if (!confirm(`Yakin ingin ${action} "${entry.label}"?`)) return;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    // Toggle ALL entries with this type + label (not just visible range)
                    const res = await fetch('/properties/calendar/toggle-status', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({
                            date_type: entry.date_type,
                            label: entry.label,
                            status: newStatus ? 1 : 0,
                        })
                    });
                    const json = await res.json();

                    // Show all entries after deactivation so user can see the result
                    if (!newStatus) {
                        this.showAllEntries = true;
                    }

                    // Refresh both calendar and list
                    await Promise.all([this.fetchEntries(), this.fetchListEntries()]);

                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 2000 });
                } catch (e) { alert('Network error'); }
            },

            /* Regenerate prices for all rooms */
            async regenerateAll() {
                if (!confirm('Regenerate harga untuk SEMUA kamar? Ini mungkin memerlukan waktu.')) return;
                this.regenerating = true;
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                try {
                    const res = await fetch('/properties/calendar/regenerate-all', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    const json = await res.json();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: json.message, showConfirmButton: false, timer: 3000 });
                } catch (e) { alert('Network error'); }
                this.regenerating = false;
            },
        };
    }
    </script>
</x-app-layout>
