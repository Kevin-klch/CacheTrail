<?php

namespace Tests\Feature;

use App\Models\LoginRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRecordsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_login_is_recorded_with_address_and_device(): void
    {
        $user = User::factory()->create();

        $this->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0 Safari/537.36')
            ->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect();

        $record = LoginRecord::sole();
        $this->assertTrue($record->user->is($user));
        $this->assertSame('127.0.0.1', $record->ip_address);
        $this->assertSame('Windows · Chrome', $record->deviceLabel());
    }

    public function test_failed_logins_are_not_recorded(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'falsch']);

        $this->assertDatabaseEmpty('login_records');
    }

    public function test_administrators_see_the_logins_and_can_show_only_the_feedback_account(): void
    {
        config(['cachetrail.feedback.demo_data' => true]);
        $feedbackAccount = User::factory()->create(['email' => config('cachetrail.feedback.email'), 'name' => 'Feedback']);
        $other = User::factory()->create(['name' => 'Rheinpirat']);
        LoginRecord::factory()->for($feedbackAccount)->create(['ip_address' => '203.0.113.7']);
        LoginRecord::factory()->for($other)->create(['ip_address' => '198.51.100.4']);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.logins.index'))
            ->assertOk()
            ->assertSee('203.0.113.7')
            ->assertSee('198.51.100.4')
            ->assertSee('iPhone · Safari')
            ->assertSee('davon 1 mit dem Feedback-Konto');

        $this->get(route('admin.logins.index', ['account' => 'feedback']))
            ->assertSee('203.0.113.7')
            ->assertDontSee('198.51.100.4');
    }

    public function test_only_administrators_see_the_logins(): void
    {
        $this->actingAs(User::factory()->moderator()->create())->get(route('admin.logins.index'))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('admin.logins.index'))->assertForbidden();
    }

    public function test_old_logins_are_deleted(): void
    {
        $old = LoginRecord::factory()->create(['created_at' => now()->subDays(LoginRecord::KEEP_DAYS + 1)]);
        $recent = LoginRecord::factory()->create(['created_at' => now()->subDays(LoginRecord::KEEP_DAYS - 1)]);

        $this->artisan('model:prune', ['--model' => [LoginRecord::class]])->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
    }
}
