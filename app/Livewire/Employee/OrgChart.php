<?php

namespace App\Livewire\Employee;

use App\Models\Employee;
use App\Models\Department;
use Livewire\Component;

class OrgChart extends Component
{
    public ?int $departmentId = null;

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;

        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        $query = Employee::where('tenant_id', $tenantId)->where('status', 'active')
            ->with(['department', 'position']);

        if ($this->departmentId) {
            $query->where('department_id', $this->departmentId);
        }

        $employees = $query->get();

        // Build tree structure grouped by department
        $tree = $departments->map(fn ($dept) => [
            'department' => $dept,
            'employees'  => $employees->where('department_id', $dept->id)->values(),
            'head'       => $employees->where('department_id', $dept->id)
                ->first(fn ($e) => str_contains(strtolower($e->position?->title ?? ''), 'head') ||
                                   str_contains(strtolower($e->position?->title ?? ''), 'manager')),
        ]);

        return view('livewire.employee.org-chart', [
            'departments' => $departments,
            'tree'        => $tree,
        ]);
    }
}
