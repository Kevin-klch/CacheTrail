<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admins_can_search_users(): void
    {
        User::factory()->create(['name' => 'Erika Muster']);
        User::factory()->create(['name' => 'Max Mustermann']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.users.index', ['q' => 'erika']))
            ->assertOk()
            ->assertSee('Erika Muster')
            ->assertDontSee('Max Mustermann');
    }

    public function test_admins_can_change_the_role_of_other_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.users.update', $user), ['role' => UserRole::Admin->value])
            ->assertRedirect();

        $this->assertTrue($user->fresh()->isAdmin());
    }

    public function test_admins_cannot_demote_or_block_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch(route('admin.users.update', $admin), ['role' => UserRole::User->value])->assertForbidden();
        $this->actingAs($admin)->patch(route('admin.users.update', $admin), ['blocked' => 1])->assertForbidden();

        $this->assertTrue($admin->fresh()->isAdmin());
        $this->assertFalse($admin->fresh()->isBlocked());
    }

    public function test_normal_users_cannot_change_accounts(): void
    {
        $other = User::factory()->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('admin.users.update', $other), ['blocked' => 1])
            ->assertForbidden();

        $this->assertFalse($other->fresh()->isBlocked());
    }

    public function test_blocked_users_cannot_log_in(): void
    {
        $user = User::factory()->create(['blocked_at' => now()]);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors(['email' => 'Dieses Konto wurde gesperrt.']);

        $this->assertGuest();
    }

    public function test_blocked_users_are_logged_out_on_their_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('home'))->assertOk();

        $user->setBlocked(true);

        $this->get(route('home'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_routes_and_ratings_of_blocked_users_are_hidden(): void
    {
        $blocked = User::factory()->create();
        $trail = Trail::factory()->for($blocked)->published()->create(['name' => 'Route des Gesperrten']);
        $otherTrail = Trail::factory()->published()->create(['name' => 'Andere Route']);
        TrailRating::factory()->for($otherTrail)->for($blocked)->create(['rating' => 1, 'comment' => 'Böser Kommentar']);
        $otherTrail->refreshCommunityStatistics();
        $this->assertSame(1, $otherTrail->fresh()->rating_count);

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.users.update', $blocked), ['blocked' => 1]);

        $this->assertSame(0, $otherTrail->fresh()->rating_count);
        $this->assertNull($otherTrail->fresh()->average_rating);

        $viewer = User::factory()->create();
        $this->actingAs($viewer)->get(route('trails.index'))->assertDontSee('Route des Gesperrten')->assertSee('Andere Route');
        $this->actingAs($viewer)->get(route('trails.show', $trail))->assertForbidden();
        $this->actingAs($viewer)->get(route('trails.show', $otherTrail))->assertDontSee('Böser Kommentar');
    }

    public function test_unblocking_restores_routes_and_ratings(): void
    {
        $blocked = User::factory()->create(['blocked_at' => now()]);
        $trail = Trail::factory()->published()->create();
        TrailRating::factory()->for($trail)->for($blocked)->create(['rating' => 4]);

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.users.update', $blocked), ['blocked' => 0]);

        $this->assertFalse($blocked->fresh()->isBlocked());
        $this->assertSame(1, $trail->fresh()->rating_count);
    }

    public function test_admins_see_the_admin_link_with_open_reports(): void
    {
        $this->actingAs(User::factory()->create())->get(route('home'))->assertDontSee(route('admin.reports.index'));
        $this->actingAs(User::factory()->admin()->create())->get(route('home'))->assertSee(route('admin.reports.index'));
    }
}
