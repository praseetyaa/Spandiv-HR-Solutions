<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\DashboardController;
use App\Livewire\Talent\TalentGrid;
use App\Livewire\Idp\IdpManager;
use App\Livewire\Performance\ReviewCycleManager;
use App\Livewire\Performance\GoalManager;
use App\Livewire\PsychTest\TestManager;
use App\Livewire\PsychTest\TestAssignmentManager;
use App\Livewire\PsychTest\TestResultManager;
use App\Livewire\Settings\NotificationManager;
use App\Livewire\Settings\AuditLogViewer;
use App\Livewire\Settings\TenantSettingsManager;
use App\Livewire\Settings\ApiTokenManager;
use App\Livewire\Settings\UserProfile;
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
use App\Livewire\Employee\EmployeeManager;
use App\Livewire\Employee\DepartmentManager;
use App\Livewire\Employee\PositionManager;
use App\Livewire\Attendance\AttendanceManager;
use App\Livewire\Attendance\LeaveManager;
use App\Livewire\Attendance\OvertimeManager;
use App\Livewire\Payroll\PayrollManager;
use App\Livewire\Payroll\SalaryComponentManager;
use App\Livewire\Payroll\BonusManager;
use App\Livewire\Recruitment\JobPostingManager;
use App\Livewire\Recruitment\CandidateManager;
use App\Livewire\Recruitment\OnboardingManager;
use App\Livewire\Recruitment\CandidatePipeline;
use App\Livewire\Recruitment\InterviewScheduler;
use App\Livewire\Employee\OrgChart;
use App\Livewire\Attendance\ClockInOut;
use App\Livewire\Attendance\LeaveCalendar;
use App\Livewire\Performance\ReviewForm;
use App\Http\Controllers\Platform\DashboardController as PlatformDashboardController;
use App\Http\Controllers\Platform\TenantController;
use App\Http\Controllers\Platform\PlanController;
use App\Http\Controllers\Platform\SubscriptionController;
use App\Http\Controllers\Platform\ImpersonateController;
use App\Http\Controllers\PsychTest\PublicTestController;
use App\Http\Controllers\Payroll\PayslipController;
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

    // ── Employees (KARYAWAN) ─────────────────────────────
    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('/', EmployeeManager::class)->name('index');
        Route::get('/departments', DepartmentManager::class)->name('departments');
        Route::get('/positions', PositionManager::class)->name('positions');
        Route::get('/org-chart', OrgChart::class)->name('org-chart');
    });

    // ── Attendance & Leave (KEHADIRAN) ───────────────────
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', AttendanceManager::class)->name('index');
        Route::get('/clock', ClockInOut::class)->name('clock');
        Route::get('/leave', LeaveManager::class)->name('leave');
        Route::get('/leave-calendar', LeaveCalendar::class)->name('leave-calendar');
        Route::get('/overtime', OvertimeManager::class)->name('overtime');
    });

    // ── Payroll (PENGGAJIAN) ─────────────────────────────
    Route::prefix('payroll')->name('payroll.')->group(function () {
        Route::get('/', PayrollManager::class)->name('index');
        Route::get('/components', SalaryComponentManager::class)->name('components');
        Route::get('/bonus', BonusManager::class)->name('bonus');
    });

    // ── Recruitment & Onboarding (REKRUTMEN) ─────────────
    Route::prefix('recruitment')->name('recruitment.')->group(function () {
        Route::get('/postings', JobPostingManager::class)->name('postings');
        Route::get('/candidates', CandidateManager::class)->name('candidates');
        Route::get('/pipeline', CandidatePipeline::class)->name('pipeline');
        Route::get('/interviews', InterviewScheduler::class)->name('interviews');
        Route::get('/onboarding', OnboardingManager::class)->name('onboarding');
    });

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

    // ── Performance Management ────────────────────────────
    Route::prefix('performance')->name('performance.')->group(function () {
        Route::get('/cycles', ReviewCycleManager::class)->name('cycles');
        Route::get('/goals', GoalManager::class)->name('goals');
        Route::get('/review/{reviewId?}', ReviewForm::class)->name('review');
    });

    // ── Psych Test ────────────────────────────────────────
    Route::prefix('psych-test')->name('psych-test.')->group(function () {
        Route::get('/tests', TestManager::class)->name('tests');
        Route::get('/assignments', TestAssignmentManager::class)->name('assignments');
        Route::get('/results', TestResultManager::class)->name('results');
    });

    // ── Settings ──────────────────────────────────────────
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/notifications', NotificationManager::class)->name('notifications');
        Route::get('/audit-log', AuditLogViewer::class)->name('audit-log');
        Route::get('/general', TenantSettingsManager::class)->name('general');
        Route::get('/api', ApiTokenManager::class)->name('api');
        Route::get('/profile', UserProfile::class)->name('profile');
    });
});

// ============================================================
// Psych Test Public Routes (Token-based, no auth)
// ============================================================
Route::prefix('test')->name('test.')->group(function () {
    Route::get('/{token}', [PublicTestController::class, 'start'])->name('start');
    Route::post('/answer', [PublicTestController::class, 'submitAnswer'])->name('answer');
    Route::post('/{sessionId}/finish', [PublicTestController::class, 'finish'])->name('finish');
    Route::post('/{sessionId}/tab-switch', [PublicTestController::class, 'reportTabSwitch'])->name('tab-switch');
});

// ============================================================
// Payslip Routes
// ============================================================
Route::middleware(['auth', 'tenant', 'subscription'])->prefix('payslip')->name('payslip.')->group(function () {
    Route::get('/{payroll}', [PayslipController::class, 'show'])->name('show');
    Route::get('/{payroll}/download', [PayslipController::class, 'download'])->name('download');
});

// ============================================================
// Platform Admin Routes
// ============================================================
Route::middleware(['auth', 'platform'])->prefix('platform')->name('platform.')->group(function () {
    Route::get('/dashboard', [PlatformDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/create', [TenantController::class, 'create'])->name('create');
        Route::post('/', [TenantController::class, 'store'])->name('store');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
        Route::post('/{tenant}/suspend', [TenantController::class, 'suspend'])->name('suspend');
        Route::post('/{tenant}/activate', [TenantController::class, 'activate'])->name('activate');
    });

    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::post('/', [PlanController::class, 'store'])->name('store');
        Route::put('/{plan}', [PlanController::class, 'update'])->name('update');
    });

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [SubscriptionController::class, 'index'])->name('index');
        Route::post('/{subscription}/approve', [SubscriptionController::class, 'approve'])->name('approve');
    });

    Route::post('/impersonate/{tenant}', [ImpersonateController::class, 'start'])->name('impersonate.start');
    Route::post('/impersonate/stop', [ImpersonateController::class, 'stop'])->name('impersonate.stop');
});

// ============================================================
// Subscription Expired
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/subscription/expired', function () {
        return view('subscription.expired');
    })->name('subscription.expired');
});
