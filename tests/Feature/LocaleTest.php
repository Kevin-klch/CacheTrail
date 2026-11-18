<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use App\Support\Format;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_site_is_german_by_default(): void
    {
        $this->get(route('home'))
            ->assertSee('<html lang="de">', false)
            ->assertSee('Hier beginnt dein Abenteuer.');
    }

    public function test_guests_can_switch_to_english_for_their_session(): void
    {
        $this->from(route('home'))
            ->post(route('locale.update'), ['locale' => 'en'])
            ->assertRedirect(route('home'));

        $this->get(route('home'))
            ->assertSee('<html lang="en">', false)
            ->assertSee('Your adventure starts here.')
            ->assertDontSee('Hier beginnt dein Abenteuer.');
    }

    public function test_the_choice_is_saved_in_the_account_of_logged_in_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('locale.update'), ['locale' => 'en']);

        $this->assertSame('en', $user->fresh()->locale);
        $this->assertSame('en', $user->fresh()->preferredLocale());

        // A new session (e.g. another device) still uses the language of the account.
        $this->flushSession();
        $this->actingAs($user->fresh())->get(route('profile.edit'))->assertSee('Your profile');
    }

    public function test_unknown_languages_are_rejected(): void
    {
        $this->post(route('locale.update'), ['locale' => 'fr'])->assertSessionHasErrors('locale');
    }

    public function test_emails_are_sent_in_the_language_of_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['locale' => 'en']);

        $user->sendEmailVerificationNotification();

        Notification::assertSentTo($user, VerifyEmail::class, fn (VerifyEmail $notification, array $channels, User $notifiable, ?string $locale): bool => $locale === 'en');
    }

    public function test_numbers_and_dates_follow_the_language(): void
    {
        app()->setLocale('de');
        $this->assertSame('1.234,5', Format::number(1234.5, 1));
        $this->assertSame('1,5', Format::rating(1.5));
        $this->assertSame('2', Format::rating(2.0));
        $this->assertSame('5. März 2026', Format::date('2026-03-05'));

        app()->setLocale('en');
        $this->assertSame('1,234.5', Format::number(1234.5, 1));
        $this->assertSame('1.5', Format::rating(1.5));
        $this->assertSame('March 5, 2026', Format::date('2026-03-05'));
    }

    public function test_route_figures_are_shown_in_english_format(): void
    {
        $trail = Trail::factory()->published()->create();
        $trail->forceFill(['total_distance_meters' => 2800, 'average_difficulty' => 2.5])->saveQuietly();

        $this->actingAs(User::factory()->create(['locale' => 'en']))
            ->get(route('trails.show', $trail))
            ->assertSee('~ 2.8 km')
            ->assertSee('2.5 / 5');
    }
}
