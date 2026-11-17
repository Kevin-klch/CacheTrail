<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public const int BIO_MAX_LENGTH = 500;

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()->load('geocachingAccount')]);
    }

    /**
     * The short public text in the info box of the profile.
     */
    public function updateBio(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updateBio', [
            'bio' => ['nullable', 'string', 'max:'.self::BIO_MAX_LENGTH],
        ], attributes: ['bio' => __('About me')]);

        $bio = trim((string) ($validated['bio'] ?? ''));

        $request->user()->forceFill(['bio' => $bio === '' ? null : $bio])->save();

        return back()->with('status', 'bio-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('deleteAccount', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'account-deleted');
    }
}
