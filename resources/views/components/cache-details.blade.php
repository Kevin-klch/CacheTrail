@props(['cache', 'url' => null])

{{-- Name, code, type, rating and status of one trail cache, shared by the editor and the detail page. --}}
<div {{ $attributes->class(['min-w-0']) }}>
    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
        <span @class([
            'font-serif text-lg font-bold leading-tight',
            'text-forest-900' => $cache->cache_status !== App\Enums\CacheStatus::Archived,
            'text-ink-muted line-through decoration-1' => $cache->cache_status === App\Enums\CacheStatus::Archived,
        ])>{{ $cache->cache_name }}</span>
        @unless ($cache->cache_status === App\Enums\CacheStatus::Active)
            <x-cache-status-badge :status="$cache->cache_status" />
        @endunless
    </div>
    <p class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-sm text-ink-muted">
        @if ($url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-mono font-medium text-forest-700 hover:underline">
                {{ $cache->cache_code }}
                <x-icon name="external" class="size-3.5" />
            </a>
        @else
            <span class="font-mono font-medium text-forest-700">{{ $cache->cache_code }}</span>
        @endif
        <span>{{ $cache->cache_type->label() }}</span>
        <span>{{ $cache->ratingLabel() }}</span>
    </p>
    @if ($cache->cache_status->hint())
        <p @class([
            'mt-1.5 inline-flex items-center gap-1.5 text-sm font-medium',
            'text-amber-800' => $cache->cache_status === App\Enums\CacheStatus::TemporarilyDisabled,
            'text-red-800' => $cache->cache_status === App\Enums\CacheStatus::Archived,
            'text-ink-muted' => $cache->cache_status === App\Enums\CacheStatus::Unknown,
        ])>
            <x-icon name="alert" class="size-4" />
            {{ $cache->cache_status->hint() }}
        </p>
    @endif
</div>
