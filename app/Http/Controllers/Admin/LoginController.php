<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Recent logins for administrators, e.g. to see whether testers use the shared feedback account.
 */
class LoginController extends Controller
{
    public function index(Request $request): View
    {
        $onlyFeedbackAccount = $request->query('account') === 'feedback';
        $feedbackAccountId = config('cachetrail.feedback.demo_data')
            ? User::where('email', config('cachetrail.feedback.email'))->value('id')
            : null;

        $forFeedbackAccount = fn (Builder $query): Builder => $query->where('user_id', $feedbackAccountId);

        return view('admin.logins.index', [
            'onlyFeedbackAccount' => $onlyFeedbackAccount,
            'hasFeedbackAccount' => $feedbackAccountId !== null,
            'logins' => LoginRecord::query()
                ->when($onlyFeedbackAccount, $forFeedbackAccount)
                ->with('user')
                ->latest('created_at')
                ->latest('id')
                ->paginate(50)
                ->withQueryString(),
            'loginsToday' => LoginRecord::where('created_at', '>=', today())->count(),
            'feedbackLoginsToday' => $feedbackAccountId === null
                ? 0
                : $forFeedbackAccount(LoginRecord::query())->where('created_at', '>=', today())->count(),
        ]);
    }
}
