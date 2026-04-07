<?php
namespace App\Jobs;

use App\Models\Employee;
use App\Services\OnboardingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AutoAssignOnboardingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $employeeId) {}

    public function handle(OnboardingService $service): void
    {
        $employee = Employee::findOrFail($this->employeeId);
        $service->assignTemplate($employee);
    }
}
