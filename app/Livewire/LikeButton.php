<?php

namespace App\Livewire;

use App\Models\Trail;
use App\Services\Badges\BadgeAwarder;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Public "Gefällt mir" for a route; the number of likes determines its popularity.
 */
class LikeButton extends Component
{
    #[Locked]
    public Trail $trail;

    #[Locked]
    public bool $large = false;

    /**
     * The shared feedback account only shows the routes marked for it; testers cannot change them.
     */
    #[Locked]
    public bool $readOnly = false;

    public bool $liked = false;

    public function mount(Trail $trail, bool $large = false): void
    {
        $this->trail = $trail;
        $this->large = $large;
        $this->readOnly = (bool) auth()->user()?->isFeedbackAccount();
        $this->liked = $trail->isLikedBy(auth()->user());
    }

    public function toggle(BadgeAwarder $badges): void
    {
        Gate::authorize('view', $this->trail);
        abort_if($this->readOnly, 403);

        $user = auth()->user();

        if ($this->trail->isLikedBy($user)) {
            $this->trail->likedBy()->detach($user);
        } else {
            $this->trail->likedBy()->syncWithoutDetaching([$user->id]);
        }

        $this->trail->refreshCommunityStatistics();
        $this->liked = ! $this->liked;

        // Likes count towards the badges of the route's creator.
        $badges->award($this->trail->user);
    }

    public function render(): View
    {
        return view('livewire.like-button');
    }
}
