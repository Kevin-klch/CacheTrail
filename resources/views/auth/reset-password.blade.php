<x-layouts.auth :title="__('Choose a new password')">
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <x-input name="email" type="email" :label="__('Email address')" :value="$request->email" autocomplete="email" required />
        <x-input name="password" type="password" :label="__('New password')" autocomplete="new-password" :hint="__('At least 8 characters.')" required autofocus />
        <x-input name="password_confirmation" type="password" :label="__('Confirm password')" autocomplete="new-password" required />

        <x-button class="w-full">{{ __('Save new password') }}</x-button>
    </form>
</x-layouts.auth>
