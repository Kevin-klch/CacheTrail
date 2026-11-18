<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    aria-pressed="{{ $liked ? 'true' : 'false' }}"
    title="{{ $liked ? __('Unlike') : __('Like') }}"
    @class([
        'relative z-10 inline-flex items-center justify-center gap-2 rounded-full font-semibold shadow transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-forest-700 disabled:opacity-70',
        'px-5 py-2.5 text-sm' => $large,
        'size-10' => ! $large,
        'bg-trail-500 text-white hover:bg-trail-600' => $liked,
        'bg-paper/95 text-forest-800 hover:bg-white' => ! $liked && ! $large,
        'bg-white text-forest-800 ring-1 ring-forest-700/20 hover:bg-forest-50 hover:ring-forest-700/40' => ! $liked && $large,
    ])
>
    <x-icon name="heart" @class(['size-5', 'fill-current' => $liked]) />
    @if ($large)
        <span>{{ $liked ? __('You like this') : __('Like') }}</span>
        <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $trail->like_count }}</span>
    @else
        <span class="sr-only">{{ $liked ? __('Unlike') : __('Like') }}</span>
    @endif
</button>
