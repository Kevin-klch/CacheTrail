<?php

namespace App\Enums;

/**
 * Badges users can earn. Tiered badges have several levels (e.g. 1, 10, 25 completed routes),
 * one-off badges a single level. How the figures are measured lives in BadgeAwarder.
 */
enum Badge: string
{
    // Completed routes and membership
    case CompletedRoutes = 'completed_routes';
    case Membership = 'membership';

    // Creators
    case PublishedRoutes = 'published_routes';
    case LikesReceived = 'likes_received';

    // Collectors (summed over completed routes)
    case CachesCollected = 'caches_collected';
    case KilometersCollected = 'kilometers_collected';

    // Special
    case Pioneer = 'pioneer';
    case SummitStormer = 'summit_stormer';
    case PuzzleFox = 'puzzle_fox';
    case Cyclist = 'cyclist';
    case Hiker = 'hiker';
    case Critic = 'critic';

    /**
     * Number of first members that get the pioneer badge.
     */
    public const int PIONEER_MEMBERS = 100;

    /**
     * Minimum average difficulty or terrain of a route for the puzzle fox and summit stormer badges.
     */
    public const float DEMANDING_RATING = 4.0;

    /**
     * The levels of the badge in ascending order.
     *
     * @return list<int>
     */
    public function levels(): array
    {
        return match ($this) {
            self::CompletedRoutes => [1, 10, 25, 50, 100, 250],
            self::Membership => [1, 2, 3, 5, 10],
            self::PublishedRoutes => [1, 5, 10, 25],
            self::LikesReceived => [10, 50, 100, 500],
            self::CachesCollected => [10, 100, 500, 1000],
            self::KilometersCollected => [10, 50, 100, 500],
            self::Cyclist, self::Hiker, self::Critic => [10],
            self::Pioneer, self::SummitStormer, self::PuzzleFox => [1],
        };
    }

    public function name(): string
    {
        return match ($this) {
            self::CompletedRoutes => __('Trail walker'),
            self::Membership => __('Loyal member'),
            self::PublishedRoutes => __('Route builder'),
            self::LikesReceived => __('Crowd favourite'),
            self::CachesCollected => __('Cache collector'),
            self::KilometersCollected => __('Distance devourer'),
            self::Pioneer => __('Pioneer'),
            self::SummitStormer => __('Summit stormer'),
            self::PuzzleFox => __('Puzzle fox'),
            self::Cyclist => __('Cyclist'),
            self::Hiker => __('Hiker'),
            self::Critic => __('Critic'),
        };
    }

    /**
     * What the badge stands for at the given level, e.g. "10 Routen absolviert".
     */
    public function description(int $level): string
    {
        return match ($this) {
            self::CompletedRoutes => trans_choice('First route completed|:count routes completed', $level),
            self::Membership => trans_choice(':count year on CacheTrail|:count years on CacheTrail', $level),
            self::PublishedRoutes => trans_choice('First route published|:count routes published', $level),
            self::LikesReceived => __(':count likes received', ['count' => $level]),
            self::CachesCollected => __(':count caches on completed routes', ['count' => $level]),
            self::KilometersCollected => __(':count km on completed routes', ['count' => $level]),
            self::Pioneer => __('One of the first :count members', ['count' => self::PIONEER_MEMBERS]),
            self::SummitStormer => __('Completed a route with terrain 4 or higher'),
            self::PuzzleFox => __('Completed a route with difficulty 4 or higher'),
            self::Cyclist => __(':count bike routes completed', ['count' => $level]),
            self::Hiker => __(':count walking routes completed', ['count' => $level]),
            self::Critic => __(':count routes rated', ['count' => $level]),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CompletedRoutes => 'check-circle',
            self::Membership => 'calendar',
            self::PublishedRoutes => 'route',
            self::LikesReceived => 'heart',
            self::CachesCollected => 'compass',
            self::KilometersCollected => 'map',
            self::Pioneer => 'flag',
            self::SummitStormer => 'terrain',
            self::PuzzleFox => 'difficulty',
            self::Cyclist => 'bike',
            self::Hiker => 'foot',
            self::Critic => 'star',
        };
    }

    /**
     * Position of the level within the badge (0 for the first), used for the medal colour.
     */
    public function tier(int $level): int
    {
        $index = array_search($level, $this->levels(), true);

        return $index === false ? 0 : $index;
    }

    public function isTiered(): bool
    {
        return count($this->levels()) > 1;
    }
}
