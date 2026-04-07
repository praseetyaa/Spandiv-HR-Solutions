<?php
namespace App\Policies;
use App\Models\Tenant;
use App\Models\User;

class TenantPolicy
{
    public function viewAny(User $user): bool { return $user->guard === 'platform'; }
    public function view(User $user, Tenant $t): bool { return $user->guard === 'platform'; }
    public function create(User $user): bool { return $user->guard === 'platform' && $user->hasRole('super_admin'); }
    public function update(User $user, Tenant $t): bool { return $user->guard === 'platform' && $user->hasRole('super_admin'); }
    public function delete(User $user, Tenant $t): bool { return $user->guard === 'platform' && $user->hasRole('super_admin'); }
    public function impersonate(User $user): bool { return $user->guard === 'platform' && $user->hasRole('super_admin'); }
}
