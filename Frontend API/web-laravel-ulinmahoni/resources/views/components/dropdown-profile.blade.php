@props([
    'align' => 'right'
])

<div class="relative inline-flex" x-data="{ open: false }">
    <button
        class="inline-flex justify-center items-center group"
        aria-haspopup="true"
        @click.prevent="open = !open"
        :aria-expanded="open"                        
    >
        <img class="w-8 h-8 rounded-full border-2 border-gray-200" src="{{ Auth::user()->profile_photo_url }}" width="32" height="32" alt="{{ Auth::user()->username }}" />
        <div class="flex items-center truncate">
            <span class="truncate ml-2 text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ Auth::user()->name }}</span>
            <svg class="w-3 h-3 shrink-0 ml-1 fill-current text-gray-400" viewBox="0 0 12 12">
                <path d="M5.9 11.4L.5 6l1.4-1.4 4 4 4-4L11.3 6z" />
            </svg>
        </div>
    </button>

    <div
        class="origin-top-right z-10 absolute top-full min-w-56 bg-white border border-gray-200 py-1.5 rounded-lg shadow-lg overflow-hidden mt-1 {{$align === 'right' ? 'right-0' : 'left-0'}}"                
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
        <div class="pt-0.5 pb-2 px-3 mb-1 border-b border-gray-200">
            <div class="font-medium text-gray-800">{{ Auth::user()->name }}</div>
            <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
        </div>
        <ul>
            <li>
                <a class="flex items-center py-2 px-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50" 
                   href="{{ route('profile.show') }}" 
                   @click="open = false" 
                   @focus="open = true" 
                   @focusout="open = false">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile Settings
                </a>
            </li>
            <li>
                <a class="flex items-center py-2 px-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50" 
                   href="/bookings" 
                   @click="open = false" 
                   @focus="open = true" 
                   @focusout="open = false">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    My Bookings
                </a>
            </li>
            <li>
                <a class="flex items-center py-2 px-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50" 
                   href="/wishlist" 
                   @click="open = false" 
                   @focus="open = true" 
                   @focusout="open = false">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                    Wishlist
                </a>
            </li>
            @if(Auth::user()->is_admin)
            <li>
                <a class="flex items-center py-2 px-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50" 
                   href="/admin/dashboard" 
                   @click="open = false" 
                   @focus="open = true" 
                   @focusout="open = false">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                    </svg>
                    Admin Dashboard
                </a>
            </li>
            @endif
            <li class="border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <a class="flex items-center py-2 px-3 text-sm text-red-600 hover:text-red-700 hover:bg-gray-50"
                        href="{{ route('logout') }}"
                        @click.prevent="$root.submit();"
                        @focus="open = true"
                        @focusout="open = false"
                    >
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        {{ __('Sign Out') }}
                    </a>
                </form>                                
            </li>
        </ul>                
    </div>
</div>