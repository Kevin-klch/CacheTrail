<?php

namespace Tests\Feature;

use App\Enums\ReportReason;
use App\Enums\ReportStatus;
use App\Enums\TrailVisibility;
use App\Livewire\ReportButton;
use App\Models\Report;
use App\Models\Trail;
use App\Models\TrailRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_route_can_be_reported_with_a_reason(): void
    {
        $trail = Trail::factory()->published()->create();
        $reporter = User::factory()->create();

        Livewire::actingAs($reporter)->test(ReportButton::class, ['reportable' => $trail])
            ->set('open', true)
            ->set('reason', ReportReason::WrongInformation->value)
            ->set('description', 'Die Dauer stimmt nicht.')
            ->call('submit')
            ->assertSet('sent', true)
            ->assertSee('Danke, wir prüfen deine Meldung.');

        $report = Report::sole();
        $this->assertTrue($report->reportable->is($trail));
        $this->assertSame('trail', $report->reportable_type);
        $this->assertSame(ReportStatus::Open, $report->status);
        $this->assertSame('Die Dauer stimmt nicht.', $report->description);
    }

    public function test_a_rating_can_be_reported(): void
    {
        $rating = TrailRating::factory()->create();

        Livewire::actingAs(User::factory()->create())->test(ReportButton::class, ['reportable' => $rating])
            ->set('reason', ReportReason::Inappropriate->value)
            ->call('submit');

        $this->assertSame('trail_rating', Report::sole()->reportable_type);
    }

    public function test_a_reason_is_required(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(ReportButton::class, ['reportable' => Trail::factory()->published()->create()])
            ->call('submit')
            ->assertHasErrors(['reason' => 'required']);
    }

    public function test_own_content_cannot_be_reported_and_open_reports_are_not_duplicated(): void
    {
        $trail = Trail::factory()->published()->create();

        Livewire::actingAs($trail->user)->test(ReportButton::class, ['reportable' => $trail])
            ->assertDontSee('Melden')
            ->set('reason', 'spam')
            ->call('submit')
            ->assertForbidden();

        $reporter = User::factory()->create();
        Livewire::actingAs($reporter)->test(ReportButton::class, ['reportable' => $trail])->set('reason', 'spam')->call('submit');
        Livewire::actingAs($reporter)->test(ReportButton::class, ['reportable' => $trail])->set('reason', 'spam')->call('submit')->assertForbidden();

        $this->assertSame(1, Report::count());
    }

    public function test_only_admins_can_open_the_admin_area(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.reports.index'))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('admin.users.index'))->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())->get(route('admin.reports.index'))->assertOk();
    }

    public function test_admins_see_open_reports_with_their_details(): void
    {
        $trail = Trail::factory()->published()->create(['name' => 'Gemeldete Runde']);
        Report::factory()->for($trail, 'reportable')->create(['reason' => ReportReason::Spam, 'description' => 'Reine Werbung']);
        Report::factory()->for(Trail::factory()->published()->create(['name' => 'Alte Meldung']), 'reportable')
            ->create(['status' => ReportStatus::Dismissed]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Gemeldete Runde')
            ->assertSee('Spam oder Werbung')
            ->assertSee('Reine Werbung')
            ->assertDontSee('Alte Meldung');
    }

    public function test_an_admin_can_hide_a_reported_route_which_closes_all_its_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $trail = Trail::factory()->published()->create();
        $first = Report::factory()->for($trail, 'reportable')->create();
        $second = Report::factory()->for($trail, 'reportable')->create();

        $this->actingAs($admin)
            ->patch(route('admin.reports.update', $first), ['action' => 'hide_trail'])
            ->assertRedirect();

        $this->assertSame(TrailVisibility::Hidden, $trail->fresh()->visibility);
        $this->assertSame(ReportStatus::Resolved, $first->fresh()->status);
        $this->assertSame(ReportStatus::Resolved, $second->fresh()->status);
        $this->assertTrue($first->fresh()->resolver->is($admin));
    }

    public function test_an_admin_can_delete_a_reported_rating(): void
    {
        $rating = TrailRating::factory()->create(['rating' => 1]);
        $rating->trail->refreshCommunityStatistics();
        $report = Report::factory()->for($rating, 'reportable')->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.reports.update', $report), ['action' => 'delete_rating']);

        $this->assertModelMissing($rating);
        $this->assertSame(0, $rating->trail->fresh()->rating_count);
        $this->assertSame(ReportStatus::Resolved, $report->fresh()->status);
    }

    public function test_an_admin_can_dismiss_a_report(): void
    {
        $report = Report::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->patch(route('admin.reports.update', $report), ['action' => 'dismiss']);

        $this->assertSame(ReportStatus::Dismissed, $report->fresh()->status);
        $this->assertSame(TrailVisibility::Published, $report->reportable->fresh()->visibility);
    }

    public function test_reports_of_deleted_content_can_still_be_closed(): void
    {
        $report = Report::factory()->create();
        $report->reportable->delete();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('admin.reports.index'))
            ->assertSee('Der gemeldete Inhalt existiert nicht mehr.');

        $this->patch(route('admin.reports.update', $report), ['action' => 'resolve']);

        $this->assertSame(ReportStatus::Resolved, $report->fresh()->status);
    }
}
