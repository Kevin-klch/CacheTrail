<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Public profile of a geocacher: info box, geocaching and CacheTrail figures, completed and
 * published routes. The email address is never shown.
 */
class PublicProfileController extends Controller
{
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

        return view('profiles.show', [
            'profile' => $profile,
            'account' => $profile->geocachingAccount,
            'trails' => $trails,
            'likesReceived' => (int) $profile->trails()->published()->sum('like_count'),
            'completedCount' => $profile->completedTrails()->count(),
            'lastCompleted' => $profile->completedTrails()->published()->orderByPivot('completed_at', 'desc')->first(),
        ]);
    }
}
