<!-- Column 1 -->
<div class="col-span-full sm:col-span-6 xl:col-span-3">

    <!-- Column header -->
    <header>
        <div class="flex items-center justify-between mb-2">
            <h2 class="grow font-semibold text-slate-800 truncate">To Do’s 🖋️</h2>
            <div x-data="{ modalOpen: false }">
                <button class="shrink-0 text-indigo-500 hover:text-indigo-600 ml-2" @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </button>
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
                    x-cloak></div>
                <!-- Modal dialog -->
                <div id="feedback-modal"
                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                    role="dialog" aria-modal="true" x-show="modalOpen"
                    x-transition:enter="transition ease-in-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in-out duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                    <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full"
                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                        <!-- Modal header -->
                        <div class="px-5 py-3 border-b border-slate-200">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-slate-800">Add To Do's</div>
                                <button class="text-slate-400 hover:text-slate-500" @click="modalOpen = false">
                                    <div class="sr-only">Close</div>
                                    <svg class="w-4 h-4 fill-current">
                                        <path
                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form action="" method="post">
                            @csrf
                            <!-- Modal content -->
                            <div class="px-5 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-slate-800 mb-3"></div>
                                </div>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="name">Activity Name<span
                                                class="text-rose-500">*</span></label>
                                        <input id="name" name="name" class="form-input w-full px-2 py-1" type="text" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="name">Type</label>
                                        <select id="type" name="type" class="type form-select">
                                            <option selected disabled hidden>Select Here</option>
                                            <option value="personal">Personal</option>
                                            <option value="group">Group</option>
                                        </select>
                                    </div>
                                    <div id="div-invitation" class="hidden">
                                        <label class="block text-sm font-medium mb-1" for="invite">Invite</label>
                                        <select disabled id="select-invitation" class="form-select" name="users[]"
                                            multiple="multiple" style="width: 400px">
                                            <option value=""></option>
                                        </select>
                                    </div>
                                    <div>
                                        <input id="invite_id" name="invite_id"
                                            class="invite_id form-input w-full pl-11" type="text" / hidden>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="name">Calendar
                                            Tag</label>
                                        <select id="calendarTag" name="calenderTag" class=" color form-select">
                                            <option selected disabled hidden>Select Here</option>
                                            <option value="">
                                            </option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="name">Start Date<span
                                                class="text-rose-500">*</span></label>
                                        <input id="start_date" name="start_date"
                                            class=" start_date form-input w-full px-2 py-1" type="datetime-local"
                                            required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="name">End Date<span
                                                class="text-rose-500">*</span></label>
                                        <input id="end_date" name="end_date" class="form-input w-full px-2 py-1"
                                            type="datetime-local" required />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1" for="feedback">Notes</label>
                                        <textarea id="notes" name="notes" class="form-textarea w-full px-2 py-1" rows="4"
                                            required></textarea>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="px-5 py-4 border-t border-slate-200">
                                <div class="flex flex-wrap justify-end space-x-2">
                                    <button type="button"
                                        class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                        @click="modalOpen = false">Cancel</button>
                                    <button type="submit"
                                        class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid gap-2">

             <!-- Card 1 -->
            <div class="bg-white shadow-lg rounded-sm border border-slate-200 p-4">
                <!-- Body -->
                <div class="mb-3">
                    <!-- Title -->
                    <h2 class="font-semibold text-slate-800 mb-1">Product Update - Q4 2021</h2>
                    <!-- Content -->
                    <div>
                        <div class="text-sm">Dedicated form for a category of users that will perform actions.</div>
                        <!-- List -->
                        <ul class="mt-3">
                            <li class="flex items-center border-t border-slate-200 py-2">
                                <svg class="w-3 h-3 shrink-0 fill-current text-emerald-500 mr-2" viewBox="0 0 12 12">
                                    <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z" />
                                </svg>
                                <div class="text-sm text-slate-400 line-through">Implement new designs</div>
                            </li>
                            <li class="flex items-center border-t border-slate-200 py-2">
                                <svg class="w-3 h-3 shrink-0 fill-current text-slate-400 mr-2" viewBox="0 0 12 12">
                                    <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z" />
                                </svg>
                                <div class="text-sm">Usability testing</div>
                            </li>
                            <li class="flex items-center border-t border-slate-200 py-2">
                                <svg class="w-3 h-3 shrink-0 fill-current text-slate-400 mr-2" viewBox="0 0 12 12">
                                    <path d="M10.28 1.28L3.989 7.575 1.695 5.28A1 1 0 00.28 6.695l3 3a1 1 0 001.414 0l7-7A1 1 0 0010.28 1.28z" />
                                </svg>
                                <div class="text-sm">Design navigation changes</div>
                            </li>
                        </ul>
                        <img class="w-full mt-3" src="{{ asset('images/task-image-01.jpg') }}"" width="259" height="142" alt="Task 01" />
                    </div>
                </div>
                <!-- Footer -->
                <div class="flex items-center justify-between">
                    <!-- Left side -->
                    <div class="flex shrink-0 -space-x-3 -ml-px">
                        <a class="block" href="#0">
                            <img class="rounded-full border-2 border-white box-content" src="{{ asset('images/user-28-05.jpg') }}"" width="28" height="28" alt="User 05" />
                        </a>
                        <a class="block" href="#0">
                            <img class="rounded-full border-2 border-white box-content" src="{{ asset('images/user-28-02.jpg') }}"" width="28" height="28" alt="User 02" />
                        </a>
                    </div>
                    <!-- Right side -->
                    <div class="flex items-center">
                        <!-- Date -->
                        <div class="flex items-center text-amber-500 ml-3">
                            <svg class="w-4 h-4 shrink-0 fill-current mr-1.5" viewBox="0 0 16 16">
                                <path d="M15 2h-2V0h-2v2H9V0H7v2H5V0H3v2H1a1 1 0 00-1 1v12a1 1 0 001 1h14a1 1 0 001-1V3a1 1 0 00-1-1zm-1 12H2V6h12v8z" />
                            </svg>
                            <div class="text-sm text-amber-600">Mar 27</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </header>
</div>

<!-- Column 2 -->
<div class="col-span-full sm:col-span-6 xl:col-span-3">

    <!-- Column header -->
    <header>
        <div class="flex items-center justify-between mb-2">
            <h2 class="grow font-semibold text-slate-800 truncate">In Progress ✌️</h2>
            <div x-data="{ modalOpen: false }">
                <button class="shrink-0 text-indigo-500 hover:text-indigo-600 ml-2" @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </button>
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
                    x-cloak></div>
                <!-- Modal dialog -->
                <div id="feedback-modal"
                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                    role="dialog" aria-modal="true" x-show="modalOpen"
                    x-transition:enter="transition ease-in-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in-out duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                    <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full"
                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                        <!-- Modal header -->
                        <div class="px-5 py-3 border-b border-slate-200">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-slate-800">Add Progress</div>
                                <button class="text-slate-400 hover:text-slate-500" @click="modalOpen = false">
                                    <div class="sr-only">Close</div>
                                    <svg class="w-4 h-4 fill-current">
                                        <path
                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form action="" method="post">
                            @csrf
                            <!-- Modal content -->
                            <div class="px-5 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-slate-800 mb-3"></div>
                                </div>
                                <div class="space-y-3">
                                    
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="px-5 py-4 border-t border-slate-200">
                                <div class="flex flex-wrap justify-end space-x-2">
                                    <button type="button"
                                        class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                        @click="modalOpen = false">Cancel</button>
                                    <button type="submit"
                                        class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid gap-2">

        </div>
    </header>
</div>

<!-- Column 3 -->
<div class="col-span-full sm:col-span-6 xl:col-span-3">

    <!-- Column header -->
    <header>
        <div class="flex items-center justify-between mb-2">
            <h2 class="grow font-semibold text-slate-800 truncate">Completed 🎉</h2>
            <div x-data="{ modalOpen: false }">
                <button class="shrink-0 text-indigo-500 hover:text-indigo-600 ml-2" @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </button>
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
                    x-cloak></div>
                <!-- Modal dialog -->
                <div id="feedback-modal"
                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                    role="dialog" aria-modal="true" x-show="modalOpen"
                    x-transition:enter="transition ease-in-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in-out duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                    <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full"
                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                        <!-- Modal header -->
                        <div class="px-5 py-3 border-b border-slate-200">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-slate-800">Add Complete Tasks</div>
                                <button class="text-slate-400 hover:text-slate-500" @click="modalOpen = false">
                                    <div class="sr-only">Close</div>
                                    <svg class="w-4 h-4 fill-current">
                                        <path
                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form action="" method="post">
                            @csrf
                            <!-- Modal content -->
                            <div class="px-5 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-slate-800 mb-3"></div>
                                </div>
                                <div class="space-y-3">
                                    
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="px-5 py-4 border-t border-slate-200">
                                <div class="flex flex-wrap justify-end space-x-2">
                                    <button type="button"
                                        class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                        @click="modalOpen = false">Cancel</button>
                                    <button type="submit"
                                        class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid gap-2">

        </div>
    </header>
</div>

<!-- Column 4 -->
<div class="col-span-full sm:col-span-6 xl:col-span-3">

    <!-- Column header -->
    <header>
        <div class="flex items-center justify-between mb-2">
            <h2 class="grow font-semibold text-slate-800 truncate">Notes 📒</h2>
            <div x-data="{ modalOpen: false }">
                <button class="shrink-0 text-indigo-500 hover:text-indigo-600 ml-2" @click.prevent="modalOpen = true" aria-controls="feedback-modal">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                        <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                    </svg>
                </button>
                <!-- Modal backdrop -->
                <div class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" x-show="modalOpen"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-out duration-100"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" aria-hidden="true"
                    x-cloak></div>
                <!-- Modal dialog -->
                <div id="feedback-modal"
                    class="fixed inset-0 z-50 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6"
                    role="dialog" aria-modal="true" x-show="modalOpen"
                    x-transition:enter="transition ease-in-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-4"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in-out duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-4" x-cloak>
                    <div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full"
                        @click.outside="modalOpen = false" @keydown.escape.window="modalOpen = false">
                        <!-- Modal header -->
                        <div class="px-5 py-3 border-b border-slate-200">
                            <div class="flex justify-between items-center">
                                <div class="font-semibold text-slate-800">Add Notes</div>
                                <button class="text-slate-400 hover:text-slate-500" @click="modalOpen = false">
                                    <div class="sr-only">Close</div>
                                    <svg class="w-4 h-4 fill-current">
                                        <path
                                            d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <form action="" method="post">
                            @csrf
                            <!-- Modal content -->
                            <div class="px-5 py-4">
                                <div class="text-sm">
                                    <div class="font-medium text-slate-800 mb-3"></div>
                                </div>
                                <div class="space-y-3">
                                    
                                </div>
                            </div>
                            <!-- Modal footer -->
                            <div class="px-5 py-4 border-t border-slate-200">
                                <div class="flex flex-wrap justify-end space-x-2">
                                    <button type="button"
                                        class="btn-sm border-slate-200 hover:border-slate-300 text-slate-600"
                                        @click="modalOpen = false">Cancel</button>
                                    <button type="submit"
                                        class="btn-sm bg-indigo-500 hover:bg-indigo-600 text-white">Create</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards -->
        <div class="grid gap-2">

        </div>
    </header>
</div>

