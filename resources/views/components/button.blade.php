@props(['variant' => 'primary', 'href' => null])

@php
    $classes = [
        'inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 disabled:cursor-not-allowed disabled:opacity-60',
        match ($variant) {
            'primary' => 'bg-forest-700 text-paper hover:bg-forest-800 focus-visible:outline-forest-700',
            'accent' => 'bg-trail-500 text-white hover:bg-trail-600 focus-visible:outline-trail-500',
            'secondary' => 'border border-forest-700/25 bg-white/60 text-forest-800 hover:border-forest-700/50 hover:bg-white focus-visible:outline-forest-700',
            'light' => 'bg-paper text-forest-900 hover:bg-white focus-visible:outline-paper',
            'danger' => 'bg-red-700 text-white hover:bg-red-800 focus-visible:outline-red-700',
            'ghost' => 'text-forest-800 hover:bg-forest-700/10 focus-visible:outline-forest-700',
        },
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit'])->class($classes) }}>{{ $slot }}</button>
@endif
