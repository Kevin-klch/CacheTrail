@props(['current', 'trail' => null])

{{-- Progress of the route editor; the remaining steps of specification 4.4 follow in phase 4. --}}
@php
    $steps = [
        1 => ['label' => __('General information'), 'url' => $trail?->exists ? route('trails.edit', $trail) : null],
        2 => ['label' => __('Caches and order'), 'url' => $trail?->exists ? route('trails.caches', $trail) : null],
    ];
@endphp

<nav aria-label="{{ __('Route editor steps') }}" {{ $attributes->class(['mt-6']) }}>
    <ol class="flex flex-wrap items-center gap-2 text-sm">
        @foreach ($steps as $number => $step)
            <li class="flex items-center gap-2">
                @if (! $loop->first)
                    <span class="h-px w-6 bg-paper/30" aria-hidden="true"></span>
                @endif
                @php $isCurrent = $number === $current; @endphp
                <a @if ($step['url'] && ! $isCurrent) href="{{ $step['url'] }}" @endif
                    @if ($isCurrent) aria-current="step" @endif
                    @class([
                        'inline-flex items-center gap-2 rounded-full px-3 py-1.5 font-medium',
                        'bg-paper text-forest-900' => $isCurrent,
                        'text-forest-100 hover:bg-paper/10' => ! $isCurrent && $step['url'],
                        'text-forest-200/60' => ! $isCurrent && ! $step['url'],
                    ])>
                    <span @class([
                        'grid size-5 place-items-center rounded-full text-xs font-bold',
                        'bg-forest-700 text-paper' => $isCurrent,
                        'bg-paper/20' => ! $isCurrent,
                    ])>{{ $number }}</span>
                    {{ $step['label'] }}
                </a>
            </li>
        @endforeach
    </ol>
</nav>
