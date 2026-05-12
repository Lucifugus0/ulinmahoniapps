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
            <div class="location-tabs-row">
                <button class="location-tab-trigger active" data-location="all">
                    {{ __('homepage.cities.all') }}
                </button>
                <button class="location-tab-trigger" data-location="jakarta">
                    {{ __('homepage.cities.jakarta') }}
                </button>
                <button class="location-tab-trigger" data-location="bogor">
                    {{ __('homepage.cities.bogor') }}
                </button>
            </div>
        </div>

        <!-- Property Type Content -->
        <div class="property-tab-contents">
            <!-- All Properties Content — shows every property type combined, selected by default -->
            <div class="property-tab-content active" data-tab="all" data-location="all">
                @include('components.homepage.property-cards.kos', ['kos' => $allProperties ?? []])
            </div>
            <div class="property-tab-content" data-tab="all" data-location="jakarta">
                @include('components.homepage.property-cards.kos', ['kos' => $allJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="all" data-location="bogor">
                @include('components.homepage.property-cards.kos', ['kos' => $allBogor ?? []])
            </div>

            <!-- Kos Content -->
            <div class="property-tab-content" data-tab="kos" data-location="all">
                @include('components.homepage.property-cards.kos', ['kos' => $kos ?? []])
            </div>
            <div class="property-tab-content" data-tab="kos" data-location="jakarta">
                @include('components.homepage.property-cards.kos', ['kos' => $kosJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="kos" data-location="bogor">
                @include('components.homepage.property-cards.kos', ['kos' => $kosBogor ?? []])
            </div>

            <!-- Apartment Content -->
            <div class="property-tab-content" data-tab="apartment" data-location="all">
                @include('components.homepage.property-cards.apartment', ['apartments' => $apartments ?? []])
            </div>
            <div class="property-tab-content" data-tab="apartment" data-location="jakarta">
                @include('components.homepage.property-cards.apartment', ['apartments' => $apartmentsJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="apartment" data-location="bogor">
                @include('components.homepage.property-cards.apartment', ['apartments' => $apartmentsBogor ?? []])
            </div>

            <!-- Villa Content -->
            <div class="property-tab-content" data-tab="villa" data-location="all">
                @include('components.homepage.property-cards.villa', ['villas' => $villas ?? []])
            </div>
            <div class="property-tab-content" data-tab="villa" data-location="jakarta">
                @include('components.homepage.property-cards.villa', ['villas' => $villasJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="villa" data-location="bogor">
                @include('components.homepage.property-cards.villa', ['villas' => $villasBogor ?? []])
            </div>

            <!-- Hotel Content -->
            <div class="property-tab-content" data-tab="hotel" data-location="all">
                @include('components.homepage.property-cards.hotel', ['hotels' => $hotels ?? []])
            </div>
            <div class="property-tab-content" data-tab="hotel" data-location="jakarta">
                @include('components.homepage.property-cards.hotel', ['hotels' => $hotelsJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="hotel" data-location="bogor">
                @include('components.homepage.property-cards.hotel', ['hotels' => $hotelsBogor ?? []])
            </div>

            {{-- House Content (commented out since House tab is not active) --}}
            {{--
            <div class="property-tab-content" data-tab="house" data-location="jakarta">
                @include('components.homepage.property-cards.house', ['houses' => $housesJakarta ?? []])
            </div>
            <div class="property-tab-content" data-tab="house" data-location="bogor">
                @include('components.homepage.property-cards.house', ['houses' => $housesBogor ?? []])
            </div>
            --}}
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
        font-weight: 600;
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
        font-weight: 500;
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

    /* Property tabs — theme-aware via var(--accent). Inactive uses 60% opacity for state contrast,
       active goes to full opacity + glass-pill background. Color flips green/maroon by theme. */
    .property-tabs-row .property-tab-trigger {
        color: var(--accent);
        opacity: 0.6;
    }
    .property-tabs-row .property-tab-trigger:hover {
        opacity: 0.85;
        background: rgba(255, 255, 255, 0.2);
    }
    .property-tabs-row .property-tab-trigger.active {
        color: var(--accent);
        opacity: 1;
        background: rgba(255, 255, 255, 0.35);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.18));
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

    /* City/location tabs — same theme-aware var(--accent) scheme as property tabs */
    .location-tabs-row .location-tab-trigger {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--accent);
        opacity: 0.6;
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
        color: var(--accent);
        opacity: 1;
        background: rgba(255, 255, 255, 0.3);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
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
        font-weight: 500;
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

    html.dark .filter-divider {
        background: linear-gradient(to right, transparent, var(--glass-border, rgba(255, 255, 255, 0.1)), transparent);
    }

    /* Dark mode tab styling — color flips automatically via var(--accent); only the
       glass-pill background tints adjust for the darker surface. */
    html.dark .property-tabs-row .property-tab-trigger:hover {
        background: rgba(255, 255, 255, 0.04);
    }
    html.dark .property-tabs-row .property-tab-trigger.active {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--glass-border);
    }
    html.dark .location-tabs-row .location-tab-trigger:hover {
        background: rgba(255, 255, 255, 0.04);
    }
    html.dark .location-tabs-row .location-tab-trigger.active {
        background: rgba(255, 255, 255, 0.06);
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
