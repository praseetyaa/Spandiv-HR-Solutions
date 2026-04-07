<?php

namespace App\Livewire\Attendance;

use App\Models\Employee;
use App\Models\OvertimeRequest;
use Livewire\Component;

class OvertimeManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public ?string $date = null;
    public string $startTime = '';
    public string $endTime = '';
    public float $hours = 0;
    public string $reason = '';

    public function getRequestsProperty()
    {
        return OvertimeRequest::with(['employee.department'])
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getStatsProperty()
    {
        return [
            'pending'  => OvertimeRequest::where('status', 'pending')->count(),
            'approved' => OvertimeRequest::where('status', 'approved')->count(),
            'total_hours' => OvertimeRequest::where('status', 'approved')->sum('hours'),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'date', 'startTime', 'endTime', 'hours', 'reason']);
        $this->date = now()->format('Y-m-d');
        if ($id) {
            $o = OvertimeRequest::findOrFail($id);
            $this->editingId = $o->id;
            $this->employeeId = $o->employee_id;
            $this->date = $o->date->format('Y-m-d');
            $this->startTime = $o->start_time;
            $this->endTime = $o->end_time;
            $this->hours = $o->hours;
            $this->reason = $o->reason;
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'date' => 'required|date',
            'startTime' => 'required',
            'endTime' => 'required',
            'hours' => 'required|numeric|min:0.5',
            'reason' => 'required|string',
        ]);

        $data = [
            'employee_id' => $this->employeeId,
            'date' => $this->date,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'hours' => $this->hours,
            'reason' => $this->reason,
        ];

        if ($this->editingId) {
            OvertimeRequest::findOrFail($this->editingId)->update($data);
        } else {
            OvertimeRequest::create(['tenant_id' => auth()->user()->tenant_id, 'status' => 'pending', ...$data]);
        }
        $this->showForm = false;
    }

    public function approve(int $id): void
    {
        OvertimeRequest::findOrFail($id)->update(['status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function reject(int $id): void
    {
        OvertimeRequest::findOrFail($id)->update(['status' => 'rejected', 'approved_by' => auth()->id(), 'approved_at' => now()]);
    }

    public function delete(int $id): void { OvertimeRequest::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.attendance.overtime-manager')
            ->layout('layouts.app', ['pageTitle' => 'Lembur']);
    }
}
