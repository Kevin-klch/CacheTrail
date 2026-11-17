<?php

namespace Tests\Unit;

use App\Support\GeoDistance;
use App\Support\RoutePreview;
use PHPUnit\Framework\TestCase;

class GeoTest extends TestCase
{
    public function test_distance_between_two_known_cities(): void
    {
        // Düsseldorf to Cologne, roughly 34 km as the crow flies.
        $this->assertEqualsWithDelta(34_000, GeoDistance::between(51.2277, 6.7735, 50.9375, 6.9603), 1_500);
    }

    public function test_distance_along_a_path_sums_the_segments(): void
    {
        $segment = GeoDistance::between(51.0, 7.0, 51.01, 7.0);

        $this->assertEqualsWithDelta(2 * $segment, GeoDistance::alongPath([[51.0, 7.0], [51.01, 7.0], [51.02, 7.0]]), 0.01);
        $this->assertSame(0.0, GeoDistance::alongPath([[51.0, 7.0]]));
    }

    public function test_route_preview_fits_the_route_into_the_drawing_area(): void
    {
        $points = RoutePreview::project([[51.0, 7.0], [51.01, 7.02], [51.02, 7.0]], 400, 240, 30);

        foreach ($points as [$x, $y]) {
            $this->assertGreaterThanOrEqual(30, $x);
            $this->assertLessThanOrEqual(370, $x);
            $this->assertGreaterThanOrEqual(30, $y);
            $this->assertLessThanOrEqual(210, $y);
        }

        // North is up: the northernmost station has the smallest y.
        $this->assertLessThan($points[0][1], $points[2][1]);
    }

    public function test_a_single_station_is_centered(): void
    {
        $this->assertSame([[200.0, 120.0]], RoutePreview::project([[51.0, 7.0]], 400, 240));
    }
}
