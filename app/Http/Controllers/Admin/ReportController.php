<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReportStatus;
use App\Enums\TrailVisibility;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Trail;
use App\Models\TrailRating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Reports of routes and ratings and the moderation actions taken on them.
 */
class ReportController extends Controller
{
    public const array ACTIONS = ['dismiss', 'hide_trail', 'delete_rating', 'resolve'];

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Report::class);

        $status = ReportStatus::tryFrom((string) $request->query('status')) ?? ReportStatus::Open;

        return view('admin.reports.index', [
            'status' => $status,
            'reports' => Report::query()
                ->where('status', $status)
                ->with(['user', 'resolver', 'reportable' => function ($morphTo): void {
                    $morphTo->morphWith([TrailRating::class => ['trail', 'user'], Trail::class => ['user']]);
                }])
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'openCount' => Report::open()->count(),
        ]);
    }

    public function update(Request $request, Report $report): RedirectResponse
    {
        Gate::authorize('update', $report);

        $action = $request->validate(['action' => ['required', Rule::in(self::ACTIONS)]])['action'];
        $reportable = $report->reportable;

        match ($action) {
            'hide_trail' => $this->hideTrail($reportable instanceof TrailRating ? $reportable->trail : $reportable),
            'delete_rating' => $this->deleteRating($reportable),
            default => null,
        };

        $report->close($action === 'dismiss' ? ReportStatus::Dismissed : ReportStatus::Resolved, $request->user());

        // Other open reports about the same item are settled by the same decision.
        if ($action !== 'dismiss' && $reportable !== null) {
            Report::open()->whereMorphedTo('reportable', $reportable)
                ->each(fn (Report $other) => $other->close(ReportStatus::Resolved, $request->user()));
        }

        return back()->with('status', __('The report has been closed.'));
    }

    private function hideTrail(mixed $trail): void
    {
        abort_unless($trail instanceof Trail, 422);

        $trail->changeVisibility(TrailVisibility::Hidden);
        $trail->save();
    }

    private function deleteRating(mixed $rating): void
    {
        abort_unless($rating instanceof TrailRating, 422);

        $trail = $rating->trail;
        $rating->delete();
        $trail->refreshCommunityStatistics();
    }
}
