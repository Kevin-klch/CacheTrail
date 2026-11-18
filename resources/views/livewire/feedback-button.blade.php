<div x-data x-on:keydown.escape.window="if ($wire.open) $wire.toggle()" class="print:hidden">
    @if ($open)
        <div class="fixed inset-x-4 bottom-20 z-40 max-h-[calc(100vh-7rem)] overflow-y-auto rounded-3xl border border-sand-200 bg-white p-5 shadow-2xl shadow-forest-950/20 sm:inset-x-auto sm:bottom-24 sm:right-6 sm:w-[26rem]"
            role="dialog" aria-labelledby="feedback-title">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="feedback-title" class="text-xl font-bold">{{ __('Your feedback') }}</h2>
                    <p class="mt-1 text-sm text-ink-muted">{{ __('CacheTrail is still being built. What works, what is missing, what bothers you?') }}</p>
                </div>
                <button type="button" wire:click="toggle" class="-mr-1 -mt-1 grid size-9 shrink-0 place-items-center rounded-full text-ink-muted transition hover:bg-sand-100 hover:text-ink">
                    <x-icon name="close" class="size-5" />
                    <span class="sr-only">{{ __('Close') }}</span>
                </button>
            </div>

            @if ($sent)
                <div class="mt-5 rounded-2xl bg-forest-50 px-4 py-6 text-center">
                    <x-icon name="check-circle" class="mx-auto size-8 text-forest-700" />
                    <p class="mt-2 font-semibold text-forest-900">{{ __('Thank you for your feedback!') }}</p>
                    <p class="mt-1 text-sm text-ink-muted">{{ __('Every message is read and helps to make CacheTrail better.') }}</p>
                    <button type="button" wire:click="$set('sent', false)" class="mt-4 text-sm font-semibold text-forest-700 hover:underline">{{ __('Send more feedback') }}</button>
                </div>
            @else
                <form wire:submit="submit" class="mt-5 space-y-4">
                    <fieldset>
                        <legend class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Topic') }}</legend>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach (App\Enums\FeedbackCategory::cases() as $option)
                                <label class="flex cursor-pointer items-center gap-2 rounded-2xl border border-sand-300 bg-white px-3 py-2.5 text-sm font-medium leading-tight transition hover:border-forest-500 has-checked:border-forest-700 has-checked:bg-forest-700 has-checked:text-paper has-focus-visible:ring-4 has-focus-visible:ring-forest-500/25">
                                    <input type="radio" wire:model="category" value="{{ $option->value }}" class="sr-only">
                                    <x-icon :name="$option->icon()" class="size-4" />
                                    {{ $option->label() }}
                                </label>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </fieldset>

                    <x-textarea name="message" id="feedback-message" wire:model="message" :label="__('Message')" rows="5" :maxlength="App\Livewire\FeedbackButton::MESSAGE_MAX_LENGTH"
                        :placeholder="__('e.g. On the route page I would like to see ...')" />

                    @if ($this->asksForContact())
                        <x-input name="contact" id="feedback-contact" type="email" wire:model="contact" :label="__('Email address (optional)')" maxlength="255"
                            :hint="__('Only if you would like an answer. It is used for nothing else.')" />
                    @endif

                    <div class="hidden" aria-hidden="true">
                        <label for="feedback-website">Website</label>
                        <input id="feedback-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="flex items-center justify-between gap-3">
                        <p class="text-xs text-ink-muted">{{ __('The address of the current page is sent along.') }}</p>
                        <x-button class="shrink-0 px-5 py-2.5" wire:loading.attr="disabled" wire:target="submit">
                            <x-icon name="mail" class="size-4" />
                            {{ __('Send') }}
                        </x-button>
                    </div>
                </form>
            @endif
        </div>
    @endif

    <button type="button" wire:click="toggle" aria-expanded="{{ $open ? 'true' : 'false' }}"
        class="fixed bottom-4 right-4 z-40 inline-flex items-center gap-2 rounded-full bg-trail-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-forest-950/25 transition hover:bg-trail-600 focus:outline-none focus-visible:ring-4 focus-visible:ring-trail-300 sm:bottom-6 sm:right-6">
        <x-icon :name="$open ? 'close' : 'mail'" class="size-4" />
        {{ __('Feedback') }}
    </button>
</div>
