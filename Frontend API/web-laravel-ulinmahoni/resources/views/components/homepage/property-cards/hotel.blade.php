<!-- Grid container -->
<div class="property-cards-grid">
    @forelse($hotels as $hotel)
    <div class="property-card-wrapper">
        <a href="{{ route('hotels.show', ['id' => $hotel['id']]) }}" class="property-card-link">
            <div class="property-card">
                <!-- Image Container -->
                <div class="property-card-image">
                    @php
                        $mainImage = $hotel['thumbnail'] ?? $hotel['images'][0]['image'] ?? $hotel['image'] ?? null;
                    @endphp

                    @if($mainImage)
                        <img src="{{ env('ADMIN_URL') }}/storage/{{ $mainImage }}"
                             alt="{{ $hotel['name'] }}"
                             class="property-card-img"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'property-card-no-image\'><i class=\'fas fa-building\'></i></div>';">

                        @if(count($hotel['images'] ?? []) > 1)
                            <div class="property-card-image-count">
                                <i class="fas fa-images"></i>{{ count($hotel['images']) }}
                            </div>
                        @endif
                    @else
                        <div class="property-card-no-image">
                            <i class="fas fa-building"></i>
                            <span>No Image</span>
                        </div>
                    @endif

                    @if(isset($hotel['available_rooms']) && $hotel['available_rooms'] > 0)
                    <span class="property-card-available-badge">
                        <i class="fas fa-door-open"></i>{{ $hotel['available_rooms'] }} {{ __('homepage.rooms.available') }}
                    </span>
                    @elseif(isset($hotel['available_rooms']) && $hotel['available_rooms'] == 0)
                    <span class="property-card-full-badge">
                        <i class="fas fa-door-closed"></i>{{ __('homepage.rooms.full') }}
                    </span>
                    @endif
                </div>

                <!-- Content -->
                <div class="property-card-content">
                    <h4 class="property-card-title">
                        {{ $hotel['name'] }}
                    </h4>

                    @if(!empty($hotel['gender']))
                    @php $genderKey = strtolower($hotel['gender']); @endphp
                    <div class="property-card-gender">
                        @if($genderKey === 'male')
                            <i class="fas fa-mars gender-icon-male"></i>
                        @elseif($genderKey === 'female')
                            <i class="fas fa-venus gender-icon-female"></i>
                        @else
                            <i class="fas fa-venus-mars gender-icon-mixed"></i>
                        @endif
                        <span>{{ __('properties.gender.' . $genderKey, ['default' => $hotel['gender']]) }}</span>
                    </div>
                    @endif

                    <div class="property-card-location">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $hotel['subLocation'] }}</span>
                    </div>

                    <div class="property-card-price-section">
                        @php
                            $roomPriceMonthly = $hotel['room_price_original_monthly'] ?? null;
                            $roomPriceDaily = $hotel['price_original_daily'] ?? null;
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
        <p class="apt-empty-text">{{ __('homepage.messages.no_hotels_available') }}</p>
    </div>
    @endforelse
</div>
