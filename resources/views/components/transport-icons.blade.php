@props(['transportations'])

@php
    $label = collect($transportations)->map->label()->implode(', ');
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5']) }} title="{{ $label }}">
    @foreach ($transportations as $transportation)
        <x-icon :name="$transportation->icon()" class="size-4" />
    @endforeach
    <span class="sr-only">{{ $label }}</span>
</span>
