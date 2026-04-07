<?php
namespace App\Policies;
use App\Models\JobPosting;
use App\Models\User;

class JobPostingPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function view(User $user, JobPosting $jp): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function update(User $user, JobPosting $jp): bool { return $user->hasAnyRole(['company_owner','hr_admin','recruiter']); }
    public function delete(User $user, JobPosting $jp): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
