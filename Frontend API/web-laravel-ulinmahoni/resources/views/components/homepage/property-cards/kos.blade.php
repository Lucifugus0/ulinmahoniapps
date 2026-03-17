<!-- Grid container -->
<div class="property-cards-grid">
    @forelse($kos as $kosan)
    <div class="property-card-wrapper">
        <a href="{{ route('houses.show', ['id' => $kosan['id']]) }}" class="property-card-link">
            <div class="property-card">
                <!-- Image Container -->
                <div class="property-card-image">
                    @php
                        // Use thumbnail first, fallback to first image, then property image
                        $mainImage = $kosan['thumbnail'] ?? $kosan['images'][0]['image'] ?? $kosan['image'] ?? null;
                    @endphp

                    @if($mainImage)
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}"
                             alt="{{ $kosan['name'] }}"
                             class="property-card-img"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'property-card-no-image\'><i class=\'fas fa-building\'></i></div>';">

                        @if(count($kosan['images'] ?? []) > 1)
                            <div class="property-card-image-count">
                                <i class="fas fa-images"></i>{{ count($kosan['images']) }}
                            </div>
                        @endif
                    @else
                        <div class="property-card-no-image">
                            <i class="fas fa-building"></i>
                            <span>No Image</span>   
                        </div>
                    @endif

                    @if(isset($kosan['available_rooms']) && $kosan['available_rooms'] > 0)
                    <span class="property-card-available-badge">
                        <i class="fas fa-door-open"></i>{{ $kosan['available_rooms'] }} {{ __('homepage.rooms.available') }}
                    </span>
                    @elseif(isset($kosan['available_rooms']) && $kosan['available_rooms'] == 0)
                    <span class="property-card-full-badge">
                        <i class="fas fa-door-closed"></i>{{ __('homepage.rooms.full') }}
                    </span>
                    @endif
                </div>

                <!-- Content -->
                <div class="property-card-content">
                    <!-- Property Name -->
                    <h4 class="property-card-title">
                        {{ $kosan['name'] }}
                    </h4>

                    @if(!empty($kosan['gender']))
                    @php $genderKey = strtolower($kosan['gender']); @endphp
                    <div class="property-card-gender">
                        @if($genderKey === 'male')
                            <i class="fas fa-mars gender-icon-male"></i>        
                        @elseif($genderKey === 'female')
                            <i class="fas fa-venus gender-icon-female"></i>
                        @else
                            <i class="fas fa-venus-mars gender-icon-mixed"></i>
                        @endif
                        <span>{{ __('properties.gender.' . $genderKey, ['default' => $kosan['gender']]) }}</span>
                    </div>
                    @endif

                    <!-- Location -->
                    <div class="property-card-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $kosan['subLocation'] }}</span>
                    </div>

                    <!-- Price Section -->
                    <div class="property-card-price-section">
                        @php
                            $roomPriceMonthly = $kosan['room_price_original_monthly'] ?? null;
                            $roomPriceDaily = $kosan['price_original_daily'] ?? null;
                            $roomPrice = $roomPriceMonthly ?: $roomPriceDaily;
                            /* Use translated price period labels instead of hardcoded '/bulan' or '/hari' */
                            $pricePeriod = $roomPriceMonthly ? __('properties.price_info.per_month_full') : __('properties.price.per_day');
                        @endphp
                        @if(!empty($roomPrice))
                        <div class="property-card-price">
                            <div class="property-card-price-value">
                                Rp{{ number_format($roomPrice, 0, ',', '.') }}
                            </div>
                            <div class="property-card-price-period">{{ $pricePeriod }}</div>
                        </div>
                        <span class="property-card-view-btn">
                            {{ __('homepage.actions.view') }} <i class="fas fa-arrow-right"></i>
                        </span>
                        @else
                        <span class="property-card-view-btn property-card-view-btn-center">
                            {{ __('homepage.actions.view_detail') }} <i class="fas fa-arrow-right"></i>
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
    </div>
    @empty
    <div class="property-cards-empty">
        <div class="property-cards-empty-content">
            <div class="property-cards-empty-icon">
                <i class="fas fa-home"></i>
            </div>
            <p>{{ __('homepage.messages.no_kos_available') }}</p>
        </div>
    </div>
    @endforelse
</div>

<style>
    /* All values in rem for uniform scaling at any zoom level */
    .property-cards-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem; /* 16px */
    }

    @media (min-width: 40rem) { /* 640px */
        .property-cards-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 64rem) { /* 1024px */
        .property-cards-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (min-width: 80rem) { /* 1280px */
        .property-cards-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .property-card-wrapper {
        height: 100%;
    }

    .property-card-link {
        display: block;
        height: 100%;
        text-decoration: none;
    }

    .property-card {
        background: #ffffff;
        border-radius: 0.5rem; /* 8px */
        overflow: hidden;
        box-shadow: 0 0.0625rem 0.1875rem rgba(0, 0, 0, 0.1);
        border: 0.0625rem solid #e5e7eb; /* 1px */
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.3s ease;
    }

    .property-card:hover {
        box-shadow: 0 0.25rem 0.375rem rgba(0, 0, 0, 0.1);
    }

    .property-card-image {
        position: relative;
        overflow: hidden;
        aspect-ratio: 4 / 3;
        background-color: #f3f4f6;
    }

    .property-card-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .property-card:hover .property-card-img {
        transform: scale(1.05);
    }

    .property-card-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
    }

    .property-card-no-image i {
        font-size: 2rem; /* 32px */
        margin-bottom: 0.5rem; /* 8px */
    }

    .property-card-no-image span {
        font-size: 0.875rem; /* 14px */
    }

    .property-card-image-count {
        position: absolute;
        top: 0.75rem; /* 12px */
        right: 0.75rem; /* 12px */
        background: rgba(0, 0, 0, 0.6);
        color: #ffffff;
        font-size: 0.75rem; /* 12px */
        padding: 0.375rem 0.625rem; /* 6px 10px */
        border-radius: 9999px;
        font-weight: 500;
        backdrop-filter: blur(4px);
    }

    .property-card-image-count i {
        margin-right: 0.25rem; /* 4px */
    }

    .property-card-available-badge {
        position: absolute;
        bottom: 0.5rem; /* 8px */
        right: 0.5rem; /* 8px */
        background: #22c55e;
        color: #ffffff;
        font-size: 0.75rem; /* 12px */
        padding: 0.25rem 0.5rem; /* 4px 8px */
        border-radius: 9999px;
        font-weight: 500;
    }

    .property-card-available-badge i {
        margin-right: 0.25rem; /* 4px */
    }

    .property-card-full-badge {
        position: absolute;
        bottom: 0.5rem; /* 8px */
        right: 0.5rem; /* 8px */
        background: #ef4444;
        color: #ffffff;
        font-size: 0.75rem; /* 12px */
        padding: 0.25rem 0.5rem; /* 4px 8px */
        border-radius: 9999px;
        font-weight: 500;
    }

    .property-card-full-badge i {
        margin-right: 0.25rem; /* 4px */
    }

    .property-card-content {
        padding: 0.75rem; /* 12px */
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .property-card-title {
        font-size: 1rem; /* 16px */
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0; /* 4px */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        transition: color 0.2s ease;
    }

    .property-card:hover .property-card-title {
        color: #0d9488;
    }

    .property-card-gender {
        display: inline-flex;
        align-self: flex-start;
        align-items: center;
        color: #374151;
        font-size: 1rem; /* 16px - same as title */
        font-weight: 700;
        margin-bottom: 0.375rem; /* 6px */
        border: 1.5px solid #d1d5db;
        border-radius: 6px;
        padding: 0.1rem 0.25rem;
    }

    .property-card-gender i {
        margin-right: 0.3rem;
        flex-shrink: 0;
    }

    .gender-icon-male { color: #3b82f6; }
    .gender-icon-female { color: #ef4444; }
    .gender-icon-mixed {
        background: linear-gradient(to right, #ef4444, #3b82f6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .property-card-location {
        display: flex;
        align-items: flex-start;
        color: #4b5563;
        font-size: 0.875rem; /* 14px */
        margin-bottom: 0.25rem; /* 4px - tighter spacing */
    }

    .property-card-location i {
        margin-top: 0.125rem; /* 2px */
        margin-right: 0.375rem; /* 6px */
        color: #9ca3af;
        flex-shrink: 0;
    }

    .property-card-location span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .property-card-price-section {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 0.5rem; /* 8px */
        border-top: 0.0625rem solid #f3f4f6; /* 1px */
    }

    .property-card-price {
        display: flex;
        flex-direction: column;
    }

    .property-card-price-value {
        font-size: 1.125rem; /* 18px */
        font-weight: 700;
        color: #111827;
    }

    .property-card-price-period {
        font-size: 0.75rem; /* 12px */
        color: #6b7280;
    }

    .property-card-view-btn {
        font-size: 0.875rem; /* 14px */
        font-weight: 700;
        color: #111827;
        transition: color 0.2s ease;
    }

    .property-card-view-btn:hover {
        color: #374151;
    }

    .property-card-view-btn i {
        font-size: 0.75rem; /* 12px */
        margin-left: 0.25rem; /* 4px */
    }

    .property-card-view-btn-center {
        width: 100%;
        text-align: center;
    }

    .property-cards-empty {
        grid-column: 1 / -1;
    }

    .property-cards-empty-content {
        background: #ffffff;
        border-radius: 0.75rem; /* 12px */
        box-shadow: 0 0.0625rem 0.1875rem rgba(0, 0, 0, 0.1);
        border: 0.0625rem solid #f3f4f6; /* 1px */
        padding: 2rem; /* 32px */
        text-align: center;
    }

    .property-cards-empty-icon {
        width: 4rem; /* 64px */
        height: 4rem; /* 64px */
        margin: 0 auto 1rem; /* 16px */
        color: #d1d5db;
    }

    .property-cards-empty-icon i {
        font-size: 3rem; /* 48px */
    }

    .property-cards-empty-content p {
        color: #6b7280;
        margin: 0;
    }
</style>
