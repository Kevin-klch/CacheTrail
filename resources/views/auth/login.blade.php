<x-layouts.auth :title="__('Welcome back')" :subtitle="__('Log in to discover and share routes.')">
    @if (session('status'))
        <x-alert class="mb-6">{{ session('status') }}</x-alert>
    @endif

    <div class="mb-6 rounded-2xl bg-forest-50 px-4 py-3 text-sm text-forest-900 ring-1 ring-forest-600/15">
        <p class="font-semibold">{{ __('Want to try CacheTrail?') }}</p>
        <p class="mt-1">{{ __('Log in with the shared test account:') }}</p>
        <dl class="mt-2 grid grid-cols-[auto_1fr] gap-x-3 gap-y-0.5">
            <dt class="text-ink-muted">{{ __('Email address') }}</dt>
            <dd class="font-mono">{{ config('cachetrail.feedback.email') }}</dd>
            <dt class="text-ink-muted">{{ __('Password') }}</dt>
            <dd class="font-mono">{{ config('cachetrail.feedback.password') }}</dd>
        </dl>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <x-input name="email" type="email" :label="__('Email address')" autocomplete="email" required autofocus />
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
