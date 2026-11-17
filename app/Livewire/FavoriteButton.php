<?php

namespace App\Livewire;

use App\Models\Trail;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Heart button to save a route as a favorite (specification 9.5).
 */
class FavoriteButton extends Component
{
    #[Locked]
    public Trail $trail;

    #[Locked]
    public bool $large = false;

    public bool $favorited = false;

    public function mount(Trail $trail, bool $large = false): void
    {
        $this->trail = $trail;
        $this->large = $large;
        $this->favorited = $trail->isFavoritedBy(auth()->user());
    }

    public function toggle(): void
    {
        Gate::authorize('view', $this->trail);

        $user = auth()->user();

        if ($this->trail->isFavoritedBy($user)) {
            $this->trail->favoritedBy()->detach($user);
        } else {
            $this->trail->favoritedBy()->syncWithoutDetaching([$user->id]);
        }

        $this->trail->refreshCommunityStatistics();
        $this->favorited = ! $this->favorited;
    }

    public function render(): View
    {
        return view('livewire.favorite-button');
    }
}
