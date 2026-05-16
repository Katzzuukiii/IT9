<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Room;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = Doctor::paginate(15);
        return view('doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        $rooms = Room::all();
        return view('doctors.create', compact('rooms'));
    }

    public function store(StoreDoctorRequest $request): RedirectResponse
    {
        $doctor = Doctor::create($request->validated());

        if ($request->has('rooms')) {
            $doctor->rooms()->sync($request->input('rooms'));
        }

        return redirect()->route('doctors.index')
                        ->with('success', 'Doctor created successfully.');
    }

    public function show(Doctor $doctor): View
    {
        $appointments = $doctor->appointments()->latest()->paginate(10);
        return view('doctors.show', compact('doctor', 'appointments'));
    }

    public function edit(Doctor $doctor): View
    {
        $rooms = Room::all();
        return view('doctors.edit', compact('doctor', 'rooms'));
    }

    public function update(UpdateDoctorRequest $request, Doctor $doctor): RedirectResponse
    {
        $doctor->update($request->validated());

        if ($request->has('rooms')) {
            $doctor->rooms()->sync($request->input('rooms'));
        }

        return redirect()->route('doctors.show', $doctor)
                        ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->forceDelete();
        return redirect()->route('doctors.index')
                        ->with('success', 'Doctor deleted successfully.');
    }

    public function search()
    {
        $term = request('q');
        $doctors = Doctor::search($term)->paginate(15);
        return view('doctors.index', compact('doctors'));
    }

    public function schedule(Doctor $doctor): View
    {
        $appointments = $doctor->appointments()
                              ->where('start_time', '>=', now())
                              ->orderBy('start_time', 'asc')
                              ->paginate(15);
        return view('doctors.schedule', compact('doctor', 'appointments'));
    }
}
