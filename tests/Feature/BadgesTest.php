<?php

namespace Tests\Feature;

use App\Enums\Badge;
use App\Enums\Transportation;
use App\Livewire\CompleteButton;
use App\Livewire\LikeButton;
use App\Livewire\TrailRatings;
use App\Models\Trail;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\Badges\BadgeAwarder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BadgesTest extends TestCase
{
    use RefreshDatabase;

    private function hasBadge(User $user, Badge $badge, int $level): bool
    {
        return $user->badges()->where('badge', $badge)->where('level', $level)->exists();
    }

    /**
     * Completes the given number of published routes for the user, without going through the UI.
     *
     * @param  array<string, mixed>  $figures
     */
    private function complete(User $user, int $count, array $figures = []): void
    {
        Trail::factory()->published()->count($count)->create()->each(function (Trail $trail) use ($user, $figures): void {
            $trail->forceFill($figures)->saveQuietly();
            $user->completedTrails()->attach($trail, ['completed_at' => now()]);
        });
    }

    public function test_completing_the_first_route_earns_a_badge_and_shows_a_notice(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CompleteButton::class, ['trail' => Trail::factory()->published()->create()])
            ->call('toggle')
            ->assertDispatched('badges-earned', fn (string $event, array $params): bool => in_array(
                ['name' => 'Routengänger', 'description' => 'Erste Route absolviert'], $params['badges'], true,
            ));

        $this->assertTrue($this->hasBadge($user, Badge::CompletedRoutes, 1));
    }

    public function test_all_reached_levels_are_awarded_once_and_kept(): void
    {
        $user = User::factory()->create();
        $this->complete($user, 10);
        $awarder = app(BadgeAwarder::class);

        $awarder->award($user);
        $this->assertTrue($this->hasBadge($user, Badge::CompletedRoutes, 1));
        $this->assertTrue($this->hasBadge($user, Badge::CompletedRoutes, 10));
        $this->assertFalse($this->hasBadge($user, Badge::CompletedRoutes, 25));

        $this->assertCount(0, $awarder->award($user), 'Nothing is awarded twice.');

        $user->completedTrails()->detach();
        $awarder->award($user);
        $this->assertTrue($this->hasBadge($user, Badge::CompletedRoutes, 10), 'Earned badges are kept.');
    }

    public function test_the_daily_command_awards_membership_years(): void
    {
        $user = User::factory()->create(['created_at' => now()->subYears(2)->subDay()]);

        $this->artisan('badges:award')->assertSuccessful();

        $this->assertTrue($this->hasBadge($user, Badge::Membership, 1));
        $this->assertTrue($this->hasBadge($user, Badge::Membership, 2));
        $this->assertFalse($this->hasBadge($user, Badge::Membership, 3));
    }

    public function test_only_the_first_members_are_pioneers(): void
    {
        $pioneer = User::factory()->create();
        User::factory()->count(Badge::PIONEER_MEMBERS)->create();
        $latecomer = User::factory()->create();
        $awarder = app(BadgeAwarder::class);

        $awarder->award($pioneer);
        $awarder->award($latecomer);

        $this->assertTrue($this->hasBadge($pioneer, Badge::Pioneer, 1));
        $this->assertFalse($this->hasBadge($latecomer, Badge::Pioneer, 1));
    }

    public function test_collector_and_special_badges_are_measured_over_completed_routes(): void
    {
        $user = User::factory()->create();
        $this->complete($user, 10, [
            'cache_count' => 10,
            'total_distance_meters' => 5200,
            'average_terrain' => 4.5,
            'average_difficulty' => 2.0,
            'transportations' => [Transportation::Bike],
        ]);

        app(BadgeAwarder::class)->award($user);

        $this->assertTrue($this->hasBadge($user, Badge::CachesCollected, 100));
        $this->assertTrue($this->hasBadge($user, Badge::KilometersCollected, 50));
        $this->assertTrue($this->hasBadge($user, Badge::SummitStormer, 1));
        $this->assertFalse($this->hasBadge($user, Badge::PuzzleFox, 1));
        $this->assertTrue($this->hasBadge($user, Badge::Cyclist, 10));
        $this->assertFalse($this->hasBadge($user, Badge::Hiker, 10));
    }

    public function test_likes_count_towards_the_badges_of_the_creator(): void
    {
        $trail = Trail::factory()->published()->create();
        $trail->likedBy()->attach(User::factory()->count(9)->create());

        Livewire::actingAs(User::factory()->create())
            ->test(LikeButton::class, ['trail' => $trail])
            ->call('toggle')
            ->assertNotDispatched('badges-earned');

        $this->assertTrue($this->hasBadge($trail->user, Badge::LikesReceived, 10));
    }

    public function test_rating_routes_can_earn_the_critic_badge(): void
    {
        $user = User::factory()->create();

        foreach (Trail::factory()->published()->count(10)->create() as $trail) {
            Livewire::actingAs($user)->test(TrailRatings::class, ['trail' => $trail])->set('rating', 4)->call('save');
        }

        $this->assertTrue($this->hasBadge($user, Badge::Critic, 10));
    }

    public function test_publishing_a_route_announces_the_badge_after_the_redirect(): void
    {
        $trail = Trail::factory()->withCaches(3)->create();

        $this->actingAs($trail->user)
            ->post(route('trails.publish', $trail))
            ->assertSessionHas(BadgeAwarder::SESSION_KEY, fn (array $notices): bool => in_array(
                ['name' => 'Routenbauer', 'description' => 'Erste Route veröffentlicht'], $notices, true,
            ));

        $this->get(route('trails.show', $trail))->assertSee('Neues Abzeichen');
    }

    public function test_badges_are_checked_when_logging_in(): void
    {
        $user = User::factory()->create(['created_at' => now()->subYear()->subDay()]);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password']);

        $this->assertTrue($this->hasBadge($user, Badge::Membership, 1));
    }

    public function test_the_profile_shows_the_highest_level_and_marks_new_badges_once(): void
    {
        $user = User::factory()->create();
        UserBadge::factory()->for($user)->unseen()->create(['badge' => Badge::CompletedRoutes, 'level' => 1]);
        UserBadge::factory()->for($user)->unseen()->create(['badge' => Badge::CompletedRoutes, 'level' => 10]);

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertSee('10 Routen absolviert')
            ->assertDontSee('Erste Route absolviert')
            ->assertDontSee('>Neu<', false);

        $this->actingAs($user)->get($user->profileUrl())->assertSee('>Neu<', false);
        $this->actingAs($user)->get($user->profileUrl())->assertDontSee('>Neu<', false);
    }

    public function test_profiles_without_badges_say_so(): void
    {
        $user = User::factory()->create(['name' => 'Neuling']);

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertSee('Neuling hat noch keine Abzeichen verdient.');
    }
}
