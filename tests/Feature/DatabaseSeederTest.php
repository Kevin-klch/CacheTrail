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

    public function test_the_feedback_version_is_seeded_with_a_demo_community(): void
    {
        $this->seed();

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
    }

    public function test_the_shared_feedback_account_can_log_in(): void
    {
        $this->seed();

        $feedback = User::firstWhere('email', config('cachetrail.feedback.email'));

        $this->assertSame('Feedback', $feedback->name);
        $this->assertTrue(Hash::check(config('cachetrail.feedback.password'), $feedback->password));
        $this->assertFalse($feedback->isAdmin());
    }
}
