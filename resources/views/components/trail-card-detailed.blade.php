@props(['trail', 'location' => null])

{{-- Large route card for the detailed directory view: cover image and route preview side by side, all key figures and a longer description. --}}
<article {{ $attributes->class(['group relative overflow-hidden rounded-[2rem] border border-sand-200 bg-white shadow-[0_12px_32px_-18px_rgb(26_50_38/0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-18px_rgb(26_50_38/0.35)]']) }}>
    @if ($trail->imageUrl() && $trail->caches->isNotEmpty())
        <div class="grid h-80 grid-rows-2 sm:h-72 sm:grid-cols-[3fr_2fr] sm:grid-rows-1">
            <img src="{{ $trail->imageUrl() }}" alt="" loading="lazy" class="size-full object-cover">
            <x-route-preview :trail="$trail" class="size-full" />
        </div>
    @else
        <x-trail-image :trail="$trail" class="h-56 w-full sm:h-72" />
    @endif
    @if ($location && $trail->distanceFromLabel($location))
        <span class="absolute left-4 top-4 z-10 inline-flex items-center gap-1.5 rounded-full bg-paper/95 px-3 py-1 text-xs font-semibold text-forest-800 shadow">
            <x-icon name="compass" class="size-3.5" />
            {{ $trail->distanceFromLabel($location) }}
        </span>
    @endif

    <div class="p-6 sm:p-8">
        <div class="flex flex-wrap items-start justify-between gap-x-6 gap-y-2">
            <div>
                <h3 class="text-3xl font-bold leading-tight">
                    <a href="{{ route('trails.show', $trail) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                        {{ $trail->name }}
                    </a>
                </h3>
                <p class="mt-1 text-sm text-ink-muted">
                    {{ __('by :name', ['name' => $trail->user->name]) }}
                    @if ($trail->published_at)
                        · {{ $trail->published_at->translatedFormat('j. F Y') }}
                    @endif
                </p>
            </div>
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 group-hover:underline" aria-hidden="true">
                {{ __('View route') }}
                <x-icon name="arrow-right" class="size-4" />
            </span>
        </div>

        <x-trail-stats :trail="$trail" class="mt-5 text-base" />

        <div class="mt-5 space-y-3 leading-relaxed text-ink-muted">
            @foreach (array_slice($trail->descriptionParagraphs(), 0, 2) as $paragraph)
                <p @class(['line-clamp-3' => $loop->first, 'line-clamp-2' => ! $loop->first])>{{ $paragraph }}</p>
            @endforeach
        </div>
    </div>
</article>
