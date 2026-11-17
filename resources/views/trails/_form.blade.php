{{-- Shared fields for creating and editing a trail. Expects $trail. --}}
<div class="space-y-6">
    <x-input name="name" :label="__('Route name')" :value="$trail->name" maxlength="120" required :placeholder="__('e.g. Forest adventure on the Lower Rhine')" />

    <x-textarea name="description" :label="__('Description')" :value="$trail->description" rows="8" maxlength="10000" required
        :hint="__('Describe what makes the route special, where to park and what to bring. Blank lines create new paragraphs.')" />

    <fieldset>
        <legend class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Estimated duration') }}</legend>
        <div class="grid max-w-sm grid-cols-2 gap-3">
            <div class="relative">
                <label for="duration_hours" class="sr-only">{{ __('Hours') }}</label>
                <input id="duration_hours" name="duration_hours" type="number" min="0" max="72" required
                    value="{{ old('duration_hours', $trail->exists ? $trail->durationHours() : null) }}"
                    class="block w-full rounded-xl border border-sand-300 bg-white py-2.5 pl-4 pr-14 text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-ink-muted">{{ __('hrs.') }}</span>
            </div>
            <div class="relative">
                <label for="duration_minutes" class="sr-only">{{ __('Minutes') }}</label>
                <input id="duration_minutes" name="duration_minutes" type="number" min="0" max="59" step="5" required
                    value="{{ old('duration_minutes', $trail->exists ? $trail->durationRemainingMinutes() : 0) }}"
                    class="block w-full rounded-xl border border-sand-300 bg-white py-2.5 pl-4 pr-14 text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-ink-muted">{{ __('min.') }}</span>
            </div>
        </div>
        @error('duration_hours')
            <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
        @enderror
        @error('duration_minutes')
            <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-sm text-ink-muted">{{ __('Include the time for searching the caches, not just the walking or riding time.') }}</p>
    </fieldset>

    <fieldset>
        <legend class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Means of transport') }}</legend>
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach (App\Enums\Transportation::cases() as $option)
                <label class="relative flex cursor-pointer items-center gap-3 rounded-2xl border border-sand-300 bg-white px-4 py-3 transition has-checked:border-forest-600 has-checked:bg-forest-50 has-checked:ring-2 has-checked:ring-forest-600/20 has-focus-visible:ring-4 has-focus-visible:ring-forest-500/25">
                    <input type="radio" name="transportation" value="{{ $option->value }}" class="sr-only" required
                        @checked(old('transportation', $trail->transportation?->value) === $option->value)>
                    <x-transport-icons :transportation="$option" class="text-forest-700" />
                    <span class="text-sm font-medium">{{ $option->label() }}</span>
                </label>
            @endforeach
        </div>
        @error('transportation')
            <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
        @enderror
    </fieldset>

    <div>
        <span class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Cover image') }}</span>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <x-trail-image :trail="$trail" class="aspect-[16/9] w-full shrink-0 rounded-2xl sm:w-56" />
            <div class="space-y-3">
                <label for="image" class="sr-only">{{ __('Choose cover image') }}</label>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-ink-muted file:mr-4 file:rounded-full file:border-0 file:bg-forest-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-forest-800 hover:file:bg-forest-200">
                <p class="text-sm text-ink-muted">{{ __('JPG, PNG or WebP, up to 5 MB. Optional: without an image, the route gets a placeholder.') }}</p>
                @if ($trail->image_path)
                    <label class="inline-flex items-center gap-2 text-sm text-ink-muted">
                        <input type="hidden" name="remove_image" value="0">
                        <input type="checkbox" name="remove_image" value="1" class="size-4 rounded border-sand-300 accent-forest-700">
                        {{ __('Remove current image') }}
                    </label>
                @endif
                @error('image')
                    <p class="text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-sand-100/70 px-4 py-3 text-sm text-ink-muted">
        <x-icon name="route" class="mr-1 inline size-4 align-text-bottom text-forest-600" />
        {{ __('Adding geocaches by GC code, the order and the map will follow in the next development step.') }}
    </div>
</div>
