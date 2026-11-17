<?php

namespace App\Livewire;

use App\Models\Trail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Marks a route as completed ("absolviert") by the current user, or undoes it.
 */
class CompleteButton extends Component
{
    #[Locked]
    public Trail $trail;

    public ?string $completedAt = null;

    public function mount(Trail $trail): void
    {
        $this->trail = $trail;
        $this->completedAt = $this->trail->completedBy()->whereKey(auth()->id())->first()?->pivot->completed_at;
    }

    public function toggle(): void
    {
        Gate::authorize('view', $this->trail);

        $user = auth()->user();

        if ($this->completedAt !== null) {
            $this->trail->completedBy()->detach($user);
            $this->completedAt = null;

            return;
        }

        $now = now();
        $this->trail->completedBy()->syncWithoutDetaching([$user->id => ['completed_at' => $now]]);
        $this->completedAt = $now->toDateTimeString();
    }

    public function render(): View
    {
        return view('livewire.complete-button', [
            'completedDate' => $this->completedAt ? Carbon::parse($this->completedAt) : null,
        ]);
    }
}
