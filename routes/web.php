<?php

use App\Http\Controllers\AdminSubmissionController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ParticipantTaskController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\SubmissionController;
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
        Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
        Route::get('/submissions/export', [AdminSubmissionController::class, 'export'])->name('submissions.export');
        Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
        Route::get('/submissions/{submission}/download', [AdminSubmissionController::class, 'download'])
            ->name('submissions.download');
        Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    });

    Route::middleware('role:user')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'participant'])->name('dashboard');
        Route::get('/tasks', [ParticipantTaskController::class, 'index'])->name('tasks.index');
        Route::get('/tasks/{task}/submission/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/tasks/{task}/submission', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::get('/tasks/{task}', [ParticipantTaskController::class, 'show'])->name('tasks.show');
        Route::get('/submissions', [PlaceholderController::class, 'submissions'])->name('submissions.index');
        Route::get('/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::get('/submissions/{submission}/edit', [SubmissionController::class, 'edit'])->name('submissions.edit');
        Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    });
});
