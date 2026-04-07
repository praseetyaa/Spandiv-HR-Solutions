<?php
namespace App\Policies;
use App\Models\CompanyPolicy;
use App\Models\User;

class CompanyPolicyPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, CompanyPolicy $p): bool { return true; }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function update(User $user, CompanyPolicy $p): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function delete(User $user, CompanyPolicy $p): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
