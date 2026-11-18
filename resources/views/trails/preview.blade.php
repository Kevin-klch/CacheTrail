<x-layouts.app :title="__('Preview of :name', ['name' => $trail->name])">
    <div class="slant-bottom relative overflow-hidden bg-forest-700 pb-40 pt-10 text-paper sm:pt-14">
        <x-contour-lines />
        <div class="relative mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Route editor') }}</p>
            <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ __('Preview') }}</h1>
            <p class="mt-3 max-w-2xl text-lg text-forest-100/90">{{ __('This is how other geocachers will see your route. Check everything before publishing.') }}</p>
            <x-editor-steps :current="3" :trail="$trail" />
        </div>
    </div>

    <div class="mx-auto -mt-28 max-w-6xl px-4 sm:px-6 lg:px-8">
        @include('trails._details', ['isPreview' => true])

        <x-card class="mt-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            @if ($trail->isPublished())
                <p class="text-sm text-ink-muted">{{ __('This route is already published.') }}</p>
                <x-button :href="route('trails.show', $trail)">{{ __('Done') }}</x-button>
            @else
                <p class="text-sm text-ink-muted">{{ __('After publishing, all premium users can find your route.') }}</p>
                <div class="flex flex-col-reverse gap-3 sm:flex-row">
                    <x-button variant="secondary" :href="route('trails.caches', $trail)">{{ __('Back to the caches') }}</x-button>
                    <x-button variant="secondary" :href="route('trails.show', $trail)">{{ __('Keep as draft') }}</x-button>
                    <form method="POST" action="{{ route('trails.publish', $trail) }}">
                        @csrf
                        <x-button class="w-full">{{ __('Publish route') }}</x-button>
                    </form>
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
