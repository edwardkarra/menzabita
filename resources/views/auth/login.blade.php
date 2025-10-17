<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Invitation Banner -->
    @if(isset($invitation) && $invitation)
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">{{ __('Group Invitation') }}</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        {{ __('You have been invited to join') }} <strong>{{ $invitation['group_name'] }}</strong>. 
                        {{ __('After logging in, you will automatically be added to the group.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

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
