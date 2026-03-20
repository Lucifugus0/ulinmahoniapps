<!-- Promo berlangsung Section -->
<section class="promo-section">
    <div class="promo-container-centered">
        <div class="promo-container">
            <!-- Swiper container -->
            <div class="swiper promo-swiper">
            @if(count($promos) > 0)
                <div class="swiper-wrapper">
                    @foreach ($promos as $promo)
                    <div class="swiper-slide">
                        <a href="/promo/{{ $promo['id'] }}" class="promo-link">
                            <div class="promo-image-container">
                                @if($promo['image'])
                                    <img src="{{ env('ADMIN_URL') }}/storage/{{ $promo['image'] }}"
                                         alt="{{ $promo['title'] }}"
                                         class="promo-image"
                                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'promo-no-image\'><i class=\'fas fa-image\'></i></div>';">
                                @else
                                    <div class="promo-no-image">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <!-- Navigation & Pagination -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            @else
                <div class="promo-empty">
                    <i class="fas fa-tag"></i>
                    <p>{{ __('homepage.messages.no_promos') }}</p>
                </div>
            @endif
            </div>
        </div>
    </div>
</section>

<style>
    .promo-section {
        width: 100%;
        background-color: transparent;
        padding: 1rem 0;
    }

    .promo-container-centered {
        max-width: 80rem;    /* Match search bar - 1280px */
        margin: 0 auto;      /* Center horizontally */
        padding: 0 1rem;     /* Side spacing like search bar */
    }

    .promo-container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    .promo-swiper {
        width: 100%;
        padding: 0.5rem 0 2.5rem 0;
    }

    .promo-link {
        display: block;
        width: 100%;
    }

    /* Full width image with 1911x372 aspect ratio — glass card container */
    .promo-image-container {
        width: 100%;
        aspect-ratio: 1911 / 372;
        background: var(--glass-bg, rgba(255, 255, 255, 0.45));
        overflow: hidden;
        border-radius: var(--radius-xl, 2rem);
        border: 1px solid var(--glass-border-subtle, rgba(255, 255, 255, 0.25));
        box-shadow: var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.08));
        transition: all 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .promo-link:hover .promo-image-container {
        transform: translateY(-4px);
        box-shadow: var(--glass-shadow-hover, 0 12px 40px rgba(0, 0, 0, 0.12));
    }

    .promo-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .promo-link:hover .promo-image {
        transform: scale(1.02);
    }

    .promo-no-image {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e5e7eb;
        color: #9ca3af;
    }

    .promo-no-image i {
        font-size: 3rem;
    }

    .promo-empty {
        text-align: center;
        padding: 3rem 0;
        color: #6b7280;
    }

    .promo-empty i {
        font-size: 2rem;
        margin-bottom: 1rem;
        display: block;
    }

    .promo-empty p {
        margin: 0;
    }

    /* Swiper navigation */
    .promo-swiper .swiper-button-next,
    .promo-swiper .swiper-button-prev {
        color: #ffffff;
        background: rgba(0, 0, 0, 0.5);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
    }

    .promo-swiper .swiper-button-next::after,
    .promo-swiper .swiper-button-prev::after {
        font-size: 1rem;
    }

    .promo-swiper .swiper-pagination-bullet {
        background: #9ca3af;
    }

    .promo-swiper .swiper-pagination-bullet-active {
        background: #0d9488;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var slideCount = document.querySelectorAll('.promo-swiper .swiper-slide').length;
        new Swiper('.promo-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: slideCount > 1,
            autoplay: slideCount > 1 ? {
                delay: 5000,
                disableOnInteraction: false,
            } : false,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            }
        });
    });
</script>
