<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Talent\TalentGrid;
use App\Livewire\Idp\IdpManager;
use App\Livewire\Learning\CourseCatalog;
use App\Livewire\Learning\TrainingManager;
use App\Livewire\Learning\CertificationTracker;
use App\Livewire\Benefit\BenefitManager;
use App\Livewire\Expense\ExpenseManager;
use App\Livewire\Loan\LoanManager;
use App\Livewire\Compliance\PolicyManager;
use App\Livewire\Compliance\DisciplinaryManager;
use App\Livewire\Compliance\GrievanceManager;
use App\Livewire\Engagement\SurveyManager;
use App\Livewire\Engagement\RecognitionWall;
use App\Livewire\Engagement\AnnouncementManager;
use Illuminate\Support\Facades\Route;

// ============================================================
// Guest Routes
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

// ============================================================
// Authenticated Routes (Tenant)
// ============================================================
Route::middleware(['auth', 'tenant', 'subscription'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

    // ── Employees ──────────────────────────────────────────
    // Route::prefix('employees')->name('employees.')->group(function () {
    //     // Coming in Step 4
    // });

    // ── Attendance ─────────────────────────────────────────
    // Route::prefix('attendance')->name('attendance.')->group(function () {
    //     // Coming in Step 5
    // });

    // ── Leave Management ───────────────────────────────────
    // Route::prefix('leave')->name('leave.')->group(function () {
    //     // Coming in Step 6
    // });

    // ── Payroll ────────────────────────────────────────────
    // Route::prefix('payroll')->name('payroll.')->middleware('feature:payroll')->group(function () {
    //     // Coming in Step 7
    // });

    // ── Talent & Performance ──────────────────────────────
    Route::prefix('talent')->name('talent.')->group(function () {
        Route::get('/nine-box', TalentGrid::class)->name('nine-box');
    });

    // ── IDP (Individual Development Plans) ────────────────
    Route::prefix('idp')->name('idp.')->group(function () {
        Route::get('/', IdpManager::class)->name('index');
    });

    // ── Learning & Training ──────────────────────────────
    Route::prefix('learning')->name('learning.')->group(function () {
        Route::get('/courses', CourseCatalog::class)->name('courses');
        Route::get('/training', TrainingManager::class)->name('training');
        Route::get('/certifications', CertificationTracker::class)->name('certifications');
    });

    // ── Benefit & Expense ────────────────────────────────
    Route::get('/benefit', BenefitManager::class)->name('benefit.index');
    Route::get('/expense', ExpenseManager::class)->name('expense.index');
    Route::get('/loans', LoanManager::class)->name('loans.index');

    // ── Compliance ───────────────────────────────────────
    Route::prefix('compliance')->name('compliance.')->group(function () {
        Route::get('/policies', PolicyManager::class)->name('policies');
        Route::get('/disciplinary', DisciplinaryManager::class)->name('disciplinary');
        Route::get('/grievances', GrievanceManager::class)->name('grievances');
    });

    // ── Engagement ───────────────────────────────────────
    Route::prefix('engagement')->name('engagement.')->group(function () {
        Route::get('/surveys', SurveyManager::class)->name('surveys');
        Route::get('/recognition', RecognitionWall::class)->name('recognition');
        Route::get('/announcements', AnnouncementManager::class)->name('announcements');
    });
});

// ============================================================
// Platform Admin Routes
// ============================================================
Route::middleware(['auth', 'platform'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Platform admin routes will be added here
});

// ============================================================
// Subscription Expired
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/subscription/expired', function () {
        return view('subscription.expired');
    })->name('subscription.expired');
});
