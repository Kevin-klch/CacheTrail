<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_trails_are_visible_to_other_users(): void
    {
        $trail = Trail::factory()->published()->create(['name' => 'Waldrunde']);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertOk()
            ->assertSee('Waldrunde')
            ->assertDontSee(route('trails.edit', $trail));
    }

    public function test_drafts_are_only_visible_to_the_owner_and_admins(): void
    {
        $trail = Trail::factory()->create();

        $this->actingAs(User::factory()->create())->get(route('trails.show', $trail))->assertForbidden();
        $this->actingAs($trail->user)->get(route('trails.show', $trail))->assertOk()->assertSee('Entwurf');
        $this->actingAs(User::factory()->admin()->create())->get(route('trails.show', $trail))->assertOk();
    }

    public function test_hidden_trails_are_not_visible_to_other_users(): void
    {
        $trail = Trail::factory()->hidden()->create();

        $this->actingAs(User::factory()->create())->get(route('trails.show', $trail))->assertForbidden();
        $this->actingAs($trail->user)->get(route('trails.show', $trail))->assertOk();
    }

    public function test_the_route_list_only_shows_published_trails(): void
    {
        Trail::factory()->published()->create(['name' => 'Sichtbare Runde']);
        Trail::factory()->create(['name' => 'Geheimer Entwurf']);
        Trail::factory()->hidden()->create(['name' => 'Ausgeblendete Runde']);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.index'))
            ->assertOk()
            ->assertSee('Sichtbare Runde')
            ->assertDontSee('Geheimer Entwurf')
            ->assertDontSee('Ausgeblendete Runde');
    }

    public function test_the_home_page_shows_the_latest_published_trails(): void
    {
        Trail::factory()->published()->create(['name' => 'Ältere Runde', 'published_at' => now()->subDays(10)]);
        Trail::factory()->published()->create(['name' => 'Neueste Runde', 'published_at' => now()]);
        Trail::factory()->create(['name' => 'Geheimer Entwurf']);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Neueste Runde', 'Ältere Runde'])
            ->assertDontSee('Geheimer Entwurf');
    }

    public function test_my_routes_lists_only_the_users_own_trails_including_drafts(): void
    {
        $user = User::factory()->create();
        Trail::factory()->for($user)->create(['name' => 'Mein Entwurf']);
        Trail::factory()->for($user)->published()->create(['name' => 'Meine Runde']);
        Trail::factory()->published()->create(['name' => 'Fremde Runde']);

        $this->actingAs($user)
            ->get(route('trails.mine'))
            ->assertOk()
            ->assertSee('Mein Entwurf')
            ->assertSee('Meine Runde')
            ->assertDontSee('Fremde Runde');
    }
}
