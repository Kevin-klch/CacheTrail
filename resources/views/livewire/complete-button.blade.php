<div class="flex items-center gap-3">
    @if ($completedDate)
        <span class="inline-flex items-center gap-2 rounded-full bg-forest-100 px-4 py-2.5 text-sm font-semibold text-forest-800">
            <x-icon name="check-circle" class="size-5" />
            {{ __('Completed on :date', ['date' => $completedDate->translatedFormat('j. F Y')]) }}
        </span>
        <button type="button" wire:click="toggle" class="text-sm font-medium text-ink-muted hover:text-forest-800 hover:underline">{{ __('Undo') }}</button>
    @else
        <x-button type="button" variant="secondary" wire:click="toggle" wire:loading.attr="disabled">
            <x-icon name="check-circle" class="size-5" />
            {{ __('Mark as completed') }}
        </x-button>
    @endif
</div>
