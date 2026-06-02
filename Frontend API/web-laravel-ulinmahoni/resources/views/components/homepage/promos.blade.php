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
                        {{-- Banner click opens modal (preventDefault on JS handler).
                             Falls back to /promo/{id} if JS fails — graceful degradation. --}}
                        <a href="/promo/{{ $promo['id'] }}"
                           class="promo-link"
                           data-promo-id="{{ $promo['id'] }}"
                           data-promo-title="{{ $promo['title'] }}"
                           data-promo-description="{{ $promo['description'] ?? '' }}"
                           data-promo-code="{{ $promo['promo_code'] ?? '' }}"
                           data-promo-image="{{ $promo['image'] ? env('ADMIN_URL') . '/storage/' . $promo['image'] : '' }}"
                           {{-- how_to_claim drives step descriptions; terms_conditions drives the T&C list.
                                Both fall back to hardcoded i18n when empty/missing. Blade's {{ }} escapes
                                JSON's quote characters to &quot; — sufficient for safe attribute passing.
                                Using htmlspecialchars() in addition would double-escape and break JSON.parse. --}}
                           data-promo-how-to-claim="{{ json_encode($promo['how_to_claim'] ?? []) }}"
                           data-promo-terms="{{ json_encode($promo['terms_conditions'] ?? []) }}">
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

    /* Full width image with 1920x620 aspect ratio — glass card container */
    .promo-image-container {
        width: 100%;
        aspect-ratio: 1920 / 620;
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

    /* ========================================================================
       Promo Detail Modal — liquid glass, animated 4-step claim flow
       ======================================================================== */

    .promo-detail-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 2rem 1rem;
        overflow-y: auto;
    }
    .promo-detail-modal.hidden { display: none; }

    .promo-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        backdrop-filter: blur(8px) saturate(140%);
        -webkit-backdrop-filter: blur(8px) saturate(140%);
    }

    /* Liquid glass — strong frosted surface using the project's design tokens
       (--glass-* defined in components/homepage/styles.blade.php). Layers:
       1. Translucent base with strong blur + saturation bump for color richness
       2. Inset top highlight for the wet/glossy look
       3. Subtle linear-gradient overlay (white → transparent) for depth
       4. Outer drop-shadow for elevation off the dark backdrop */
    .promo-modal-content {
        position: relative;
        width: 100%;
        max-width: 960px;
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.05) 60%) ,
            rgba(255, 255, 255, 0.55);
        backdrop-filter: var(--glass-blur-strong, blur(48px)) saturate(200%);
        -webkit-backdrop-filter: var(--glass-blur-strong, blur(48px)) saturate(200%);
        border-radius: 1.75rem;
        border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.35));
        box-shadow:
            var(--glass-shadow, 0 8px 32px rgba(0, 0, 0, 0.10)),
            0 30px 80px rgba(0, 0, 0, 0.25),
            inset 0 1px 0 rgba(255, 255, 255, 0.55),
            inset 0 -1px 0 rgba(255, 255, 255, 0.06);
        padding: 2.5rem 2rem;
        margin: auto;
        color: #0f0f1a;
        overflow: hidden;
    }
    /* Soft iridescent sheen — radial glows in UM brand colors (green + subtle maroon) */
    .promo-modal-content::before {
        content: "";
        position: absolute;
        top: -30%;
        left: -10%;
        width: 80%;
        height: 60%;
        background: radial-gradient(closest-side, rgba(15, 81, 61, 0.20), transparent 70%);
        pointer-events: none;
        z-index: 0;
    }
    .promo-modal-content::after {
        content: "";
        position: absolute;
        bottom: -20%;
        right: -15%;
        width: 70%;
        height: 60%;
        background: radial-gradient(closest-side, rgba(128, 0, 0, 0.12), transparent 70%);
        pointer-events: none;
        z-index: 0;
    }
    .promo-modal-content > * { position: relative; z-index: 1; }

    .promo-modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 9999px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        font-size: 1.25rem;
        line-height: 1;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.2s;
    }
    .promo-modal-close:hover {
        background: rgba(255, 255, 255, 0.95);
        color: #111827;
        transform: scale(1.05);
    }

    .promo-modal-header { text-align: center; margin-bottom: 1.5rem; }
    .promo-modal-title {
        font-size: clamp(1.75rem, 3vw, 2.5rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        line-height: 1.15;
        margin: 0 0 0.75rem;
        background: linear-gradient(135deg, #0a3a2b 0%, #0F513D 60%, #1a7553 100%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        -webkit-text-fill-color: transparent;
    }
    .promo-modal-description {
        max-width: 42rem;
        margin: 0 auto 1.25rem;
        /* Light-mode color bumped from #4b5563 to #1f2937 (gray-800) for stronger contrast on glass.
           Font size bumped 2 tiers (0.95rem → 1.15rem) for readability. */
        color: #1f2937;
        font-size: 1.15rem;
        line-height: 1.55;
    }
    .promo-modal-actions {
        display: flex;
        gap: 0.75rem;
        justify-content: center;
        flex-wrap: wrap;
    }
    .promo-modal-actions .btn-claim,
    .promo-modal-actions .btn-properties {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
    }
    /* Brand-aligned: Claim + View Properties buttons now use UM Green (#0F513D) instead of
       UM Maroon. Gradient and shadows derived from rgba(15, 81, 61, ...) to keep depth /
       hover lift consistent with the original maroon treatment. */
    .promo-modal-actions .btn-claim {
        background: linear-gradient(135deg, #0F513D, #1a7a5c);
        color: #fff;
        box-shadow: 0 8px 20px rgba(15, 81, 61, 0.40);
    }
    .promo-modal-actions .btn-claim:hover { transform: translateY(-2px); box-shadow: 0 12px 28px rgba(15, 81, 61, 0.50); }
    .promo-modal-actions .btn-properties {
        background: rgba(255, 255, 255, 0.7);
        color: #0F513D;
        border: 1px solid rgba(15, 81, 61, 0.40);
        backdrop-filter: blur(8px);
    }
    .promo-modal-actions .btn-properties:hover { background: rgba(255, 255, 255, 0.95); border-color: rgba(15, 81, 61, 0.70); transform: translateY(-2px); }

    .promo-modal-banner {
        margin: 1.75rem 0;
        border-radius: 1.25rem;
        overflow: hidden;
        aspect-ratio: 1920 / 620;
        background: rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    }
    .promo-modal-banner img { width: 100%; height: 100%; object-fit: cover; display: block; }

    .promo-modal-steps-section { margin: 2rem 0 1.5rem; text-align: center; }
    .promo-modal-steps-section h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 0.5rem;
        color: #111827;
    }
    .promo-modal-steps-section > p {
        /* "Hanya perlu 4 langkah..." subtitle — darkened from #6b7280 to #374151 (gray-700) and bumped from 0.875rem to 1.05rem. */
        color: #374151;
        font-size: 1.05rem;
        margin: 0 0 2rem;
    }

    /* Step grid + connecting line scale dynamically with the number of admin-entered steps.
       --step-count is set inline by JS at render time (range 2-5). Mobile media query overrides
       to a 2-col grid regardless. */
    .promo-steps {
        display: grid;
        grid-template-columns: repeat(var(--step-count, 4), minmax(0, 1fr));
        gap: 1.25rem;
        position: relative;
    }
    /* Connecting dotted line — left/right margin = 50% / step-count so the line spans circle centers */
    .promo-steps::before {
        content: "";
        position: absolute;
        top: 1.75rem;          /* circle radius */
        left: calc(50% / var(--step-count, 4));
        right: calc(50% / var(--step-count, 4));
        height: 2px;
        background-image: linear-gradient(to right, rgba(15, 81, 61, 0.40) 50%, transparent 50%);
        background-size: 12px 2px;
        background-repeat: repeat-x;
        z-index: 0;
    }

    .promo-step {
        position: relative;
        text-align: center;
        z-index: 1;
        opacity: 0.35;
        transform: translateY(0);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    .promo-step.active {
        opacity: 1;
        transform: translateY(-2px);
    }

    .promo-step .step-circle {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 9999px;
        margin: 0 auto 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.125rem;
        background: rgba(0, 0, 0, 0.08);
        color: rgba(0, 0, 0, 0.35);
        border: 2px solid transparent;
        transition: all 0.5s ease;
    }
    .promo-step.active .step-circle {
        background: linear-gradient(135deg, #0F513D, #1a7553);
        color: #fff;
        box-shadow: 0 8px 22px rgba(15, 81, 61, 0.45);
        transform: scale(1.08);
    }

    .promo-step h4 {
        font-size: 0.95rem;
        font-weight: 700;
        margin: 0 0 0.4rem;
        color: #6b7280;
        transition: color 0.5s;
    }
    .promo-step.active h4 { color: #111827; }
    .promo-step p {
        /* Step descriptions — bumped 2 tiers (0.75rem → 0.95rem) and darkened so light-mode reads clearly.
           Inactive #9ca3af → #6b7280 (still muted to preserve animation contrast).
           Active #4b5563 → #1f2937 (gray-800) for prominence when the step lights up. */
        font-size: 0.95rem;
        line-height: 1.5;
        color: #6b7280;
        margin: 0 0 0.5rem;
        transition: color 0.5s;
    }
    .promo-step.active p { color: #1f2937; }

    /* Promo Code prominent display — sits between Cara Klaim and Syarat sections.
       Uses flexbox column for bulletproof horizontal centering of heading + box. */
    .promo-modal-code {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, 0.55);
        border: 1px solid rgba(128, 0, 0, 0.30);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        display: flex;
        flex-direction: column;
        align-items: center;       /* horizontally center heading + box */
        text-align: center;
    }
    .promo-modal-code.hidden { display: none; }
    .promo-modal-code h3 {
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #800000;
        margin: 0 0 0.85rem;
        text-align: center;
    }
    .promo-code-box {
        display: flex;
        align-items: stretch;
        gap: 0;
        width: 100%;
        max-width: 26rem;
        margin: 0 auto;            /* belt-and-suspenders centering */
        border-radius: 9999px;
        background: rgba(255, 255, 255, 0.9);
        border: 2px dashed rgba(128, 0, 0, 0.55);
        padding: 0.4rem 0.4rem 0.4rem 0.6rem;
        box-shadow: 0 8px 24px rgba(128, 0, 0, 0.15);
    }
    .promo-code-box input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 1.05rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #5c0000;
        text-align: center;
        padding: 0.5rem 0.75rem;
        cursor: text;
    }
    .promo-code-box button {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.6rem 1.1rem;
        border-radius: 9999px;
        border: none;
        background: linear-gradient(135deg, #800000, #a83333);
        color: #fff;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.22, 1, 0.36, 1);
        box-shadow: 0 4px 12px rgba(128, 0, 0, 0.35);
    }
    .promo-code-box button:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(128, 0, 0, 0.45);
    }
    .promo-code-box button.copied {
        background: linear-gradient(135deg, #5c0000, #800000);
    }
    @media (max-width: 480px) {
        .promo-code-box { flex-wrap: wrap; border-radius: 1rem; padding: 0.5rem; }
        .promo-code-box input { width: 100%; padding: 0.5rem; }
        .promo-code-box button { width: 100%; justify-content: center; padding: 0.65rem; border-radius: 0.75rem; }
    }

    .promo-modal-terms {
        margin-top: 1.5rem;
        padding: 1.5rem;
        border-radius: 1.25rem;
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .promo-modal-terms h3 {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #111827;
        margin: 0 0 1rem;
    }
    .promo-modal-terms h3 i { color: #0F513D; font-size: 1.15rem; }
    .promo-modal-terms ul {
        list-style: none;
        margin: 0; padding: 0;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.6rem 1.5rem;
    }
    .promo-modal-terms li {
        display: flex; gap: 0.55rem; align-items: flex-start;
        /* Bumped 2 tiers (0.85rem → 1.05rem) and darkened to #1f2937 for readable contrast on glass. */
        font-size: 1.05rem;
        color: #1f2937;
        line-height: 1.45;
    }
    .promo-modal-terms li i {
        color: #0F513D;
        /* Match icon size to new li font-size so the checkmark scales with the text. */
        font-size: 1.1rem;
        margin-top: 0.15rem;
        flex-shrink: 0;
    }

    /* -------- Mobile responsive -------- */
    @media (max-width: 640px) {
        .promo-modal-content { padding: 1.5rem 1rem; border-radius: 1.25rem; }
        .promo-steps { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .promo-steps::before { display: none; }
        .promo-modal-terms ul { grid-template-columns: 1fr; }
    }

    /* -------- Dark mode overrides -------- */
    html.dark .promo-modal-content {
        background:
            linear-gradient(180deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 60%) ,
            rgba(17, 24, 39, 0.55);
        border-color: rgba(255, 255, 255, 0.14);
        color: #f3f4f6;
        box-shadow:
            0 30px 80px rgba(0, 0, 0, 0.55),
            inset 0 1px 0 rgba(255, 255, 255, 0.10),
            inset 0 -1px 0 rgba(255, 255, 255, 0.03);
    }
    html.dark .promo-modal-content::before {
        background: radial-gradient(closest-side, rgba(127, 212, 180, 0.22), transparent 70%);
    }
    html.dark .promo-modal-content::after {
        background: radial-gradient(closest-side, rgba(168, 51, 51, 0.18), transparent 70%);
    }
    html.dark .promo-modal-close {
        background: rgba(31, 41, 55, 0.7);
        border-color: rgba(255, 255, 255, 0.1);
        color: #d1d5db;
    }
    html.dark .promo-modal-close:hover { background: rgba(31, 41, 55, 0.95); color: #fff; }
    html.dark .promo-modal-title {
        background: linear-gradient(135deg, #7fd4b4 0%, #52bf99 60%, #a8e3cb 100%);
        background-clip: text;
        -webkit-background-clip: text;
    }
    html.dark .promo-modal-description { color: #d1d5db; }
    /* Dark-mode View Properties — soft mint variant of UM Green (was the dark maroon
       #d97777) so the label stays legible on the dark glass surface. */
    html.dark .promo-modal-actions .btn-properties {
        background: rgba(31, 41, 55, 0.7);
        color: #5fb892;
        border-color: rgba(95, 184, 146, 0.45);
    }
    html.dark .promo-modal-actions .btn-properties:hover { background: rgba(31, 41, 55, 0.95); border-color: rgba(95, 184, 146, 0.75); }
    html.dark .promo-modal-banner { background: rgba(255, 255, 255, 0.04); border-color: rgba(255, 255, 255, 0.08); }
    html.dark .promo-modal-steps-section h3 { color: #f3f4f6; }
    html.dark .promo-modal-steps-section > p { color: #9ca3af; }
    html.dark .promo-step .step-circle { background: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.4); }
    /* Specificity-aware active override for dark mode — without this, the inactive dark rule
       above (0,3,1) wins over the light-mode active rule (0,3,0) and the green gradient never
       renders on the active circle. Mirrors the light-mode active style. */
    html.dark .promo-step.active .step-circle {
        background: linear-gradient(135deg, #0F513D, #1a7553);
        color: #fff;
        box-shadow: 0 8px 22px rgba(15, 81, 61, 0.55);
        transform: scale(1.08);
    }
    html.dark .promo-step h4 { color: #9ca3af; }
    html.dark .promo-step.active h4 { color: #f3f4f6; }
    html.dark .promo-step p { color: #6b7280; }
    html.dark .promo-step.active p { color: #d1d5db; }
    html.dark .promo-modal-code {
        background: rgba(31, 41, 55, 0.55);
        border-color: rgba(217, 119, 119, 0.35);
    }
    html.dark .promo-modal-code h3 { color: #d97777; }
    html.dark .promo-code-box {
        background: rgba(17, 24, 39, 0.9);
        border-color: rgba(217, 119, 119, 0.5);
    }
    html.dark .promo-code-box input { color: #e5a0a0; }

    html.dark .promo-modal-terms {
        background: rgba(31, 41, 55, 0.5);
        border-color: rgba(75, 85, 99, 0.4);
    }
    html.dark .promo-modal-terms h3 { color: #f3f4f6; }
    html.dark .promo-modal-terms h3 i,
    html.dark .promo-modal-terms li i { color: #7fd4b4; }
    html.dark .promo-modal-terms li { color: #d1d5db; }
    html.dark .promo-steps::before {
        background-image: linear-gradient(to right, rgba(127, 212, 180, 0.45) 50%, transparent 50%);
    }
</style>

{{-- Promo Detail Modal — single instance, populated dynamically from clicked banner's data attributes --}}
<div id="promoDetailModal" class="promo-detail-modal hidden" role="dialog" aria-modal="true" aria-labelledby="pmTitle">
    <div class="promo-modal-backdrop" data-promo-close></div>
    <div class="promo-modal-content">
        <button type="button" class="promo-modal-close" aria-label="Close" data-promo-close>&times;</button>

        <div class="promo-modal-header">
            <h2 id="pmTitle" class="promo-modal-title"></h2>
            <p class="promo-modal-description">
                <span id="pmDescription"></span>
            </p>
            <div class="promo-modal-actions">
                <button type="button" id="pmClaimBtn" class="btn-claim">
                    <i class="fas fa-tag"></i>
                    <span id="pmClaimLabel">{{ __('promo.claim_promo') }}</span>
                </button>
                <a id="pmPropertiesLink" href="{{ route('properties.index') }}" class="btn-properties">
                    {{ __('promo.view_properties') }}
                    <i class="fas fa-external-link-alt"></i>
                </a>
            </div>
        </div>

        <div class="promo-modal-banner">
            <img id="pmImage" src="" alt="" />
        </div>

        <div class="promo-modal-steps-section">
            <h3>{{ __('promo.how_to_claim_title') }}</h3>
            {{-- Subtitle text is rebuilt by JS with the actual step count substituted into :count --}}
            <p id="pmStepsSubtitle">{{ __('promo.how_to_claim_subtitle', ['count' => 4]) }}</p>

            {{-- Step circles render dynamically (2-5) from admin's how_to_claim array. JS populates
                 this container; the inline `--step-count` CSS variable scales the grid + connector line.
                 Falls back to 4 hardcoded i18n steps when the admin array has fewer than 2 items. --}}
            <div class="promo-steps" id="pmSteps" style="--step-count: 4;"></div>
        </div>

        {{-- Prominent Promo Code text-box with copy button (between Cara Klaim and Syarat). Hidden when promo has no code. --}}
        <div class="promo-modal-code hidden" id="pmCodeSection">
            <h3>{{ __('promo.promo_code') }}</h3>
            <div class="promo-code-box">
                <input type="text" id="pmCodeInput" readonly value="" aria-label="{{ __('promo.promo_code') }}" />
                <button type="button" id="pmCodeCopyBtn" aria-label="{{ __('promo.copy') }}">
                    <i class="fas fa-copy" id="pmCodeCopyIcon"></i>
                    <span id="pmCodeCopyLabel">{{ __('promo.copy') }}</span>
                </button>
            </div>
        </div>

        {{-- Terms render from `terms_conditions` array on the banner if set; falls back to the 4
             hardcoded i18n terms below when empty. JS overwrites #pmTermsList per-banner. --}}
        <div class="promo-modal-terms">
            <h3><i class="fas fa-leaf"></i> {{ __('promo.terms_title') }}</h3>
            <ul id="pmTermsList">
                <li><i class="fas fa-check-circle"></i> {{ __('promo.terms_1') }}</li>
                <li><i class="fas fa-check-circle"></i> {{ __('promo.terms_2') }}</li>
                <li><i class="fas fa-check-circle"></i> {{ __('promo.terms_3') }}</li>
                <li><i class="fas fa-check-circle"></i> {{ __('promo.terms_4') }}</li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        /* Only initialize Swiper if promo slides exist — prevents
           getComputedStyle crash when .swiper-wrapper is missing */
        var slideCount = document.querySelectorAll('.promo-swiper .swiper-slide').length;
        if (slideCount === 0) return;
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

        /* ============================================================
           Promo Detail Modal — banner click → modal open with animation
           ============================================================ */
        var modal = document.getElementById('promoDetailModal');
        if (!modal) return;

        var pmTitle = document.getElementById('pmTitle');
        var pmDescription = document.getElementById('pmDescription');
        var pmImage = document.getElementById('pmImage');
        var pmClaimBtn = document.getElementById('pmClaimBtn');
        var pmStepsContainer = document.getElementById('pmSteps');
        var pmStepsSubtitle = document.getElementById('pmStepsSubtitle');

        /* Step titles + descs are now admin-defined per banner (stored as [{title, desc}, ...]
           in m_promo_banners.how_to_claim). HomeController normalizes legacy string-only banners
           to that shape with auto-titled "Langkah N" before the data reaches this template.
           Generic numbered fallback for any blank title slot post-normalization. */
        var STEP_NUMBERED_TITLE_PREFIX = { id: 'Langkah ', en: 'Step ', zh: '步骤 ' };
        var STEPS_SUBTITLE_TEMPLATE = @json(__('promo.how_to_claim_subtitle'));
        var APP_LOCALE = @json(app()->getLocale());

        var pmTermsList = document.getElementById('pmTermsList');
        /* Snapshot the hardcoded T&C list HTML so we can restore it when a banner has no
           per-banner terms_conditions array set. */
        var pmTermsDefaultHtml = pmTermsList ? pmTermsList.innerHTML : '';
        var pmClaimLabel = document.getElementById('pmClaimLabel');
        var defaultClaimLabel = pmClaimLabel ? pmClaimLabel.textContent : 'Claim Promo';
        var pmCodeSection = document.getElementById('pmCodeSection');
        var pmCodeInput = document.getElementById('pmCodeInput');
        var pmCodeCopyBtn = document.getElementById('pmCodeCopyBtn');
        var pmCodeCopyLabel = document.getElementById('pmCodeCopyLabel');
        var pmCodeCopyIcon = document.getElementById('pmCodeCopyIcon');
        var defaultCodeCopyLabel = pmCodeCopyLabel ? pmCodeCopyLabel.textContent : 'Copy';

        var animationTimer = null;
        var currentPromoCode = '';

        /* HTML-escape arbitrary text for safe injection into innerHTML */
        function escapePromoText(s) {
            var div = document.createElement('div');
            div.textContent = String(s == null ? '' : s);
            return div.innerHTML;
        }

        /* Build the Cara Klaim step circles from the admin's how_to_claim array. Each entry is
           {title, desc} — admin-defined per-banner in the Backend. HomeController normalizes
           legacy [string] data to that shape with auto-titled "Langkah N". Empty title slots
           fall back to a locale-aware "Langkah N" prefix. Sets --step-count CSS variable so
           the grid + dotted connector line scale to match the actual step count (2-5). */
        function renderClaimSteps(howToClaimRaw) {
            var pairs = (Array.isArray(howToClaimRaw) ? howToClaimRaw : [])
                .filter(function(p) {
                    return p && typeof p === 'object' &&
                           typeof p.desc === 'string' && p.desc.trim() !== '';
                })
                .slice(0, 5)
                .map(function(p, i) {
                    var prefix = STEP_NUMBERED_TITLE_PREFIX[APP_LOCALE] || STEP_NUMBERED_TITLE_PREFIX.en;
                    var title = (typeof p.title === 'string' && p.title.trim() !== '')
                        ? p.title
                        : prefix + (i + 1);
                    return { title: title, desc: p.desc };
                });

            var count = pairs.length;
            if (count === 0) {
                /* No usable steps — leave container empty (admin should always configure at least 2,
                   enforced by Backend validation; this is purely a defensive guard) */
                pmStepsContainer.innerHTML = '';
                return;
            }

            pmStepsContainer.style.setProperty('--step-count', count);
            /* Animation disabled — render every step with the `active` class so all circles
               start in their lit (green gradient + scale-up) state and stay there. The cycling
               animation logic in startStepsAnimation() is left in place but no longer invoked. */
            pmStepsContainer.innerHTML = pairs.map(function(p, i) {
                var n = i + 1;
                return '<div class="promo-step active" data-step="' + n + '">' +
                       '<div class="step-circle">' + n + '</div>' +
                       '<h4>' + escapePromoText(p.title) + '</h4>' +
                       '<p>' + escapePromoText(p.desc) + '</p>' +
                       '</div>';
            }).join('');

            /* Update subtitle "Hanya perlu N langkah..." — replace :count placeholder if the lang
               key has it; otherwise leave the rendered fallback intact. */
            if (pmStepsSubtitle && STEPS_SUBTITLE_TEMPLATE.indexOf(':count') !== -1) {
                pmStepsSubtitle.textContent = STEPS_SUBTITLE_TEMPLATE.replace(':count', count);
            }
        }

        function openPromoModal(data) {
            pmTitle.textContent = data.title || '';
            pmDescription.textContent = data.description || '';
            pmImage.src = data.image || '';
            pmImage.alt = data.title || '';
            currentPromoCode = data.promoCode || '';

            /* Dedicated Promo Code section + top Claim button — both shown only when a promo code is set */
            if (currentPromoCode) {
                pmClaimBtn.style.display = '';
                pmCodeInput.value = currentPromoCode;
                pmCodeSection.classList.remove('hidden');
            } else {
                pmClaimBtn.style.display = 'none';
                pmCodeInput.value = '';
                pmCodeSection.classList.add('hidden');
            }

            /* Cara Klaim — render N step circles dynamically (2-5 from admin's how_to_claim,
               or the 4-step hardcoded fallback when the array has 0/1 items). */
            renderClaimSteps(data.howToClaim);

            /* Syarat & Ketentuan list — render from per-banner terms_conditions when present;
               fall back to the hardcoded 4-line default snapshot otherwise. */
            if (pmTermsList) {
                var terms = Array.isArray(data.terms) ? data.terms.filter(function(s) {
                    return typeof s === 'string' && s.trim() !== '';
                }) : [];
                if (terms.length > 0) {
                    pmTermsList.innerHTML = terms.map(function(t) {
                        /* Escape HTML to prevent XSS — admin-entered text rendered safely */
                        var div = document.createElement('div');
                        div.textContent = t;
                        return '<li><i class="fas fa-check-circle"></i> ' + div.innerHTML + '</li>';
                    }).join('');
                } else {
                    pmTermsList.innerHTML = pmTermsDefaultHtml;
                }
            }

            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            /* Animation disabled — steps render with the `active` class statically (see renderClaimSteps).
               startStepsAnimation()/stopStepsAnimation() are intentionally not called. */
        }

        function closePromoModal() {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }

        /* Animation: cycle through however many step circles renderClaimSteps() built (2-5).
           - Each step lights up sequentially every 800ms
           - After the last step, hold 3s then reset all and start over.
           Uses a live querySelectorAll so the loop adapts automatically to the current step count. */
        function startStepsAnimation() {
            var steps = document.querySelectorAll('#pmSteps .promo-step');
            if (!steps.length) return;
            stopStepsAnimation();

            var i = 0;
            function tick() {
                if (i === 0) {
                    steps.forEach(function(s) { s.classList.remove('active'); });
                }
                if (i < steps.length) {
                    steps[i].classList.add('active');
                    i++;
                    animationTimer = setTimeout(tick, 800);
                } else {
                    /* All steps lit — pause 3s before restart */
                    animationTimer = setTimeout(function() { i = 0; tick(); }, 3000);
                }
            }
            tick();
        }

        function stopStepsAnimation() {
            if (animationTimer) {
                clearTimeout(animationTimer);
                animationTimer = null;
            }
        }

        function copyCodeToClipboard(code, feedbackEl, successText) {
            if (!code) return;
            var done = function() {
                if (!feedbackEl) return;
                var orig = feedbackEl.textContent;
                feedbackEl.textContent = successText || 'Copied!';
                setTimeout(function() { feedbackEl.textContent = orig; }, 1800);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(done).catch(function() {
                    /* Fallback for older browsers */
                    var ta = document.createElement('textarea');
                    ta.value = code;
                    document.body.appendChild(ta);
                    ta.select();
                    try { document.execCommand('copy'); done(); } catch (e) {}
                    document.body.removeChild(ta);
                });
            } else {
                /* Insecure context fallback */
                var ta = document.createElement('textarea');
                ta.value = code;
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); done(); } catch (e) {}
                document.body.removeChild(ta);
            }
        }

        /* Safe JSON parse: returns [] when the attribute is missing or malformed */
        function parseJsonArray(raw) {
            if (!raw) return [];
            try {
                var v = JSON.parse(raw);
                return Array.isArray(v) ? v : [];
            } catch (e) {
                return [];
            }
        }

        /* Click handler: intercept banner click, open modal */
        document.querySelectorAll('.promo-link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                openPromoModal({
                    id: this.dataset.promoId,
                    title: this.dataset.promoTitle,
                    description: this.dataset.promoDescription,
                    promoCode: this.dataset.promoCode,
                    image: this.dataset.promoImage,
                    howToClaim: parseJsonArray(this.dataset.promoHowToClaim),
                    terms: parseJsonArray(this.dataset.promoTerms),
                });
            });
        });

        /* Close handlers: backdrop, X button, Escape key */
        modal.querySelectorAll('[data-promo-close]').forEach(function(el) {
            el.addEventListener('click', closePromoModal);
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) closePromoModal();
        });

        /* Claim button → copy promo code, swap label briefly */
        pmClaimBtn.addEventListener('click', function() {
            copyCodeToClipboard(currentPromoCode, pmClaimLabel, '{{ __('promo.copied') }}');
        });

        /* Dedicated Promo Code section copy button → swap icon + label + button color briefly */
        pmCodeCopyBtn.addEventListener('click', function() {
            copyCodeToClipboard(currentPromoCode, pmCodeCopyLabel, '{{ __('promo.copied') }}');
            pmCodeCopyIcon.className = 'fas fa-check';
            pmCodeCopyBtn.classList.add('copied');
            /* Auto-select input text so users can manually re-copy if needed */
            pmCodeInput.select();
            setTimeout(function() {
                pmCodeCopyIcon.className = 'fas fa-copy';
                pmCodeCopyBtn.classList.remove('copied');
            }, 1800);
        });
    });
</script>
