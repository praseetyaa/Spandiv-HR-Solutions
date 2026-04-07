<?php
namespace App\Observers;

use App\Events\OnboardingTaskCompleted;
use App\Models\EmployeeOnboardingTask;

class OnboardingTaskObserver
{
    public function updated(EmployeeOnboardingTask $task): void
    {
        if ($task->wasChanged('is_completed') && $task->is_completed) {
            $task->update(['completed_at' => now()]);
            OnboardingTaskCompleted::dispatch($task);
        }
    }
}
