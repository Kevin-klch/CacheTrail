<?php

namespace App\Contracts;

use App\Data\GeocacheData;

/**
 * Source of geocache data. The application talks to geocache sources only through this interface
 * (specification 7.3), so the mock can later be replaced by an official API provider.
 */
interface GeocacheProvider
{
    /**
     * Short identifier stored with each trail cache, e.g. "mock".
     */
    public function name(): string;

    public function findByCode(string $code): ?GeocacheData;

    /**
     * Looks up several codes at once to keep the number of API requests low.
     *
     * @param  list<string>  $codes
     * @return array<string, GeocacheData> keyed by normalized code; unknown codes are missing
     */
    public function findManyByCodes(array $codes): array;

    /**
     * Link to the public cache page, or null if the provider has no real cache pages (e.g. mock data).
     */
    public function cacheUrl(string $code): ?string;
}
