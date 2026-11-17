@props(['label', 'name', 'values' => [], 'min' => 0, 'max' => null, 'step' => 1, 'unit' => null])

{{-- A "from / to" pair of number inputs named min_{name} and max_{name}. --}}
<fieldset {{ $attributes }}>
    <legend class="mb-1.5 block text-sm font-medium text-forest-900">
        {{ $label }}@if ($unit) <span class="font-normal text-ink-muted">({{ $unit }})</span>@endif
    </legend>
    <div class="flex items-center gap-2">
        @foreach (['min' => __('from'), 'max' => __('to')] as $bound => $placeholder)
            @php $field = "{$bound}_{$name}"; @endphp
            @if (! $loop->first)
                <span class="text-ink-muted" aria-hidden="true">–</span>
            @endif
            <label for="{{ $field }}" class="sr-only">{{ $label }} {{ $placeholder }}</label>
            <input id="{{ $field }}" name="{{ $field }}" type="number" min="{{ $min }}" @if ($max !== null) max="{{ $max }}" @endif step="{{ $step }}"
                value="{{ $values[$field] ?? '' }}" placeholder="{{ $placeholder }}"
                @class([
                    'block w-full min-w-0 rounded-xl border bg-white px-3 py-2 text-ink shadow-xs placeholder:text-ink-muted/60 focus:outline-none focus:ring-4',
                    'border-sand-300 focus:border-forest-500 focus:ring-forest-500/15' => ! $errors->has($field),
                    'border-red-600 focus:ring-red-600/15' => $errors->has($field),
                ])>
        @endforeach
    </div>
    @error("min_{$name}")
        <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
    @enderror
    @error("max_{$name}")
        <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
    @enderror
</fieldset>
