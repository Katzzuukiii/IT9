<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->search; 

        $patients = Patient::when($search, function ($query, $search) {
            return $query->where('first_name', 'like', "%$search%")
                         ->orWhere('last_name', 'like', "%$search%")
                         ->orWhere('email', 'like', "%$search%")
                         ->orWhere('phone', 'like', "%$search%");
        })->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        Patient::create($request->validated());
        return redirect()->route('patients.index')
                        ->with('success', 'Patient created successfully.');
    }

    public function show(Patient $patient): View
    {
        $appointments = $patient->appointments()->latest()->paginate(10);
        $transactions = $patient->transactions()->latest()->paginate(10);
        return view('patients.show', compact('patient', 'appointments', 'transactions'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());
        return redirect()->route('patients.show', $patient)
                        ->with('success', 'Patient updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->forceDelete();
        return redirect()->route('patients.index')
                        ->with('success', 'Patient deleted successfully.');
    }

    public function search()
    {
        $term = request('q');
        $patients = Patient::search($term)->paginate(15);
        return view('patients.index', compact('patients'));
    }
}
