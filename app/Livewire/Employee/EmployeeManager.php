<?php

namespace App\Livewire\Employee;

use App\Models\Department;
use App\Models\Employee;
use App\Models\JobPosition;
use Livewire\Component;

class EmployeeManager extends Component
{
    public string $search = '';
    public string $departmentFilter = '';
    public string $statusFilter = '';
    public ?int $selectedId = null;

    // Form
    public bool $showForm = false;
    public ?int $editingId = null;
    public string $firstName = '';
    public string $lastName = '';
    public string $email = '';
    public string $phone = '';
    public string $employeeNumber = '';
    public ?int $departmentId = null;
    public ?int $positionId = null;
    public string $employmentType = 'permanent';
    public ?string $joinDate = null;
    public string $status = 'active';

    public function getEmployeesProperty()
    {
        return Employee::with(['department', 'jobPosition'])
            ->when($this->search, fn ($q) => $q->where(fn ($s) =>
                $s->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('employee_number', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->when($this->departmentFilter, fn ($q) => $q->where('department_id', $this->departmentFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getDepartmentsProperty() { return Department::active()->orderBy('name')->get(); }
    public function getPositionsProperty() { return JobPosition::active()->orderBy('title')->get(); }

    public function selectEmployee(int $id): void
    {
        $this->selectedId = $this->selectedId === $id ? null : $id;
    }

    public function getSelectedProperty()
    {
        if (!$this->selectedId) return null;
        return Employee::with(['department', 'jobPosition', 'manager'])->find($this->selectedId);
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'firstName', 'lastName', 'email', 'phone', 'employeeNumber', 'departmentId', 'positionId', 'employmentType', 'joinDate', 'status']);
        if ($id) {
            $e = Employee::findOrFail($id);
            $this->editingId = $e->id;
            $this->firstName = $e->first_name;
            $this->lastName = $e->last_name ?? '';
            $this->email = $e->email ?? '';
            $this->phone = $e->phone ?? '';
            $this->employeeNumber = $e->employee_number;
            $this->departmentId = $e->department_id;
            $this->positionId = $e->position_id;
            $this->employmentType = $e->employment_type;
            $this->joinDate = $e->join_date->format('Y-m-d');
            $this->status = $e->status;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'firstName' => 'required|string|max:255',
            'employeeNumber' => 'required|string|max:50',
            'departmentId' => 'required|exists:departments,id',
            'positionId' => 'required|exists:job_positions,id',
            'joinDate' => 'required|date',
        ]);

        $data = [
            'first_name' => $this->firstName,
            'last_name' => $this->lastName ?: null,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'employee_number' => $this->employeeNumber,
            'department_id' => $this->departmentId,
            'position_id' => $this->positionId,
            'employment_type' => $this->employmentType,
            'join_date' => $this->joinDate,
            'status' => $this->status,
        ];

        if ($this->editingId) {
            Employee::findOrFail($this->editingId)->update($data);
        } else {
            Employee::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        }

        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Employee::findOrFail($id)->delete();
        if ($this->selectedId === $id) $this->selectedId = null;
    }

    public function render()
    {
        return view('livewire.employee.employee-manager')
            ->layout('layouts.app', ['pageTitle' => 'Data Karyawan']);
    }
}
