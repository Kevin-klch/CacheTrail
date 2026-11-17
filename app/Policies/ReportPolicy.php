<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class ReportPolicy
{
    /**
     * Users can report routes and ratings of others that they can see, once while the report is open.
     */
    public function create(User $user, Model $reportable): bool
    {
        $author = match (true) {
            $reportable instanceof Trail => $reportable->user_id,
            $reportable instanceof TrailRating => $reportable->user_id,
            default => null,
        };

        $trail = $reportable instanceof TrailRating ? $reportable->trail : $reportable;

        return $author !== null
            && $author !== $user->id
            && $trail instanceof Trail
            && Gate::forUser($user)->allows('view', $trail)
            && ! Report::query()->open()->whereBelongsTo($user)->whereMorphedTo('reportable', $reportable)->exists();
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Report $report): bool
    {
        return $user->isAdmin();
    }
}
