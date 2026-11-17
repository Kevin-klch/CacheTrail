<x-layouts.auth :title="__('Forgot your password?')" :subtitle="__('Enter your email address and we will send you a link to choose a new password.')">
    @if (session('status'))
        <x-alert class="mb-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <x-input name="email" type="email" :label="__('Email address')" autocomplete="email" required autofocus />

        <x-button class="w-full">{{ __('Send reset link') }}</x-button>
    </form>

    <x-slot:footer>
        <a href="{{ route('login') }}" class="font-semibold text-forest-700 underline-offset-4 hover:underline">{{ __('Back to login') }}</a>
    </x-slot:footer>
</x-layouts.auth>
