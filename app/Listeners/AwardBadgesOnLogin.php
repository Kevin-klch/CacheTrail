<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\Badges\BadgeAwarder;
use Illuminate\Auth\Events\Login;

/**
 * Checks for new badges when someone logs in, e.g. a further year of membership.
 */
class AwardBadgesOnLogin
{
    public function __construct(private BadgeAwarder $badges) {}

    public function handle(Login $event): void
    {
        if ($event->user instanceof User) {
            $this->badges->awardAndFlash($event->user);
        }
    }
}
