{{-- Media, description, caches and key figures of a trail; shared by the detail page and the preview step. --}}
        @if ($trail->imageUrl() && $trail->caches->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-[3fr_2fr]">
                <x-trail-image :trail="$trail" class="aspect-[16/10] w-full rounded-[2rem] shadow-2xl shadow-forest-950/20" />
                <x-trail-map :markers="$trail->mapMarkers()" class="aspect-[16/10] w-full lg:aspect-auto" />
            </div>
        @elseif ($trail->caches->isNotEmpty())
            <x-trail-map :markers="$trail->mapMarkers()" class="aspect-[4/3] w-full sm:aspect-[21/9]" />
        @else
            <x-trail-image :trail="$trail" class="aspect-[21/9] w-full rounded-[2rem] shadow-2xl shadow-forest-950/20" />
        @endif

        <div class="mt-10 grid grid-cols-1 gap-10 lg:grid-cols-[1fr_22rem]">
            <article>
                <div class="flex flex-wrap items-center gap-3">
                    @unless ($trail->isPublished())
                        <x-visibility-badge :visibility="$trail->visibility" />
                    @endunless
                </div>
                <h1 class="mt-2 text-4xl font-bold leading-tight sm:text-5xl">{{ $trail->name }}</h1>
                <div class="mt-3 h-1 w-40 rounded-full bg-gradient-to-r from-trail-500 to-transparent"></div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                    <p class="text-ink-muted">
                        {{ __('Created by') }}
                        @auth
                            <a href="{{ $trail->user->profileUrl() }}" class="font-semibold text-forest-700 hover:underline">{{ $trail->user->name }}</a>
                            <x-role-badge :user="$trail->user" />
                        @else
                            <span class="font-semibold text-forest-700">{{ $trail->user->name }}</span>
                            <x-role-badge :user="$trail->user" />
                        @endauth
                    </p>
                    @if (! ($isPreview ?? false) && $trail->isPublished())
                        <div class="flex flex-wrap items-center gap-4">
                            <livewire:report-button :reportable="$trail" />
                            <livewire:like-button :trail="$trail" :large="true" />
                            <livewire:bookmark-button :trail="$trail" :large="true" />
                        </div>
                    @endif
                </div>

                <div class="mt-8 space-y-4 break-words text-lg leading-relaxed text-ink">
                    @foreach ($trail->descriptionParagraphs() as $paragraph)
                        <p>{!! nl2br(e($paragraph)) !!}</p>
                    @endforeach
                </div>

                <section class="mt-14">
                    <div class="flex flex-wrap items-baseline justify-between gap-3">
                        <h2 class="text-3xl font-bold">{{ __('Caches on this route') }}</h2>
                        @if (! ($isPreview ?? false) && auth()->user()?->can('update', $trail))
                            <a href="{{ route('trails.caches', $trail) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                                <x-icon name="edit" class="size-4" />
                                {{ __('Edit caches') }}
                            </a>
                        @endif
                    </div>

                    @if ($trail->caches->isEmpty())
                        <x-empty-state class="mt-5" :title="__('No caches yet')">
                            {{ __('This route does not contain any caches yet.') }}
                        </x-empty-state>
                    @else
                        <p class="mt-2 text-ink-muted">{{ __('Visit the stations in the order recommended by the creator.') }}</p>
                        <ol class="relative mt-6 space-y-3">
                            <span class="absolute bottom-6 left-[1.1rem] top-6 border-l-2 border-dashed border-trail-400/70" aria-hidden="true"></span>
                            @foreach ($trail->caches as $cache)
                                <li @class([
                                    'relative flex items-start gap-4 rounded-2xl border bg-white p-4',
                                    'border-sand-200' => $cache->cache_status === App\Enums\CacheStatus::Active,
                                    'border-amber-200 bg-amber-50/40' => $cache->cache_status === App\Enums\CacheStatus::TemporarilyDisabled,
                                    'border-red-200 bg-red-50/40' => $cache->cache_status === App\Enums\CacheStatus::Archived,
                                    'border-sand-300' => $cache->cache_status === App\Enums\CacheStatus::Unknown,
                                ])>
                                    <span @class([
                                        'relative -ml-[0.15rem] grid size-9 shrink-0 place-items-center rounded-full text-sm font-bold ring-4 ring-paper',
                                        'bg-forest-700 text-paper' => $cache->cache_status !== App\Enums\CacheStatus::Archived,
                                        'bg-sand-300 text-ink-muted' => $cache->cache_status === App\Enums\CacheStatus::Archived,
                                    ])>{{ $loop->iteration }}</span>
                                    <x-cache-details :cache="$cache" :url="$geocacheProvider->cacheUrl($cache->cache_code)" class="flex-1" />
                                </li>
                            @endforeach
                        </ol>
                        @if ($usesExampleData)
                            <p class="mt-4 text-xs text-ink-muted">{{ __('Example data: the caches currently come from a test data set, not from Geocaching.com.') }}</p>
                        @endif
                    @endif
                </section>

                @if (! ($isPreview ?? false) && $trail->isPublished())
                    <livewire:trail-ratings :trail="$trail" />
                @endif
            </article>

            <aside class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                @if (! ($isPreview ?? false) && $trail->isPublished())
                    <livewire:complete-button :trail="$trail" />
                @endif

                <x-card class="p-5 sm:p-6">
                    <h2 class="sr-only">{{ __('Route details') }}</h2>
                    <dl>
                        <x-stat :label="__('Duration')" icon="clock">{{ $trail->durationLabel() }}</x-stat>
                        <x-stat :label="__('Transport')" :icon="$trail->transportations->first()?->icon() ?? 'route'">
                            {{ $trail->transportationLabel() }}
                        </x-stat>
                        <x-stat :label="__('Caches')" icon="route">{{ $trail->cache_count }}</x-stat>
                        <x-stat :label="__('Rating')" icon="star">
                            @if ($trail->rating_count > 0)
                                {{ $trail->averageRatingLabel() }} / 5
                                <span class="block text-xs font-normal text-ink-muted">{{ trans_choice('(:count rating)|(:count ratings)', $trail->rating_count) }}</span>
                            @else
                                –
                            @endif
                        </x-stat>
                        <x-stat :label="__('Length')" icon="map">
                            @if ($trail->distanceLabel())
                                {{ $trail->distanceLabel() }}
                                <span class="block text-xs font-normal text-ink-muted">{{ __('as the crow flies between the stations') }}</span>
                            @else
                                –
                            @endif
                        </x-stat>
                        <x-stat :label="__('Difficulty')" icon="compass">{{ App\Models\Trail::ratingAverageLabel($trail->average_difficulty) ?? '–' }}</x-stat>
                        <x-stat :label="__('Terrain')" icon="map">{{ App\Models\Trail::ratingAverageLabel($trail->average_terrain) ?? '–' }}</x-stat>
                    </dl>
                    <p class="border-t border-sand-200 pt-4 text-xs text-ink-muted">
                        @if ($trail->published_at)
                            {{ __('Published on :date', ['date' => $trail->published_at->translatedFormat('j. F Y')]) }}<br>
                        @endif
                        {{ __('Last updated on :date', ['date' => $trail->updated_at->translatedFormat('j. F Y')]) }}
                    </p>
                </x-card>

                <x-card class="p-5 sm:p-6">
                    <h2 class="flex items-center gap-2 text-xl font-bold">
                        <x-icon name="tool" class="text-forest-600" />
                        {{ __('Required equipment') }}
                    </h2>
                    @if ($equipment = $trail->equipmentLabels())
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($equipment as $item)
                                <li class="rounded-full bg-forest-50 px-3 py-1 text-sm font-medium text-forest-800 ring-1 ring-forest-600/15">{{ $item }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-2 text-sm text-ink-muted">{{ __('No special equipment needed.') }}</p>
                    @endif
                </x-card>

                @if (! ($isPreview ?? false) && auth()->user()?->can('update', $trail))
                    <x-button variant="secondary" :href="route('trails.edit', $trail)" class="w-full">
                        <x-icon name="edit" class="size-4" />
                        {{ __('Edit route') }}
                    </x-button>
                @endif
            </aside>
        </div>
