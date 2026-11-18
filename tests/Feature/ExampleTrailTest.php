<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_configured_example_route(): void
    {
        $trail = Trail::factory()->published()->withCaches(3)->create(['name' => 'Waldrunde']);
        Trail::factory()->published()->withCaches(3)->create(['name' => 'Andere Runde', 'like_count' => 10]);
        config(['cachetrail.trails.example' => $trail->slug]);

        $this->get(route('trails.example'))
            ->assertOk()
            ->assertSee('Waldrunde')
            ->assertSee('Caches dieser Route')
            ->assertSee(route('register'))
            ->assertDontSee(route('trails.edit', $trail))
            ->assertDontSee($trail->user->profileUrl());
    }

    public function test_the_most_liked_route_is_used_when_the_configured_one_is_not_available(): void
    {
        config(['cachetrail.trails.example' => 'gibt-es-nicht']);
        Trail::factory()->published()->withCaches(3)->create(['name' => 'Wenig beliebt', 'like_count' => 1]);
        Trail::factory()->published()->withCaches(3)->create(['name' => 'Sehr beliebt', 'like_count' => 5]);
        Trail::factory()->withCaches(3)->create(['name' => 'Entwurf', 'like_count' => 50]);

        $this->get(route('trails.example'))->assertOk()->assertSee('Sehr beliebt');
    }

    public function test_the_example_page_is_not_found_without_published_routes(): void
    {
        Trail::factory()->withCaches(3)->create();

        $this->get(route('trails.example'))->assertNotFound();
    }

    public function test_the_home_page_teases_the_example_route(): void
    {
        $trail = Trail::factory()->published()->withCaches(3)->create(['name' => 'Waldrunde']);
        config(['cachetrail.trails.example' => $trail->slug]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['So funktioniert CacheTrail', 'Beispielroute', 'Waldrunde'])
            ->assertSee(route('trails.example'));
    }

    public function test_logged_in_users_see_a_link_to_all_routes_instead_of_the_sign_up(): void
    {
        $trail = Trail::factory()->published()->withCaches(3)->create();
        config(['cachetrail.trails.example' => $trail->slug]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.example'))
            ->assertOk()
            ->assertSee('Alle Routen entdecken')
            ->assertDontSee('Kostenlos registrieren');
    }
}
