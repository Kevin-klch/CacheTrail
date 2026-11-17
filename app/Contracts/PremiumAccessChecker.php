<?php

namespace App\Contracts;

use App\Models\User;

/**
 * Decides whether a user may use premium features (discovering and creating routes).
 *
 * The final check (e.g. via the Geocaching.com API) is not confirmed yet,
 * so the implementation must stay swappable.
 */
interface PremiumAccessChecker
{
    public function hasPremiumAccess(User $user): bool;
}
