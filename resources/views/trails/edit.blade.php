<x-layouts.app :title="__('Edit route')">
    <x-page-header :title="__('Edit route')" :eyebrow="$trail->name">
        <x-slot:below>
            <x-editor-steps :current="1" :trail="$trail" />
        </x-slot:below>
    </x-page-header>

    <div class="mx-auto -mt-20 max-w-3xl space-y-8 px-4 sm:px-6 lg:px-8">
        <x-card class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold">{{ __('Caches') }}</h2>
                <p class="mt-1 text-sm text-ink-muted">{{ trans_choice(':count cache in this route.|:count caches in this route.', $trail->cache_count) }}</p>
            </div>
            <x-button variant="secondary" :href="route('trails.caches', $trail)">
                <x-icon name="route" class="size-4" />
                {{ __('Edit caches') }}
            </x-button>
        </x-card>

        <x-card>
            <form method="POST" action="{{ route('trails.update', $trail) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('trails._form')

                @can('publish', $trail)
                    <div class="mt-6 max-w-sm">
                        <x-select name="visibility" :label="__('Visibility')" :value="$trail->visibility->value"
                            :options="collect(App\Enums\TrailVisibility::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()" />
                        <p class="mt-1.5 text-sm text-ink-muted">{{ __('Drafts and hidden routes are only visible to you.') }}</p>
                    </div>
                @else
                    <input type="hidden" name="visibility" value="{{ App\Enums\TrailVisibility::Hidden->value }}">
                    <p class="mt-6 rounded-2xl bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ __('The moderation team has hidden this route. Only they can make it visible again.') }}</p>
                @endcan

                <div class="mt-8 flex flex-col-reverse gap-3 border-t border-sand-200 pt-6 sm:flex-row sm:justify-end">
                    <x-button variant="secondary" :href="route('trails.show', $trail)">{{ __('Cancel') }}</x-button>
                    <x-button>{{ __('Save changes') }}</x-button>
                </div>
            </form>
        </x-card>

        <x-card class="border-red-200">
            <h2 class="text-2xl font-bold text-red-800">{{ __('Delete route') }}</h2>
            <p class="mt-1 text-sm text-ink-muted">{{ __('The route will be permanently deleted. This cannot be undone.') }}</p>
            <form method="POST" action="{{ route('trails.destroy', $trail) }}" class="mt-5">
                @csrf
                @method('DELETE')
                <label class="mb-4 flex items-center gap-2 text-sm text-ink-muted">
                    <input type="checkbox" required class="size-4 rounded border-sand-300 accent-red-700">
                    {{ __('Yes, I want to delete this route permanently.') }}
                </label>
                <x-button variant="danger">
                    <x-icon name="trash" class="size-4" />
                    {{ __('Delete route') }}
                </x-button>
            </form>
        </x-card>
    </div>
</x-layouts.app>
