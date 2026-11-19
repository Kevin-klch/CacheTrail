<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_notice_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Bestätige deine E-Mail-Adresse');
    }

    public function test_without_email_verification_new_accounts_can_start_right_away(): void
    {
        config(['cachetrail.email_verification' => false]);
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Spontan',
            'email' => 'spontan@example.com',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
        ]);

        Notification::assertNothingSent();
        $this->get(route('trails.create'))->assertOk();
    }

    public function test_without_email_verification_a_changed_address_needs_no_confirmation(): void
    {
        config(['cachetrail.email_verification' => false]);
        Notification::fake();
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('user-profile-information.update'), ['email' => 'neu@example.com']);

        $this->assertSame('neu@example.com', $user->fresh()->email);
        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function test_email_can_be_verified(): void
    {
        Event::fake();

        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)],
        );

        $this->actingAs($user)->get($verificationUrl)->assertRedirect(route('home').'?verified=1');

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_is_not_verified_with_an_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')],
        );

        $this->actingAs($user)->get($verificationUrl)->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_users_are_redirected_from_route_features(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get(route('trails.index'))
            ->assertRedirect(route('verification.notice'));
    }
}
