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
        <x-card class="p-4 sm:p-6" x-data="{ advanced: @js($hasAdvancedFilters || $errors->any()) }">
            {{-- Search bar; all other filter fields belong to this form via the "form" attribute --}}
            <form id="directory-filter" method="GET" action="{{ route('trails.index') }}" role="search">
                @if ($view !== 'compact')
                    <input type="hidden" name="view" value="{{ $view }}">
                @endif

                <div class="flex items-center gap-2 rounded-full border border-sand-300 bg-paper/60 p-1.5 pl-5 transition focus-within:border-forest-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-forest-500/15">
                    <x-icon name="search" class="text-ink-muted" />
                    <label for="q" class="sr-only">{{ __('Search') }}</label>
                    <input id="q" name="q" type="search" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('Place, region or route name') }}"
                        class="min-w-0 flex-1 bg-transparent py-2 text-ink placeholder:text-ink-muted/70 focus:outline-none">
                    @if ($filters !== [])
                        <a href="{{ route('trails.index', $viewQuery) }}" class="hidden shrink-0 rounded-full px-3 py-2 text-sm font-medium text-ink-muted hover:text-forest-800 sm:inline">{{ __('Reset') }}</a>
                    @endif
                    <x-button variant="accent" class="shrink-0 px-6">{{ __('Search') }}</x-button>
                </div>
            </form>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                {{-- Means of transport as pills; changing them applies the filter immediately --}}
                @php
                    $transportOptions = ['' => [__('All'), null]];
                    foreach (Transportation::cases() as $case) {
                        $transportOptions[$case->value] = [$case->label(), $case->icon()];
                    }
                @endphp
                <div class="inline-flex rounded-full bg-sand-100 p-1" role="radiogroup" aria-label="{{ __('Means of transport') }}">
                    @foreach ($transportOptions as $value => [$label, $icon])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="transportation" value="{{ $value }}" form="directory-filter" class="peer sr-only"
                                @checked(($filters['transportation'] ?? '') === (string) $value) x-on:change="$el.form.requestSubmit()">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-sm font-medium text-forest-800 transition hover:bg-white/70 peer-checked:bg-forest-700 peer-checked:text-paper peer-focus-visible:ring-2 peer-focus-visible:ring-forest-500">
                                @if ($icon)
                                    <x-icon :name="$icon" class="size-4" />
                                @endif
                                {{ $label }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <button type="button" x-on:click="advanced = ! advanced" x-bind:aria-expanded="advanced.toString()" aria-controls="advanced-filters"
                    class="inline-flex items-center gap-2 rounded-full border border-sand-300 px-4 py-2 text-sm font-semibold text-forest-800 transition hover:border-forest-500"
                    x-bind:class="advanced && 'border-forest-700 bg-forest-50'">
                    <x-icon name="sliders" class="size-4" />
                    {{ __('More filters') }}
                    @if ($hasAdvancedFilters)
                        <span class="rounded-full bg-forest-700 px-2 py-0.5 text-xs text-paper">{{ collect($advancedFilters)->filter(fn ($key) => isset($filters[$key]))->count() }}</span>
                    @endif
                </button>

                {{-- Location: only kept in the session, rounded to about 1 km --}}
                @if ($location)
                    <form method="POST" action="{{ route('location.destroy') }}" class="inline-flex">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" name="return_query" value="{{ http_build_query(request()->query()) }}">
                        <span class="inline-flex items-center gap-2 rounded-full bg-forest-100 py-1 pl-4 pr-1 text-sm text-forest-900">
                            <x-icon name="compass" class="size-4 text-forest-600" />
                            <span>{{ __('Your location:') }} <strong class="font-semibold">{{ $location->label() }}</strong></span>
                            <button type="submit" class="grid size-7 place-items-center rounded-full text-forest-700 hover:bg-forest-200" title="{{ __('Remove location') }}">
                                <x-icon name="close" class="size-4" />
                                <span class="sr-only">{{ __('Remove location') }}</span>
                            </button>
                        </span>
                    </form>
                @else
                    <form method="POST" action="{{ route('location.store') }}" class="inline-flex"
                        x-data="shareLocation(@js(['denied' => __('Location access was denied. You can allow it in your browser settings.'), 'unavailable' => __('Your location could not be determined.')]))">
                        @csrf
                        <input type="hidden" name="latitude">
                        <input type="hidden" name="longitude">
                        <input type="hidden" name="return_query" value="{{ http_build_query(request()->query()) }}">
                        <button type="button" x-on:click="locate" x-bind:disabled="busy" title="{{ __('For distances and nearby routes. Your location is not stored permanently.') }}"
                            class="inline-flex items-center gap-2 rounded-full border border-sand-300 px-4 py-2 text-sm font-semibold text-forest-800 transition hover:border-forest-500 disabled:opacity-60">
                            <x-icon name="compass" class="size-4" />
                            <span x-show="! busy">{{ __('Use my location') }}</span>
                            <span x-show="busy" x-cloak>{{ __('Determining location …') }}</span>
                        </button>
                        <span class="ml-3 self-center text-sm font-medium text-red-700" x-show="error" x-text="error" x-cloak role="alert"></span>
                    </form>
                @endif

                <div class="ml-auto flex items-center gap-2">
                    <label for="sort" class="text-sm text-ink-muted">{{ __('Sort by') }}</label>
                    <select id="sort" name="sort" form="directory-filter" x-on:change="$el.form.requestSubmit()"
                        class="rounded-full border border-sand-300 bg-white py-2 pl-4 pr-9 text-sm font-semibold text-forest-800 focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
                        @foreach (TrailSort::cases() as $option)
                            @continue($option === TrailSort::Nearest && ! $location)
                            <option value="{{ $option->value }}" @selected($sort === $option)>{{ $option->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            @error('latitude')
                <p class="mt-3 text-sm font-medium text-red-700">{{ __('Your location could not be determined.') }}</p>
            @enderror

            {{-- More filters --}}
            <div id="advanced-filters" x-show="advanced" x-cloak x-transition.opacity class="mt-4 rounded-2xl bg-sand-100/70 p-4 sm:p-5">
                <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 lg:grid-cols-3">
                    <x-range-filter :label="__('Duration')" name="hours" form="directory-filter" :values="$filters" :max="72" step="0.5" :unit="__('hrs.')" />
                    <x-range-filter :label="__('Number of caches')" name="caches" form="directory-filter" :values="$filters" :max="100" />
                    <x-range-filter :label="__('Length')" name="km" form="directory-filter" :values="$filters" :max="1000" step="0.5" unit="km" />
                    <x-range-filter :label="__('Difficulty')" name="difficulty" form="directory-filter" :values="$filters" :min="1" :max="5" step="0.5" />
                    <x-range-filter :label="__('Terrain')" name="terrain" form="directory-filter" :values="$filters" :min="1" :max="5" step="0.5" />

                    <div>
                        @if ($location)
                            <x-select name="radius" form="directory-filter" :label="__('Maximum distance to the start')" :value="$filters['radius'] ?? ''" :placeholder="__('Any distance')"
                                :options="collect(TrailIndexRequest::RADIUS_OPTIONS_KM)->mapWithKeys(fn ($km) => [$km => __(':count km', ['count' => $km])])->all()" />
                        @else
                            <p class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Maximum distance to the start') }}</p>
                            <p class="rounded-xl border border-dashed border-sand-300 bg-white/60 px-3 py-2 text-sm text-ink-muted">{{ __('Use your location to filter by distance.') }}</p>
                        @endif
                    </div>
                </div>
                <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-ink-muted">{{ __('Length is measured as the crow flies between the stations.') }}</p>
                    <x-button form="directory-filter">{{ __('Apply filters') }}</x-button>
                </div>
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
