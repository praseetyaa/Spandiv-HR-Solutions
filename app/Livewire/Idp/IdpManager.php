<?php

namespace App\Livewire\Idp;

use App\Models\Employee;
use App\Models\IdpActivity;
use App\Models\IdpPlan;
use Livewire\Component;

class IdpManager extends Component
{
    // Filters
    public ?int $employeeFilter = null;
    public ?int $yearFilter = null;
    public ?string $statusFilter = null;

    // IDP Plan form
    public bool $showPlanForm = false;
    public ?int $editingPlanId = null;
    public ?int $planEmployeeId = null;
    public ?int $planYear = null;
    public string $planFocus = '';
    public string $planStatus = 'draft';

    // Activity form
    public bool $showActivityForm = false;
    public ?int $editingActivityId = null;
    public ?int $activityPlanId = null;
    public string $activityType = 'training';
    public string $activityTitle = '';
    public string $activityDescription = '';
    public ?string $activityTargetDate = null;
    public string $activityStatus = 'planned';
    public string $activityOutcome = '';

    // Expanded plan rows
    public array $expandedPlans = [];

    protected function rules(): array
    {
        return [
            'planEmployeeId' => 'required|exists:employees,id',
            'planYear'       => 'required|integer|min:2020|max:2040',
            'planFocus'      => 'required|string|max:2000',
            'planStatus'     => 'required|in:draft,active,completed',
        ];
    }

    public function mount(): void
    {
        $this->yearFilter = now()->year;
        $this->planYear = now()->year;
    }

    public function getPlansProperty()
    {
        return IdpPlan::with(['employee.department', 'employee.jobPosition', 'activities', 'approvedBy'])
            ->when($this->employeeFilter, fn ($q) => $q->where('employee_id', $this->employeeFilter))
            ->when($this->yearFilter, fn ($q) => $q->where('year', $this->yearFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    // ============================================================
    // Plan Row Toggle
    // ============================================================

    public function togglePlan(int $planId): void
    {
        if (in_array($planId, $this->expandedPlans)) {
            $this->expandedPlans = array_values(array_diff($this->expandedPlans, [$planId]));
        } else {
            $this->expandedPlans[] = $planId;
        }
    }

    // ============================================================
    // IDP Plan CRUD
    // ============================================================

    public function openPlanForm(?int $planId = null): void
    {
        $this->resetPlanForm();

        if ($planId) {
            $plan = IdpPlan::findOrFail($planId);
            $this->editingPlanId = $plan->id;
            $this->planEmployeeId = $plan->employee_id;
            $this->planYear = $plan->year;
            $this->planFocus = $plan->development_focus;
            $this->planStatus = $plan->status;
        }

        $this->showPlanForm = true;
    }

    public function savePlan(): void
    {
        $this->validate();

        $data = [
            'employee_id'      => $this->planEmployeeId,
            'year'             => $this->planYear,
            'development_focus' => $this->planFocus,
            'status'           => $this->planStatus,
        ];

        if ($this->editingPlanId) {
            IdpPlan::findOrFail($this->editingPlanId)->update($data);
        } else {
            $data['tenant_id'] = auth()->user()->tenant_id;
            IdpPlan::create($data);
        }

        $this->showPlanForm = false;
        $this->resetPlanForm();
    }

    public function deletePlan(int $planId): void
    {
        IdpPlan::findOrFail($planId)->delete();
    }

    public function approvePlan(int $planId): void
    {
        $plan = IdpPlan::findOrFail($planId);
        $plan->update([
            'status'      => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }

    private function resetPlanForm(): void
    {
        $this->editingPlanId = null;
        $this->planEmployeeId = null;
        $this->planYear = now()->year;
        $this->planFocus = '';
        $this->planStatus = 'draft';
    }

    // ============================================================
    // Activity CRUD
    // ============================================================

    public function openActivityForm(int $planId, ?int $activityId = null): void
    {
        $this->resetActivityForm();
        $this->activityPlanId = $planId;

        if ($activityId) {
            $activity = IdpActivity::findOrFail($activityId);
            $this->editingActivityId = $activity->id;
            $this->activityType = $activity->activity_type;
            $this->activityTitle = $activity->title;
            $this->activityDescription = $activity->description ?? '';
            $this->activityTargetDate = $activity->target_date?->format('Y-m-d');
            $this->activityStatus = $activity->status;
            $this->activityOutcome = $activity->outcome ?? '';
        }

        $this->showActivityForm = true;
    }

    public function saveActivity(): void
    {
        $this->validate([
            'activityPlanId'      => 'required|exists:idp_plans,id',
            'activityType'        => 'required|in:training,mentoring,project,course,certification,other',
            'activityTitle'       => 'required|string|max:255',
            'activityDescription' => 'nullable|string|max:2000',
            'activityTargetDate'  => 'required|date',
            'activityStatus'      => 'required|in:planned,in_progress,completed,cancelled',
            'activityOutcome'     => 'nullable|string|max:2000',
        ]);

        $data = [
            'idp_id'        => $this->activityPlanId,
            'activity_type' => $this->activityType,
            'title'         => $this->activityTitle,
            'description'   => $this->activityDescription ?: null,
            'target_date'   => $this->activityTargetDate,
            'status'        => $this->activityStatus,
            'outcome'       => $this->activityOutcome ?: null,
            'completed_at'  => $this->activityStatus === 'completed' ? now() : null,
        ];

        if ($this->editingActivityId) {
            IdpActivity::findOrFail($this->editingActivityId)->update($data);
        } else {
            IdpActivity::create($data);
        }

        $this->showActivityForm = false;
        $this->resetActivityForm();
    }

    public function deleteActivity(int $activityId): void
    {
        IdpActivity::findOrFail($activityId)->delete();
    }

    public function markActivityComplete(int $activityId): void
    {
        IdpActivity::findOrFail($activityId)->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);
    }

    private function resetActivityForm(): void
    {
        $this->editingActivityId = null;
        $this->activityPlanId = null;
        $this->activityType = 'training';
        $this->activityTitle = '';
        $this->activityDescription = '';
        $this->activityTargetDate = null;
        $this->activityStatus = 'planned';
        $this->activityOutcome = '';
    }

    public function render()
    {
        return view('livewire.idp.idp-manager')
            ->layout('layouts.app', ['pageTitle' => 'Individual Development Plans']);
    }
}
