<?php

namespace App\Livewire\Compliance;

use App\Models\Employee;
use App\Models\Grievance;
use App\Models\User;
use Livewire\Component;

class GrievanceManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public string $priorityFilter = '';

    // Form
    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public string $category = 'workplace';
    public string $description = '';
    public string $priority = 'medium';
    public bool $isAnonymous = false;

    // Resolution
    public bool $showResolveForm = false;
    public ?int $resolvingId = null;
    public string $resolution = '';

    // Selected detail
    public ?int $selectedId = null;

    public function getGrievancesProperty()
    {
        return Grievance::with(['employee', 'assignedTo'])
            ->when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->latest()
            ->get();
    }

    public function getSelectedGrievanceProperty()
    {
        if (!$this->selectedId) return null;
        return Grievance::with(['employee', 'assignedTo'])->find($this->selectedId);
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }
    public function getUsersProperty() { return User::orderBy('name')->get(); }

    public function getStatsProperty(): array
    {
        return [
            'open' => Grievance::where('status', 'open')->count(),
            'investigating' => Grievance::where('status', 'investigating')->count(),
            'resolved' => Grievance::where('status', 'resolved')->count(),
            'critical' => Grievance::where('priority', 'critical')->whereNotIn('status', ['resolved', 'closed'])->count(),
        ];
    }

    public function selectGrievance(int $id): void { $this->selectedId = $id; }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'category', 'description', 'priority', 'isAnonymous']);
        $this->priority = 'medium';
        $this->category = 'workplace';
        if ($id) {
            $g = Grievance::findOrFail($id);
            $this->editingId = $g->id;
            $this->employeeId = $g->employee_id;
            $this->category = $g->category;
            $this->description = $g->description;
            $this->priority = $g->priority;
            $this->isAnonymous = $g->is_anonymous;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'category' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required',
        ]);

        $data = [
            'employee_id' => $this->isAnonymous ? null : $this->employeeId,
            'category' => $this->category,
            'description' => $this->description,
            'priority' => $this->priority,
            'is_anonymous' => $this->isAnonymous,
        ];

        if ($this->editingId) {
            Grievance::findOrFail($this->editingId)->update($data);
        } else {
            Grievance::create([
                'tenant_id' => auth()->user()->tenant_id,
                ...$data,
            ]);
        }
        $this->showForm = false;
    }

    public function updateStatus(int $id, string $status): void
    {
        Grievance::findOrFail($id)->update(['status' => $status]);
    }

    public function assignTo(int $id, int $userId): void
    {
        Grievance::findOrFail($id)->update(['assigned_to' => $userId, 'status' => 'investigating']);
    }

    public function openResolveForm(int $id): void
    {
        $this->resolvingId = $id;
        $this->resolution = '';
        $this->showResolveForm = true;
    }

    public function resolveGrievance(): void
    {
        $this->validate(['resolution' => 'required|string']);
        Grievance::findOrFail($this->resolvingId)->update([
            'status' => 'resolved',
            'resolution' => $this->resolution,
            'resolved_at' => now(),
        ]);
        $this->showResolveForm = false;
    }

    public function delete(int $id): void
    {
        Grievance::findOrFail($id)->delete();
        if ($this->selectedId === $id) $this->selectedId = null;
    }

    public function render()
    {
        return view('livewire.compliance.grievance-manager')
            ->layout('layouts.app', ['pageTitle' => 'Keluhan & Grievance']);
    }
}
