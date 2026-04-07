<?php
namespace App\Policies;
use App\Models\AttendanceCorrection;
use App\Models\User;

class AttendanceCorrectionPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','manager']); }
    public function view(User $user, AttendanceCorrection $c): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $c->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $c->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function create(User $user): bool { return true; }
    public function approve(User $user, AttendanceCorrection $c): bool
    {
        if ($user->hasRole('manager')) return $user->employee?->department_id === $c->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function delete(User $user, AttendanceCorrection $c): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
