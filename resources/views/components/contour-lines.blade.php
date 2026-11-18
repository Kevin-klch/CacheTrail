{{-- Light contour lines like on a topographic map, as background of the green headers.
     The parent needs "relative overflow-hidden"; content on top of it needs "relative". --}}
<svg {{ $attributes->class(['pointer-events-none absolute inset-0 h-full w-full text-forest-500/40']) }} preserveAspectRatio="xMidYMid slice" viewBox="0 0 1200 600" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
    <path d="M-50 480c160-60 260 20 420-30s240-170 420-140 260 120 460 60" />
    <path d="M-50 420c170-50 260 10 410-40s230-160 410-130 270 110 480 50" />
    <path d="M-50 360c180-40 260 0 400-50s220-150 400-120 280 100 500 40" />
    <path d="M-50 300c190-30 260-10 390-60s210-140 390-110 290 90 520 30" />
    <path d="M-50 240c200-20 260-20 380-70s200-130 380-100 300 80 540 20" />
    <path d="M-50 180c210-10 260-30 370-80s190-120 370-90 310 70 560 10" />
    <path d="M-50 120c220 0 260-40 360-90s180-110 360-80 320 60 580 0" />
</svg>
