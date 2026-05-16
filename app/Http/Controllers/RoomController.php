<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Http\Requests\StoreRoomRequest;
use App\Http\Requests\UpdateRoomRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::paginate(15);
        return view('rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        return view('rooms.create');
    }

    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());
        return redirect()->route('rooms.index')
                        ->with('success', 'Room created successfully.');
    }

    public function show(Room $room): View
    {
        $appointments = $room->appointments()->latest()->paginate(10);
        return view('rooms.show', compact('room', 'appointments'));
    }

    public function edit(Room $room): View
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());
        return redirect()->route('rooms.show', $room)
                        ->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->forceDelete();
        return redirect()->route('rooms.index')
                        ->with('success', 'Room deleted successfully.');
    }

    public function search()
    {
        $term = request('q');
        $rooms = Room::search($term)->paginate(15);
        return view('rooms.index', compact('rooms'));
    }

    public function schedule(Room $room): View
    {
        $appointments = $room->appointments()
                            ->where('start_time', '>=', now())
                            ->orderBy('start_time', 'asc')
                            ->paginate(15);
        return view('rooms.schedule', compact('room', 'appointments'));
    }
}
