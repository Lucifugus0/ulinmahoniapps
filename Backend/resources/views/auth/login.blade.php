<x-authentication-layout>
    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 space-y-8 transition-all hover:shadow-2xl">
        <!-- Header -->
        <div class="text-center space-y-3">
            <div class="animate-bounce-slow">
                <svg class="w-14 h-14 mx-auto text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ __('ui.login_welcome') }}
            </h1>
            <p class="text-gray-500 dark:text-gray-300 font-light">
                {{ __('ui.login_subtitle') }}
            </p>
        </div>

        @if (session('status'))
            <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-700 dark:text-emerald-300 text-sm flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('status') }}
            </div>
        @endif

        <!-- Form -->
        <form method="POST" action="" class="space-y-6">
            @csrf
            <div class="space-y-5">
                <!-- Email Input -->
                <div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-amber-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <x-input id="email" type="email" name="email" value="" required autofocus
                            class="pl-10 w-full rounded-lg border-gray-300 focus:border-amber-300 focus:ring-2 focus:ring-amber-200 transition-all"
                            placeholder="{{ __('ui.login_email_placeholder') }}" />
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 group-focus-within:text-amber-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <x-input id="password" type="password" name="password" required autocomplete="current-password"
                            class="pl-10 w-full pr-10 rounded-lg border-gray-300 focus:border-amber-300 focus:ring-2 focus:ring-amber-200 transition-all"
                            placeholder="••••••••" />
                        <button type="button"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-amber-500 transition-colors"
                            onclick="togglePasswordVisibility()">
                            <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded transition">
                    <span class="text-sm text-gray-600 dark:text-gray-300">{{ __('ui.login_remember_me') }}</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3 px-4 bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                {{ __('ui.login_sign_in') }}
            </button>
        </form>

        <x-validation-errors />

        <!-- Footer Links -->
        {{-- <div class="text-center text-sm text-gray-500 dark:text-gray-400 space-y-3">
            <p class="border-t border-gray-200 dark:border-gray-700 pt-4">
                {{ __('New here?') }}
                <a href="{{ route('register') }}" class="font-medium text-amber-600 hover:text-amber-700 transition-colors">
                    {{ __('Create account') }}
                </a>
            </p>
        </div> --}}
    </div>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }

        // Remember me functionality
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const rememberCheckbox = document.getElementById('remember');
            const loginForm = document.querySelector('form');

            // Load saved credentials on page load
            const savedEmail = localStorage.getItem('remembered_email');
            const savedPassword = localStorage.getItem('remembered_password');

            if (savedEmail && savedPassword) {
                emailInput.value = savedEmail;
                passwordInput.value = savedPassword;
                rememberCheckbox.checked = true;
            }

            // Save or remove credentials on form submit
            loginForm.addEventListener('submit', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('remembered_email', emailInput.value);
                    localStorage.setItem('remembered_password', passwordInput.value);
                } else {
                    localStorage.removeItem('remembered_email');
                    localStorage.removeItem('remembered_password');
                }
            });

            // Clear credentials if checkbox is unchecked
            rememberCheckbox.addEventListener('change', function() {
                if (!this.checked) {
                    localStorage.removeItem('remembered_email');
                    localStorage.removeItem('remembered_password');
                }
            });
        });
    </script>
</x-authentication-layout>