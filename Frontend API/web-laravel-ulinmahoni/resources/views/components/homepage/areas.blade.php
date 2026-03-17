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
            <!-- Area Tabs -->
            <div class="flex border-b border-gray-200">
                <!-- JAKARTA AREA -->
                <button class="area-tab-trigger px-6 py-3 text-lg font-medium text-teal-600 border-b-2 border-teal-600" data-tab="jakarta">
                    {{ __('homepage.cities.jakarta') }}
                </button>
                <!-- BOGOR AREA -->
                <button class="area-tab-trigger px-6 py-3 text-lg font-medium text-gray-500 hover:text-gray-700" data-tab="bogor">
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