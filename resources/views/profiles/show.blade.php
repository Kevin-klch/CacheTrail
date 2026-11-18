<x-layouts.app :title="$profile->name">
    <div @class([
        'relative isolate overflow-hidden rounded-b-[2.5rem] bg-forest-700 pb-28 text-paper sm:rounded-b-[4rem]',
        'pt-10 sm:pt-14' => ! $profile->coverUrl(),
        'pt-32 sm:pt-44' => $profile->coverUrl(),
    ])>
        @if ($profile->coverUrl())
            <img src="{{ $profile->coverUrl() }}" alt="" class="absolute inset-0 -z-10 size-full object-cover">
            {{-- Keeps the name readable on bright images --}}
            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest-900/90 via-forest-900/45 to-forest-900/10"></div>
        @endif
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-6 px-4 sm:px-6 lg:px-8">
            <x-avatar :user="$profile" class="size-24 text-4xl ring-4 ring-paper/90 shadow-xl" />
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Geocacher') }}</p>
                <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ $profile->name }}</h1>
                @if ($account)
                    <p class="mt-2 inline-flex items-center gap-1.5 text-forest-100/90">
                        <x-icon name="compass" class="size-4" />
                        {{ __('On Geocaching.com: :name', ['name' => $account->username]) }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="relative mx-auto -mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_26rem]">
            {{-- Info box --}}
            <x-card>
                <h2 class="text-2xl font-bold">{{ __('About :name', ['name' => $profile->name]) }}</h2>
                @if ($profile->bio)
                    <p class="mt-3 whitespace-pre-line break-words leading-relaxed text-ink">{{ $profile->bio }}</p>
                @else
                    <p class="mt-3 text-ink-muted">{{ __(':name has not written anything about themselves yet.', ['name' => $profile->name]) }}</p>
                @endif

                {{-- Most recently completed routes --}}
                <section class="mt-8 border-t border-sand-200 pt-6">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <h3 class="flex items-center gap-2 text-xl font-bold">
                            <x-icon name="check-circle" class="text-forest-600" />
                            {{ __('Recently completed') }}
                        </h3>
                        @if ($completedCount > 0)
                            <span class="text-sm text-ink-muted">{{ trans_choice(':count route completed in total|:count routes completed in total', $completedCount) }}</span>
                        @endif
                    </div>

                    @if ($recentCompletions->isEmpty())
                        <p class="mt-3 text-sm text-ink-muted">{{ __(':name has not completed any routes yet.', ['name' => $profile->name]) }}</p>
                    @else
                        <ul class="mt-4 divide-y divide-sand-200 overflow-hidden rounded-2xl border border-sand-200 bg-white">
                            @foreach ($recentCompletions as $completed)
                                <li>
                                    <a href="{{ route('trails.show', $completed) }}" class="flex items-center gap-4 px-4 py-3 transition hover:bg-forest-50/60">
                                        <x-trail-image :trail="$completed" class="aspect-square w-12 shrink-0 rounded-xl" />
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate font-heading text-lg font-bold text-forest-900">{{ $completed->name }}</span>
                                            <span class="flex items-center gap-1.5 text-sm text-ink-muted">
                                                <x-icon name="calendar" class="size-3.5" />
                                                {{ __('Completed on :date', ['date' => \Illuminate\Support\Carbon::parse($completed->pivot->completed_at)->translatedFormat('j. F Y')]) }}
                                            </span>
                                        </span>
                                        <x-icon name="arrow-right" class="size-4 text-forest-500" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </x-card>

            {{-- Figures --}}
            <x-card class="p-5 sm:p-6">
                <h2 class="sr-only">{{ __('Figures') }}</h2>
                <dl>
                    <x-stat :label="__('Geocaches found')" icon="compass">{{ $account?->finds_count !== null ? number_format($account->finds_count, 0, ',', '.') : '–' }}</x-stat>
                    <x-stat :label="__('Geocacher since')" icon="calendar"><span class="whitespace-nowrap">{{ $account?->member_since?->translatedFormat('F Y') ?? '–' }}</span></x-stat>
                    <x-stat :label="__('On CacheTrail since')" icon="calendar"><span class="whitespace-nowrap">{{ $profile->created_at->translatedFormat('F Y') }}</span></x-stat>
                    <x-stat :label="__('Completed routes')" icon="check-circle">{{ $completedCount }}</x-stat>
                    <x-stat :label="__('Published routes')" icon="route">{{ $trails->total() }}</x-stat>
                    <x-stat :label="__('Likes received')" icon="heart">{{ $likesReceived }}</x-stat>
                </dl>
                @unless ($account)
                    <p class="border-t border-sand-200 pt-4 text-xs text-ink-muted">{{ __('Geocaching.com figures appear once the account is connected.') }}</p>
                @endunless
            </x-card>
        </div>

        {{-- Badges --}}
        <x-card class="mt-6">
            <div class="flex flex-wrap items-baseline justify-between gap-2">
                <h2 class="flex items-center gap-2 text-2xl font-bold">
                    <x-icon name="award" class="text-forest-600" />
                    {{ __('Badges') }}
                </h2>
                @if ($badges->isNotEmpty())
                    <span class="text-sm text-ink-muted">{{ trans_choice(':count badge|:count badges', $badges->count()) }}</span>
                @endif
            </div>

            @if ($badges->isEmpty())
                <p class="mt-3 text-ink-muted">
                    @if (auth()->user()->is($profile))
                        {{ __('Complete, rate or publish routes to earn your first badges.') }}
                    @else
                        {{ __(':name has not earned any badges yet.', ['name' => $profile->name]) }}
                    @endif
                </p>
            @else
                <ul class="mt-6 grid grid-cols-1 gap-x-6 gap-y-7 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($badges as ['badge' => $earned, 'isNew' => $isNew])
                        <li class="flex items-center gap-4">
                            <x-badge-medal :badge="$earned->badge" :level="$earned->level" size="lg" />
                            <div class="min-w-0">
                                <p class="flex flex-wrap items-center gap-2 font-heading text-lg font-bold leading-tight text-forest-900">
                                    {{ $earned->name() }}
                                    @if ($isNew)
                                        <span class="rounded-full bg-trail-500 px-2 py-0.5 font-sans text-[0.65rem] font-bold uppercase tracking-wider text-white">{{ __('New') }}</span>
                                    @endif
                                </p>
                                <p class="text-sm text-ink">{{ $earned->description() }}</p>
                                <p class="text-xs text-ink-muted">{{ __('Earned on :date', ['date' => $earned->earned_at->translatedFormat('j. F Y')]) }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-card>

        <h2 class="mt-12 text-3xl font-bold">{{ __('Routes by :name', ['name' => $profile->name]) }}</h2>

        @if ($trails->isEmpty())
            <x-empty-state class="mt-6" :title="__('No routes yet')">
                {{ __(':name has not published any routes yet.', ['name' => $profile->name]) }}
            </x-empty-state>
        @else
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($trails as $trail)
                    <x-trail-card :trail="$trail" />
                @endforeach
            </div>
            <div class="mt-10">{{ $trails->links() }}</div>
        @endif
    </div>
</x-layouts.app>
