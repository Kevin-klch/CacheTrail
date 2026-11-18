@props(['user', 'size' => 'sm'])

{{-- Badges after a name: the main role (admin, moderator, owner; normal users get none) followed by the
     secondary roles (supporter, developer). --}}
@foreach ($user->roleBadges() as $role)
    <span {{ $attributes->class([
        'relative z-10 inline-flex items-center gap-1 rounded-full align-baseline font-sans font-bold uppercase leading-none tracking-wider',
        'px-1.5 py-0.5 text-[0.6rem]' => $size === 'sm',
        'px-3 py-1.5 text-xs' => $size === 'lg',
        $role->badgeClasses(),
    ]) }} title="{{ $role->label() }}">
        <x-icon :name="$role->icon()" @class(['size-3' => $size === 'sm', 'size-3.5' => $size === 'lg']) />
        {{ $role->label() }}
    </span>
@endforeach
