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
        public ?string $placeName = null,
    ) {}

    public static function fromSession(Session $session): ?self
    {
        $stored = $session->get(self::SESSION_KEY);

        return is_array($stored)
            ? new self((float) $stored['latitude'], (float) $stored['longitude'], $stored['place_name'] ?? null)
            : null;
    }

    /**
     * @param  (callable(float, float): ?string)|null  $placeNameResolver  looks up a place name for the rounded coordinates
     */
    public static function remember(Session $session, float $latitude, float $longitude, ?callable $placeNameResolver = null): self
    {
        $latitude = round($latitude, self::PRECISION);
        $longitude = round($longitude, self::PRECISION);
        $location = new self($latitude, $longitude, $placeNameResolver ? $placeNameResolver($latitude, $longitude) : null);

        $session->put(self::SESSION_KEY, [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'place_name' => $location->placeName,
        ]);

        return $location;
    }

    public static function forget(Session $session): void
    {
        $session->forget(self::SESSION_KEY);
    }

    /**
     * The place name, or the rounded coordinates if no name is known, e.g. "Moers" or "51,46° N, 6,63° O".
     */
    public function label(): string
    {
        return $this->placeName ?? sprintf(
            '%s° %s, %s° %s',
            number_format(abs($this->latitude), 2, ',', ''),
            $this->latitude >= 0 ? 'N' : 'S',
            number_format(abs($this->longitude), 2, ',', ''),
            $this->longitude >= 0 ? 'O' : 'W',
        );
    }

    /**
     * Distance to a point in meters.
     */
    public function distanceTo(float $latitude, float $longitude): float
    {
        return GeoDistance::between($this->latitude, $this->longitude, $latitude, $longitude);
    }
}
