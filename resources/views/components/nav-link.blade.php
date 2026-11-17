@props(['href', 'active' => false, 'icon' => null, 'locked' => false, 'mobile' => false])

<a
    href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    @if ($locked) title="{{ __('Requires premium access') }}" @endif
    {{ $attributes->class([
        'group inline-flex items-center gap-2 whitespace-nowrap font-medium transition',
        'rounded-full px-3.5 py-2 text-sm' => ! $mobile,
        'w-full rounded-2xl px-4 py-3 text-base' => $mobile,
        'bg-paper/15 text-white' => $active,
        'text-forest-100 hover:bg-paper/10 hover:text-white' => ! $active && ! $locked,
        'text-forest-200/70 hover:bg-paper/10 hover:text-forest-100' => ! $active && $locked,
    ]) }}
>
    @if ($icon)
        <x-icon :name="$icon" @class(['size-4' => ! $mobile, 'size-5' => $mobile]) />
    @endif
    <span>{{ $slot }}</span>
    @if ($locked)
        <x-icon name="lock" class="size-3.5 opacity-80" />
        <span class="sr-only">({{ __('Requires premium access') }})</span>
    @endif
</a>
