<!-- Footer Component — all text uses __() translation helpers for i18n (ID/EN/ZH) -->
<footer class="bg-gray-800 text-white py-12 px-4">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-8">
        <div>
            <h4 class="text-2xl font-bold mb-6 text-white">Ulin Mahoni</h4>
            <p class="text-gray-400 text-sm">
                {{ __('common.footer.tagline') }}
            </p>
        </div>

        <div>
            <h4 class="text-xl font-semibold mb-5 text-white">{{ __('common.footer.quick_links') }}</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
                <li>
                    <a href="{{ route('homepage') }}" class="hover:text-white transition">
                        {{ __('common.navigation.home') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('properties.index') }}" class="hover:text-white transition">
                        {{ __('common.navigation.properties') }}
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-xl font-semibold mb-5 text-white">{{ __('common.footer.contact_us') }}</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
                <li class="flex items-start">
                    <i class="fas fa-map-marker-alt mr-2 mt-1 text-teal-500"></i>
                    <span>Jl. Ciheuleut Dalam, Tegallega, Bogor, Jawa Barat, 16129</span>
                </li>
                <li class="flex items-center">
                    <i class="fab fa-whatsapp mr-2 text-green-500"></i>
                    <a href="https://wa.me/6281188099700" target="_blank" class="hover:text-white hover:underline transition-colors">
                        +62 811-8809-9700
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <h4 class="text-xl font-semibold mb-5 text-white">{{ __('common.footer.follow_us') }}</h4>
            <div class="flex space-x-4">
                <!-- Instagram -->
                <a href="https://instagram.com/ulinmahoni/" class="text-gray-400 hover:text-pink-600 transition-colors duration-200" aria-label="Instagram">
                    <div class="w-10 h-10 rounded-full bg-gray-800 hover:bg-white flex items-center justify-center transition-colors duration-200">
                        <i class="fa-brands fa-instagram text-lg"></i>
                    </div>
                </a>
                <!-- TikTok -->
                <a href="https://www.tiktok.com/@ulin.mahoni" target="_blank" class="text-gray-400 hover:text-white transition-colors duration-200" aria-label="TikTok">
                    <div class="w-10 h-10 rounded-full bg-gray-800 hover:bg-white flex items-center justify-center transition-colors duration-200">
                        <i class="fa-brands fa-tiktok text-lg"></i>
                    </div>
                </a>
                <!-- Twitter (X) -->
                <a href="https://x.com/ulinmahoni?s=21" target="_blank" aria-label="Twitter">
                    <div class="w-10 h-10 rounded-full bg-gray-800 hover:bg-white flex items-center justify-center transition-colors duration-200">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/3/33/X_logo-white.png" alt="X" class="w-5 h-5 object-contain">
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400 text-sm">
        <p>{!! __('common.footer.copyright') !!}</p>
    </div>
</footer>
