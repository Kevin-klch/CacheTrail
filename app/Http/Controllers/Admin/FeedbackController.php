<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Messages from the feedback button, for moderators and administrators.
 */
class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $showDone = $request->query('status') === 'done';

        return view('admin.feedback.index', [
            'showDone' => $showDone,
            'feedback' => Feedback::query()
                ->when($showDone, fn ($query) => $query->whereNotNull('done_at'), fn ($query) => $query->whereNull('done_at'))
                ->with('user')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'openCount' => Feedback::open()->count(),
        ]);
    }

    public function update(Feedback $feedback): RedirectResponse
    {
        $feedback->forceFill(['done_at' => $feedback->isDone() ? null : now()])->save();

        return back()->with('status', $feedback->isDone() ? __('The feedback has been marked as done.') : __('The feedback is open again.'));
    }

    public function destroy(Feedback $feedback): RedirectResponse
    {
        $feedback->delete();

        return back()->with('status', __('The feedback has been deleted.'));
    }
}
