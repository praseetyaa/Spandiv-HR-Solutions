<?php

namespace App\Livewire\Performance;

use App\Models\Employee;
use App\Models\Goal;
use App\Models\ReviewCycle;
use Livewire\Component;

class GoalManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public ?int $cycleFilter = null;
    public ?int $employeeFilter = null;

    // Goal form
    public bool $showGoalForm = false;
    public ?int $editingGoalId = null;
    public ?int $goalEmployeeId = null;
    public ?int $goalCycleId = null;
    public string $goalTitle = '';
    public string $goalDescription = '';
    public string $goalMetricUnit = '';
    public ?string $goalTarget = null;
    public ?string $goalActual = null;
    public ?int $goalWeight = null;
    public string $goalStatus = 'draft';

    // ── Computed ──
    public function getGoalsProperty()
    {
        return Goal::with(['employee', 'cycle'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->cycleFilter, fn ($q) => $q->where('cycle_id', $this->cycleFilter))
            ->when($this->employeeFilter, fn ($q) => $q->where('employee_id', $this->employeeFilter))
            ->latest()
            ->get();
    }

    public function getCyclesProperty()
    {
        return ReviewCycle::orderByDesc('start_date')->get();
    }

    public function getEmployeesProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function getStatsProperty(): array
    {
        $goals = $this->goals;
        $achieved = $goals->where('status', 'achieved')->count();
        $total = $goals->count();
        $avgAch = $total > 0 ? $goals->avg(fn ($g) => $g->achievement_percent) : 0;

        return [
            'total'    => $total,
            'active'   => $goals->where('status', 'active')->count(),
            'achieved' => $achieved,
            'avg_pct'  => round($avgAch, 1),
        ];
    }

    // ── CRUD ──
    public function openGoalForm(?int $id = null): void
    {
        $this->reset(['editingGoalId', 'goalEmployeeId', 'goalCycleId', 'goalTitle', 'goalDescription', 'goalMetricUnit', 'goalTarget', 'goalActual', 'goalWeight', 'goalStatus']);
        $this->goalStatus = 'draft';
        if ($id) {
            $g = Goal::findOrFail($id);
            $this->editingGoalId = $g->id;
            $this->goalEmployeeId = $g->employee_id;
            $this->goalCycleId = $g->cycle_id;
            $this->goalTitle = $g->title;
            $this->goalDescription = $g->description ?? '';
            $this->goalMetricUnit = $g->metric_unit ?? '';
            $this->goalTarget = $g->target;
            $this->goalActual = $g->actual;
            $this->goalWeight = $g->weight_percent;
            $this->goalStatus = $g->status ?? 'draft';
        }
        $this->showGoalForm = true;
    }

    public function saveGoal(): void
    {
        $this->validate([
            'goalEmployeeId' => 'required|exists:employees,id',
            'goalCycleId'    => 'required|exists:review_cycles,id',
            'goalTitle'      => 'required|string|max:255',
            'goalTarget'     => 'required|numeric|min:0',
            'goalWeight'     => 'required|integer|min:1|max:100',
        ]);

        $data = [
            'employee_id'    => $this->goalEmployeeId,
            'cycle_id'       => $this->goalCycleId,
            'title'          => $this->goalTitle,
            'description'    => $this->goalDescription ?: null,
            'metric_unit'    => $this->goalMetricUnit ?: null,
            'target'         => $this->goalTarget,
            'actual'         => $this->goalActual ?? 0,
            'weight_percent' => $this->goalWeight,
            'status'         => $this->goalStatus,
        ];

        if ($this->editingGoalId) {
            Goal::findOrFail($this->editingGoalId)->update($data);
        } else {
            Goal::create([
                'tenant_id' => auth()->user()->tenant_id,
                ...$data,
            ]);
        }
        $this->showGoalForm = false;
    }

    public function updateProgress(int $id, $actual): void
    {
        $goal = Goal::findOrFail($id);
        $goal->update(['actual' => $actual]);
    }

    public function updateStatus(int $id, string $status): void
    {
        Goal::findOrFail($id)->update(['status' => $status]);
    }

    public function deleteGoal(int $id): void
    {
        Goal::findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.performance.goal-manager')
            ->layout('layouts.app', ['pageTitle' => 'Goal / KPI']);
    }
}
