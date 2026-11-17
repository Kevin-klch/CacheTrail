<?php

namespace Tests\Feature;

use App\Livewire\FavoriteButton;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_route_can_be_saved_and_removed_as_favorite(): void
    {
        $user = User::factory()->create();
        $trail = Trail::factory()->published()->create();

        $component = Livewire::actingAs($user)->test(FavoriteButton::class, ['trail' => $trail])
            ->assertSet('favorited', false)
            ->call('toggle')
            ->assertSet('favorited', true);

        $this->assertTrue($trail->isFavoritedBy($user));
        $this->assertSame(1, $trail->fresh()->favorite_count);

        $component->call('toggle')->assertSet('favorited', false);

        $this->assertFalse($trail->isFavoritedBy($user));
        $this->assertSame(0, $trail->fresh()->favorite_count);
    }

    public function test_drafts_of_others_cannot_be_saved_as_favorite(): void
    {
        $trail = Trail::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test(FavoriteButton::class, ['trail' => $trail])
            ->call('toggle')
            ->assertForbidden();

        $this->assertDatabaseCount('trail_favorites', 0);
    }

    public function test_the_favorites_page_lists_only_published_favorites(): void
    {
        $user = User::factory()->create();
        $favorite = Trail::factory()->published()->create(['name' => 'Lieblingsrunde']);
        $hidden = Trail::factory()->hidden()->create(['name' => 'Ausgeblendete Runde']);
        Trail::factory()->published()->create(['name' => 'Andere Runde']);
        $user->favoriteTrails()->attach([$favorite->id, $hidden->id]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('Lieblingsrunde')
            ->assertDontSee('Ausgeblendete Runde')
            ->assertDontSee('Andere Runde');
    }

    public function test_the_favorites_page_explains_how_to_add_favorites(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('Noch keine Favoriten');
    }

    public function test_deleting_a_user_removes_their_favorites(): void
    {
        $user = User::factory()->create();
        $user->favoriteTrails()->attach(Trail::factory()->published()->create());

        $user->delete();

        $this->assertDatabaseCount('trail_favorites', 0);
    }
}
