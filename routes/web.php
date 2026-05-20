<?php

use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PatientAddressController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\PrescriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Farmasi\DashboardController as FarmasiDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('landing'))->name('home');

// ===== PUBLIK — Tracking tanpa login =====
Route::prefix('track')->name('track.')->group(function () {
    Route::get('/',          [PublicTrackingController::class, 'index'])->name('index');
    Route::get('/{kode}',    [PublicTrackingController::class, 'show'])->name('show');
    Route::get('/{kode}/poll', [PublicTrackingController::class, 'poll'])->name('poll');
});

// ===== SUPER ADMIN =====
Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->except(['create', 'edit']);
    Route::resource('couriers', CourierController::class)->except(['create', 'edit']);
    Route::get('couriers/available-users', [CourierController::class, 'availableUsers'])->name('couriers.available-users');

    Route::resource('patients', PatientController::class)->except(['create', 'edit']);
    Route::resource('patients.addresses', PatientAddressController::class)->except(['create', 'edit']);

    Route::resource('prescriptions', PrescriptionController::class)->except(['create', 'edit']);
    Route::get('prescriptions/addresses-by-patient/{patient}', [PrescriptionController::class, 'addressesByPatient'])
        ->name('prescriptions.addresses-by-patient');
    Route::get('prescriptions/{prescription}/track', [PrescriptionController::class, 'track'])
        ->name('prescriptions.track');
});

// ===== FARMASI =====
Route::middleware(['auth', 'role:farmasi'])->prefix('farmasi')->name('farmasi.')->group(function () {
    Route::get('/dashboard', [FarmasiDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pengiriman-json', [FarmasiDashboardController::class, 'pengirimanJson'])->name('dashboard.pengiriman-json');
    Route::get('/dashboard/kanban-json', [FarmasiDashboardController::class, 'kanbanJson'])->name('dashboard.kanban-json');

    Route::resource('prescriptions', \App\Http\Controllers\Farmasi\PrescriptionController::class)
        ->except(['create', 'edit']);
    Route::get('prescriptions/addresses-by-patient/{patient}',
        [\App\Http\Controllers\Farmasi\PrescriptionController::class, 'addressesByPatient'])
        ->name('prescriptions.addresses-by-patient');
    Route::patch('prescriptions/{prescription}/quick-status',
        [\App\Http\Controllers\Farmasi\PrescriptionController::class, 'quickStatus'])
        ->name('prescriptions.quick-status');
});

// ===== PROFILE =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
