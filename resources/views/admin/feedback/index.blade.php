<x-layouts.admin :title="__('Feedback')">
    <x-card class="p-4 sm:p-6">
        <div class="flex flex-wrap gap-2 text-sm" role="tablist">
            @foreach (['' => __('Open').' ('.$openCount.')', 'done' => __('Marked as done')] as $value => $label)
                <a href="{{ route('admin.feedback.index', array_filter(['status' => $value])) }}" role="tab" aria-selected="{{ $showDone === ($value === 'done') ? 'true' : 'false' }}"
                    @class([
                        'rounded-full px-4 py-1.5 font-medium transition',
                        'bg-forest-700 text-paper' => $showDone === ($value === 'done'),
                        'text-forest-800 hover:bg-forest-50' => $showDone !== ($value === 'done'),
                    ])>{{ $label }}</a>
            @endforeach
        </div>

        @if ($feedback->isEmpty())
            <x-empty-state class="mt-6" icon="mail" :title="__('No feedback')">
                {{ $showDone ? __('No feedback has been marked as done yet.') : __('There is no new feedback at the moment.') }}
            </x-empty-state>
        @else
            <ul class="mt-6 space-y-4">
                @foreach ($feedback as $item)
                    <li class="rounded-2xl border border-sand-200 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <p class="flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-trail-600">
                                    <x-icon :name="$item->category->icon()" class="size-4" />
                                    {{ $item->category->shortLabel() }}
                                </p>
                                <p class="mt-2 whitespace-pre-line break-words text-ink">{{ $item->message }}</p>
                                <p class="mt-3 text-sm text-ink-muted">
                                    {{ $item->user?->name ?? __('Guest') }}
                                    · {{ App\Support\Format::date($item->created_at) }}, {{ $item->created_at->format('H:i') }}
                                    @if ($item->contact)
                                        · <a href="mailto:{{ $item->contact }}" class="font-medium text-forest-700 hover:underline">{{ $item->contact }}</a>
                                    @endif
                                </p>
                                @if ($item->page_url)
                                    <p class="mt-1 truncate text-xs text-ink-muted">
                                        {{ __('Page') }}: <a href="{{ $item->page_url }}" class="hover:underline" target="_blank" rel="noopener noreferrer">{{ Illuminate\Support\Str::after($item->page_url, url('/')) ?: '/' }}</a>
                                    </p>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('admin.feedback.update', $item) }}">
                                    @csrf @method('PATCH')
                                    <x-button variant="secondary" class="px-4 py-2">{{ $item->isDone() ? __('Reopen') : __('Mark as done') }}</x-button>
                                </form>
                                <form method="POST" action="{{ route('admin.feedback.destroy', $item) }}">
                                    @csrf @method('DELETE')
                                    <x-button variant="ghost" class="px-4 py-2">{{ __('Delete') }}</x-button>
                                </form>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">{{ $feedback->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>
