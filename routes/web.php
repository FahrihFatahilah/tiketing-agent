<?php

use App\Http\Controllers\Admin\BusController;
use App\Http\Controllers\Admin\BusTypeController;
use App\Http\Controllers\Admin\RouteController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ManifestController;
use App\Http\Controllers\PassengerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TripController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Trip & Seat Map
    Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}/seatmap', [TripController::class, 'seatmap'])->name('trips.seatmap');
    Route::patch('/trips/{trip}/status', [TripController::class, 'updateStatus'])->name('trips.status')->middleware('role:admin|pengurus');

    // Passengers
    Route::post('/trips/{trip}/passengers', [PassengerController::class, 'store'])->name('passengers.store');
    Route::patch('/trips/{trip}/passengers/{passenger}', [PassengerController::class, 'update'])->name('passengers.update');
    Route::delete('/trips/{trip}/passengers/{passenger}', [PassengerController::class, 'destroy'])->name('passengers.destroy');

    // Manifest
    Route::get('/trips/{trip}/manifest', [ManifestController::class, 'show'])->name('manifest.show');
    Route::get('/trips/{trip}/manifest/pdf', [ManifestController::class, 'pdf'])->name('manifest.pdf');

    // Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'create', 'edit']);
        Route::resource('schedules', ScheduleController::class)->except(['show', 'create', 'edit']);
        Route::resource('buses', BusController::class)->except(['show', 'create', 'edit']);
        Route::resource('routes', RouteController::class)->except(['show', 'create', 'edit']);
    });

    // Occupancy report
    Route::get('/rekap', [\App\Http\Controllers\RekapController::class, 'index'])->name('rekap.index')->middleware('role:admin|pengurus');
});

require __DIR__ . '/auth.php';
