<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Switches the language of the site. It is remembered in the session and, for logged-in users,
 * in their account (so emails are sent in that language too).
 */
class LocaleController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $locale = $request->validate([
            'locale' => ['required', Rule::in(array_keys(config('cachetrail.locales')))],
        ])['locale'];

        $request->session()->put('locale', $locale);

        // The shared feedback account is used by many people at once; each of them keeps their own language.
        if ($request->user() && ! $request->user()->isFeedbackAccount()) {
            $request->user()->forceFill(['locale' => $locale])->save();
        }

        return back();
    }
}
