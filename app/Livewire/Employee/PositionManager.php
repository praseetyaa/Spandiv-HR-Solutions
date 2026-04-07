<?php

namespace App\Livewire\Employee;

use App\Models\Department;
use App\Models\JobPosition;
use Livewire\Component;

class PositionManager extends Component
{
    public string $search = '';
    public string $departmentFilter = '';
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $title = '';
    public ?int $departmentId = null;
    public string $grade = '';
    public string $level = 'staff';
    public string $description = '';
    public bool $isActive = true;

    public function getPositionsProperty()
    {
        return JobPosition::with('department')
            ->withCount('employees')
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->departmentFilter, fn ($q) => $q->where('department_id', $this->departmentFilter))
            ->orderBy('title')
            ->get();
    }

    public function getDepartmentsProperty()
    {
        return Department::active()->orderBy('name')->get();
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'title', 'departmentId', 'grade', 'level', 'description', 'isActive']);
        $this->isActive = true;
        $this->level = 'staff';
        if ($id) {
            $p = JobPosition::findOrFail($id);
            $this->editingId = $p->id;
            $this->title = $p->title;
            $this->departmentId = $p->department_id;
            $this->grade = $p->grade ?? '';
            $this->level = $p->level;
            $this->description = $p->description ?? '';
            $this->isActive = $p->is_active;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'departmentId' => 'required|exists:departments,id',
        ]);

        $data = [
            'title' => $this->title,
            'department_id' => $this->departmentId,
            'grade' => $this->grade ?: null,
            'level' => $this->level,
            'description' => $this->description ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            JobPosition::findOrFail($this->editingId)->update($data);
        } else {
            JobPosition::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void { JobPosition::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.employee.position-manager')
            ->layout('layouts.app', ['pageTitle' => 'Jabatan']);
    }
}
