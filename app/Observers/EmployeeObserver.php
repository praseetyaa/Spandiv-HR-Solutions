<?php
namespace App\Observers;

use App\Models\Employee;
use App\Jobs\AutoAssignOnboardingJob;
use App\Services\LeaveBalanceService;

class EmployeeObserver
{
    public function __construct(private LeaveBalanceService $leaveBalanceService) {}

    public function created(Employee $employee): void
    {
        if ($employee->status !== 'active') return;
        $this->leaveBalanceService->initializeForEmployee($employee);
        AutoAssignOnboardingJob::dispatch($employee->id)->delay(now()->addSeconds(5));
    }

    public function updated(Employee $employee): void
    {
        if ($employee->wasChanged('status') && $employee->status === 'active' && $employee->getOriginal('status') !== 'active') {
            $this->leaveBalanceService->initializeForEmployee($employee);
        }
    }
}
