<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;

class LeaveBalanceService
{
    public function initializeForEmployee(Employee $employee): void
    {
        $leaveTypes = LeaveType::where('tenant_id', $employee->tenant_id)
            ->where('is_active', true)
            ->get();

        $year = now()->year;

        foreach ($leaveTypes as $type) {
            LeaveBalance::firstOrCreate(
                [
                    'employee_id'   => $employee->id,
                    'leave_type_id' => $type->id,
                    'year'          => $year,
                ],
                [
                    'tenant_id'     => $employee->tenant_id,
                    'balance'       => $type->days_per_year,
                    'used'          => 0,
                    'carry_forward' => 0,
                ]
            );
        }
    }

    public function resetAnnualBalances(int $tenantId, int $newYear): int
    {
        $previousYear = $newYear - 1;
        $leaveTypes = LeaveType::where('tenant_id', $tenantId)->where('is_active', true)->get();
        $count = 0;

        $employees = Employee::where('tenant_id', $tenantId)->where('status', 'active')->get();

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $type) {
                $prevBalance = LeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $type->id)
                    ->where('year', $previousYear)
                    ->first();

                $carryForward = 0;
                if ($type->carry_over && $prevBalance) {
                    $remaining = max(0, $prevBalance->balance - $prevBalance->used);
                    $carryForward = min($remaining, $type->max_carry_days);
                }

                LeaveBalance::updateOrCreate(
                    [
                        'employee_id'   => $employee->id,
                        'leave_type_id' => $type->id,
                        'year'          => $newYear,
                    ],
                    [
                        'tenant_id'     => $tenantId,
                        'balance'       => $type->days_per_year + $carryForward,
                        'used'          => 0,
                        'carry_forward' => $carryForward,
                    ]
                );
                $count++;
            }
        }

        return $count;
    }

    public function deductBalance(LeaveRequest $request): bool
    {
        $balance = LeaveBalance::where('employee_id', $request->employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('year', $request->start_date->year)
            ->first();

        if (!$balance) return false;

        $available = $balance->balance - $balance->used;
        if ($available < $request->total_days) return false;

        $balance->increment('used', $request->total_days);
        return true;
    }

    public function restoreBalance(LeaveRequest $request): void
    {
        LeaveBalance::where('employee_id', $request->employee_id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('year', $request->start_date->year)
            ->decrement('used', $request->total_days);
    }
}
