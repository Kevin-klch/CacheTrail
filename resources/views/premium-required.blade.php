<x-layouts.app :title="__('Premium access required')">
    <x-page-header :title="__('Premium access required')" :eyebrow="__('Almost there')">
        {{ __('This area is reserved for geocachers with premium access.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-card>
            <div class="flex flex-col items-start gap-6 sm:flex-row">
                <span class="grid size-14 shrink-0 place-items-center rounded-full bg-forest-100 text-forest-700">
                    <x-icon name="lock" class="size-7" />
                </span>
                <div class="space-y-3 text-ink-muted">
                    <p>{{ __('Discovering, creating and saving routes requires premium access.') }}</p>
                    <p>{{ __('In the future, you will be able to connect your Geocaching.com account here so that your premium membership can be verified automatically.') }}</p>
                    <div class="pt-3">
                        <x-button :href="route('home')" variant="secondary">{{ __('Back to home') }}</x-button>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</x-layouts.app>
