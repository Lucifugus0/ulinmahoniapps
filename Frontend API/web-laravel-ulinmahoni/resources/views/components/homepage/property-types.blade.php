<!-- Property Types Component -->
<section class="section-light">
    <div class="property-types-container">
        <!-- Property Type Tabs -->
        <div class="property-tabs-wrapper">
            <button class="property-tab-trigger active" data-tab="kos">
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

        <!-- Location Tabs -->
        <div class="location-tabs-wrapper">
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

        <!-- Property Type Content -->
        <div class="property-tab-contents">
            <!-- Kos Content -->
            <div class="property-tab-content active" data-tab="kos" data-location="all">
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

<!-- Liquid glass tabs — frosted pill-style tab selectors with glass card containers -->
<style>
    .property-types-container {
        width: 100%;
        max-width: 80rem;
        margin: 0 auto;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    /* Glass tab bar — frosted container for tab buttons */
    .property-tabs-wrapper {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 2rem;
        padding: 0.25rem;
        background: var(--glass-bg, rgba(255, 255, 255, 0.45));
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.25));
        border-radius: var(--radius-lg, 1.75rem);
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
    }

    .property-tabs-wrapper .property-tab-trigger {
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

    .property-tabs-wrapper .property-tab-trigger:hover {
        color: var(--text-primary, #1a1a2e);
        background: rgba(255, 255, 255, 0.2);
    }

    /* Active tab — frosted glass pill, noticeably translucent */
    .property-tabs-wrapper .property-tab-trigger.active,
    .property-tabs-wrapper .property-tab-trigger.text-teal-600 {
        color: var(--accent, #0ea5a0);
        background: rgba(255, 255, 255, 0.35);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.18));
    }

    /* Location sub-tabs — thinner glass bar */
    .location-tabs-wrapper {
        display: flex;
        gap: 0.25rem;
        margin-bottom: 1.5rem;
        padding: 0.2rem;
        background: var(--glass-bg, rgba(255, 255, 255, 0.3));
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.2));
        border-radius: var(--radius-md, 1.25rem);
        width: fit-content;
    }

    .location-tabs-wrapper .location-tab-trigger {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: var(--text-tertiary, #8888a4);
        background: transparent;
        border: none;
        border-radius: var(--radius-sm, 0.75rem);
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        white-space: nowrap;
    }

    .location-tabs-wrapper .location-tab-trigger:hover {
        color: var(--text-primary, #1a1a2e);
    }

    .location-tabs-wrapper .location-tab-trigger.active {
        color: var(--accent, #0ea5a0);
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

    .browse-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 2rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #ffffff;
        background: linear-gradient(135deg, var(--accent, #0ea5a0), var(--accent-hover, #0d9488));
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-lg, 1.75rem);
        box-shadow: 0 4px 15px rgba(14, 165, 160, 0.25);
        transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
        text-decoration: none;
        gap: 0.5rem;
    }

    .browse-all-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 160, 0.35);
    }

    .browse-all-btn i { font-size: 0.75rem; }

    /* Dark mode tab overrides */
    html.dark .property-tabs-wrapper {
        background: var(--glass-bg, rgba(30, 30, 50, 0.5));
        border-color: var(--glass-border, rgba(255, 255, 255, 0.1));
    }

    html.dark .property-tabs-wrapper .property-tab-trigger:hover {
        color: var(--text-primary);
        background: rgba(255, 255, 255, 0.04);
    }

    html.dark .property-tabs-wrapper .property-tab-trigger.active {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--glass-border);
    }

    html.dark .location-tabs-wrapper {
        background: var(--glass-bg, rgba(30, 30, 50, 0.4));
        border-color: var(--glass-border);
    }

    html.dark .location-tabs-wrapper .location-tab-trigger.active {
        background: rgba(255, 255, 255, 0.06);
    }

    /* Mobile responsive */
    @media (max-width: 48rem) {
        .property-types-container { padding: 0 0.75rem; }

        .property-tabs-wrapper {
            margin-bottom: 1.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .property-tabs-wrapper::-webkit-scrollbar { display: none; }
        .property-tabs-wrapper .property-tab-trigger {
            padding: 0.5rem 0.875rem;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .location-tabs-wrapper {
            margin-bottom: 1rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .location-tabs-wrapper::-webkit-scrollbar { display: none; }
        .location-tabs-wrapper .location-tab-trigger {
            padding: 0.4rem 0.75rem;
            font-size: 0.8125rem;
            flex-shrink: 0;
        }

        .browse-all-wrapper { padding: 1.5rem 0; }
        .browse-all-btn {
            padding: 0.625rem 1.5rem;
            font-size: 0.8125rem;
        }
    }
</style>
