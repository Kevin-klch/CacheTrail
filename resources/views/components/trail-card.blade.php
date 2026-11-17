@props(['trail'])

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl border border-sand-200 bg-white shadow-[0_12px_32px_-18px_rgb(26_50_38/0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-18px_rgb(26_50_38/0.35)]']) }}>
    <x-trail-image :trail="$trail" class="aspect-[16/9] w-full" />

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-2xl font-bold leading-tight">
            <a href="{{ route('trails.show', $trail) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                {{ $trail->name }}
            </a>
        </h3>
        <p class="mt-1 text-sm text-ink-muted">{{ __('by :name', ['name' => $trail->user->name]) }}</p>

        <dl class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-forest-800">
            <div class="inline-flex items-center gap-1.5">
                <dt><x-icon name="clock" class="size-4 text-forest-500" /><span class="sr-only">{{ __('Estimated duration') }}</span></dt>
                <dd class="font-medium">{{ $trail->durationLabel() }}</dd>
            </div>
            <div class="inline-flex items-center gap-1.5">
                <dt><x-icon name="route" class="size-4 text-forest-500" /><span class="sr-only">{{ __('Caches') }}</span></dt>
                <dd class="font-medium">{{ trans_choice(':count cache|:count caches', $trail->cache_count) }}</dd>
            </div>
            @if ($trail->distanceLabel())
                <div class="inline-flex items-center gap-1.5">
                    <dt><x-icon name="map" class="size-4 text-forest-500" /><span class="sr-only">{{ __('Length (straight line)') }}</span></dt>
                    <dd class="font-medium">{{ $trail->distanceLabel() }}</dd>
                </div>
            @endif
            @if ($trail->average_difficulty !== null)
                <div class="inline-flex items-center gap-1.5">
                    <dt class="font-semibold text-forest-500" title="{{ __('Difficulty') }} / {{ __('Terrain') }}">D/T</dt>
                    <dd class="font-medium">{{ number_format($trail->average_difficulty, 1, ',', '') }} / {{ number_format($trail->average_terrain, 1, ',', '') }}</dd>
                </div>
            @endif
            <div class="inline-flex items-center gap-1.5">
                <dt class="sr-only">{{ __('Means of transport') }}</dt>
                <dd class="text-forest-700"><x-transport-icons :transportations="$trail->transportations" /></dd>
            </div>
        </dl>

        <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-ink-muted">{{ $trail->description }}</p>
    </div>
</article>
