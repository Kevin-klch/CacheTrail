<?php

namespace Tests\Feature;

use App\Livewire\TrailRatings;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TrailRatingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_published_route_can_be_rated_with_stars_and_a_comment(): void
    {
        $trail = Trail::factory()->published()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test(TrailRatings::class, ['trail' => $trail])
            ->set('rating', 4)
            ->set('comment', '  Schöne Runde!  ')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Schöne Runde!');

        $rating = TrailRating::sole();
        $this->assertSame(4, $rating->rating);
        $this->assertSame('Schöne Runde!', $rating->comment);

        $trail->refresh();
        $this->assertSame(1, $trail->rating_count);
        $this->assertSame(4.0, $trail->average_rating);
    }

    public function test_the_comment_is_optional_but_stars_are_required(): void
    {
        $trail = Trail::factory()->published()->create();

        Livewire::actingAs(User::factory()->create())->test(TrailRatings::class, ['trail' => $trail])
            ->call('save')
            ->assertHasErrors(['rating' => 'required'])
            ->set('rating', 6)
            ->call('save')
            ->assertHasErrors('rating')
            ->set('rating', 5)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertNull(TrailRating::sole()->comment);
    }

    public function test_a_user_can_rate_a_route_only_once_and_edit_the_rating(): void
    {
        $trail = Trail::factory()->published()->create();
        $user = User::factory()->create();
        $component = Livewire::actingAs($user)->test(TrailRatings::class, ['trail' => $trail]);

        $component->set('rating', 2)->call('save');
        $component->call('edit')
            ->assertSet('editing', true)
            ->assertSet('rating', 2)
            ->set('rating', 5)
            ->call('save');

        $this->assertSame(1, TrailRating::count());
        $this->assertSame(5, TrailRating::sole()->rating);
        $this->assertSame(5.0, $trail->fresh()->average_rating);
    }

    public function test_creators_cannot_rate_their_own_route(): void
    {
        $trail = Trail::factory()->published()->create();

        Livewire::actingAs($trail->user)->test(TrailRatings::class, ['trail' => $trail])
            ->assertSee('Deine eigene Route kannst du nicht bewerten')
            ->set('rating', 5)
            ->call('save')
            ->assertForbidden();

        $this->assertDatabaseCount('trail_ratings', 0);
    }

    public function test_the_creator_can_reply_once_to_each_rating(): void
    {
        $rating = TrailRating::factory()->create();
        $trail = $rating->trail;

        Livewire::actingAs($trail->user)->test(TrailRatings::class, ['trail' => $trail])
            ->call('startReply', $rating->id)
            ->set('reply', 'Danke für dein Feedback!')
            ->call('saveReply')
            ->assertSee('Antwort des Erstellers')
            ->assertSee('Danke für dein Feedback!');

        $this->assertSame('Danke für dein Feedback!', $rating->fresh()->creator_reply);
        $this->assertNotNull($rating->fresh()->creator_replied_at);
    }

    public function test_an_empty_reply_removes_it(): void
    {
        $rating = TrailRating::factory()->create();
        $rating->reply('Alte Antwort');

        Livewire::actingAs($rating->trail->user)->test(TrailRatings::class, ['trail' => $rating->trail])
            ->call('startReply', $rating->id)
            ->assertSet('reply', 'Alte Antwort')
            ->set('reply', '   ')
            ->call('saveReply');

        $this->assertNull($rating->fresh()->creator_reply);
    }

    public function test_only_the_creator_can_reply(): void
    {
        $rating = TrailRating::factory()->create();

        Livewire::actingAs(User::factory()->create())->test(TrailRatings::class, ['trail' => $rating->trail])
            ->call('startReply', $rating->id)
            ->assertForbidden();
    }

    public function test_authors_and_admins_can_delete_ratings_but_others_cannot(): void
    {
        $first = TrailRating::factory()->create();
        $second = TrailRating::factory()->for($first->trail)->create();

        Livewire::actingAs(User::factory()->create())->test(TrailRatings::class, ['trail' => $first->trail])
            ->call('delete', $first->id)
            ->assertForbidden();

        Livewire::actingAs($first->user)->test(TrailRatings::class, ['trail' => $first->trail])
            ->call('delete', $first->id);

        Livewire::actingAs(User::factory()->admin()->create())->test(TrailRatings::class, ['trail' => $first->trail])
            ->call('delete', $second->id);

        $this->assertDatabaseCount('trail_ratings', 0);
        $this->assertSame(0, $first->trail->fresh()->rating_count);
        $this->assertNull($first->trail->fresh()->average_rating);
    }

    public function test_the_detail_page_shows_ratings_and_the_route_cards_the_average(): void
    {
        $trail = Trail::factory()->published()->create(['name' => 'Bewertete Runde']);
        TrailRating::factory()->for($trail)->create(['rating' => 5, 'comment' => 'Top!']);
        TrailRating::factory()->for($trail)->create(['rating' => 4]);
        $trail->refreshCommunityStatistics();

        $this->actingAs(User::factory()->create());

        $this->get(route('trails.show', $trail))
            ->assertOk()
            ->assertSee('Bewertungen')
            ->assertSee('Top!')
            ->assertSee('4,5 / 5');

        $this->get(route('trails.index'))->assertSee('4,5');
    }
}
