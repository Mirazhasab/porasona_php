<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ExamController;

// User routes - require user middleware (excludes admins)
Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Exams
    Route::prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/{mcqSet}', [ExamController::class, 'show'])->name('show');
        Route::get('/{mcqSet}/take', [ExamController::class, 'takeLivewire'])->name('take');
        Route::get('/{mcqSet}/start', [ExamController::class, 'start'])->name('start');
        Route::post('/{mcqSet}/submit', [ExamController::class, 'submit'])->name('submit');
        Route::get('/{mcqSet}/time-remaining', [ExamController::class, 'timeRemaining'])->name('time-remaining');
        Route::post('/{mcqSet}/auto-submit', [ExamController::class, 'autoSubmit'])->name('auto-submit');
    });
    
    // Profile - redirect to existing profile route
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', function() {
            return redirect()->route('profile.show');
        })->name('show');
    });
    
});