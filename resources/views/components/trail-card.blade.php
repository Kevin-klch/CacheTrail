@props(['trail', 'location' => null])

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl border border-sand-200 bg-white shadow-[0_12px_32px_-18px_rgb(26_50_38/0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-18px_rgb(26_50_38/0.35)]']) }}>
    <x-trail-image :trail="$trail" class="aspect-[16/9] w-full" />
    @if ($location && $trail->distanceFromLabel($location))
        <span class="absolute left-4 top-4 z-10 inline-flex items-center gap-1.5 rounded-full bg-paper/95 px-3 py-1 text-xs font-semibold text-forest-800 shadow">
            <x-icon name="compass" class="size-3.5" />
            {{ $trail->distanceFromLabel($location) }}
        </span>
    @endif

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-2xl font-bold leading-tight">
            <a href="{{ route('trails.show', $trail) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                {{ $trail->name }}
            </a>
        </h3>
        <p class="mt-1 text-sm text-ink-muted">{{ __('by :name', ['name' => $trail->user->name]) }}</p>

        <x-trail-stats :trail="$trail" class="mt-4" />

        <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-ink-muted">{{ $trail->description }}</p>
    </div>
</article>
