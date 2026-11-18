<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public const int BIO_MAX_LENGTH = 500;

    public const int MAX_IMAGE_KILOBYTES = 5120;

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

    /**
     * Profile picture and cover image of the public profile: upload a new one or remove the current one.
     */
    public function updateImages(Request $request): RedirectResponse
    {
        $image = ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_IMAGE_KILOBYTES];

        $request->validateWithBag('updateImages', [
            'avatar' => [...$image, 'dimensions:min_width=100,min_height=100'],
            'cover' => [...$image, 'dimensions:min_width=600,min_height=150'],
            'remove_avatar' => ['boolean'],
            'remove_cover' => ['boolean'],
        ], attributes: ['avatar' => __('Profile picture'), 'cover' => __('Cover image')]);

        $user = $request->user();

        foreach (['avatar' => 'avatars', 'cover' => 'covers'] as $field => $directory) {
            $column = $field.'_path';

            if ($request->hasFile($field) || $request->boolean('remove_'.$field)) {
                if ($user->{$column}) {
                    Storage::disk('public')->delete($user->{$column});
                }

                $user->{$column} = $request->hasFile($field) ? $request->file($field)->store($directory, 'public') : null;
            }
        }

        $user->save();

        return back()->with('status', 'images-updated');
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
