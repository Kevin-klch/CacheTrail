@props(['markers', 'location' => null])

{{-- Map view of the route directory: one marker per route at its start point. --}}
<div
    wire:ignore
    x-data="directoryMap(@js($markers), @js($location ? ['latitude' => $location->latitude, 'longitude' => $location->longitude] : null), @js(['view' => __('View route'), 'you' => __('Your approximate location')]))"
    {{ $attributes->class(['relative isolate overflow-hidden rounded-[2rem] border border-sand-200 bg-sand-100 shadow-[0_12px_32px_-16px_rgb(26_50_38/0.25)]']) }}
>
    <div x-ref="map" class="size-full" role="region" aria-label="{{ __('Map of the routes') }}"></div>
</div>
