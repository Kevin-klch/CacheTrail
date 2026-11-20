<x-layouts.admin :title="__('Logins')">
    <x-card class="p-4 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2 text-sm" role="tablist">
                @foreach (array_filter(['' => __('All accounts'), 'feedback' => $hasFeedbackAccount ? __('Feedback account') : null]) as $value => $label)
                    <a href="{{ route('admin.logins.index', array_filter(['account' => $value])) }}" role="tab" aria-selected="{{ $onlyFeedbackAccount === ($value === 'feedback') ? 'true' : 'false' }}"
                        @class([
                            'rounded-full px-4 py-1.5 font-medium transition',
                            'bg-forest-700 text-paper' => $onlyFeedbackAccount === ($value === 'feedback'),
                            'text-forest-800 hover:bg-forest-50' => $onlyFeedbackAccount !== ($value === 'feedback'),
                        ])>{{ $label }}</a>
                @endforeach
            </div>
            <p class="text-sm text-ink-muted">
                {{ __('Today: :count logins', ['count' => $loginsToday]) }}
                @if ($hasFeedbackAccount)
                    · {{ __(':count of them with the feedback account', ['count' => $feedbackLoginsToday]) }}
                @endif
            </p>
        </div>

        @if ($logins->isEmpty())
            <x-empty-state class="mt-6" icon="user" :title="__('No logins')">
                {{ __('Nobody has logged in during the last :days days.', ['days' => App\Models\LoginRecord::KEEP_DAYS]) }}
            </x-empty-state>
        @else
            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[36rem] text-left text-sm">
                    <thead class="border-b border-sand-200 text-xs uppercase tracking-wide text-ink-muted">
                        <tr>
                            <th class="py-2 pr-4 font-semibold">{{ __('Time') }}</th>
                            <th class="py-2 pr-4 font-semibold">{{ __('Account') }}</th>
                            <th class="py-2 pr-4 font-semibold">{{ __('Device') }}</th>
                            <th class="py-2 font-semibold">{{ __('IP address') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sand-200">
                        @foreach ($logins as $login)
                            <tr>
                                <td class="whitespace-nowrap py-3 pr-4">{{ App\Support\Format::date($login->created_at) }}, {{ $login->created_at->format('H:i') }}</td>
                                <td class="py-3 pr-4">
                                    <a href="{{ $login->user->profileUrl() }}" class="font-semibold text-forest-800 hover:underline">{{ $login->user->name }}</a>
                                    @if ($login->user->isFeedbackAccount())
                                        <span class="ml-1 rounded-full bg-trail-500/15 px-2 py-0.5 text-xs font-medium text-trail-700">{{ __('Feedback account') }}</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4" title="{{ $login->user_agent }}">{{ $login->deviceLabel() }}</td>
                                <td class="py-3 font-mono text-xs text-ink-muted">{{ $login->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $logins->links() }}</div>
        @endif

        <p class="mt-6 text-xs text-ink-muted">{{ __('Logins are deleted automatically after :days days.', ['days' => App\Models\LoginRecord::KEEP_DAYS]) }}</p>
    </x-card>
</x-layouts.admin>
