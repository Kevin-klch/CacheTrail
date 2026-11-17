@props(['title'])

{{-- Frame of the admin area with its own sub navigation. --}}
<x-layouts.app :title="$title">
    <div class="rounded-b-[2.5rem] bg-forest-900 pb-28 pt-10 text-paper sm:rounded-b-[4rem] sm:pt-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-trail-400">{{ __('Administration') }}</p>
            <h1 class="mt-1 text-4xl font-bold text-paper sm:text-5xl">{{ $title }}</h1>

            <nav class="mt-6 flex flex-wrap gap-2 text-sm" aria-label="{{ __('Administration') }}">
                @php $openReports = App\Models\Report::open()->count(); @endphp
                @foreach (['admin.reports.index' => [__('Reports'), 'flag', $openReports], 'admin.users.index' => [__('Users'), 'user', null]] as $routeName => [$label, $icon, $badge])
                    <a href="{{ route($routeName) }}" @class([
                        'inline-flex items-center gap-2 rounded-full px-4 py-2 font-medium transition',
                        'bg-paper text-forest-900' => request()->routeIs($routeName),
                        'text-forest-100 hover:bg-paper/10' => ! request()->routeIs($routeName),
                    ])>
                        <x-icon :name="$icon" class="size-4" />
                        {{ $label }}
                        @if ($badge)
                            <span class="rounded-full bg-trail-500 px-2 py-0.5 text-xs text-white">{{ $badge }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="mx-auto -mt-20 max-w-7xl px-4 sm:px-6 lg:px-8">
        @if (session('status'))
            <x-alert class="mb-6">{{ session('status') }}</x-alert>
        @endif

        {{ $slot }}
    </div>
</x-layouts.app>
