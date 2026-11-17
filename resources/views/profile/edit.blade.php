<x-layouts.app :title="__('Profile')">
    <x-page-header :title="__('Your profile')" :eyebrow="__('Account')">
        {{ __('Manage your CacheTrail account.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
        <x-card>
            <h2 class="text-2xl font-bold">{{ __('Profile information') }}</h2>
            <p class="mt-1 text-sm text-ink-muted">{{ __('Your name is shown on the routes you publish.') }}</p>

            @if (session('status') === 'profile-information-updated')
                <x-alert class="mt-6">{{ __('Your profile has been saved.') }}</x-alert>
            @endif

            @unless ($user->hasVerifiedEmail())
                <div class="mt-6 rounded-2xl bg-trail-400/15 px-4 py-3 text-sm text-trail-600">
                    <p>{{ __('Your email address has not been confirmed yet.') }}</p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 font-semibold">{{ __('A new confirmation link has been sent to your email address.') }}</p>
                    @else
                        <form method="POST" action="{{ route('verification.send') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="font-semibold underline underline-offset-4">{{ __('Resend confirmation email') }}</button>
                        </form>
                    @endif
                </div>
            @endunless

            <form method="POST" action="{{ route('user-profile-information.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-input name="name" :label="__('Name')" :value="$user->name" bag="updateProfileInformation" autocomplete="name" required />
                <x-input name="email" type="email" :label="__('Email address')" :value="$user->email" bag="updateProfileInformation" autocomplete="email" :hint="__('If you change your email address, you will need to confirm it again.')" required />

                <x-button>{{ __('Save') }}</x-button>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-2xl font-bold">{{ __('Change password') }}</h2>
            <p class="mt-1 text-sm text-ink-muted">{{ __('Use a long password that you do not use anywhere else.') }}</p>

            @if (session('status') === 'password-updated')
                <x-alert class="mt-6">{{ __('Your password has been changed.') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('user-password.update') }}" class="mt-6 space-y-5">
                @csrf
                @method('PUT')

                <x-input name="current_password" type="password" :label="__('Current password')" bag="updatePassword" autocomplete="current-password" required />
                <x-input name="password" id="new_password" type="password" :label="__('New password')" bag="updatePassword" autocomplete="new-password" :hint="__('At least 8 characters.')" required />
                <x-input name="password_confirmation" id="new_password_confirmation" type="password" :label="__('Confirm password')" bag="updatePassword" autocomplete="new-password" required />

                <x-button>{{ __('Change password') }}</x-button>
            </form>
        </x-card>

        <x-card class="border-red-200">
            <h2 class="text-2xl font-bold text-red-800">{{ __('Delete account') }}</h2>
            <p class="mt-1 text-sm text-ink-muted">{{ __('Your account and all associated data will be permanently deleted. This cannot be undone.') }}</p>

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-6 space-y-5">
                @csrf
                @method('DELETE')

                <x-input name="password" id="delete_password" type="password" :label="__('Confirm with your password')" bag="deleteAccount" autocomplete="current-password" required />

                <x-button variant="danger">{{ __('Delete account permanently') }}</x-button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
