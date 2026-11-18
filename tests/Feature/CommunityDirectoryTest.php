<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Public profiles, sorting by popularity and rating, and popular routes on the home page.
 */
class CommunityDirectoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @param  array<string, mixed>  $figures
     */
    private function publishedTrail(string $name, array $figures): Trail
    {
        $trail = Trail::factory()->published()->create(['name' => $name]);
        $trail->forceFill($figures)->saveQuietly();

        return $trail;
    }

    public function test_routes_can_be_sorted_by_popularity(): void
    {
        $this->publishedTrail('Mittel', ['like_count' => 5]);
        $this->publishedTrail('Top', ['like_count' => 12]);
        $this->publishedTrail('Neu', ['like_count' => 0]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.index', ['sort' => 'popular']))
            ->assertSeeInOrder(['Top', 'Mittel', 'Neu']);
    }

    public function test_best_rated_prefers_routes_with_enough_ratings(): void
    {
        $this->publishedTrail('Einmal perfekt', ['rating_count' => 1, 'average_rating' => 5.0]);
        $this->publishedTrail('Oft sehr gut', ['rating_count' => 10, 'average_rating' => 4.6]);
        $this->publishedTrail('Oft gut', ['rating_count' => 4, 'average_rating' => 4.0]);
        $this->publishedTrail('Unbewertet', ['rating_count' => 0, 'average_rating' => null]);

        $this->actingAs(User::factory()->create())
            ->get(route('trails.index', ['sort' => 'best_rated']))
            ->assertSeeInOrder(['Oft sehr gut', 'Oft gut', 'Einmal perfekt', 'Unbewertet']);
    }

    public function test_the_home_page_shows_popular_routes(): void
    {
        $this->publishedTrail('Beliebte Runde', ['like_count' => 3]);
        $this->publishedTrail('Unbeachtete Runde', ['like_count' => 0, 'published_at' => now()->subYear()]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeInOrder(['Beliebte Routen', 'Beliebte Runde']);
    }

    public function test_public_profiles_show_published_routes_but_never_the_email_address(): void
    {
        $author = User::factory()->create(['name' => 'Erika Muster', 'email' => 'erika@example.com']);
        Trail::factory()->for($author)->published()->create(['name' => 'Erikas Runde', 'like_count' => 4]);
        Trail::factory()->for($author)->create(['name' => 'Erikas Entwurf']);

        $this->actingAs(User::factory()->create())
            ->get($author->profileUrl())
            ->assertOk()
            ->assertSee('Erika Muster')
            ->assertSee('Erikas Runde')
            ->assertDontSee('Erikas Entwurf')
            ->assertDontSee('erika@example.com');

        $this->assertStringEndsWith('/nutzer/Erika%20Muster', $author->profileUrl());
    }

    public function test_other_spellings_redirect_to_the_profile(): void
    {
        $author = User::factory()->create(['name' => 'TestCacher']);

        $this->actingAs(User::factory()->create());

        $this->get('/nutzer/TestCacher')->assertOk();
        $this->get('/nutzer/testcacher')->assertRedirect($author->profileUrl());
        $this->get('/nutzer/'.$author->id)->assertNotFound();
        $this->get('/nutzer/Unbekannt')->assertNotFound();
        $this->get('/nutzer/999999')->assertNotFound();
    }

    public function test_route_cards_link_to_the_creators_profile(): void
    {
        $trail = Trail::factory()->published()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('trails.index'))
            ->assertSee($trail->user->profileUrl());
    }
}
