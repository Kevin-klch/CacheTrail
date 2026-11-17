@props(['trail'])

{{-- Key figures of a trail as a compact row of icons and values. --}}
<dl {{ $attributes->class(['flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-forest-800']) }}>
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
    @if ($trail->rating_count > 0)
        <div class="inline-flex items-center gap-1.5">
            <dt><x-icon name="star" class="size-4 fill-current text-trail-500" /><span class="sr-only">{{ __('Rating') }}</span></dt>
            <dd class="font-medium">{{ $trail->averageRatingLabel() }} <span class="font-normal text-ink-muted">({{ $trail->rating_count }})</span></dd>
        </div>
    @endif
    <div class="inline-flex items-center gap-1.5">
        <dt class="sr-only">{{ __('Means of transport') }}</dt>
        <dd class="text-forest-700"><x-transport-icons :transportations="$trail->transportations" /></dd>
    </div>
</dl>
