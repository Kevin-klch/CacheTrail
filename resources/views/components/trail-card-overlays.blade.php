@props(['trail', 'location' => null])

{{-- Badges and buttons on top of a route card image: distance, rating and the personal actions. --}}
@if ($location && $trail->distanceFromLabel($location))
    <span class="absolute left-3 top-3 z-10 inline-flex items-center gap-1.5 rounded-full bg-paper/95 px-3 py-1 text-xs font-semibold text-forest-800 shadow">
        <x-icon name="compass" class="size-3.5" />
        {{ $trail->distanceFromLabel($location) }}
    </span>
@endif

@if ($trail->rating_count > 0)
    <span class="absolute bottom-3 left-3 z-10 inline-flex items-center gap-1 rounded-full bg-paper/95 px-2.5 py-1 text-xs font-semibold text-forest-900 shadow"
        title="{{ trans_choice('(:count rating)|(:count ratings)', $trail->rating_count) }}">
        <x-icon name="star" class="size-3.5 fill-current text-trail-500" />
        {{ $trail->averageRatingLabel() }}
        <span class="font-normal text-ink-muted">({{ $trail->rating_count }})</span>
    </span>
@endif

@can('view', $trail)
    <div class="absolute right-3 top-3 z-10 flex gap-2">
        <livewire:like-button :trail="$trail" :key="'like-'.$trail->id" />
        <livewire:bookmark-button :trail="$trail" :key="'bookmark-'.$trail->id" />
    </div>
@endcan
