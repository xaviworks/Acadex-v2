@extends('layouts.guest')

@section('contents')
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-white" :status="session('status')" />

    <style>
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-background-clip: text;
            -webkit-text-fill-color: white !important;
            transition: background-color 5000s ease-in-out 0s;
            box-shadow: inset 0 0 20px 20px transparent !important;
        }
    </style>

    <form method="POST" action="{{ route('login') }}" class="w-full max-w-sm mx-auto text-white">
        @csrf

        <!-- Email Username -->
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email Username')" class="text-white" />
            <div class="relative flex rounded-md shadow-sm">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
                    <i class="fas fa-user"></i>
                </span>
                <x-text-input 
                    id="email" 
                    class="pl-10 mt-1 w-full rounded-r-none border border-gray-300 shadow-sm bg-transparent text-white placeholder-white focus:ring-green-500 focus:border-green-500 [&:-webkit-autofill]:bg-transparent [&:-webkit-autofill]:appearance-none [&:-webkit-autofill]:[box-shadow:0_0_0_30px_transparent_inset]"
                    type="text" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    placeholder="Enter your username" 
                    pattern="^[^@]+$" 
                    title="Do not include '@' or domain — just the username."
                />
                <span class="inline-flex items-center px-3 rounded-r-md bg-white/20 border border-l-0 border-gray-300 mt-1 text-sm text-white">
                    @brokenshire.edu.ph
                </span>
            </div>

            <!-- Live warning -->
            <p id="email-warning" class="text-sm text-red-400 mt-1 hidden">
                Please enter only your username — do not include '@' or email domain.
            </p>

            <x-input-error :messages="$errors->get('email')" class="text-red-400 mt-1" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Password')" class="text-white" />
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-white">
                    <i class="fas fa-lock"></i>
                </span>
                <x-text-input
                    id="password"
                    class="pl-10 pr-14 mt-1 w-full border border-gray-300 rounded-md shadow-sm bg-transparent text-white placeholder-white focus:ring-green-500 focus:border-green-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />
                <div class="absolute inset-y-0 right-0 flex items-center mr-5 mt-1">
                    <button
                        type="button"
                        id="togglePassword"
                        class="text-white/90 hover:text-white focus:outline-none transition-colors duration-200 h-full flex items-center px-2 hidden"
                    >
                        <i class="far fa-eye text-lg"></i>
                    </button>
                </div>
            </div>
            <x-input-error :messages="$errors->get('password')" class="text-red-400 mt-1" />
        </div>

        <!-- Forgot Password and Submit Button -->
        <div class="flex items-center justify-between">
            @if (Route::has('password.request'))
                <a class="text-sm text-green-300 hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="bg-green-700 hover:bg-green-800 text-white">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Don't have an account? Register Link -->
    <div class="text-center mt-4">
        <p class="text-sm text-white">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-green-300 hover:underline">
                <br>{{ __('Register') }}
            </a>
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const emailField = document.getElementById('email');
            const warning = document.getElementById('email-warning');
            const togglePassword = document.getElementById('togglePassword');
            const passwordField = document.getElementById('password');

            emailField.addEventListener('input', () => {
                if (emailField.value.includes('@')) {
                    warning.classList.remove('hidden');
                } else {
                    warning.classList.add('hidden');
                }
            });

            // Show/hide password toggle button based on password field content
            passwordField.addEventListener('input', function() {
                if (this.value.length > 0) {
                    togglePassword.classList.remove('hidden');
                } else {
                    togglePassword.classList.add('hidden');
                    // Reset to password type and eye icon when hiding
                    passwordField.setAttribute('type', 'password');
                    togglePassword.querySelector('i').classList.remove('fa-eye-slash');
                    togglePassword.querySelector('i').classList.add('fa-eye');
                }
            });

            // Password visibility toggle
            togglePassword.addEventListener('click', function() {
                const icon = this.querySelector('i');
                const type = passwordField.getAttribute('type');
                
                if (type === 'password') {
                    passwordField.setAttribute('type', 'text');
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordField.setAttribute('type', 'password');
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });
    </script>
@endsection
