<?php

namespace App\Livewire;

use App\Models\Trail;
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

    public bool $liked = false;

    public function mount(Trail $trail, bool $large = false): void
    {
        $this->trail = $trail;
        $this->large = $large;
        $this->liked = $trail->isLikedBy(auth()->user());
    }

    public function toggle(): void
    {
        Gate::authorize('view', $this->trail);

        $user = auth()->user();

        if ($this->trail->isLikedBy($user)) {
            $this->trail->likedBy()->detach($user);
        } else {
            $this->trail->likedBy()->syncWithoutDetaching([$user->id]);
        }

        $this->trail->refreshCommunityStatistics();
        $this->liked = ! $this->liked;
    }

    public function render(): View
    {
        return view('livewire.like-button');
    }
}
