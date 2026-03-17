<div class="col-span-full sm:col-span-6 xl:col-span-4 bg-white shadow-lg rounded-sm border border-slate-200">
    <div class="flex flex-col h-full">
        <!-- Card top -->
        <div class="grow p-5">
            <div class="flex justify-between items-start">
                <!-- Image + name -->
                <header>                
                    <div class="flex mb-2">
                        <a class="relative inline-flex items-start mr-5" href="#0">
                            <div class="absolute top-0 right-0 -mr-2 bg-white rounded-full shadow" aria-hidden="true">
                                <svg class="w-8 h-8 fill-current text-amber-500" viewBox="0 0 32 32">
                                    <path d="M21 14.077a.75.75 0 01-.75-.75 1.5 1.5 0 00-1.5-1.5.75.75 0 110-1.5 1.5 1.5 0 001.5-1.5.75.75 0 111.5 0 1.5 1.5 0 001.5 1.5.75.75 0 010 1.5 1.5 1.5 0 00-1.5 1.5.75.75 0 01-.75.75zM14 24.077a1 1 0 01-1-1 4 4 0 00-4-4 1 1 0 110-2 4 4 0 004-4 1 1 0 012 0 4 4 0 004 4 1 1 0 010 2 4 4 0 00-4 4 1 1 0 01-1 1z" />
                                </svg>
                            </div>
                            <img class="rounded-full" src="{{ asset('images/' . $member->image) }}" width="64" height="64" alt="{{ $member->name }}" />
                        </a>
                        <div class="mt-1 pr-1">
                            <a class="inline-flex text-slate-800 hover:text-slate-900" href="#0">
                                <h2 class="text-xl leading-snug justify-center font-semibold">{{ $member->name }}</h2>
                            </a>
                            <div class="flex items-center"><span class="text-sm font-medium text-slate-400 -mt-0.5 mr-1">-&gt;</span> <span>{{ $member->location }}</span></div>
                        </div>
                    </div>
                </header>
                <!-- Menu button -->
                <div class="relative inline-flex shrink-0" x-data="{ open: false }">
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
                        class="origin-top-right z-10 absolute top-full right-0 min-w-36 bg-white border border-slate-200 py-1.5 rounded shadow-lg overflow-hidden mt-1"                
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
            </div>
            <!-- Bio -->
            <div class="mt-2">
                <div class="text-sm">{{ $member->content }}</div>
            </div>
        </div>
        <!-- Card footer -->
        <div class="border-t border-slate-200">
            <div class="flex divide-x divide-slate-200r">
                <a class="block flex-1 text-center text-sm text-indigo-500 hover:text-indigo-600 font-medium px-3 py-4" href="{{ route('messages') }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 fill-current shrink-0 mr-2" viewBox="0 0 16 16">
                            <path d="M8 0C3.6 0 0 3.1 0 7s3.6 7 8 7h.6l5.4 2v-4.4c1.2-1.2 2-2.8 2-4.6 0-3.9-3.6-7-8-7zm4 10.8v2.3L8.9 12H8c-3.3 0-6-2.2-6-5s2.7-5 6-5 6 2.2 6 5c0 2.2-2 3.8-2 3.8z" />
                        </svg>
                        <span>Send Email</span>
                    </div>
                </a>
                <a class="block flex-1 text-center text-sm text-slate-600 hover:text-slate-800 font-medium px-3 py-4 group" href="{{ route('settings') }}">
                    <div class="flex items-center justify-center">
                        <svg class="w-4 h-4 fill-current text-slate-400 group-hover:text-slate-500 shrink-0 mr-2" viewBox="0 0 16 16">
                            <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                        </svg>
                        <span>Edit Profile</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>