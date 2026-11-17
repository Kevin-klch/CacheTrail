<div class="space-y-8">
    {{-- Step 2: add caches --}}
    <x-card>
        <h2 class="text-2xl font-bold">{{ __('Add geocaches') }}</h2>
        <p class="mt-1 text-sm text-ink-muted">{{ __('Enter one or more GC codes, separated by spaces, commas or new lines. The caches are added to the end of the route.') }}</p>

        <form wire:submit="addCaches" class="mt-5 space-y-4">
            <div>
                <label for="codes" class="sr-only">{{ __('GC codes') }}</label>
                <textarea id="codes" wire:model="codes" rows="3" placeholder="GC12345&#10;GC23456"
                    class="block w-full rounded-xl border border-sand-300 bg-white px-4 py-3 font-mono uppercase text-ink shadow-xs placeholder:normal-case placeholder:text-ink-muted/50 focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15"></textarea>
                @error('codes')
                    <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <x-button wire:loading.attr="disabled" wire:target="addCaches">
                <x-icon name="plus" class="size-4" />
                <span wire:loading.remove wire:target="addCaches">{{ __('Add caches') }}</span>
                <span wire:loading wire:target="addCaches">{{ __('Looking up caches …') }}</span>
            </x-button>
        </form>

        @if ($added !== [])
            <x-alert class="mt-5">{{ trans_choice(':count cache added.|:count caches added.', count($added)) }}</x-alert>
        @endif

        @if ($failures !== [])
            <div class="mt-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3" role="alert">
                <p class="text-sm font-semibold text-red-800">{{ trans_choice('This code could not be added:|These codes could not be added:', count($failures)) }}</p>
                <ul class="mt-2 space-y-1 text-sm text-red-800">
                    @foreach ($failures as $code => $message)
                        <li><span class="font-mono font-semibold">{{ $code }}</span> – {{ $message }}</li>
                    @endforeach
                </ul>
                <p class="mt-2 text-xs text-red-700/80">{{ __('The codes are still in the input field so you can correct them.') }}</p>
            </div>
        @endif
    </x-card>

    {{-- Step 3: order --}}
    <x-card>
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h2 class="text-2xl font-bold">{{ __('Order of the stations') }}</h2>
            <p class="text-sm text-ink-muted">{{ trans_choice(':count cache|:count caches', $caches->count()) }}</p>
        </div>
        <p class="mt-1 text-sm text-ink-muted">{{ __('Drag the caches into the order you recommend, or use the arrows. The order is never changed automatically.') }}</p>

        @if ($caches->isEmpty())
            <x-empty-state class="mt-6" :title="__('No caches yet')">
                {{ __('Add the first caches of your route using their GC codes.') }}
            </x-empty-state>
        @else
            <ol wire:sort="sortCache" class="mt-6 space-y-2">
                @foreach ($caches as $cache)
                    <li wire:key="cache-{{ $cache->id }}" wire:sort:item="{{ $cache->id }}"
                        class="flex items-start gap-3 rounded-2xl border border-sand-200 bg-white p-3 shadow-xs sm:items-center">
                        <button type="button" wire:sort:handle class="hidden cursor-grab touch-none rounded-lg p-1 text-ink-muted hover:bg-sand-100 active:cursor-grabbing sm:block" title="{{ __('Drag to reorder') }}">
                            <x-icon name="grip" />
                            <span class="sr-only">{{ __('Drag to reorder') }}</span>
                        </button>

                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-forest-700 text-sm font-bold text-paper">{{ $loop->iteration }}</span>

                        <x-cache-details :cache="$cache" class="flex-1" />

                        <div class="flex shrink-0 items-center gap-1">
                            <button type="button" wire:click="moveUp({{ $cache->id }})" @disabled($loop->first)
                                class="rounded-lg p-1.5 text-forest-700 hover:bg-forest-50 disabled:opacity-30 disabled:hover:bg-transparent">
                                <x-icon name="chevron-up" />
                                <span class="sr-only">{{ __('Move up') }}</span>
                            </button>
                            <button type="button" wire:click="moveDown({{ $cache->id }})" @disabled($loop->last)
                                class="rounded-lg p-1.5 text-forest-700 hover:bg-forest-50 disabled:opacity-30 disabled:hover:bg-transparent">
                                <x-icon name="chevron-down" />
                                <span class="sr-only">{{ __('Move down') }}</span>
                            </button>
                            <button type="button" wire:click="removeCache({{ $cache->id }})"
                                class="rounded-lg p-1.5 text-red-700 hover:bg-red-50">
                                <x-icon name="trash" />
                                <span class="sr-only">{{ __('Remove :code', ['code' => $cache->cache_code]) }}</span>
                            </button>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($usesExampleData)
            <p class="mt-4 text-xs text-ink-muted">{{ __('Example data: the caches currently come from a test data set, not from Geocaching.com.') }}</p>
        @endif
    </x-card>

    {{-- Finish --}}
    <x-card class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        @if ($trail->isPublished())
            <p class="text-sm text-ink-muted">{{ __('This route is published. Changes to the caches are visible immediately.') }}</p>
            <x-button :href="route('trails.show', $trail)">{{ __('Done') }}</x-button>
        @else
            <div>
                <p class="text-sm text-ink-muted">{{ __('A route needs at least :count caches to be published.', ['count' => App\Models\Trail::minCachesToPublish()]) }}</p>
                @error('publish')
                    <p class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col-reverse gap-3 sm:flex-row">
                <x-button variant="secondary" :href="route('trails.show', $trail)">{{ __('Keep as draft') }}</x-button>
                <x-button type="button" wire:click="publish" :disabled="$caches->count() < App\Models\Trail::minCachesToPublish()">{{ __('Publish route') }}</x-button>
            </div>
        @endif
    </x-card>
</div>
