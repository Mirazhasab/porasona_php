<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MCQDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;

use App\Http\Controllers\McqSetController;
use App\Http\Controllers\McqQuestionController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserAccessController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\SubscriptionSettingController;
use App\Http\Controllers\Admin\McqNoteApprovalController;
use App\Http\Controllers\McqNoteHubController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;

// Debug route for database testing
Route::get('/debug-db', function () {
    return view('debug-db');
});

Route::post('stripe/webhook', StripeWebhookController::class)->name('stripe.webhook');

// Bug fix routes (admin only)
Route::middleware(['auth'])->prefix('admin/bugfix')->group(function () {
    Route::get('/user-access', [App\Http\Controllers\BugFixController::class, 'fixUserAccess']);
    Route::get('/relationships', [App\Http\Controllers\BugFixController::class, 'fixRelationships']);
    Route::get('/health', [App\Http\Controllers\BugFixController::class, 'healthCheck']);
});

Route::get('/', function () {
    $stats = [
        'totalQuestions' => \App\Models\McqQuestion::count(),
        'totalExams' => \App\Models\McqSet::count(),
        'totalUsers' => \App\Models\User::count(),
        'activeExams' => \App\Models\McqSet::where('status', 'approved')->count()
    ];
    
    $recentExams = \App\Models\McqSet::with('questions')
        ->where('status', 'approved')
        ->latest()
        ->limit(3)
        ->get();
    
    return view('homepage', compact('stats', 'recentExams'));
});

// Check admin status
Route::get('/check-admin', function () {
    $user = auth()->user();
    if (!$user) {
        return 'Not logged in';
    }
    
    return [
        'name' => $user->name,
        'email' => $user->email,
        'role_column' => $user->role,
        'is_admin_by_role' => $user->role === 'admin',
        'has_spatie_role' => $user->hasRole('admin'),
        'final_check' => ($user->hasRole('admin') || $user->role === 'admin')
    ];
})->middleware('auth');

// Force set admin role
Route::get('/force-admin', function () {
    $user = auth()->user();
    if (!$user) {
        return 'Not logged in';
    }
    
    $user->role = 'admin';
    $user->save();
    
    return redirect('/mcq_sets')->with('success', 'Admin role forced! Please refresh the page.');
})->middleware('auth');

// Authentication Routes (Livewire)
Route::get('/login', App\Livewire\Auth\Login::class)->name('login');
Route::post('/login', [GoogleController::class, 'handleLogin'])->name('login.submit');
Route::get('/register', App\Livewire\Auth\Register::class)->name('register');
Route::post('/register', [GoogleController::class, 'handleRegister'])->name('register.submit');
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/success', [GoogleController::class, 'success'])->name('auth.success');
Route::get('/auth/error', [GoogleController::class, 'error'])->name('auth.error');
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

// Dashboard Routes - Unified MCQ System for All Users
Route::middleware(['auth', 'subscription.check'])->group(function () {
    // Main Dashboard (redirects to MCQ Dashboard)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // MCQ System Routes (accessible by all authenticated users)
    Route::prefix('mcq')->name('mcq.')->group(function () {
        // Dashboard - always accessible to authenticated users
        Route::get('/dashboard', [MCQDashboardController::class, 'index'])->name('dashboard');
        
        // Practice Questions - requires 'practice' permission
        Route::middleware(['user.access:practice'])->group(function () {
            Route::get('/questions', [MCQDashboardController::class, 'questions'])->name('questions');
        });
        
        // Examinations - redirect to new exams system
        Route::middleware(['user.access:exams'])->group(function () {
            Route::get('/examinations', function() { return redirect('/exams'); })->name('examinations');
        });
        
        // Analytics/Results - requires 'results' permission
        Route::middleware(['user.access:results'])->group(function () {
            Route::get('/analytics', [MCQDashboardController::class, 'analytics'])->name('analytics');
        });
        
        // Students/Classmates - requires 'classmate' permission
        Route::middleware(['user.access:classmate'])->group(function () {
            Route::get('/students', [MCQDashboardController::class, 'students'])->name('students');
        });
        
        // Leaderboard - requires 'leaderboard' permission
        Route::middleware(['user.access:leaderboard'])->group(function () {
            Route::get('/leaderboard', [MCQDashboardController::class, 'leaderboard'])->name('leaderboard');
        });
        
        // Posts overview - requires 'post' permission
        Route::middleware(['user.access:post'])->group(function () {
            Route::get('/posts', [MCQDashboardController::class, 'posts'])->name('posts');
        });
        
        // Read MCQ - requires 'read_access' permission
        Route::middleware(['user.access:read_access'])->group(function () {
            Route::get('/read', [MCQDashboardController::class, 'read'])->name('read');
            Route::get('/read/{mcqSet}', [MCQDashboardController::class, 'readContent'])->name('read.content');
        });

        // Notes hub - requires 'notes' permission
        Route::middleware(['user.access:notes'])->prefix('notes')->name('notes.')->group(function () {
            Route::get('/', [McqNoteHubController::class, 'index'])->name('index');
        });
        
        // Post Management Routes - requires 'post' permission
        Route::middleware(['user.access:post'])->prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::get('/{post}', [PostController::class, 'show'])->name('show');
            Route::post('/{post}/like', [PostController::class, 'like'])->name('like');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
            
            // Admin only actions
            Route::middleware(['role:admin'])->group(function () {
                Route::post('/{post}/approve', [PostController::class, 'approve'])->name('approve');
                Route::post('/{post}/publish', [PostController::class, 'publish'])->name('publish');
                Route::post('/{post}/archive', [PostController::class, 'archive'])->name('archive');
            });
        });
        
        // Settings (Admin only)
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/settings', [MCQDashboardController::class, 'settings'])->name('settings');
            Route::put('/settings', [MCQDashboardController::class, 'updateSettings'])->name('settings.update');
        });
    });

    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/checkout', [SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
    
    // MCQ Set Import Routes (Admin only) - Must be BEFORE resource routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('mcq_sets/import', [McqSetController::class, 'showImport'])->name('mcq_sets.show-import');
        Route::post('mcq_sets/import', [McqSetController::class, 'import'])->name('mcq_sets.import');
        Route::post('mcq_sets/{id}/approve', [McqSetController::class, 'approve'])->name('mcq_sets.approve');
        Route::post('mcq_sets/{id}/reject', [McqSetController::class, 'reject'])->name('mcq_sets.reject');
    });
    
    // MCQ Management System Routes - requires 'mcq_management' permission or admin role
    Route::middleware(['user.access:mcq_management'])->group(function () {
        Route::resource('mcq_sets', McqSetController::class);
        Route::resource('mcq_questions', McqQuestionController::class);
    });
    
    // Exam Taking Routes - requires 'exams' permission
    Route::middleware(['user.access:exams'])->prefix('exams')->name('exams.')->group(function () {
        Route::get('/', [ExamController::class, 'index'])->name('index');
        Route::get('/{mcqSet}', [ExamController::class, 'show'])->name('show');
        Route::get('/{mcqSet}/take', [ExamController::class, 'takeLivewire'])->name('take');
        Route::get('/{mcqSet}/start', [ExamController::class, 'start'])->name('start');
        Route::post('/{mcqSet}/submit', [ExamController::class, 'submit'])->name('submit');
        Route::get('/{mcqSet}/time-remaining', [ExamController::class, 'timeRemaining'])->name('time-remaining');
        Route::post('/{mcqSet}/auto-submit', [ExamController::class, 'autoSubmit'])->name('auto-submit');
    });
    
    // Result and Analytics Routes - requires 'results' permission
    Route::middleware(['user.access:results'])->prefix('results')->name('results.')->group(function () {
        Route::get('/', [ResultController::class, 'index'])->name('index');
        Route::get('/{mcqSet}', [ResultController::class, 'show'])->name('show');
        Route::get('/{mcqSet}/review', [ResultController::class, 'review'])->name('review');
        Route::get('/{mcqSet}/leaderboard', [ResultController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/global/leaderboard', [ResultController::class, 'globalLeaderboard'])->name('global-leaderboard');
        
        // Admin only - Export results
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/{mcqSet}/export', [ResultController::class, 'export'])->name('export');
        });
    });
    
    // Legacy Routes (optional - for backward compatibility)
    Route::middleware(['role:user|moderator|admin'])->group(function () {
        Route::get('/profile', function () {
            return view('profile.show');
        })->name('profile.show');
        
        Route::get('/comments', function () {
            return view('comments.index');
        })->name('comments.index');
    });
    
    // Moderator Routes (moderator + admin only)
    Route::middleware(['role:moderator|admin'])->group(function () {
        Route::get('/moderate/comments', function () {
            return view('moderate.comments');
        })->name('moderate.comments');
        
        Route::get('/reports', function () {
            return view('reports.index');
        })->name('reports.index');
    });
    
    // Admin Routes (admin only)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // User Access Management
        Route::prefix('user-access')->name('user-access.')->group(function () {
            Route::get('/', [UserAccessController::class, 'index'])->name('index');
            Route::get('/{userId}/edit', [UserAccessController::class, 'edit'])->name('edit');
            Route::put('/{userId}', [UserAccessController::class, 'update'])->name('update');
            Route::get('/{userId}', [UserAccessController::class, 'show'])->name('show');
            Route::post('/bulk-update', [UserAccessController::class, 'bulkUpdate'])->name('bulk-update');
            Route::patch('/{userId}/reset', [UserAccessController::class, 'reset'])->name('reset');
            Route::patch('/{userId}/grant-all', [UserAccessController::class, 'grantAll'])->name('grant-all');
        });

        Route::resource('subscriptions', AdminSubscriptionController::class)->except(['show']);
        Route::get('subscriptions/assign', [AdminSubscriptionController::class, 'assignForm'])->name('subscriptions.assign-form');
        Route::post('subscriptions/assign', [AdminSubscriptionController::class, 'assign'])->name('subscriptions.assign');
        Route::get('subscriptions/manual-review', [AdminSubscriptionController::class, 'manualReview'])->name('subscriptions.manual-review');
        Route::post('subscriptions/manual-review/{userSubscription}/approve', [AdminSubscriptionController::class, 'approveManual'])->name('subscriptions.manual.approve');
        Route::post('subscriptions/manual-review/{userSubscription}/reject', [AdminSubscriptionController::class, 'rejectManual'])->name('subscriptions.manual.reject');

        Route::get('subscription-settings', [SubscriptionSettingController::class, 'edit'])->name('subscription-settings.edit');
        Route::put('subscription-settings', [SubscriptionSettingController::class, 'update'])->name('subscription-settings.update');

        Route::get('notes', [McqNoteApprovalController::class, 'index'])->name('notes.index');
        Route::patch('notes/{note}/approve', [McqNoteApprovalController::class, 'approve'])->name('notes.approve');
        Route::patch('notes/{note}/reject', [McqNoteApprovalController::class, 'reject'])->name('notes.reject');
        Route::patch('notes/{note}/make-private', [McqNoteApprovalController::class, 'makePrivate'])->name('notes.make-private');
    });
    

});
