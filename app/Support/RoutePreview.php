<?php

namespace App\Support;

/**
 * Projects route coordinates into a drawing area for the schematic route preview,
 * keeping the real proportions of the route and centering it.
 */
final class RoutePreview
{
    /**
     * @param  list<array{0: float, 1: float}>  $coordinates  latitude/longitude pairs in route order
     * @return list<array{0: float, 1: float}> x/y points within the drawing area
     */
    public static function project(array $coordinates, int $width = 400, int $height = 240, int $padding = 34): array
    {
        if ($coordinates === []) {
            return [];
        }

        $meanLatitude = array_sum(array_column($coordinates, 0)) / count($coordinates);
        $longitudeScale = cos(deg2rad($meanLatitude));

        // Equirectangular projection: good enough for the small area of a route.
        $projected = array_map(
            fn (array $coordinate): array => [$coordinate[1] * $longitudeScale, -$coordinate[0]],
            $coordinates,
        );

        $xs = array_column($projected, 0);
        $ys = array_column($projected, 1);
        $spanX = max($xs) - min($xs);
        $spanY = max($ys) - min($ys);

        $availableWidth = $width - 2 * $padding;
        $availableHeight = $height - 2 * $padding;
        $scale = ($spanX == 0.0 && $spanY == 0.0)
            ? 0.0
            : min(
                $spanX > 0 ? $availableWidth / $spanX : PHP_FLOAT_MAX,
                $spanY > 0 ? $availableHeight / $spanY : PHP_FLOAT_MAX,
            );

        $offsetX = ($width - $spanX * $scale) / 2;
        $offsetY = ($height - $spanY * $scale) / 2;

        return array_map(
            fn (array $point): array => [
                round($offsetX + ($point[0] - min($xs)) * $scale, 1),
                round($offsetY + ($point[1] - min($ys)) * $scale, 1),
            ],
            $projected,
        );
    }
}
