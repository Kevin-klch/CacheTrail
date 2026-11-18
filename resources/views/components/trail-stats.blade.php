@props(['trail', 'size' => 'compact'])

{{-- Key figures of a trail as small tiles: icon and label on top, value below. Icons come from x-icon,
     so custom attribute icons can be swapped in there in one place. Compact cards use short labels. --}}
@php
    $format = fn (?float $value): string => $value === null ? '–' : App\Support\Format::number($value, 1);
    $compact = $size === 'compact';

    $tiles = [
        ['icon' => 'clock', 'label' => __('Duration'), 'short' => __('Duration'), 'value' => $trail->durationLabel()],
        ['icon' => 'route', 'label' => __('Caches'), 'short' => __('Caches'), 'value' => (string) $trail->cache_count],
        ['icon' => 'map', 'label' => __('Length'), 'short' => __('Length'), 'value' => $trail->distanceLabel() ?? '–'],
        ['icon' => 'difficulty', 'label' => __('Difficulty'), 'short' => __('D rating'), 'value' => $format($trail->average_difficulty)],
        ['icon' => 'terrain', 'label' => __('Terrain'), 'short' => __('T rating'), 'value' => $format($trail->average_terrain)],
    ];
@endphp

<dl {{ $attributes->class(['grid gap-2', 'grid-cols-3' => $compact, 'grid-cols-3 sm:grid-cols-6' => ! $compact]) }}>
    @foreach ($tiles as $tile)
        <div class="min-w-0 rounded-xl bg-sand-100/70 px-2.5 py-2" title="{{ $tile['label'] }}">
            <dt class="flex items-center gap-1 text-[0.7rem] font-medium uppercase tracking-wide text-ink-muted">
                <x-icon :name="$tile['icon']" class="size-3.5 shrink-0 text-forest-500" />
                <span class="truncate" aria-hidden="true">{{ $compact ? $tile['short'] : $tile['label'] }}</span>
                <span class="sr-only">{{ $tile['label'] }}</span>
            </dt>
            <dd @class(['mt-0.5 truncate font-semibold text-forest-900', 'text-sm' => $compact, 'text-base' => ! $compact])>{{ $tile['value'] }}</dd>
        </div>
    @endforeach
    <div class="min-w-0 rounded-xl bg-sand-100/70 px-2.5 py-2" title="{{ __('Means of transport') }}">
        <dt class="truncate text-[0.7rem] font-medium uppercase tracking-wide text-ink-muted">
            <span aria-hidden="true">{{ $compact ? __('On the way') : __('Transport') }}</span>
            <span class="sr-only">{{ __('Means of transport') }}</span>
        </dt>
        <dd class="mt-1 text-forest-800"><x-transport-icons :transportations="$trail->transportations" /></dd>
    </div>
</dl>
