@props(['trail'])

{{-- Cover image of a trail; without an image a schematic preview of the route, or a drawn placeholder if it has no caches yet. --}}
@if ($trail->imageUrl())
    <img src="{{ $trail->imageUrl() }}" alt="" loading="lazy" {{ $attributes->class(['object-cover']) }}>
@elseif ($trail->caches->isNotEmpty())
    <x-route-preview :trail="$trail" :attributes="$attributes" />
@else
    <div {{ $attributes->class(['relative overflow-hidden bg-forest-600']) }} aria-hidden="true">
        <svg class="absolute inset-0 size-full text-forest-400/50" preserveAspectRatio="xMidYMid slice" viewBox="0 0 400 240" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M-20 200c60-30 110 10 170-20s80-90 150-80 70 40 120 20" />
            <path d="M-20 170c70-25 110 0 165-30s75-85 145-75 75 35 130 15" />
            <path d="M-20 140c80-20 110-10 160-40s70-80 140-70 80 30 140 10" />
            <path d="M-20 110c90-15 110-20 155-50s65-75 135-65 85 25 150 5" />
            <path d="M-20 80c100-10 110-30 150-60s60-70 130-60 90 20 160 0" />
        </svg>
        <svg class="absolute inset-0 size-full" preserveAspectRatio="xMidYMid meet" viewBox="0 0 400 240" fill="none">
            <path d="M70 190c40-30 30-80 90-95s70-55 120-50 50 40 70 20" class="stroke-trail-400" stroke-width="4" stroke-dasharray="10 9" stroke-linecap="round" />
            <circle cx="70" cy="190" r="9" class="fill-paper" />
            <circle cx="350" cy="65" r="9" class="fill-trail-400" />
        </svg>
    </div>
@endif
