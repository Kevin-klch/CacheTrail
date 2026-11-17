<?php

namespace App\Queries;

use App\Enums\TrailSort;
use App\Enums\Transportation;
use App\Models\Trail;
use App\Support\UserLocation;
use Illuminate\Database\Eloquent\Builder;

/**
 * Builds the query of published trails for the route directory from search, filters, sort order and location.
 */
class TrailDirectory
{
    private const float KM_PER_DEGREE_LATITUDE = 111.32;

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Trail>
     */
    public function query(array $filters, TrailSort $sort, ?UserLocation $location): Builder
    {
        $query = Trail::query()->published();

        if (isset($filters['q'])) {
            $search = $filters['q'];
            $query->where(fn (Builder $query) => $query
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('description', "%{$search}%"));
        }

        if (isset($filters['transportation'])) {
            $query->suitableFor(Transportation::from($filters['transportation']));
        }

        $this->between($query, 'estimated_duration_minutes', $filters['min_hours'] ?? null, $filters['max_hours'] ?? null, 60);
        $this->between($query, 'cache_count', $filters['min_caches'] ?? null, $filters['max_caches'] ?? null);
        $this->between($query, 'total_distance_meters', $filters['min_km'] ?? null, $filters['max_km'] ?? null, 1000);
        $this->between($query, 'average_difficulty', $filters['min_difficulty'] ?? null, $filters['max_difficulty'] ?? null);
        $this->between($query, 'average_terrain', $filters['min_terrain'] ?? null, $filters['max_terrain'] ?? null);

        if ($location !== null && isset($filters['radius'])) {
            [$expression, $bindings] = $this->squaredDistanceKm($location);

            $query->whereNotNull('start_latitude')
                ->whereRaw("{$expression} <= ?", [...$bindings, $filters['radius'] ** 2]);
        }

        $this->sort($query, $sort, $location);

        return $query->orderByDesc('id');
    }

    /**
     * @param  Builder<Trail>  $query
     */
    private function between(Builder $query, string $column, mixed $min, mixed $max, int $factor = 1): void
    {
        if ($min !== null) {
            $query->where($column, '>=', $min * $factor);
        }

        if ($max !== null) {
            $query->where($column, '<=', $max * $factor);
        }
    }

    /**
     * @param  Builder<Trail>  $query
     */
    private function sort(Builder $query, TrailSort $sort, ?UserLocation $location): void
    {
        match ($sort) {
            TrailSort::Newest => $query->latest('published_at'),
            TrailSort::Popular => $query->orderByDesc('like_count')->latest('published_at'),
            TrailSort::BestRated => $query
                ->orderByRaw('case when rating_count >= ? then 0 else 1 end', [TrailSort::MIN_RATINGS_FOR_RANKING])
                ->orderByRaw('average_rating is null')
                ->orderByDesc('average_rating')
                ->orderByDesc('rating_count'),
            TrailSort::Shortest => $query->orderByRaw('total_distance_meters is null')->orderBy('total_distance_meters'),
            TrailSort::Longest => $query->orderByRaw('total_distance_meters is null')->orderByDesc('total_distance_meters'),
            TrailSort::Nearest => $location === null
                ? $query->latest('published_at')
                : $query->orderByRaw('start_latitude is null')->orderByRaw(...$this->squaredDistanceKm($location)),
        };
    }

    /**
     * Squared distance in km² between the start of a trail and the location, using the
     * equirectangular approximation. It needs only basic arithmetic, so it works on every database,
     * and is accurate enough for regional distances.
     *
     * @return array{0: string, 1: list<float>}
     */
    private function squaredDistanceKm(UserLocation $location): array
    {
        $kmPerDegreeLongitude = self::KM_PER_DEGREE_LATITUDE * cos(deg2rad($location->latitude));

        $expression = '((start_latitude - ?) * ?) * ((start_latitude - ?) * ?)'
            .' + ((start_longitude - ?) * ?) * ((start_longitude - ?) * ?)';

        return [$expression, [
            $location->latitude, self::KM_PER_DEGREE_LATITUDE, $location->latitude, self::KM_PER_DEGREE_LATITUDE,
            $location->longitude, $kmPerDegreeLongitude, $location->longitude, $kmPerDegreeLongitude,
        ]];
    }
}
