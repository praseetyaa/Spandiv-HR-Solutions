<?php

namespace App\Livewire\Attendance;

use App\Services\AttendanceService;
use Livewire\Component;

class ClockInOut extends Component
{
    public ?string $lat = null;
    public ?string $lng = null;
    public ?string $todayStatus = null;
    public ?string $clockInTime = null;
    public ?string $clockOutTime = null;

    public function mount(): void
    {
        $this->refreshStatus();
    }

    public function refreshStatus(): void
    {
        $employee = auth()->user()->employee;
        if (!$employee) return;

        $attendance = \App\Models\Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->first();

        if ($attendance) {
            $this->clockInTime  = $attendance->clock_in?->format('H:i');
            $this->clockOutTime = $attendance->clock_out?->format('H:i');
            $this->todayStatus  = $attendance->clock_out ? 'completed' : 'clocked_in';
        } else {
            $this->todayStatus = 'not_started';
        }
    }

    public function clockIn(AttendanceService $service): void
    {
        $employee = auth()->user()->employee;
        if (!$employee) {
            $this->addError('clock', 'Data karyawan tidak ditemukan.');
            return;
        }

        try {
            $service->clockIn($employee, [
                'lat'    => $this->lat,
                'lng'    => $this->lng,
                'method' => 'web',
            ]);
            $this->refreshStatus();
            session()->flash('success', 'Clock-in berhasil pada ' . now()->format('H:i'));
        } catch (\RuntimeException $e) {
            $this->addError('clock', $e->getMessage());
        }
    }

    public function clockOut(AttendanceService $service): void
    {
        $employee = auth()->user()->employee;
        if (!$employee) return;

        try {
            $service->clockOut($employee, [
                'lat' => $this->lat,
                'lng' => $this->lng,
            ]);
            $this->refreshStatus();
            session()->flash('success', 'Clock-out berhasil pada ' . now()->format('H:i'));
        } catch (\Throwable $e) {
            $this->addError('clock', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.attendance.clock-in-out');
    }
}
