<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Pagination ✨</h1>
        </div>

        <div class="border-t border-slate-200">

            <!-- Components -->
            <div class="space-y-8 mt-8">

                <!-- Option 1 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Option 1</h2>
                    <div class="px-6 py-8 bg-slate-50 border border-slate-200 rounded-sm">
                        <!-- Start -->
                        <div class="flex justify-center">
                            <nav class="flex" role="navigation" aria-label="Navigation">
                                <div class="mr-2">
                                    <span class="inline-flex items-center justify-center rounded leading-5 px-2.5 py-2 bg-white border border-slate-200 text-slate-300">
                                        <span class="sr-only">Previous</span><wbr />
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 16 16">
                                            <path d="M9.4 13.4l1.4-1.4-4-4 4-4-1.4-1.4L4 8z" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="inline-flex text-sm font-medium -space-x-px shadow-sm">
                                    <li>
                                        <span class="inline-flex items-center justify-center rounded-l leading-5 px-3.5 py-2 bg-white border border-slate-200 text-indigo-500">1</span>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white hover:bg-indigo-500 border border-slate-200 text-slate-600 hover:text-white" href="#0">2</a>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white hover:bg-indigo-500 border border-slate-200 text-slate-600 hover:text-white" href="#0">3</a>
                                    </li>
                                    <li>
                                        <span class="inline-flex items-center justify-center leading-5 px-3.5 py-2 bg-white border border-slate-200 text-slate-400">…</span>
                                    </li>
                                    <li>
                                        <a class="inline-flex items-center justify-center rounded-r leading-5 px-3.5 py-2 bg-white hover:bg-indigo-500 border border-slate-200 text-slate-600 hover:text-white" href="#0">9</a>
                                    </li>
                                </ul>
                                <div class="ml-2">
                                    <a href="#0" class="inline-flex items-center justify-center rounded leading-5 px-2.5 py-2 bg-white hover:bg-indigo-500 border border-slate-200 text-slate-600 hover:text-white shadow-sm">
                                        <span class="sr-only">Next</span><wbr />
                                        <svg class="h-4 w-4 fill-current" viewBox="0 0 16 16">
                                            <path d="M6.6 13.4L5.2 12l4-4-4-4 1.4-1.4L12 8z" />
                                        </svg>
                                    </a>
                                </div>
                            </nav>
                        </div>
                        <!-- End -->
                    </div>
                </div>

                <!-- Option 2 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Option 2</h2>
                    <div class="px-6 py-8 bg-slate-50 border border-slate-200 rounded-sm">
                        <!-- Start -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <nav class="mb-4 sm:mb-0 sm:order-1" role="navigation" aria-label="Navigation">
                                <ul class="flex justify-center">
                                    <li class="ml-3 first:ml-0">
                                        <a class="btn bg-white border-slate-200 text-slate-300 cursor-not-allowed" href="#0" disabled>&lt;- Previous</a>
                                    </li>
                                    <li class="ml-3 first:ml-0">
                                        <a class="btn bg-white border-slate-200 hover:border-slate-300 text-indigo-500" href="#0">Next -&gt;</a>
                                    </li>            
                                </ul>
                            </nav>
                            <div class="text-sm text-slate-500 text-center sm:text-left">
                                Showing <span class="font-medium text-slate-600">1</span> to <span class="font-medium text-slate-600">10</span> of <span class="font-medium text-slate-600">467</span> results
                            </div>
                        </div>
                        <!-- End -->
                    </div>
                </div>

                <!-- Option 3 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Option 3</h2>
                    <div class="px-6 py-8 bg-slate-50 border border-slate-200 rounded-sm">
                        <!-- Start -->
                        <div>
                            <nav class="flex justify-between" role="navigation" aria-label="Navigation">
                                <div class="flex-1 mr-2">
                                    <a class="btn bg-white border-slate-200 text-slate-300 cursor-not-allowed" href="#0" disabled>&lt;-<span class="hidden sm:inline">&nbsp;Previous</span></a>
                                </div>
                                <div class="grow text-center">
                                    <ul class="inline-flex text-sm font-medium -space-x-px">
                                        <li>
                                            <span class="inline-flex items-center justify-center rounded-full leading-5 px-2 py-2 bg-white border border-slate-200 text-indigo-500 shadow-sm"><span class="w-5">1</span></span>
                                        </li>
                                        <li>
                                            <a class="inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent" href="#0"><span class="w-5">2</span></a>
                                        </li>
                                        <li>
                                            <a class="inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent" href="#0"><span class="w-5">3</span></a>
                                        </li>
                                        <li>
                                            <span class="inline-flex items-center justify-center leading-5 px-2 py-2 text-slate-400">…</span>
                                        </li>
                                        <li>
                                            <a class="inline-flex items-center justify-center rounded-r leading-5 px-2 py-2 text-slate-600 hover:text-indigo-500 border border-transparent" href="#0"><span class="w-5">9</span></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="flex-1 text-right ml-2">
                                    <a class="btn bg-white border-slate-200 hover:border-slate-300 text-indigo-500" href="#0"><span class="hidden sm:inline">Next&nbsp;</span>-&gt;</a>
                                </div>
                            </nav>
                        </div>
                        <!-- End -->
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
