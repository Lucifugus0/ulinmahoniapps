<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Alert & Banner ✨</h1>
        </div>

        <div class="border-t border-slate-200">

            <!-- Components -->
            <div class="space-y-8 mt-8">

                <!-- Banner -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Banner</h2>
                    <div class="space-y-3">

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-amber-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div class="font-medium">We’re currently experiencing an increase in inquiries. There may be a delay in responses from the Support.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-emerald-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div class="font-medium">All Systems are operational. Sorry for any inconvenience this might cause!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-rose-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div class="font-medium">Customers are experiencing a delay in withdrawing their funds!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-indigo-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div class="font-medium">There are over 200 apps available in the store!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->                                    

                    </div>
                </div>

                <!-- Banner 2 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Banner 2</h2>
                    <div class="space-y-3">

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-amber-100 border border-amber-200 text-amber-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div>We’re currently experiencing an increase in inquiries. There may be a delay in responses from the Support.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-emerald-100 border border-emerald-200 text-emerald-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div>All Systems are operational. Sorry for any inconvenience this might cause!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-rose-100 border border-rose-200 text-rose-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div>Customers are experiencing a delay in withdrawing their funds!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="px-4 py-2 rounded-sm text-sm bg-indigo-100 border border-indigo-200 text-indigo-500">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div>There are over 200 apps available in the store!</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->                                    

                    </div>
                </div> 
                
                <!-- Toast -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Toast</h2>
                    <div class="space-y-3">

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-amber-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div class="font-medium">A warning toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- End -->
                        </div>

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-emerald-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div class="font-medium">A successful toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-rose-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div class="font-medium">A dangerous toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->

                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-indigo-500 text-white">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div class="font-medium">An informational toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->                                    

                    </div>
                </div>
                
                <!-- Toast 2 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Toast 2</h2>
                    <div class="space-y-3">
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-amber-100 border border-amber-200 text-amber-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div>A warning toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- End -->
                        </div>
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-emerald-100 border border-emerald-200 text-emerald-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div>A successful toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-rose-100 border border-rose-200 text-rose-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div>A dangerous toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-indigo-100 border border-indigo-200 text-indigo-500">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current opacity-80 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div>An informational toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                    </div>
                </div>

                <!-- Toast 3 -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Toast 3</h2>
                    <div class="space-y-3">
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-amber-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div>A warning toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <!-- End -->
                        </div>
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-emerald-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div>A successful toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-rose-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div>A dangerous toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex min-w-80 px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-indigo-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div>An informational toast.</div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- End -->
                
                    </div>
                </div>
                
                <!-- Notification -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Notification</h2>
                    <div class="space-y-3">
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex flex-col max-w-lg px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-amber-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                                        </svg>
                                        <div>
                                            <div class="font-medium text-slate-800 mb-1">Merged Pull Request</div>
                                            <div>Lorem ipsum dolor sit amet, consectetur adipiscing sed do eiusmod tempor incididunt ut labore et dolore.</div>
                                        </div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <a class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">Action -&gt;</a>
                                </div>
                            </div>
                            <!-- End -->
                        </div>
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex flex-col max-w-lg px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-emerald-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zM7 11.4L3.6 8 5 6.6l2 2 4-4L12.4 6 7 11.4z" />
                                        </svg>
                                        <div>
                                            <div class="font-medium text-slate-800 mb-1">Merged Pull Request</div>
                                            <div>Lorem ipsum dolor sit amet, consectetur adipiscing sed do eiusmod tempor incididunt ut labore et dolore.</div>
                                        </div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <a class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">Action -&gt;</a>
                                </div>
                            </div>
                        </div>                                    
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex flex-col max-w-lg px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-rose-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z" />
                                        </svg>
                                        <div>
                                            <div class="font-medium text-slate-800 mb-1">Merged Pull Request</div>
                                            <div>Lorem ipsum dolor sit amet, consectetur adipiscing sed do eiusmod tempor incididunt ut labore et dolore.</div>
                                        </div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <a class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">Action -&gt;</a>
                                </div>
                            </div>
                        </div>                                      
                        <!-- End -->
                
                        <!-- Start -->
                        <div x-show="open" x-data="{ open: true }">
                            <div class="inline-flex flex-col max-w-lg px-4 py-2 rounded-sm text-sm bg-white shadow-lg border border-slate-200 text-slate-600">
                                <div class="flex w-full justify-between items-start">
                                    <div class="flex">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-indigo-500 mt-[3px] mr-3" viewBox="0 0 16 16">
                                            <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z" />
                                        </svg>
                                        <div>
                                            <div class="font-medium text-slate-800 mb-1">Merged Pull Request</div>
                                            <div>Lorem ipsum dolor sit amet, consectetur adipiscing sed do eiusmod tempor incididunt ut labore et dolore.</div>
                                        </div>
                                    </div>
                                    <button class="opacity-70 hover:opacity-80 ml-3 mt-[3px]" @click="open = false">
                                        <div class="sr-only">Close</div>
                                        <svg class="w-4 h-4 fill-current">
                                            <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="text-right mt-1">
                                    <a class="font-medium text-indigo-500 hover:text-indigo-600" href="#0">Action -&gt;</a>
                                </div>
                            </div>
                        </div>                                      
                        <!-- End -->
                
                    </div>
                </div>                                

            </div>

        </div>

    </div>
</x-app-layout>
