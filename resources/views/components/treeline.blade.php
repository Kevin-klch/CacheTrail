{{-- Forest silhouette on top of the footer, built from the tree image in public/images/tree.png.
     The white tree is used as a mask so the silhouette takes the current text colour; a 720px wide
     group of trees with varied sizes repeats seamlessly (trees crossing the edge continue on the other side). --}}
@php
    // [x, y, width, height] within a 720 x 112 tile.
    $trees = [
        [0, 22, 68, 90], [60, 16, 72, 96], [115, 46, 50, 66], [156, 46, 50, 66], [188, 52, 45, 60],
        [227, 34, 59, 78], [257, 16, 72, 96], [305, 34, 59, 78], [337, 16, 72, 96], [375, 46, 50, 66],
        [419, 52, 45, 60], [452, 16, 72, 96], [489, 34, 59, 78], [520, 40, 54, 72], [553, 40, 54, 72],
        [592, 28, 63, 84], [638, 40, 54, 72], [667, 34, 59, 78], [-53, 34, 59, 78], [705, 4, 81, 108],
        [-15, 4, 81, 108],
    ];
    $id = 'treeline-'.Str::random(6);
@endphp

<svg {{ $attributes->class(['block h-[112px] w-full']) }} aria-hidden="true">
    <defs>
        <pattern id="{{ $id }}-pattern" width="720" height="112" patternUnits="userSpaceOnUse">
            @foreach ($trees as [$x, $y, $width, $height])
                <image href="{{ asset('images/tree.png') }}" x="{{ $x }}" y="{{ $y }}" width="{{ $width }}" height="{{ $height }}" preserveAspectRatio="none" />
            @endforeach
        </pattern>
        <mask id="{{ $id }}-mask" maskUnits="userSpaceOnUse" x="0" y="0" width="100%" height="112">
            <rect width="100%" height="112" fill="url(#{{ $id }}-pattern)" />
        </mask>
    </defs>
    <rect width="100%" height="112" fill="currentColor" mask="url(#{{ $id }}-mask)" />
    {{-- Forest floor covering the trunks, so the trees stand on the footer without thin gaps between them --}}
    <rect y="100" width="100%" height="12" fill="currentColor" />
</svg>
