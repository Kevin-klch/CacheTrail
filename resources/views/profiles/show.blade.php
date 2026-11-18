<x-layouts.app :title="$profile->name">
    <div class="rounded-b-[2.5rem] bg-forest-700 pb-28 pt-10 text-paper sm:rounded-b-[4rem] sm:pt-14">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-6 px-4 sm:px-6 lg:px-8">
            <span class="grid size-20 shrink-0 place-items-center rounded-full bg-paper font-heading text-4xl font-bold text-forest-800" aria-hidden="true">
                {{ mb_strtoupper(mb_substr($profile->name, 0, 1)) }}
            </span>
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

    <div class="mx-auto -mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_22rem]">
            {{-- Info box --}}
            <x-card>
                <h2 class="text-2xl font-bold">{{ __('About :name', ['name' => $profile->name]) }}</h2>
                @if ($profile->bio)
                    <p class="mt-3 whitespace-pre-line break-words leading-relaxed text-ink">{{ $profile->bio }}</p>
                @else
                    <p class="mt-3 text-ink-muted">{{ __(':name has not written anything about themselves yet.', ['name' => $profile->name]) }}</p>
                @endif
                @if (auth()->user()->is($profile))
                    <a href="{{ route('profile.edit') }}#infobox" class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-forest-700 hover:underline">
                        <x-icon name="edit" class="size-4" />
                        {{ __('Edit info box') }}
                    </a>
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
