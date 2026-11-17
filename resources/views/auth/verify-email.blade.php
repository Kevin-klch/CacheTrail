<x-layouts.auth :title="__('Confirm your email address')">
    <div class="space-y-5">
        <p class="text-ink-muted">
            {{ __('We have sent you an email with a confirmation link. Please click the link to activate your account.') }}
        </p>

        @if (session('status') === 'verification-link-sent')
            <x-alert>{{ __('A new confirmation link has been sent to your email address.') }}</x-alert>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-button class="w-full">{{ __('Resend confirmation email') }}</x-button>
        </form>
    </div>

    <x-slot:footer>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="font-semibold text-forest-700 underline-offset-4 hover:underline">{{ __('Log out') }}</button>
        </form>
    </x-slot:footer>
</x-layouts.auth>
