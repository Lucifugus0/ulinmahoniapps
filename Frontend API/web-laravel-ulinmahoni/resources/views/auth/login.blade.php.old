<x-authentication-layout>
    <h1 class="text-3xl text-slate-800 font-bold mb-6">Welcome to {{ $CRM_ISS->nilai }}✨</h1>
    @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ session('status') }}
        </div>
    @endif   
    <!-- Form -->
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="space-y-4">
            <div>
                <x-jet-label for="email" value="{{ __('Your Email') }}" />
                <x-jet-input id="email" type="email" name="email" :value="old('email')" required autofocus />                
            </div>
            <div>
                <x-jet-label for="password" value="{{ __('Your Password') }}" />
                <x-jet-input id="password" type="password" name="password" required autocomplete="current-password" />                
            </div>
            {{-- <div>
                <input type="checkbox" name="remember" id="remember"/> 
                <label for="remember-me">Remember me</label>
            </div> --}}
        </div>
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <div class="mr-1">
                    <a class="text-sm underline hover:no-underline" href="{{ route('password.request') }}">
                        {{ __('Forgot Password?') }}
                    </a>
                </div>
            @endif            
            <x-jet-button class="ml-3">
                {{ __('Sign in') }}
            </x-jet-button>            
        </div>
    </form>
    <x-jet-validation-errors class="mt-4"/>    
    <!-- Footer -->
    <div class="pt-5 mt-6 border-t border-slate-200">
        <!-- Warning -->
        <div class="mt-5">
        </div>
    </div>
</x-authentication-layout>
