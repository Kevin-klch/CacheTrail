<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    aria-pressed="{{ $bookmarked ? 'true' : 'false' }}"
    title="{{ $bookmarked ? __('Remove from the list') : __('Save for later') }}"
    @class([
        'relative z-10 inline-flex items-center justify-center gap-2 rounded-full font-semibold shadow transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-forest-700 disabled:opacity-70',
        'px-5 py-2.5 text-sm' => $large,
        'size-10' => ! $large,
        'bg-forest-700 text-paper hover:bg-forest-800' => $bookmarked,
        'bg-paper/95 text-forest-800 hover:bg-white' => ! $bookmarked && ! $large,
        'bg-white text-forest-800 ring-1 ring-forest-700/20 hover:bg-forest-50 hover:ring-forest-700/40' => ! $bookmarked && $large,
    ])
>
    <x-icon name="bookmark" @class(['size-5', 'fill-current' => $bookmarked]) />
    @if ($large)
        <span>{{ $bookmarked ? __('Saved for later') : __('Save for later') }}</span>
    @else
        <span class="sr-only">{{ $bookmarked ? __('Remove from the list') : __('Save for later') }}</span>
    @endif
</button>
