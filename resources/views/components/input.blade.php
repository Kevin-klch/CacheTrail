@props(['name', 'label', 'type' => 'text', 'value' => null, 'bag' => 'default', 'hint' => null])

@php
    $id = $attributes->get('id', $name);
    $message = $errors->getBag($bag)->first($name);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-forest-900">{{ $label }}</label>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
        @if ($message) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->class([
            'block w-full rounded-xl border bg-white px-4 py-2.5 text-ink shadow-xs transition placeholder:text-ink-muted/60 focus:outline-none focus:ring-4',
            'border-sand-300 focus:border-forest-500 focus:ring-forest-500/15' => ! $message,
            'border-red-600 focus:border-red-600 focus:ring-red-600/15' => $message,
        ]) }}
    >
    @if ($message)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
    @elseif ($hint)
        <p class="mt-1.5 text-sm text-ink-muted">{{ $hint }}</p>
    @endif
</div>
