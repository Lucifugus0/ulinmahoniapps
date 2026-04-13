{{-- Font Awesome icon picker — searchable dropdown with live preview.
     Uses Iconify search API (supports FA prefixes) and converts results
     to Font Awesome class format (fas fa-*, fab fa-*, far fa-*). --}}
@props(['model' => 'icon', 'name' => 'icon', 'placeholder' => 'fas fa-phone'])

@php
    /** Unique ID suffix to avoid collisions when multiple pickers are on the page */
    $uid = '_fip' . crc32($model . $name);
@endphp

<div x-data="{
    {{ $uid }}Open: false,
    {{ $uid }}Query: '',
    {{ $uid }}Icons: [],
    {{ $uid }}Loading: false,
    {{ $uid }}Timer: null,

    /** Preset common footer icons shown when picker opens with empty search */
    {{ $uid }}Presets: [
        'fas fa-map-marker-alt', 'fab fa-whatsapp', 'fas fa-phone', 'fas fa-envelope',
        'fab fa-instagram', 'fab fa-tiktok', 'fab fa-twitter', 'fab fa-facebook',
        'fab fa-youtube', 'fas fa-globe', 'fas fa-clock', 'fas fa-building',
        'fas fa-home', 'fas fa-info-circle', 'fas fa-headset', 'fas fa-comments',
        'fab fa-telegram', 'fab fa-linkedin', 'fab fa-pinterest', 'fab fa-github',
        'fas fa-at', 'fas fa-fax', 'fas fa-mobile-alt', 'fas fa-map'
    ],

    /** Convert Iconify name (fa-solid:phone) to FA class (fas fa-phone) */
    {{ $uid }}ToFaClass(iconifyName) {
        const [prefix, name] = iconifyName.split(':');
        const map = { 'fa-solid': 'fas', 'fa-brands': 'fab', 'fa-regular': 'far' };
        return (map[prefix] || 'fas') + ' fa-' + name;
    },

    /** Search Font Awesome icons via Iconify API */
    async {{ $uid }}Search(query) {
        if (!query.trim()) {
            this.{{ $uid }}Icons = this.{{ $uid }}Presets;
            return;
        }
        this.{{ $uid }}Loading = true;
        this.{{ $uid }}Icons = [];
        try {
            const url = 'https://api.iconify.design/search?query='
                + encodeURIComponent(query.trim())
                + '&prefixes=fa-solid,fa-brands,fa-regular&limit=48';
            const res = await fetch(url);
            const data = await res.json();
            if (data.icons) {
                this.{{ $uid }}Icons = data.icons.map(i => this.{{ $uid }}ToFaClass(i));
            }
        } catch (e) { console.error('FA icon search failed:', e); }
        this.{{ $uid }}Loading = false;
    }
}" class="relative">
    <div class="flex items-center gap-2">
        <!-- Text input showing the current FA class value -->
        <input type="text" name="{{ $name }}" x-model="{{ $model }}"
            class="w-full px-3 py-2 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            placeholder="{{ $placeholder }}" />

        <!-- Live preview of selected icon -->
        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 rounded-lg border border-gray-200">
            <template x-if="{{ $model }} && {{ $model }}.trim()">
                <i :class="{{ $model }}" class="text-xl text-gray-700"></i>
            </template>
            <template x-if="!{{ $model }} || !{{ $model }}.trim()">
                <span class="text-gray-400 text-xs">Icon</span>
            </template>
        </div>

        <!-- Toggle picker button -->
        <button type="button"
            @click="{{ $uid }}Open = !{{ $uid }}Open; if ({{ $uid }}Open && {{ $uid }}Icons.length === 0) {{ $uid }}Icons = {{ $uid }}Presets;"
            class="flex-shrink-0 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded-lg transition">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <!-- Dropdown panel — uses fixed positioning to escape overflow-hidden containers -->
    <div x-show="{{ $uid }}Open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
        @click.outside="{{ $uid }}Open = false"
        x-ref="{{ $uid }}Dropdown"
        x-effect="if ({{ $uid }}Open) { $nextTick(() => { const r = $el.previousElementSibling.getBoundingClientRect(); const d = $refs.{{ $uid }}Dropdown; d.style.position = 'fixed'; d.style.top = (r.bottom + 4) + 'px'; d.style.left = r.left + 'px'; }); }"
        class="z-[9999] bg-white rounded-lg shadow-xl border border-gray-200 p-4" style="min-width: 480px; position: fixed;" x-cloak>

        <!-- Search input -->
        <div class="relative mb-3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400 text-sm"></i>
            </div>
            <input type="text" x-model="{{ $uid }}Query"
                @input="clearTimeout({{ $uid }}Timer);
                    {{ $uid }}Timer = setTimeout(() => {{ $uid }}Search({{ $uid }}Query), 400);"
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Search icons... (e.g. phone, map, instagram)" />
        </div>

        <!-- Loading spinner -->
        <div x-show="{{ $uid }}Loading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-sm text-gray-500">Searching icons...</span>
        </div>

        <!-- Icon grid -->
        <div x-show="!{{ $uid }}Loading && {{ $uid }}Icons.length > 0" class="max-h-56 overflow-y-auto">
            <div class="grid grid-cols-8 gap-1">
                <template x-for="icon in {{ $uid }}Icons" :key="icon">
                    <button type="button"
                        @click="{{ $model }} = icon; {{ $uid }}Open = false;"
                        class="group flex flex-col items-center justify-center p-2 rounded-lg hover:bg-blue-50 transition cursor-pointer"
                        :class="{ 'bg-blue-100 ring-2 ring-blue-400': {{ $model }} === icon }"
                        :title="icon">
                        <i :class="icon" class="text-xl text-gray-700 group-hover:text-blue-600"></i>
                        <span class="text-[9px] text-gray-400 mt-1 truncate w-full text-center" x-text="icon.replace(/^(fas|fab|far) fa-/, '')"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- No results -->
        <div x-show="!{{ $uid }}Loading && {{ $uid }}Icons.length === 0 && {{ $uid }}Query.length > 0" class="text-center py-6">
            <p class="text-sm text-gray-500">No icons found for "<span x-text="{{ $uid }}Query"></span>"</p>
        </div>

        <!-- Footer: count + close -->
        <div x-show="!{{ $uid }}Loading && {{ $uid }}Icons.length > 0" class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400"><span x-text="{{ $uid }}Icons.length"></span> icons</p>
            <button type="button" @click="{{ $uid }}Open = false" class="text-xs text-blue-600 hover:text-blue-800">Close</button>
        </div>
    </div>
</div>
