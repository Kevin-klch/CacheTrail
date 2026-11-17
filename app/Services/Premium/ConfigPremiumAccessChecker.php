<?php

namespace App\Services\Premium;

use App\Contracts\PremiumAccessChecker;
use App\Models\User;

/**
 * Interim implementation until a real premium verification exists:
 * grants premium access to every user or to nobody, depending on configuration.
 */
class ConfigPremiumAccessChecker implements PremiumAccessChecker
{
    public function __construct(private readonly bool $grantToAllUsers) {}

    public function hasPremiumAccess(User $user): bool
    {
        return $this->grantToAllUsers;
    }
}
