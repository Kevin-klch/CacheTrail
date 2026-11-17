<div class="relative">
    @if ($sent)
        <p class="inline-flex items-center gap-1.5 text-sm font-medium text-forest-700">
            <x-icon name="check" class="size-4" />
            {{ __('Thank you, we will check your report.') }}
        </p>
    @elseif ($canReport)
        <button type="button" wire:click="$toggle('open')" aria-expanded="{{ $open ? 'true' : 'false' }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-ink-muted hover:text-red-700">
            <x-icon name="flag" class="size-4" />
            {{ __('Report') }}
        </button>

        @if ($open)
            <form wire:submit="submit" class="mt-3 w-full max-w-md space-y-3 rounded-2xl border border-sand-200 bg-white p-4 text-left shadow-lg">
                <x-select name="reason" wire:model="reason" :label="__('Reason')" :placeholder="__('Please choose')"
                    :options="collect(App\Enums\ReportReason::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->all()" />
                <div>
                    <label for="report-description-{{ $reportableType }}-{{ $reportableId }}" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Details (optional)') }}</label>
                    <textarea id="report-description-{{ $reportableType }}-{{ $reportableId }}" wire:model="description" rows="2" maxlength="1000"
                        class="block w-full rounded-xl border border-sand-300 bg-white px-3 py-2 text-sm text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15"></textarea>
                </div>
                <div class="flex gap-2">
                    <x-button variant="danger" class="px-4 py-2">{{ __('Send report') }}</x-button>
                    <x-button type="button" variant="ghost" class="px-4 py-2" wire:click="$set('open', false)">{{ __('Cancel') }}</x-button>
                </div>
            </form>
        @endif
    @endif
</div>
