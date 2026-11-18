<?php

namespace Tests\Feature;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Livewire\ReportButton;
use App\Models\Trail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Limits that keep strangers from sending mail, filling the disk or flooding the moderation queue.
 */
class RateLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_one_connection_can_only_create_a_few_accounts_per_hour(): void
    {
        Notification::fake();

        foreach (range(1, CreateNewUser::REGISTRATIONS_PER_HOUR + 1) as $number) {
            $response = $this->post(route('register.store'), [
                'name' => 'Cacher'.$number,
                'email' => "cacher{$number}@example.com",
                'password' => 'sicheres-passwort',
                'password_confirmation' => 'sicheres-passwort',
            ]);
            Auth::logout();
        }

        $response->assertSessionHasErrors('email');
        $this->assertSame(CreateNewUser::REGISTRATIONS_PER_HOUR, User::count());
    }

    public function test_the_email_address_can_only_be_changed_a_few_times_per_hour(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        foreach (range(1, UpdateUserProfileInformation::EMAIL_CHANGES_PER_HOUR + 1) as $number) {
            $response = $this->actingAs($user)->put(route('user-profile-information.update'), ['email' => "neu{$number}@example.com"]);
        }

        $response->assertSessionHasErrorsIn('updateProfileInformation', 'email');
        $this->assertSame('neu'.UpdateUserProfileInformation::EMAIL_CHANGES_PER_HOUR.'@example.com', $user->fresh()->email);
    }

    public function test_reports_are_limited(): void
    {
        $user = User::factory()->create();
        $trails = Trail::factory()->published()->count(ReportButton::REPORTS_PER_HOUR + 1)->create();

        foreach ($trails as $index => $trail) {
            $component = Livewire::actingAs($user)
                ->test(ReportButton::class, ['reportable' => $trail])
                ->set('reason', 'spam')
                ->call('submit');
        }

        $component->assertHasErrors('reason');
        $this->assertDatabaseCount('reports', ReportButton::REPORTS_PER_HOUR);
    }

    public function test_creating_routes_is_limited(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (range(1, 61) as $number) {
            $response = $this->post(route('trails.store'), [
                'name' => 'Runde '.$number,
                'description' => 'Eine Runde.',
                'duration_hours' => 1,
                'duration_minutes' => 0,
                'transportations' => ['foot'],
            ]);
        }

        $response->assertTooManyRequests();
        $this->assertSame(60, Trail::count());
    }
}
