<?php

namespace App\Policies;

use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class TrailRatingPolicy
{
    /**
     * Published routes can be rated once per user, but not by their creator.
     */
    public function create(User $user, Trail $trail): bool
    {
        return $trail->isPublished()
            && ! $trail->isOwnedBy($user)
            && Gate::forUser($user)->allows('premium')
            && ! $trail->ratings()->whereBelongsTo($user)->exists();
    }

    public function update(User $user, TrailRating $rating): bool
    {
        return $rating->user_id === $user->id;
    }

    public function delete(User $user, TrailRating $rating): bool
    {
        return $rating->user_id === $user->id || $user->canModerate();
    }

    /**
     * Only the creator of the route may reply to its ratings (specification 4.5).
     */
    public function reply(User $user, TrailRating $rating): bool
    {
        return $rating->trail->isOwnedBy($user);
    }
}
