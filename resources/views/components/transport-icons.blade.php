@props(['transportation'])

<span {{ $attributes->class(['inline-flex items-center gap-1.5']) }} title="{{ $transportation->label() }}">
    @if ($transportation->allowsWalking())
        <x-icon name="foot" class="size-4" />
    @endif
    @if ($transportation->allowsCycling())
        <x-icon name="bike" class="size-4" />
    @endif
    <span class="sr-only">{{ $transportation->label() }}</span>
</span>
