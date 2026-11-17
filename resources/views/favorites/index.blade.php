<x-layouts.app :title="__('Favorites')">
    <x-page-header :title="__('Favorites')" :eyebrow="__('Your collection')">
        {{ __('Routes you have saved for later.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        @if ($trails->isEmpty())
            <x-card>
                <x-empty-state icon="heart" :title="__('No favorites yet')">
                    {{ __('Save routes you like with the heart and find them here again.') }}
                    <div class="mt-5">
                        <x-button :href="route('trails.index')">{{ __('Discover routes') }}</x-button>
                    </div>
                </x-empty-state>
            </x-card>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($trails as $trail)
                    <x-trail-card :trail="$trail" />
                @endforeach
            </div>
            <div class="mt-10">{{ $trails->links() }}</div>
        @endif
    </div>
</x-layouts.app>
