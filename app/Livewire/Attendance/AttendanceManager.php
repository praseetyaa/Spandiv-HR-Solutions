<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use App\Models\Employee;
use Livewire\Component;

class AttendanceManager extends Component
{
    public string $search = '';
    public string $statusFilter = '';
    public ?string $dateFilter = null;

    public bool $showForm = false;
    public ?int $editingId = null;
    public ?int $employeeId = null;
    public ?string $date = null;
    public ?string $clockIn = null;
    public ?string $clockOut = null;
    public string $status = 'present';
    public string $clockInMethod = 'manual';
    public string $notes = '';

    public function mount(): void
    {
        $this->dateFilter = now()->format('Y-m-d');
    }

    public function getAttendancesProperty()
    {
        return Attendance::with(['employee.department'])
            ->when($this->dateFilter, fn ($q) => $q->whereDate('date', $this->dateFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->search, fn ($q) => $q->whereHas('employee', fn ($e) =>
                $e->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
            ))
            ->latest('date')
            ->get();
    }

    public function getEmployeesProperty() { return Employee::active()->orderBy('first_name')->get(); }

    public function getStatsProperty()
    {
        $date = $this->dateFilter ?: now()->format('Y-m-d');
        $base = Attendance::whereDate('date', $date);
        return [
            'total'   => (clone $base)->count(),
            'present' => (clone $base)->where('status', 'present')->count(),
            'late'    => (clone $base)->where('status', 'late')->count(),
            'absent'  => (clone $base)->where('status', 'absent')->count(),
        ];
    }

    public function openForm(?int $id = null): void
    {
        $this->reset(['editingId', 'employeeId', 'date', 'clockIn', 'clockOut', 'status', 'clockInMethod', 'notes']);
        $this->date = now()->format('Y-m-d');
        $this->status = 'present';
        $this->clockInMethod = 'manual';
        if ($id) {
            $a = Attendance::findOrFail($id);
            $this->editingId = $a->id;
            $this->employeeId = $a->employee_id;
            $this->date = $a->date->format('Y-m-d');
            $this->clockIn = $a->clock_in?->format('H:i');
            $this->clockOut = $a->clock_out?->format('H:i');
            $this->status = $a->status;
            $this->clockInMethod = $a->clock_in_method;
            $this->notes = $a->notes ?? '';
        }
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            'employeeId' => 'required|exists:employees,id',
            'date' => 'required|date',
        ]);

        $data = [
            'employee_id' => $this->employeeId,
            'date' => $this->date,
            'clock_in' => $this->clockIn ? "{$this->date} {$this->clockIn}:00" : null,
            'clock_out' => $this->clockOut ? "{$this->date} {$this->clockOut}:00" : null,
            'status' => $this->status,
            'clock_in_method' => $this->clockInMethod,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingId) {
            Attendance::findOrFail($this->editingId)->update($data);
        } else {
            Attendance::create(['tenant_id' => auth()->user()->tenant_id, ...$data]);
        }
        $this->showForm = false;
    }

    public function delete(int $id): void { Attendance::findOrFail($id)->delete(); }

    public function render()
    {
        return view('livewire.attendance.attendance-manager')
            ->layout('layouts.app', ['pageTitle' => 'Absensi']);
    }
}
