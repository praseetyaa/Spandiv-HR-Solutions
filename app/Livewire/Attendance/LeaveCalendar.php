<?php

namespace App\Livewire\Attendance;

use App\Models\LeaveRequest;
use Livewire\Component;
use Carbon\Carbon;

class LeaveCalendar extends Component
{
    public int $month;
    public int $year;
    public ?int $departmentId = null;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
    }

    public function render()
    {
        $tenantId = auth()->user()->tenant_id;
        $startDate = Carbon::create($this->year, $this->month, 1)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        $query = LeaveRequest::where('tenant_id', $tenantId)
            ->where('status', 'approved')
            ->where(fn ($q) => $q
                ->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
            )
            ->with(['employee.department', 'leaveType']);

        if ($this->departmentId) {
            $query->whereHas('employee', fn ($q) => $q->where('department_id', $this->departmentId));
        }

        $leaves = $query->get();

        // Group by date
        $calendarData = [];
        foreach ($leaves as $leave) {
            $current = $leave->start_date->copy()->max($startDate);
            $end     = $leave->end_date->copy()->min($endDate);

            while ($current->lte($end)) {
                $key = $current->toDateString();
                $calendarData[$key][] = [
                    'employee' => $leave->employee->full_name,
                    'type'     => $leave->leaveType->name,
                    'color'    => $leave->leaveType->color ?? '#3B82F6',
                ];
                $current->addDay();
            }
        }

        $departments = \App\Models\Department::where('tenant_id', $tenantId)->orderBy('name')->get();

        return view('livewire.attendance.leave-calendar', [
            'calendarData' => $calendarData,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'departments'  => $departments,
        ]);
    }
}
