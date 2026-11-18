<x-layouts.app :title="__('About CacheTrail')" :description="__('Why CacheTrail exists, how routes work and what geocaching is.')">
    <x-page-header :title="__('About CacheTrail')" :eyebrow="__('The project')">
        {{ __('Complete geocaching routes from the community, instead of single caches on a crowded map.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-4xl space-y-8 px-4 sm:px-6 lg:px-8">
        {{-- Why --}}
        <x-card>
            <h2 class="text-3xl font-bold">{{ __('Why CacheTrail exists') }}</h2>
            <div class="mt-4 space-y-4 text-lg leading-relaxed text-ink">
                <p>{{ __('Anyone who has opened the geocaching map knows the problem: hundreds of caches, but which ones make a nice tour together? Which order makes sense, how long will it take, and do you need a torch or tweezers?') }}</p>
                <p>{{ __('Experienced geocachers often know the answer: their favourite round through the woods, the bike tour along the river, the short walk for the kids. CacheTrail is the place to share exactly these tours, so others can simply head out.') }}</p>
            </div>
        </x-card>

        {{-- How it works --}}
        <x-card>
            <h2 class="text-3xl font-bold">{{ __('How it works') }}</h2>
            <ol class="mt-6 space-y-5">
                @foreach ([
                    ['compass', __('Find a route'), __('Search by place or region and filter by duration, length, difficulty, terrain and means of transport.')],
                    ['route', __('Follow the caches in order'), __('Every route lists its caches in the order the creator recommends, on a map and with all key figures.')],
                    ['check-circle', __('Log your finds on Geocaching.com'), __('The caches themselves are on Geocaching.com. You log your finds there as usual; on CacheTrail you can mark the route as completed and rate it.')],
                    ['share', __('Share your own route'), __('Put existing caches together into your own route and show others your favourite tour.')],
                ] as $index => [$icon, $title, $text])
                    <li class="flex gap-4">
                        <span class="grid size-11 shrink-0 place-items-center rounded-2xl bg-forest-100 text-forest-700">
                            <x-icon :name="$icon" class="size-5" />
                        </span>
                        <div>
                            <h3 class="text-xl font-bold">{{ $index + 1 }}. {{ $title }}</h3>
                            <p class="mt-1 leading-relaxed text-ink-muted">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </x-card>

        {{-- New to geocaching --}}
        <x-card id="neu" class="border-trail-400/40! bg-[#fbebe4]!">
            <h2 class="text-3xl font-bold">{{ __('New to geocaching?') }}</h2>
            <div class="mt-4 space-y-4 text-lg leading-relaxed text-ink">
                <p>{{ __('Geocaching is a worldwide treasure hunt: people hide small containers, the caches, and publish their coordinates. Others look for them with a smartphone or GPS device, write their name in the logbook and put the cache back exactly where it was.') }}</p>
                <p>{{ __('There are millions of caches, in forests, in cities and probably just around the corner from you. All you need is a free account on Geocaching.com, the official platform of geocaching.') }}</p>
            </div>
            <x-button variant="accent" href="https://www.geocaching.com/play" target="_blank" rel="noopener noreferrer" class="mt-6">
                {{ __('Get started on Geocaching.com') }}
                <x-icon name="external" class="size-4" />
            </x-button>
        </x-card>

        {{-- Good to know --}}
        <x-card>
            <h2 class="text-3xl font-bold">{{ __('Good to know') }}</h2>
            <ul class="mt-4 space-y-3 leading-relaxed text-ink">
                <li class="flex gap-3"><x-icon name="check" class="mt-1 size-4 shrink-0 text-forest-600" />{{ __('CacheTrail does not host any caches. Cache details, hints and logs stay on Geocaching.com.') }}</li>
                <li class="flex gap-3"><x-icon name="check" class="mt-1 size-4 shrink-0 text-forest-600" />{{ __('CacheTrail is an independent community project and is not affiliated with Geocaching HQ.') }}</li>
                <li class="flex gap-3"><x-icon name="check" class="mt-1 size-4 shrink-0 text-forest-600" />{{ __('Until the connection to Geocaching.com is approved, the caches on CacheTrail are fictional test data.') }}</li>
            </ul>
        </x-card>

        <div class="flex flex-wrap justify-center gap-3 pt-2">
            <x-button :href="route('trails.example')">
                {{ __('View example route') }}
                <x-icon name="arrow-right" class="size-4" />
            </x-button>
            @guest
                <x-button variant="secondary" :href="route('register')">{{ __('Sign up for free') }}</x-button>
            @else
                <x-button variant="secondary" :href="route('trails.index')">{{ __('Discover all routes') }}</x-button>
            @endguest
        </div>
    </div>
</x-layouts.app>
