@props(['status'])

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold',
    'bg-forest-100 text-forest-800' => $status === App\Enums\CacheStatus::Active,
    'bg-amber-100 text-amber-900' => $status === App\Enums\CacheStatus::TemporarilyDisabled,
    'bg-red-100 text-red-800' => $status === App\Enums\CacheStatus::Archived,
    'bg-sand-200 text-ink' => $status === App\Enums\CacheStatus::Unknown,
]) }}>
    {{ $status->label() }}
</span>
