<x-layouts.app :title="__('Discover routes')">
    <x-page-header :title="__('Discover routes')" :eyebrow="__('Route directory')">
        {{ __('Find the geocaching route that fits your day.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('trails.index') }}" role="search" class="grid gap-4 md:grid-cols-[2fr_1fr_1fr_1fr_auto] md:items-end">
                <div>
                    <label for="q" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Search') }}</label>
                    <div class="relative">
                        <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ink-muted" />
                        <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Place, region or route name') }}"
                            class="block w-full rounded-xl border border-sand-300 bg-white py-2.5 pl-11 pr-4 text-ink shadow-xs placeholder:text-ink-muted/60 focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
                    </div>
                </div>

                <x-select name="transportation" :label="__('Means of transport')" :value="$filters['transportation'] ?? ''" :placeholder="__('All')"
                    :options="collect(App\Enums\Transportation::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()" />

                <x-input name="min_hours" type="number" min="0" max="72" step="0.5" :label="__('Duration from (hrs.)')" :value="$filters['min_hours'] ?? ''" />
                <x-input name="max_hours" type="number" min="0" max="72" step="0.5" :label="__('Duration up to (hrs.)')" :value="$filters['max_hours'] ?? ''" />

                <div class="flex gap-2">
                    <x-button class="flex-1 md:flex-none">{{ __('Filter') }}</x-button>
                    @if (array_filter($filters))
                        <x-button variant="ghost" :href="route('trails.index')">{{ __('Reset') }}</x-button>
                    @endif
                </div>
            </form>
        </x-card>

        <div class="mt-8 flex items-baseline justify-between gap-4">
            <p class="text-sm text-ink-muted">{{ trans_choice(':count route found|:count routes found', $trails->total()) }}</p>
            <p class="text-sm text-ink-muted">{{ __('Sorted by: newest') }}</p>
        </div>

        @if ($trails->isEmpty())
            <x-empty-state class="mt-6" icon="search" :title="__('No matching routes')">
                @if (array_filter($filters))
                    {{ __('Try other search terms or remove some filters.') }}
                @else
                    {{ __('Search, filters and the route list will appear here as soon as the first routes are published.') }}
                @endif
            </x-empty-state>
        @else
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($trails as $trail)
                    <x-trail-card :trail="$trail" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $trails->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
