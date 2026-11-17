<x-layouts.auth :title="__('Create your account')" :subtitle="__('Your CacheTrail account is free and separate from Geocaching.com.')">
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <x-input name="name" :label="__('Name')" autocomplete="name" required autofocus />
        <x-input name="email" type="email" :label="__('Email address')" autocomplete="email" required />
        <x-input name="password" type="password" :label="__('Password')" autocomplete="new-password" :hint="__('At least 8 characters.')" required />
        <x-input name="password_confirmation" type="password" :label="__('Confirm password')" autocomplete="new-password" required />

        <x-button class="w-full">{{ __('Sign up') }}</x-button>
    </form>

    <x-slot:footer>
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="font-semibold text-forest-700 underline-offset-4 hover:underline">{{ __('Log in') }}</a>
    </x-slot:footer>
</x-layouts.auth>
