@php
    $hasPremiumAccess = auth()->check() && Gate::allows('premium');
    $openReports = Gate::allows('admin') ? App\Models\Report::open()->count() : 0;

    $items = auth()->check() ? [
        ['route' => 'trails.index', 'label' => __('Discover routes'), 'icon' => 'compass'],
        ['route' => 'trails.create', 'label' => __('Create route'), 'icon' => 'plus'],
        ['route' => 'trails.mine', 'label' => __('My routes'), 'icon' => 'route'],
        ['route' => 'bookmarks.index', 'label' => __('Saved'), 'icon' => 'bookmark'],
    ] : [];
@endphp

<header x-data="{ open: false }" class="relative z-30 bg-forest-800 text-paper">
    <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-6 px-4 sm:px-6 lg:px-8" aria-label="{{ __('Main navigation') }}">
        <a href="{{ route('home') }}" class="shrink-0 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-paper">
            <x-logo inverted />
            <span class="sr-only">{{ __('Home') }}</span>
        </a>

        <div class="hidden flex-1 items-center gap-1 lg:flex">
            @foreach ($items as $item)
                @continue($item['route'] === 'trails.create')
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])" :icon="$item['icon']" :locked="! $hasPremiumAccess">
                    {{ $item['label'] }}
                </x-nav-link>
            @endforeach
        </div>

        <div class="hidden items-center gap-3 lg:flex">
            @auth
                <a href="{{ route('trails.create') }}" @class([
                    'inline-flex items-center gap-2 whitespace-nowrap rounded-full px-4 py-2 text-sm font-semibold transition focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-paper',
                    'bg-trail-500 text-white hover:bg-trail-600' => $hasPremiumAccess,
                    'bg-paper/10 text-forest-100 hover:bg-paper/15' => ! $hasPremiumAccess,
                ]) @unless ($hasPremiumAccess) title="{{ __('Requires premium access') }}" @endunless>
                    <x-icon name="plus" class="size-4" />
                    {{ __('Create route') }}
                    @unless ($hasPremiumAccess)
                        <x-icon name="lock" class="size-3.5 opacity-80" />
                        <span class="sr-only">({{ __('Requires premium access') }})</span>
                    @endunless
                </a>

                <div class="relative" x-data="{ menu: false }" x-on:keydown.escape.window="menu = false" x-on:click.outside="menu = false">
                    <button
                        type="button"
                        class="relative inline-flex items-center gap-1.5 rounded-full p-1 pr-2 text-forest-100 transition hover:bg-paper/10 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-paper"
                        x-on:click="menu = ! menu"
                        x-bind:aria-expanded="menu.toString()"
                        aria-controls="user-menu"
                    >
                        <span class="grid size-9 place-items-center rounded-full bg-paper font-heading text-lg font-bold text-forest-800" aria-hidden="true">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <x-icon name="chevron-down" class="size-4 transition" x-bind:class="menu && 'rotate-180'" />
                        <span class="sr-only">{{ __('Account menu') }}</span>
                        @if ($openReports > 0)
                            <span class="absolute right-1 top-0.5 size-2.5 rounded-full bg-trail-500 ring-2 ring-forest-800" aria-hidden="true"></span>
                        @endif
                    </button>

                    <div
                        id="user-menu"
                        x-show="menu"
                        x-cloak
                        x-transition.origin.top.right
                        class="absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl bg-white text-ink shadow-xl ring-1 ring-forest-900/10"
                    >
                        <div class="border-b border-sand-200 px-4 py-3">
                            <p class="truncate font-semibold text-forest-900">{{ auth()->user()->name }}</p>
                            <p class="truncate text-sm text-ink-muted">{{ auth()->user()->email }}</p>
                        </div>
                        <div class="p-1.5">
                            <a href="{{ auth()->user()->profileUrl() }}" class="flex items-center gap-3 rounded-xl px-3 py-2 text-sm hover:bg-sand-100">
                                <x-icon name="user" class="size-4 text-forest-700" />
                                {{ __('My public profile') }}
                            </a>
                            <a href="{{ route('profile.edit') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2 text-sm hover:bg-sand-100', 'bg-sand-100 font-semibold' => request()->routeIs('profile.*')])>
                                <x-icon name="sliders" class="size-4 text-forest-700" />
                                {{ __('Account settings') }}
                            </a>
                            @can('admin')
                                <a href="{{ route('admin.reports.index') }}" @class(['flex items-center gap-3 rounded-xl px-3 py-2 text-sm hover:bg-sand-100', 'bg-sand-100 font-semibold' => request()->routeIs('admin.*')])>
                                    <x-icon name="flag" class="size-4 text-forest-700" />
                                    {{ __('Admin') }}
                                    @if ($openReports > 0)
                                        <span class="ml-auto rounded-full bg-trail-500 px-1.5 py-0.5 text-xs font-semibold text-white">{{ $openReports }}</span>
                                    @endif
                                </a>
                            @endcan
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-sand-200 p-1.5">
                            @csrf
                            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm hover:bg-sand-100">
                                <x-icon name="logout" class="size-4 text-forest-700" />
                                {{ __('Log out') }}
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <x-nav-link :href="route('login')" :active="request()->routeIs('login')">{{ __('Log in') }}</x-nav-link>
                <x-button variant="light" :href="route('register')">{{ __('Sign up') }}</x-button>
            @endauth
        </div>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-full p-2 text-paper transition hover:bg-paper/10 lg:hidden"
            x-on:click="open = ! open"
            x-bind:aria-expanded="open.toString()"
            aria-controls="mobile-menu"
        >
            <span class="sr-only">{{ __('Toggle menu') }}</span>
            <x-icon name="menu" class="size-6" x-show="! open" />
            <x-icon name="close" class="size-6" x-show="open" x-cloak />
        </button>
    </nav>

    <div
        id="mobile-menu"
        x-show="open"
        x-cloak
        x-transition.opacity
        x-on:keydown.escape.window="open = false"
        class="border-t border-paper/10 bg-forest-800 px-4 pb-6 pt-3 lg:hidden"
    >
        <div class="flex flex-col gap-1">
            <x-nav-link mobile :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-nav-link>
            @foreach ($items as $item)
                <x-nav-link mobile :href="route($item['route'])" :active="request()->routeIs($item['route'])" :icon="$item['icon']" :locked="! $hasPremiumAccess">
                    {{ $item['label'] }}
                </x-nav-link>
            @endforeach
        </div>

        <div class="mt-4 flex flex-col gap-1 border-t border-paper/10 pt-4">
            @auth
                @can('admin')
                    <x-nav-link mobile :href="route('admin.reports.index')" :active="request()->routeIs('admin.*')" icon="flag">
                        {{ __('Admin') }}
                        @if ($openReports > 0)
                            <span class="ml-1 rounded-full bg-trail-500 px-1.5 py-0.5 text-xs text-white">{{ $openReports }}</span>
                        @endif
                    </x-nav-link>
                @endcan
                <x-nav-link mobile :href="auth()->user()->profileUrl()" icon="user">{{ __('My public profile') }}</x-nav-link>
                <x-nav-link mobile :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="sliders">{{ __('Account settings') }}</x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center gap-2 rounded-2xl px-4 py-3 text-base font-medium text-forest-100 transition hover:bg-paper/10 hover:text-white">
                        <x-icon name="logout" />
                        {{ __('Log out') }}
                    </button>
                </form>
            @else
                <x-nav-link mobile :href="route('login')" :active="request()->routeIs('login')">{{ __('Log in') }}</x-nav-link>
                <x-button variant="light" :href="route('register')" class="mt-2">{{ __('Sign up') }}</x-button>
            @endauth
        </div>
    </div>
</header>
