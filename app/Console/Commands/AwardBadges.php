<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Badges\BadgeAwarder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('badges:award')]
#[Description('Award badges that users have reached but not received yet (e.g. membership years)')]
class AwardBadges extends Command
{
    public function handle(BadgeAwarder $badges): int
    {
        $awarded = 0;

        User::query()->notBlocked()->lazyById()->each(function (User $user) use ($badges, &$awarded): void {
            $awarded += $badges->award($user)->count();
        });

        $this->info(trans_choice(':count badge awarded.|:count badges awarded.', $awarded));

        return self::SUCCESS;
    }
}
