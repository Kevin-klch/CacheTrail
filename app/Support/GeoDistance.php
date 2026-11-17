<?php

namespace App\Support;

/**
 * Distances between coordinates on the earth's surface.
 */
final class GeoDistance
{
    private const int EARTH_RADIUS_METERS = 6_371_000;

    /**
     * Great-circle distance (haversine formula) in meters.
     */
    public static function between(float $latitudeA, float $longitudeA, float $latitudeB, float $longitudeB): float
    {
        $deltaLatitude = deg2rad($latitudeB - $latitudeA);
        $deltaLongitude = deg2rad($longitudeB - $longitudeA);

        $a = sin($deltaLatitude / 2) ** 2
            + cos(deg2rad($latitudeA)) * cos(deg2rad($latitudeB)) * sin($deltaLongitude / 2) ** 2;

        return self::EARTH_RADIUS_METERS * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Sum of the straight-line distances along the given points in their order.
     *
     * @param  iterable<array{0: float, 1: float}>  $points  latitude/longitude pairs
     */
    public static function alongPath(iterable $points): float
    {
        $total = 0.0;
        $previous = null;

        foreach ($points as $point) {
            if ($previous !== null) {
                $total += self::between($previous[0], $previous[1], $point[0], $point[1]);
            }

            $previous = $point;
        }

        return $total;
    }
}
