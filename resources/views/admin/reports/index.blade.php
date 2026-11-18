<x-layouts.admin :title="__('Reports')">
    <x-card class="p-4 sm:p-6">
        <div class="flex flex-wrap gap-2 text-sm" role="tablist">
            @foreach (App\Enums\ReportStatus::cases() as $option)
                <a href="{{ route('admin.reports.index', ['status' => $option->value]) }}" role="tab" aria-selected="{{ $status === $option ? 'true' : 'false' }}"
                    @class([
                        'rounded-full px-4 py-1.5 font-medium transition',
                        'bg-forest-700 text-paper' => $status === $option,
                        'text-forest-800 hover:bg-forest-50' => $status !== $option,
                    ])>
                    {{ $option->label() }}
                    @if ($option === App\Enums\ReportStatus::Open)
                        ({{ $openCount }})
                    @endif
                </a>
            @endforeach
        </div>

        @if ($reports->isEmpty())
            <x-empty-state class="mt-6" icon="flag" :title="__('No reports')">
                {{ $status === App\Enums\ReportStatus::Open ? __('There are no open reports. Everything is fine.') : __('There are no reports with this status.') }}
            </x-empty-state>
        @else
            <ul class="mt-6 space-y-4">
                @foreach ($reports as $report)
                    @php
                        $item = $report->reportable;
                        $trail = $item instanceof App\Models\TrailRating ? $item->trail : $item;
                    @endphp
                    <li class="rounded-2xl border border-sand-200 p-5">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide text-trail-600">
                                    {{ $report->reason->label() }}
                                    · {{ $item instanceof App\Models\TrailRating ? __('Rating') : __('Route') }}
                                </p>
                                @if ($item === null)
                                    <p class="mt-1 font-heading text-xl font-bold text-ink-muted">{{ __('The reported content no longer exists.') }}</p>
                                @else
                                    <p class="mt-1 font-heading text-xl font-bold text-forest-900">
                                        <a href="{{ route('trails.show', $trail) }}" class="hover:underline" target="_blank" rel="noopener">{{ $trail->name }}</a>
                                    </p>
                                    @if ($item instanceof App\Models\TrailRating)
                                        <div class="mt-2 rounded-xl bg-sand-100/70 px-3 py-2 text-sm">
                                            <p class="font-semibold">{{ $item->user->name }} · {{ trans_choice(':count star|:count stars', $item->rating) }}</p>
                                            @if ($item->comment)
                                                <p class="mt-1 whitespace-pre-line text-ink">{{ $item->comment }}</p>
                                            @endif
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-ink-muted">
                                            {{ __('by') }} {{ $trail->user->name }} · {{ $trail->visibility->label() }}
                                        </p>
                                    @endif
                                @endif
                                <p class="mt-3 text-sm text-ink-muted">
                                    {{ __('Reported by :name on :date', ['name' => $report->user->name, 'date' => App\Support\Format::date($report->created_at).', '.$report->created_at->format('H:i')]) }}
                                </p>
                                @if ($report->description)
                                    <p class="mt-1 whitespace-pre-line text-sm text-ink">„{{ $report->description }}“</p>
                                @endif
                                @unless ($report->isOpen())
                                    <p class="mt-2 text-xs text-ink-muted">
                                        {{ __(':status by :name on :date', ['status' => $report->status->label(), 'name' => $report->resolver?->name ?? '–', 'date' => ($report->resolved_at ? App\Support\Format::date($report->resolved_at) : '–')]) }}
                                    </p>
                                @endunless
                            </div>

                            @if ($report->isOpen())
                                <div class="flex flex-wrap gap-2">
                                    @if ($item instanceof App\Models\TrailRating)
                                        <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="delete_rating">
                                            <x-button variant="danger" class="px-4 py-2">{{ __('Delete rating') }}</x-button>
                                        </form>
                                    @elseif ($item instanceof App\Models\Trail && $item->isPublished())
                                        <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="action" value="hide_trail">
                                            <x-button variant="danger" class="px-4 py-2">{{ __('Hide route') }}</x-button>
                                        </form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="resolve">
                                        <x-button variant="secondary" class="px-4 py-2">{{ __('Mark as resolved') }}</x-button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reports.update', $report) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="action" value="dismiss">
                                        <x-button variant="ghost" class="px-4 py-2">{{ __('Dismiss') }}</x-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">{{ $reports->links() }}</div>
        @endif
    </x-card>
</x-layouts.admin>
