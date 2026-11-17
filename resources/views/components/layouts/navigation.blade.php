@php
    $hasPremiumAccess = auth()->check() && Gate::allows('premium');
    $openReports = Gate::allows('admin') ? App\Models\Report::open()->count() : 0;

    $items = auth()->check() ? [
        ['route' => 'trails.index', 'label' => __('Discover routes'), 'icon' => 'compass'],
        ['route' => 'trails.create', 'label' => __('Create route'), 'icon' => 'plus'],
        ['route' => 'trails.mine', 'label' => __('My routes'), 'icon' => 'route'],
        ['route' => 'favorites.index', 'label' => __('Favorites'), 'icon' => 'heart'],
    ] : [];
@endphp

<header x-data="{ open: false }" class="relative z-30 bg-forest-800 text-paper">
    <nav class="mx-auto flex h-18 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8" aria-label="{{ __('Main navigation') }}">
        <a href="{{ route('home') }}" class="shrink-0 rounded-lg focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-paper">
            <x-logo inverted />
            <span class="sr-only">{{ __('Home') }}</span>
        </a>

        <div class="hidden items-center gap-1 xl:flex">
            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">{{ __('Home') }}</x-nav-link>
            @foreach ($items as $item)
                <x-nav-link :href="route($item['route'])" :active="request()->routeIs($item['route'])" :icon="$item['icon']" :locked="! $hasPremiumAccess">
                    {{ $item['label'] }}
                </x-nav-link>
            @endforeach
        </div>

        <div class="hidden items-center gap-2 xl:flex">
            @auth
                @can('admin')
                    <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.*')" icon="flag">
                        {{ __('Admin') }}
                        @if ($openReports > 0)
                            <span class="ml-1 rounded-full bg-trail-500 px-1.5 py-0.5 text-xs text-white">{{ $openReports }}</span>
                        @endif
                    </x-nav-link>
                @endcan
                <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">{{ __('Profile') }}</x-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full px-3.5 py-2 text-sm font-medium text-forest-100 transition hover:bg-paper/10 hover:text-white">
                        <x-icon name="logout" class="size-4" />
                        {{ __('Log out') }}
                    </button>
                </form>
            @else
                <x-nav-link :href="route('login')" :active="request()->routeIs('login')">{{ __('Log in') }}</x-nav-link>
                <x-button variant="light" :href="route('register')">{{ __('Sign up') }}</x-button>
            @endauth
        </div>

        <button
            type="button"
            class="inline-flex items-center justify-center rounded-full p-2 text-paper transition hover:bg-paper/10 xl:hidden"
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
        class="border-t border-paper/10 bg-forest-800 px-4 pb-6 pt-3 xl:hidden"
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
                <x-nav-link mobile :href="route('profile.edit')" :active="request()->routeIs('profile.*')" icon="user">{{ __('Profile') }}</x-nav-link>
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
