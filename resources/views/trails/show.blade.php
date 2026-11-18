<x-layouts.app :title="$trail->name" :description="Str::limit($trail->descriptionParagraphs()[0] ?? '', 155)">
    <div class="slant-bottom bg-forest-700 pb-40 pt-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <a href="{{ route('trails.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-forest-100 hover:text-white">
                <x-icon name="arrow-right" class="size-4 rotate-180" />
                {{ __('All routes') }}
            </a>
        </div>
    </div>

    <div class="mx-auto -mt-36 max-w-6xl px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <x-alert class="mb-6">
                {{ match (session('status')) {
                    'trail-published' => __('Your route has been published.'),
                    'trail-updated' => __('Your changes have been saved.'),
                    default => '',
                } }}
            </x-alert>
        @endif

        @include('trails._details')
    </div>
</x-layouts.app>
