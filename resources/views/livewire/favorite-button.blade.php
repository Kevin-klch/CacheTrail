<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    aria-pressed="{{ $favorited ? 'true' : 'false' }}"
    title="{{ $favorited ? __('Remove from favorites') : __('Save as favorite') }}"
    @class([
        'relative z-10 inline-flex items-center justify-center gap-2 rounded-full font-semibold shadow transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-forest-700 disabled:opacity-70',
        'px-5 py-2.5 text-sm' => $large,
        'size-10' => ! $large,
        'bg-trail-500 text-white hover:bg-trail-600' => $favorited,
        'bg-paper/95 text-forest-800 hover:bg-white' => ! $favorited,
    ])
>
    <x-icon name="heart" @class(['size-5', 'fill-current' => $favorited]) />
    @if ($large)
        <span>{{ $favorited ? __('Saved as favorite') : __('Save as favorite') }}</span>
        <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $trail->favorite_count }}</span>
    @else
        <span class="sr-only">{{ $favorited ? __('Remove from favorites') : __('Save as favorite') }}</span>
    @endif
</button>
