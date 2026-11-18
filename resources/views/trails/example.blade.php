<x-layouts.app :title="__('Example route')" :description="__('See what a geocaching route on CacheTrail looks like: map, caches in the recommended order and all key figures.')">
    <div class="slant-bottom bg-forest-700 pb-40 pt-10 text-paper sm:pt-14">
        <div class="mx-auto flex max-w-6xl flex-wrap items-end justify-between gap-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Example route') }}</p>
                <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ __('This is what a route looks like') }}</h1>
                <p class="mt-3 text-lg text-forest-100/90">{{ __('Every route shows the caches on a map and in the order recommended by the creator, together with duration, length, difficulty and terrain.') }}</p>
            </div>
            @guest
                <div class="flex flex-wrap gap-3">
                    <x-button variant="accent" :href="route('register')">{{ __('Sign up for free') }}</x-button>
                    <x-button variant="light" :href="route('login')">{{ __('Log in') }}</x-button>
                </div>
            @else
                <x-button variant="light" :href="route('trails.index')">
                    {{ __('Discover all routes') }}
                    <x-icon name="arrow-right" class="size-4" />
                </x-button>
            @endguest
        </div>
    </div>

    <div class="mx-auto -mt-28 max-w-6xl px-4 sm:px-6 lg:px-8">
        @include('trails._details', ['isPreview' => true])

        @guest
            <x-card class="mt-12 flex flex-col gap-4 bg-white sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ __('Want to see more?') }}</h2>
                    <p class="mt-1 text-ink-muted">{{ __('Sign up to discover all routes, save your favourites and create your own routes.') }}</p>
                </div>
                <x-button variant="accent" :href="route('register')" class="shrink-0">
                    {{ __('Sign up for free') }}
                    <x-icon name="arrow-right" class="size-4" />
                </x-button>
            </x-card>
        @endguest
    </div>
</x-layouts.app>
