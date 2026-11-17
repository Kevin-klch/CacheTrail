<x-layouts.app :title="$trail->name">
    <div class="rounded-b-[2.5rem] bg-forest-700 pb-40 pt-8 sm:rounded-b-[4rem]">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('trails.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-forest-100 hover:text-white">
                <x-icon name="arrow-right" class="size-4 rotate-180" />
                {{ __('All routes') }}
            </a>
        </div>
    </div>

    <div class="mx-auto -mt-36 max-w-6xl px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <x-alert class="mb-6">
                {{ match (session('status')) {
                    'trail-published' => __('Your route has been published.'),
                    'trail-saved-as-draft' => __('Your route has been saved as a draft. Only you can see it.'),
                    'trail-updated' => __('Your changes have been saved.'),
                    default => '',
                } }}
            </x-alert>
        @endif

        <x-trail-image :trail="$trail" class="aspect-[21/9] w-full rounded-[2rem] shadow-2xl shadow-forest-950/20" />

        <div class="mt-10 grid gap-10 lg:grid-cols-[1fr_22rem]">
            <article>
                <div class="flex flex-wrap items-center gap-3">
                    @unless ($trail->isPublished())
                        <x-visibility-badge :visibility="$trail->visibility" />
                    @endunless
                </div>
                <h1 class="mt-2 text-4xl font-bold leading-tight sm:text-5xl">{{ $trail->name }}</h1>
                <div class="mt-3 h-1 w-40 rounded-full bg-gradient-to-r from-trail-500 to-transparent"></div>
                <p class="mt-4 text-ink-muted">{{ __('Created by :name', ['name' => $trail->user->name]) }}</p>

                <div class="mt-8 space-y-4 text-lg leading-relaxed text-ink">
                    @foreach ($trail->descriptionParagraphs() as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>

                <section class="mt-14">
                    <h2 class="text-3xl font-bold">{{ __('Caches on this route') }}</h2>
                    <x-empty-state class="mt-5" :title="__('Caches follow soon')">
                        {{ __('The list of caches, in the order recommended by the creator, and the map will appear here in the next development step.') }}
                    </x-empty-state>
                </section>
            </article>

            <aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                <x-card class="p-5 sm:p-6">
                    <h2 class="sr-only">{{ __('Route details') }}</h2>
                    <dl>
                        <x-stat :label="__('Duration')" icon="clock">{{ $trail->durationLabel() }}</x-stat>
                        <x-stat :label="__('Transport')" :icon="$trail->transportations->first()?->icon() ?? 'route'">
                            {{ $trail->transportationLabel() }}
                        </x-stat>
                        <x-stat :label="__('Caches')" icon="route" pending />
                        <x-stat :label="__('Length')" icon="map" pending />
                        <x-stat :label="__('Difficulty')" icon="compass" pending />
                        <x-stat :label="__('Terrain')" icon="map" pending />
                    </dl>
                    <p class="mt-4 border-t border-sand-200 pt-4 text-xs text-ink-muted">
                        @if ($trail->published_at)
                            {{ __('Published on :date', ['date' => $trail->published_at->translatedFormat('j. F Y')]) }}<br>
                        @endif
                        {{ __('Last updated on :date', ['date' => $trail->updated_at->translatedFormat('j. F Y')]) }}
                    </p>
                </x-card>

                @can('update', $trail)
                    <x-button variant="secondary" :href="route('trails.edit', $trail)" class="w-full">
                        <x-icon name="edit" class="size-4" />
                        {{ __('Edit route') }}
                    </x-button>
                @endcan
            </aside>
        </div>
    </div>
</x-layouts.app>
