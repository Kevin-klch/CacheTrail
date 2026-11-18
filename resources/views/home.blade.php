<x-layouts.app>
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-b-[3rem] bg-forest-700 text-paper sm:rounded-b-[5rem]">
        <svg class="absolute inset-0 h-full w-full text-forest-500/40" preserveAspectRatio="xMidYMid slice" viewBox="0 0 1200 600" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path d="M-50 480c160-60 260 20 420-30s240-170 420-140 260 120 460 60" />
            <path d="M-50 420c170-50 260 10 410-40s230-160 410-130 270 110 480 50" />
            <path d="M-50 360c180-40 260 0 400-50s220-150 400-120 280 100 500 40" />
            <path d="M-50 300c190-30 260-10 390-60s210-140 390-110 290 90 520 30" />
            <path d="M-50 240c200-20 260-20 380-70s200-130 380-100 300 80 540 20" />
            <path d="M-50 180c210-10 260-30 370-80s190-120 370-90 310 70 560 10" />
            <path d="M-50 120c220 0 260-40 360-90s180-110 360-80 320 60 580 0" />
        </svg>

        <div class="relative mx-auto grid grid-cols-1 max-w-7xl items-center gap-12 px-4 pb-24 pt-16 sm:px-6 sm:pb-32 sm:pt-24 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <div>
                @if (session('status') === 'account-deleted')
                    <x-alert class="mb-8 max-w-lg">{{ __('Your account has been deleted.') }}</x-alert>
                @endif

                <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Geocaching routes from the community') }}</p>
                <h1 class="mt-3 text-5xl font-bold leading-[1.05] text-paper sm:text-6xl lg:text-7xl">{{ __('Your adventure starts here.') }}</h1>
                <p class="mt-6 max-w-xl text-lg text-forest-100/90">
                    {{ __('Discover ready-made geocaching routes that fit your day, or share your favorite tour with other geocachers.') }}
                </p>

                <form method="GET" action="{{ route('trails.index') }}" role="search" class="mt-10 flex max-w-xl flex-col gap-3 rounded-[1.75rem] bg-paper p-2 shadow-2xl shadow-forest-950/30 sm:flex-row sm:rounded-full">
                    <label for="hero-search" class="sr-only">{{ __('Search routes') }}</label>
                    <div class="flex flex-1 items-center gap-3 px-4">
                        <x-icon name="search" class="text-ink-muted" />
                        <input id="hero-search" name="q" type="search" placeholder="{{ __('Place, region or route name') }}" class="w-full bg-transparent py-3 text-ink placeholder:text-ink-muted/70 focus:outline-none">
                    </div>
                    <x-button variant="accent" class="px-6 py-3">{{ __('Find routes') }}</x-button>
                </form>
            </div>

            {{-- Illustrated route preview --}}
            <div class="relative mx-auto hidden w-full max-w-md lg:block" aria-hidden="true">
                <div class="rotate-2 rounded-[2rem] bg-paper p-4 shadow-2xl shadow-forest-950/40">
                    <svg viewBox="0 0 400 300" class="w-full rounded-3xl bg-sand-100">
                        <path d="M0 210c60-20 90 10 150-10s90-80 160-70 70 30 90 20V300H0Z" class="fill-forest-100" />
                        <path d="M40 0c20 60-10 90 30 140s110 40 120 100 10 50 0 60" class="stroke-sky-300" stroke-width="10" fill="none" stroke-linecap="round" />
                        <path d="M0 70h400M250 0v300" class="stroke-white" stroke-width="6" />
                        <path d="M70 240c40-40 20-90 80-110s60-70 120-60 70 60 90 30" class="stroke-trail-500" stroke-width="4" fill="none" stroke-dasharray="10 9" stroke-linecap="round" />
                        @foreach ([[70, 240], [150, 130], [270, 70], [360, 100]] as $index => [$x, $y])
                            <circle cx="{{ $x }}" cy="{{ $y }}" r="15" class="fill-forest-700 stroke-paper" stroke-width="3" />
                            <text x="{{ $x }}" y="{{ $y + 5 }}" text-anchor="middle" class="fill-paper font-sans text-[14px] font-bold">{{ $index + 1 }}</text>
                        @endforeach
                    </svg>
                    <div class="px-2 pb-1 pt-4">
                        <p class="font-heading text-xl font-bold text-forest-900">{{ __('Your next route') }}</p>
                        <div class="mt-2 flex flex-wrap gap-x-5 gap-y-1 text-sm text-ink-muted">
                            <span class="inline-flex items-center gap-1.5"><x-icon name="route" class="size-4" /> 4 {{ __('caches') }}</span>
                            <span class="inline-flex items-center gap-1.5"><x-icon name="map" class="size-4" /> {{ __('Your order') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Intro and latest routes, connected by the trail line --}}
    <section class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative grid grid-cols-1 gap-12 pt-20 lg:grid-cols-2 lg:gap-32 lg:pb-8">
            {{-- The trail line leaves the hero and ends at a finish marker, like the last station of a route --}}
            <div class="pointer-events-none absolute left-1/2 top-0 hidden h-[calc(100%+4rem)] w-24 -translate-x-1/2 flex-col items-center lg:flex" aria-hidden="true">
                <x-trail-line class="w-full flex-1 text-trail-500/80" />
                <span class="relative -mt-1 grid size-16 shrink-0 place-items-center rounded-full border-4 border-paper bg-trail-500 text-white shadow-xl shadow-trail-600/40 ring-8 ring-trail-500/15">
                    <x-icon name="flag" class="size-7" />
                    <span class="absolute -inset-2 -z-10 animate-ping rounded-full bg-trail-500/40 [animation-duration:2s] motion-reduce:hidden"></span>
                </span>
                <span class="mt-3 rounded-full bg-trail-500 px-3 py-1 text-xs font-bold uppercase tracking-widest text-white shadow-md">{{ __('Finish') }}</span>
            </div>
            <div class="lg:pt-10">
                <h2 class="text-4xl font-bold">{{ __('Geocaching made easy') }}</h2>
                <div class="mt-5 space-y-4 text-lg leading-relaxed text-ink-muted">
                    <p>{{ __('No more puzzling together single caches on a crowded map. On CacheTrail, geocachers share complete routes, each in the order they recommend.') }}</p>
                    <p>{{ __('Only have an hour and want a nice walk with your kids? Filter by distance, duration, difficulty and means of transport and only see the routes that really fit.') }}</p>
                </div>
            </div>

            <div>
                <h2 class="text-center text-2xl font-bold lg:text-left">{{ __('Recently added') }}</h2>
                @if ($latestTrails->isEmpty())
                    <x-empty-state class="mt-5" :title="__('No routes yet')">
                        {{ __('The first routes will appear here as soon as they are published.') }}
                    </x-empty-state>
                @else
                    <x-trail-card :trail="$latestTrails->first()" class="mt-5" />

                    @if ($latestTrails->count() > 1)
                        <ul class="mt-4 divide-y divide-sand-200 rounded-3xl border border-sand-200 bg-white/70">
                            @foreach ($latestTrails->skip(1) as $trail)
                                <li>
                                    <a href="{{ route('trails.show', $trail) }}" class="flex items-center gap-4 px-5 py-3 transition hover:bg-white">
                                        <x-trail-image :trail="$trail" class="aspect-square w-12 shrink-0 rounded-xl" />
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate font-heading text-lg font-bold text-forest-900">{{ $trail->name }}</span>
                                            <span class="flex items-center gap-3 text-sm text-ink-muted">
                                                <span class="inline-flex items-center gap-1"><x-icon name="clock" class="size-3.5" /> {{ $trail->durationLabel() }}</span>
                                                <x-transport-icons :transportations="$trail->transportations" />
                                            </span>
                                        </span>
                                        <x-icon name="arrow-right" class="size-4 text-forest-500" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <a href="{{ route('trails.index') }}" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                        {{ __('Show all routes') }}
                        <x-icon name="arrow-right" class="size-4" />
                    </a>
                @endif
            </div>
        </div>

        {{-- How it works --}}
        <div class="relative mt-28">
            <h2 class="text-center text-4xl font-bold">{{ __('How CacheTrail works') }}</h2>
            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ([
                    ['icon' => 'compass', 'title' => __('Discover'), 'text' => __('Browse routes by region, length, duration, difficulty and terrain.')],
                    ['icon' => 'map', 'title' => __('Head out'), 'text' => __('Follow the caches in the order the creator recommends, on foot or by bike.')],
                    ['icon' => 'share', 'title' => __('Share'), 'text' => __('Combine existing caches into your own route and show others your favorite tour.')],
                ] as $index => $step)
                    <x-card class="relative bg-white">
                        <span class="absolute right-6 top-5 font-heading text-5xl font-bold text-sand-200">{{ $index + 1 }}</span>
                        <span class="grid size-12 place-items-center rounded-2xl bg-forest-100 text-forest-700">
                            <x-icon :name="$step['icon']" class="size-6" />
                        </span>
                        <h3 class="mt-5 text-2xl font-bold">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-ink-muted">{{ $step['text'] }}</p>
                    </x-card>
                @endforeach
            </div>

            {{-- Example route, so visitors can see a real route before signing up --}}
            @if ($exampleTrail)
                <div class="mt-10 overflow-hidden rounded-[2rem] border border-sand-200 bg-white shadow-[0_12px_32px_-16px_rgb(26_50_38/0.25)]">
                    <div class="grid grid-cols-1 lg:grid-cols-[1.15fr_1fr]">
                        <x-trail-map :markers="$exampleTrail->mapMarkers()" class="aspect-[4/3] w-full rounded-none! border-0! shadow-none! lg:aspect-auto lg:min-h-[26rem]" />
                        <div class="flex flex-col p-6 sm:p-8">
                            <p class="inline-flex items-center gap-2 text-sm font-semibold uppercase tracking-widest text-trail-500">
                                <x-icon name="map" class="size-4" />
                                {{ __('Example route') }}
                            </p>
                            <h3 class="mt-2 text-3xl font-bold leading-tight">{{ $exampleTrail->name }}</h3>
                            <p class="mt-1 text-sm text-ink-muted">{{ __('by :name', ['name' => $exampleTrail->user->name]) }}</p>
                            @if ($excerpt = $exampleTrail->descriptionParagraphs()[0] ?? null)
                                <p class="mt-4 leading-relaxed text-ink">{{ Str::limit($excerpt, 180) }}</p>
                            @endif
                            <x-trail-stats :trail="$exampleTrail" class="mt-6" />
                            <div class="mt-auto flex flex-wrap items-center gap-x-5 gap-y-3 pt-8">
                                <x-button :href="route('trails.example')">
                                    {{ __('View example route') }}
                                    <x-icon name="arrow-right" class="size-4" />
                                </x-button>
                                <span class="text-sm text-ink-muted">{{ __('No account needed') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Popular routes --}}
        <div class="relative mt-28">
            <h2 class="text-center text-4xl font-bold">{{ __('Popular routes') }}</h2>
            @if ($popularTrails->isEmpty())
                <x-empty-state class="mx-auto mt-10 max-w-2xl" icon="heart" :title="__('No popular routes yet')">
                    {{ __('As soon as routes are liked, the community favorites will appear here.') }}
                </x-empty-state>
            @else
                <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach ($popularTrails as $trail)
                        <x-trail-card :trail="$trail" />
                    @endforeach
                </div>
                @auth
                    <p class="mt-6 text-center">
                        <a href="{{ route('trails.index', ['sort' => 'popular']) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                            {{ __('Show all popular routes') }}
                            <x-icon name="arrow-right" class="size-4" />
                        </a>
                    </p>
                @endauth
            @endif
        </div>
    </section>

    {{-- Call to action --}}
    <section class="mx-auto mt-28 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-forest-800 px-6 py-14 text-center sm:px-12">
            <x-trail-line class="absolute -left-6 top-0 h-full w-24 text-trail-500/60" />
            <x-trail-line class="absolute -right-6 top-0 h-full w-24 -scale-x-100 text-forest-500" />
            <div class="relative">
                <h2 class="text-4xl font-bold text-paper">{{ __('Do you know a great tour?') }}</h2>
                <p class="mx-auto mt-4 max-w-xl text-lg text-forest-100/90">
                    {{ __('Create your own route from existing geocaches and let others enjoy it as much as you did.') }}
                </p>
                <x-button variant="accent" :href="auth()->check() ? route('trails.create') : route('register')" class="mt-8 px-7 py-3 text-base">
                    {{ __('Create route') }}
                    <x-icon name="arrow-right" class="size-4" />
                </x-button>
            </div>
        </div>
    </section>
</x-layouts.app>
