<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
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

    public function test_the_username_cannot_be_changed_in_the_profile(): void
    {
        $user = User::factory()->create(['name' => 'TestCacher']);

        $this->actingAs($user)
            ->put(route('user-profile-information.update'), [
                'name' => 'Neuer Name',
                'email' => $user->email,
            ])
            ->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('TestCacher', $user->name);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->get(route('profile.edit'))->assertSee('Dein Benutzername kann nicht geändert werden.');
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

    public function test_deleting_an_account_removes_its_routes_and_their_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $trail = Trail::factory()->for($user)->create();
        $trail->forceFill(['image_path' => 'trails/eigenes-bild.jpg'])->save();
        Storage::disk('public')->put('trails/eigenes-bild.jpg', 'bild');

        $this->actingAs($user)->delete(route('profile.destroy'), ['password' => 'password']);

        $this->assertModelMissing($trail);
        Storage::disk('public')->assertMissing('trails/eigenes-bild.jpg');
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
