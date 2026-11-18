<?php

namespace Tests\Feature;

use App\Enums\FeedbackCategory;
use App\Enums\UserRole;
use App\Livewire\FeedbackButton;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class FeedbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_feedback_button_is_shown_to_guests_and_users(): void
    {
        $this->get(route('home'))->assertSeeLivewire(FeedbackButton::class);
        $this->actingAs(User::factory()->create())->get(route('trails.index'))->assertSeeLivewire(FeedbackButton::class);
    }

    public function test_the_button_can_be_switched_off(): void
    {
        config(['cachetrail.feedback.button' => false]);

        $this->get(route('home'))->assertDontSeeLivewire(FeedbackButton::class);
    }

    public function test_a_user_can_send_feedback(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(FeedbackButton::class)
            ->call('toggle')
            ->assertSee('Dein Feedback')
            ->assertDontSee('E-Mail-Adresse (optional)')
            ->set('category', 'idea')
            ->set('message', 'Eine Kartenansicht für alle Routen wäre toll.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSee('Danke für dein Feedback!');

        $feedback = Feedback::sole();
        $this->assertTrue($feedback->user->is($user));
        $this->assertSame(FeedbackCategory::Idea, $feedback->category);
        $this->assertSame('Eine Kartenansicht für alle Routen wäre toll.', $feedback->message);
        $this->assertNull($feedback->contact);
        $this->assertNotNull($feedback->page_url);
    }

    public function test_guests_can_leave_an_email_address(): void
    {
        Livewire::test(FeedbackButton::class)
            ->call('toggle')
            ->assertSee('E-Mail-Adresse (optional)')
            ->set('category', 'bug')
            ->set('message', 'Die Suche findet Moers nicht.')
            ->set('contact', 'gast@example.com')
            ->call('submit')
            ->assertHasNoErrors();

        $feedback = Feedback::sole();
        $this->assertNull($feedback->user_id);
        $this->assertSame('gast@example.com', $feedback->contact);
    }

    public function test_feedback_is_validated(): void
    {
        Livewire::test(FeedbackButton::class)
            ->set('category', 'beschwerde')
            ->set('message', 'kurz')
            ->set('contact', 'keine-adresse')
            ->call('submit')
            ->assertHasErrors(['category', 'message', 'contact']);

        $this->assertDatabaseEmpty('feedback');
    }

    public function test_the_page_address_cannot_be_changed_by_the_browser(): void
    {
        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::test(FeedbackButton::class)->set('pageUrl', 'https://example.org');
    }

    public function test_spam_bots_filling_the_hidden_field_are_ignored(): void
    {
        Livewire::test(FeedbackButton::class)
            ->set('category', 'other')
            ->set('message', 'Billige Uhren kaufen!')
            ->set('website', 'https://spam.example')
            ->call('submit')
            ->assertSet('sent', true);

        $this->assertDatabaseEmpty('feedback');
    }

    public function test_sending_is_limited(): void
    {
        $component = Livewire::actingAs(User::factory()->create())->test(FeedbackButton::class);

        foreach (range(1, FeedbackButton::MAX_ATTEMPTS) as $attempt) {
            $component->set('category', 'idea')->set('message', 'Idee Nummer '.$attempt)->call('submit')->assertHasNoErrors();
        }

        $component->set('category', 'idea')->set('message', 'Noch eine Idee')->call('submit')->assertHasErrors('message');

        $this->assertSame(FeedbackButton::MAX_ATTEMPTS, Feedback::count());
    }

    public function test_testers_on_the_shared_account_each_have_their_own_limit(): void
    {
        config(['cachetrail.feedback.demo_data' => true]);
        $account = User::factory()->create(['email' => config('cachetrail.feedback.email')]);

        // Other testers used up the allowance of the account; it counts per connection instead.
        foreach (range(1, FeedbackButton::MAX_ATTEMPTS) as $attempt) {
            RateLimiter::hit('feedback:'.$account->id, 600);
        }

        $component = Livewire::actingAs($account)->test(FeedbackButton::class)
            ->set('category', 'bug')->set('message', 'Hier klemmt etwas')->call('submit')->assertHasNoErrors();

        foreach (range(1, FeedbackButton::MAX_ATTEMPTS) as $attempt) {
            RateLimiter::hit('feedback:127.0.0.1', 600);
        }

        $component->set('category', 'bug')->set('message', 'Noch ein Fehler')->call('submit')->assertHasErrors('message');
    }

    public function test_moderators_read_and_close_feedback(): void
    {
        $moderator = User::factory()->create(['role' => UserRole::Moderator]);
        $open = Feedback::factory()->create(['message' => 'Bitte eine Dunkelansicht.']);
        Feedback::factory()->done()->create(['message' => 'Erledigte Nachricht']);

        $this->actingAs($moderator)
            ->get(route('admin.feedback.index'))
            ->assertOk()
            ->assertSee('Bitte eine Dunkelansicht.')
            ->assertDontSee('Erledigte Nachricht');

        $this->patch(route('admin.feedback.update', $open))->assertRedirect();
        $this->assertTrue($open->fresh()->isDone());

        $this->get(route('admin.feedback.index', ['status' => 'done']))->assertSee('Bitte eine Dunkelansicht.');

        $this->delete(route('admin.feedback.destroy', $open))->assertRedirect();
        $this->assertModelMissing($open);
    }

    public function test_normal_users_cannot_read_feedback(): void
    {
        $feedback = Feedback::factory()->create();

        $this->actingAs(User::factory()->create())->get(route('admin.feedback.index'))->assertForbidden();
        $this->patch(route('admin.feedback.update', $feedback))->assertForbidden();
        $this->delete(route('admin.feedback.destroy', $feedback))->assertForbidden();
    }
}
