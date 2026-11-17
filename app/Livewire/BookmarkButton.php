<?php

namespace App\Livewire;

use App\Models\Trail;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Saves a route on the user's private list "Für später merken".
 */
class BookmarkButton extends Component
{
    #[Locked]
    public Trail $trail;

    #[Locked]
    public bool $large = false;

    public bool $bookmarked = false;

    public function mount(Trail $trail, bool $large = false): void
    {
        $this->trail = $trail;
        $this->large = $large;
        $this->bookmarked = $trail->isBookmarkedBy(auth()->user());
    }

    public function toggle(): void
    {
        Gate::authorize('view', $this->trail);

        $user = auth()->user();

        if ($this->trail->isBookmarkedBy($user)) {
            $this->trail->bookmarkedBy()->detach($user);
        } else {
            $this->trail->bookmarkedBy()->syncWithoutDetaching([$user->id]);
        }

        $this->bookmarked = ! $this->bookmarked;
    }

    public function render(): View
    {
        return view('livewire.bookmark-button');
    }
}
