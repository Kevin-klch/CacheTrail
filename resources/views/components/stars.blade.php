@props(['value', 'size' => 'size-4'])

{{-- Read-only star display; half values are rounded to the nearest whole star. --}}
@php $filled = (int) round((float) $value); @endphp

<span {{ $attributes->class(['inline-flex items-center gap-0.5 text-trail-500']) }} title="{{ __(':value of 5 stars', ['value' => App\Support\Format::number((float) $value, 1)]) }}">
    @for ($star = 1; $star <= 5; $star++)
        <x-icon name="star" @class([$size, 'fill-current' => $star <= $filled, 'text-sand-300' => $star > $filled]) />
    @endfor
    <span class="sr-only">{{ __(':value of 5 stars', ['value' => App\Support\Format::number((float) $value, 1)]) }}</span>
</span>
