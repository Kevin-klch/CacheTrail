<?php

namespace App\Services\Geocoding;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Keeps the server within the Nominatim usage policy (at most one request per second) and stops a
 * single visitor from triggering lookups without end. When a lookup is not allowed, the caller
 * behaves as if the service were unavailable.
 */
final class NominatimThrottle
{
    /** Lookups one visitor may trigger within ten minutes (cached results do not count). */
    public const int PER_VISITOR = 30;

    public static function allows(): bool
    {
        $visitor = 'nominatim:'.(auth()->id() ?? request()->ip());

        if (RateLimiter::tooManyAttempts($visitor, self::PER_VISITOR)) {
            return false;
        }

        if (! RateLimiter::attempt('nominatim', 1, fn (): bool => true, 1)) {
            return false;
        }

        RateLimiter::hit($visitor, 600);

        return true;
    }
}
