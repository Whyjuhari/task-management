<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlaceholderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
        Route::get('/tasks', [PlaceholderController::class, 'adminTasks'])->name('tasks.index');
        Route::get('/submissions', [PlaceholderController::class, 'adminSubmissions'])->name('submissions.index');
        Route::get('/participants', [PlaceholderController::class, 'adminParticipants'])->name('participants.index');
    });

    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'participant'])->name('dashboard');
        Route::get('/tasks', [PlaceholderController::class, 'tasks'])->name('tasks.index');
        Route::get('/submissions', [PlaceholderController::class, 'submissions'])->name('submissions.index');
    });
});
