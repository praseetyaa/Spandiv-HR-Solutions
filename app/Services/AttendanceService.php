<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\WorkSchedule;
use Carbon\Carbon;

class AttendanceService
{
    public function clockIn(Employee $employee, array $data = []): Attendance
    {
        $today = now()->toDateString();

        $attendance = Attendance::firstOrNew([
            'employee_id' => $employee->id,
            'date'        => $today,
        ]);

        if ($attendance->clock_in) {
            throw new \RuntimeException('Sudah clock-in hari ini.');
        }

        $schedule = $this->getActiveSchedule($employee);

        $attendance->fill([
            'tenant_id'       => $employee->tenant_id,
            'schedule_id'     => $schedule?->id,
            'clock_in'        => now(),
            'clock_in_method' => $data['method'] ?? 'web',
            'clock_in_lat'    => $data['lat'] ?? null,
            'clock_in_lng'    => $data['lng'] ?? null,
            'clock_in_photo'  => $data['photo'] ?? null,
            'status'          => 'present',
        ]);

        // Check if late
        if ($schedule && $schedule->start_time) {
            $expectedStart = Carbon::parse($today . ' ' . $schedule->start_time);
            $tolerance     = $schedule->late_tolerance_min ?? 0;
            $lateMinutes   = max(0, now()->diffInMinutes($expectedStart, false) * -1 - $tolerance);

            if ($lateMinutes > 0) {
                $attendance->status       = 'late';
                $attendance->late_minutes = $lateMinutes;
            }
        }

        $attendance->save();
        return $attendance;
    }

    public function clockOut(Employee $employee, array $data = []): Attendance
    {
        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('date', now()->toDateString())
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->firstOrFail();

        $attendance->update([
            'clock_out'     => now(),
            'clock_out_lat' => $data['lat'] ?? null,
            'clock_out_lng' => $data['lng'] ?? null,
        ]);

        // Check early leave
        $schedule = $attendance->schedule;
        if ($schedule && $schedule->end_time) {
            $expectedEnd = Carbon::parse($attendance->date . ' ' . $schedule->end_time);
            $earlyMinutes = max(0, $expectedEnd->diffInMinutes(now(), false));
            if ($earlyMinutes > 0) {
                $attendance->update(['early_leave_minutes' => $earlyMinutes]);
            }
        }

        return $attendance->refresh();
    }

    protected function getActiveSchedule(Employee $employee): ?WorkSchedule
    {
        $empSchedule = EmployeeSchedule::where('employee_id', $employee->id)
            ->where('effective_date', '<=', now())
            ->where(fn ($q) => $q->whereNull('end_date')->orWhere('end_date', '>=', now()))
            ->latest('effective_date')
            ->first();

        if ($empSchedule) {
            return $empSchedule->schedule;
        }

        return WorkSchedule::where('tenant_id', $employee->tenant_id)
            ->where('is_default', true)
            ->first();
    }
}
