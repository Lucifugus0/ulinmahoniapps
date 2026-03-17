<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="{ activeTab: '{{ session('active_tab', 'account') }}' }">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1
                    class="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                    {{ __('ui.account_settings') }}
                </h1>
            </div>
        </div>

        <div id="containerAccount" class="bg-white shadow-md rounded-lg overflow-hidden mt-8">
            <!-- Header -->
            <div class="flex justify-between items-center px-6 py-4 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">{{ __('ui.user_profile') }}</h3>
                <!-- Tabs -->
                <div class="flex items-center gap-4">
                    <!-- Account -->
                    <button @click="activeTab = 'account'"
                        :class="activeTab === 'account'
                            ?
                            'bg-blue-500 text-white px-3 py-2 rounded-lg' :
                            'text-gray-600 hover:text-blue-500'"
                        class="flex items-center gap-2 text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.5 21a7.5 7.5 0 0113 0" />
                        </svg>
                        {{ __('ui.account') }}
                    </button>

                    @if (auth()->user()->is_admin == 1)
                        <!-- Security -->
                        <button @click="activeTab = 'security'"
                            :class="activeTab === 'security'
                                ?
                                'bg-blue-500 text-white px-3 py-2 rounded-lg' :
                                'text-gray-600 hover:text-blue-500'"
                            class="flex items-center gap-2 text-sm font-medium transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 15v2m-6-6h12a2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a2 2 0 012-2zm10-4V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            {{ __('ui.security') }}
                        </button>
                    @endif

                    <!-- History -->
                    <button @click="activeTab = 'history'"
                        :class="activeTab === 'history'
                            ?
                            'bg-blue-500 text-white px-3 py-2 rounded-lg' :
                            'text-gray-600 hover:text-blue-500'"
                        class="flex items-center gap-2 text-sm font-medium transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('ui.history') }}
                    </button>
                </div>
            </div>
        </div>


        <div class="p-6">
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column: Profile Card (selalu tampil) -->
                <div class="w-full lg:w-1/3 space-y-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 group">
                        <div class="flex flex-col items-center text-center">
                            <!-- Avatar -->
                            <div class="relative mb-4">
                                <div
                                    class="w-28 h-28 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                                    @if ($user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}"
                                            alt="{{ $user->first_name }} {{ $user->last_name }}"
                                            class="w-28 h-28 rounded-full object-cover shadow-lg">
                                    @else
                                        <div
                                            class="w-28 h-28 rounded-full bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-green-500 border-2 border-white shadow-md"
                                    title="Active"></div>
                            </div>

                            <!-- User Info -->
                            <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ $user->first_name }}
                                {{ $user->last_name }}</h2>
                            <p class="text-gray-600 mb-3 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z">
                                    </path>
                                </svg>
                                {{ optional($user->role)->name ?? __('ui.no_role') }}
                            </p>

                            <!-- Status Badge -->
                            <div
                                class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-medium mb-6 shadow-sm {{ $user->status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span
                                    class="w-2.5 h-2.5 rounded-full mr-2 animate-pulse {{ $user->status == 1 ? 'bg-green-500' : 'bg-red-500' }}">
                                </span>
                                {{ $user->status == 1 ? __('ui.active') : __('ui.inactive') }}
                            </div>


                            <!-- Separator -->
                            <div class="w-full border-t border-gray-100 my-4"></div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3 w-full">
                                <div x-data="{ modalOpenDetail: {{ ($errors->hasBag('profile') && $errors->getBag('profile')->any()) ? 'true' : 'false' }} }" class="w-full">
                                    <!-- Button -->
                                    <button
                                        class="w-full px-5 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-600 rounded-xl text-sm font-medium hover:from-indigo-100 hover:to-blue-100 hover:shadow-md transition-all duration-300 flex items-center justify-center group/button"
                                        type="button" @click.prevent="modalOpenDetail = true">
                                        <svg class="w-4 h-4 mr-2 group-hover/button:scale-110 transition-transform"
                                            fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                            </path>
                                        </svg>
                                        {{ __('ui.edit_profile') }}
                                    </button>

                                    <!-- Modal backdrop -->
                                    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 transition-opacity"
                                        x-show="modalOpenDetail" x-transition.opacity aria-hidden="true" x-cloak>
                                    </div>

                                    <!-- Modal dialog -->
                                    <div class="fixed inset-0 z-50 flex items-center justify-center px-4 sm:px-6"
                                        x-show="modalOpenDetail" x-transition x-cloak
                                        @keydown.escape.window="modalOpenDetail = false">
                                        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md animate-modal relative z-50 overflow-hidden"
                                            @click.outside="modalOpenDetail = false">

                                            <!-- Header -->
                                            <div
                                                class="px-7 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                                <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor"
                                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                        </path>
                                                    </svg>
                                                    {{ __('ui.edit_profile') }}
                                                </h2>
                                                <button
                                                    class="text-gray-500 hover:text-gray-700 transition-colors p-1 rounded-full hover:bg-gray-100"
                                                    @click="modalOpenDetail = false">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                        xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd"
                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Body -->
                                            <div class="p-6">
                                                <form id="userForm" method="POST"
                                                    action="{{ route('user.profile.update') }}"
                                                    autocomplete="off">
                                                    @csrf
                                                    @method('PUT')

                                                    <!-- First Name & Last Name -->
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                                                        <div class="group/input">
                                                            <label
                                                                class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                                {{ __('ui.first_name') }} <span class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <input type="text" name="first_name" id="first_name"
                                                                required
                                                                class="w-full px-4 py-3 border rounded-xl text-sm focus:border-indigo-500 focus:ring-3 focus:ring-indigo-100 transition-all duration-200 group-hover/input:border-gray-300 {{ $errors->profile->has('first_name') ? 'border-red-400' : 'border-gray-200' }}"
                                                                placeholder="{{ __('ui.enter_first_name') }}"
                                                                value="{{ old('first_name', $user->first_name) }}">
                                                            @error('first_name', 'profile')
                                                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                        <div class="group/input">
                                                            <label
                                                                class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                                {{ __('ui.last_name') }} <span class="text-red-500 ml-1">*</span>
                                                            </label>
                                                            <input type="text" name="last_name" id="last_name"
                                                                required
                                                                class="w-full px-4 py-3 border rounded-xl text-sm focus:border-indigo-500 focus:ring-3 focus:ring-indigo-100 transition-all duration-200 group-hover/input:border-gray-300 {{ $errors->profile->has('last_name') ? 'border-red-400' : 'border-gray-200' }}"
                                                                placeholder="{{ __('ui.enter_last_name') }}"
                                                                value="{{ old('last_name', $user->last_name) }}">
                                                            @error('last_name', 'profile')
                                                                <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>

                                                    <!-- Username -->
                                                    <div class="mb-5 group/input">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                            {{ __('ui.username') }}
                                                        </label>
                                                        <input type="text" name="username" id="username"
                                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-3 focus:ring-indigo-100 transition-all duration-200 group-hover/input:border-gray-300"
                                                            placeholder="{{ __('ui.enter_username') }}"
                                                            value="{{ old('username', $user->username) }}">
                                                        @error('username', 'profile')
                                                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- Email -->
                                                    <div class="mb-5 group/input">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                            {{ __('ui.email') }} <span class="text-red-500 ml-1">*</span>
                                                        </label>
                                                        <input type="email" name="email" id="email" required
                                                            class="w-full px-4 py-3 border rounded-xl text-sm focus:border-indigo-500 focus:ring-3 focus:ring-indigo-100 transition-all duration-200 group-hover/input:border-gray-300 {{ $errors->profile->has('email') ? 'border-red-400' : 'border-gray-200' }}"
                                                            placeholder="{{ __('ui.email_address') }}"
                                                            value="{{ old('email', $user->email) }}">
                                                        @error('email', 'profile')
                                                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                                        @enderror
                                                    </div>

                                                    <!-- Phone Number -->
                                                    <div class="mb-5 group/input">
                                                        <label
                                                            class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                                            {{ __('ui.contact') }}
                                                        </label>
                                                        <input type="text" name="phone_number" id="phone_number"
                                                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-3 focus:ring-indigo-100 transition-all duration-200 group-hover/input:border-gray-300"
                                                            placeholder="{{ __('ui.phone_number') }}"
                                                            value="{{ old('phone_number', $user->phone_number) }}">
                                                        @error('phone_number', 'profile')
                                                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <!-- Footer -->
                                                    <div class="mt-6 flex justify-end gap-3">
                                                        <button type="button" @click="modalOpenDetail = false"
                                                            class="px-5 py-2.5 rounded-xl border text-sm font-medium text-gray-600 hover:bg-gray-100 transition-all duration-200">
                                                            {{ __('ui.cancel') }}
                                                        </button>
                                                        <button type="submit"
                                                            class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl shadow hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 flex items-center gap-2 hover:shadow-md">
                                                            <svg class="w-4 h-4" fill="currentColor"
                                                                viewBox="0 0 20 20"
                                                                xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd"
                                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span>{{ __('ui.save_changes') }}</span>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: berubah sesuai tab -->
                <div class="w-full lg:w-2/3 space-y-6">
                    <!-- Account Details -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300"
                        x-show="activeTab === 'account'">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            {{ __('ui.account_details') }}
                        </h2>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.username') }}
                                </label>
                                <div
                                    class="flex items-center text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span class="text-indigo-600 font-medium">
                                        {{ $user->username ? '@' . $user->username : '' }}
                                    </span>
                                </div>

                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z">
                                        </path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                    {{ __('ui.email') }}
                                </label>
                                <div
                                    class="flex items-center text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span>{{ $user->email }}</span>
                                    @if ($user->email_verified_at)
                                        <span
                                            class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-emerald-100 text-emerald-800 rounded-full">
                                            {{ __('ui.verified') }}
                                        </span>
                                    @else
                                        <span
                                            class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                                            {{ __('ui.not_verified') }}
                                        </span>
                                    @endif
                                </div>

                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.role') }}
                                </label>
                                <div
                                    class="text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $user->role->name }}
                                    </span>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z">
                                        </path>
                                    </svg>
                                    {{ __('ui.contact') }}
                                </label>
                                <div
                                    class="flex items-center text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span>{{ $user->phone_number }}</span>
                                </div>
                            </div>

                            <div class="group" x-data="{ showLangDropdown: false }">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                    </svg>
                                    {{ __('ui.languages') }}
                                </label>
                                <div class="relative">
                                    <button type="button"
                                        @click="showLangDropdown = !showLangDropdown"
                                        class="w-full text-left text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors cursor-pointer flex items-center justify-between">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ app()->getLocale() === 'id' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ app()->getLocale() === 'id' ? __('ui.lang_indonesian') : __('ui.lang_english') }}
                                        </span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform" :class="showLangDropdown ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="showLangDropdown"
                                        @click.away="showLangDropdown = false"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-1"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden"
                                        x-cloak>

                                        <form method="POST" action="{{ route('user.locale.update') }}">
                                            @csrf
                                            <input type="hidden" name="locale" value="en">
                                            <button type="submit"
                                                class="w-full flex items-center justify-between px-4 py-3 text-sm hover:bg-indigo-50 transition-colors {{ app()->getLocale() === 'en' ? 'bg-blue-50' : '' }}">
                                                <div class="flex items-center gap-3">
                                                    <span class="fi fi-gb w-5 h-4 rounded shadow-sm"></span>
                                                    <span class="font-medium text-gray-700">English</span>
                                                </div>
                                                @if(app()->getLocale() === 'en')
                                                    <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        <div class="border-t border-gray-100"></div>

                                        <form method="POST" action="{{ route('user.locale.update') }}">
                                            @csrf
                                            <input type="hidden" name="locale" value="id">
                                            <button type="submit"
                                                class="w-full flex items-center justify-between px-4 py-3 text-sm hover:bg-indigo-50 transition-colors {{ app()->getLocale() === 'id' ? 'bg-red-50' : '' }}">
                                                <div class="flex items-center gap-3">
                                                    <span class="fi fi-id w-5 h-4 rounded shadow-sm"></span>
                                                    <span class="font-medium text-gray-700">Bahasa Indonesia</span>
                                                </div>
                                                @if(app()->getLocale() === 'id')
                                                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.country') }}
                                </label>
                                <div
                                    class="flex items-center text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span class="fi fi-gb mr-2"></span>
                                    Indonesia
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.joined_date') }}
                                </label>
                                <div
                                    class="flex items-center text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <span>{{ $user->created_at->format('M d, Y') }}</span>
                                    <span
                                        class="ml-2 px-1.5 py-0.5 text-xs font-medium bg-amber-100 text-amber-800 rounded-full">
                                        {{ $user->created_at->diffForHumans(['parts' => 2, 'short' => true]) }}
                                    </span>
                                </div>
                            </div>

                            <div class="group" x-data="{ showAlert: false }">
                                <label class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.security') }}
                                </label>

                                <div
                                    class="text-sm text-gray-900 bg-gray-50 p-3 rounded-xl border border-gray-100 group-hover:bg-indigo-50 group-hover:border-indigo-100 transition-colors">
                                    <button
                                        @click="
                                                    @if (auth()->user()->is_admin == 1) activeTab = 'security'
                                                    @else
                                                        showAlert = true
                                                        setTimeout(() => showAlert = false, 2500) @endif
                                                "
                                        :class="activeTab === 'security'
                                            ?
                                            'bg-blue-500 text-white px-3 py-2 rounded-lg flex items-center gap-2' :
                                            'text-gray-600 hover:text-blue-500 flex items-center gap-2'"
                                        class="text-sm font-medium transition">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 15v2m-6-6h12a2 2 0 012 2v7a2 2 0 01-2 2H6a2 2 0 01-2-2v-7a2 2 0 012-2zm10-4V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        {{ __('ui.change_password') }}
                                    </button>
                                </div>

                                <!-- Notifikasi Modern -->
                                <div x-show="showAlert" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="fixed bottom-5 right-5 bg-white border border-gray-200 shadow-lg rounded-xl px-4 py-3 flex items-center space-x-3 text-gray-800">
                                    <svg class="w-6 h-6 text-yellow-500 flex-shrink-0 mt-0.5" fill="none"
                                        stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                    </svg>
                                    <span class="text-sm font-medium">{{ __('ui.admin_only_password') }}</span>
                                </div>
                            </div>


                        </div>
                    </div>
                    @if (auth()->user()->is_admin == 1)
                        <!-- Security -->
                        <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300"
                            x-show="activeTab === 'security'" x-data="{ passwordMatch: true }">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ __('ui.change_password_btn') }}
                            </h2>


                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">{{ __('ui.password_requirements') }}</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <ul class="list-disc pl-5 space-y-1">
                                                <li>{{ __('ui.password_min_chars') }}</li>
                                                <li>{{ __('ui.password_uppercase') }}</li>
                                                <li>{{ __('ui.password_symbol') }}</li>
                                                <li>{{ __('ui.password_number') }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('user.password.update') }}" class="space-y-5"
                                @submit="if(!passwordMatch) { $event.preventDefault(); }">
                                @csrf
                                @method('PUT')

                                <!-- Current Password -->
                                <div class="group">
                                    <label for="current-password"
                                        class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('ui.current_password') }} <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="current-password" name="current_password" required
                                            class="block w-full border rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors group-hover:border-gray-300 {{ $errors->security->has('current_password') ? 'border-red-400' : 'border-gray-200' }}"
                                            placeholder="{{ __('ui.enter_current_password') }}">
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 toggle-password transition-colors">
                                            <svg class="h-5 w-5 eye-open" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg class="h-5 w-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('current_password', 'security')
                                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="group">
                                    <label for="new-password"
                                        class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('ui.new_password') }} <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="new-password" name="password" required
                                            class="block w-full border rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors group-hover:border-gray-300 {{ $errors->security->has('password') ? 'border-red-400' : 'border-gray-200' }}"
                                            placeholder="{{ __('ui.enter_new_password') }}">
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 toggle-password transition-colors">
                                            <svg class="h-5 w-5 eye-open" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg class="h-5 w-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="mt-2 bg-gray-100 rounded-lg h-1.5">
                                        <div class="h-full bg-red-500 rounded-lg password-strength" style="width: 0%">
                                        </div>
                                    </div>
                                    @error('password', 'security')
                                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="group">
                                    <label for="confirm-password"
                                        class="block text-sm font-medium text-gray-700 mb-2 flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-500" fill="currentColor"
                                            viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd"
                                                d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        {{ __('ui.confirm_new_password') }} <span class="text-red-500 ml-1">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password" id="confirm-password" name="password_confirmation"
                                            required
                                            class="block w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-colors group-hover:border-gray-300"
                                            placeholder="{{ __('ui.confirm_your_password') }}"
                                            @input="passwordMatch = ($el.value === document.getElementById('new-password').value)">
                                        <button type="button"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-indigo-600 toggle-password transition-colors">
                                            <svg class="h-5 w-5 eye-open" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg class="h-5 w-5 eye-closed hidden" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p x-show="!passwordMatch && document.getElementById('confirm-password').value.length > 0"
                                        class="text-red-500 text-xs mt-2">{{ __('ui.passwords_do_not_match') }}</p>
                                    @error('password_confirmation', 'security')
                                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!passwordMatch">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    {{ __('ui.change_password_btn') }}
                                </button>
                            </form>
                        </div>
                    @endif


                    <div class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-300"
                        x-show="activeTab === 'history'" x-data="{
                            activities: [],
                            loading: false,
                            error: null,
                            initialized: false,
                            expandedDays: {},
                            translations: {
                                today: '{{ __("ui.today") }}',
                                yesterday: '{{ __("ui.yesterday") }}',
                                activities_count: '{{ __("ui.activities_count") }}',
                                guest_label: '{{ __("ui.guest_label") }}',
                                printed_by_label: '{{ __("ui.printed_by_label") }}',
                                order_label: '{{ __("ui.order_label") }}',
                                printed: '{{ __("ui.printed") }}',
                                printed_at: '{{ __("ui.printed_at") }}',
                            },
                            get todayKey() {
                                return new Date().toISOString().split('T')[0];
                            },
                            get groupedActivities() {
                                const groups = {};
                                this.activities.forEach(activity => {
                                    const dateKey = new Date(activity.timestamp).toISOString().split('T')[0];
                                    if (!groups[dateKey]) {
                                        const d = new Date(activity.timestamp);
                                        const today = new Date();
                                        const yesterday = new Date();
                                        yesterday.setDate(yesterday.getDate() - 1);
                                        let label = '';
                                        if (dateKey === today.toISOString().split('T')[0]) {
                                            label = this.translations.today;
                                        } else if (dateKey === yesterday.toISOString().split('T')[0]) {
                                            label = this.translations.yesterday;
                                        } else {
                                            label = d.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                                        }
                                        groups[dateKey] = { label: label, dateKey: dateKey, activities: [] };
                                    }
                                    groups[dateKey].activities.push(activity);
                                });
                                return Object.values(groups).sort((a, b) => b.dateKey.localeCompare(a.dateKey));
                            },
                            isExpanded(dateKey) {
                                if (dateKey === this.todayKey) return true;
                                return this.expandedDays[dateKey] || false;
                            },
                            toggleDay(dateKey) {
                                this.expandedDays[dateKey] = !this.expandedDays[dateKey];
                            },
                            async fetchActivities() {
                                try {
                                    this.loading = true;
                                    this.error = null;
                                    const response = await fetch('{{ route('user.activity') }}');
                                    const data = await response.json();
                                    if (data.success) {
                                        this.activities = data.activities;
                                    } else {
                                        this.error = 'Failed to load activities';
                                    }
                                } catch (err) {
                                    this.error = 'Error loading activities';
                                    console.error(err);
                                } finally {
                                    this.loading = false;
                                }
                            }
                        }" x-init="$watch('activeTab', value => {
                            if (value === 'history' && !initialized) {
                                initialized = true;
                                fetchActivities();
                            }
                        });
                        if (activeTab === 'history' && !initialized) {
                            initialized = true;
                            fetchActivities();
                        }">

                        <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ __('ui.activity_history') }}
                        </h2>

                        <!-- Loading State -->
                        <div x-show="loading" class="flex items-center justify-center py-12">
                            <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span class="ml-3 text-gray-500 text-sm">{{ __('ui.loading_history') }}</span>
                        </div>

                        <!-- Error State -->
                        <div x-show="!loading && error" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-red-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500" x-text="error"></p>
                        </div>

                        <!-- Empty State -->
                        <div x-show="!loading && !error && activities.length === 0" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">{{ __('ui.no_activities') }}</p>
                        </div>

                        <!-- Grouped Activities by Date -->
                        <div x-show="!loading && !error && activities.length > 0" class="space-y-4">
                            <template x-for="(group, groupIndex) in groupedActivities" :key="group.dateKey">
                                <div class="rounded-xl border border-gray-200 overflow-hidden">
                                    <!-- Date Header -->
                                    <button @click="toggleDay(group.dateKey)"
                                        class="w-full flex items-center justify-between px-5 py-3 transition-colors"
                                        :class="group.dateKey === todayKey ? 'bg-gradient-to-r from-indigo-50 to-blue-50' :
                                            'bg-gray-50 hover:bg-gray-100'">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                                :class="group.dateKey === todayKey ? 'bg-indigo-500 text-white' :
                                                    'bg-gray-300 text-white'">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <h3 class="text-sm font-semibold"
                                                    :class="group.dateKey === todayKey ? 'text-indigo-700' : 'text-gray-700'"
                                                    x-text="group.label"></h3>
                                                <p class="text-xs text-gray-500">
                                                    <span x-text="group.activities.length"></span> {{ __('ui.activities_count') }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span x-show="group.dateKey === todayKey"
                                                class="px-2 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700 rounded-full">
                                                {{ __('ui.live') }}
                                            </span>
                                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                                :class="isExpanded(group.dateKey) ? 'rotate-180' : ''" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </button>

                                    <!-- Activities for this date -->
                                    <div x-show="isExpanded(group.dateKey)"
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-2"
                                        class="px-5 py-4 border-t border-gray-100">
                                        <div class="space-y-4 relative border-l-2 border-gray-200 ml-2">
                                            <template x-for="(activity, index) in group.activities"
                                                :key="index">
                                                <div class="ml-6 relative group/item">
                                                    <!-- Timeline Dot -->
                                                    <div class="absolute -left-[1.05rem] top-1.5 w-4 h-4 rounded-full ring-2 ring-white shadow-md flex items-center justify-center"
                                                        :class="{
                                                            'bg-gradient-to-r from-purple-500 to-indigo-600': activity
                                                                .type === 'reservation',
                                                            'bg-gradient-to-r from-green-500 to-emerald-600': activity
                                                                .type === 'checkin',
                                                            'bg-gradient-to-r from-red-500 to-rose-600': activity
                                                                .type === 'checkout',
                                                            'bg-gradient-to-r from-sky-500 to-blue-600': activity
                                                                .type === 'payment',
                                                            'bg-gradient-to-r from-orange-500 to-amber-600': activity
                                                                .type === 'print_registration'
                                                        }">
                                                        <svg class="w-2 h-2 text-white" fill="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path
                                                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                        </svg>
                                                    </div>

                                                    <!-- Activity Card -->
                                                    <div class="p-4 rounded-xl border transition-all duration-300"
                                                        :class="{
                                                            'bg-gradient-to-r from-purple-50 to-indigo-50 border-purple-100 hover:border-purple-200': activity
                                                                .type === 'reservation',
                                                            'bg-gradient-to-r from-green-50 to-emerald-50 border-green-100 hover:border-green-200': activity
                                                                .type === 'checkin',
                                                            'bg-gradient-to-r from-red-50 to-rose-50 border-red-100 hover:border-red-200': activity
                                                                .type === 'checkout',
                                                            'bg-gradient-to-r from-sky-50 to-blue-50 border-sky-100 hover:border-sky-200': activity
                                                                .type === 'payment',
                                                            'bg-gradient-to-r from-orange-50 to-amber-50 border-orange-100 hover:border-orange-200': activity
                                                                .type === 'print_registration'
                                                        }">
                                                        <div class="flex justify-between items-start">
                                                            <div class="flex-1">
                                                                <h3 class="text-base font-semibold text-gray-900 transition-colors"
                                                                    :class="{
                                                                        'group-hover/item:text-indigo-700': activity
                                                                            .type === 'reservation',
                                                                        'group-hover/item:text-emerald-700': activity
                                                                            .type === 'checkin',
                                                                        'group-hover/item:text-rose-700': activity
                                                                            .type === 'checkout',
                                                                        'group-hover/item:text-blue-700': activity
                                                                            .type === 'payment',
                                                                        'group-hover/item:text-amber-700': activity
                                                                            .type === 'print_registration'
                                                                    }"
                                                                    x-text="activity.title"></h3>
                                                                <p class="text-sm text-gray-600 mt-1"
                                                                    x-text="activity.description"></p>

                                                                <!-- Activity Details -->
                                                                <div class="mt-3" x-show="activity.data">
                                                                    <template
                                                                        x-if="activity.type === 'reservation' || activity.type === 'checkin' || activity.type === 'checkout' || activity.type === 'print_registration'">
                                                                        <div class="flex flex-wrap gap-2">
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white border border-gray-200"
                                                                                x-show="activity.data.room_name"
                                                                                x-text="activity.data.room_name"></span>
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white border border-gray-200"
                                                                                x-show="activity.data.guest_name"
                                                                                x-text="translations.guest_label + ': ' + activity.data.guest_name"></span>
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white border border-gray-200"
                                                                                x-show="activity.data.printed_by"
                                                                                x-text="translations.printed_by_label + ': ' + activity.data.printed_by"></span>
                                                                        </div>
                                                                    </template>

                                                                    <template x-if="activity.type === 'payment'">
                                                                        <div class="flex items-center gap-2">
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-white border border-gray-200"
                                                                                x-show="activity.data.order_id"
                                                                                x-text="translations.order_label + ': ' + activity.data.order_id"></span>
                                                                            <span
                                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                                                                                x-show="activity.data.status"
                                                                                :class="{
                                                                                    'bg-green-100 text-green-800': activity
                                                                                        .data.status === 'paid',
                                                                                    'bg-yellow-100 text-yellow-800': activity
                                                                                        .data.status === 'pending',
                                                                                    'bg-red-100 text-red-800': activity
                                                                                        .data.status === 'failed'
                                                                                }"
                                                                                x-text="activity.data.status"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>

                                                            <!-- Badge untuk print status -->
                                                            <div x-show="activity.type === 'print_registration' && activity.data && activity.data.is_printed == 1"
                                                                class="ml-2">
                                                                <span
                                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                    <span x-text="translations.printed"></span>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3 flex justify-between items-center">
                                                            <!-- Print timestamp -->
                                                            <div
                                                                x-show="activity.type === 'print_registration' && activity.data.printed_at">
                                                                <span
                                                                    class="text-xs text-gray-600 bg-gray-50 px-2 py-1 rounded border border-gray-200">
                                                                    <span x-text="translations.printed_at"></span>: <span
                                                                        x-text="new Date(activity.data.printed_at).toLocaleString('en-US', {
                                                                        month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit'
                                                                    })"></span>
                                                                </span>
                                                            </div>

                                                            <!-- Activity timestamp (time only since date is in the group header) -->
                                                            <span
                                                                class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full border border-gray-200 ml-auto"
                                                                x-text="new Date(activity.timestamp).toLocaleString('id-ID', {
                                                                    hour: '2-digit', minute: '2-digit'
                                                                })"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('modal', () => ({
                modalOpenDetail: false,
                files: [],
                fileUploaded: false,

                loadFiles(archives) {
                    this.files = [];
                }
            }));
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Flash notifications via Toastify
            @if (session('success'))
                Toastify({
                    text: "{{ session('success') }}",
                    duration: 3500,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    style: { background: "#10B981" }
                }).showToast();
            @endif

            @if (session('error'))
                Toastify({
                    text: "{{ session('error') }}",
                    duration: 4000,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    style: { background: "#EF4444" }
                }).showToast();
            @endif

            @if (session('password_success'))
                Toastify({
                    text: "{{ session('password_success') }}",
                    duration: 3500,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    style: { background: "#10B981" }
                }).showToast();
            @endif

            @if (session('password_error'))
                Toastify({
                    text: "{{ session('password_error') }}",
                    duration: 4000,
                    close: true,
                    gravity: "bottom",
                    position: "right",
                    style: { background: "#EF4444" }
                }).showToast();
            @endif

            // Toggle password visibility
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const input = this.parentElement.querySelector('input');
                    const eyeOpen = this.querySelector('.eye-open');
                    const eyeClosed = this.querySelector('.eye-closed');

                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeOpen.classList.add('hidden');
                        eyeClosed.classList.remove('hidden');
                    } else {
                        input.type = 'password';
                        eyeOpen.classList.remove('hidden');
                        eyeClosed.classList.add('hidden');
                    }
                });
            });

            // Password strength indicator
            const passwordInput = document.getElementById('new-password');
            if (passwordInput) {
                const strengthBar = document.querySelector('.password-strength');

                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    if (password.length >= 8) strength += 25;
                    if (/[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 25;

                    strengthBar.style.width = strength + '%';

                    // Change color based on strength
                    if (strength < 50) {
                        strengthBar.className = 'h-full bg-red-500 rounded-lg password-strength';
                    } else if (strength < 100) {
                        strengthBar.className = 'h-full bg-yellow-500 rounded-lg password-strength';
                    } else {
                        strengthBar.className = 'h-full bg-green-500 rounded-lg password-strength';
                    }
                });
            }
        });
    </script>
</x-app-layout>
