<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Accordion ✨</h1>
        </div>

        <div class="border-t border-slate-200">

            <!-- Components -->
            <div class="space-y-8 mt-8">

                <!-- Basic Accordion -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Basic Accordion</h2>
                    <!-- Start -->
                    <div class="px-5 py-4 rounded-sm border border-slate-200" x-data="{ open: false }">
                        <button
                            class="flex items-center justify-between w-full group mb-1"
                            @click.prevent="open = !open"
                            :aria-expanded="open"
                        >
                            <div class="text-sm text-slate-800 font-medium">Accordion Title</div>
                            <svg class="w-8 h-8 shrink-0 fill-current text-slate-400 group-hover:text-slate-500 ml-3" :class="{ 'rotate-180': open }" viewBox="0 0 32 32">
                                <path d="M16 20l-5.4-5.4 1.4-1.4 4 4 4-4 1.4 1.4z" />
                            </svg>
                        </button>
                        <div class="text-sm" x-show="open" x-cloak>
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam quis. Ut enim ad minim veniam quis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                        </div>
                    </div>
                    <!-- End -->
                </div>                            

                <!-- Table Row with Accordion -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Table Row with Accordion</h2>
                    <!-- Start -->
                    <div class="rounded-sm border border-slate-200">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full divide-y divide-slate-200">
                                <!-- Table body -->
                                <tbody class="text-sm" x-data="{ open: false }">
                                    <!-- Row -->
                                    <tr>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="flex items-center text-slate-800">
                                                <div class="w-10 h-10 shrink-0 flex items-center justify-center bg-slate-100 rounded-full mr-2 sm:mr-3">
                                                    <img class="rounded-full ml-1" src="{{ asset('images/user-40-07.jpg') }}" width="40" height="40" alt="User 01" />
                                                </div>
                                                <div class="font-medium text-slate-800">Mark Cameron</div>
                                            </div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left font-medium text-emerald-500">$129.00</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="inline-flex font-medium bg-amber-100 text-amber-600 rounded-full text-center px-2.5 py-0.5">Refunded</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-center">1</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left">🇲🇽 New Mexico, MX</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="w-4 h-4 fill-current text-slate-400 shrink-0 mr-2" viewBox="0 0 16 16">
                                                    <path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" />
                                                </svg>
                                                <div>Subscription</div>
                                            </div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                            <div class="flex items-center">
                                                <button
                                                    class="text-slate-400 hover:text-slate-500 transform"
                                                    :class="{ 'rotate-180': open }"
                                                    @click.prevent="open = !open"
                                                    :aria-expanded="open"
                                                    aria-controls="description-01"
                                                >
                                                    <span class="sr-only">Menu</span>
                                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                                        <path d="M16 20l-5.4-5.4 1.4-1.4 4 4 4-4 1.4 1.4z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <!--
                                    Example of content revealing when clicking the button on the right side:
                                    Note that you must set a "colspan" attribute on the <td> element,
                                    and it should match the number of columns in your table
                                    -->
                                    <tr id="description-01" role="region" x-show="open" x-cloak>
                                        <td colspan="10" class="px-2 first:pl-5 last:pr-5 py-3">
                                            <div class="flex items-center bg-slate-50 p-3 -mt-3">
                                                <svg class="w-4 h-4 shrink-0 fill-current text-slate-400 mr-2">
                                                    <path d="M1 16h3c.3 0 .5-.1.7-.3l11-11c.4-.4.4-1 0-1.4l-3-3c-.4-.4-1-.4-1.4 0l-11 11c-.2.2-.3.4-.3.7v3c0 .6.4 1 1 1zm1-3.6l10-10L13.6 4l-10 10H2v-1.6z" />
                                                </svg>
                                                <div class="italic">Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End -->
                </div>

                <!-- Rich Table Row with Accordion -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Rich Table Row with Accordion</h2>
                    <!-- Start -->
                    <div class="rounded-sm border border-slate-200">
                        <div class="overflow-x-auto">
                            <table class="table-auto w-full divide-y divide-slate-200">
                                <!-- Table body -->
                                <tbody class="text-sm" x-data="{ open: false }">
                                    <!-- Row -->
                                    <tr>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="flex items-center text-slate-800">
                                                <div class="w-10 h-10 shrink-0 flex items-center justify-center bg-slate-100 rounded-full mr-2 sm:mr-3">
                                                    <img class="rounded-full ml-1" src="{{ asset('images/user-40-07.jpg') }}" width="40" height="40" alt="User 01" />
                                                </div>
                                                <div class="font-medium text-slate-800">Mark Cameron</div>
                                            </div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left">mark.cameron@app.com</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left">🇬🇧 London, UK</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left">22/01/2021</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                            <div class="text-left text-emerald-500 font-medium">+249.88</div>
                                        </td>
                                        <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px">
                                            <div class="flex items-center">
                                                <button
                                                    class="text-slate-400 hover:text-slate-500 transform"
                                                    :class="{ 'rotate-180': open }"
                                                    @click.prevent="open = !open"
                                                    :aria-expanded="open"
                                                    aria-controls="description-01"
                                                >
                                                    <span class="sr-only">Menu</span>
                                                    <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                                        <path d="M16 20l-5.4-5.4 1.4-1.4 4 4 4-4 1.4 1.4z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>                                                    
                                    </tr>
                                    <!--
                                    Example of content revealing when clicking the button on the right side:
                                    Note that you must set a "colspan" attribute on the <td> element,
                                    and it should match the number of columns in your table
                                    -->
                                    <tr id="description-01" role="region" x-show="open" x-cloak>
                                        <td colspan="10" class="px-2 first:pl-5 last:pr-5 py-3">
                                            <div class="bg-slate-50 p-3 -mt-3">
                                                <div class="text-sm mb-3">
                                                    <div class="font-medium text-slate-800 mb-1">Excepteur sint occaecat cupidatat.</div>
                                                    <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam quis. Ut enim ad minim veniam quis. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
                                                </div>
                                                <button class="btn-xs bg-indigo-500 hover:bg-indigo-600 text-white">Approve</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End -->
                </div>                            

            </div>

        </div>

    </div>
</x-app-layout>
