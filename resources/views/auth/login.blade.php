<x-layouts.auth :title="__('Welcome back')" :subtitle="__('Log in to discover and share routes.')">
    @if (session('status'))
        <x-alert class="mb-6">{{ session('status') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-input name="email" :label="__('Email address or username')" autocomplete="username" required autofocus />
        <x-input name="password" type="password" :label="__('Password')" autocomplete="current-password" required />

        <div class="flex items-center justify-between gap-4 text-sm">
            <label class="inline-flex items-center gap-2 text-ink-muted">
                <input type="checkbox" name="remember" class="size-4 rounded border-sand-300 accent-forest-700" @checked(old('remember'))>
                {{ __('Remember me') }}
            </label>
            <a href="{{ route('password.request') }}" class="font-medium text-forest-700 underline-offset-4 hover:underline">{{ __('Forgot your password?') }}</a>
        </div>

        <x-button class="w-full">{{ __('Log in') }}</x-button>
    </form>

    <x-slot:footer>
        {{ __('No account yet?') }}
        <a href="{{ route('register') }}" class="font-semibold text-forest-700 underline-offset-4 hover:underline">{{ __('Sign up for free') }}</a>
    </x-slot:footer>
</x-layouts.auth>
