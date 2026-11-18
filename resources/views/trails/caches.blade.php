<x-layouts.app :title="__('Caches of :name', ['name' => $trail->name])">
    <div class="slant-bottom bg-forest-700 pb-28 pt-10 text-paper sm:pt-14">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Route editor') }}</p>
            <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ $trail->name }}</h1>
            <x-editor-steps :current="2" :trail="$trail" />
        </div>
    </div>

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        @error('publish')
            <x-alert type="info" class="mb-6">{{ $message }}</x-alert>
        @enderror

        @if (session('status') === 'trail-created')
            <x-alert class="mb-6">{{ __('Your route has been saved as a draft. Now add the caches.') }}</x-alert>
        @endif

        <livewire:trail-cache-editor :trail="$trail" />
    </div>
</x-layouts.app>
