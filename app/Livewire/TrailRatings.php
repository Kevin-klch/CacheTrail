<?php

namespace App\Livewire;

use App\Models\Trail;
use App\Models\TrailRating;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Ratings section of a route: 1 to 5 stars with an optional comment (one per user),
 * and one reply per rating by the creator of the route.
 */
class TrailRatings extends Component
{
    use WithPagination;

    public const int PER_PAGE = 10;

    #[Locked]
    public Trail $trail;

    public ?int $rating = null;

    public string $comment = '';

    public bool $editing = false;

    #[Locked]
    public ?int $replyingTo = null;

    public string $reply = '';

    public function mount(Trail $trail): void
    {
        $this->trail = $trail;
    }

    public function save(): void
    {
        $existing = $this->ownRating();

        $existing === null
            ? Gate::authorize('create', [TrailRating::class, $this->trail])
            : Gate::authorize('update', $existing);

        $validated = $this->validate([
            'rating' => ['required', 'integer', 'between:'.TrailRating::MIN_STARS.','.TrailRating::MAX_STARS],
            'comment' => ['nullable', 'string', 'max:2000'],
        ], attributes: ['rating' => __('Stars'), 'comment' => __('Comment')]);

        $rating = $existing ?? new TrailRating;
        $rating->fill([
            'rating' => $validated['rating'],
            'comment' => trim((string) $validated['comment']) === '' ? null : trim($validated['comment']),
        ]);
        $rating->user()->associate(auth()->user());
        $rating->trail()->associate($this->trail);
        $rating->save();

        $this->trail->refreshCommunityStatistics();
        $this->reset(['rating', 'comment', 'editing']);
        session()->flash('rating-status', $existing ? __('Your rating has been updated.') : __('Thank you for your rating!'));
    }

    public function edit(): void
    {
        $rating = $this->ownRating();

        if ($rating === null) {
            return;
        }

        Gate::authorize('update', $rating);

        $this->rating = $rating->rating;
        $this->comment = (string) $rating->comment;
        $this->editing = true;
    }

    public function cancelEdit(): void
    {
        $this->reset(['rating', 'comment', 'editing']);
        $this->resetValidation();
    }

    public function delete(int $ratingId): void
    {
        $rating = $this->trail->ratings()->findOrFail($ratingId);

        Gate::authorize('delete', $rating);

        $rating->delete();
        $this->trail->refreshCommunityStatistics();
        $this->reset(['rating', 'comment', 'editing']);
    }

    public function startReply(int $ratingId): void
    {
        $rating = $this->trail->ratings()->findOrFail($ratingId);

        Gate::authorize('reply', $rating);

        $this->replyingTo = $rating->id;
        $this->reply = (string) $rating->creator_reply;
    }

    public function saveReply(): void
    {
        $rating = $this->trail->ratings()->findOrFail((int) $this->replyingTo);

        Gate::authorize('reply', $rating);

        $this->validate(['reply' => ['nullable', 'string', 'max:2000']], attributes: ['reply' => __('Reply')]);

        $rating->reply($this->reply);
        $this->reset(['replyingTo', 'reply']);
    }

    public function cancelReply(): void
    {
        $this->reset(['replyingTo', 'reply']);
        $this->resetValidation();
    }

    public function render(): View
    {
        $this->trail->refresh();

        return view('livewire.trail-ratings', [
            'ratings' => $this->trail->visibleRatings()->with('user')->latest()->paginate(self::PER_PAGE),
            'ownRating' => $this->ownRating(),
            'canRate' => Gate::allows('create', [TrailRating::class, $this->trail]),
        ]);
    }

    private function ownRating(): ?TrailRating
    {
        return $this->trail->ratings()->whereBelongsTo(auth()->user())->first();
    }
}
