@props(['name'])

<svg {{ $attributes->class(['size-5 shrink-0']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('compass')
            <circle cx="12" cy="12" r="9" /><path d="m15.5 8.5-2 5-5 2 2-5Z" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('map')
            <path d="m9 4-6 2.5v13L9 17l6 2.5 6-2.5v-13L15 6.5 9 4Z" /><path d="M9 4v13M15 6.5v13" />
            @break
        @case('heart')
            <path d="M12 20s-7.5-4.6-9.2-9.4C1.6 7.2 3.8 4 7.2 4c2 0 3.6 1.1 4.8 2.8C13.2 5.1 14.8 4 16.8 4c3.4 0 5.6 3.2 4.4 6.6C19.5 15.4 12 20 12 20Z" />
            @break
        @case('user')
            <circle cx="12" cy="8" r="4" /><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7" />
            @break
        @case('lock')
            <rect x="4.5" y="10.5" width="15" height="10" rx="2" /><path d="M8 10.5V7a4 4 0 0 1 8 0v3.5" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" />
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break
        @case('close')
            <path d="M6 6l12 12M18 6 6 18" />
            @break
        @case('logout')
            <path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 17l5-5-5-5M15 12H4" />
            @break
        @case('route')
            <circle cx="6" cy="18" r="2.5" /><circle cx="18" cy="6" r="2.5" /><path d="M8.5 18H15a3 3 0 0 0 0-6H9a3 3 0 0 1 0-6h6.5" stroke-dasharray="2.5 2.5" />
            @break
        @case('share')
            <circle cx="18" cy="5" r="3" /><circle cx="6" cy="12" r="3" /><circle cx="18" cy="19" r="3" /><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4" />
            @break
        @case('check')
            <path d="m5 12.5 4.5 4.5L19 7.5" />
            @break
        @case('arrow-right')
            <path d="M5 12h14M13 6l6 6-6 6" />
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3.5 6.5 8.5 6.5 8.5-6.5" />
            @break
    @endswitch
</svg>
