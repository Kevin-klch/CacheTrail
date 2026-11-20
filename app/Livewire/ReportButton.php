<?php

namespace App\Livewire;

use App\Enums\ReportReason;
use App\Models\Report;
use App\Models\TrailRating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * "Report" link with a small form for a route or a rating (specification 9.7).
 */
class ReportButton extends Component
{
    public const int REPORTS_PER_HOUR = 10;

    #[Locked]
    public string $reportableType;

    #[Locked]
    public int $reportableId;

    public bool $open = false;

    public bool $sent = false;

    public string $reason = '';

    public string $description = '';

    public function mount(Model $reportable): void
    {
        $this->reportableType = $reportable->getMorphClass();
        $this->reportableId = $reportable->getKey();
    }

    public function submit(): void
    {
        $reportable = $this->reportable();

        Gate::authorize('create', [Report::class, $reportable]);

        $validated = $this->validate([
            'reason' => ['required', Rule::enum(ReportReason::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ], attributes: ['reason' => __('Reason'), 'description' => __('Description')]);

        $key = 'reports:'.auth()->user()->rateLimitKey();

        if (RateLimiter::tooManyAttempts($key, self::REPORTS_PER_HOUR)) {
            throw ValidationException::withMessages(['reason' => __('You have sent a lot of reports in a short time. Please try again later.')]);
        }

        RateLimiter::hit($key, 3600);

        $report = new Report([
            'reason' => $validated['reason'],
            'description' => trim($validated['description']) === '' ? null : trim($validated['description']),
        ]);
        $report->user()->associate(auth()->user());
        $report->reportable()->associate($reportable);
        $report->save();

        $this->reset(['open', 'reason', 'description']);
        $this->sent = true;
    }

    public function render(): View
    {
        return view('livewire.report-button', [
            'canReport' => Gate::allows('create', [Report::class, $this->reportable()]),
        ]);
    }

    private function reportable(): Model
    {
        $class = Relation::getMorphedModel($this->reportableType);
        $reportable = $class::query()->findOrFail($this->reportableId);

        // The policy needs the route and its author to decide whether the item may be reported.
        return $reportable->load($reportable instanceof TrailRating ? ['trail.user'] : ['user']);
    }
}
