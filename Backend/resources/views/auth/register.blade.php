<x-authentication-layout>
    <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 space-y-6">
        <!-- Header -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ __('Create your Account') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                {{ __('Join our community today') }}
            </p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf
            
            <div class="space-y-5">
                <!-- Name Field -->
                <div>
                    <x-label for="name" class="mb-1">
                        {{ __('Full Name') }} <span class="text-red-500">*</span>
                    </x-label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <x-input id="name" type="text" name="name" :value="old('name')" required autofocus 
                            class="pl-10 w-full" placeholder="John Doe" />
                    </div>
                </div>

                <!-- Email Field -->
                <div>
                    <x-label for="email" class="mb-1">
                        {{ __('Email Address') }} <span class="text-red-500">*</span>
                    </x-label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <x-input id="email" type="email" name="email" :value="old('email')" required 
                            class="pl-10 w-full" placeholder="your@email.com" />
                    </div>
                </div>

                <!-- Password Field -->
                <div>
                    <x-label for="password" value="{{ __('Password') }}" class="mb-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-input id="password" type="password" name="password" required 
                            autocomplete="new-password" class="pl-10 w-full" placeholder="••••••••" />
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimum 8 characters</p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="mb-1" />
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <x-input id="password_confirmation" type="password" name="password_confirmation" required 
                            autocomplete="new-password" class="pl-10 w-full" placeholder="••••••••" />
                    </div>
                </div>
            </div>

            <!-- Newsletter Checkbox -->
            <div class="flex items-center">
                <input id="newsletter" name="newsletter" type="checkbox" 
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="newsletter" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    {{ __('Email me about product news') }}
                </label>
            </div>

            <!-- Terms Checkbox -->
            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" id="terms" 
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1">
                        <span class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                            {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-indigo-600 dark:text-indigo-400 hover:underline">'.__('Terms of Service').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-indigo-600 dark:text-indigo-400 hover:underline">'.__('Privacy Policy').'</a>',
                            ]) !!}
                        </span>
                    </label>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="pt-2">
                <x-button class="w-full justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    {{ __('Sign Up') }}
                </x-button>
            </div>
        </form>

        <x-validation-errors class="!mt-6" />

        <!-- Footer -->
        <div class="pt-5 mt-6 border-t border-gray-200 dark:border-gray-700 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('Already have an account?') }}
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500">
                    {{ __('Sign in') }}
                </a>
            </p>
        </div>
    </div>
</x-authentication-layout>