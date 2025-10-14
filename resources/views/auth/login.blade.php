<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <div class="flex">
                <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                    +963
                </span>
                <x-text-input id="phone_input" class="block mt-1 w-full rounded-l-none" type="text" name="phone_display" :value="old('phone') ? substr(old('phone'), 4) : ''" required autofocus autocomplete="phone" placeholder="9xxxxxxxx" maxlength="9" />
                <input type="hidden" id="phone" name="phone" :value="old('phone')" />
            </div>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <!-- Sign Up Link -->
        <div class="flex items-center justify-center mt-4">
            <span class="text-sm text-gray-600">{{ __("Don't have an account?") }}</span>
            <a href="{{ route('register') }}" class="ms-2 text-sm text-indigo-600 hover:text-indigo-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Sign up') }}
            </a>
        </div>
    </form>
</x-guest-layout>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.getElementById('phone_input');
    const hiddenPhone = document.getElementById('phone');
    
    phoneInput.addEventListener('input', function() {
        // Only allow digits
        this.value = this.value.replace(/\D/g, '');
        // Update hidden field with +963 prefix
        hiddenPhone.value = '+963' + this.value;
    });
    
    // Initialize hidden field on page load
    if (phoneInput.value) {
        hiddenPhone.value = '+963' + phoneInput.value;
    }
});
</script>
