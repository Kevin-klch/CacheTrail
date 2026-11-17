<?php

namespace App\Services\Geocaching;

use App\Contracts\GeocacheProvider;
use App\Data\GeocacheData;
use App\Enums\CacheStatus;
use App\Enums\CacheType;
use App\Support\GcCode;

/**
 * Serves fictional example caches from a JSON file so the application works without an API.
 */
class MockGeocacheProvider implements GeocacheProvider
{
    /**
     * @var array<string, GeocacheData>|null
     */
    private ?array $caches = null;

    public function __construct(private readonly string $dataFile) {}

    public function name(): string
    {
        return 'mock';
    }

    public function findByCode(string $code): ?GeocacheData
    {
        return $this->caches()[GcCode::normalize($code)] ?? null;
    }

    public function findManyByCodes(array $codes): array
    {
        $found = [];

        foreach ($codes as $code) {
            $cache = $this->findByCode($code);

            if ($cache !== null) {
                $found[$cache->code] = $cache;
            }
        }

        return $found;
    }

    public function cacheUrl(string $code): ?string
    {
        return null;
    }

    /**
     * @return list<GeocacheData>
     */
    public function all(): array
    {
        return array_values($this->caches());
    }

    /**
     * @return array<string, GeocacheData>
     */
    private function caches(): array
    {
        if ($this->caches !== null) {
            return $this->caches;
        }

        /** @var list<array{code: string, name: string, latitude: float, longitude: float, type: string, difficulty: float, terrain: float, status: string}> $rows */
        $rows = json_decode((string) file_get_contents($this->dataFile), true, flags: JSON_THROW_ON_ERROR);

        $this->caches = [];

        foreach ($rows as $row) {
            $this->caches[$row['code']] = new GeocacheData(
                code: $row['code'],
                name: $row['name'],
                latitude: (float) $row['latitude'],
                longitude: (float) $row['longitude'],
                type: CacheType::from($row['type']),
                difficulty: (float) $row['difficulty'],
                terrain: (float) $row['terrain'],
                status: CacheStatus::from($row['status']),
            );
        }

        return $this->caches;
    }
}
