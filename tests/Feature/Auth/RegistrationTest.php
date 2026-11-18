<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_can_be_rendered(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Konto erstellen');
    }

    public function test_new_users_can_register_and_receive_a_verification_email(): void
    {
        Notification::fake();

        $this->post(route('register.store'), [
            'name' => 'Erika Muster',
            'email' => 'erika@example.com',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
        ])->assertRedirect(route('home'));

        $user = User::where('email', 'erika@example.com')->firstOrFail();

        $this->assertAuthenticatedAs($user);
        $this->assertSame(UserRole::User, $user->role);
        $this->assertFalse($user->hasVerifiedEmail());
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_usernames_are_unique_regardless_of_case(): void
    {
        User::factory()->create(['name' => 'TestCacher']);

        $this->post(route('register.store'), [
            'name' => ' testcacher ',
            'email' => 'zweiter@example.com',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
        ])->assertSessionHasErrors(['name' => 'Dieser Benutzername ist schon vergeben.']);

        $this->assertGuest();
    }

    public function test_usernames_may_not_contain_characters_that_break_the_profile_address(): void
    {
        foreach (['Wald/Fuchs', '?frage', 'a', ' '] as $name) {
            $this->post(route('register.store'), [
                'name' => $name,
                'email' => 'fuchs@example.com',
                'password' => 'sicheres-passwort',
                'password_confirmation' => 'sicheres-passwort',
            ])->assertSessionHasErrors('name');
        }

        $this->post(route('register.store'), [
            'name' => "Wald-Fuchs_2 O'Neil",
            'email' => 'fuchs@example.com',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
        ])->assertSessionHasNoErrors();
    }

    public function test_registration_cannot_assign_the_admin_role(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Mallory',
            'email' => 'mallory@example.com',
            'password' => 'sicheres-passwort',
            'password_confirmation' => 'sicheres-passwort',
            'role' => 'admin',
        ]);

        $this->assertSame(UserRole::User, User::where('email', 'mallory@example.com')->firstOrFail()->role);
    }

    public function test_registration_shows_german_validation_errors(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => '',
                'email' => 'not-an-email',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors([
                'name' => 'Geocaching-Benutzername ist ein Pflichtfeld.',
                'email' => 'E-Mail-Adresse muss eine gültige E-Mail-Adresse sein.',
            ]);

        $this->assertGuest();
    }
}
