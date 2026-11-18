@props(['user', 'size' => 'sm'])

{{-- Small badge with the role after a name (admin, moderator, supporter); normal users get none. --}}
@if ($user->role->hasBadge())
    <span {{ $attributes->class([
        'relative z-10 inline-flex items-center gap-1 rounded-full align-middle font-sans font-bold uppercase leading-none tracking-wider',
        'px-1.5 py-0.5 text-[0.6rem]' => $size === 'sm',
        'px-3 py-1.5 text-xs' => $size === 'lg',
        $user->role->badgeClasses(),
    ]) }} title="{{ $user->role->label() }}">
        <x-icon :name="$user->role->icon()" @class(['size-3' => $size === 'sm', 'size-3.5' => $size === 'lg']) />
        {{ $user->role->label() }}
    </span>
@endif
