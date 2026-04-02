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

        // ============================================================
        // Sample tenant: PT Spandiv Global
        // ============================================================
        $tenant = Tenant::create([
            'plan_id'   => $plan->id,
            'name'      => 'PT Spandiv Global',
            'slug'      => 'spandiv',
            'subdomain' => 'spandiv',
            'status'    => 'active',
        ]);

        // Tenant Settings
        TenantSetting::create([
            'tenant_id'       => $tenant->id,
            'brand_color'     => '#2B5BA8',
            'timezone'        => 'Asia/Jakarta',
            'currency'        => 'IDR',
            'language'        => 'id',
            'payroll_cutoff_day' => 25,
            'company_address' => 'Jl. HR Rasuna Said Kav. 62, Jakarta Selatan',
            'company_phone'   => '021-5551234',
            'company_email'   => 'hr@spandiv.com',
        ]);

        // Subscription
        TenantSubscription::create([
            'tenant_id'      => $tenant->id,
            'plan_id'        => $plan->id,
            'starts_at'      => now(),
            'ends_at'        => now()->addYear(),
            'billing_cycle'  => 'yearly',
            'payment_status' => 'paid',
            'status'         => 'active',
            'amount_paid'    => $plan->price_yearly,
        ]);

        // ============================================================
        // Company Owner user
        // ============================================================
        $owner = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Budi Santoso',
            'email'     => 'owner@spandiv.com',
            'password'  => Hash::make('password'),
            'guard'     => 'web',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $owner->assignRole('company_owner');

        // HR Admin user
        $hrAdmin = User::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Sari Dewi',
            'email'     => 'hr@spandiv.com',
            'password'  => Hash::make('password'),
            'guard'     => 'web',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
        $hrAdmin->assignRole('hr_admin');

        // ============================================================
        // Sample Departments
        // ============================================================
        $engineering = Department::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Engineering',
            'code'      => 'ENG',
            'is_active' => true,
        ]);

        $hr = Department::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Human Resources',
            'code'      => 'HR',
            'is_active' => true,
        ]);

        $finance = Department::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Finance',
            'code'      => 'FIN',
            'is_active' => true,
        ]);

        $marketing = Department::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Marketing',
            'code'      => 'MKT',
            'is_active' => true,
        ]);

        $operations = Department::create([
            'tenant_id' => $tenant->id,
            'name'      => 'Operations',
            'code'      => 'OPS',
            'is_active' => true,
        ]);

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
            JobPosition::create($pos);
        }
    }
}
