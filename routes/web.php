<?php

use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParticipantTaskController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\TaskController;
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
        Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
        Route::resource('tasks', TaskController::class);
        Route::get('/submissions', [PlaceholderController::class, 'adminSubmissions'])->name('submissions.index');
        Route::get('/participants', [PlaceholderController::class, 'adminParticipants'])->name('participants.index');
    });

    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'participant'])->name('dashboard');
        Route::get('/tasks', [ParticipantTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}', [ParticipantTaskController::class, 'show'])->name('tasks.show');
        Route::get('/submissions', [PlaceholderController::class, 'submissions'])->name('submissions.index');
    });
});
