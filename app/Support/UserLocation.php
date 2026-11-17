<?php

namespace App\Support;

use Illuminate\Contracts\Session\Session;

/**
 * The visitor's approximate location for distance filters and sorting.
 * It is kept in the session only (never in URLs or the database) and rounded to about one kilometre.
 */
final readonly class UserLocation
{
    private const string SESSION_KEY = 'user_location';

    private const int PRECISION = 2;

    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {}

    public static function fromSession(Session $session): ?self
    {
        $stored = $session->get(self::SESSION_KEY);

        return is_array($stored) ? new self((float) $stored['latitude'], (float) $stored['longitude']) : null;
    }

    public static function remember(Session $session, float $latitude, float $longitude): self
    {
        $location = new self(round($latitude, self::PRECISION), round($longitude, self::PRECISION));

        $session->put(self::SESSION_KEY, ['latitude' => $location->latitude, 'longitude' => $location->longitude]);

        return $location;
    }

    public static function forget(Session $session): void
    {
        $session->forget(self::SESSION_KEY);
    }

    /**
     * Distance to a point in meters.
     */
    public function distanceTo(float $latitude, float $longitude): float
    {
        return GeoDistance::between($this->latitude, $this->longitude, $latitude, $longitude);
    }
}
