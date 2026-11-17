<x-layouts.app :title="__('Create route')">
    <x-page-header :title="__('Create route')" :eyebrow="__('Route editor')">
        {{ __('Combine existing geocaches into a route and share it with the community.') }}

        <x-slot:below>
            <x-editor-steps :current="1" />
        </x-slot:below>
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-card>
            <form method="POST" action="{{ route('trails.store') }}" enctype="multipart/form-data">
                @csrf

                @include('trails._form')

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-sand-200 pt-6 sm:flex-row sm:justify-end">
                    <p class="text-sm text-ink-muted sm:mr-auto sm:self-center">{{ __('The route is saved as a draft for now.') }}</p>
                    <x-button>
                        {{ __('Continue to the caches') }}
                        <x-icon name="arrow-right" class="size-4" />
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
