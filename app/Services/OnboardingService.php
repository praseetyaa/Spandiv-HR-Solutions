<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeOnboarding;
use App\Models\EmployeeOnboardingTask;
use App\Models\OnboardingTemplate;

class OnboardingService
{
    public function assignTemplate(Employee $employee, ?int $templateId = null): ?EmployeeOnboarding
    {
        $template = $templateId
            ? OnboardingTemplate::findOrFail($templateId)
            : $this->findBestTemplate($employee);

        if (!$template) return null;

        $onboarding = EmployeeOnboarding::create([
            'tenant_id'         => $employee->tenant_id,
            'employee_id'       => $employee->id,
            'template_id'       => $template->id,
            'start_date'        => $employee->join_date,
            'expected_end_date' => $employee->join_date->addDays(
                $template->taskTemplates()->max('due_day_offset') ?? 30
            ),
            'status'            => 'not_started',
            'created_by'        => auth()->id() ?? 1,
        ]);

        foreach ($template->taskTemplates()->orderBy('order_number')->get() as $taskTpl) {
            EmployeeOnboardingTask::create([
                'onboarding_id'    => $onboarding->id,
                'template_task_id' => $taskTpl->id,
                'title'            => $taskTpl->title,
                'description'      => $taskTpl->description,
                'category'         => $taskTpl->category,
                'due_date'         => $employee->join_date->addDays($taskTpl->due_day_offset),
            ]);
        }

        return $onboarding;
    }

    protected function findBestTemplate(Employee $employee): ?OnboardingTemplate
    {
        // Try specific match first
        $template = OnboardingTemplate::where('tenant_id', $employee->tenant_id)
            ->where('is_active', true)
            ->where(fn ($q) => $q
                ->where('department_id', $employee->department_id)
                ->orWhere('position_id', $employee->position_id)
            )
            ->first();

        // Fall back to default
        return $template ?? OnboardingTemplate::where('tenant_id', $employee->tenant_id)
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    public function recalculateProgress(EmployeeOnboarding $onboarding): void
    {
        $total     = $onboarding->tasks()->count();
        $completed = $onboarding->tasks()->where('is_completed', true)->count();

        $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
        $status  = match (true) {
            $percent >= 100                                      => 'completed',
            $percent > 0                                         => 'in_progress',
            $onboarding->expected_end_date->isPast()             => 'overdue',
            default                                              => 'not_started',
        };

        $onboarding->update([
            'progress_percent' => $percent,
            'status'           => $status,
        ]);
    }
}
