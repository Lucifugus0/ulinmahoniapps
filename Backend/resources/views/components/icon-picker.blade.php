@props(['model' => 'icon', 'name' => 'icon'])

<div x-data="{ _ipOpen: false, _ipQuery: '', _ipIcons: [], _ipLoading: false, _ipTimer: null }" class="relative">
    <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Iconify)</label>
    <div class="flex items-center gap-2">
        <input type="text" name="{{ $name }}" x-model="{{ $model }}"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            placeholder="mdi:wifi, mdi:pool, mdi:parking" />
        <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-gray-100 rounded-md"
            x-html="{{ $model }} ? '<span class=\'iconify text-2xl text-gray-700\' data-icon=\'' + {{ $model }} + '\'></span>' : '<span class=\'text-gray-400 text-xs\'>Icon</span>'"
            x-effect="let _icon = {{ $model }}; $nextTick(() => { if (window.Iconify) Iconify.scan($el); })">
        </div>
        <button type="button"
            @click="_ipOpen = !_ipOpen; if (_ipOpen && _ipIcons.length === 0 && !_ipQuery) {
                _ipIcons = ['mdi:wifi','mdi:pool','mdi:parking','mdi:bed','mdi:television','mdi:air-conditioner','mdi:shower','mdi:fridge','mdi:desk','mdi:sofa','mdi:lamp','mdi:fan','mdi:water-pump','mdi:elevator','mdi:stairs','mdi:door','mdi:window-open','mdi:key','mdi:lock','mdi:security','mdi:cctv','mdi:fire-extinguisher','mdi:smoking-off','mdi:coffee-maker','mdi:microwave','mdi:washing-machine','mdi:iron','mdi:hair-dryer','mdi:bathtub','mdi:toilet','mdi:water-boiler','mdi:dumbbell','mdi:basketball','mdi:tennis','mdi:grill','mdi:flower','mdi:tree','mdi:car-garage','mdi:bicycle','mdi:bus-stop','mdi:hospital-building','mdi:food','mdi:silverware-fork-knife','mdi:glass-cocktail','mdi:medical-bag','mdi:baby-carriage','mdi:paw','mdi:wheelchair-accessibility'];
            }"
            class="flex-shrink-0 px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded-md transition">
            Pilih
        </button>
    </div>

    <!-- Icon Picker Dropdown -->
    <div x-show="_ipOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
        @click.outside="_ipOpen = false"
        class="absolute z-[60] mt-2 left-0 right-0 bg-white rounded-lg shadow-lg border border-gray-200 p-3" x-cloak>

        <!-- Search -->
        <div class="relative mb-3">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" x-model="_ipQuery"
                @input="clearTimeout(_ipTimer); if (!_ipQuery.trim()) {
                    _ipIcons = ['mdi:wifi','mdi:pool','mdi:parking','mdi:bed','mdi:television','mdi:air-conditioner','mdi:shower','mdi:fridge','mdi:desk','mdi:sofa','mdi:lamp','mdi:fan','mdi:water-pump','mdi:elevator','mdi:stairs','mdi:door','mdi:window-open','mdi:key','mdi:lock','mdi:security','mdi:cctv','mdi:fire-extinguisher','mdi:smoking-off','mdi:coffee-maker','mdi:microwave','mdi:washing-machine','mdi:iron','mdi:hair-dryer','mdi:bathtub','mdi:toilet','mdi:water-boiler','mdi:dumbbell','mdi:basketball','mdi:tennis','mdi:grill','mdi:flower','mdi:tree','mdi:car-garage','mdi:bicycle','mdi:bus-stop','mdi:hospital-building','mdi:food','mdi:silverware-fork-knife','mdi:glass-cocktail','mdi:medical-bag','mdi:baby-carriage','mdi:paw','mdi:wheelchair-accessibility'];
                } else {
                    _ipTimer = setTimeout(async () => {
                        _ipLoading = true; _ipIcons = [];
                        try {
                            const r = await fetch('https://api.iconify.design/search?query=' + encodeURIComponent(_ipQuery.trim()) + '&limit=48');
                            const d = await r.json();
                            if (d.icons) _ipIcons = d.icons;
                        } catch(e) { console.error(e); }
                        _ipLoading = false;
                    }, 400);
                }"
                class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                placeholder="Cari icon... (contoh: wifi, bed, parking)" />
        </div>

        <!-- Loading -->
        <div x-show="_ipLoading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2 text-sm text-gray-500">Mencari icon...</span>
        </div>

        <!-- Icon Grid -->
        <div x-show="!_ipLoading && _ipIcons.length > 0" class="max-h-56 overflow-y-auto">
            <div class="grid grid-cols-6 gap-1">
                <template x-for="_ipIcon in _ipIcons" :key="_ipIcon">
                    <button type="button"
                        @click="{{ $model }} = _ipIcon; _ipOpen = false; $nextTick(() => { if (window.Iconify) Iconify.scan(); })"
                        class="group flex items-center justify-center p-2 rounded-lg hover:bg-blue-50 transition cursor-pointer"
                        :class="{ 'bg-blue-100 ring-2 ring-blue-400': {{ $model }} === _ipIcon }"
                        :title="_ipIcon">
                        <span class="iconify text-xl text-gray-700 group-hover:text-blue-600" :data-icon="_ipIcon"></span>
                    </button>
                </template>
            </div>
        </div>

        <!-- No Results -->
        <div x-show="!_ipLoading && _ipIcons.length === 0 && _ipQuery.length > 0" class="text-center py-6">
            <p class="text-sm text-gray-500">Tidak ada icon ditemukan untuk "<span x-text="_ipQuery"></span>"</p>
        </div>

        <!-- Initial State -->
        <div x-show="!_ipLoading && _ipIcons.length === 0 && _ipQuery.length === 0" class="text-center py-4">
            <p class="text-sm text-gray-400">Ketik keyword untuk mencari icon</p>
        </div>

        <!-- Result Count & Close -->
        <div x-show="!_ipLoading && _ipIcons.length > 0" class="mt-2 pt-2 border-t border-gray-100 flex items-center justify-between">
            <p class="text-xs text-gray-400"><span x-text="_ipIcons.length"></span> icon ditemukan</p>
            <button type="button" @click="_ipOpen = false" class="text-xs text-blue-600 hover:text-blue-800">Tutup</button>
        </div>
    </div>
</div>
