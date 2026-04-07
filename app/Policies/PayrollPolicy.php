<?php
namespace App\Policies;
use App\Models\Payroll;
use App\Models\User;

class PayrollPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function view(User $user, Payroll $p): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $p->employee_id;
        return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']);
    }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function update(User $user, Payroll $p): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function finalize(User $user): bool { return $user->hasAnyRole(['company_owner','finance_admin']); }
    public function delete(User $user, Payroll $p): bool { return $user->hasAnyRole(['company_owner']); }
}
