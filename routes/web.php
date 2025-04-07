<?php

use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskHistoryController;
use App\Http\Controllers\TaskShareController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth')->group(function () {
    // Task Routes
    Route::resource('tasks', TaskController::class);
    Route::post('/tasks/{task}/toggle-google-calendar', [GoogleCalendarController::class, 'toggleSync'])->name('tasks.google-calendar.toggle');

    // Task History Routes
    Route::prefix('tasks/{task}/history')->group(function () {
        Route::get('/', [TaskHistoryController::class, 'index'])->name('tasks.history.index');
        Route::get('/{history}', [TaskHistoryController::class, 'show'])->name('tasks.history.show');
        Route::post('/{history}/restore', [TaskHistoryController::class, 'restore'])->name('tasks.history.restore');
    });

    // Task Share Routes
    Route::prefix('tasks/{task}/shares')->group(function () {
        Route::get('/', [TaskShareController::class, 'create'])->name('tasks.shares.create');
        Route::post('/', [TaskShareController::class, 'store'])->name('tasks.shares.store');
        Route::delete('/{share}', [TaskShareController::class, 'destroy'])->name('tasks.shares.destroy');
    });

    Route::get('/auth/google', [GoogleCalendarController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleCalendarController::class, 'handleGoogleCallback']);
    Route::post('/tasks/{task}/sync-google', [GoogleCalendarController::class, 'syncTask'])->name('tasks.google.sync');
});

// Public Shared Task View
Route::get('/shared/{token}', [TaskShareController::class, 'show'])
    ->name('tasks.shares.show');

// Home Redirect
Route::redirect('/', '/tasks')->middleware('auth');
Route::redirect('/home', '/tasks')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
