<?php

namespace App\Livewire\Employee;

use App\Models\Department;
use App\Models\Employee;
use Livewire\Component;

class DepartmentManager extends Component
{
    public string $search = '';
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $name = '';
    public string $code = '';
    public ?int $parentId = null;
    public ?int $headEmployeeId = null;
    public bool $isActive = true;

    public function getDepartmentsProperty()
    {
        return Department::withCount('employees')
            ->with(['parent', 'headEmployee'])
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")->orWhere('code', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();
    }

    public function getParentOptionsProperty()
    {
        return Department::active()->orderBy('name')->get();
    }

    public function getEmployeeOptionsProperty()
    {
        return Employee::active()->orderBy('first_name')->get();
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'name', 'code', 'parentId', 'headEmployeeId', 'isActive']);
        $this->isActive = true;
        if ($id) {
            $d = Department::findOrFail($id);
            $this->editingId = $d->id;
            $this->name = $d->name;
            $this->code = $d->code;
            $this->parentId = $d->parent_id;
            $this->headEmployeeId = $d->head_employee_id;
            $this->isActive = $d->is_active;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'parent_id' => $this->parentId ?: null,
            'head_employee_id' => $this->headEmployeeId ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingId) {
            Department::findOrFail($this->editingId)->update($data);
        } else {
            Department::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void { Department::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.employee.department-manager')
            ->layout('layouts.app', ['pageTitle' => 'Departemen']);
    }
}
