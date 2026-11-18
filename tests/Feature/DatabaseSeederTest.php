<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_main_version_starts_without_demo_data(): void
    {
        $this->seed();

        $this->assertDatabaseCount('trails', 0);
        $this->assertDatabaseCount('trail_ratings', 0);
    }

    public function test_locally_there_is_an_admin_account_with_admin_admin(): void
    {
        $this->app['env'] = 'local';
        $this->seed();
        $this->app['env'] = 'testing';

        $admin = User::sole();
        $this->assertSame('admin', $admin->name);
        $this->assertTrue($admin->isOwner());
        $this->assertTrue($admin->hasVerifiedEmail());

        $this->post(route('login.store'), ['email' => 'admin', 'password' => 'admin']);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_outside_local_development_the_admin_needs_a_configured_password(): void
    {
        $this->seed();
        $this->assertDatabaseCount('users', 0);

        config(['cachetrail.admin_password' => 'ein-sicheres-passwort']);
        $this->seed();

        $this->assertTrue(Hash::check('ein-sicheres-passwort', User::sole()->password));
    }

    public function test_a_fresh_site_without_routes_still_works(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Noch keine Routen')
            ->assertDontSee(route('trails.example'));
    }
}
