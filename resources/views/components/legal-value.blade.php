@props(['key', 'label'])

{{-- One operator detail from config('cachetrail.legal'); missing values are highlighted outside production. --}}
@php
    $value = config('cachetrail.legal.'.$key);
@endphp

@if (filled($value)){{ $value }}@elseif (! app()->isProduction())<mark class="rounded bg-amber-100 px-1 text-amber-900">[{{ $label }}: CACHETRAIL_LEGAL_{{ strtoupper($key) }}]</mark>@endif
