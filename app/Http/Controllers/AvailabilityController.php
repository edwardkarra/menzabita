<?php

namespace App\Http\Controllers;

use App\Models\Availability;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    /**
     * Display availability management for a specific group.
     */
    public function index(Group $group)
    {
        // Check if user is a member of the group
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }

        $availabilities = Availability::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('availability.index', compact('group', 'availabilities'));
    }

    /**
     * Show the form for creating new availability.
     */
    public function create(Group $group)
    {
        // Check if user is a member of the group
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }

        return view('availability.create', compact('group'));
    }

    /**
     * Store a newly created availability in storage.
     */
    public function store(Request $request, Group $group)
    {
        // Check if user is a member of the group
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'note' => 'nullable|string|max:255',
        ]);

        // Check for overlapping availability
        $overlapping = Availability::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time' => 'You already have availability set for this time period.']);
        }

        Availability::create([
            'user_id' => Auth::id(),
            'group_id' => $group->id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'note' => $request->note,
        ]);

        return redirect()->route('availability.index', $group)
            ->with('success', 'Availability added successfully!');
    }

    /**
     * Display the specified availability.
     */
    public function show(Group $group, Availability $availability)
    {
        // Check if user owns this availability or is group owner
        if ($availability->user_id !== Auth::id() && !$group->isOwner(Auth::id())) {
            abort(403, 'You cannot view this availability.');
        }

        return view('availability.show', compact('group', 'availability'));
    }

    /**
     * Show the form for editing the specified availability.
     */
    public function edit(Group $group, Availability $availability)
    {
        // Check if user owns this availability
        if ($availability->user_id !== Auth::id()) {
            abort(403, 'You cannot edit this availability.');
        }

        return view('availability.edit', compact('group', 'availability'));
    }

    /**
     * Update the specified availability in storage.
     */
    public function update(Request $request, Group $group, Availability $availability)
    {
        // Check if user owns this availability
        if ($availability->user_id !== Auth::id()) {
            abort(403, 'You cannot edit this availability.');
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'note' => 'nullable|string|max:255',
        ]);

        // Check for overlapping availability (excluding current one)
        $overlapping = Availability::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('id', '!=', $availability->id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                          ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($overlapping) {
            return back()->withErrors(['time' => 'You already have availability set for this time period.']);
        }

        $availability->update([
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'note' => $request->note,
        ]);

        return redirect()->route('availability.index', $group)
            ->with('success', 'Availability updated successfully!');
    }

    /**
     * Remove the specified availability from storage.
     */
    public function destroy(Group $group, Availability $availability)
    {
        // Check if user owns this availability
        if ($availability->user_id !== Auth::id()) {
            abort(403, 'You cannot delete this availability.');
        }

        $availability->delete();

        return redirect()->route('availability.index', $group)
            ->with('success', 'Availability deleted successfully!');
    }

    /**
     * Display calendar view for the group.
     */
    public function calendar(Group $group)
    {
        // Check if user is a member of the group
        if (!$group->isMember(Auth::user())) {
            abort(403, 'You are not a member of this group.');
        }

        // Get all availabilities for the group for the next 30 days
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(30);

        $availabilities = Availability::with('user')
            ->where('group_id', $group->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            // Ensure keys are 'Y-m-d' strings to match the calendar view lookup
            ->groupBy(function ($availability) {
                return Carbon::parse($availability->date)->format('Y-m-d');
            });

        return view('availability.calendar', compact('group', 'availabilities', 'startDate', 'endDate'));
    }

    /**
     * Clear all availability for a specific date.
     */
    public function clearDate(Request $request, Group $group)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        Availability::where('group_id', $group->id)
            ->where('user_id', Auth::id())
            ->where('date', $request->date)
            ->delete();

        return back()->with('success', 'All availability cleared for the selected date.');
    }
}
