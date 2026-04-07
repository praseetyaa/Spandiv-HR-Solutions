<?php
namespace App\Policies;
use App\Models\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function view(User $user, Candidate $c): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function update(User $user, Candidate $c): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function delete(User $user, Candidate $c): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
