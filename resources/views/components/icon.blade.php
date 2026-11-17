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
        @case('foot')
            <path d="M8 3.5c1.7 0 2.5 1.8 2.5 4S9.6 12 8.2 12 5.5 10.3 5.5 8 6.3 3.5 8 3.5ZM6 14.5h4.3l-.4 3a2.2 2.2 0 0 1-4.3-.4Z" /><path d="M16 7c1.7 0 2.5 1.8 2.5 4s-.9 4.5-2.3 4.5-2.7-1.7-2.7-4S14.3 7 16 7ZM14 18h4.3l-.4 2a2.2 2.2 0 0 1-4.3-.4Z" />
            @break
        @case('bike')
            <circle cx="5.5" cy="16.5" r="3.5" /><circle cx="18.5" cy="16.5" r="3.5" /><path d="M5.5 16.5 9 9h6l3.5 7.5M9 9 12 16.5h-6.5M15 9l-3 7.5M14 6h2.5" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />
            @break
        @case('calendar')
            <rect x="3.5" y="5" width="17" height="15.5" rx="2" /><path d="M3.5 10h17M8 3v4M16 3v4" />
            @break
        @case('edit')
            <path d="M4 20h4L19 9a2.8 2.8 0 0 0-4-4L4 16Z" /><path d="m13.5 6.5 4 4" />
            @break
        @case('trash')
            <path d="M4 7h16M9 7V4.5h6V7M6.5 7l1 13h9l1-13M10 11v5.5M14 11v5.5" />
            @break
        @case('image')
            <rect x="3" y="4.5" width="18" height="15" rx="2" /><circle cx="8.5" cy="9.5" r="1.8" /><path d="m21 16-5-5-9 8.5" />
            @break
        @case('eye-off')
            <path d="M3 3l18 18M10.6 5.1A10 10 0 0 1 12 5c5 0 8.5 4.5 9.5 7a13 13 0 0 1-2.9 4.1M6.6 6.6A13 13 0 0 0 2.5 12c1 2.5 4.5 7 9.5 7a9.6 9.6 0 0 0 5.4-1.6M9.9 9.9a3 3 0 0 0 4.2 4.2" />
            @break
        @case('bookmark')
            <path d="M6.5 4h11a1 1 0 0 1 1 1v15l-6.5-4-6.5 4V5a1 1 0 0 1 1-1Z" />
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="9" /><path d="m8 12.5 3 3 5-6" />
            @break
        @case('sliders')
            <path d="M4 7h10M18 7h2M4 17h4M12 17h8" /><circle cx="16" cy="7" r="2" /><circle cx="10" cy="17" r="2" />
            @break
        @case('difficulty')
            <path d="M4 17a8 8 0 0 1 16 0" /><path d="m12 17 4-5" /><path d="M6.5 13.5 5 12.5M17.5 13.5l1.5-1M12 9V7.5" />
            @break
        @case('terrain')
            <path d="m3 19 6-10 4 6 2-3 6 7Z" /><path d="m9 9 1.5 2.5" />
            @break
        @case('star')
            <path d="m12 3.5 2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.9l-5.2 2.7 1-5.8-4.3-4.1 5.9-.9Z" />
            @break
        @case('flag')
            <path d="M5 21V4M5 4h11l-2 4 2 4H5" />
            @break
        @case('grip')
            <circle cx="9" cy="6" r="1.2" fill="currentColor" /><circle cx="15" cy="6" r="1.2" fill="currentColor" /><circle cx="9" cy="12" r="1.2" fill="currentColor" /><circle cx="15" cy="12" r="1.2" fill="currentColor" /><circle cx="9" cy="18" r="1.2" fill="currentColor" /><circle cx="15" cy="18" r="1.2" fill="currentColor" />
            @break
        @case('chevron-up')
            <path d="m6 15 6-6 6 6" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('external')
            <path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" />
            @break
        @case('alert')
            <path d="M12 4 2.5 20h19Z" /><path d="M12 10v4.5M12 17.5v.01" />
            @break
        @case('grid')
            <rect x="4" y="4" width="7" height="7" rx="1.5" /><rect x="13" y="4" width="7" height="7" rx="1.5" /><rect x="4" y="13" width="7" height="7" rx="1.5" /><rect x="13" y="13" width="7" height="7" rx="1.5" />
            @break
        @case('list')
            <rect x="4" y="4" width="16" height="7" rx="1.5" /><rect x="4" y="13" width="16" height="7" rx="1.5" />
            @break
        @case('mail')
            <rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3.5 6.5 8.5 6.5 8.5-6.5" />
            @break
    @endswitch
</svg>
