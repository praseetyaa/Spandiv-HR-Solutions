<?php
namespace App\Policies;
use App\Models\PayrollPeriod;
use App\Models\User;

class PayrollPeriodPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','finance_admin']); }
    public function process(User $user): bool { return $user->hasAnyRole(['company_owner','finance_admin']); }
    public function delete(User $user, PayrollPeriod $p): bool { return $user->hasRole('company_owner'); }
}
