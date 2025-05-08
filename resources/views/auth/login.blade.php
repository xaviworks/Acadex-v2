<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Username -->
        <div>
            <x-input-label for="email" :value="__('Email Username')" />
            <div class="flex rounded-md shadow-sm">
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full rounded-r-none" 
                    type="text" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    placeholder="Enter your username" 
                    pattern="^[^@]+$" 
                    title="Do not include '@' or domain — just the username."
                />
                <span class="inline-flex items-center px-3 rounded-r-md bg-gray-200 border border-l-0 border-gray-300 mt-1 text-sm text-gray-600">
                    @brokenshire.edu.ph
                </span>
            </div>

            <!-- Live warning -->
            <p id="email-warning" class="text-sm text-red-600 mt-1 hidden">
                Please enter only your username — do not include '@' or email domain.
            </p>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailField = document.getElementById('email');
            const warning = document.getElementById('email-warning');

            emailField.addEventListener('input', () => {
                if (emailField.value.includes('@')) {
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }
            });
        });
    </script>
</x-guest-layout>
