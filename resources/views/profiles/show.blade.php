<x-layouts.app :title="$profile->name">
    <div class="rounded-b-[2.5rem] bg-forest-700 pb-28 pt-10 text-paper sm:rounded-b-[4rem] sm:pt-14">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-6 px-4 sm:px-6 lg:px-8">
            <span class="grid size-20 shrink-0 place-items-center rounded-full bg-paper font-serif text-4xl font-bold text-forest-800" aria-hidden="true">
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

                @if ($lastCompleted)
                    <div class="mt-6 flex items-center gap-3 rounded-2xl bg-forest-50 px-4 py-3">
                        <x-icon name="check-circle" class="size-6 shrink-0 text-forest-600" />
                        <p class="text-sm text-ink">
                            {{ __('Last completed route:') }}
                            <a href="{{ route('trails.show', $lastCompleted) }}" class="font-semibold text-forest-800 hover:underline">{{ $lastCompleted->name }}</a>
                            <span class="text-ink-muted">· {{ \Illuminate\Support\Carbon::parse($lastCompleted->pivot->completed_at)->translatedFormat('j. F Y') }}</span>
                        </p>
                    </div>
                @endif
            </x-card>

            {{-- Figures --}}
            <x-card class="p-5 sm:p-6">
                <h2 class="sr-only">{{ __('Figures') }}</h2>
                <dl>
                    <x-stat :label="__('Geocaches found')" icon="compass">{{ $account?->finds_count !== null ? number_format($account->finds_count, 0, ',', '.') : '–' }}</x-stat>
                    <x-stat :label="__('Geocacher since')" icon="calendar">{{ $account?->member_since?->translatedFormat('F Y') ?? '–' }}</x-stat>
                    <x-stat :label="__('On CacheTrail since')" icon="calendar">{{ $profile->created_at->translatedFormat('F Y') }}</x-stat>
                    <x-stat :label="__('Completed routes')" icon="check-circle">{{ $completedCount }}</x-stat>
                    <x-stat :label="__('Published routes')" icon="route">{{ $trails->total() }}</x-stat>
                    <x-stat :label="__('Likes received')" icon="heart">{{ $likesReceived }}</x-stat>
                </dl>
                @unless ($account)
                    <p class="mt-4 border-t border-sand-200 pt-4 text-xs text-ink-muted">{{ __('Geocaching.com figures appear once the account is connected.') }}</p>
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
