<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $invitation = session('invitation');
        return view('auth.register', compact('invitation'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^\+963\d{9}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Check if there's an invitation to process
        $invitation = session('invitation');
        if ($invitation && isset($invitation['group_id'])) {
            $group = Group::find($invitation['group_id']);
            if ($group && $group->is_active) {
                // Add user to the group
                GroupMember::create([
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'role' => 'member',
                    'joined_at' => now(),
                ]);

                // Clear the invitation from session
                session()->forget('invitation');

                return redirect()->route('groups.show', $group)
                    ->with('success', 'Account created successfully! You have been added to the group.');
            }
        }

        return redirect(route('dashboard', absolute: false));
    }
}
