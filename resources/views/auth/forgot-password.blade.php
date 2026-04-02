<x-guest-layout>
    <h2 class="text-xl font-bold text-white mb-2">Reset your password</h2>
    <p class="text-sm mb-6" style="color: #6b6b8a;">
        Enter your email and we'll send you a reset link.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Send reset link') }}
        </x-primary-button>

        <p class="text-center text-sm mt-6" style="color: #6b6b8a;">
            <a href="{{ route('login') }}" class="font-medium" style="color: #ff6b2b;">Back to sign in</a>
        </p>
    </form>
</x-guest-layout>
