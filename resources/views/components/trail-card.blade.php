@props(['trail'])

<article {{ $attributes->class(['group relative flex flex-col overflow-hidden rounded-3xl border border-sand-200 bg-white shadow-[0_12px_32px_-18px_rgb(26_50_38/0.25)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_-18px_rgb(26_50_38/0.35)]']) }}>
    <x-trail-image :trail="$trail" class="aspect-[16/9] w-full" />

    <div class="flex flex-1 flex-col p-5">
        <h3 class="text-2xl font-bold leading-tight">
            <a href="{{ route('trails.show', $trail) }}" class="after:absolute after:inset-0 focus-visible:outline-none group-focus-within:underline">
                {{ $trail->name }}
            </a>
        </h3>
        <p class="mt-1 text-sm text-ink-muted">{{ __('by :name', ['name' => $trail->user->name]) }}</p>

        <dl class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-forest-800">
            <div class="inline-flex items-center gap-1.5">
                <dt><x-icon name="clock" class="size-4 text-forest-500" /><span class="sr-only">{{ __('Estimated duration') }}</span></dt>
                <dd class="font-medium">{{ $trail->durationLabel() }}</dd>
            </div>
            <div class="inline-flex items-center gap-1.5">
                <dt class="sr-only">{{ __('Means of transport') }}</dt>
                <dd class="text-forest-700"><x-transport-icons :transportation="$trail->transportation" /></dd>
            </div>
        </dl>

        <p class="mt-4 line-clamp-3 text-sm leading-relaxed text-ink-muted">{{ $trail->description }}</p>
    </div>
</article>
