<?php

namespace App\Services\Badges;

use App\Enums\Badge;
use App\Enums\Transportation;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Support\Collection;

/**
 * Measures a user's figures and awards every badge level that has been reached but not stored yet.
 *
 * It is called after actions that can earn badges (completing, publishing, rating, liking), on login
 * and once a day for all users (membership years). Badges are never taken away again.
 */
class BadgeAwarder
{
    /**
     * Session key for badges announced after a redirect (see the badge-notices component).
     */
    public const string SESSION_KEY = 'earned-badges';

    /**
     * Data for the "new badge" notices.
     *
     * @param  Collection<int, UserBadge>  $badges
     * @return list<array{name: string, description: string}>
     */
    public static function notices(Collection $badges): array
    {
        return $badges->map(fn (UserBadge $badge): array => [
            'name' => $badge->name(),
            'description' => $badge->description(),
        ])->values()->all();
    }

    /**
     * Awards new badges and remembers them for the notice on the next page.
     */
    public function awardAndFlash(User $user): void
    {
        $new = $this->award($user);

        if ($new->isNotEmpty() && auth()->user()?->is($user)) {
            session()->flash(self::SESSION_KEY, [...session(self::SESSION_KEY, []), ...self::notices($new)]);
        }
    }

    /**
     * @return Collection<int, UserBadge> the badges earned just now
     */
    public function award(User $user): Collection
    {
        $earned = $user->badges()->get(['badge', 'level'])
            ->map(fn (UserBadge $badge): string => $badge->badge->value.':'.$badge->level)
            ->all();

        $new = collect();

        foreach (Badge::cases() as $badge) {
            $value = $this->figure($user, $badge);

            foreach ($badge->levels() as $level) {
                if ($value >= $level && ! in_array($badge->value.':'.$level, $earned, true)) {
                    $new->push($user->badges()->create([
                        'badge' => $badge,
                        'level' => $level,
                        'earned_at' => now(),
                    ]));
                }
            }
        }

        return $new;
    }

    /**
     * The user's current figure for a badge, compared against its levels.
     */
    public function figure(User $user, Badge $badge): int
    {
        return match ($badge) {
            Badge::CompletedRoutes => $user->completedTrails()->count(),
            Badge::Membership => (int) floor($user->created_at->diffInYears(now())),
            Badge::PublishedRoutes => $user->trails()->published()->count(),
            Badge::LikesReceived => (int) $user->trails()->published()->sum('like_count'),
            Badge::CachesCollected => (int) $user->completedTrails()->sum('trails.cache_count'),
            Badge::KilometersCollected => (int) floor($user->completedTrails()->sum('trails.total_distance_meters') / 1000),
            Badge::Pioneer => User::query()->where('id', '<=', $user->id)->count() <= Badge::PIONEER_MEMBERS ? 1 : 0,
            Badge::SummitStormer => (int) $user->completedTrails()->where('trails.average_terrain', '>=', Badge::DEMANDING_RATING)->exists(),
            Badge::PuzzleFox => (int) $user->completedTrails()->where('trails.average_difficulty', '>=', Badge::DEMANDING_RATING)->exists(),
            Badge::Cyclist => $user->completedTrails()->suitableFor(Transportation::Bike)->count(),
            Badge::Hiker => $user->completedTrails()->suitableFor(Transportation::Foot)->count(),
            Badge::Critic => $user->ratings()->count(),
        };
    }
}
