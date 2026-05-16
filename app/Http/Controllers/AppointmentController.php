<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Room;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AppointmentController extends Controller
{
    public function index(): View
    {
        $appointments = Appointment::with(['patient', 'doctor', 'room'])
                                   ->latest('start_time')
                                   ->paginate(15);
        return view('appointments.index', compact('appointments'));
    }

    public function create(): View
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::active()->get();
        $rooms = Room::available()->get();
        return view('appointments.create', compact('patients', 'doctors', 'rooms'));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        // Validate schedule conflicts
        if (!$this->validateScheduleConflict($request)) {
            return back()->with('error', 'Schedule conflict: Doctor or room not available at this time.')
                        ->withInput();
        }

        $appointment = Appointment::create($request->validated());

        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient', 'doctor', 'room']);
        $transactions = $appointment->transactions()->paginate(10);
        return view('appointments.show', compact('appointment', 'transactions'));
    }

    public function edit(Appointment $appointment): View
    {
        $patients = Patient::active()->get();
        $doctors = Doctor::active()->get();
        $rooms = Room::available()->get();
        return view('appointments.edit', compact('appointment', 'patients', 'doctors', 'rooms'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        // Validate schedule conflicts (excluding current appointment)
        if (!$this->validateScheduleConflict($request, $appointment->id)) {
            return back()->with('error', 'Schedule conflict: Doctor or room not available at this time.')
                         ->withInput();
        }

        $appointment->update($request->validated());

        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->forceDelete();
        return redirect()->route('appointments.index')
                        ->with('success', 'Appointment deleted successfully.');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $appointment->cancel(request('cancellation_reason'));
        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment cancelled successfully.');
    }

    public function complete(Appointment $appointment): RedirectResponse
    {
        $appointment->markAsCompleted();
        return redirect()->route('appointments.show', $appointment)
                        ->with('success', 'Appointment marked as completed.');
    }

    public function search()
    {
        $term = request('q');
        $appointments = Appointment::search($term)
                                   ->with(['patient', 'doctor', 'room'])
                                   ->paginate(15);
        return view('appointments.index', compact('appointments'));
    }

    public function schedule()
    {
        $doctorId = request('doctor_id');
        $roomId = request('room_id');

        $query = Appointment::where('start_time', '>=', now())
                           ->where('status', '!=', 'cancelled');

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($roomId) {
            $query->where('room_id', $roomId);
        }

        $appointments = $query->orderBy('start_time', 'asc')
                             ->with(['patient', 'doctor', 'room'])
                             ->paginate(15);

        return view('appointments.schedule', compact('appointments'));
    }

    /**
     * Validate schedule conflicts - Prevent double booking
     */
    private function validateScheduleConflict($request, $excludeAppointmentId = null)
    {
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $doctorId = $request->input('doctor_id');
        $roomId = $request->input('room_id');

        // Check doctor availability only if doctor is assigned
        if ($doctorId) {
            $doctor = Doctor::find($doctorId);
            if (!$doctor->isAvailableAt($startTime, $endTime, $excludeAppointmentId)) {
                return false;
            }
        }

        // Check room availability
        if ($roomId) {
            $room = Room::find($roomId);
            if (!$room->isAvailableAt($startTime, $endTime, $excludeAppointmentId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get available doctors for a specific time
     */
    public function getAvailableDoctors()
    {
        $startTime = request('start_time');
        $endTime = request('end_time');

        $doctors = Doctor::active()
                        ->get()
                        ->filter(function ($doctor) use ($startTime, $endTime) {
                            return $doctor->isAvailableAt($startTime, $endTime);
                        });

        return response()->json($doctors);
    }

    /**
     * Get available rooms for a specific time
     */
    public function getAvailableRooms()
    {
        $startTime = request('start_time');
        $endTime = request('end_time');

        $rooms = Room::available()
                    ->get()
                    ->filter(function ($room) use ($startTime, $endTime) {
                        return $room->isAvailableAt($startTime, $endTime);
                    });

        return response()->json($rooms);
    }
}
