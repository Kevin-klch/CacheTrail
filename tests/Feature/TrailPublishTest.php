<?php

namespace Tests\Feature;

use App\Enums\TrailVisibility;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrailPublishTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_preview_shows_the_route_as_others_will_see_it(): void
    {
        $trail = Trail::factory()->withCaches(2)->create(['name' => 'Vorschau-Runde']);

        $this->actingAs($trail->user)
            ->get(route('trails.preview', $trail))
            ->assertOk()
            ->assertSee('Vorschau-Runde')
            ->assertSee('So sehen andere Geocacher deine Route.')
            ->assertSee(route('trails.publish', $trail))
            ->assertDontSee('Route bearbeiten');
    }

    public function test_other_users_cannot_open_the_preview_or_publish(): void
    {
        $trail = Trail::factory()->withCaches(2)->create();
        $this->actingAs(User::factory()->create());

        $this->get(route('trails.preview', $trail))->assertForbidden();
        $this->post(route('trails.publish', $trail))->assertForbidden();

        $this->assertSame(TrailVisibility::Draft, $trail->fresh()->visibility);
    }

    public function test_publishing_requires_at_least_two_caches(): void
    {
        $trail = Trail::factory()->withCaches(1)->create();

        $this->actingAs($trail->user)
            ->post(route('trails.publish', $trail))
            ->assertRedirect(route('trails.caches', $trail))
            ->assertSessionHasErrors('publish');

        $this->assertSame(TrailVisibility::Draft, $trail->fresh()->visibility);
    }

    public function test_a_trail_with_enough_caches_can_be_published(): void
    {
        $trail = Trail::factory()->withCaches(2)->create();

        $this->actingAs($trail->user)
            ->post(route('trails.publish', $trail))
            ->assertRedirect(route('trails.show', $trail));

        $this->assertSame(TrailVisibility::Published, $trail->fresh()->visibility);
        $this->assertNotNull($trail->fresh()->published_at);
    }
}
