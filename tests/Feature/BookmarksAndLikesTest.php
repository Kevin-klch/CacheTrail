<?php

namespace Tests\Feature;

use App\Livewire\BookmarkButton;
use App\Livewire\LikeButton;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BookmarksAndLikesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_route_can_be_saved_for_later_and_removed_again(): void
    {
        $user = User::factory()->create();
        $trail = Trail::factory()->published()->create();

        $component = Livewire::actingAs($user)->test(BookmarkButton::class, ['trail' => $trail])
            ->assertSet('bookmarked', false)
            ->call('toggle')
            ->assertSet('bookmarked', true);

        $this->assertTrue($trail->isBookmarkedBy($user));
        $this->assertSame(0, $trail->fresh()->like_count, 'Bookmarks are private and do not count as likes.');

        $component->call('toggle')->assertSet('bookmarked', false);
        $this->assertFalse($trail->isBookmarkedBy($user));
    }

    public function test_a_route_can_be_liked_and_unliked(): void
    {
        $user = User::factory()->create();
        $trail = Trail::factory()->published()->create();

        $component = Livewire::actingAs($user)->test(LikeButton::class, ['trail' => $trail])
            ->call('toggle')
            ->assertSet('liked', true);

        $this->assertTrue($trail->isLikedBy($user));
        $this->assertSame(1, $trail->fresh()->like_count);
        $this->assertFalse($trail->isBookmarkedBy($user), 'Likes do not put a route on the list.');

        $component->call('toggle')->assertSet('liked', false);
        $this->assertSame(0, $trail->fresh()->like_count);
    }

    public function test_drafts_of_others_cannot_be_bookmarked_or_liked(): void
    {
        $trail = Trail::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(BookmarkButton::class, ['trail' => $trail])->call('toggle')->assertForbidden();
        Livewire::actingAs($user)->test(LikeButton::class, ['trail' => $trail])->call('toggle')->assertForbidden();

        $this->assertDatabaseCount('trail_bookmarks', 0);
        $this->assertDatabaseCount('trail_likes', 0);
    }

    public function test_the_list_shows_only_published_bookmarked_routes(): void
    {
        $user = User::factory()->create();
        $saved = Trail::factory()->published()->create(['name' => 'Gemerkte Runde']);
        $hidden = Trail::factory()->hidden()->create(['name' => 'Ausgeblendete Runde']);
        Trail::factory()->published()->create(['name' => 'Andere Runde']);
        $user->bookmarkedTrails()->attach([$saved->id, $hidden->id]);

        $this->actingAs($user)
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertSee('Für später gemerkt')
            ->assertSee('Gemerkte Runde')
            ->assertDontSee('Ausgeblendete Runde')
            ->assertDontSee('Andere Runde');
    }

    public function test_the_empty_list_explains_how_to_save_routes(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('bookmarks.index'))
            ->assertOk()
            ->assertSee('Deine Merkliste ist leer');
    }

    public function test_the_old_favorites_address_redirects_to_the_list(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/favoriten')
            ->assertRedirect('/merkliste');
    }

    public function test_deleting_a_user_removes_their_bookmarks_and_likes(): void
    {
        $user = User::factory()->create();
        $trail = Trail::factory()->published()->create();
        $user->bookmarkedTrails()->attach($trail);
        $user->likedTrails()->attach($trail);

        $user->delete();

        $this->assertDatabaseCount('trail_bookmarks', 0);
        $this->assertDatabaseCount('trail_likes', 0);
    }
}
