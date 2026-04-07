<?php
namespace App\Policies;
use App\Models\PsychTest;
use App\Models\User;

class PsychTestPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function view(User $user, PsychTest $t): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function update(User $user, PsychTest $t): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function assign(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function delete(User $user, PsychTest $t): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
