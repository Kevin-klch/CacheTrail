<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * User management for administrators: search, change role, block and unblock.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? null;

        return view('admin.users.index', [
            'search' => $search,
            'users' => User::query()
                ->when($search, fn (Builder $query, string $search) => $query->where(fn (Builder $query) => $query
                    ->whereLike('name', "%{$search}%")
                    ->orWhereLike('email', "%{$search}%")))
                ->withCount(['trails', 'ratings'])
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['sometimes', Rule::enum(UserRole::class)],
            'blocked' => ['sometimes', 'boolean'],
        ]);

        // Admins cannot lock themselves out.
        abort_if($user->is($request->user()), 403, __('You cannot change your own account here.'));

        // The owner role can only be granted or taken away by an owner, and owners cannot be blocked by others.
        $touchesOwner = $user->isOwner() || ($validated['role'] ?? null) === UserRole::Owner->value;
        abort_if($touchesOwner && ! $request->user()->isOwner(), 403, __('Only the owner can change owner accounts.'));

        if (isset($validated['role'])) {
            $user->forceFill(['role' => $validated['role']])->save();
        }

        if (isset($validated['blocked'])) {
            $user->setBlocked((bool) $validated['blocked']);
        }

        return back()->with('status', __('The account of :name has been updated.', ['name' => $user->name]));
    }
}
