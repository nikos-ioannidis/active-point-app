<?php

use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeWorkTypeController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\WorkCategoryController;
use App\Http\Controllers\WorkTypeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // else redirect to login
    return redirect()->route('login');
})->name('home');

// dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin routes
    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('employees', EmployeeController::class);
        // Employee work type routes
        Route::get('/employees/{employee}/work-types', [EmployeeWorkTypeController::class, 'edit'])->name('employees.work-types.edit');
        Route::post('/employees/{employee}/work-types', [EmployeeWorkTypeController::class, 'update'])->name('employees.work-types.update');

        Route::resource('work-categories', WorkCategoryController::class);
        Route::resource('work-types', WorkTypeController::class);
        Route::resource('trainings', TrainingController::class);
        Route::resource('vehicles', VehicleController::class)->except('destroy');

        // System Settings routes
        Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('system-settings.index');
        Route::post('/system-settings', [SystemSettingsController::class, 'update'])->name('system-settings.update');
    });

    // Technician routes
    Route::middleware(['role:Technician'])->group(function () {
        // No routes for technicians yet
    });

    // Routes accessible to both Admin and Technician
    // Daily Reports (02) routes - accessible to both Admin and Technician
    Route::resource('daily-reports', DailyReportController::class);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
