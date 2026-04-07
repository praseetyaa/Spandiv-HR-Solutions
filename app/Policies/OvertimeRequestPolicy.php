<?php
namespace App\Policies;
use App\Models\OvertimeRequest;
use App\Models\User;

class OvertimeRequestPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, OvertimeRequest $ot): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $ot->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $ot->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function create(User $user): bool { return true; }
    public function approve(User $user, OvertimeRequest $ot): bool
    {
        if ($user->hasRole('manager')) return $user->employee?->department_id === $ot->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function delete(User $user, OvertimeRequest $ot): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
