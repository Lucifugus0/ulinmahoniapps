<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-5">

            <!-- Title -->
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Find the right product for you ✨</h1>

        </div>

        <!-- Search form -->
        <div class="max-w-xl mb-5">
            <form class="relative">
                <label for="app-search" class="sr-only">Search</label>
                <input id="app-search" class="form-input w-full pl-9 py-3 focus:border-slate-300" type="search" placeholder="Search…" />
                <button class="absolute inset-0 right-auto group" type="submit" aria-label="Search">
                    <svg class="w-4 h-4 shrink-0 fill-current text-slate-400 group-hover:text-slate-500 ml-3 mr-2" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                        <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Filters -->
        <div class="mb-4 border-b border-slate-200">
            <ul class="text-sm font-medium flex flex-nowrap -mx-4 sm:-mx-6 lg:-mx-8 overflow-x-scroll no-scrollbar">
                <li class="pb-3 mr-6 last:mr-0 first:pl-4 sm:first:pl-6 lg:first:pl-8 last:pr-4 sm:last:pr-6 lg:last:pr-8">
                    <a class="text-indigo-500 whitespace-nowrap" href="#0">View All</a>
                </li>
                <li class="pb-3 mr-6 last:mr-0 first:pl-4 sm:first:pl-6 lg:first:pl-8 last:pr-4 sm:last:pr-6 lg:last:pr-8">
                    <a class="text-slate-500 hover:text-slate-600 whitespace-nowrap" href="#0">Courses</a>
                </li>
                <li class="pb-3 mr-6 last:mr-0 first:pl-4 sm:first:pl-6 lg:first:pl-8 last:pr-4 sm:last:pr-6 lg:last:pr-8">
                    <a class="text-slate-500 hover:text-slate-600 whitespace-nowrap" href="#0">Digital Goods</a>
                </li>
                <li class="pb-3 mr-6 last:mr-0 first:pl-4 sm:first:pl-6 lg:first:pl-8 last:pr-4 sm:last:pr-6 lg:last:pr-8">
                    <a class="text-slate-500 hover:text-slate-600 whitespace-nowrap" href="#0">Online Events</a>
                </li>
                <li class="pb-3 mr-6 last:mr-0 first:pl-4 sm:first:pl-6 lg:first:pl-8 last:pr-4 sm:last:pr-6 lg:last:pr-8">
                    <a class="text-slate-500 hover:text-slate-600 whitespace-nowrap" href="#0">Crowdfunding</a>
                </li>
            </ul>
        </div>

        <!-- Page content -->
        <div>

            <!-- Cards 1 (Video Courses) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Video Courses</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-01 />                            
                </div>
            </div>

            <!-- Cards 2 (Digital Goods) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Digital Goods</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-02 />
                </div>
            </div>

            <!-- Cards 3 (Online Events) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Online Events</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-03 />
                </div>
            </div>

            <!-- Cards 4 (Crowdfunding) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Crowdfunding</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-04 />
                </div>
            </div>

            <!-- Cards 5 (Popular Categories) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Popular Categories</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-05 />
                </div>
            </div>

            <!-- Cards 6 (Trending Now) -->
            <div class="mt-8">
                <h2 class="text-xl leading-snug text-slate-800 font-bold mb-5">Trending Now</h2>
                <div class="grid grid-cols-12 gap-6">
                    <x-ecommerce.shop-cards-06 />
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
