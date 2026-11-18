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

                <div>
                    <p class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Geocaching username') }}</p>
                    <p class="flex items-center gap-2 rounded-xl border border-sand-200 bg-sand-100/60 px-4 py-2.5 font-semibold text-forest-900">
                        <x-icon name="lock" class="size-4 text-ink-muted" />
                        {{ $user->name }}
                    </p>
                    <p class="mt-1.5 text-sm text-ink-muted">{{ __('Your username cannot be changed. As soon as you connect your Geocaching.com account, it is taken over from there automatically.') }}</p>
                </div>
                <x-input name="email" type="email" :label="__('Email address')" :value="$user->email" bag="updateProfileInformation" autocomplete="email" :hint="__('If you change your email address, you will need to confirm it again.')" required />

                <x-button>{{ __('Save') }}</x-button>
            </form>
        </x-card>

        <x-card id="bilder">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold">{{ __('Profile picture and cover image') }}</h2>
                    <p class="mt-1 text-sm text-ink-muted">{{ __('Shown on your public profile. JPG, PNG or WebP, up to 5 MB.') }}</p>
                </div>
                <a href="{{ $user->profileUrl() }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                    {{ __('View public profile') }}
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            @if (session('status') === 'images-updated')
                <x-alert class="mt-6">{{ __('Your images have been saved.') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('profile.images.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Preview: cover with the profile picture on top, like on the public profile --}}
                <div class="relative overflow-hidden rounded-2xl bg-forest-700">
                    @if ($user->coverUrl())
                        <img src="{{ $user->coverUrl() }}" alt="{{ __('Current cover image') }}" class="h-36 w-full object-cover">
                    @else
                        <div class="grid h-36 place-items-center text-sm text-forest-100/80">{{ __('No cover image yet') }}</div>
                    @endif
                    <x-avatar :user="$user" class="absolute bottom-3 left-4 size-16 text-2xl ring-4 ring-paper" />
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @foreach (['avatar' => [__('Profile picture'), __('Square images work best, at least 100 × 100 pixels.'), $user->avatar_path], 'cover' => [__('Cover image'), __('Wide images work best, at least 600 × 150 pixels.'), $user->cover_path]] as $field => [$label, $hint, $current])
                        <div class="space-y-2">
                            <label for="{{ $field }}" class="block text-sm font-medium text-forest-900">{{ $label }}</label>
                            <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png,image/webp"
                                class="block w-full text-sm text-ink-muted file:mr-4 file:rounded-full file:border-0 file:bg-forest-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-forest-800 hover:file:bg-forest-200">
                            @if ($errors->updateImages->has($field))
                                <p class="text-sm text-red-700">{{ $errors->updateImages->first($field) }}</p>
                            @else
                                <p class="text-sm text-ink-muted">{{ $hint }}</p>
                            @endif
                            @if ($current)
                                <label class="inline-flex items-center gap-2 text-sm text-ink-muted">
                                    <input type="hidden" name="remove_{{ $field }}" value="0">
                                    <input type="checkbox" name="remove_{{ $field }}" value="1" class="size-4 rounded border-sand-300 accent-forest-700">
                                    {{ __('Remove') }}
                                </label>
                            @endif
                        </div>
                    @endforeach
                </div>

                <x-button>{{ __('Save images') }}</x-button>
            </form>
        </x-card>

        <x-card id="infobox">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="text-2xl font-bold">{{ __('Info box') }}</h2>
                    <p class="mt-1 text-sm text-ink-muted">{{ __('A few words about you for your public profile, e.g. your favourite caches or where you like to go geocaching.') }}</p>
                </div>
                <a href="{{ $user->profileUrl() }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                    {{ __('View public profile') }}
                    <x-icon name="arrow-right" class="size-4" />
                </a>
            </div>

            @if (session('status') === 'bio-updated')
                <x-alert class="mt-6">{{ __('Your info box has been saved.') }}</x-alert>
            @endif

            <form method="POST" action="{{ route('profile.bio.update') }}" class="mt-6 space-y-4" x-data="{ length: {{ mb_strlen((string) old('bio', $user->bio)) }} }">
                @csrf
                @method('PUT')

                <div>
                    <label for="bio" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('About me') }}</label>
                    <textarea id="bio" name="bio" rows="4" maxlength="{{ App\Http\Controllers\Profile\ProfileController::BIO_MAX_LENGTH }}" x-on:input="length = $el.value.length"
                        class="block w-full rounded-xl border border-sand-300 bg-white px-4 py-3 leading-relaxed text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">{{ old('bio', $user->bio) }}</textarea>
                    <div class="mt-1.5 flex justify-between gap-4 text-sm">
                        @if ($errors->updateBio->has('bio'))
                            <p class="text-red-700">{{ $errors->updateBio->first('bio') }}</p>
                        @else
                            <span></span>
                        @endif
                        <span class="text-ink-muted"><span x-text="length"></span> / {{ App\Http\Controllers\Profile\ProfileController::BIO_MAX_LENGTH }}</span>
                    </div>
                </div>

                <x-button>{{ __('Save info box') }}</x-button>
            </form>
        </x-card>

        <x-card>
            <h2 class="text-2xl font-bold">{{ __('Geocaching.com account') }}</h2>
            @if ($user->geocachingAccount)
                <p class="mt-1 text-sm text-ink-muted">{{ __('Connected as :name. Your finds and membership are shown on your profile.', ['name' => $user->geocachingAccount->username]) }}</p>
                <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl bg-sand-100/70 px-4 py-3">
                        <dt class="text-xs uppercase tracking-wide text-ink-muted">{{ __('Geocaches found') }}</dt>
                        <dd class="mt-1 font-heading text-xl font-bold text-forest-900">{{ App\Support\Format::number((int) $user->geocachingAccount->finds_count) }}</dd>
                    </div>
                    <div class="rounded-2xl bg-sand-100/70 px-4 py-3">
                        <dt class="text-xs uppercase tracking-wide text-ink-muted">{{ __('Geocacher since') }}</dt>
                        <dd class="mt-1 font-heading text-xl font-bold text-forest-900">{{ $user->geocachingAccount->member_since?->translatedFormat('F Y') ?? '–' }}</dd>
                    </div>
                    <div class="rounded-2xl bg-sand-100/70 px-4 py-3">
                        <dt class="text-xs uppercase tracking-wide text-ink-muted">{{ __('Membership') }}</dt>
                        <dd class="mt-1 font-heading text-xl font-bold text-forest-900">{{ ucfirst((string) $user->geocachingAccount->membership_status) ?: '–' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-1 text-sm text-ink-muted">{{ __('Not connected yet. As soon as CacheTrail has been approved by Geocaching HQ, you can connect your account here; your finds and membership will then appear on your profile.') }}</p>
            @endif
        </x-card>

        @if ($user->isFeedbackAccount())
            <x-card class="border-trail-400/40 bg-trail-400/10">
                <h2 class="text-2xl font-bold">{{ __('Shared feedback account') }}</h2>
                <p class="mt-1 text-sm text-ink">{{ __('Everybody trying out CacheTrail uses this account, so its email address and password cannot be changed and it cannot be deleted. Register your own account if you want to try that.') }}</p>
            </x-card>
        @else
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
        @endif
    </div>
</x-layouts.app>
