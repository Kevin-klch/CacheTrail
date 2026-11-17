<?php

namespace App\Console\Commands;

use App\Models\Trail;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('trails:refresh-statistics')]
#[Description('Recalculate cache, distance, like and rating figures of all routes')]
class RefreshTrailStatistics extends Command
{
    public function handle(): int
    {
        $count = 0;

        Trail::query()->lazyById()->each(function (Trail $trail) use (&$count): void {
            $trail->refreshStatistics();
            $trail->refreshCommunityStatistics();
            $count++;
        });

        $this->components->info("Statistics of {$count} routes refreshed.");

        return self::SUCCESS;
    }
}
