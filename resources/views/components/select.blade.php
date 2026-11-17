@props(['name', 'label', 'options' => [], 'value' => null, 'placeholder' => null])

@php
    $id = $attributes->get('id', $name);
    $message = $errors->first($name);
    $selected = (string) old($name, $value);
@endphp

<div>
    <label for="{{ $id }}" class="mb-1.5 block text-sm font-medium text-forest-900">{{ $label }}</label>
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($message) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except('id')->class([
            'block w-full rounded-xl border bg-white px-4 py-2.5 text-ink shadow-xs transition focus:outline-none focus:ring-4',
            'border-sand-300 focus:border-forest-500 focus:ring-forest-500/15' => ! $message,
            'border-red-600 focus:border-red-600 focus:ring-red-600/15' => $message,
        ]) }}
    >
        @if ($placeholder !== null)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($message)
        <p id="{{ $id }}-error" class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
    @endif
</div>
