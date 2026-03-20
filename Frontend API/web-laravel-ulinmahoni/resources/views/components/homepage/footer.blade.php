<!-- Footer Component — liquid glass dark footer with frosted social icons
     All text uses __() translation helpers for i18n (ID/EN/ZH) -->
<footer class="site-footer text-white py-16 px-4" style="background: linear-gradient(180deg, #1a1a2e 0%, #12122a 100%); position: relative;">
    <!-- Top glass edge — subtle light refraction line -->
    <div style="position: absolute; top: 0; left: 0; right: 0; height: 1px; background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.12), transparent);"></div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-10">
        <div>
            <h4 class="text-2xl font-light mb-6 text-white tracking-tight">Ulin Mahoni</h4>
            <p class="text-gray-400 text-sm leading-relaxed">
                {{ __('common.footer.tagline') }}
            </p>
        </div>

        <div>
            <h4 class="text-lg font-medium mb-5 text-white">{{ __('common.footer.quick_links') }}</h4>
            <ul class="space-y-3 text-gray-400 text-sm">
                <li>
                    <a href="{{ route('homepage') }}" class="hover:text-white transition-colors duration-300">
                        {{ __('common.navigation.home') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('properties.index') }}" class="hover:text-white transition-colors duration-300">
                        {{ __('common.navigation.properties') }}
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-lg font-medium mb-5 text-white">{{ __('common.footer.contact_us') }}</h4>
            <ul class="space-y-3 text-gray-400 text-sm">
                <li class="flex items-start">
                    <i class="fas fa-map-marker-alt mr-2 mt-1" style="color: var(--accent, #0ea5a0);"></i>
                    <span>Jl. Ciheuleut Dalam, Tegallega, Bogor, Jawa Barat, 16129</span>
                </li>
                <li class="flex items-center">
                    <i class="fab fa-whatsapp mr-2 text-green-400"></i>
                    <a href="https://wa.me/6281188099700" target="_blank" class="hover:text-white hover:underline transition-colors duration-300">
                        +62 811-8809-9700
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-lg font-medium mb-5 text-white">{{ __('common.footer.follow_us') }}</h4>
            <div class="flex space-x-3">
                <!-- Instagram — glass icon circle -->
                <a href="https://instagram.com/ulinmahoni/" class="group" aria-label="Instagram">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px);">
                        <i class="fa-brands fa-instagram text-lg text-gray-400 group-hover:text-pink-400 transition-colors"></i>
                    </div>
                </a>
                <!-- TikTok — glass icon circle -->
                <a href="https://www.tiktok.com/@ulin.mahoni" target="_blank" class="group" aria-label="TikTok">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px);">
                        <i class="fa-brands fa-tiktok text-lg text-gray-400 group-hover:text-white transition-colors"></i>
                    </div>
                </a>
                <!-- Twitter (X) — glass icon circle -->
                <a href="https://x.com/ulinmahoni?s=21" target="_blank" class="group" aria-label="Twitter">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300" style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px);">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/33/X_logo-white.png" alt="X" class="w-4 h-4 object-contain opacity-60 group-hover:opacity-100 transition-opacity">
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="mt-12 pt-8 text-center text-gray-500 text-sm" style="border-top: 1px solid rgba(255, 255, 255, 0.06);">
        <p>{!! __('common.footer.copyright') !!}</p>
    </div>
</footer>
