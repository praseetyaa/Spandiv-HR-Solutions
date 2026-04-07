<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerObservers();
        $this->registerEvents();
        $this->registerPolicies();
    }

    protected function registerObservers(): void
    {
        \App\Models\Employee::observe(\App\Observers\EmployeeObserver::class);
        \App\Models\LeaveRequest::observe(\App\Observers\LeaveRequestObserver::class);
        \App\Models\Payroll::observe(\App\Observers\PayrollObserver::class);
        \App\Models\CandidateTestSession::observe(\App\Observers\CandidateTestSessionObserver::class);
        \App\Models\EmployeeOnboardingTask::observe(\App\Observers\OnboardingTaskObserver::class);
    }

    protected function registerEvents(): void
    {
        Event::listen(\App\Events\LeaveRequested::class, [\App\Listeners\SendLeaveNotification::class, 'handleRequested']);
        Event::listen(\App\Events\LeaveApproved::class, [\App\Listeners\SendLeaveNotification::class, 'handleApproved']);
        Event::listen(\App\Events\LeaveRejected::class, [\App\Listeners\SendLeaveNotification::class, 'handleRejected']);
        Event::listen(\App\Events\PayrollPublished::class, [\App\Listeners\SendPayrollNotification::class, 'handle']);
        Event::listen(\App\Events\OnboardingTaskCompleted::class, [\App\Listeners\TriggerOnboardingSetup::class, 'handle']);
        Event::listen(\App\Events\PsychTestAssigned::class, [\App\Listeners\SendPsychTestInvitation::class, 'handle']);

        // Audit log on all major events
        $auditableEvents = [
            \App\Events\LeaveRequested::class,
            \App\Events\LeaveApproved::class,
            \App\Events\LeaveRejected::class,
            \App\Events\OvertimeApproved::class,
            \App\Events\PayrollPublished::class,
            \App\Events\PsychTestAssigned::class,
            \App\Events\PsychTestCompleted::class,
            \App\Events\AnnouncementPublished::class,
        ];

        foreach ($auditableEvents as $event) {
            Event::listen($event, [\App\Listeners\LogActivityOnModel::class, 'handle']);
        }
    }

    protected function registerPolicies(): void
    {
        // Super Admin bypass semua policy
        Gate::before(function ($user, $ability) {
            if ($user->guard === 'platform' && $user->hasRole('super_admin')) {
                return true;
            }
        });

        Gate::policy(\App\Models\Employee::class, \App\Policies\EmployeePolicy::class);
        Gate::policy(\App\Models\Attendance::class, \App\Policies\AttendancePolicy::class);
        Gate::policy(\App\Models\AttendanceCorrection::class, \App\Policies\AttendanceCorrectionPolicy::class);
        Gate::policy(\App\Models\LeaveRequest::class, \App\Policies\LeaveRequestPolicy::class);
        Gate::policy(\App\Models\OvertimeRequest::class, \App\Policies\OvertimeRequestPolicy::class);
        Gate::policy(\App\Models\Payroll::class, \App\Policies\PayrollPolicy::class);
        Gate::policy(\App\Models\PayrollPeriod::class, \App\Policies\PayrollPeriodPolicy::class);
        Gate::policy(\App\Models\JobPosting::class, \App\Policies\JobPostingPolicy::class);
        Gate::policy(\App\Models\Candidate::class, \App\Policies\CandidatePolicy::class);
        Gate::policy(\App\Models\PsychTest::class, \App\Policies\PsychTestPolicy::class);
        Gate::policy(\App\Models\PerformanceReview::class, \App\Policies\PerformanceReviewPolicy::class);
        Gate::policy(\App\Models\Goal::class, \App\Policies\GoalPolicy::class);
        Gate::policy(\App\Models\ExpenseRequest::class, \App\Policies\ExpenseRequestPolicy::class);
        Gate::policy(\App\Models\CompanyPolicy::class, \App\Policies\CompanyPolicyPolicy::class);
        Gate::policy(\App\Models\Tenant::class, \App\Policies\TenantPolicy::class);
    }
}
