<x-app-layout background="bg-white">
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Input Form ✨</h1>
        </div>

        <div class="border-t border-slate-200">

            <!-- Components -->
            <div class="space-y-8 mt-8">

                <!-- Input Types -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Input Types</h2>
                    <div class="grid gap-5 md:grid-cols-3">
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="default">Default</label>
                                <input id="default" class="form-input w-full" type="text" />
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-sm font-medium mb-1" for="tooltip">W/ Tooltip</label>
                                    <div
                                        class="relative ml-2"
                                        x-data="{ open: false }"
                                        @mouseenter="open = true"
                                        @mouseleave="open = false"
                                    >
                                        <button
                                            class="block"
                                            aria-haspopup="true"
                                            :aria-expanded="open"
                                            @focus="open = true"
                                            @focusout="open = false"
                                            @click.prevent            
                                        >
                                            <svg class="w-4 h-4 fill-current text-slate-400" viewBox="0 0 16 16">
                                                <path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm0 12c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1zm1-3H7V4h2v5z"  />
                                            </svg>
                                        </button>
                                        <div class="z-10 absolute bottom-full left-1/2 -translate-x-1/2">
                                            <div
                                                class="min-w-60 bg-slate-800 text-slate-200 px-2 py-1 rounded overflow-hidden mb-2"
                                                x-show="open"
                                                x-transition:enter="transition ease-out duration-200 transform"
                                                x-transition:enter-start="opacity-0 translate-y-2"
                                                x-transition:enter-end="opacity-100 translate-y-0"
                                                x-transition:leave="transition ease-out duration-200"
                                                x-transition:leave-start="opacity-100"
                                                x-transition:leave-end="opacity-0"
                                                x-cloak
                                            >
                                                <div class="text-sm">Excepteur sint occaecat cupidata non proident, sunt in.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input id="tooltip" class="form-input w-full" type="text" />
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="mandatory">Mandatory <span class="text-rose-500">*</span></label>
                                <input id="mandatory" class="form-input w-full" type="text" required />
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="prefix">W/ Prefix</label>
                                <div class="relative">
                                    <input id="prefix" class="form-input w-full pl-12" type="text" />
                                    <div class="absolute inset-0 right-auto flex items-center pointer-events-none">
                                        <span class="text-sm text-slate-400 font-medium px-3">USD</span>
                                    </div>
                                </div>
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="suffix">W/ Suffix</label>
                                <div class="relative">
                                    <input id="suffix" class="form-input w-full pr-8" type="text" />
                                    <div class="absolute inset-0 left-auto flex items-center pointer-events-none">
                                        <span class="text-sm text-slate-400 font-medium px-3">%</span>
                                    </div>
                                </div>
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="placeholder">W/ Placeholder</label>
                                <input id="placeholder" class="form-input w-full" type="text" placeholder="Something cool..." />
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <form>
                                <label class="block text-sm font-medium mb-1" for="icon">W/ Icon</label>
                                <div class="relative">
                                    <input id="icon" class="form-input w-full pl-9" type="text" />
                                    <div class="absolute inset-0 right-auto flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 fill-current text-slate-400 shrink-0 ml-3 mr-2" viewBox="0 0 16 16">
                                            <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                                        </svg>
                                    </div>
                                </div>
                            </form>
                            <!-- End -->
                        </div>

                        <div>
                            <!-- Start -->
                            <div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="supporting-text">W/ Supporting Text</label>
                                    <input id="supporting-text" class="form-input w-full" type="text" />
                                </div>
                                <div class="text-xs mt-1">Supporting text goes here!</div>
                            </div>
                            <!-- End -->
                        </div>
                        
                        <div>
                            <!-- Start -->
                            <form>
                                <label class="block text-sm font-medium mb-1" for="form-search">Search</label>
                                <div class="relative">
                                    <input id="form-search" class="form-input w-full pl-9" type="search" />
                                    <button class="absolute inset-0 right-auto group" type="submit" aria-label="Search">
                                        <svg class="w-4 h-4 shrink-0 fill-current text-slate-400 group-hover:text-slate-500 ml-3 mr-2" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7 14c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zM7 2C4.243 2 2 4.243 2 7s2.243 5 5 5 5-2.243 5-5-2.243-5-5-5z" />
                                            <path d="M15.707 14.293L13.314 11.9a8.019 8.019 0 01-1.414 1.414l2.393 2.393a.997.997 0 001.414 0 .999.999 0 000-1.414z" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                            <!-- End -->
                        </div>
                    </div>
                </div>

                <!-- Input Sizes -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Input Sizes</h2>
                    <div class="grid gap-5 md:grid-cols-3">
                        
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="small">Small</label>
                                <input id="small" class="form-input w-full px-2 py-1" type="text" />
                            </div>
                            <!-- End -->
                        </div>

                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="normal">Default</label>
                                <input id="normal" class="form-input w-full" type="text" />
                            </div>
                            <!-- End -->
                        </div>

                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="large">Large</label>
                                <input id="large" class="form-input w-full px-4 py-3" type="text" />
                            </div>
                            <!-- End -->
                        </div>

                    </div>
                </div>
                
                <!-- Input States -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Input States</h2>
                    <div class="grid gap-5 md:grid-cols-3">
                
                        <div>
                            <!-- Start -->
                            <div>
                                <label class="block text-sm font-medium mb-1" for="disabled">Disabled</label>
                                <input id="disabled" class="form-input w-full disabled:border-slate-200 disabled:bg-slate-100 disabled:text-slate-400 disabled:cursor-not-allowed" type="text" placeholder="Something cool..." disabled />
                            </div>
                            <!-- End -->
                        </div>

                        <div>
                            <!-- Start -->
                            <div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="error">Error <span class="text-rose-500">*</span></label>
                                    <input id="error" class="form-input w-full border-rose-300" type="text" />
                                </div>
                                <div class="text-xs mt-1 text-rose-500">This field is required!</div>
                            </div>
                            <!-- End -->
                        </div>

                        <div>
                            <!-- Start -->
                            <div>
                                <div>
                                    <label class="block text-sm font-medium mb-1" for="success">Success <span class="text-rose-500">*</span></label>
                                    <input id="success" class="form-input w-full border-emerald-300" type="text" />
                                </div>
                                <div class="text-xs mt-1 text-emerald-500">Sounds good!</div>
                            </div>
                            <!-- End -->
                        </div>
                
                    </div>
                </div>

                <!-- Select -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Select</h2>
                    <label class="block text-sm font-medium mb-1" for="country">Country</label>
                    <select id="country" class="form-select">
                        <option>Italy</option>
                        <option>USA</option>
                        <option>United Kingdom</option>
                    </select>
                </div>

                <!-- Checkbox -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Checkbox</h2>
                    <div class="flex flex-wrap items-center -m-3">
                
                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox" />
                                <span class="text-sm ml-2">Active</span>
                            </label>
                            <!-- End -->
                        </div>

                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox" checked />
                                <span class="text-sm ml-2">Selected</span>
                            </label>
                            <!-- End -->
                        </div>

                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="checkbox" class="form-checkbox disabled:bg-slate-50" disabled />
                                <span class="text-sm ml-2">Disabled</span>
                            </label>
                            <!-- End -->
                        </div>
                
                    </div>
                </div>

                <!-- Radio -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Radio</h2>
                    <div class="flex flex-wrap items-center -m-3">
                
                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="radio" name="radio-buttons" class="form-radio" />
                                <span class="text-sm ml-2">Active</span>
                            </label>
                            <!-- End -->
                        </div>
                
                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="radio" name="radio-buttons" class="form-radio" checked />
                                <span class="text-sm ml-2">Selected</span>
                            </label>
                            <!-- End -->
                        </div>
                
                        <div class="m-3">
                            <!-- Start -->
                            <label class="flex items-center">
                                <input type="radio" name="radio-buttons" class="form-radio disabled:bg-slate-50" disabled />
                                <span class="text-sm ml-2">Disabled</span>
                            </label>
                            <!-- End -->
                        </div>
                
                    </div>
                </div>

                <!-- Toggle Switch -->
                <div>
                    <h2 class="text-2xl text-slate-800 font-bold mb-6">Toggle Switch</h2>
                    <div class="flex flex-wrap items-center -m-3">
                
                        <div class="m-3 w-24">
                            <!-- Start -->
                            <div class="flex items-center" x-data="{ checked: true }">
                                <div class="form-switch">
                                    <input type="checkbox" id="switch-1" class="sr-only" x-model="checked" />
                                    <label class="bg-slate-400" for="switch-1">
                                        <span class="bg-white shadow-sm" aria-hidden="true"></span>
                                        <span class="sr-only">Switch label</span>
                                    </label>
                                </div>
                                <div class="text-sm text-slate-400 italic ml-2" x-text="checked ? 'On' : 'Off'"></div>
                            </div>
                            <!-- End -->
                        </div>

                        <div class="m-3 w-24">
                            <!-- Start -->
                            <div class="flex items-center" x-data="{ checked: false }">
                                <div class="form-switch">
                                    <input type="checkbox" id="switch-2" class="sr-only" x-model="checked" />
                                    <label class="bg-slate-400" for="switch-2">
                                        <span class="bg-white shadow-sm" aria-hidden="true"></span>
                                        <span class="sr-only">Switch label</span>
                                    </label>
                                </div>
                                <div class="text-sm text-slate-400 italic ml-2" x-text="checked ? 'On' : 'Off'"></div>
                            </div>
                            <!-- End -->
                        </div>

                        <div class="m-3 w-32">
                            <!-- Start -->
                            <div class="flex items-center" x-data="{ checked: false }">
                                <div class="form-switch">
                                    <input type="checkbox" id="switch-3" class="sr-only" x-model="checked" disabled />
                                    <label class="bg-slate-400" for="switch-3">
                                        <span class="bg-white shadow-sm" aria-hidden="true"></span>
                                        <span class="sr-only">Switch label</span>
                                    </label>
                                </div>
                                <div class="text-sm text-slate-400 italic ml-2">Disabled</div>
                            </div>
                            <!-- End -->
                        </div>
                
                    </div>
                </div>

            </div>

        </div>

    </div>
</x-app-layout>
