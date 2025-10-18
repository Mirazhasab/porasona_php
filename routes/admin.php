<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MCQController;

// Admin routes - all require admin middleware
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-update', [UserController::class, 'bulkUpdate'])->name('bulk-update');
        Route::patch('/{user}/reset-access', [UserController::class, 'resetAccess'])->name('reset-access');
        Route::patch('/{user}/grant-all-access', [UserController::class, 'grantAllAccess'])->name('grant-all-access');
    });
    
    // MCQ Management
    Route::prefix('mcqs')->name('mcqs.')->group(function () {
        Route::get('/', [MCQController::class, 'index'])->name('index');
        Route::get('/create', [MCQController::class, 'create'])->name('create');
        Route::post('/', [MCQController::class, 'store'])->name('store');
        Route::get('/{mcqSet}', [MCQController::class, 'show'])->name('show');
        Route::get('/{mcqSet}/edit', [MCQController::class, 'edit'])->name('edit');
        Route::put('/{mcqSet}', [MCQController::class, 'update'])->name('update');
        Route::delete('/{mcqSet}', [MCQController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [MCQController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [MCQController::class, 'reject'])->name('reject');
        
        // Import functionality
        Route::get('/import/form', [MCQController::class, 'showImport'])->name('import.form');
        Route::post('/import', [MCQController::class, 'import'])->name('import');
    });
    
    // Legacy user access routes (backward compatibility)
    Route::prefix('user-access')->name('user-access.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{userId}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{userId}', [UserController::class, 'update'])->name('update');
        Route::get('/{userId}', [UserController::class, 'show'])->name('show');
        Route::post('/bulk-update', [UserController::class, 'bulkUpdate'])->name('bulk-update');
        Route::patch('/{userId}/reset', [UserController::class, 'resetAccess'])->name('reset');
        Route::patch('/{userId}/grant-all', [UserController::class, 'grantAllAccess'])->name('grant-all');
    });
    
});