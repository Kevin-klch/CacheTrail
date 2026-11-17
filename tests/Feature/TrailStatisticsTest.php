<?php

namespace Tests\Feature;

use App\Actions\Trails\AddCachesToTrail;
use App\Enums\DistanceMethod;
use App\Enums\TrailVisibility;
use App\Models\Trail;
use App\Models\TrailCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailStatisticsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Three caches on a line with exactly 0.01° latitude between them (about 1112 m each).
     */
    private function trailWithKnownCaches(): Trail
    {
        $trail = Trail::factory()->create();

        foreach ([[51.0, 2.0, 1.0], [51.01, 3.0, 2.0], [51.02, 1.0, 4.5]] as $position => [$latitude, $difficulty, $terrain]) {
            TrailCache::factory()->for($trail)->create([
                'position' => $position,
                'latitude' => $latitude,
                'longitude' => 7.0,
                'difficulty' => $difficulty,
                'terrain' => $terrain,
            ]);
        }

        $trail->refreshStatistics();

        return $trail->fresh();
    }

    public function test_averages_count_distance_and_start_are_calculated(): void
    {
        $trail = $this->trailWithKnownCaches();

        $this->assertSame(3, $trail->cache_count);
        $this->assertSame(2.0, $trail->average_difficulty);
        $this->assertSame(2.5, $trail->average_terrain);
        $this->assertEqualsWithDelta(2224, $trail->total_distance_meters, 2);
        $this->assertSame(DistanceMethod::StraightLine, $trail->distance_method);
        $this->assertSame(51.0, $trail->start_latitude);
    }

    public function test_figures_are_formatted_for_display(): void
    {
        $trail = $this->trailWithKnownCaches();

        $this->assertSame('ca. 2,2 km', $trail->distanceLabel());
        $this->assertSame('2,0 / 5', Trail::ratingAverageLabel($trail->average_difficulty));
        $this->assertSame('ca. 850 m', (new Trail)->forceFill(['total_distance_meters' => 847])->distanceLabel());
    }

    public function test_the_distance_follows_the_creators_order(): void
    {
        $trail = $this->trailWithKnownCaches();
        $straight = $trail->total_distance_meters;

        // Moving the middle station to the end makes the route go back and forth.
        $trail->moveCache($trail->caches()->where('position', 1)->first(), 2);

        $this->assertEqualsWithDelta($straight + 1112, $trail->fresh()->total_distance_meters, 2);
    }

    public function test_figures_are_updated_when_caches_are_added_and_removed(): void
    {
        $trail = Trail::factory()->create();
        $this->app->make(AddCachesToTrail::class)->handle($trail, 'GCCT01 GCCT02 GCCT03');

        $this->assertSame(3, $trail->fresh()->cache_count);

        $trail->removeCache($trail->caches()->first());

        $trail->refresh();
        $this->assertSame(2, $trail->cache_count);
        $this->assertSame('GCCT02', $trail->caches()->first()->cache_code);
        $this->assertSame($trail->caches()->first()->latitude, $trail->start_latitude);
    }

    public function test_a_trail_without_caches_has_no_figures(): void
    {
        $trail = Trail::factory()->withCaches(1)->create();
        $trail->removeCache($trail->caches()->first());

        $trail->refresh();
        $this->assertSame(0, $trail->cache_count);
        $this->assertNull($trail->total_distance_meters);
        $this->assertNull($trail->average_difficulty);
        $this->assertNull($trail->distanceLabel());
    }

    public function test_the_refresh_command_recalculates_all_trails(): void
    {
        $trail = Trail::factory()->withCaches(3)->create();
        $trail->forceFill(['cache_count' => 0, 'total_distance_meters' => null])->saveQuietly();

        $this->artisan('trails:refresh-statistics')->assertSuccessful();

        $this->assertSame(3, $trail->fresh()->cache_count);
        $this->assertNotNull($trail->fresh()->total_distance_meters);
    }

    public function test_the_detail_page_shows_the_figures_and_the_map(): void
    {
        $trail = $this->trailWithKnownCaches();
        $trail->changeVisibility(TrailVisibility::Published);
        $trail->save();

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertOk()
            ->assertSee('ca. 2,2 km')
            ->assertSee('Luftlinie zwischen den Stationen')
            ->assertSee('2,0 / 5')
            ->assertSee('2,5 / 5')
            ->assertSee('x-data="trailMap(', false);
    }
}
