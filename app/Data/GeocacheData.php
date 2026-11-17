<?php

namespace App\Data;

use App\Enums\CacheStatus;
use App\Enums\CacheType;

/**
 * Provider independent snapshot of a single geocache.
 */
final readonly class GeocacheData
{
    public function __construct(
        public string $code,
        public string $name,
        public float $latitude,
        public float $longitude,
        public CacheType $type,
        public float $difficulty,
        public float $terrain,
        public CacheStatus $status,
    ) {}
}
