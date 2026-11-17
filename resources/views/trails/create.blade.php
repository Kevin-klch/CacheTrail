<x-layouts.app :title="__('Create route')">
    <x-page-header :title="__('Create route')" :eyebrow="__('Route editor')">
        {{ __('Combine existing geocaches into a route and share it with the community.') }}
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl px-4 sm:px-6 lg:px-8">
        <x-card>
            <form method="POST" action="{{ route('trails.store') }}" enctype="multipart/form-data">
                @csrf

                @include('trails._form')

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-sand-200 pt-6 sm:flex-row sm:justify-end">
                    <x-button variant="secondary" name="intent" value="draft">{{ __('Save as draft') }}</x-button>
                    <x-button name="intent" value="publish">{{ __('Publish route') }}</x-button>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>
