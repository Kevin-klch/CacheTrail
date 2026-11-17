@props(['trail', 'location' => null])

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl border border-sand-200 bg-white shadow-[0_12px_32px_-18px_rgb(26_50_38/0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-18px_rgb(26_50_38/0.35)]']) }}>
    <div class="relative">
        <x-trail-image :trail="$trail" class="aspect-[16/9] w-full" />
        <x-trail-card-overlays :trail="$trail" :location="$location" />
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-2xl font-bold leading-tight">
            <a href="{{ route('trails.show', $trail) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                {{ $trail->name }}
            </a>
        </h3>
        <p class="mt-1 text-sm text-ink-muted">
            {{ __('by') }} <a href="{{ $trail->user->profileUrl() }}" class="relative z-10 font-medium text-forest-700 hover:underline">{{ $trail->user->name }}</a>
        </p>

        <x-trail-stats :trail="$trail" class="mt-4" />

        <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-ink-muted">{{ $trail->description }}</p>
    </div>
</article>
