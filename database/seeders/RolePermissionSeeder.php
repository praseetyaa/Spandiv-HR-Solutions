<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ============================================================
        // PLATFORM GUARD — Super Admin & Support Admin
        // ============================================================
        $platformPermissions = [
            'platform.tenants.view',
            'platform.tenants.create',
            'platform.tenants.update',
            'platform.tenants.delete',
            'platform.plans.manage',
            'platform.subscriptions.manage',
            'platform.users.manage',
            'platform.analytics.view',
            'platform.audit.view',
            'platform.impersonate',
            'platform.settings.manage',
        ];

        foreach ($platformPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'platform']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'platform']);
        $superAdmin->givePermissionTo($platformPermissions);

        $supportAdmin = Role::firstOrCreate(['name' => 'support_admin', 'guard_name' => 'platform']);
        $supportAdmin->givePermissionTo([
            'platform.tenants.view',
            'platform.audit.view',
        ]);

        // ============================================================
        // WEB GUARD — Company-level roles
        // ============================================================
        $webPermissions = [
            // Employee
            'employees.view', 'employees.create', 'employees.update', 'employees.delete',
            'employees.import', 'employees.export',
            // Attendance
            'attendance.view', 'attendance.clock', 'attendance.manage', 'attendance.correct',
            'attendance.approve',
            // Leave
            'leave.view', 'leave.create', 'leave.approve', 'leave.manage',
            // Payroll
            'payroll.view', 'payroll.run', 'payroll.approve', 'payroll.manage',
            'payslip.view', 'payslip.download',
            // Recruitment
            'recruitment.view', 'recruitment.create', 'recruitment.manage',
            'candidates.view', 'candidates.manage',
            'interviews.schedule', 'interviews.manage',
            // Psych Test
            'psych_test.view', 'psych_test.create', 'psych_test.manage',
            'psych_test.assign', 'psych_test.results',
            // Onboarding
            'onboarding.view', 'onboarding.manage',
            // Performance
            'performance.view', 'performance.create', 'performance.manage',
            'goals.view', 'goals.create', 'goals.manage',
            'talent.view', 'talent.manage',
            // Learning
            'learning.view', 'learning.create', 'learning.manage',
            'training.view', 'training.manage',
            // Benefit
            'benefit.view', 'benefit.manage',
            'expense.view', 'expense.create', 'expense.approve', 'expense.manage',
            'loan.view', 'loan.manage',
            // Compliance
            'compliance.view', 'compliance.manage',
            'policy.view', 'policy.manage',
            'disciplinary.view', 'disciplinary.manage',
            'grievance.view', 'grievance.manage',
            // Wellness
            'survey.view', 'survey.create', 'survey.manage',
            'recognition.view', 'recognition.create',
            'announcement.view', 'announcement.manage',
            // Settings
            'settings.view', 'settings.manage',
            'users.view', 'users.manage',
            // Reports
            'reports.view', 'reports.export',
        ];

        foreach ($webPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // COMPANY_OWNER — full access
        $companyOwner = Role::firstOrCreate(['name' => 'company_owner', 'guard_name' => 'web']);
        $companyOwner->givePermissionTo($webPermissions);

        // HR_ADMIN — full HR, no billing
        $hrAdmin = Role::firstOrCreate(['name' => 'hr_admin', 'guard_name' => 'web']);
        $hrAdmin->givePermissionTo(array_filter($webPermissions, fn($p) =>
            !str_starts_with($p, 'settings.manage')
        ));

        // FINANCE_ADMIN — payroll + expense focused
        $financeAdmin = Role::firstOrCreate(['name' => 'finance_admin', 'guard_name' => 'web']);
        $financeAdmin->givePermissionTo([
            'employees.view',
            'attendance.view',
            'leave.view',
            'payroll.view', 'payroll.run', 'payroll.approve', 'payroll.manage',
            'payslip.view', 'payslip.download',
            'benefit.view', 'benefit.manage',
            'expense.view', 'expense.approve', 'expense.manage',
            'loan.view', 'loan.manage',
            'reports.view', 'reports.export',
        ]);

        // RECRUITER
        $recruiter = Role::firstOrCreate(['name' => 'recruiter', 'guard_name' => 'web']);
        $recruiter->givePermissionTo([
            'recruitment.view', 'recruitment.create', 'recruitment.manage',
            'candidates.view', 'candidates.manage',
            'interviews.schedule', 'interviews.manage',
            'psych_test.view', 'psych_test.assign', 'psych_test.results',
            'onboarding.view', 'onboarding.manage',
        ]);

        // MANAGER — team-scoped
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->givePermissionTo([
            'employees.view',
            'attendance.view', 'attendance.approve',
            'leave.view', 'leave.approve',
            'performance.view', 'performance.create', 'performance.manage',
            'goals.view', 'goals.create', 'goals.manage',
            'talent.view',
            'recognition.view', 'recognition.create',
            'reports.view',
        ]);

        // EMPLOYEE — self-only
        $employee = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employee->givePermissionTo([
            'attendance.view', 'attendance.clock',
            'leave.view', 'leave.create',
            'payslip.view', 'payslip.download',
            'expense.view', 'expense.create',
            'goals.view',
            'performance.view',
            'learning.view',
            'recognition.view', 'recognition.create',
            'survey.view',
            'announcement.view',
            'policy.view',
        ]);
    }
}
