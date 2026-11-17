<?php

namespace Tests\Feature;

use App\Livewire\CompleteButton;
use App\Models\GeocachingAccount;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class CompletionsAndProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_route_can_be_marked_as_completed_and_undone(): void
    {
        $user = User::factory()->create();
        $trail = Trail::factory()->published()->create();

        $component = Livewire::actingAs($user)->test(CompleteButton::class, ['trail' => $trail])
            ->assertSee('Als absolviert markieren')
            ->call('toggle')
            ->assertSee('Absolviert am');

        $this->assertTrue($trail->isCompletedBy($user));

        $component->call('toggle')->assertSee('Als absolviert markieren');
        $this->assertFalse($trail->isCompletedBy($user));
    }

    public function test_drafts_of_others_cannot_be_completed(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(CompleteButton::class, ['trail' => Trail::factory()->create()])
            ->call('toggle')
            ->assertForbidden();

        $this->assertDatabaseCount('trail_completions', 0);
    }

    public function test_the_profile_shows_completions_and_the_last_completed_route(): void
    {
        $user = User::factory()->create();
        $older = Trail::factory()->published()->create(['name' => 'Alte Runde']);
        $newer = Trail::factory()->published()->create(['name' => 'Neue Runde']);
        $user->completedTrails()->attach($older, ['completed_at' => now()->subMonth()]);
        $user->completedTrails()->attach($newer, ['completed_at' => now()->subDay()]);

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertOk()
            ->assertSeeInOrder(['Zuletzt absolviert:', 'Neue Runde'])
            ->assertSeeInOrder(['Absolvierte Routen', '2']);
    }

    public function test_the_info_box_can_be_edited_and_is_shown_on_the_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('profile.bio.update'), ['bio' => '  Ich cache am liebsten im Wald.  '])
            ->assertSessionHasNoErrors();

        $this->assertSame('Ich cache am liebsten im Wald.', $user->fresh()->bio);

        $this->get($user->profileUrl())->assertSee('Ich cache am liebsten im Wald.');

        $this->put(route('profile.bio.update'), ['bio' => '']);
        $this->assertNull($user->fresh()->bio);
    }

    public function test_the_info_box_is_limited_in_length(): void
    {
        $this->actingAs(User::factory()->create())
            ->put(route('profile.bio.update'), ['bio' => str_repeat('a', 501)])
            ->assertSessionHasErrorsIn('updateBio', 'bio');
    }

    public function test_geocaching_figures_are_shown_when_an_account_is_connected(): void
    {
        $user = User::factory()->create();
        GeocachingAccount::factory()->for($user)->create(['username' => 'Waldfuchs', 'finds_count' => 1234, 'member_since' => '2015-04-12']);

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertSee('Auf Geocaching.com: Waldfuchs')
            ->assertSee('1.234')
            ->assertSee('April 2015');
    }

    public function test_profiles_without_a_connected_account_explain_the_missing_figures(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertSee('Die Zahlen von Geocaching.com erscheinen, sobald das Konto verbunden ist.');

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertSee('Noch nicht verbunden.');
    }

    public function test_tokens_of_geocaching_accounts_are_stored_encrypted(): void
    {
        $account = GeocachingAccount::factory()->create();
        $account->forceFill(['access_token' => 'secret-token'])->save();

        $this->assertNotSame('secret-token', DB::table('geocaching_accounts')->value('access_token'));
        $this->assertSame('secret-token', $account->fresh()->access_token);
    }
}
