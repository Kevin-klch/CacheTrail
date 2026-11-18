@props(['inverted' => false])

<span {{ $attributes->class(['inline-flex items-center gap-2.5']) }}>
    <svg viewBox="0 0 40 40" class="size-9 shrink-0" aria-hidden="true">
        <circle cx="20" cy="20" r="17" fill="none" stroke="currentColor" stroke-width="2.5" @class(['text-paper' => $inverted, 'text-forest-700' => ! $inverted]) />
        <circle cx="20" cy="20" r="11.5" fill="none" stroke="currentColor" stroke-width="1" stroke-dasharray="2 3" class="text-forest-300" />
        <path d="M20 6.5 24 20h-8Z" class="fill-trail-500" />
        <path d="M20 33.5 16 20h8Z" @class(['fill-paper' => $inverted, 'fill-forest-700' => ! $inverted]) />
        <circle cx="20" cy="20" r="2" @class(['fill-forest-800' => $inverted, 'fill-paper' => ! $inverted]) />
    </svg>
    <span @class([
        'font-heading text-2xl font-bold tracking-tight',
        'text-paper' => $inverted,
        'text-forest-800' => ! $inverted,
    ])>Cache<span @class(['text-trail-400' => $inverted, 'text-trail-500' => ! $inverted])>Trail</span></span>
</span>
