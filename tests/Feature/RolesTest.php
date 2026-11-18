<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Report;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_moderators_handle_reports_but_cannot_manage_users(): void
    {
        $moderator = User::factory()->moderator()->create();

        $this->actingAs($moderator)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertDontSee(route('admin.users.index'));

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->patch(route('admin.users.update', User::factory()->create()), ['role' => 'admin'])->assertForbidden();
    }

    public function test_moderators_may_delete_content_of_others_but_not_edit_it(): void
    {
        $moderator = User::factory()->moderator()->create();
        $trail = Trail::factory()->published()->create();
        $rating = TrailRating::factory()->for($trail)->create();

        $this->assertTrue(Gate::forUser($moderator)->allows('delete', $trail));
        $this->assertFalse(Gate::forUser($moderator)->allows('update', $trail));
        $this->assertTrue(Gate::forUser($moderator)->allows('delete', $rating));
        $this->assertTrue(Gate::forUser($moderator)->allows('update', Report::factory()->create()));
    }

    public function test_supporters_and_users_have_no_moderation_rights(): void
    {
        foreach ([User::factory()->supporter()->create(), User::factory()->create()] as $user) {
            $this->actingAs($user)->get(route('admin.reports.index'))->assertForbidden();
            $this->assertFalse(Gate::forUser($user)->allows('delete', Trail::factory()->published()->create()));
        }
    }

    public function test_admins_can_assign_every_role(): void
    {
        $user = User::factory()->create();

        foreach (UserRole::cases() as $role) {
            $this->actingAs(User::factory()->admin()->create())
                ->patch(route('admin.users.update', $user), ['role' => $role->value])
                ->assertSessionHasNoErrors();

            $this->assertSame($role, $user->fresh()->role);
        }
    }

    public function test_the_role_is_shown_as_a_badge_after_the_name(): void
    {
        $moderator = User::factory()->moderator()->create(['name' => 'Waldhüterin']);
        $trail = Trail::factory()->published()->for($moderator)->create();
        $viewer = User::factory()->create();

        $this->actingAs($viewer)->get($moderator->profileUrl())->assertSeeInOrder(['Waldhüterin', 'Moderator']);
        $this->actingAs($viewer)->get(route('trails.show', $trail))->assertSeeInOrder(['Waldhüterin', 'Moderator']);
        $this->actingAs($viewer)->get(route('trails.index'))->assertSeeInOrder(['Waldhüterin', 'Moderator']);
    }

    public function test_normal_users_get_no_badge(): void
    {
        $user = User::factory()->create(['name' => 'Waldfuchs']);

        $this->actingAs(User::factory()->create())
            ->get($user->profileUrl())
            ->assertDontSee('title="Nutzer"', false);
    }

    public function test_the_navigation_names_the_area_by_role(): void
    {
        $this->actingAs(User::factory()->moderator()->create())->get(route('home'))->assertSee('Moderation');
        $this->actingAs(User::factory()->admin()->create())->get(route('home'))->assertSee(route('admin.reports.index'));
    }
}
