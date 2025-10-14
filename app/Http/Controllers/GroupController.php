<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GroupController extends Controller
{

    /**
     * Display a listing of the user's groups.
     */
    public function index()
    {
        $user = Auth::user();
        $groups = $user->groups()->with(['owner', 'members.user'])->get();
        
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new group.
     */
    public function create()
    {
        return view('groups.create');
    }

    /**
     * Store a newly created group in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'owner_id' => Auth::id(),
            'invite_code' => Str::random(8),
            'is_active' => true,
        ]);

        // Add the creator as an admin member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'admin',
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group created successfully!');
    }

    /**
     * Display the specified group.
     */
    public function show(Group $group)
    {
        // Check if user is a member of the group
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }

        $group->load(['owner', 'members.user', 'availabilities.user']);
        
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified group.
     */
    public function edit(Group $group)
    {
        // Only the owner can edit the group
        if (!$group->isOwner(Auth::user())) {
            abort(403, 'Only the group owner can edit this group.');
        }

        return view('groups.edit', compact('group'));
    }

    /**
     * Update the specified group in storage.
     */
    public function update(Request $request, Group $group)
    {
        // Only the owner can update the group
        if (!$group->isOwner(Auth::user())) {
            abort(403, 'Only the group owner can update this group.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Group updated successfully!');
    }

    /**
     * Remove the specified group from storage.
     */
    public function destroy(Group $group)
    {
        // Only the owner can delete the group
        if (!$group->isOwner(Auth::user())) {
            abort(403, 'Only the group owner can delete this group.');
        }

        $group->delete();

        return redirect()->route('groups.index')
            ->with('success', 'Group deleted successfully!');
    }

    /**
     * Join a group using invite code.
     */
    public function join(Request $request)
    {
        $request->validate([
            'invite_code' => 'required|string|size:8',
        ]);

        $group = Group::where('invite_code', $request->invite_code)
            ->where('is_active', true)
            ->first();

        if (!$group) {
            return back()->withErrors(['invite_code' => 'Invalid invite code.']);
        }

        // Check if user is already a member
        if ($group->isMember(Auth::user())) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'You are already a member of this group.');
        }

        // Add user as a member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Successfully joined the group!');
    }

    /**
     * Leave a group.
     */
    public function leave(Group $group)
    {
        // Owner cannot leave their own group
        if ($group->isOwner(Auth::user())) {
            return back()->withErrors(['error' => 'Group owners cannot leave their own group. Delete the group instead.']);
        }

        $membership = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($membership) {
            $membership->delete();
        }

        return redirect()->route('groups.index')
            ->with('success', 'You have left the group.');
    }

    /**
     * Remove a member from the group.
     */
    public function removeMember(Group $group, GroupMember $member)
    {
        // Only admins and owners can remove members
        $userMembership = GroupMember::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userMembership || !in_array($userMembership->role, ['admin', 'owner'])) {
            abort(403, 'You do not have permission to remove members.');
        }

        // Cannot remove the owner
        if ($member->user_id === $group->owner_id) {
            return back()->withErrors(['error' => 'Cannot remove the group owner.']);
        }

        $member->delete();

        return back()->with('success', 'Member removed successfully.');
    }

    /**
     * Handle invitation link access.
     */
    public function handleInvite($inviteCode)
    {
        $group = Group::where('invite_code', $inviteCode)
            ->where('is_active', true)
            ->first();

        if (!$group) {
            return redirect()->route('groups.index')
                ->withErrors(['error' => 'Invalid or expired invitation link.']);
        }

        // If user is not authenticated, redirect to register with invitation data
        if (!Auth::check()) {
            return redirect()->route('register')
                ->with('invitation', [
                    'group_id' => $group->id,
                    'group_name' => $group->name,
                    'invite_code' => $inviteCode
                ]);
        }

        // If user is authenticated, check if already a member
        if ($group->isMember(Auth::user())) {
            return redirect()->route('groups.show', $group)
                ->with('info', 'You are already a member of this group.');
        }

        // Add user as a member
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
            'role' => 'member',
            'joined_at' => now(),
        ]);

        return redirect()->route('groups.show', $group)
            ->with('success', 'Successfully joined the group!');
    }
}
