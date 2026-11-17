<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Error pages, page metadata and small guest flows.
 */
class PolishTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_pages_show_the_cachetrail_404_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/routen/gibt-es-nicht')
            ->assertNotFound()
            ->assertSee('Seite nicht gefunden')
            ->assertSee('Zur Startseite');
    }

    public function test_forbidden_pages_show_the_cachetrail_403_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.reports.index'))
            ->assertForbidden()
            ->assertSee('Kein Zugriff');
    }

    public function test_pages_have_a_favicon_and_description(): void
    {
        $this->get(route('home'))
            ->assertSee('favicon.svg')
            ->assertSee('<meta name="description"', false);

        $trail = Trail::factory()->published()->create(['description' => "Eine kurze Waldrunde mit Aussicht.\n\nZweiter Absatz."]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.show', $trail))
            ->assertSee('<meta name="description" content="Eine kurze Waldrunde mit Aussicht.">', false);
    }

    public function test_guests_are_invited_to_register_to_create_routes(): void
    {
        $this->get(route('home'))->assertSee(route('register'));
    }
}
