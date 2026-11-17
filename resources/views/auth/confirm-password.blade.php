<x-layouts.auth :title="__('Confirm password')" :subtitle="__('Please confirm your password to continue.')">
    <form method="POST" action="{{ route('password.confirm.store') }}" class="space-y-5">
        @csrf

        <x-input name="password" type="password" :label="__('Password')" autocomplete="current-password" required autofocus />

        <x-button class="w-full">{{ __('Confirm') }}</x-button>
    </form>
</x-layouts.auth>
