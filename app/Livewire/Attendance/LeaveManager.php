<?php

namespace App\Livewire\Attendance;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Livewire\Component;

class LeaveManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public string $tab = 'requests'; // requests | types

    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public ?int $leaveTypeId = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public int $totalDays = 1;
    public string $reason = '';

    // Type form
    public bool $showTypeForm = false;
    public ?int $editingTypeId = null;
    public string $typeName = '';
    public string $typeCode = '';
    public int $daysPerYear = 12;
    public bool $isPaid = true;

    public function getRequestsProperty()
    {
        return LeaveRequest::with(['employee.department', 'leaveType'])
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getLeaveTypesProperty() { return LeaveType::withCount('leaveRequests')->orderBy('name')->get(); }
    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getStatsProperty()
    {
        return [
            'pending'  => LeaveRequest::where('status', 'pending')->count(),
            'approved' => LeaveRequest::where('status', 'approved')->count(),
            'rejected' => LeaveRequest::where('status', 'rejected')->count(),
            'total'    => LeaveRequest::count(),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'leaveTypeId', 'startDate', 'endDate', 'totalDays', 'reason']);
        $this->totalDays = 1;
        if ($id) {
            $r = LeaveRequest::findOrFail($id);
            $this->editingId = $r->id;
            $this->employeeId = $r->employee_id;
            $this->leaveTypeId = $r->leave_type_id;
            $this->startDate = $r->start_date->format('Y-m-d');
            $this->endDate = $r->end_date->format('Y-m-d');
            $this->totalDays = $r->total_days;
            $this->reason = $r->reason ?? '';
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'leaveTypeId' => 'required|exists:leave_types,id',
            'startDate' => 'required|date',
            'endDate' => 'required|date|after_or_equal:startDate',
        ]);

        $data = [
            'employee_id' => $this->employeeId,
            'leave_type_id' => $this->leaveTypeId,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_days' => $this->totalDays,
            'reason' => $this->reason ?: null,
        ];

        if ($this->editingId) {
            LeaveRequest::findOrFail($this->editingId)->update($data);
        } else {
            LeaveRequest::create(['tenant_id' => auth()->user()->tenant_id, 'status' => 'pending', ...$data]);
        }
        $this->showForm = false;
    }

    public function approve(int $id): void
    {
        LeaveRequest::findOrFail($id)->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function reject(int $id): void
    {
        LeaveRequest::findOrFail($id)->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function delete(int $id): void { LeaveRequest::findOrFail($id)->delete(); }

    // Leave Type CRUD
    public function openTypeForm(?int $id = null): void
    {
        $this->reset(['editingTypeId', 'typeName', 'typeCode', 'daysPerYear', 'isPaid']);
        $this->daysPerYear = 12;
        $this->isPaid = true;
        if ($id) {
            $t = LeaveType::findOrFail($id);
            $this->editingTypeId = $t->id;
            $this->typeName = $t->name;
            $this->typeCode = $t->code;
            $this->daysPerYear = $t->days_per_year;
            $this->isPaid = $t->is_paid;
        }
        $this->showTypeForm = true;
    }

    public function saveType(): void
    {
        $this->validate(['typeName' => 'required|string|max:255', 'typeCode' => 'required|string|max:20']);
        $data = ['name' => $this->typeName, 'code' => $this->typeCode, 'days_per_year' => $this->daysPerYear, 'is_paid' => $this->isPaid];
        if ($this->editingTypeId) LeaveType::findOrFail($this->editingTypeId)->update($data);
        else LeaveType::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        $this->showTypeForm = false;
    }

    public function deleteType(int $id): void { LeaveType::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.attendance.leave-manager')
            ->layout('layouts.app', ['pageTitle' => 'Cuti & Izin']);
    }
}
