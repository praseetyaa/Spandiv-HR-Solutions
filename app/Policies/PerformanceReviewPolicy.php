<?php
namespace App\Policies;
use App\Models\PerformanceReview;
use App\Models\User;

class PerformanceReviewPolicy
{
    public function viewAny(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin','manager']); }
    public function view(User $user, PerformanceReview $r): bool
    {
        if ($user->hasRole('employee')) return $user->employee?->id === $r->employee_id;
        if ($user->hasRole('manager')) return $user->employee?->department_id === $r->employee?->department_id;
        return $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function create(User $user): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
    public function submit(User $user, PerformanceReview $r): bool
    {
        return $user->id === $r->reviewer_id || $user->hasAnyRole(['company_owner','hr_admin']);
    }
    public function delete(User $user, PerformanceReview $r): bool { return $user->hasAnyRole(['company_owner','hr_admin']); }
}
