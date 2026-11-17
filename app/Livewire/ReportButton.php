<?php

namespace App\Livewire;

use App\Enums\ReportReason;
use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * "Report" link with a small form for a route or a rating (specification 9.7).
 */
class ReportButton extends Component
{
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

        return $class::query()->findOrFail($this->reportableId);
    }
}
