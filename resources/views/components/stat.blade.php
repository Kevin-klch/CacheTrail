@props(['label', 'icon'])

<div {{ $attributes->class(['flex items-center justify-between gap-4 border-b border-sand-200 py-3 last:border-b-0']) }}>
    <dt class="inline-flex items-center gap-2 font-heading text-base font-medium text-forest-800">
        <x-icon :name="$icon" class="size-4 text-forest-500" />
        {{ $label }}
    </dt>
    <dd class="text-right font-semibold text-ink">
        {{ $slot }}
    </dd>
</div>
