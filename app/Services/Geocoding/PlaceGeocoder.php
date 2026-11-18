<?php

namespace App\Services\Geocoding;

use App\Support\UserLocation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Finds a place such as "Moers" by name using the OpenStreetMap Nominatim search, for the
 * "routes within X km of a place" search of the route directory.
 *
 * Requests are made by the server and cached (places for 30 days, unknown names for one day),
 * so the Nominatim usage policy (max. 1 request per second) is respected. Failures return null;
 * the directory then falls back to the normal text search.
 */
class PlaceGeocoder
{
    private const int CACHE_DAYS = 30;

    private const int MISS_CACHE_HOURS = 24;

    public function locate(string $query): ?UserLocation
    {
        $query = trim(preg_replace('/\s+/', ' ', $query) ?? '');

        if ($query === '') {
            return null;
        }

        $key = 'place-geocode:'.md5(mb_strtolower($query));
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return $cached === [] ? null : new UserLocation($cached['latitude'], $cached['longitude'], $cached['name']);
        }

        $place = $this->lookup($query);

        if ($place === false) {
            return null;
        }

        if ($place === null) {
            Cache::put($key, [], now()->addHours(self::MISS_CACHE_HOURS));

            return null;
        }

        Cache::put($key, [
            'latitude' => $place->latitude,
            'longitude' => $place->longitude,
            'name' => $place->placeName,
        ], now()->addDays(self::CACHE_DAYS));

        return $place;
    }

    /**
     * @return UserLocation|false|null the place, null if nothing was found, false if the service failed
     */
    private function lookup(string $query): UserLocation|false|null
    {
        if (! NominatimThrottle::allows()) {
            return false;
        }

        try {
            $response = Http::withUserAgent(config('app.name').' route directory (Laravel)')
                ->acceptJson()
                ->timeout(4)
                ->get(config('cachetrail.geocoding.search_url'), [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'addressdetails' => 1,
                    'accept-language' => 'de',
                ]);
        } catch (Throwable $exception) {
            Log::warning('Place geocoding failed', ['message' => $exception->getMessage()]);

            return false;
        }

        if (! $response->successful()) {
            return false;
        }

        $result = $response->json('0');

        if (! is_array($result) || ! isset($result['lat'], $result['lon'])) {
            return null;
        }

        return new UserLocation((float) $result['lat'], (float) $result['lon'], $this->nameOf($result, $query));
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function nameOf(array $result, string $query): string
    {
        $address = (array) ($result['address'] ?? []);

        foreach (['city', 'town', 'village', 'municipality', 'county', 'state'] as $field) {
            if (! empty($address[$field])) {
                return (string) $address[$field];
            }
        }

        return (string) ($result['name'] ?? $query);
    }
}
