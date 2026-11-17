<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use App\Support\UserLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Filters, sorting, location and map view of the route directory (phase 4b).
 */
class TrailDirectoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    /**
     * @param  array<string, mixed>  $figures
     */
    private function publishedTrail(string $name, array $figures): Trail
    {
        $trail = Trail::factory()->published()->create(['name' => $name]);
        $trail->forceFill($figures)->saveQuietly();

        return $trail;
    }

    public function test_trails_can_be_filtered_by_number_of_caches(): void
    {
        $this->publishedTrail('Wenige', ['cache_count' => 3]);
        $this->publishedTrail('Mittel', ['cache_count' => 8]);
        $this->publishedTrail('Viele', ['cache_count' => 20]);

        $this->get(route('trails.index', ['min_caches' => 5, 'max_caches' => 10]))
            ->assertSee('Mittel')
            ->assertDontSee('Wenige')
            ->assertDontSee('Viele');
    }

    public function test_trails_can_be_filtered_by_length_in_kilometers(): void
    {
        $this->publishedTrail('Kurz', ['total_distance_meters' => 800]);
        $this->publishedTrail('Mittel', ['total_distance_meters' => 4500]);
        $this->publishedTrail('Lang', ['total_distance_meters' => 12000]);

        $this->get(route('trails.index', ['min_km' => 1, 'max_km' => 5]))
            ->assertSee('Mittel')
            ->assertDontSee('Kurz')
            ->assertDontSee('Lang');
    }

    public function test_trails_can_be_filtered_by_average_difficulty_and_terrain(): void
    {
        $this->publishedTrail('Leicht', ['average_difficulty' => 1.5, 'average_terrain' => 1.5]);
        $this->publishedTrail('Knifflig', ['average_difficulty' => 3.5, 'average_terrain' => 2.0]);
        $this->publishedTrail('Kletterei', ['average_difficulty' => 3.5, 'average_terrain' => 4.5]);

        $this->get(route('trails.index', ['min_difficulty' => 3, 'max_terrain' => 3]))
            ->assertSee('Knifflig')
            ->assertDontSee('Leicht')
            ->assertDontSee('Kletterei');
    }

    public function test_out_of_range_filters_are_rejected(): void
    {
        $this->get(route('trails.index', ['min_difficulty' => 7, 'radius' => 3, 'sort' => 'random']))
            ->assertSessionHasErrors(['min_difficulty', 'radius', 'sort']);
    }

    public function test_trails_can_be_sorted_by_length(): void
    {
        $this->publishedTrail('Mittel', ['total_distance_meters' => 4000]);
        $this->publishedTrail('Ohne Caches', ['total_distance_meters' => null]);
        $this->publishedTrail('Kurz', ['total_distance_meters' => 900]);
        $this->publishedTrail('Lang', ['total_distance_meters' => 9000]);

        $this->get(route('trails.index', ['sort' => 'shortest']))->assertSeeInOrder(['Kurz', 'Mittel', 'Lang', 'Ohne Caches']);
        $this->get(route('trails.index', ['sort' => 'longest']))->assertSeeInOrder(['Lang', 'Mittel', 'Kurz', 'Ohne Caches']);
    }

    public function test_the_location_is_stored_rounded_in_the_session_only(): void
    {
        Http::fake(['*' => Http::response(['address' => ['town' => 'Ratingen']])]);

        $this->post(route('location.store'), ['latitude' => 51.234567, 'longitude' => 6.789012, 'return_query' => 'q=wald'])
            ->assertRedirect(route('trails.index', ['q' => 'wald', 'sort' => 'nearest']));

        $location = UserLocation::fromSession(session()->driver());

        $this->assertSame(51.23, $location->latitude);
        $this->assertSame(6.79, $location->longitude);
    }

    public function test_invalid_locations_are_rejected(): void
    {
        $this->post(route('location.store'), ['latitude' => 120, 'longitude' => 6.7])
            ->assertSessionHasErrors('latitude');
    }

    public function test_the_location_can_be_removed(): void
    {
        $this->withSession(['user_location' => ['latitude' => 51.0, 'longitude' => 7.0]])
            ->delete(route('location.destroy'), ['return_query' => 'sort=nearest&radius=10&q=wald'])
            ->assertRedirect(route('trails.index', ['q' => 'wald']));

        $this->assertNull(UserLocation::fromSession(session()->driver()));
    }

    public function test_routes_can_be_sorted_by_distance_and_filtered_by_radius(): void
    {
        // About 1.1 km, 11 km and 55 km north of the location.
        $this->publishedTrail('Nah', ['start_latitude' => 51.01, 'start_longitude' => 7.0]);
        $this->publishedTrail('Mittel', ['start_latitude' => 51.10, 'start_longitude' => 7.0]);
        $this->publishedTrail('Fern', ['start_latitude' => 51.50, 'start_longitude' => 7.0]);
        $this->publishedTrail('Ohne Start', ['start_latitude' => null, 'start_longitude' => null]);

        $this->withSession(['user_location' => ['latitude' => 51.0, 'longitude' => 7.0]]);

        $this->get(route('trails.index', ['sort' => 'nearest']))
            ->assertSeeInOrder(['Nah', 'Mittel', 'Fern', 'Ohne Start'])
            ->assertSee('ca. 1,1 km entfernt');

        $this->get(route('trails.index', ['radius' => 25]))
            ->assertSee('Nah')
            ->assertSee('Mittel')
            ->assertDontSee('Fern')
            ->assertDontSee('Ohne Start');
    }

    public function test_nearest_sort_and_radius_are_ignored_without_a_location(): void
    {
        $this->publishedTrail('Ältere', ['start_latitude' => 51.0, 'start_longitude' => 7.0, 'published_at' => now()->subDay()]);
        $this->publishedTrail('Neuere', ['start_latitude' => 52.0, 'start_longitude' => 7.0, 'published_at' => now()]);

        $this->get(route('trails.index', ['sort' => 'nearest', 'radius' => 5]))
            ->assertOk()
            ->assertSeeInOrder(['Neuere', 'Ältere'])
            ->assertSee('Sortiert nach: Neueste');
    }

    public function test_the_map_view_lists_route_starts_without_pagination(): void
    {
        $this->publishedTrail('Waldrunde', ['start_latitude' => 51.4, 'start_longitude' => 6.5, 'cache_count' => 4]);
        $this->publishedTrail('Ohne Caches', ['start_latitude' => null, 'start_longitude' => null]);

        $this->get(route('trails.index', ['view' => 'map']))
            ->assertOk()
            ->assertSee('x-data="directoryMap(', false)
            ->assertSee('Waldrunde')
            ->assertSee('1 Route auf der Karte')
            ->assertDontSee('Ohne Caches');
    }
}
