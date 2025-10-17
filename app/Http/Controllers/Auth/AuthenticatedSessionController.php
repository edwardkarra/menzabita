<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $invitation = session('invitation');
        return view('auth.login', compact('invitation'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Check if there's an invitation to process
        $invitation = session('invitation');
        if ($invitation && isset($invitation['group_id'])) {
            $group = Group::find($invitation['group_id']);
            if ($group && $group->is_active) {
                // Check if user is already a member
                if (!$group->isMember(Auth::user())) {
                    // Add user to the group
                    GroupMember::create([
                        'group_id' => $group->id,
                        'user_id' => Auth::id(),
                        'role' => 'member',
                        'joined_at' => now(),
                    ]);

                    // Clear the invitation from session
                    session()->forget('invitation');

                    return redirect()->route('groups.show', $group)
                        ->with('success', 'Successfully logged in and joined the group!');
                } else {
                    // Clear the invitation from session
                    session()->forget('invitation');

                    return redirect()->route('groups.show', $group)
                        ->with('info', 'You are already a member of this group.');
                }
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
