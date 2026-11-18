<?php

namespace App\Enums;

/**
 * Sort orders of the route directory (specification 4.2).
 */
enum TrailSort: string
{
    case Newest = 'newest';
    case Popular = 'popular';
    case BestRated = 'best_rated';
    case Shortest = 'shortest';
    case Longest = 'longest';
    case Nearest = 'nearest';

    /**
     * Routes need at least this many ratings to rank fully by their average,
     * so a single five-star rating does not put a route on top.
     */
    public const int MIN_RATINGS_FOR_RANKING = 3;

    public function label(): string
    {
        return match ($this) {
            self::Newest => __('Newest'),
            self::Popular => __('Most popular'),
            self::BestRated => __('Best rated'),
            self::Shortest => __('Shortest'),
            self::Longest => __('Longest'),
            self::Nearest => __('Shortest distance'),
        };
    }
}
