<x-guest-layout>
    @if(isset($invitation) && $invitation)
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-medium text-blue-800">You're invited to join a group!</h3>
                    <p class="text-sm text-blue-600 mt-1">Group: {{ $invitation['group_name'] ?? 'Unknown Group' }}</p>
                    <p class="text-xs text-blue-500 mt-1">Complete your registration to automatically join this group.</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <div class="flex">
                <div id="country-selector-register"></div>
                <x-text-input id="phone_input" class="block mt-1 w-full rounded-l-none" type="text" name="phone_display" :value="old('phone') ? substr(old('phone'), strpos(old('phone'), ' ') ?: 4) : ''" required autocomplete="phone" placeholder="9xxxxxxxx" />
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
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

    <!-- Country Codes and Selector Scripts -->
    <script src="{{ asset('js/country-codes.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/CountryCodeSelector.js') }}?v={{ time() }}"></script>

<script>
// Initialize the country code selector for register
const registerSelector = new CountryCodeSelector('country-selector-register', '+963');

// Update the hidden phone field when country or phone input changes
function updatePhoneField() {
    const phoneDisplay = document.getElementById('phone_input');
    const phoneHidden = document.getElementById('phone');
    const selectedCountryCode = registerSelector.getSelectedCountryCode();
    
    if (phoneDisplay && phoneHidden) {
        phoneHidden.value = selectedCountryCode + phoneDisplay.value;
    }
}

// Listen for country selection changes
document.addEventListener('countrySelected', updatePhoneField);

// Listen for phone input changes
document.addEventListener('DOMContentLoaded', function() {
    const phoneDisplay = document.getElementById('phone_input');
    if (phoneDisplay) {
        phoneDisplay.addEventListener('input', updatePhoneField);
        
        // Handle existing phone value on page load
        const existingPhone = phoneDisplay.value;
        if (existingPhone) {
            updatePhoneField();
        }
    }
});
</script>
