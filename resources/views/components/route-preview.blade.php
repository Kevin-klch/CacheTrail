@props(['trail'])

{{-- Schematic preview of the real route: stations and connecting line, without map tiles (no external requests). --}}
@php
    $points = App\Support\RoutePreview::project(
        $trail->caches->map(fn ($cache) => [$cache->latitude, $cache->longitude])->values()->all(),
    );
    $showNumbers = count($points) <= 12;
@endphp

<div {{ $attributes->class(['relative overflow-hidden bg-forest-600']) }} aria-hidden="true">
    <svg class="absolute inset-0 size-full text-forest-400/40" preserveAspectRatio="xMidYMid slice" viewBox="0 0 400 240" fill="none" stroke="currentColor" stroke-width="1.5">
        <path d="M-20 200c60-30 110 10 170-20s80-90 150-80 70 40 120 20" />
        <path d="M-20 160c70-25 110 0 165-30s75-85 145-75 75 35 130 15" />
        <path d="M-20 120c80-20 110-10 160-40s70-80 140-70 80 30 140 10" />
        <path d="M-20 80c100-10 110-30 150-60s60-70 130-60 90 20 160 0" />
    </svg>
    <svg class="absolute inset-0 size-full" preserveAspectRatio="xMidYMid meet" viewBox="0 0 400 240" fill="none">
        @if (count($points) > 1)
            <polyline points="{{ collect($points)->map(fn ($point) => implode(',', $point))->implode(' ') }}"
                class="stroke-trail-400" stroke-width="3.5" stroke-dasharray="9 8" stroke-linecap="round" stroke-linejoin="round" />
        @endif
        @foreach ($points as $index => [$x, $y])
            @php $isEnd = $loop->last && count($points) > 1; @endphp
            <circle cx="{{ $x }}" cy="{{ $y }}" r="{{ $showNumbers ? 11 : 5 }}" @class([
                'stroke-paper',
                'fill-forest-800' => ! $loop->first && ! $isEnd,
                'fill-forest-400' => $loop->first,
                'fill-trail-500' => $isEnd,
            ]) stroke-width="2.5" />
            @if ($showNumbers)
                <text x="{{ $x }}" y="{{ $y + 4 }}" text-anchor="middle" class="fill-paper font-sans text-[11px] font-bold">{{ $index + 1 }}</text>
            @endif
        @endforeach
    </svg>
</div>
