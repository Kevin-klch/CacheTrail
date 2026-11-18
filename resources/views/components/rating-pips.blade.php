@props(['label', 'short', 'value'])

{{-- Difficulty or terrain (1–5 in half steps) as a chip with five pips, like the stars on Geocaching.com. --}}
@php
    $formatted = str_replace('.', ',', (string) (float) $value);
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full bg-forest-50 px-2.5 py-1 text-xs font-semibold text-forest-800 ring-1 ring-forest-600/15']) }}
    title="{{ __(':label :value of 5', ['label' => $label, 'value' => $formatted]) }}">
    <span aria-hidden="true">{{ $short }}</span>
    <span class="inline-flex gap-0.5" aria-hidden="true">
        @for ($pip = 1; $pip <= 5; $pip++)
            <span @class([
                'size-2 rounded-full border border-forest-600',
                'bg-forest-600' => $value >= $pip,
                'bg-[linear-gradient(90deg,var(--color-forest-600)_50%,transparent_50%)]' => $value < $pip && $value >= $pip - 0.5,
            ])></span>
        @endfor
    </span>
    <span aria-hidden="true">{{ $formatted }}</span>
    <span class="sr-only">{{ __(':label :value of 5', ['label' => $label, 'value' => $formatted]) }}</span>
</span>
