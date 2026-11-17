@use('App\Enums\TrailSort')
@use('App\Enums\Transportation')
@use('App\Http\Requests\TrailIndexRequest')

@php
    $advancedFilters = ['min_hours', 'max_hours', 'min_caches', 'max_caches', 'min_km', 'max_km', 'min_difficulty', 'max_difficulty', 'min_terrain', 'max_terrain', 'radius'];
    $hasAdvancedFilters = collect($advancedFilters)->contains(fn ($key) => isset($filters[$key]));
    $viewQuery = $view === 'compact' ? [] : ['view' => $view];
@endphp

<x-layouts.app :title="__('Discover routes')">
    <x-page-header :title="__('Discover routes')" :eyebrow="__('Route directory')">
        {{ __('Find the geocaching route that fits your day.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-card class="p-4 sm:p-5">
            <form method="GET" action="{{ route('trails.index') }}" role="search">
                @if ($view !== 'compact')
                    <input type="hidden" name="view" value="{{ $view }}">
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-[2fr_1fr_1fr_auto] md:items-end">
                    <div>
                        <label for="q" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Search') }}</label>
                        <div class="relative">
                            <x-icon name="search" class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-ink-muted" />
                            <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Place, region or route name') }}"
                                class="block w-full rounded-xl border border-sand-300 bg-white py-2.5 pl-11 pr-4 text-ink shadow-xs placeholder:text-ink-muted/60 focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
                        </div>
                    </div>

                    <x-select name="transportation" :label="__('Means of transport')" :value="$filters['transportation'] ?? ''" :placeholder="__('All')"
                        :options="collect(Transportation::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()" />

                    <x-select name="sort" :label="__('Sort by')" :value="$sort->value"
                        :options="collect(TrailSort::cases())
                            ->reject(fn ($case) => $case === TrailSort::Nearest && ! $location)
                            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()" />

                    <div class="flex gap-2">
                        <x-button class="flex-1 md:flex-none">{{ __('Filter') }}</x-button>
                        @if ($filters !== [])
                            <x-button variant="ghost" :href="route('trails.index', $viewQuery)">{{ __('Reset') }}</x-button>
                        @endif
                    </div>
                </div>

                <details class="group mt-4 border-t border-sand-200 pt-3" @if ($hasAdvancedFilters || $errors->any()) open @endif>
                    <summary class="inline-flex cursor-pointer list-none items-center gap-1.5 rounded-full px-2 py-1 text-sm font-semibold text-forest-700 hover:bg-forest-50 [&::-webkit-details-marker]:hidden">
                        <x-icon name="chevron-down" class="size-4 transition group-open:rotate-180" />
                        {{ __('More filters') }}
                        @if ($hasAdvancedFilters)
                            <span class="rounded-full bg-forest-700 px-2 py-0.5 text-xs text-paper">{{ collect($advancedFilters)->filter(fn ($key) => isset($filters[$key]))->count() }}</span>
                        @endif
                    </summary>

                    <div class="mt-4 grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
                        <x-range-filter :label="__('Duration')" name="hours" :values="$filters" :max="72" step="0.5" :unit="__('hrs.')" />
                        <x-range-filter :label="__('Number of caches')" name="caches" :values="$filters" :max="100" />
                        <x-range-filter :label="__('Length')" name="km" :values="$filters" :max="1000" step="0.5" unit="km" />
                        <x-range-filter :label="__('Difficulty')" name="difficulty" :values="$filters" :min="1" :max="5" step="0.5" />
                        <x-range-filter :label="__('Terrain')" name="terrain" :values="$filters" :min="1" :max="5" step="0.5" />

                        <div>
                            @if ($location)
                                <x-select name="radius" :label="__('Maximum distance to the start')" :value="$filters['radius'] ?? ''" :placeholder="__('Any distance')"
                                    :options="collect(TrailIndexRequest::RADIUS_OPTIONS_KM)->mapWithKeys(fn ($km) => [$km => __(':count km', ['count' => $km])])->all()" />
                            @else
                                <p class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Maximum distance to the start') }}</p>
                                <p class="rounded-xl border border-dashed border-sand-300 px-3 py-2 text-sm text-ink-muted">{{ __('Share your location below to filter by distance.') }}</p>
                            @endif
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-ink-muted">{{ __('Length is measured as the crow flies between the stations.') }}</p>
                </details>
            </form>

            {{-- Location: only kept in the session, rounded to about 1 km --}}
            <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-sand-200 pt-4 text-sm">
                @if ($location)
                    <span class="inline-flex items-center gap-1.5 font-medium text-forest-800">
                        <x-icon name="compass" class="size-4 text-forest-600" />
                        {{ __('Your approximate location is used for distances.') }}
                    </span>
                    <form method="POST" action="{{ route('location.destroy') }}">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="return_query" value="{{ http_build_query(request()->query()) }}">
                        <button type="submit" class="font-semibold text-forest-700 underline-offset-4 hover:underline">{{ __('Remove location') }}</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('location.store') }}" class="flex flex-wrap items-center gap-3"
                        x-data="shareLocation(@js(['denied' => __('Location access was denied. You can allow it in your browser settings.'), 'unavailable' => __('Your location could not be determined.')]))">
                        @csrf
                        <input type="hidden" name="latitude" x-ref="latitude">
                        <input type="hidden" name="longitude" x-ref="longitude">
                        <input type="hidden" name="return_query" value="{{ http_build_query(request()->query()) }}">
                        <x-button type="button" variant="secondary" x-on:click="locate" x-bind:disabled="busy">
                            <x-icon name="compass" class="size-4" />
                            <span x-show="! busy">{{ __('Use my location') }}</span>
                            <span x-show="busy" x-cloak>{{ __('Determining location …') }}</span>
                        </x-button>
                        <span class="text-ink-muted" x-show="! error">{{ __('For distances and nearby routes. Your location is not stored permanently.') }}</span>
                        <span class="font-medium text-red-700" x-show="error" x-text="error" x-cloak role="alert"></span>
                    </form>
                @endif
                @error('latitude')
                    <span class="font-medium text-red-700">{{ __('Your location could not be determined.') }}</span>
                @enderror
            </div>
        </x-card>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm text-ink-muted">
                @if ($view === 'map')
                    {{ trans_choice(':count route on the map|:count routes on the map', $trails->count()) }}
                @else
                    {{ trans_choice(':count route found|:count routes found', $trails->total()) }}
                @endif
                · {{ __('Sorted by: :sort', ['sort' => $sort->label()]) }}
            </p>

            <div class="inline-flex rounded-full border border-sand-300 bg-white p-1 text-sm font-medium" role="group" aria-label="{{ __('View') }}">
                @foreach (['compact' => [__('Compact'), 'grid'], 'detailed' => [__('Detailed'), 'list'], 'map' => [__('Map'), 'map']] as $option => [$label, $icon])
                    <a href="{{ request()->fullUrlWithQuery(['view' => $option === 'compact' ? null : $option, 'page' => null]) }}"
                        @if ($view === $option) aria-current="true" @endif
                        @class([
                            'inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 transition',
                            'bg-forest-700 text-paper' => $view === $option,
                            'text-forest-800 hover:bg-forest-50' => $view !== $option,
                        ])>
                        <x-icon :name="$icon" class="size-4" />
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($trails->isEmpty())
            <x-empty-state class="mt-6" icon="search" :title="__('No matching routes')">
                @if ($filters !== [])
                    {{ __('Try other search terms or remove some filters.') }}
                @else
                    {{ __('Search, filters and the route list will appear here as soon as the first routes are published.') }}
                @endif
            </x-empty-state>
        @elseif ($view === 'map')
            <x-directory-map :markers="$mapMarkers" :location="$location" class="mt-6 h-[34rem] w-full" />
            @if ($trails->count() >= App\Http\Controllers\TrailController::MAP_LIMIT)
                <p class="mt-3 text-sm text-ink-muted">{{ __('Only the first :count routes are shown. Narrow down your search to see more.', ['count' => App\Http\Controllers\TrailController::MAP_LIMIT]) }}</p>
            @endif
        @elseif ($view === 'detailed')
            <div class="mx-auto mt-6 max-w-5xl space-y-8">
                @foreach ($trails as $trail)
                    <x-trail-card-detailed :trail="$trail" :location="$location" />
                @endforeach
            </div>
            <div class="mx-auto mt-10 max-w-5xl">{{ $trails->links() }}</div>
        @else
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($trails as $trail)
                    <x-trail-card :trail="$trail" :location="$location" />
                @endforeach
            </div>
            <div class="mt-10">{{ $trails->links() }}</div>
        @endif
    </div>
</x-layouts.app>
