<?php
namespace App\Policies;
use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin','manager']); }
    public function view(User $user, Attendance $att): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $att->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $att->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']);
    }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','employee','manager']); }
    public function update(User $user, Attendance $att): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function delete(User $user, Attendance $att): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
