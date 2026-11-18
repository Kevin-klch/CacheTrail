@props(['markers'])

{{-- Interactive Leaflet map of a route; redraws on "trail-caches-updated" window events (route editor). --}}
<div
    wire:ignore
    x-data="trailMap(@js($markers), @js([
        'start' => __('Start'),
        'end' => __('Finish'),
        'difficulty' => __('Difficulty'),
        'terrain' => __('Terrain'),
        'status' => __('Status'),
        'openCache' => __('Open on Geocaching.com'),
        'navigate' => __('Start navigation'),
        'station' => __('Station :number of :total'),
        'previous' => __('Previous station'),
        'next' => __('Next station'),
        'decimal' => App\Support\Format::decimalSeparator(),
    ]))"
    x-on:trail-caches-updated.window="update($event)"
    {{ $attributes->class(['relative isolate overflow-hidden rounded-[2rem] border border-sand-200 bg-sand-100 shadow-[0_12px_32px_-16px_rgb(26_50_38/0.25)]']) }}
>
    <div x-ref="map" class="size-full" role="region" aria-label="{{ __('Map of the route') }}"></div>
</div>
