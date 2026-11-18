<?php

namespace App\Queries;

use App\Models\Trail;

/**
 * The route shown to visitors as an example on the home page and on the public example page.
 *
 * The slug configured in cachetrail.trails.example is used when that route is published and has
 * enough caches; otherwise the most liked published route with enough caches is chosen.
 */
class ExampleTrail
{
    public function find(): ?Trail
    {
        $query = Trail::query()
            ->published()
            ->where('cache_count', '>=', Trail::minCachesToPublish())
            ->with(['user', 'caches']);

        $slug = config('cachetrail.trails.example');

        if (is_string($slug) && $slug !== '') {
            $configured = (clone $query)->where('slug', $slug)->first();

            if ($configured) {
                return $configured;
            }
        }

        return $query
            ->orderByDesc('like_count')
            ->orderByDesc('average_rating')
            ->oldest('published_at')
            ->first();
    }
}
