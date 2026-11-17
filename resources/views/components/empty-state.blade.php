@props(['icon' => 'route', 'title'])

<div {{ $attributes->class(['flex flex-col items-center rounded-3xl border-2 border-dashed border-sand-300 bg-sand-100/50 px-6 py-12 text-center']) }}>
    <span class="grid size-14 place-items-center rounded-full bg-paper text-forest-600 ring-1 ring-sand-200">
        <x-icon :name="$icon" class="size-7" />
    </span>
    <h3 class="mt-4 text-xl font-bold">{{ $title }}</h3>
    <div class="mt-2 max-w-md text-sm text-ink-muted">{{ $slot }}</div>
</div>
