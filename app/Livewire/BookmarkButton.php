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

    /**
     * The shared feedback account only shows the routes marked for it; testers cannot change them.
     */
    #[Locked]
    public bool $readOnly = false;

    public bool $bookmarked = false;

    public function mount(Trail $trail, bool $large = false): void
    {
        $this->trail = $trail;
        $this->large = $large;
        $this->readOnly = (bool) auth()->user()?->isFeedbackAccount();
        $this->bookmarked = $trail->isBookmarkedBy(auth()->user());
    }

    public function toggle(): void
    {
        Gate::authorize('view', $this->trail);
        abort_if($this->readOnly, 403);

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
