<!-- Promo berlangsung Section -->
<section class="section-dark">
    <div class="section-container">
        <div class="section-title">
            <h3 class="text-4xl font-medium">Ongoing Promos</h3>
            <div class="divider mt-2 md-2">
                <div class="divider-line"></div>
                <p class="divider-text">Limited Offers</p>
                <div class="divider-line"></div>
            </div>
        </div>

        <!-- Swiper container -->
        <div class="swiper promo-swiper">
            @if(count($promos) > 0)
                <div class="swiper-wrapper mb-8">
                    @foreach ($promos as $promo)
                    <div class="swiper-slide">
                        <a href="/promo/{{ $promo['id'] }}" class="block">
                            <div class="bg-white rounded-lg overflow-hidden shadow-md">
                                @if($promo['image'])
                                    <img src="data:image/png;base64,{{ $promo['image'] }}"
                                             alt="{{ $promo['title'] }}"
                                         class="w-full h-48 object-cover transition-transform duration-300 hover:scale-105">
                                @else
                                    <div class="bg-gray-100 w-full h-48 flex items-center justify-center">
                                        <i class="fas fa-image text-4xl text-gray-400"></i>
                                        <span class="ml-2 text-gray-500">No Image</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <div class="flex items-center mb-2">
                                        <span class="bg-yellow-400 text-xs px-2 py-1 rounded-full text-gray-800 font-medium">{{ $promo['badge'] }}</span>
                                    </div>
                                    <h3 class="font-medium text-gray-800 mb-1">{{ $promo['title'] }}</h3>
                                    <p class="text-gray-600 text-sm">{{ $promo['description'] }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-tag text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-600">No promos available at this time</p>
                    <p class="text-sm text-gray-500 mt-2">Please check back later for the latest offers</p>
                </div>
            @endif

            <!-- Add Navigation -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<style>
    .promo-swiper {
        padding: 20px 40px;
    }
    
    .swiper-button-next,
    .swiper-button-prev {
        color: #0d9488; /* teal-600 */
    }
    
    .swiper-pagination-bullet-active {
        background: #0d9488; /* teal-600 */
    }
    
    @media (min-width: 1024px) {
        .promo-swiper .swiper-slide {
            width: 33.333%; /* 3 cards per view on large screens */
        }
    }
    
    @media (min-width: 768px) and (max-width: 1023px) {
        .promo-swiper .swiper-slide {
            width: 50%; /* 2 cards per view on medium screens */
        }
    }
    
    @media (max-width: 767px) {
        .promo-swiper .swiper-slide {
            width: 100%; /* 1 card per view on small screens */
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.promo-swiper', {
            slidesPerView: 'auto',
            spaceBetween: 24,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // Mobile
                320: {
                    slidesPerView: 1,
                    spaceBetween: 16
                },
                // Tablet
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                // Desktop
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24
                }
            }
        });
    });
</script>