<?php

namespace App\Actions\Trails;

use App\Contracts\GeocacheProvider;
use App\Models\Trail;
use App\Models\TrailCache;
use App\Support\GcCode;
use Illuminate\Support\Facades\DB;

/**
 * Adds caches by GC code to the end of a trail, keeping the order in which they were entered.
 */
class AddCachesToTrail
{
    public const int MAX_CACHES_PER_TRAIL = 100;

    public function __construct(private readonly GeocacheProvider $provider) {}

    public function handle(Trail $trail, string $input): AddCachesResult
    {
        $codes = GcCode::parseList($input);
        $existing = $trail->caches()->pluck('cache_code')->all();
        $failures = [];
        $candidates = [];

        foreach ($codes as $code) {
            if (! GcCode::isValid($code)) {
                $failures[$code] = __('This is not a valid GC code.');
            } elseif (in_array($code, $existing, true)) {
                $failures[$code] = __('This cache is already part of the route.');
            } else {
                $candidates[] = $code;
            }
        }

        $found = $candidates === [] ? [] : $this->provider->findManyByCodes($candidates);
        $free = self::MAX_CACHES_PER_TRAIL - count($existing);
        $added = [];

        DB::transaction(function () use ($trail, $candidates, $found, $existing, &$free, &$failures, &$added): void {
            $position = count($existing);

            foreach ($candidates as $code) {
                if (! isset($found[$code])) {
                    $failures[$code] = __('No cache with this code was found.');

                    continue;
                }

                if ($free <= 0) {
                    $failures[$code] = __('A route can contain at most :max caches.', ['max' => self::MAX_CACHES_PER_TRAIL]);

                    continue;
                }

                $trail->caches()->create([
                    ...TrailCache::attributesFrom($found[$code], $this->provider->name()),
                    'position' => $position++,
                ]);

                $added[] = $code;
                $free--;
            }

            $trail->refreshStatistics();
        });

        return new AddCachesResult($added, $failures);
    }
}
