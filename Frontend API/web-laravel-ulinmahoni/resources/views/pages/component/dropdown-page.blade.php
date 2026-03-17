<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Dropdown ✨</h1>
        </div>

        <div class="border-t border-slate-200">

            <!-- Components -->
            <div class="space-y-8 mt-8 mb-80">

                <!-- Classic Dropdown -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Classic Dropdown</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false, selected: 2 }">
                        <button
                            class="btn justify-between min-w-44 bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600"
                            aria-label="Select date range"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open" 
                        >
                            <span class="flex items-center">
                                <span x-text="$refs.options.children[selected].children[1].innerHTML"></span>
                            </span>
                            <svg class="shrink-0 ml-1 fill-current text-slate-400" width="11" height="7" viewBox="0 0 11 7">
                                <path d="M5.4 6.8L0 1.4 1.4 0l4 4 4-4 1.4 1.4z" />
                            </svg>
                        </button>
                        <div
                            class="z-10 absolute top-full left-0 w-full bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                
                        >
                            <div class="font-medium text-sm text-slate-600" x-ref="options">
                                <button
                                    tabindex="0"
                                    class="flex items-center w-full hover:bg-slate-50 py-1 px-3 cursor-pointer"
                                    :class="selected === 0 && 'text-indigo-500'"
                                    @click="selected = 0;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"
                                >
                                    <svg class="shrink-0 mr-2 fill-current text-indigo-400" :class="selected !== 0 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                    <span>Today</span>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center w-full hover:bg-slate-50 py-1 px-3 cursor-pointer"
                                    :class="selected === 1 && 'text-indigo-500'"
                                    @click="selected = 1;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"
                                >
                                    <svg class="shrink-0 mr-2 fill-current text-indigo-400" :class="selected !== 1 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                    <span>Last 7 Days</span>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center w-full hover:bg-slate-50 py-1 px-3 cursor-pointer"
                                    :class="selected === 2 && 'text-indigo-500'"
                                    @click="selected = 2;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"                                        
                                >
                                    <svg class="shrink-0 mr-2 fill-current text-indigo-400" :class="selected !== 2 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                    <span>Last Month</span>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center w-full hover:bg-slate-50 py-1 px-3 cursor-pointer"
                                    :class="selected === 3 && 'text-indigo-500'"
                                    @click="selected = 3;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"                                        
                                >
                                    <svg class="shrink-0 mr-2 fill-current text-indigo-400" :class="selected !== 3 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                    <span>Last 12 Months</span>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center w-full hover:bg-slate-50 py-1 px-3 cursor-pointer"
                                    :class="selected === 4 && 'text-indigo-500'"
                                    @click="selected = 4;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"                                        
                                >
                                    <svg class="shrink-0 mr-2 fill-current text-indigo-400" :class="selected !== 4 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                    <span>All Time</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                </div>

                <!-- Full-width Dropdown -->
                <div class="w-80">
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Full-width Dropdown</h2>
                    <!-- Start -->
                    <div class="relative inline-flex w-full" x-data="{ open: false, selected: 0 }">
                        <button
                            class="btn w-full justify-between min-w-44 bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600"
                            aria-label="Select date range"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open" 
                        >
                            <span class="flex items-center">
                                <span x-text="$refs.options.children[selected].children[0].innerHTML"></span>
                            </span>
                            <svg class="shrink-0 ml-1 fill-current text-slate-400" width="11" height="7" viewBox="0 0 11 7">
                                <path d="M5.4 6.8L0 1.4 1.4 0l4 4 4-4 1.4 1.4z" />
                            </svg>
                        </button>
                        <div
                            class="z-10 absolute top-full left-0 w-full bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-100 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-100"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                
                        >
                            <div class="font-medium text-sm text-slate-600 divide-y divide-slate-200" x-ref="options">
                                <button
                                    tabindex="0"
                                    class="flex items-center justify-between w-full hover:bg-slate-50 py-2 px-3 cursor-pointer"
                                    :class="selected === 0 && 'text-indigo-500'"
                                    @click="selected = 0;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"
                                >
                                    <span>Most Popular</span>
                                    <svg class="shrink-0 ml-2 fill-current text-indigo-400" :class="selected !== 0 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center justify-between w-full hover:bg-slate-50 py-2 px-3 cursor-pointer"
                                    :class="selected === 1 && 'text-indigo-500'"
                                    @click="selected = 1;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"
                                >
                                    <span>Newest</span>
                                    <svg class="shrink-0 ml-2 fill-current text-indigo-400" :class="selected !== 1 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center justify-between w-full hover:bg-slate-50 py-2 px-3 cursor-pointer"
                                    :class="selected === 2 && 'text-indigo-500'"
                                    @click="selected = 2;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"                                        
                                >
                                    <span>Lowest Price</span>
                                    <svg class="shrink-0 ml-2 fill-current text-indigo-400" :class="selected !== 2 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                </button>
                                <button
                                    tabindex="0"
                                    class="flex items-center justify-between w-full hover:bg-slate-50 py-2 px-3 cursor-pointer"
                                    :class="selected === 3 && 'text-indigo-500'"
                                    @click="selected = 3;open = false"
                                    @focus="open = true"
                                    @focusout="open = false"                                        
                                >
                                    <span>Highest Price</span>
                                    <svg class="shrink-0 ml-2 fill-current text-indigo-400" :class="selected !== 3 && 'invisible'" width="12" height="9" viewBox="0 0 12 9">
                                        <path d="M10.28.28L3.989 6.575 1.695 4.28A1 1 0 00.28 5.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28.28z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                </div>
                
                <!-- Filter -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Filter</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false }">
                        <button
                            class="btn bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"
                        >
                            <span class="sr-only">Filter</span><wbr>
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                <path d="M9 15H7a1 1 0 010-2h2a1 1 0 010 2zM11 11H5a1 1 0 010-2h6a1 1 0 010 2zM13 7H3a1 1 0 010-2h10a1 1 0 010 2zM15 3H1a1 1 0 010-2h14a1 1 0 010 2z" />
                            </svg>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 min-w-56 bg-white border border-slate-200 pt-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                
                        >
                            <div class="text-xs font-semibold text-slate-400 uppercase pt-1.5 pb-2 px-4">Filters</div>
                            <ul class="mb-4">
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" checked />
                                        <span class="text-sm font-medium ml-2">Direct VS Indirect</span>
                                    </label>
                                </li>
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" checked />
                                        <span class="text-sm font-medium ml-2">Real Time Value</span>
                                    </label>
                                </li>
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" checked />
                                        <span class="text-sm font-medium ml-2">Top Channels</span>
                                    </label>
                                </li>
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" />
                                        <span class="text-sm font-medium ml-2">Sales VS Refunds</span>
                                    </label>
                                </li>
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" />
                                        <span class="text-sm font-medium ml-2">Last Order</span>
                                    </label>
                                </li>
                                <li class="py-1 px-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" class="form-checkbox" />
                                        <span class="text-sm font-medium ml-2">Total Spent</span>
                                    </label>
                                </li>
                            </ul>
                            <div class="py-2 px-3 border-t border-slate-200 bg-slate-50">
                                <ul class="flex items-center justify-between">
                                    <li>
                                        <button class="btn-xs bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600">Clear</button>
                                    </li>
                                    <li>
                                        <button class="btn-xs bg-indigo-500 hover:bg-indigo-600 text-white" @click="open = false" @focusout="open = false">Apply</button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                </div>
                
                <!-- Profile -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Profile</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false }">
                        <button
                            class="inline-flex justify-center items-center group"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"                        
                        >
                            <img class="w-8 h-8 rounded-full" src="{{ asset('images/user-avatar-32.png') }}" width="32" height="32" alt="User" />
                            <div class="flex items-center truncate">
                                <span class="truncate ml-2 text-sm font-medium group-hover:text-slate-800">Acme Inc.</span>
                                <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-slate-400" viewBox="0 0 12 12">
                                    <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                                </svg>
                            </div>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 min-w-44 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                    
                        >
                            <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-slate-200">
                                <div class="font-medium text-slate-800">Acme Inc.</div>
                                <div class="text-xs text-slate-500 italic">Administrator</div>
                            </div>
                            <ul>
                                <li>
                                    <a class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3" href="{{ route('settings') }}" @click="open = false" @focus="open = true" @focusout="open = false">Settings</a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3" href="{{ route('login') }}" @click="open = false" @focus="open = true" @focusout="open = false">Sign Out</a>
                                </li>
                            </ul>                
                        </div>
                    </div>
                    <!-- End -->
                </div>
                
                <!-- Switch Account -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Switch Account</h2>
                    <!-- Start -->
                    <div class="relative" x-data="{ open: false }">
                        <button
                            class="grow flex items-center truncate"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"
                        >
                            <img class="w-8 h-8 rounded-full mr-2" src="{{ asset('images/user-avatar-32.png') }}" width="32" height="32" alt="Group 01" />
                            <div class="truncate">
                                <span class="text-sm font-medium">Acme Inc.</span>
                            </div>
                            <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-slate-400" viewBox="0 0 12 12">
                                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
                            </svg>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 min-w-60 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                
                        >
                            <ul>
                                <li>
                                    <a class="font-medium text-sm text-slate-600 hover:text-slate-800 block py-1.5 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <div class="flex items-center justify-between">
                                            <div class="grow flex items-center truncate">
                                                <img class="w-7 h-7 rounded-full mr-2" src="{{ asset('images/channel-01.png') }}" width="28" height="28" alt="Channel 01" />
                                                <div class="truncate">Acme Inc.</div>
                                            </div>
                                            <svg class="w-3 h-3 shrink-0 fill-current text-indigo-400 ml-1" viewBox="0 0 12 12">
                                                <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z" />
                                            </svg>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-slate-600 hover:text-slate-800 block py-1.5 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <div class="flex items-center justify-between">
                                            <div class="grow flex items-center truncate">
                                                <img class="w-7 h-7 rounded-full mr-2" src="{{ asset('images/channel-02.png') }}" width="28" height="28" alt="Channel 02" />
                                                <div class="truncate">Acme Limited</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-slate-600 hover:text-slate-800 block py-1.5 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <div class="flex items-center justify-between">
                                            <div class="grow flex items-center truncate">
                                                <img class="w-7 h-7 rounded-full mr-2" src="{{ asset('images/channel-03.png') }}" width="28" height="28" alt="Channel 03" />
                                                <div class="truncate">Acme Srl</div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End -->
                </div>
                
                <!-- Notification -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Notification</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false }">
                        <button
                            class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 transition duration-150 rounded-full"
                            :class="{ 'bg-slate-200': open }"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"                        
                        >
                            <span class="sr-only">Notifications</span>
                            <svg class="w-4 h-4" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path class="fill-current text-slate-500" d="M6.5 0C2.91 0 0 2.462 0 5.5c0 1.075.37 2.074 1 2.922V12l2.699-1.542A7.454 7.454 0 006.5 11c3.59 0 6.5-2.462 6.5-5.5S10.09 0 6.5 0z" />
                                <path class="fill-current text-slate-400" d="M16 9.5c0-.987-.429-1.897-1.147-2.639C14.124 10.348 10.66 13 6.5 13c-.103 0-.202-.018-.305-.021C7.231 13.617 8.556 14 10 14c.449 0 .886-.04 1.307-.11L15 16v-4h-.012C15.627 11.285 16 10.425 16 9.5z" />
                            </svg>
                            <div class="absolute top-0 right-0 w-2.5 h-2.5 bg-rose-500 border-2 border-white rounded-full"></div>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 -mr-48 sm:mr-0 min-w-80 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                    
                        >
                            <div class="text-xs font-semibold text-slate-400 uppercase pt-1.5 pb-2 px-4">Notifications</div>
                            <ul>
                                <li class="border-b border-slate-200 last:border-0">
                                    <a class="block py-2 px-4 hover:bg-slate-50" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <span class="block text-sm mb-2">📣 <span class="font-medium text-slate-800">Edit your information in a swipe</span> Sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.</span>
                                        <span class="block text-xs font-medium text-slate-400">Feb 12, 2021</span>
                                    </a>
                                </li>
                                <li class="border-b border-slate-200 last:border-0">
                                    <a class="block py-2 px-4 hover:bg-slate-50" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <span class="block text-sm mb-2">📣 <span class="font-medium text-slate-800">Edit your information in a swipe</span> Sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.</span>
                                        <span class="block text-xs font-medium text-slate-400">Feb 9, 2021</span>
                                    </a>
                                </li>
                                <li class="border-b border-slate-200 last:border-0">
                                    <a class="block py-2 px-4 hover:bg-slate-50" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <span class="block text-sm mb-2">🚀<span class="font-medium text-slate-800">Say goodbye to paper receipts!</span> Sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim.</span>
                                        <span class="block text-xs font-medium text-slate-400">Jan 24, 2020</span>
                                    </a>
                                </li>
                            </ul>                
                        </div>
                    </div>
                    <!-- End -->
                </div>

                <!-- Help Center -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Help Center</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false }">
                        <button
                            class="w-8 h-8 flex items-center justify-center bg-slate-100 hover:bg-slate-200 transition duration-150 rounded-full"
                            :class="{ 'bg-slate-200': open }"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"                        
                        >
                            <span class="sr-only">Info</span>
                            <svg class="w-4 h-4" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                <path class="fill-current text-slate-500" d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z" />
                            </svg>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 min-w-44 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                    
                        >
                            <div class="text-xs font-semibold text-slate-400 uppercase pt-1.5 pb-2 px-3">Need help?</div>
                            <ul>
                                <li>
                                    <a class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <svg class="w-3 h-3 fill-current text-indigo-300 shrink-0 mr-2" viewBox="0 0 12 12">
                                            <rect y="3" width="12" height="9" rx="1" />
                                            <path d="M2 0h8v2H2z" />
                                        </svg>
                                        <span>Documentation</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <svg class="w-3 h-3 fill-current text-indigo-300 shrink-0 mr-2" viewBox="0 0 12 12">
                                            <path d="M10.5 0h-9A1.5 1.5 0 000 1.5v9A1.5 1.5 0 001.5 12h9a1.5 1.5 0 001.5-1.5v-9A1.5 1.5 0 0010.5 0zM10 7L8.207 5.207l-3 3-1.414-1.414 3-3L5 2h5v5z" />
                                        </svg>
                                        <span>Support Site</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-indigo-500 hover:text-indigo-600 flex items-center py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">
                                        <svg class="w-3 h-3 fill-current text-indigo-300 shrink-0 mr-2" viewBox="0 0 12 12">
                                            <path d="M11.854.146a.5.5 0 00-.525-.116l-11 4a.5.5 0 00-.015.934l4.8 1.921 1.921 4.8A.5.5 0 007.5 12h.008a.5.5 0 00.462-.329l4-11a.5.5 0 00-.116-.525z" />
                                        </svg>
                                        <span>Contact us</span>
                                    </a>
                                </li>
                            </ul>                
                        </div>
                    </div>
                    <!-- End -->
                </div>

                <!-- Quick Selection -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Quick Selection</h2>
                    <!-- Start -->
                    <div class="relative inline-flex" x-data="{ open: false }">
                        <button
                            class="text-slate-400 hover:text-slate-500 rounded-full"
                            :class="{ 'bg-slate-100 text-slate-500': open }"
                            aria-haspopup="true"
                            @click.prevent="open = !open"
                            :aria-expanded="open"
                        >
                            <span class="sr-only">Menu</span>
                            <svg class="w-8 h-8 fill-current" viewBox="0 0 32 32">
                                <circle cx="16" cy="16" r="2" />
                                <circle cx="10" cy="16" r="2" />
                                <circle cx="22" cy="16" r="2" />
                            </svg>
                        </button>
                        <div
                            class="origin-top-right z-10 absolute top-full left-0 min-w-36 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
                            @click.outside="open = false"
                            @keydown.escape.window="open = false"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200 transform"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-out duration-200"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-cloak                
                        >
                            <ul>
                                <li>
                                    <a class="font-medium text-sm text-slate-600 hover:text-slate-800 flex py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">Option 1</a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-slate-600 hover:text-slate-800 flex py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">Option 2</a>
                                </li>
                                <li>
                                    <a class="font-medium text-sm text-rose-500 hover:text-rose-600 flex py-1 px-3" href="#0" @click="open = false" @focus="open = true" @focusout="open = false">Remove</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- End -->
                </div>
                
                <!-- Datepicker (built with flatpickr) -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Datepicker (built with <a class="underline hover:no-underline" href="https://github.com/flatpickr/flatpickr" target="_blank">flatpickr</a>)</h2>
                    <!-- Start -->
                    <div class="relative">
                        <input class="datepicker form-input pl-9 text-slate-500 hover:text-slate-600 font-medium focus:border-slate-300 w-60" placeholder="Select dates" />
                        <div class="absolute inset-0 right-auto flex items-center pointer-events-none">
                            <svg class="w-4 h-4 fill-current text-slate-500 ml-3" viewBox="0 0 16 16">
                                <path d="M15 2h-2V0h-2v2H9V0H7v2H5V0H3v2H1a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V3a1 1 0 00-1-1zm-1 12H2V6h12v8z" />
                            </svg>
                        </div>
                    </div>
                    <!-- End -->
                </div>                              

            </div>

        </div>

    </div>
</x-app-layout>
