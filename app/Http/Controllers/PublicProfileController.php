<?php

namespace App\Http\Controllers;

use App\Enums\Badge;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

/**
 * Public profile of a geocacher: info box, geocaching and CacheTrail figures, completed and
 * published routes. The email address is never shown.
 */
class PublicProfileController extends Controller
{
    /**
     * Number of most recently completed routes listed on the profile.
     */
    public const int RECENT_COMPLETIONS = 3;

    public function __invoke(string $user): View|RedirectResponse
    {
        $profile = User::with('geocachingAccount')->findOrFail((int) $user);

        // Old or shortened addresses lead to the current one, e.g. after a name change.
        if ($user !== $profile->profileSlug()) {
            return redirect()->to($profile->profileUrl(), 301);
        }

        $trails = $profile->trails()
            ->published()
            ->with(['user', 'caches:id,trail_id,position,latitude,longitude'])
            ->latest('published_at')
            ->paginate(TrailController::PER_PAGE);

        $badges = $this->highestBadges($profile);

        // The owner has now seen their new badges; they are marked as "Neu" only this once.
        if (auth()->user()->is($profile)) {
            $profile->badges()->whereNull('seen_at')->update(['seen_at' => now()]);
        }

        return view('profiles.show', [
            'profile' => $profile,
            'badges' => $badges,
            'account' => $profile->geocachingAccount,
            'trails' => $trails,
            'likesReceived' => (int) $profile->trails()->published()->sum('like_count'),
            'completedCount' => $profile->completedTrails()->count(),
            'recentCompletions' => $profile->completedTrails()
                ->published()
                ->with('caches:id,trail_id,position,latitude,longitude')
                ->orderByPivot('completed_at', 'desc')
                ->limit(self::RECENT_COMPLETIONS)
                ->get(),
        ]);
    }

    /**
     * The highest earned level of every badge, in the order of the Badge enum, each with an "isNew" flag
     * that is set when the owner has not seen one of its levels yet.
     *
     * @return Collection<int, array{badge: UserBadge, isNew: bool}>
     */
    private function highestBadges(User $profile): Collection
    {
        $order = array_flip(array_map(fn (Badge $badge): string => $badge->value, Badge::cases()));
        $showNew = auth()->user()->is($profile);

        return $profile->badges()->get()
            ->groupBy(fn (UserBadge $badge): string => $badge->badge->value)
            ->sortBy(fn (Collection $levels, string $badge): int => $order[$badge])
            ->map(fn (Collection $levels): array => [
                'badge' => $levels->sortByDesc('level')->first(),
                'isNew' => $showNew && $levels->contains(fn (UserBadge $badge): bool => $badge->isNew()),
            ])
            ->values();
    }
}
