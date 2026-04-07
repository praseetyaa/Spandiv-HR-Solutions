<?php
namespace App\Policies;
use App\Models\ExpenseRequest;
use App\Models\User;

class ExpenseRequestPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, ExpenseRequest $e): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $e->employee_id;
        return $user->hasAnyRole(['company_owner','hr_admin','finance_admin','manager']);
    }
    public function create(User $user): bool { return true; }
    public function approve(User $user, ExpenseRequest $e): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function delete(User $user, ExpenseRequest $e): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $e->employee_id && $e->status === 'draft';
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
}
