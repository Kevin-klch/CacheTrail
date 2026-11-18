@props(['badge', 'level', 'size' => 'md'])

{{-- Round medal of a badge. Tiered badges get bronze, silver, gold and then CacheTrail colours per level;
     one-off badges are forest green with a golden ring. --}}
@php
    /** @var \App\Enums\Badge $badge */
    $colours = $badge->isTiered()
        ? [
            'bg-[#b9773f] ring-[#e8c39b]',
            'bg-[#8c9a95] ring-[#d6dedb]',
            'bg-[#c9971c] ring-[#f1d98f]',
            'bg-forest-600 ring-forest-200',
            'bg-trail-500 ring-trail-400/40',
            'bg-forest-900 ring-trail-400',
        ][min($badge->tier($level), 5)]
        : 'bg-forest-700 ring-[#f1d98f]';
    $large = $size === 'lg';
@endphp

<span {{ $attributes->class([
    'relative grid shrink-0 place-items-center rounded-full text-white shadow-md ring-4',
    'size-16' => $large,
    'size-11' => ! $large,
    $colours,
]) }} aria-hidden="true">
    <x-icon :name="$badge->icon()" @class(['size-7' => $large, 'size-5' => ! $large]) />
    @if ($badge->isTiered())
        <span @class([
            'absolute -bottom-2 left-1/2 -translate-x-1/2 rounded-full bg-white px-1.5 font-bold leading-tight text-forest-900 shadow ring-1 ring-sand-200',
            'text-xs' => $large,
            'text-[0.6rem]' => ! $large,
        ])>{{ $level }}</span>
    @endif
</span>
