<x-layouts.app :title="__('My routes')">
    <x-page-header :title="__('My routes')" :eyebrow="__('Your routes')">
        {{ __('Manage your drafts and published routes.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-card>
            @if (session('status') === 'trail-deleted')
                <x-alert class="mb-6">{{ __('The route has been deleted.') }}</x-alert>
            @endif

            <div class="flex flex-wrap items-center justify-between gap-4">
                <p class="text-sm text-ink-muted">{{ trans_choice(':count route|:count routes', $trails->total()) }}</p>
                <x-button :href="route('trails.create')">
                    <x-icon name="plus" class="size-4" />
                    {{ __('Create route') }}
                </x-button>
            </div>

            @if ($trails->isEmpty())
                <x-empty-state class="mt-6" :title="__('No routes yet')">
                    {{ __('As soon as you create routes, you will find them here.') }}
                </x-empty-state>
            @else
                <ul class="mt-6 divide-y divide-sand-200">
                    @foreach ($trails as $trail)
                        <li class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center">
                            <x-trail-image :trail="$trail" class="aspect-[16/9] w-full shrink-0 rounded-xl sm:w-36" />
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('trails.show', $trail) }}" class="font-serif text-xl font-bold text-forest-900 hover:underline">{{ $trail->name }}</a>
                                    <x-visibility-badge :visibility="$trail->visibility" />
                                </div>
                                <p class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-ink-muted">
                                    <span class="inline-flex items-center gap-1.5"><x-icon name="route" class="size-4" /> {{ trans_choice(':count cache|:count caches', $trail->caches_count) }}</span>
                                    <span class="inline-flex items-center gap-1.5"><x-icon name="clock" class="size-4" /> {{ $trail->durationLabel() }}</span>
                                    <x-transport-icons :transportations="$trail->transportations" />
                                    <span>{{ __('Last updated on :date', ['date' => $trail->updated_at->translatedFormat('j. F Y')]) }}</span>
                                </p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <x-button variant="secondary" :href="route('trails.caches', $trail)">
                                    <x-icon name="route" class="size-4" />
                                    {{ __('Caches') }}
                                </x-button>
                                <x-button variant="secondary" :href="route('trails.edit', $trail)">
                                    <x-icon name="edit" class="size-4" />
                                    {{ __('Edit') }}
                                </x-button>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-6">
                    {{ $trails->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-layouts.app>
