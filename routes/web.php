<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clinic Management Routes
    Route::resource('patients', PatientController::class);
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');

    Route::resource('doctors', DoctorController::class);
    Route::get('/doctors/search', [DoctorController::class, 'search'])->name('doctors.search');
    Route::get('/doctors/{doctor}/schedule', [DoctorController::class, 'schedule'])->name('doctors.schedule');

    Route::resource('rooms', RoomController::class);
    Route::get('/rooms/search', [RoomController::class, 'search'])->name('rooms.search');
    Route::get('/rooms/{room}/schedule', [RoomController::class, 'schedule'])->name('rooms.schedule');

    Route::resource('appointments', AppointmentController::class);
    Route::get('/appointments/search', [AppointmentController::class, 'search'])->name('appointments.search');
    Route::get('/appointments/schedule', [AppointmentController::class, 'schedule'])->name('appointments.schedule');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/complete', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::get('/appointments/available-doctors', [AppointmentController::class, 'getAvailableDoctors'])->name('appointments.available-doctors');
    Route::get('/appointments/available-rooms', [AppointmentController::class, 'getAvailableRooms'])->name('appointments.available-rooms');

    Route::resource('inventories', InventoryController::class);
    Route::get('/inventories/search', [InventoryController::class, 'search'])->name('inventories.search');
    Route::get('/inventories/low-stock', [InventoryController::class, 'lowStock'])->name('inventories.low-stock');
    Route::get('/inventories/expired', [InventoryController::class, 'expired'])->name('inventories.expired');
    Route::post('/inventories/{inventory}/restock', [InventoryController::class, 'restock'])->name('inventories.restock');

    Route::resource('transactions', TransactionController::class);
    Route::get('/transactions/search', [TransactionController::class, 'search'])->name('transactions.search');
    Route::post('/transactions/{transaction}/complete', [TransactionController::class, 'complete'])->name('transactions.complete');
    Route::post('/transactions/{transaction}/refund', [TransactionController::class, 'refund'])->name('transactions.refund');
    Route::post('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::get('/transactions/appointment/{appointment}', [TransactionController::class, 'byAppointment'])->name('transactions.by-appointment');
    Route::get('/transactions/report', [TransactionController::class, 'report'])->name('transactions.report');

    // Staff Management Routes
    Route::resource('staff', StaffController::class);
    Route::get('/staff/search', [StaffController::class, 'search'])->name('staff.search');

    // Report Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'dashboard'])->name('dashboard');
        Route::get('/daily-visits', [ReportController::class, 'dailyPatientVisits'])->name('daily-visits');
        Route::get('/income', [ReportController::class, 'incomeReport'])->name('income');
        Route::get('/inventory', [ReportController::class, 'inventoryStatus'])->name('inventory');
        Route::get('/doctor-performance', [ReportController::class, 'doctorPerformance'])->name('doctor-performance');
    });
});

require __DIR__.'/auth.php';
