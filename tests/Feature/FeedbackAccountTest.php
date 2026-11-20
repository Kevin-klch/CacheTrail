<?php

namespace Tests\Feature;

use App\Livewire\BookmarkButton;
use App\Livewire\LikeButton;
use App\Livewire\ReportButton;
use App\Models\Report;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Tests\TestCase;

class FeedbackAccountTest extends TestCase
{
    use RefreshDatabase;

    private function feedbackAccount(): User
    {
        config(['cachetrail.feedback.demo_data' => true]);

        return User::factory()->create(['email' => config('cachetrail.feedback.email'), 'name' => 'Feedback']);
    }

    public function test_the_login_page_shows_the_shared_login_details_when_demo_data_is_on(): void
    {
        config(['cachetrail.feedback.demo_data' => true]);

        $this->get(route('login'))
            ->assertSee(config('cachetrail.feedback.email'))
            ->assertSee(config('cachetrail.feedback.password'));
    }

    public function test_the_email_address_of_the_feedback_account_cannot_be_changed(): void
    {
        $account = $this->feedbackAccount();

        $this->actingAs($account)
            ->put(route('user-profile-information.update'), ['email' => 'meins@example.com'])
            ->assertSessionHasErrorsIn('updateProfileInformation', 'email');

        $this->assertSame(config('cachetrail.feedback.email'), $account->fresh()->email);
    }

    public function test_the_password_of_the_feedback_account_cannot_be_changed(): void
    {
        $account = $this->feedbackAccount();
        $hash = $account->password;

        $this->actingAs($account)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'neues-passwort-123',
                'password_confirmation' => 'neues-passwort-123',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'password');

        $this->assertSame($hash, $account->fresh()->password);
    }

    public function test_the_feedback_account_cannot_be_deleted(): void
    {
        $account = $this->feedbackAccount();

        $this->actingAs($account)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrorsIn('deleteAccount', 'password');

        $this->assertModelExists($account);
        $this->assertAuthenticatedAs($account);
    }

    public function test_the_settings_explain_the_shared_account_instead_of_offering_locked_actions(): void
    {
        $this->actingAs($this->feedbackAccount())
            ->get(route('profile.edit'))
            ->assertSee('Gemeinsames Feedback-Konto')
            ->assertDontSee('Konto endgültig löschen')
            ->assertDontSee(route('profile.images.update'))
            ->assertDontSee(route('profile.bio.update'));
    }

    public function test_the_public_profile_of_the_feedback_account_cannot_be_changed(): void
    {
        $account = $this->feedbackAccount();

        $this->actingAs($account)
            ->put(route('profile.bio.update'), ['bio' => 'Hier war jemand'])
            ->assertForbidden();
        $this->put(route('profile.images.update'), ['remove_avatar' => '1'])->assertForbidden();

        $this->assertNull($account->fresh()->bio);
    }

    public function test_switching_the_language_does_not_change_it_for_other_testers(): void
    {
        $account = $this->feedbackAccount();

        $this->actingAs($account)->post(route('locale.update'), ['locale' => 'en']);
        $this->assertNull($account->fresh()->locale);

        // A language stored on the account earlier is ignored; every tester keeps the one of their session.
        $account->forceFill(['locale' => 'en'])->save();
        $this->flushSession();
        $this->actingAs($account->fresh())->get(route('home'))->assertSee('Routen entdecken');
        $this->withSession(['locale' => 'en'])->get(route('home'))->assertSee('Discover routes');
    }

    public function test_testers_can_report_what_another_tester_wrote_with_the_shared_account(): void
    {
        $account = $this->feedbackAccount();
        $trail = Trail::factory()->for($account)->published()->create();
        $ownTrail = Trail::factory()->published()->create();

        $this->assertTrue($account->can('create', [Report::class, $trail]));
        $this->assertFalse($ownTrail->user->can('create', [Report::class, $ownTrail]));
    }

    public function test_testers_cannot_like_or_save_routes_with_the_shared_account(): void
    {
        $account = $this->feedbackAccount();
        $trail = Trail::factory()->published()->create();

        Livewire::actingAs($account)->test(LikeButton::class, ['trail' => $trail])
            ->assertDontSeeHtml('wire:click')
            ->call('toggle')
            ->assertForbidden();
        Livewire::actingAs($account)->test(BookmarkButton::class, ['trail' => $trail])
            ->assertDontSeeHtml('wire:click')
            ->call('toggle')
            ->assertForbidden();

        $this->assertFalse($trail->isLikedBy($account));
        $this->assertFalse($trail->isBookmarkedBy($account));
    }

    public function test_testers_on_the_shared_account_each_have_their_own_report_limit(): void
    {
        $account = $this->feedbackAccount();
        $trail = Trail::factory()->published()->create();

        foreach (range(1, ReportButton::REPORTS_PER_HOUR) as $attempt) {
            RateLimiter::hit('reports:'.$account->id, 3600);
        }

        Livewire::actingAs($account)->test(ReportButton::class, ['reportable' => $trail])
            ->set('reason', 'spam')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame('127.0.0.1', $account->rateLimitKey());
        $this->assertSame((string) $trail->user->id, $trail->user->rateLimitKey());
    }

    public function test_the_routes_marked_for_the_shared_account_are_shown(): void
    {
        $account = $this->feedbackAccount();
        $trail = Trail::factory()->published()->create();
        $account->likedTrails()->attach($trail);
        $account->bookmarkedTrails()->attach($trail);

        Livewire::actingAs($account)->test(LikeButton::class, ['trail' => $trail, 'large' => true])->assertSee('Gefällt dir');
        Livewire::actingAs($account)->test(BookmarkButton::class, ['trail' => $trail, 'large' => true])->assertSee('Für später gemerkt');
        $this->get(route('bookmarks.index'))->assertSee($trail->name);
    }

    public function test_without_demo_data_the_address_is_an_ordinary_account(): void
    {
        config(['cachetrail.feedback.demo_data' => false]);

        $user = User::factory()->create(['email' => config('cachetrail.feedback.email')]);

        $this->assertFalse($user->isFeedbackAccount());
    }

    public function test_other_accounts_keep_all_settings(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('profile.edit'))
            ->assertDontSee('Gemeinsames Feedback-Konto');
    }
}
