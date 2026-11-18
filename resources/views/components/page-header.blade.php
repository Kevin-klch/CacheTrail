@props(['title', 'eyebrow' => null])

<div class="slant-bottom relative overflow-hidden bg-forest-700 pb-28 pt-10 text-paper sm:pt-14">
    <x-contour-lines />
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($eyebrow)
            <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ $eyebrow }}</p>
        @endif
        <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ $title }}</h1>
        @if ($slot->isNotEmpty())
            <p class="mt-3 max-w-2xl text-lg text-forest-100/90">{{ $slot }}</p>
        @endif
        {{ $below ?? '' }}
    </div>
</div>
