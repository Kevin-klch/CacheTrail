<x-layouts.admin :title="__('Users')">
    <x-card class="p-4 sm:p-6">
        <form method="GET" action="{{ route('admin.users.index') }}" role="search" class="flex flex-wrap gap-3">
            <label for="user-search" class="sr-only">{{ __('Search by name or email address') }}</label>
            <input id="user-search" name="q" type="search" value="{{ $search }}" placeholder="{{ __('Search by name or email address') }}"
                class="block min-w-0 flex-1 rounded-xl border border-sand-300 bg-white px-4 py-2.5 text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15">
            <x-button>{{ __('Search') }}</x-button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full min-w-[44rem] text-left text-sm">
                <thead class="border-b border-sand-200 text-xs uppercase tracking-wide text-ink-muted">
                    <tr>
                        <th class="py-2 pr-4 font-semibold">{{ __('Name') }}</th>
                        <th class="py-2 pr-4 font-semibold">{{ __('Email address') }}</th>
                        <th class="py-2 pr-4 font-semibold">{{ __('Routes') }}</th>
                        <th class="py-2 pr-4 font-semibold">{{ __('Ratings') }}</th>
                        <th class="py-2 pr-4 font-semibold">{{ __('Role') }}</th>
                        <th class="py-2 font-semibold">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sand-200">
                    @foreach ($users as $user)
                        @php $isSelf = $user->is(auth()->user()); @endphp
                        <tr @class(['bg-red-50/50' => $user->isBlocked()])>
                            <td class="py-3 pr-4">
                                <a href="{{ $user->profileUrl() }}" class="font-semibold text-forest-800 hover:underline">{{ $user->name }}</a>
                                @if ($isSelf)
                                    <span class="ml-1 text-xs text-ink-muted">({{ __('you') }})</span>
                                @endif
                                <span class="block text-xs text-ink-muted">{{ __('Member since :date', ['date' => $user->created_at->translatedFormat('j. F Y')]) }}</span>
                            </td>
                            <td class="py-3 pr-4 text-ink-muted">{{ $user->email }}</td>
                            <td class="py-3 pr-4">{{ $user->trails_count }}</td>
                            <td class="py-3 pr-4">{{ $user->ratings_count }}</td>
                            <td class="py-3 pr-4">
                                @if ($isSelf)
                                    {{ __('Admin') }}
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-2">
                                        @csrf @method('PATCH')
                                        <label for="role-{{ $user->id }}" class="sr-only">{{ __('Role of :name', ['name' => $user->name]) }}</label>
                                        <select id="role-{{ $user->id }}" name="role" class="rounded-lg border border-sand-300 bg-white px-2 py-1.5 text-sm">
                                            @foreach (App\Enums\UserRole::cases() as $role)
                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="text-xs font-semibold text-forest-700 hover:underline">{{ __('Save') }}</button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3">
                                @if ($isSelf)
                                    <span class="text-ink-muted">{{ __('Active') }}</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="flex items-center gap-3">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="blocked" value="{{ $user->isBlocked() ? 0 : 1 }}">
                                        <span @class(['font-semibold', 'text-red-700' => $user->isBlocked(), 'text-forest-700' => ! $user->isBlocked()])>
                                            {{ $user->isBlocked() ? __('Blocked') : __('Active') }}
                                        </span>
                                        <button type="submit" @class([
                                            'rounded-full px-3 py-1 text-xs font-semibold',
                                            'bg-forest-100 text-forest-800 hover:bg-forest-200' => $user->isBlocked(),
                                            'bg-red-100 text-red-800 hover:bg-red-200' => ! $user->isBlocked(),
                                        ])>{{ $user->isBlocked() ? __('Unblock') : __('Block') }}</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>

        <p class="mt-4 text-xs text-ink-muted">{{ __('Blocked users cannot log in. Their routes and ratings are hidden until they are unblocked.') }}</p>
    </x-card>
</x-layouts.admin>
