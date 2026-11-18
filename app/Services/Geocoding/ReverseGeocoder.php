<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Turns coordinates into a place name such as "Moers" using the OpenStreetMap Nominatim service.
 *
 * Requests are made by the server (the visitor's IP address is not sent), only with coordinates
 * already rounded to about 1 km, and cached so the Nominatim usage policy (max. 1 request per second)
 * is respected. Failures return null; the caller then shows the coordinates instead.
 */
class ReverseGeocoder
{
    private const int CACHE_DAYS = 30;

    public function placeName(float $latitude, float $longitude): ?string
    {
        $key = sprintf('reverse-geocode:%.2f,%.2f', $latitude, $longitude);

        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        $name = $this->lookup($latitude, $longitude);

        if ($name !== null) {
            Cache::put($key, $name, now()->addDays(self::CACHE_DAYS));
        }

        return $name;
    }

    private function lookup(float $latitude, float $longitude): ?string
    {
        if (! NominatimThrottle::allows()) {
            return null;
        }

        try {
            $response = Http::withUserAgent(config('app.name').' route directory (Laravel)')
                ->acceptJson()
                ->timeout(4)
                ->get(config('cachetrail.geocoding.reverse_url'), [
                    'format' => 'jsonv2',
                    'lat' => $latitude,
                    'lon' => $longitude,
                    'zoom' => 10,
                    'accept-language' => 'de',
                ]);
        } catch (Throwable $exception) {
            Log::warning('Reverse geocoding failed', ['message' => $exception->getMessage()]);

            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        $address = (array) $response->json('address', []);

        foreach (['city', 'town', 'village', 'municipality', 'county', 'state'] as $field) {
            if (! empty($address[$field])) {
                return (string) $address[$field];
            }
        }

        return null;
    }
}
