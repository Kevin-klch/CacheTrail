<x-layouts.app :title="$profile->name">
    <div class="rounded-b-[2.5rem] bg-forest-700 pb-28 pt-10 text-paper sm:rounded-b-[4rem] sm:pt-14">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-6 px-4 sm:px-6 lg:px-8">
            <span class="grid size-20 shrink-0 place-items-center rounded-full bg-paper font-serif text-4xl font-bold text-forest-800" aria-hidden="true">
                {{ mb_strtoupper(mb_substr($profile->name, 0, 1)) }}
            </span>
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Geocacher') }}</p>
                <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ $profile->name }}</h1>
                <p class="mt-2 text-forest-100/90">{{ __('Member since :date', ['date' => $profile->created_at->translatedFormat('F Y')]) }}</p>
            </div>
        </div>
    </div>

    <div class="mx-auto -mt-16 max-w-7xl px-4 sm:px-6 lg:px-8">
        <dl class="grid max-w-xl grid-cols-2 gap-4">
            <x-card class="p-5 sm:p-5">
                <dt class="text-sm text-ink-muted">{{ __('Published routes') }}</dt>
                <dd class="mt-1 font-serif text-3xl font-bold text-forest-900">{{ $trails->total() }}</dd>
            </x-card>
            <x-card class="p-5 sm:p-5">
                <dt class="text-sm text-ink-muted">{{ __('Likes received') }}</dt>
                <dd class="mt-1 font-serif text-3xl font-bold text-forest-900">{{ $likesReceived }}</dd>
            </x-card>
        </dl>

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
