<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeedbackAccountTest extends TestCase
{
    use RefreshDatabase;

    private function feedbackAccount(): User
    {
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
            ->assertDontSee('Konto endgültig löschen');
    }

    public function test_other_accounts_keep_all_settings(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('profile.edit'))
            ->assertDontSee('Gemeinsames Feedback-Konto');
    }
}
