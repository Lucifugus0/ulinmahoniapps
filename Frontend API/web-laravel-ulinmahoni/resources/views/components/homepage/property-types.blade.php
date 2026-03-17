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

<style>
    /* Use rem for consistent scaling - 1rem = 16px at 100% zoom */
    .property-types-container {
        width: 100%;
        max-width: 80rem; /* 1280px - matches search bar */
        margin: 0 auto;
        padding: 0 1rem;
        box-sizing: border-box;
    }

    .property-tabs-wrapper {
        display: flex;
        border-bottom: 0.0625rem solid #e5e7eb; /* 1px */
        margin-bottom: 2rem; /* 32px */
        gap: 0;
    }

    .property-tabs-wrapper .property-tab-trigger {
        padding: 0.75rem 1.5rem; /* 12px 24px */
        font-size: 1.125rem; /* 18px */
        font-weight: 500;
        color: #6b7280;
        background: transparent;
        border: none;
        border-bottom: 0.125rem solid transparent; /* 2px */
        cursor: pointer;
        transition: color 0.2s ease, border-color 0.2s ease;
        white-space: nowrap;
    }

    .property-tabs-wrapper .property-tab-trigger:hover {
        color: #374151;
    }

    .property-tabs-wrapper .property-tab-trigger.active,
    .property-tabs-wrapper .property-tab-trigger.text-teal-600 {
        color: #0d9488;
        border-bottom-color: #0d9488;
    }

    .location-tabs-wrapper {
        display: flex;
        border-bottom: 0.0625rem solid #e5e7eb; /* 1px */
        margin-bottom: 1.5rem; /* 24px */
        gap: 0;
    }

    .location-tabs-wrapper .location-tab-trigger {
        padding: 0.75rem 1.5rem; /* 12px 24px */
        font-size: 1.125rem; /* 18px */
        font-weight: 500;
        color: #6b7280;
        background: transparent;
        border: none;
        border-bottom: 0.125rem solid transparent; /* 2px */
        cursor: pointer;
        transition: color 0.2s ease, border-color 0.2s ease;
        white-space: nowrap;
    }

    .location-tabs-wrapper .location-tab-trigger:hover {
        color: #374151;
    }

    .location-tabs-wrapper .location-tab-trigger.active {
        color: #0d9488;
        border-bottom-color: #0d9488;
    }

    /* Property tab content visibility */
    .property-tab-content {
        display: none;
    }

    .property-tab-content.active {
        display: block;
    }

    .property-tab-content.hidden {
        display: none !important;
    }

    .browse-all-wrapper {
        text-align: center;
        padding: 2rem 0; /* 32px */
    }

    .browse-all-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem; /* 12px 24px */
        font-size: 0.875rem; /* 14px */
        font-weight: 500;
        color: #ffffff;
        background-color: #0d9488;
        border-radius: 0.5rem; /* 8px */
        transition: all 0.3s ease;
        text-decoration: none;
        gap: 0.5rem; /* 8px */
    }

    .browse-all-btn:hover {
        background-color: #0f766e;
        box-shadow: 0 0.625rem 0.9375rem -0.1875rem rgba(13, 148, 136, 0.2);
    }

    .browse-all-btn i {
        font-size: 0.75rem; /* 12px */
    }

    /* Mobile responsive */
    @media (max-width: 48rem) { /* 768px */
        .property-types-container {
            padding: 0 0.75rem; /* 12px */
        }

        .property-tabs-wrapper {
            margin-bottom: 1.5rem; /* 24px */
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .property-tabs-wrapper::-webkit-scrollbar {
            display: none;
        }

        .property-tabs-wrapper .property-tab-trigger {
            padding: 0.625rem 1rem; /* 10px 16px */
            font-size: 1rem; /* 16px */
            flex-shrink: 0;
        }

        .location-tabs-wrapper {
            margin-bottom: 1rem; /* 16px */
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .location-tabs-wrapper::-webkit-scrollbar {
            display: none;
        }

        .location-tabs-wrapper .location-tab-trigger {
            padding: 0.625rem 1rem; /* 10px 16px */
            font-size: 1rem; /* 16px */
            flex-shrink: 0;
        }

        .browse-all-wrapper {
            padding: 1.5rem 0; /* 24px */
        }

        .browse-all-btn {
            padding: 0.625rem 1.25rem; /* 10px 20px */
            font-size: 0.8125rem; /* 13px */
        }
    }
</style>
