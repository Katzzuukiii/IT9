<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class StaffController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search;
        
        $staff = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%")
                         ->orWhere('phone', 'like', "%$search%");
        })
        ->paginate(15);

        return view('staff.index', compact('staff'));
    }

    public function create(): View
    {
        return view('staff.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,staff',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        
        User::create($validated);

        return redirect()->route('staff.index')
                        ->with('success', 'Staff member added successfully.');
    }

    public function show(User $staff): View
    {
        return view('staff.show', compact('staff'));
    }

    public function edit(User $staff): View
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, User $staff): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,doctor,staff',
            'status' => 'required|in:active,inactive',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validated['password']) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        $staff->update($validated);

        return redirect()->route('staff.show', $staff)
                        ->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff): RedirectResponse
    {
        // Don't allow deleting your own account
        if ($staff->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $staff->delete();

        return redirect()->route('staff.index')
                        ->with('success', 'Staff member deleted successfully.');
    }

    public function search(Request $request): View
    {
        $term = $request->input('q');
        
        $staff = User::where('name', 'like', "%$term%")
                     ->orWhere('email', 'like', "%$term%")
                     ->orWhere('phone', 'like', "%$term%")
                     ->paginate(15);

        return view('staff.index', compact('staff'));
    }
}
