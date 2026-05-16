<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Appointment;
use App\Models\Inventory;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class TransactionController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with(['appointment', 'patient', 'doctor', 'inventory'])
                                   ->latest()
                                   ->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    public function create(): View
    {
        $appointments = Appointment::where('status', '!=', 'cancelled')->get();
        $inventories = Inventory::where('status', '!=', 'expired')->get();
        return view('transactions.create', compact('appointments', 'inventories'));
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $transaction = Transaction::create($request->validated());

        // If this is an appointment transaction, update appointment total fee
        if ($transaction->appointment_id) {
            $transaction->appointment->calculateTotalFee();
        }

        return redirect()->route('transactions.show', $transaction)
                        ->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction): View
    {
        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction): View
    {
        $appointments = Appointment::where('status', '!=', 'cancelled')->get();
        $inventories = Inventory::where('status', '!=', 'expired')->get();
        return view('transactions.edit', compact('transaction', 'appointments', 'inventories'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        // If this is an appointment transaction, update appointment total fee
        if ($transaction->appointment_id) {
            $transaction->appointment->calculateTotalFee();
        }

        return redirect()->route('transactions.show', $transaction)
                        ->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->forceDelete();
        return redirect()->route('transactions.index')
                        ->with('success', 'Transaction deleted successfully.');
    }

    public function complete(Transaction $transaction): RedirectResponse
    {
        $transaction->markAsCompleted();

        // Recalculate appointment fee if applicable
        if ($transaction->appointment_id) {
            $transaction->appointment->calculateTotalFee();
        }

        return redirect()->route('transactions.show', $transaction)
                        ->with('success', 'Transaction marked as completed.');
    }

    public function refund(Transaction $transaction): RedirectResponse
    {
        $transaction->refund();

        return redirect()->route('transactions.show', $transaction)
                        ->with('success', 'Transaction refunded successfully.');
    }

    public function cancel(Transaction $transaction): RedirectResponse
    {
        $transaction->cancel();

        return redirect()->route('transactions.show', $transaction)
                        ->with('success', 'Transaction cancelled successfully.');
    }

    public function search()
    {
        $term = request('q');
        $transactions = Transaction::search($term)
                                   ->with(['appointment', 'patient', 'doctor', 'inventory'])
                                   ->paginate(15);
        return view('transactions.index', compact('transactions'));
    }

    public function byAppointment(Appointment $appointment): View
    {
        $transactions = $appointment->transactions()
                                   ->with(['inventory', 'patient'])
                                   ->paginate(15);
        return view('transactions.by-appointment', compact('transactions', 'appointment'));
    }

    public function report()
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $transactions = Transaction::byDateRange($startDate, $endDate)
                                   ->with(['appointment', 'patient', 'doctor', 'inventory'])
                                   ->get();

        $totalAmount = $transactions->sum('amount');
        $completedAmount = $transactions->where('status', 'completed')->sum('amount');
        $pendingAmount = $transactions->where('status', 'pending')->sum('amount');

        return view('transactions.report', compact('transactions', 'totalAmount', 'completedAmount', 'pendingAmount', 'startDate', 'endDate'));
    }
}
