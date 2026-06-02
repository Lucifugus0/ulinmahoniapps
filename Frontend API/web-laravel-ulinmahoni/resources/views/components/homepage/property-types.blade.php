<!-- Property Types Component -->
<section class="section-light">
    <div class="property-types-container">
        <!-- Combined filter container — property type (row 1) + city (row 2) in one glass panel -->
        <div class="filter-container">
            <!-- Row 1: Property Type Tabs — "All" is selected by default -->
            <div class="filter-row-label">
                <i class="fas fa-building"></i>
                <span>{{ __('homepage.sections.category_property') }}</span>
            </div>
            <div class="property-tabs-row">
                <button class="property-tab-trigger active" data-tab="all">
                    {{ __('homepage.property_types.All') }}
                </button>
                <button class="property-tab-trigger" data-tab="kos">
                    {{ __('homepage.property_types.Kos') }}
                </button>
                {{--
                <button class="property-tab-trigger" data-tab="house">
                    {{ __('homepage.property_types.House') }}
                </button>
                --}}
                <button class="property-tab-trigger" data-tab="apartment">
                    {{ __('homepage.property_types.Apartment') }}
                </button>
                <button class="property-tab-trigger" data-tab="villa">
                    {{ __('homepage.property_types.Villa') }}
                </button>
                <button class="property-tab-trigger" data-tab="hotel">
                    {{ __('homepage.property_types.Hotel') }}
                </button>
            </div>

            <!-- Divider between rows -->
            <div class="filter-divider"></div>

            <!-- Row 2: City / Location Tabs -->
            <div class="filter-row-label">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ __('homepage.sections.location') }}</span>
            </div>
            {{-- Location tabs are driven by the m_cities master table ($cities, passed from
                 HomeController). "Semua Kota" (all) is always first and selected by default;
                 one pill is rendered per active city. --}}
            <div class="location-tabs-row">
                <button class="location-tab-trigger active" data-location="all">
                    {{ __('homepage.cities.all') }}
                </button>
                @foreach($cities ?? [] as $city)
                <button class="location-tab-trigger" data-location="{{ $city->slug }}">
                    {{ $city->city_name }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Property Type Content — one block of type panels per location bucket.
             $propertiesByLocation is keyed by 'all' + each city slug, and each value is
             keyed by property type ('all', 'kos', 'apartment', 'villa', 'hotel'). The
             scripts.blade.php filter shows the panel matching the active tab + location. --}}
        <div class="property-tab-contents">
            @foreach($propertiesByLocation ?? [] as $locKey => $byType)
            <!-- All Properties — every type combined for this location -->
            <div class="property-tab-content {{ $locKey === 'all' ? 'active' : '' }}" data-tab="all" data-location="{{ $locKey }}">
                @include('components.homepage.property-cards.kos', ['kos' => $byType['all'] ?? []])
            </div>

            <!-- Kos -->
            <div class="property-tab-content" data-tab="kos" data-location="{{ $locKey }}">
                @include('components.homepage.property-cards.kos', ['kos' => $byType['kos'] ?? []])
            </div>

            <!-- Apartment -->
            <div class="property-tab-content" data-tab="apartment" data-location="{{ $locKey }}">
                @include('components.homepage.property-cards.apartment', ['apartments' => $byType['apartment'] ?? []])
            </div>

            <!-- Villa -->
            <div class="property-tab-content" data-tab="villa" data-location="{{ $locKey }}">
                @include('components.homepage.property-cards.villa', ['villas' => $byType['villa'] ?? []])
            </div>

            <!-- Hotel -->
            <div class="property-tab-content" data-tab="hotel" data-location="{{ $locKey }}">
                @include('components.homepage.property-cards.hotel', ['hotels' => $byType['hotel'] ?? []])
            </div>

            {{-- House content omitted — the House type tab is currently disabled above --}}
            @endforeach
        </div>

        <!-- Browse All Button (outside tab contents) -->
        <div class="browse-all-wrapper">
            <a href="{{ route('properties.index') }}" class="browse-all-btn">
                <span>{{ __('homepage.actions.view_all_properties') }}</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Liquid glass tabs — single frosted panel with property type + city rows -->
<style>
    .property-types-container {
        width: 100%;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    /* Combined filter panel — single glass container for both tab rows */
    .filter-container {
        display: flex;
        flex-direction: column;
        margin-bottom: 2rem;
        padding: 0.5rem;
        background: var(--glass-bg, rgba(255, 255, 255, 0.45));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.25));
        border-radius: var(--radius-lg, 1.75rem);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    /* Small section label that sits above each pill row. Building icon for property type,
       Pin icon for location. Keeps the glass panel readable when the two rows aren't
       self-evident — especially when active state is "All" on both. */
    .filter-row-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.375rem 0.75rem 0.25rem;
        font-size: 0.75rem;
        font-weight: 900;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: var(--accent);
        opacity: 0.75;
    }
    .filter-row-label i {
        font-size: 0.8125rem;
        opacity: 0.9;
    }

    /* Row 1: Property type pills */
    .property-tabs-row {
        display: flex;
        gap: 0.25rem;
        padding: 0.125rem;
    }

    .property-tabs-row .property-tab-trigger {
        padding: 0.625rem 1.25rem;
        font-size: 0.9375rem;
        font-weight: 700;
        color: var(--text-tertiary, #8888a4);
        background: transparent;
        border: none;
        border-radius: var(--radius-md, 1.25rem);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        white-space: nowrap;
    }

    .property-tabs-row .property-tab-trigger:hover {
        color: var(--text-primary, #1a1a2e);
        background: rgba(255, 255, 255, 0.2);
    }

    /* Property tabs — inactive text is theme-aware: black in light mode, white in dark.
       Active state keeps the brand pill (green in light, red in dark — see dark overrides below). */
    .property-tabs-row .property-tab-trigger {
        color: #1a1a2e;
        opacity: 0.65;
    }
    .property-tabs-row .property-tab-trigger:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.2);
    }
    .property-tabs-row .property-tab-trigger.active {
        color: #ffffff;
        opacity: 1;
        background: linear-gradient(135deg, #0F513D 0%, #167a5a 100%);
        box-shadow: 0 2px 10px rgba(15, 81, 61, 0.35);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    /* Thin divider between the two rows */
    .filter-divider {
        height: 1px;
        margin: 0.375rem 0.75rem;
        background: linear-gradient(to right, transparent, var(--glass-border-subtle, rgba(255, 255, 255, 0.25)), transparent);
    }

    /* Row 2: City / location pills */
    .location-tabs-row {
        display: flex;
        gap: 0.25rem;
        padding: 0.125rem;
    }

    /* City/location tabs — same theme-aware scheme as property tabs: black in light, white in dark. */
    .location-tabs-row .location-tab-trigger {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #1a1a2e;
        opacity: 0.65;
        background: transparent;
        border: none;
        border-radius: var(--radius-sm, 0.75rem);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        white-space: nowrap;
    }

    .location-tabs-row .location-tab-trigger:hover {
        opacity: 0.85;
        background: rgba(255, 255, 255, 0.15);
    }

    .location-tabs-row .location-tab-trigger.active {
        color: #ffffff !important;
        opacity: 1;
        background: linear-gradient(135deg, #0F513D 0%, #167a5a 100%);
        box-shadow: 0 2px 8px rgba(15, 81, 61, 0.30);
    }

    /* Property tab content visibility */
    .property-tab-content { display: none; }
    .property-tab-content.active { display: block; }
    .property-tab-content.hidden { display: none !important; }

    /* Browse all — glass accent button */
    .browse-all-wrapper {
        text-align: center;
        padding: 2rem 0;
    }

    /* Lihat Semua Properti — theme-aware via var(--accent): green light / maroon dark. */
    .browse-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 2rem;
        font-size: 0.875rem;
        font-weight: 700;
        color: #ffffff;
        background: var(--accent);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-lg, 1.75rem);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        text-decoration: none;
        gap: 0.5rem;
    }

    .browse-all-btn:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.20);
    }

    .browse-all-btn i { font-size: 0.75rem; }

    /* Dark mode overrides */
    html.dark .filter-container {
        background: var(--glass-bg, rgba(30, 30, 50, 0.5));
        border-color: var(--glass-border, rgba(255, 255, 255, 0.1));
    }

    /* Row labels — brighter red in dark mode so KATEGORI PROPERTI / LOKASI pop against the dark glass. */
    html.dark .filter-row-label {
        color: #ff4d4d;
        opacity: 1;
    }

    html.dark .filter-divider {
        background: linear-gradient(to right, transparent, var(--glass-border, rgba(255, 255, 255, 0.1)), transparent);
    }

    /* Dark mode tab styling — inactive text turns white; active pills use UM Maroon red
       gradient instead of the light-mode green to match the dark theme accent. */
    html.dark .property-tabs-row .property-tab-trigger,
    html.dark .location-tabs-row .location-tab-trigger {
        color: #ffffff !important;
        opacity: 1;
    }
    html.dark .property-tabs-row .property-tab-trigger:hover,
    html.dark .location-tabs-row .location-tab-trigger:hover {
        color: #ffffff;
        opacity: 1;
        background: rgba(255, 255, 255, 0.06);
    }
    html.dark .property-tabs-row .property-tab-trigger.active {
        color: #ffffff;
        opacity: 1;
        background: linear-gradient(135deg, #800000 0%, #a83333 100%);
        border-color: rgba(255, 255, 255, 0.1);
        box-shadow: 0 2px 10px rgba(168, 51, 51, 0.45);
    }
    html.dark .location-tabs-row .location-tab-trigger.active {
        color: #ffffff !important;
        opacity: 1;
        background: linear-gradient(135deg, #800000 0%, #a83333 100%);
        box-shadow: 0 2px 8px rgba(168, 51, 51, 0.40);
    }

    /* Mobile responsive */
    @media (max-width: 48rem) {
        .property-types-container { padding: 0 0.75rem; }

        .filter-container {
            margin-bottom: 1.5rem;
            padding: 0.375rem;
        }

        .property-tabs-row {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .property-tabs-row::-webkit-scrollbar { display: none; }
        .property-tabs-row .property-tab-trigger {
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .location-tabs-row {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .location-tabs-row::-webkit-scrollbar { display: none; }
        .location-tabs-row .location-tab-trigger {
            padding: 0.4rem 0.75rem;
            font-size: 0.8125rem;
            flex-shrink: 0;
        }

        .filter-divider { margin: 0.25rem 0.5rem; }

        .filter-row-label {
            padding: 0.25rem 0.5rem 0.125rem;
            font-size: 0.6875rem;
            letter-spacing: 0.03em;
        }
        .filter-row-label i { font-size: 0.75rem; }

        .browse-all-wrapper { padding: 1.5rem 0; }
        .browse-all-btn {
            padding: 0.625rem 1.5rem;
            font-size: 0.8125rem;
        }
    }
</style>
