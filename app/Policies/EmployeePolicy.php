<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['company_owner', 'hr_admin', 'finance_admin', 'manager']);
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->hasRole('employee')) {
            return $user->employee?->id === $employee->id;
        }
        if ($user->hasRole('manager')) {
            return $user->employee?->department_id === $employee->department_id;
        }
        return $user->hasAnyRole(['company_owner', 'hr_admin', 'finance_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['company_owner', 'hr_admin']);
    }

    public function update(User $user, Employee $employee): bool
    {
        if ($user->hasRole('employee')) {
            return $user->employee?->id === $employee->id;
        }
        return $user->hasAnyRole(['company_owner', 'hr_admin']);
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->hasAnyRole(['company_owner', 'hr_admin']);
    }
}
