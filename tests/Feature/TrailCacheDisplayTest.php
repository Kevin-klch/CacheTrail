<?php

namespace Tests\Feature;

use App\Enums\CacheStatus;
use App\Models\Trail;
use App\Models\TrailCache;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailCacheDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_detail_page_lists_the_caches_in_order_with_status_hints(): void
    {
        $trail = Trail::factory()->published()->create();
        TrailCache::factory()->for($trail)->create(['position' => 2, 'cache_name' => 'Das Finale', 'cache_status' => CacheStatus::Archived]);
        TrailCache::factory()->for($trail)->create(['position' => 0, 'cache_name' => 'Der alte Baum']);
        TrailCache::factory()->for($trail)->create(['position' => 1, 'cache_name' => 'Waldgeheimnis', 'cache_status' => CacheStatus::TemporarilyDisabled]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertOk()
            ->assertSeeInOrder(['Der alte Baum', 'Waldgeheimnis', 'Das Finale'])
            ->assertSee('Dieser Cache ist momentan nicht verfügbar.')
            ->assertSee('Diese Station ist nicht mehr verfügbar.')
            ->assertSee('Beispieldaten');
    }

    public function test_mock_caches_are_not_linked_to_geocaching_com(): void
    {
        $trail = Trail::factory()->published()->withCaches(2)->create();

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertDontSee('geocaching.com/geocache', false)
            ->assertDontSee('coord.info', false);
    }

    public function test_route_cards_show_the_number_of_caches(): void
    {
        Trail::factory()->published()->withCaches(3)->create();

        $this->actingAs(User::factory()->create())
            ->get(route('trails.index'))
            ->assertSee('3 Caches');
    }

    public function test_deleting_a_trail_deletes_its_caches(): void
    {
        $trail = Trail::factory()->withCaches(2)->create();

        $this->actingAs($trail->user)->delete(route('trails.destroy', $trail));

        $this->assertDatabaseCount('trail_caches', 0);
    }
}
