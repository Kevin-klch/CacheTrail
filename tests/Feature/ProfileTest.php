<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('profile.edit'))->assertRedirect(route('login'));
    }

    public function test_profile_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk()
            ->assertSee($user->email);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), [
                'name' => 'Neuer Name',
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Neuer Name', $user->name);
        $this->assertTrue($user->hasVerifiedEmail());
    }

    public function test_changing_the_email_address_requires_verification_again(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put(route('user-profile-information.update'), [
            'name' => $user->name,
            'email' => 'neu@example.com',
        ]);

        $user->refresh();

        $this->assertSame('neu@example.com', $user->email);
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'neues-passwort',
                'password_confirmation' => 'neues-passwort',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('neues-passwort', $user->fresh()->password));
    }

    public function test_password_is_not_updated_with_a_wrong_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('user-password.update'), [
                'current_password' => 'falsch',
                'password' => 'neues-passwort',
                'password_confirmation' => 'neues-passwort',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_account_can_be_deleted_with_the_correct_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertModelMissing($user);
    }

    public function test_account_is_not_deleted_with_a_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('profile.edit'))
            ->delete(route('profile.destroy'), ['password' => 'falsch'])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHasErrorsIn('deleteAccount', 'password');

        $this->assertAuthenticatedAs($user);
        $this->assertModelExists($user);
    }
}
