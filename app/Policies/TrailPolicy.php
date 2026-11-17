<?php

namespace App\Policies;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

/**
 * Premium access itself is enforced by the "premium" middleware on all trail routes;
 * this policy repeats the check so it also holds wherever the policy is used directly.
 */
class TrailPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasPremiumAccess($user);
    }

    public function view(User $user, Trail $trail): bool
    {
        if ($this->canManage($user, $trail)) {
            return true;
        }

        return $trail->isPublished() && $this->hasPremiumAccess($user);
    }

    public function create(User $user): bool
    {
        return $this->hasPremiumAccess($user);
    }

    public function update(User $user, Trail $trail): bool
    {
        return $this->canManage($user, $trail);
    }

    public function delete(User $user, Trail $trail): bool
    {
        return $this->canManage($user, $trail);
    }

    /**
     * Only the creator and administrators may change a trail (specification 4.5).
     */
    private function canManage(User $user, Trail $trail): bool
    {
        return $user->isAdmin() || $trail->isOwnedBy($user);
    }

    private function hasPremiumAccess(User $user): bool
    {
        return Gate::forUser($user)->allows('premium');
    }
}
