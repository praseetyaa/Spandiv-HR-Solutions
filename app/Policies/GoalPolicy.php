<?php
namespace App\Policies;
use App\Models\Goal;
use App\Models\User;

class GoalPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Goal $g): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $g->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $g->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','manager','employee']); }
    public function update(User $user, Goal $g): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $g->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $g->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function delete(User $user, Goal $g): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
