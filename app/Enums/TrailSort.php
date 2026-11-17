<?php

namespace App\Enums;

/**
 * Sort orders of the route directory (specification 4.2). Popularity and rating follow with community features.
 */
enum TrailSort: string
{
    case Newest = 'newest';
    case Shortest = 'shortest';
    case Longest = 'longest';
    case Nearest = 'nearest';

    public function label(): string
    {
        return match ($this) {
            self::Newest => __('Newest'),
            self::Shortest => __('Shortest'),
            self::Longest => __('Longest'),
            self::Nearest => __('Nearest to me'),
        };
    }
}
