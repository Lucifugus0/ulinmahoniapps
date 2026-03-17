<!-- Grid container -->
<div class="property-cards-grid">
    @forelse($apartments as $apartment)
    <div class="property-card-wrapper">
        <a href="{{ route('apartments.show', ['id' => $apartment['id']]) }}" class="property-card-link">
            <div class="property-card">
                <!-- Image Container -->
                <div class="property-card-image">
                    @php
                        $mainImage = $apartment['thumbnail'] ?? $apartment['images'][0]['image'] ?? $apartment['image'] ?? null;
                    @endphp

                    @if($mainImage)
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}"
                             alt="{{ $apartment['name'] }}"
                             class="property-card-img"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'property-card-no-image\'><i class=\'fas fa-building\'></i></div>';">

                        @if(count($apartment['images'] ?? []) > 1)
                            <div class="property-card-image-count">
                                <i class="fas fa-images"></i>{{ count($apartment['images']) }}
                            </div>
                        @endif
                    @else
                        <div class="property-card-no-image">
                            <i class="fas fa-building"></i>
                            <span>No Image</span>
                        </div>
                    @endif

                    @if(isset($apartment['available_rooms']) && $apartment['available_rooms'] > 0)
                    <span class="property-card-available-badge">
                        <i class="fas fa-door-open"></i>{{ $apartment['available_rooms'] }} {{ __('homepage.rooms.available') }}
                    </span>
                    @elseif(isset($apartment['available_rooms']) && $apartment['available_rooms'] == 0)
                    <span class="property-card-full-badge">
                        <i class="fas fa-door-closed"></i>{{ __('homepage.rooms.full') }}
                    </span>
                    @endif
                </div>

                <!-- Content -->
                <div class="property-card-content">
                    <h4 class="property-card-title">
                        {{ $apartment['name'] }}
                    </h4>

                    @if(!empty($apartment['gender']))
                    @php $genderKey = strtolower($apartment['gender']); @endphp
                    <div class="property-card-gender">
                        @if($genderKey === 'male')
                            <i class="fas fa-mars gender-icon-male"></i>
                        @elseif($genderKey === 'female')
                            <i class="fas fa-venus gender-icon-female"></i>
                        @else
                            <i class="fas fa-venus-mars gender-icon-mixed"></i>
                        @endif
                        <span>{{ __('properties.gender.' . $genderKey, ['default' => $apartment['gender']]) }}</span>
                    </div>
                    @endif

                    <div class="property-card-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $apartment['subLocation'] }}</span>
                    </div>

                    <div class="property-card-price-section">
                        @php
                            $roomPriceMonthly = $apartment['room_price_original_monthly'] ?? null;
                            $roomPriceDaily = $apartment['price_original_daily'] ?? null;
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
        <p class="apt-empty-text">{{ __('homepage.messages.no_apartments_available') }}</p>
    </div>
    @endforelse
</div>

<style>
    .apt-empty-text {
        color: #6b7280;
        margin: 0;
        text-align: center;
        padding: 2rem 0;
    }
</style>
