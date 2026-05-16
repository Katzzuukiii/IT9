<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Transaction;
use App\Models\Doctor;
use App\Models\Inventory;
use Illuminate\View\View;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function dashboard(): View
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        // Daily patient visits report
        $dailyVisits = Appointment::where('status', 'completed')
                                   ->whereDate('start_time', '>=', $startDate)
                                   ->whereDate('start_time', '<=', $endDate)
                                   ->selectRaw('DATE(start_time) as visit_date, COUNT(*) as visit_count')
                                   ->groupBy('visit_date')
                                   ->orderBy('visit_date')
                                   ->get();

        // Income reports
        $incomeReports = Transaction::where('status', 'completed')
                                     ->whereDate('created_at', '>=', $startDate)
                                     ->whereDate('created_at', '<=', $endDate)
                                     ->selectRaw('DATE(created_at) as transaction_date, SUM(amount) as daily_income, COUNT(*) as transaction_count')
                                     ->groupBy('transaction_date')
                                     ->orderBy('transaction_date')
                                     ->get();

        $totalIncome = Transaction::where('status', 'completed')
                                   ->whereDate('created_at', '>=', $startDate)
                                   ->whereDate('created_at', '<=', $endDate)
                                   ->sum('amount');

        // Inventory status report
        $inventoryStatus = Inventory::selectRaw('status, COUNT(*) as count, SUM(quantity) as total_quantity')
                                      ->groupBy('status')
                                      ->get();

        $lowStockItems = Inventory::lowStock()->count();

        return view('reports.dashboard', compact(
            'dailyVisits',
            'incomeReports',
            'totalIncome',
            'inventoryStatus',
            'lowStockItems',
            'startDate',
            'endDate'
        ));
    }

    public function dailyPatientVisits(): View
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $appointments = Appointment::with(['patient', 'doctor', 'room'])
                                    ->where('status', 'completed')
                                    ->whereDate('start_time', '>=', $startDate)
                                    ->whereDate('start_time', '<=', $endDate)
                                    ->orderBy('start_time', 'desc')
                                    ->paginate(20);

        $totalVisits = Appointment::where('status', 'completed')
                                   ->whereDate('start_time', '>=', $startDate)
                                   ->whereDate('start_time', '<=', $endDate)
                                   ->count();

        $dailySummary = Appointment::where('status', 'completed')
                                    ->whereDate('start_time', '>=', $startDate)
                                    ->whereDate('start_time', '<=', $endDate)
                                    ->selectRaw('DATE(start_time) as visit_date, COUNT(*) as visit_count')
                                    ->groupBy('visit_date')
                                    ->orderBy('visit_date', 'desc')
                                    ->get();

        return view('reports.daily-patient-visits', compact(
            'appointments',
            'totalVisits',
            'dailySummary',
            'startDate',
            'endDate'
        ));
    }

    public function incomeReport(): View
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $transactions = Transaction::with(['appointment', 'patient', 'doctor'])
                                    ->whereDate('created_at', '>=', $startDate)
                                    ->whereDate('created_at', '<=', $endDate)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);

        $summary = [
            'total_income' => Transaction::where('status', 'completed')
                                         ->whereDate('created_at', '>=', $startDate)
                                         ->whereDate('created_at', '<=', $endDate)
                                         ->sum('amount'),
            'completed_transactions' => Transaction::where('status', 'completed')
                                                    ->whereDate('created_at', '>=', $startDate)
                                                    ->whereDate('created_at', '<=', $endDate)
                                                    ->count(),
            'pending_amount' => Transaction::where('status', 'pending')
                                           ->whereDate('created_at', '>=', $startDate)
                                           ->whereDate('created_at', '<=', $endDate)
                                           ->sum('amount'),
            'refunded_amount' => Transaction::where('status', 'refunded')
                                            ->whereDate('created_at', '>=', $startDate)
                                            ->whereDate('created_at', '<=', $endDate)
                                            ->sum('amount'),
        ];

        $dailyIncome = Transaction::where('status', 'completed')
                                   ->whereDate('created_at', '>=', $startDate)
                                   ->whereDate('created_at', '<=', $endDate)
                                   ->selectRaw('DATE(created_at) as income_date, SUM(amount) as daily_income, COUNT(*) as transaction_count')
                                   ->groupBy('income_date')
                                   ->orderBy('income_date', 'desc')
                                   ->get();

        return view('reports.income', compact(
            'transactions',
            'summary',
            'dailyIncome',
            'startDate',
            'endDate'
        ));
    }

    public function inventoryStatus(): View
    {
        $inventories = Inventory::all();

        $summary = [
            'total_items' => Inventory::count(),
            'active_items' => Inventory::where('status', 'active')->count(),
            'low_stock_items' => Inventory::lowStock()->count(),
            'expired_items' => Inventory::where('status', 'expired')->count(),
            'total_value' => Inventory::selectRaw('SUM(quantity * unit_price) as total_value')->first()->total_value ?? 0,
        ];

        $statusBreakdown = Inventory::selectRaw('status, COUNT(*) as count, SUM(quantity) as total_quantity, SUM(quantity * unit_price) as total_value')
                                     ->groupBy('status')
                                     ->get();

        return view('reports.inventory-status', compact(
            'inventories',
            'summary',
            'statusBreakdown'
        ));
    }

    public function doctorPerformance(): View
    {
        $startDate = request('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->format('Y-m-d'));

        $doctors = Doctor::with(['appointments' => function ($query) use ($startDate, $endDate) {
            $query->where('status', 'completed')
                  ->whereDate('start_time', '>=', $startDate)
                  ->whereDate('start_time', '<=', $endDate);
        }])
        ->get()
        ->map(function ($doctor) use ($startDate, $endDate) {
            $appointments = $doctor->appointments->filter(function ($apt) {
                return $apt->status === 'completed';
            });

            $totalRevenue = Transaction::whereHas('appointment', function ($query) use ($doctor, $startDate, $endDate) {
                $query->where('doctor_id', $doctor->id)
                      ->where('status', 'completed')
                      ->whereDate('created_at', '>=', $startDate)
                      ->whereDate('created_at', '<=', $endDate);
            })->sum('amount');

            return [
                'doctor' => $doctor,
                'completed_appointments' => $appointments->count(),
                'total_revenue' => $totalRevenue,
                'average_revenue' => $appointments->count() > 0 ? $totalRevenue / $appointments->count() : 0,
            ];
        })
        ->sortByDesc('completed_appointments');

        return view('reports.doctor-performance', compact(
            'doctors',
            'startDate',
            'endDate'
        ));
    }
}
