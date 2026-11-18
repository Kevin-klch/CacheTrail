<?php

namespace Tests\Feature;

use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Seeds as in local development, then switches back to the test environment (which, for example,
     * skips the CSRF check in requests).
     */
    private function seedLocally(): void
    {
        $this->app['env'] = 'local';
        $this->seed();
        $this->app['env'] = 'testing';
    }

    public function test_without_demo_data_the_site_starts_empty(): void
    {
        config(['cachetrail.feedback.demo_data' => false]);

        $this->seed();

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('trails', 0);
        $this->assertDatabaseCount('trail_ratings', 0);

        $this->get(route('home'))->assertOk()->assertSee('Noch keine Routen')->assertDontSee(route('trails.example'));
        $this->get(route('login'))->assertDontSee(config('cachetrail.feedback.email'));
    }

    public function test_locally_there_is_an_owner_account_admin_admin(): void
    {
        config(['cachetrail.feedback.demo_data' => false]);

        $this->seedLocally();

        $admin = User::sole();
        $this->assertSame('admin', $admin->name);
        $this->assertTrue($admin->isOwner());
        $this->assertTrue($admin->hasVerifiedEmail());

        $this->post(route('login.store'), ['email' => 'admin', 'password' => 'admin']);
        $this->assertAuthenticatedAs($admin);
    }

    public function test_outside_local_development_the_admin_needs_a_configured_password(): void
    {
        config(['cachetrail.feedback.demo_data' => false, 'cachetrail.admin_password' => 'ein-sicheres-passwort']);

        $this->seed();

        $this->assertTrue(Hash::check('ein-sicheres-passwort', User::sole()->password));
    }

    public function test_demo_data_adds_a_community_and_the_feedback_account(): void
    {
        config(['cachetrail.feedback.demo_data' => true]);

        $this->seedLocally();

        $this->assertDatabaseCount('users', 9);
        $this->assertSame(9, Trail::published()->count());
        $this->assertSame(11, Trail::count());
        $this->assertDatabaseCount('trail_caches', 61);
        $this->assertDatabaseCount('trail_ratings', 15);
        $this->assertDatabaseCount('trail_completions', 14);
        $this->assertDatabaseCount('geocaching_accounts', 5);
        $this->assertSame(2, Trail::where('slug', 'waldabenteuer-am-niederrhein')->value('like_count'));
        $this->assertTrue(Trail::published()->get()->every(fn (Trail $trail): bool => $trail->hasEnoughCachesToPublish()));
        $this->assertGreaterThan(0, User::firstWhere('email', 'rheinpirat@example.com')->badges()->count());

        $feedback = User::firstWhere('email', config('cachetrail.feedback.email'));
        $this->assertTrue(Hash::check(config('cachetrail.feedback.password'), $feedback->password));
        $this->assertFalse($feedback->isAdmin());
        $this->assertTrue(Hash::check('password', User::firstWhere('email', 'rheinpirat@example.com')->password));

        $this->get(route('login'))->assertSee(config('cachetrail.feedback.email'));
    }

    public function test_on_a_test_server_only_the_feedback_account_can_be_logged_into(): void
    {
        config(['cachetrail.feedback.demo_data' => true]);

        $this->seed();

        $this->assertFalse(Hash::check('password', User::firstWhere('email', 'rheinpirat@example.com')->password));
        $this->assertFalse(Hash::check('admin', User::firstWhere('email', 'admin@example.com')->password));
        $this->assertTrue(Hash::check(config('cachetrail.feedback.password'), User::firstWhere('email', config('cachetrail.feedback.email'))->password));
    }
}
