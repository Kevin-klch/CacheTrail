@props(['visibility'])

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold',
    'bg-forest-100 text-forest-800' => $visibility === App\Enums\TrailVisibility::Published,
    'bg-sand-200 text-ink' => $visibility === App\Enums\TrailVisibility::Draft,
    'bg-trail-400/15 text-trail-600' => $visibility === App\Enums\TrailVisibility::Hidden,
]) }}>
    <span @class([
        'size-1.5 rounded-full',
        'bg-forest-600' => $visibility === App\Enums\TrailVisibility::Published,
        'bg-ink-muted' => $visibility === App\Enums\TrailVisibility::Draft,
        'bg-trail-500' => $visibility === App\Enums\TrailVisibility::Hidden,
    ])></span>
    {{ $visibility->label() }}
</span>
