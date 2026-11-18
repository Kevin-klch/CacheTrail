<?php

namespace App\Support;

/**
 * Reads coordinates as geocachers write them, e.g. "N 51° 28.338 E 006° 31.212" (degrees and decimal
 * minutes, as on Geocaching.com) or "51.4723, 6.5202" (decimal degrees, as in map apps).
 */
final class Coordinates
{
    /**
     * @return array{latitude: float, longitude: float}|null
     */
    public static function parse(string $input): ?array
    {
        $input = trim(str_replace(['′', "'", '’'], '', $input));

        // Degrees and decimal minutes: N 51° 28.338 E 006° 31.212
        if (preg_match('/^([NS])\s*(\d{1,2})[°\s]\s*(\d{1,2}(?:[.,]\d+)?)\s*,?\s*([EOW])\s*(\d{1,3})[°\s]\s*(\d{1,2}(?:[.,]\d+)?)$/iu', $input, $m) === 1) {
            $latitude = ((int) $m[2] + self::number($m[3]) / 60) * (strtoupper($m[1]) === 'S' ? -1 : 1);
            $longitude = ((int) $m[5] + self::number($m[6]) / 60) * (strtoupper($m[4]) === 'W' ? -1 : 1);

            return self::valid($latitude, $longitude);
        }

        // Decimal degrees: 51.4723, 6.5202 (comma, semicolon or space between)
        if (preg_match('/^(-?\d{1,2}(?:\.\d+)?)\s*[,;\s]\s*(-?\d{1,3}(?:\.\d+)?)$/', $input, $m) === 1) {
            return self::valid((float) $m[1], (float) $m[2]);
        }

        return null;
    }

    /**
     * Geocaching notation, e.g. "N 51° 28.338 E 006° 31.212".
     */
    public static function format(float $latitude, float $longitude): string
    {
        $part = function (float $value, string $positive, string $negative, int $degreeDigits): string {
            $absolute = abs($value);
            $degrees = (int) floor($absolute);
            $minutes = ($absolute - $degrees) * 60;

            return sprintf('%s %0'.$degreeDigits.'d° %06.3f', $value < 0 ? $negative : $positive, $degrees, $minutes);
        };

        return $part($latitude, 'N', 'S', 2).' '.$part($longitude, 'E', 'W', 3);
    }

    private static function number(string $value): float
    {
        return (float) str_replace(',', '.', $value);
    }

    /**
     * @return array{latitude: float, longitude: float}|null
     */
    private static function valid(float $latitude, float $longitude): ?array
    {
        if (abs($latitude) > 90 || abs($longitude) > 180) {
            return null;
        }

        return ['latitude' => round($latitude, 6), 'longitude' => round($longitude, 6)];
    }
}
