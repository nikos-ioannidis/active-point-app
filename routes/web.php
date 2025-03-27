<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
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
    });

    // Technician routes
    Route::middleware(['role:Technician'])->group(function () {

    });

    // Routes accessible to both Admin and Technician
    // Add shared routes here
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
