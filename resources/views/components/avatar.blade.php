@props(['user'])

{{-- Round profile picture of a user, or the first letter of the name if there is none. Size and text size
     come from the classes passed in, e.g. class="size-20 text-4xl". --}}
@if ($user->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="" {{ $attributes->class(['shrink-0 rounded-full bg-sand-100 object-cover']) }}>
@else
    <span {{ $attributes->class(['grid shrink-0 place-items-center rounded-full bg-paper font-heading font-bold text-forest-800']) }} aria-hidden="true">{{ $user->initial() }}</span>
@endif
