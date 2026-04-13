<!-- Footer Component — 5-column layout with app links, quick links, business, socials, support
     Content loaded dynamically from CMS (FooterComposer), with translation key fallbacks -->
@php
    use App\Helpers\MultilangHelper;
    $adminUrl = config('app.admin_url', env('ADMIN_URL', ''));
    /** Extract CMS content values for app links */
    $appStoreUrl = isset($footerContent) && $footerContent->has('app_store_url') ? $footerContent->get('app_store_url')->value : '#';
    $playStoreUrl = isset($footerContent) && $footerContent->has('play_store_url') ? $footerContent->get('play_store_url')->value : '#';
    $loginRegisterUrl = isset($footerContent) && $footerContent->has('login_register_url') ? $footerContent->get('login_register_url')->value : '/login';
@endphp
<footer class="site-footer text-white py-16 px-4" style="background: linear-gradient(180deg, #0f172a 0%, #0c1322 100%); position: relative;">
    <!-- Top glass edge -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.12), transparent);"></div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10">

        <!-- Column 1: Ulin Mahoni — Logo + App buttons -->
        <div>
            <div class="flex items-center gap-3 mb-4">
                <!-- Footer logo scaled 50% larger than original h-10 (40px → 60px) -->
                <img src="{{ asset('images/assets/ulinmahoni-logo-footer.png') }}" alt="Ulin Mahoni" class="w-auto" style="height: 60px;">
                <h4 class="text-2xl font-bold text-white tracking-tight">Ulin Mahoni</h4>
            </div>

            <!-- App Store Buttons — always one row -->
            <div class="flex flex-nowrap gap-2 mb-4">
                @if($appStoreUrl && $appStoreUrl !== '#')
                    <a href="{{ $appStoreUrl }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-white text-xs font-medium whitespace-nowrap transition-colors duration-300"
                        style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fab fa-apple text-base"></i> App Store
                    </a>
                @endif
                @if($playStoreUrl && $playStoreUrl !== '#')
                    <a href="{{ $playStoreUrl }}" target="_blank"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-white text-xs font-medium whitespace-nowrap transition-colors duration-300"
                        style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                        <i class="fab fa-google-play text-base"></i> Play Store
                    </a>
                @endif
            </div>

            <!-- Login / Register Button -->
            <div class="mb-5">
                <a href="{{ $loginRegisterUrl }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-medium transition-colors duration-300"
                    style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.15);">
                    <i class="fas fa-user"></i> {{ __('common.footer.login_register') }}
                </a>
            </div>

            <!-- Payment Methods -->
            @if(isset($footerPayments) && $footerPayments->count() > 0)
                <div>
                    <h4 class="text-lg font-semibold mb-3" style="color: #2dd4bf;">{{ __('common.footer.payment_methods') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($footerPayments as $payment)
                            @php
                                $iconUrl = $payment->icon_image;
                                if ($iconUrl && !str_starts_with($iconUrl, 'http')) {
                                    $iconUrl = $adminUrl . '/storage/' . $iconUrl;
                                }
                            @endphp
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs text-gray-300"
                                style="background: rgba(255, 255, 255, 0.06); border: 1px solid rgba(255, 255, 255, 0.1);">
                                @if($iconUrl)
                                    <!-- Constrain logo dimensions for consistent sizing across all payment icons -->
                                    <img src="{{ $iconUrl }}" alt="{{ $payment->name }}" class="object-contain" style="height: 14px; max-width: 32px;">
                                @endif
                                {{ $payment->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Column 2: Tautan Cepat (Quick Links) -->
        <div>
            <h4 class="text-lg font-semibold mb-5" style="color: #2dd4bf;">{{ __('common.footer.quick_links') }}</h4>
            <ul class="space-y-3 text-gray-400 text-sm">
                @if(isset($footerQuickLinks) && $footerQuickLinks->count() > 0)
                    @foreach($footerQuickLinks as $link)
                        <li>
                            <a href="{{ $link->url }}" class="hover:text-white transition-colors duration-300">
                                {{ $link->label }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li><a href="{{ route('homepage') }}" class="hover:text-white transition-colors duration-300">{{ __('common.navigation.home') }}</a></li>
                    <li><a href="{{ route('properties.index') }}" class="hover:text-white transition-colors duration-300">{{ __('common.navigation.properties') }}</a></li>
                @endif
            </ul>
        </div>

        <!-- Column 3: Ulin Mahoni Bisnis -->
        <div>
            <h4 class="text-lg font-semibold mb-5" style="color: #2dd4bf;">{{ __('common.footer.business') }}</h4>
            <ul class="space-y-3 text-gray-400 text-sm">
                @if(isset($footerBusinessLinks) && $footerBusinessLinks->count() > 0)
                    @foreach($footerBusinessLinks as $link)
                        @php
                            /** Look up translation key by label (e.g. "Partnership" → common.footer.business_partnership) */
                            $transKey = 'common.footer.business_' . strtolower($link->label);
                            $translatedLabel = __($transKey) !== $transKey ? __($transKey) : $link->label;
                        @endphp
                        <li>
                            <a href="{{ $link->url }}" class="hover:text-white transition-colors duration-300">
                                {{ $translatedLabel }}
                            </a>
                        </li>
                    @endforeach
                @else
                    <li><a href="/kerjasama" class="hover:text-white transition-colors duration-300">{{ __('common.footer.business_partnership') }}</a></li>
                    <li><a href="/business" class="hover:text-white transition-colors duration-300">{{ __('common.footer.business_corporate') }}</a></li>
                @endif
            </ul>
        </div>

        <!-- Column 4: Ikuti Kami (Follow Us) -->
        <div>
            <h4 class="text-lg font-semibold mb-5" style="color: #2dd4bf;">{{ __('common.footer.follow_us') }}</h4>
            <div class="flex flex-wrap gap-3">
                @if(isset($footerSocials) && $footerSocials->count() > 0)
                    @foreach($footerSocials as $social)
                        <a href="{{ $social->url }}" target="_blank" class="group" aria-label="{{ $social->name }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300"
                                style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                                @if($social->icon_image)
                                    <img src="{{ $social->icon_image }}" alt="{{ $social->name }}" class="w-4 h-4 object-contain opacity-60 group-hover:opacity-100 transition-opacity">
                                @elseif($social->icon_class)
                                    <i class="{{ $social->icon_class }} text-lg text-gray-400 group-hover:text-white transition-colors"></i>
                                @endif
                            </div>
                        </a>
                    @endforeach
                @else
                    <a href="https://instagram.com/ulinmahoni/" class="group" aria-label="Instagram">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                            <i class="fab fa-instagram text-lg text-gray-400 group-hover:text-pink-400 transition-colors"></i>
                        </div>
                    </a>
                    <a href="https://www.tiktok.com/@ulin.mahoni" target="_blank" class="group" aria-label="TikTok">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.12);">
                            <i class="fab fa-tiktok text-lg text-gray-400 group-hover:text-white transition-colors"></i>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        <!-- Column 5: Support -->
        <div>
            <h4 class="text-lg font-semibold mb-5" style="color: #2dd4bf;">{{ __('common.footer.support') }}</h4>
            <ul class="space-y-3 text-gray-400 text-sm">
                @if(isset($footerContacts) && $footerContacts->count() > 0)
                    @foreach($footerContacts as $contact)
                        <li class="flex items-start">
                            @if($contact->icon_class)
                                <i class="{{ $contact->icon_class }} mr-2 mt-0.5 text-gray-400"></i>
                            @endif
                            @if($contact->link_url)
                                <a href="{{ $contact->link_url }}" target="_blank" class="hover:text-white transition-colors duration-300">
                                    {{ $contact->value }}
                                </a>
                            @else
                                <span>{{ $contact->value }}</span>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li class="flex items-center">
                        <i class="fab fa-whatsapp mr-2"></i>
                        <a href="https://wa.me/6281188099700" target="_blank" class="hover:text-white transition-colors duration-300">+62 811-8809-9700</a>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        <a href="mailto:headoffice@ulinmahoni.com" class="hover:text-white transition-colors duration-300">headoffice@ulinmahoni.com</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <!-- Copyright + Legal Links -->
    <div class="max-w-7xl mx-auto mt-12 pt-8 text-center text-gray-500 text-sm" style="border-top: 1px solid rgba(255, 255, 255, 0.06);">
        <p>
            {!! __('common.footer.copyright') !!}
            &nbsp;&nbsp;
            <a href="/terms-of-services" class="hover:text-white transition-colors duration-300" style="color: #2dd4bf;">{{ __('common.footer.terms') }}</a>
            &nbsp;|&nbsp;
            <a href="/privacy-policy" class="hover:text-white transition-colors duration-300" style="color: #2dd4bf;">{{ __('common.footer.privacy') }}</a>
        </p>
    </div>
</footer>
