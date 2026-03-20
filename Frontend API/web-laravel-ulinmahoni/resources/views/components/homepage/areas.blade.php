<!-- Area Kost Coliving Terpopuler Section -->
<section class="section-light">
    <div class="section-container">
        <div class="section-title">
            <h3 class="text-4xl font-medium">{{ __('homepage.titles.available_areas') }}</h3>
            <div class="divider mt-2 md-2">
                <div class="divider-line"></div>
                <p class="divider-text">{{ __('homepage.subtitles.areas_around_you') }}</p>
                <div class="divider-line"></div>
            </div>
        </div>

        <div class="mb-6">
            <!-- Area Tabs — glass pill tab bar -->
            <div class="flex gap-1 p-1 w-fit mb-4" style="background: var(--glass-bg, rgba(255, 255, 255, 0.15)); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: var(--radius-md, 1.25rem); border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.15));">
                <!-- JAKARTA AREA -->
                <button class="area-tab-trigger px-5 py-2 text-sm font-medium text-teal-600 border-b-2 border-teal-600" data-tab="jakarta" style="border: none; border-radius: var(--radius-sm, 0.75rem); background: rgba(255, 255, 255, 0.3); box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                    {{ __('homepage.cities.jakarta') }}
                </button>
                <!-- BOGOR AREA -->
                <button class="area-tab-trigger px-5 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-tab="bogor" style="border: none; border-radius: var(--radius-sm, 0.75rem); background: transparent;">
                    {{ __('homepage.cities.bogor') }}
                </button>
            </div>

            <!-- Area Content -->
            <div class="area-tab-contents mt-6">
                @include('components.homepage.area-cards.jakarta')
                @include('components.homepage.area-cards.bogor')
                @include('components.homepage.area-cards.tangerang')
                @include('components.homepage.area-cards.depok')
                @include('components.homepage.area-cards.bekasi')
            </div>
        </div>
    </div>
</section> 