<?php
namespace App\Policies;
use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, LeaveRequest $lr): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $lr->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $lr->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function create(User $user): bool { return true; }
    public function approve(User $user, LeaveRequest $lr): bool
    {
        if ($user->hasRole('manager')) return $user->employee?->department_id === $lr->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function update(User $user, LeaveRequest $lr): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $lr->employee_id && $lr->status === 'pending';
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function delete(User $user, LeaveRequest $lr): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
