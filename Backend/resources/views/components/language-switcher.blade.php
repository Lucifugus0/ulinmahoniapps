<div class="relative inline-flex" x-data="{ open: false }">
    <button
        class="inline-flex justify-center items-center group rounded-lg px-2 py-1.5 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"
    >
        @if(app()->getLocale() === 'id')
            <span class="fi fi-id fis rounded-sm" style="font-size: 1.1rem; line-height: 1;"></span>
            <span class="ml-1.5 text-sm font-medium text-gray-600 dark:text-gray-100 group-hover:text-gray-800 dark:group-hover:text-white">ID</span>
        @else
            <span class="fi fi-gb fis rounded-sm" style="font-size: 1.1rem; line-height: 1;"></span>
            <span class="ml-1.5 text-sm font-medium text-gray-600 dark:text-gray-100 group-hover:text-gray-800 dark:group-hover:text-white">EN</span>
        @endif
        <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400 dark:text-gray-500" viewBox="0 0 12 12">
            <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
        </svg>
    </button>

    <div
        class="origin-top-left z-10 absolute top-full left-0 min-w-36 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700/60 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1"
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
                <form method="POST" action="{{ auth()->check() ? route('user.locale.update') : route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="id">
                    <button type="submit"
                        class="w-full flex items-center py-1.5 px-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ app()->getLocale() === 'id' ? 'font-semibold text-violet-500' : 'text-gray-600 dark:text-gray-300' }}"
                        @click="open = false"
                    >
                        <span class="fi fi-id fis rounded-sm mr-2" style="font-size: 1rem; line-height: 1;"></span>
                        Indonesia
                    </button>
                </form>
            </li>
            <li>
                <form method="POST" action="{{ auth()->check() ? route('user.locale.update') : route('locale.update') }}">
                    @csrf
                    <input type="hidden" name="locale" value="en">
                    <button type="submit"
                        class="w-full flex items-center py-1.5 px-3 text-sm hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ app()->getLocale() === 'en' ? 'font-semibold text-violet-500' : 'text-gray-600 dark:text-gray-300' }}"
                        @click="open = false"
                    >
                        <span class="fi fi-gb fis rounded-sm mr-2" style="font-size: 1rem; line-height: 1;"></span>
                        English
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>
