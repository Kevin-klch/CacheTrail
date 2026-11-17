@props(['type' => 'success'])

<div role="status" {{ $attributes->class([
    'flex items-start gap-3 rounded-2xl px-4 py-3 text-sm',
    'bg-forest-100 text-forest-900' => $type === 'success',
    'bg-trail-400/15 text-trail-600' => $type === 'info',
]) }}>
    <x-icon :name="$type === 'success' ? 'check' : 'mail'" class="mt-0.5 size-4" />
    <div>{{ $slot }}</div>
</div>
