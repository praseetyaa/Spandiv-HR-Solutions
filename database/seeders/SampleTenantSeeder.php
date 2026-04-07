<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantSetting;
use App\Models\TenantSubscription;
use App\Models\User;
use App\Models\Department;
use App\Models\JobPosition;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleTenantSeeder extends Seeder
{
    public function run(): void
    {
        $plan = Plan::where('slug', 'enterprise')->first();

        if (!$plan) {
            $this->command->warn('Enterprise plan not found. Skipping SampleTenantSeeder.');
            return;
        }

        // ============================================================
        // Sample tenant: PT Spandiv Global
        // ============================================================
        $tenant = Tenant::firstOrCreate(
            ['subdomain' => 'spandiv'],
            [
                'plan_id' => $plan->id,
                'name'    => 'PT Spandiv Global',
                'slug'    => 'spandiv',
                'status'  => 'active',
            ]
        );

        // Tenant Settings
        TenantSetting::firstOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'brand_color'       => '#2B5BA8',
                'timezone'          => 'Asia/Jakarta',
                'currency'          => 'IDR',
                'language'          => 'id',
                'payroll_cutoff_day' => 25,
                'company_address'   => 'Jl. HR Rasuna Said Kav. 62, Jakarta Selatan',
                'company_phone'     => '021-5551234',
                'company_email'     => 'hr@spandiv.com',
            ]
        );

        // Subscription
        TenantSubscription::firstOrCreate(
            ['tenant_id' => $tenant->id, 'status' => 'active'],
            [
                'plan_id'        => $plan->id,
                'starts_at'      => now(),
                'ends_at'        => now()->addYear(),
                'billing_cycle'  => 'yearly',
                'payment_status' => 'paid',
                'amount_paid'    => $plan->price_yearly,
            ]
        );

        // ============================================================
        // Company Owner user
        // ============================================================
        $owner = User::firstOrCreate(
            ['email' => 'owner@spandiv.com'],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Budi Santoso',
                'password'  => Hash::make('password'),
                'guard'     => 'web',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$owner->hasRole('company_owner')) {
            $owner->assignRole('company_owner');
        }

        // HR Admin user
        $hrAdmin = User::firstOrCreate(
            ['email' => 'hr@spandiv.com'],
            [
                'tenant_id' => $tenant->id,
                'name'      => 'Sari Dewi',
                'password'  => Hash::make('password'),
                'guard'     => 'web',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        if (!$hrAdmin->hasRole('hr_admin')) {
            $hrAdmin->assignRole('hr_admin');
        }

        // ============================================================
        // Sample Departments
        // ============================================================
        $engineering = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'ENG'],
            ['name' => 'Engineering', 'is_active' => true]
        );

        $hr = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'HR'],
            ['name' => 'Human Resources', 'is_active' => true]
        );

        $finance = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'FIN'],
            ['name' => 'Finance', 'is_active' => true]
        );

        $marketing = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'MKT'],
            ['name' => 'Marketing', 'is_active' => true]
        );

        $operations = Department::firstOrCreate(
            ['tenant_id' => $tenant->id, 'code' => 'OPS'],
            ['name' => 'Operations', 'is_active' => true]
        );

        // ============================================================
        // Sample Job Positions
        // ============================================================
        $positions = [
            ['tenant_id' => $tenant->id, 'department_id' => $engineering->id, 'title' => 'Software Engineer', 'level' => 'staff'],
            ['tenant_id' => $tenant->id, 'department_id' => $engineering->id, 'title' => 'Engineering Manager', 'level' => 'manager'],
            ['tenant_id' => $tenant->id, 'department_id' => $hr->id, 'title' => 'HR Officer', 'level' => 'staff'],
            ['tenant_id' => $tenant->id, 'department_id' => $hr->id, 'title' => 'HR Manager', 'level' => 'manager'],
            ['tenant_id' => $tenant->id, 'department_id' => $finance->id, 'title' => 'Accountant', 'level' => 'staff'],
            ['tenant_id' => $tenant->id, 'department_id' => $finance->id, 'title' => 'Finance Manager', 'level' => 'manager'],
            ['tenant_id' => $tenant->id, 'department_id' => $marketing->id, 'title' => 'Marketing Specialist', 'level' => 'staff'],
            ['tenant_id' => $tenant->id, 'department_id' => $operations->id, 'title' => 'Operations Coordinator', 'level' => 'staff'],
        ];

        foreach ($positions as $pos) {
            JobPosition::firstOrCreate(
                ['tenant_id' => $pos['tenant_id'], 'title' => $pos['title']],
                collect($pos)->except(['tenant_id', 'title'])->toArray()
            );
        }
    }
}
